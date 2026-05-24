<?php
require_once __DIR__ . '/../models/weddings.model.php';
require_once __DIR__ . '/../middleware/auth.php';

function getWeddings(): void {
    $weddings = getWeddingsModel();
    http_response_code(200);
    echo json_encode($weddings);
}

function getWedding(int $id): void {
    $wedding = getWeddingByIdModel($id);
    if (!$wedding) {
        http_response_code(404);
        echo json_encode(['error' => 'Boda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode($wedding);
}

function adminCreateWedding(): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    $id = createWeddingModel($input);
    http_response_code(201);
    echo json_encode(['id' => $id]);
}

function adminUpdateWedding(int $id): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    $rows = updateWeddingModel($id, $input);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Boda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Boda actualizada']);
}

function adminDeleteWedding(int $id): void {
    requireAdmin();
    $rows = deleteWeddingModel($id);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Boda no encontrada']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Boda eliminada']);
}
