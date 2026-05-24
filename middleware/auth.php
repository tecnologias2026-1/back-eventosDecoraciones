<?php
require_once __DIR__ . '/jwt.php';

function getJwtSecret(): string {
    return $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?: 'change-me-in-production';
}

function getJwtPayload(): ?array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($header, 'Bearer ')) return null;
    return jwtDecode(substr($header, 7), getJwtSecret());
}

function requireAuth(): array {
    $payload = getJwtPayload();
    if (!$payload) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit();
    }
    return $payload;
}

function requireAdmin(): array {
    $payload = requireAuth();
    if (($payload['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Acceso denegado']);
        exit();
    }
    return $payload;
}
