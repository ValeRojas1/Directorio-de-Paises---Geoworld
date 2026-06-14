<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/helpers.php';

requireMethod('GET');

$termino = trim((string) ($_GET['q'] ?? ''));

if ($termino === '') {
    jsonError('Parámetro q requerido');
}

if (strlen($termino) < 2) {
    jsonError('El término debe tener al menos 2 caracteres');
}

try {
    $paises = searchCachePaises($termino);
    $desdeCache = true;
    $paises = array_map('resumirPaisBusqueda', $paises);

    registrarHistorialBusqueda($termino, 'nombre', count($paises));

    jsonSuccess([
        'termino'     => $termino,
        'desde_cache' => $desdeCache,
        'total'       => count($paises),
        'paises'      => $paises,
    ]);
} catch (Throwable $e) {
    jsonError('Error al buscar países: ' . $e->getMessage(), 500);
}
