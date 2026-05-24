<?php
require_once __DIR__ . '/../controllers/reservations.controller.php';

function handleReservationRoutes(string $method, string $path): void {
    // POST /reservations
    if ($method === 'POST' && $path === '/reservations') {
        createReservation();
    // GET /reservations (con ?email=X para cliente, sin param para admin)
    } elseif ($method === 'GET' && $path === '/reservations') {
        if (isset($_GET['email'])) {
            getReservationsByEmail();
        } else {
            adminGetAllReservations();
        }
    // GET /reservations/{code}
    } elseif ($method === 'GET' && preg_match('#^/reservations/([^/]+)$#', $path, $m)) {
        getReservationByCode($m[1]);
    // PATCH /reservations/{code}/status (admin)
    } elseif ($method === 'PATCH' && preg_match('#^/reservations/([^/]+)/status$#', $path, $m)) {
        adminUpdateReservationStatus($m[1]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
