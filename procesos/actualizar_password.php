<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $email = trim(strtolower($_POST['email'] ?? ''));
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $nueva_password = $_POST['nueva_password'] ?? '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^\d{6}$/', $token) && strlen($nueva_password) >= 8) {
        try {
            $stmt_check = $pdo->prepare("SELECT id, token_recuperacion, token_expira FROM usuarios WHERE email = ? LIMIT 1");
            $stmt_check->execute([$email]);
            $usuario_bd = $stmt_check->fetch();

            if (!$usuario_bd || empty($usuario_bd['token_recuperacion']) || empty($usuario_bd['token_expira']) || strtotime($usuario_bd['token_expira']) < time() || !password_verify($token, $usuario_bd['token_recuperacion'])) {
                redirect('../nueva_clave.php?email=' . urlencode($email) . '&error=token');
            }

            $password_encriptada = password_hash($nueva_password, PASSWORD_BCRYPT);
            $update = $pdo->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL, token_expira = NULL WHERE id = ?");
            $update->execute([$password_encriptada, $usuario_bd['id']]);

            redirect('../login.php?password=updated');

        } catch (PDOException $e) {
            error_log('Error actualizar password CartonPets: ' . $e->getMessage());
            redirect('../nueva_clave.php?email=' . urlencode($email) . '&error=server');
        }
    } else {
        redirect('../nueva_clave.php?email=' . urlencode($email) . '&error=datos');
    }
} else {
    redirect('../recuperar.php');
}
