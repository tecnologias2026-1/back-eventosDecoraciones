<?php
require_once __DIR__ . '/../controllers/services.controller.php';

function handleServiceRoutes(string $method, string $path): void {
    // GET /services — todos
    if ($method === 'GET' && $path === '/services') {
        getServices();
    // GET /services/{category}
    } elseif ($method === 'GET' && preg_match('#^/services/(ceremony|reception|food|others)$#', $path, $m)) {
        getServices($m[1]);
    // POST /services (admin)
    } elseif ($method === 'POST' && $path === '/services') {
        adminCreateService();
    // PUT /services/{id} (admin)
    } elseif ($method === 'PUT' && preg_match('#^/services/(\d+)$#', $path, $m)) {
        adminUpdateService((int) $m[1]);
    // DELETE /services/{id} (admin)
    } elseif ($method === 'DELETE' && preg_match('#^/services/(\d+)$#', $path, $m)) {
        adminDeleteService((int) $m[1]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
