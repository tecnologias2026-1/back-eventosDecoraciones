<?php
require_once __DIR__ . '/../controllers/auth.controller.php';

function handleAuthRoutes(string $method, string $path): void {
    if ($method === 'POST' && $path === '/auth/login') {
        login();
    } elseif ($method === 'POST' && $path === '/auth/register') {
        register();
    } elseif ($method === 'GET' && $path === '/auth/me') {
        me();
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
