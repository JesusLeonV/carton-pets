<?php
declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'mysql-db';
$db = getenv('DB_NAME') ?: 'tienda_cajas';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'admin';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Error DB CartonPets: " . $e->getMessage());
    http_response_code(500);
    die("Error de conexion a la base de datos.");
}
