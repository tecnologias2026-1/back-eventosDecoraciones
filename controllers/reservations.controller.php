<?php
require_once __DIR__ . '/../models/reservations.model.php';
require_once __DIR__ . '/../middleware/auth.php';

function createReservation(): void {
    $input = json_decode(file_get_contents('php://input'), true);

    $required = ['email', 'wedding_date', 'guest_count'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo '$field' es requerido"]);
            return;
        }
    }

    if (empty($input['venue_id']) && empty($input['venue_slug'])) {
        http_response_code(400);
        echo json_encode(['error' => "El campo 'venue_id' o 'venue_slug' es requerido"]);
        return;
    }

    try {
        $reservation = createReservationModel($input);
        http_response_code(201);
        echo json_encode([
            'message' => 'Reserva creada exitosamente',
            'code'    => $reservation['code'],
            'id'      => $reservation['id'],
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear la reserva']);
    }
}

function getReservationByCode(string $code): void {
    $reservation = getReservationByCodeModel($code);
    if (!$reservation) {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode($reservation);
}

function getReservationsByEmail(): void {
    $email = $_GET['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email inválido']);
        return;
    }
    $reservations = getReservationsByEmailModel($email);
    http_response_code(200);
    echo json_encode($reservations);
}

function adminGetAllReservations(): void {
    requireAdmin();
    $reservations = getAllReservationsModel();
    http_response_code(200);
    echo json_encode($reservations);
}

function adminDeleteReservation(string $code): void {
    requireAdmin();
    $rows = deleteReservationModel($code);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Reserva eliminada']);
}

function adminUpdateReservationStatus(string $code): void {
    requireAdmin();
    $input  = json_decode(file_get_contents('php://input'), true);
    $status = $input['status'] ?? '';

    if (!in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Estado inválido. Use: pending, confirmed, cancelled, completed']);
        return;
    }

    $rows = updateReservationStatusModel($code, $status);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Estado actualizado']);
}
