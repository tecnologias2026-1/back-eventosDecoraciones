<?php
require_once __DIR__ . '/../controllers/weddings.controller.php';

function handleWeddingRoutes(string $method, string $path): void {
    // GET /weddings
    if ($method === 'GET' && $path === '/weddings') {
        getWeddings();
    // GET /weddings/{id}
    } elseif ($method === 'GET' && preg_match('#^/weddings/(\d+)$#', $path, $m)) {
        getWedding((int) $m[1]);
    // POST /weddings (admin)
    } elseif ($method === 'POST' && $path === '/weddings') {
        adminCreateWedding();
    // PUT /weddings/{id} (admin)
    } elseif ($method === 'PUT' && preg_match('#^/weddings/(\d+)$#', $path, $m)) {
        adminUpdateWedding((int) $m[1]);
    // DELETE /weddings/{id} (admin)
    } elseif ($method === 'DELETE' && preg_match('#^/weddings/(\d+)$#', $path, $m)) {
        adminDeleteWedding((int) $m[1]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
