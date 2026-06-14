<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('GET');

try {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM v_top_busquedas LIMIT 10');
    $top = $stmt->fetchAll();

    jsonSuccess($top);
} catch (Throwable $e) {
    jsonError('Error al obtener top de búsquedas: ' . $e->getMessage(), 500);
}
