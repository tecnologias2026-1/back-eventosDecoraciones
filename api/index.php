<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip /api/v1 or /api prefix
$path = preg_replace('#^(/api/v1|/api)#', '', $path);
$path = rtrim($path, '/') ?: '/';

if ($path === '/') {
    http_response_code(200);
    echo json_encode(['message' => 'Eventos & Decoraciones API v1 — OK']);
} elseif (str_starts_with($path, '/venues')) {
    require_once 'routes/venues.routes.php';
    handleVenueRoutes($method, $path);
} elseif (str_starts_with($path, '/services')) {
    require_once 'routes/services.routes.php';
    handleServiceRoutes($method, $path);
} elseif (str_starts_with($path, '/weddings')) {
    require_once 'routes/weddings.routes.php';
    handleWeddingRoutes($method, $path);
} elseif (str_starts_with($path, '/reservations')) {
    require_once 'routes/reservations.routes.php';
    handleReservationRoutes($method, $path);
} elseif (str_starts_with($path, '/auth')) {
    require_once 'routes/auth.routes.php';
    handleAuthRoutes($method, $path);
} elseif (str_starts_with($path, '/users')) {
    require_once 'routes/user.routes.php';
    handleUserRoutes($method, $path);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}
