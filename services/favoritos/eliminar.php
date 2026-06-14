<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$usuario = requireAuth();
$input = getJsonInput();
$cca2 = strtoupper(trim((string) ($input['pais_cca2'] ?? '')));

if ($cca2 === '') {
    jsonError('pais_cca2 requerido');
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'DELETE FROM favoritos WHERE usuario_id = :usuario_id AND pais_cca2 = :cca2'
    );
    $stmt->execute([
        ':usuario_id' => $usuario['id'],
        ':cca2'       => $cca2,
    ]);

    if ($stmt->rowCount() === 0) {
        jsonError('Favorito no encontrado', 404);
    }

    jsonSuccess(['pais_cca2' => $cca2]);
} catch (Throwable $e) {
    jsonError('Error al eliminar favorito: ' . $e->getMessage(), 500);
}
