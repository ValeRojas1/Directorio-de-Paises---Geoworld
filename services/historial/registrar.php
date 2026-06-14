<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$input = getJsonInput();
$termino = trim((string) ($input['termino'] ?? ''));
$tipo = trim((string) ($input['tipo'] ?? 'nombre'));
$resultados = (int) ($input['resultados'] ?? 0);

if ($termino === '') {
    jsonError('Campo termino requerido');
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'INSERT INTO historial_busquedas (usuario_id, termino, tipo, resultados, ip_address)
         VALUES (:usuario_id, :termino, :tipo, :resultados, :ip)'
    );
    $stmt->execute([
        ':usuario_id'  => getSessionUserId(),
        ':termino'     => $termino,
        ':tipo'        => $tipo,
        ':resultados'  => $resultados,
        ':ip'          => getClientIp(),
    ]);

    jsonSuccess(['id' => (int) $db->lastInsertId()]);
} catch (Throwable $e) {
    jsonError('Error al registrar búsqueda: ' . $e->getMessage(), 500);
}
