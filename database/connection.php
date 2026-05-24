<?php
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

if ($databaseUrl) {
    $dsn = str_replace('postgresql://', 'pgsql://', $databaseUrl);
    $dsn = preg_replace('#pgsql://([^:]+):([^@]+)@([^/]+)/(.+)#', 'pgsql:host=$3;dbname=$4', $dsn);
    $parsed   = parse_url($databaseUrl);
    $user     = $parsed['user']     ?? '';
    $password = $parsed['pass']     ?? '';
} else {
    $host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'localhost';
    $port     = $_ENV['DB_PORT']     ?? getenv('DB_PORT')     ?: '5432';
    $dbname   = $_ENV['DB_NAME']     ?? getenv('DB_NAME')     ?: 'eventosdecoraciones';
    $user     = $_ENV['DB_USER']     ?? getenv('DB_USER')     ?: 'admin';
    $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
    $dsn      = "pgsql:host={$host};port={$port};dbname={$dbname}";
}

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Error conectando a la base de datos: ' . $e->getMessage()]));
}
