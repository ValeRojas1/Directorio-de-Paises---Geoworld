<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/helpers.php';

requireMethod('GET');

$cca2 = strtoupper(trim((string) ($_GET['cca2'] ?? '')));

if ($cca2 === '') {
    jsonError('Parámetro cca2 requerido');
}

try {
    $pais = getCachePaisByCca2($cca2);
    $desdeCache = $pais !== null;

    if (!$desdeCache) {
        jsonError('País no encontrado', 404);
    }

    jsonSuccess([
        'desde_cache' => $desdeCache,
        'pais'        => $pais,
    ]);
} catch (Throwable $e) {
    jsonError('Error al obtener detalle: ' . $e->getMessage(), 500);
}
