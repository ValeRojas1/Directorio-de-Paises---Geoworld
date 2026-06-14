<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$input = getJsonInput();
$titulo = trim((string) ($input['titulo'] ?? 'Comparación de países'));
$paisesRaw = $input['paises_cca2'] ?? '';
$campos = $input['campos_comparados'] ?? $input['campos'] ?? [];

if (is_array($paisesRaw)) {
    $paises = array_values(array_unique(array_map('strtoupper', array_map('trim', $paisesRaw))));
    $paisesStr = implode(',', $paises);
} else {
    $paisesStr = strtoupper(trim((string) $paisesRaw));
    $paises = array_values(array_filter(array_map('trim', explode(',', $paisesStr))));
}

if (count($paises) < 2) {
    jsonError('Debe enviar al menos 2 países');
}

if (!is_array($campos) || empty($campos)) {
    $campos = ['poblacion', 'area_km2', 'idiomas', 'monedas', 'capital', 'region'];
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'INSERT INTO comparaciones (usuario_id, titulo, paises_cca2, campos_comparados, veces_visto)
         VALUES (:usuario_id, :titulo, :paises, :campos, 0)'
    );
    $stmt->execute([
        ':usuario_id' => getSessionUserId(),
        ':titulo'     => $titulo,
        ':paises'     => $paisesStr,
        ':campos'     => json_encode(array_values($campos), JSON_UNESCAPED_UNICODE),
    ]);

    jsonSuccess(['id' => (int) $db->lastInsertId()]);
} catch (Throwable $e) {
    jsonError('Error al guardar comparación: ' . $e->getMessage(), 500);
}
