<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $pass   = $_POST['password'];

    $password_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $email, $password_encriptada]);
        echo "<script>alert('Registro exitoso'); window.location='../login.php';</script>";
    } catch (PDOException $e) {
        // AQUÍ VEMOS EL ERROR REAL
        die("Error real de base de datos: " . $e->getMessage());
    }
}
?>