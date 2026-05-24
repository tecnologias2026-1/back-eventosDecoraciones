<?php
require_once __DIR__ . '/../models/venues.model.php';
require_once __DIR__ . '/../middleware/auth.php';

function getVenues(): void {
    $minGuests = isset($_GET['guests']) ? (int) $_GET['guests'] : null;
    $venues    = getVenuesModel($minGuests);
    http_response_code(200);
    echo json_encode($venues);
}

function getVenue(string $slug): void {
    $venue = getVenueBySlugModel($slug);
    if (!$venue) {
        http_response_code(404);
        echo json_encode(['error' => 'Hacienda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode($venue);
}

function getVenueAvailability(string $slug): void {
    $blocks = getVenueAvailabilityModel($slug);
    if ($blocks === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Hacienda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode($blocks);
}

function adminCreateVenue(): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['slug']) || empty($input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'slug y name son requeridos']);
        return;
    }
    $id = createVenueModel($input);
    http_response_code(201);
    echo json_encode(['id' => $id]);
}

function adminUpdateVenue(int $id): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    $rows = updateVenueModel($id, $input);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Hacienda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Hacienda actualizada']);
}

function adminDeleteVenue(int $id): void {
    requireAdmin();
    $rows = deleteVenueModel($id);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Hacienda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Hacienda eliminada']);
}
