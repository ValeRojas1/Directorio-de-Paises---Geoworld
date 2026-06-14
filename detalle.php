<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/services/paises/helpers.php';

$cca2 = strtoupper(trim((string) ($_GET['cca2'] ?? '')));
$error = null;
$pais = null;

if ($cca2 === '') {
    $error = 'Código de país no especificado';
} else {
    try {
        $pais = getCachePaisByCca2($cca2);

        if ($pais === null) {
            $error = 'País no encontrado';
        }
    } catch (Throwable $e) {
        $error = 'Error al cargar el país: ' . $e->getMessage();
    }
}

$pageTitle = $pais
    ? $pais['nombre_comun'] . ' — GeoWorld'
    : 'País no encontrado — GeoWorld';
$activeNav = 'explorar';

include __DIR__ . '/views/detalle_pais.php';
