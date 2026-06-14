<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Mi Perfil — GeoWorld';
$activeNav = 'perfil';
$comparaciones = [];

if (!empty($_SESSION['usuario'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare(
            'SELECT id, titulo, paises_cca2, campos_comparados, veces_visto, created_at
             FROM comparaciones WHERE usuario_id = :uid ORDER BY created_at DESC LIMIT 20'
        );
        $stmt->execute([':uid' => $_SESSION['usuario']['id']]);
        $comparaciones = $stmt->fetchAll();
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }
}

include __DIR__ . '/views/perfil.php';
