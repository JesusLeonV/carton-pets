<?php
// includes/db.php
$host = "mysql-db"; 
$user = "root";
$pass = "admin";
$db = "tienda_cajas";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Definimos AMBOS nombres para que no fallen tus otros archivos
    $conn = mysqli_connect($host, $user, $pass, $db);
    $conexion = $conn; // <--- Agregamos esta línea
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>