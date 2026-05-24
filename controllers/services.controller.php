<?php
require_once __DIR__ . '/../models/services.model.php';
require_once __DIR__ . '/../middleware/auth.php';

const VALID_CATEGORIES = ['ceremony', 'reception', 'food', 'others'];

function getServices(?string $category = null): void {
    if ($category !== null && !in_array($category, VALID_CATEGORIES, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Categoría inválida']);
        return;
    }
    $services = getServicesModel($category);
    http_response_code(200);
    echo json_encode($services);
}

function adminCreateService(): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['slug']) || empty($input['name']) || empty($input['category'])) {
        http_response_code(400);
        echo json_encode(['error' => 'slug, name y category son requeridos']);
        return;
    }
    if (!in_array($input['category'], VALID_CATEGORIES, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Categoría inválida']);
        return;
    }
    $id = createServiceModel($input);
    http_response_code(201);
    echo json_encode(['id' => $id]);
}

function adminUpdateService(int $id): void {
    requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    $rows = updateServiceModel($id, $input);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Servicio no encontrado']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Servicio actualizado']);
}

function adminDeleteService(int $id): void {
    requireAdmin();
    $rows = deleteServiceModel($id);
    if ($rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Servicio no encontrado']);
        return;
    }
    http_response_code(200);
    echo json_encode(['message' => 'Servicio eliminado']);
}
