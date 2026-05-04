<?php
require_once __DIR__ . '/../database/connection.php';
require_once __DIR__ . '/../controllers/user.controller.php';

function handleUserRoutes($method, $path) {
  // GET /users - Get all users
  if ($method === 'GET' && $path === '/users') {
    getAllUsers();
  }
  // GET /users/:id - Get user by id
  elseif ($method === 'GET' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
    $id = $matches[1];
    getUserById($id);
  }
  // POST /users - Create user
  elseif ($method === 'POST' && $path === '/users') {
    createUser();
  }
  // PUT /users/:id - Update user
  elseif ($method === 'PUT' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
    $id = $matches[1];
    updateUser($id);
  }
  // DELETE /users/:id - Delete user
  elseif ($method === 'DELETE' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
    $id = $matches[1];
    deleteUser($id);
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
  }
}
?>
