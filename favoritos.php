<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Mis Favoritos — GeoWorld';
$activeNav = 'favoritos';

include __DIR__ . '/views/favoritos.php';
