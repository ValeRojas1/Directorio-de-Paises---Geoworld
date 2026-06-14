<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('GET');

$usuario = requireAuth();

try {
    $db = getDB();
    $stmt = $db->prepare(
        'SELECT id, usuario_id, pais_cca2, pais_nombre, pais_bandera_url, created_at
         FROM favoritos WHERE usuario_id = :usuario_id ORDER BY created_at DESC'
    );
    $stmt->execute([':usuario_id' => $usuario['id']]);

    jsonSuccess($stmt->fetchAll());
} catch (Throwable $e) {
    jsonError('Error al listar favoritos: ' . $e->getMessage(), 500);
}
