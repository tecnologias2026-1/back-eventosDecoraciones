<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function findUserByEmailModel(string $email): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() ?: null;
}

function findUserByIdModel(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function registerUserModel(string $name, string $email, string $password): int {
    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, 'client') RETURNING id"
    );
    $stmt->execute([
        ':name'  => $name,
        ':email' => $email,
        ':hash'  => password_hash($password, PASSWORD_BCRYPT),
    ]);
    return (int) $stmt->fetchColumn();
}
