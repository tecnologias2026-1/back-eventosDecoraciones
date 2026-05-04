<?php
// Database connection
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'tecnologias_db');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
  die(json_encode(['error' => 'Error conectando a la base de datos: ' . $conn->connect_error]));
}

// Set charset
$conn->set_charset('utf8');

echo "Conectado a la base de datos\n";

$conn->close();
?>
