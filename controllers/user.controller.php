<?php
require_once __DIR__ . '/../models/user.model.php';

function getAllUsers(): void {
  $users = getUsersModel();
  if ($users === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener usuarios']);
    return;
  }
  http_response_code(200);
  echo json_encode($users);
}

function getUserById($id): void {
  $user = getUserByIdModel((int) $id);
  if ($user === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener usuario']);
    return;
  }
  if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    return;
  }
  http_response_code(200);
  echo json_encode($user);
}

function createUser(): void {
  $input = json_decode(file_get_contents('php://input'), true);

  if (!$input || !isset($input['name'], $input['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    return;
  }

  $result = createUserModel($input);
  if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear usuario']);
    return;
  }

  http_response_code(201);
  echo json_encode(['message' => 'Usuario creado', 'id' => $result]);
}

function updateUser($id): void {
  $input = json_decode(file_get_contents('php://input'), true);

  if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    return;
  }

  $result = updateUserModel((int) $id, $input);
  if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar usuario']);
    return;
  }
  if ($result === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    return;
  }

  http_response_code(200);
  echo json_encode(['message' => 'Usuario actualizado']);
}

function deleteUser($id): void {
  $result = deleteUserModel((int) $id);
  if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar usuario']);
    return;
  }
  if ($result === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    return;
  }

  http_response_code(200);
  echo json_encode(['message' => 'Usuario eliminado']);
}
?>
