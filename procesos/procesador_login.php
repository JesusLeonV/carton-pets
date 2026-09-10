<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $email = trim(strtolower((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        redirect('../login.php?error=1');
    }

    // Flujo normal para otros usuarios si los hay
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']  = $usuario['id'];
            $_SESSION['user_nombre'] = $usuario['nombre'];
            $_SESSION['user_email']  = $usuario['email'];
            redirect('../index.php');
        }
    } catch (PDOException $e) {
        error_log('Error login CartonPets: ' . $e->getMessage());
    }

    redirect('../login.php?error=1');
}

redirect('../login.php');
