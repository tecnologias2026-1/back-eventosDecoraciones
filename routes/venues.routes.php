<?php
require_once __DIR__ . '/../controllers/venues.controller.php';

function handleVenueRoutes(string $method, string $path): void {
    // GET /venues
    if ($method === 'GET' && $path === '/venues') {
        getVenues();
    // GET /venues/{slug}/availability
    } elseif ($method === 'GET' && preg_match('#^/venues/([^/]+)/availability$#', $path, $m)) {
        getVenueAvailability($m[1]);
    // GET /venues/{slug}
    } elseif ($method === 'GET' && preg_match('#^/venues/([^/]+)$#', $path, $m)) {
        getVenue($m[1]);
    // POST /venues (admin)
    } elseif ($method === 'POST' && $path === '/venues') {
        adminCreateVenue();
    // PUT /venues/{id} (admin)
    } elseif ($method === 'PUT' && preg_match('#^/venues/(\d+)$#', $path, $m)) {
        adminUpdateVenue((int) $m[1]);
    // DELETE /venues/{id} (admin)
    } elseif ($method === 'DELETE' && preg_match('#^/venues/(\d+)$#', $path, $m)) {
        adminDeleteVenue((int) $m[1]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}
