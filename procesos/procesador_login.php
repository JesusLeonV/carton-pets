<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(strtolower($_POST['email'])); 
    $password = $_POST['password'];

    // ACCESO DE RESCATE: Si eres tú con tus credenciales, pasas directo sin mirar la DB
    if ($email === 'cartonpets@gmail.com' && $password === 'admin1234') {
        $_SESSION['usuario_id']  = 1; // ID genérico de admin
        $_SESSION['user_nombre'] = 'Admin CartonPets';
        $_SESSION['user_email']  = 'cartonpets@gmail.com';

        header("Location: ../index.php");
        exit();
    }

    // Flujo normal para otros usuarios si los hay
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id']  = $usuario['id'];
            $_SESSION['user_nombre'] = $usuario['nombre'];
            $_SESSION['user_email']  = $email;
            header("Location: ../index.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    header("Location: ../login.php?error=1");
    exit();
}