<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$usuario = requireAuth();
$input = getJsonInput();

$cca2 = strtoupper(trim((string) ($input['pais_cca2'] ?? '')));
$nombre = trim((string) ($input['pais_nombre'] ?? ''));
$bandera = trim((string) ($input['pais_bandera_url'] ?? ''));

if ($cca2 === '' || $nombre === '') {
    jsonError('pais_cca2 y pais_nombre son requeridos');
}

if ($bandera === '') {
    $bandera = 'https://flagcdn.com/' . strtolower($cca2) . '.svg';
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'INSERT IGNORE INTO favoritos (usuario_id, pais_cca2, pais_nombre, pais_bandera_url)
         VALUES (:usuario_id, :cca2, :nombre, :bandera)'
    );
    $stmt->execute([
        ':usuario_id' => $usuario['id'],
        ':cca2'       => $cca2,
        ':nombre'     => $nombre,
        ':bandera'    => $bandera,
    ]);

    jsonSuccess(['pais_cca2' => $cca2]);
} catch (Throwable $e) {
    jsonError('Error al agregar favorito: ' . $e->getMessage(), 500);
}
