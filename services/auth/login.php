<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';

requireMethod('POST');

$input = getJsonInput();
$email = trim((string) ($input['email'] ?? ''));
$password = (string) ($input['password'] ?? '');

if ($email === '' || $password === '') {
    jsonError('Email y contraseña son requeridos');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Email no válido');
}

try {
    $db = getDB();
    $stmt = $db->prepare(
        'SELECT id, nombre, email, password, rol, activo FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !(int) $usuario['activo']) {
        jsonError('Credenciales incorrectas', 401);
    }

    if (!password_verify($password, $usuario['password'])) {
        jsonError('Credenciales incorrectas', 401);
    }

    $_SESSION['usuario'] = [
        'id'     => (int) $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email'  => $usuario['email'],
        'rol'    => $usuario['rol'],
    ];

    jsonSuccess([
        'nombre' => $usuario['nombre'],
        'rol'    => $usuario['rol'],
    ]);
} catch (Throwable $e) {
    jsonError('Error al iniciar sesión: ' . $e->getMessage(), 500);
}
