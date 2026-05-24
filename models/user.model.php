<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function getUsersModel(): array|false {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id");
    return $stmt ? $stmt->fetchAll() : false;
}

function getUserByIdModel(int $id): array|null|false {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id");
    if (!$stmt->execute([':id' => $id])) return false;
    $user = $stmt->fetch();
    return $user ?: null;
}

function createUserModel(array $data): int|false {
    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role) RETURNING id"
    );
    $ok = $stmt->execute([
        ':name'          => $data['name']          ?? '',
        ':email'         => $data['email']         ?? '',
        ':password_hash' => password_hash($data['password'] ?? '', PASSWORD_BCRYPT),
        ':role'          => $data['role']           ?? 'client',
    ]);
    return $ok ? (int) $stmt->fetchColumn() : false;
}

function updateUserModel(int $id, array $data): int|false {
    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE users SET name=:name, email=:email, role=:role WHERE id=:id"
    );
    $ok = $stmt->execute([
        ':name'  => $data['name']  ?? null,
        ':email' => $data['email'] ?? null,
        ':role'  => $data['role']  ?? 'client',
        ':id'    => $id,
    ]);
    return $ok ? $stmt->rowCount() : false;
}

function deleteUserModel(int $id): int|false {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    return $stmt->execute([':id' => $id]) ? $stmt->rowCount() : false;
}
