<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $email  = trim(strtolower((string) ($_POST['email'] ?? '')));
    $pass   = (string) ($_POST['password'] ?? '');

    if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
        redirect('../registro.php?error=datos');
    }

    $password_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $email, $password_encriptada]);
        redirect('../login.php?registro=ok');
    } catch (PDOException $e) {
        error_log('Error registro CartonPets: ' . $e->getMessage());
        redirect('../registro.php?error=registro');
    }
}

redirect('../registro.php');
