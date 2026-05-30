<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $nueva_password = $_POST['nueva_password'] ?? '';

    if (!empty($email) && !empty($token) && !empty($nueva_password)) {
        try {
            // 1. Primero buscamos al usuario SOLO por correo para ver qué tiene en la BD
            $stmt_check = $pdo->prepare("SELECT id, token_recuperacion FROM usuarios WHERE email = ?");
            $stmt_check->execute([$email]);
            $usuario_bd = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$usuario_bd) {
                die("Error: El correo '$email' no existe en la base de datos.");
            }

            // 2. Comparamos lo que enviaste con lo que hay guardado
            if ($usuario_bd['token_recuperacion'] != $token) {
                echo "<div style='font-family:sans-serif; background:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; border-radius:8px; max-width:600px; margin:50px auto;'>";
                echo "<h3>❌ Error de Coincidencia en Base de Datos</h3>";
                echo "<p>El correo existe, pero los códigos no son iguales:</p>";
                echo "<ul>";
                echo "<li><strong>Código que tú escribiste:</strong> '$token' (Tipo: " . gettype($token) . ", Largo: " . strlen($token) . ")</li>";
                echo "<li><strong>Código guardado en la BD:</strong> '" . $usuario_bd['token_recuperacion'] . "' (Tipo: " . gettype($usuario_bd['token_recuperacion']) . ", Largo: " . strlen($usuario_bd['token_recuperacion'] ?? '') . ")</li>";
                echo "</ul>";
                echo "<p><strong>¿Cómo solucionarlo?</strong> Si el de la BD sale vacío (NULL) o es diferente, vuelve a solicitar un correo en recuperar.php, espera un par de segundos, y asegúrate de meter el ÚLTIMO código que te llegue sin hacer clics repetidos.</p>";
                echo "<br><a href='../nueva_clave.php?email=" . urlencode($email) . "' style='background:#721c24; color:white; padding:8px 15px; text-decoration:none; border-radius:4px;'>Volver a intentar</a>";
                echo "</div>";
                exit();
            }

            // 3. Si son iguales, procedemos a actualizar la contraseña
            $password_encriptada = password_hash($nueva_password, PASSWORD_BCRYPT);
            $update = $pdo->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE id = ?");
            $update->execute([$password_encriptada, $usuario_bd['id']]);

            echo "<script>alert('¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.'); window.location='../login.php';</script>";
            exit();

        } catch (PDOException $e) {
            die("Error en el servidor: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Por favor, completa todos los campos.'); window.location='../nueva_clave.php?email=" . urlencode($email) . "';</script>";
        exit();
    }
} else {
    header("Location: ../recuperar.php");
    exit();
}
?>