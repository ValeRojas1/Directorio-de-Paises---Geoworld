<?php

declare(strict_types=1);

/**
 * Conexión PDO singleton para XAMPP.
 * host=localhost | db=directorio_paises | user=root | pass='' | charset=utf8mb4
 */

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=directorio_paises;charset=utf8mb4',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    return $pdo;
}

function jsonSuccess(mixed $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function requireMethod(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
        jsonError('Método no permitido', 405);
    }
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return $_POST ?: [];
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : ($_POST ?: []);
}

function requireAuth(): array
{
    if (empty($_SESSION['usuario'])) {
        jsonError('Debes iniciar sesión', 401);
    }

    return $_SESSION['usuario'];
}

function getClientIp(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function getSessionUserId(): ?int
{
    return isset($_SESSION['usuario']['id']) ? (int) $_SESSION['usuario']['id'] : null;
}
