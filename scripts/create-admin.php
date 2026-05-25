<?php
// Usage: php scripts/create-admin.php <email> <password> [name]
// Example: php scripts/create-admin.php admin@eventos.com MiPass123 "Admin E&D"

require_once __DIR__ . '/../database/connection.php';

$email    = $argv[1] ?? null;
$password = $argv[2] ?? null;
$name     = $argv[3] ?? 'Administrador';

if (!$email || !$password) {
    echo "Uso: php scripts/create-admin.php <email> <password> [nombre]\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Error: email inválido.\n";
    exit(1);
}

if (strlen($password) < 8) {
    echo "Error: la contraseña debe tener al menos 8 caracteres.\n";
    exit(1);
}

try {
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        echo "Error: ya existe un usuario con ese email.\n";
        exit(1);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, 'admin') RETURNING id"
    );
    $stmt->execute([
        ':name'  => $name,
        ':email' => $email,
        ':hash'  => password_hash($password, PASSWORD_BCRYPT),
    ]);
    $id = $stmt->fetchColumn();
    echo "✓ Admin creado correctamente. ID: $id | Email: $email\n";
} catch (\Throwable $e) {
    echo "Error al crear el admin: " . $e->getMessage() . "\n";
    exit(1);
}
