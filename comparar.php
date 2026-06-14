<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Comparador de Países — GeoWorld';
$activeNav = 'comparar';
$paisesIniciales = array_filter(array_map('strtoupper', explode(',', (string) ($_GET['paises'] ?? ''))));

include __DIR__ . '/views/comparar.php';
