<?php
require_once __DIR__ . '/../models/auth.model.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/jwt.php';

function login(): void {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['email']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'email y password son requeridos']);
        return;
    }

    $user = findUserByEmailModel($input['email']);
    if (!$user || !password_verify($input['password'], $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales incorrectas']);
        return;
    }

    $token = jwtEncode([
        'sub'   => $user['id'],
        'email' => $user['email'],
        'role'  => $user['role'],
        'exp'   => time() + 86400 * 7,
    ], getJwtSecret());

    http_response_code(200);
    echo json_encode([
        'token' => $token,
        'user'  => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ],
    ]);
}

function register(): void {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'name, email y password son requeridos']);
        return;
    }

    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email inválido']);
        return;
    }

    if (strlen($input['password']) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
        return;
    }

    if (findUserByEmailModel($input['email'])) {
        http_response_code(409);
        echo json_encode(['error' => 'El email ya está registrado']);
        return;
    }

    try {
        $id = registerUserModel($input['name'], $input['email'], $input['password']);
        $token = jwtEncode([
            'sub'   => $id,
            'email' => $input['email'],
            'role'  => 'client',
            'exp'   => time() + 86400 * 7,
        ], getJwtSecret());

        http_response_code(201);
        echo json_encode(['token' => $token, 'id' => $id]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al registrar usuario']);
    }
}

function me(): void {
    $payload = requireAuth();
    $user    = findUserByIdModel((int) $payload['sub']);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }
    http_response_code(200);
    echo json_encode($user);
}
