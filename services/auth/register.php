<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$input = getJsonInput();
$nombre = trim((string) ($input['nombre'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
$password = (string) ($input['password'] ?? '');
$passwordConfirm = (string) ($input['password_confirm'] ?? $input['passwordConfirm'] ?? '');

if ($nombre === '' || $email === '' || $password === '') {
    jsonError('Nombre, email y contraseña son requeridos');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Email no válido');
}

if ($password !== $passwordConfirm) {
    jsonError('Las contraseñas no coinciden');
}

if (strlen($password) < 6) {
    jsonError('La contraseña debe tener al menos 6 caracteres');
}

try {
    $db = getDB();

    $check = $db->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        jsonError('El email ya está registrado');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (:nombre, :email, :password, :rol, 1)'
    );
    $stmt->execute([
        ':nombre'   => $nombre,
        ':email'    => $email,
        ':password' => $hash,
        ':rol'      => 'usuario',
    ]);

    $_SESSION['usuario'] = [
        'id'     => (int) $db->lastInsertId(),
        'nombre' => $nombre,
        'email'  => $email,
        'rol'    => 'usuario',
    ];

    jsonSuccess([
        'nombre' => $nombre,
        'rol'    => 'usuario',
    ]);
} catch (Throwable $e) {
    jsonError('Error al registrar usuario: ' . $e->getMessage(), 500);
}
