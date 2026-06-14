<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('GET');

$usuario = requireAuth();

try {
    $db = getDB();
    $stmt = $db->prepare(
        'SELECT id, titulo, paises_cca2, campos_comparados, veces_visto, created_at
         FROM comparaciones WHERE usuario_id = :usuario_id ORDER BY created_at DESC'
    );
    $stmt->execute([':usuario_id' => $usuario['id']]);
    $rows = $stmt->fetchAll();

    $comparaciones = array_map(static function (array $row): array {
        return [
            'id'                 => (int) $row['id'],
            'titulo'             => $row['titulo'],
            'paises_cca2'        => $row['paises_cca2'],
            'campos_comparados'  => json_decode((string) $row['campos_comparados'], true) ?: [],
            'veces_visto'        => (int) $row['veces_visto'],
            'created_at'         => $row['created_at'],
        ];
    }, $rows);

    jsonSuccess($comparaciones);
} catch (Throwable $e) {
    jsonError('Error al obtener historial de comparaciones: ' . $e->getMessage(), 500);
}
