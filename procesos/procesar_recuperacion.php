<?php
session_start();
require_once '../includes/db.php';

// =========================================================================
// CORRECCIÓN DE RUTAS: Importamos PHPMailer desde la raíz del proyecto
// =========================================================================
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));

    if (!empty($email)) {
        try {
            // 1. Verificamos si el correo existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // 2. Generamos el código de 6 números
                $token = rand(100000, 999999);
                
                // 3. Lo guardamos en la BD para ese usuario
                $update = $pdo->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE email = ?");
                $update->execute([$token, $email]);

                // 4. CONFIGURACIÓN Y ENVÍO REAL CON PHPMailer VIA SMTP
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor SMTP de Google
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'cartonpets@gmail.com'; // ✅ Cuenta oficial de la tienda
                    $mail->Password   = 'fsytdqmhgucfkgwz';       // ✅ Tu clave de aplicación de 16 letras integrada
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    // Remitente y Destinatario
                    $mail->setFrom('no-reply@cartonpets.cl', 'CartonPets Soporte');
                    $mail->addAddress($email);

                    // Contenido del correo con el diseño corporativo café
                    $mail->isHTML(true);
                    $mail->Subject = 'Código de Recuperación de Contraseña - CartonPets';
                    
                    $mail->Body = "
                    <div style='background-color: #f4f7f5; padding: 40px 20px; font-family: sans-serif;'>
                        <div style='max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center;'>
                            <h2 style='color: #2d2a26; margin-bottom: 10px;'>Restablecer tu Contraseña</h2>
                            <p style='color: #666; font-size: 15px;'>Has solicitado recuperar tu acceso al sistema de CartonPets. Usa el siguiente código temporal para configurar tu nueva clave:</p>
                            <div style='background: #f4f7f5; padding: 15px 30px; font-size: 32px; font-weight: bold; color: #927246; border: 2px dashed #2d2a26; display: inline-block; border-radius: 12px; margin: 20px 0; letter-spacing: 2px;'>
                                $token
                            </div>
                            <p style='color: #888; font-size: 12px; margin-top: 15px;'>Si tú no realizaste esta solicitud, puedes ignorar este correo de manera segura.</p>
                        </div>
                    </div>
                    ";

                    $mail->send();
                    
                    // Redirección directa hacia la raíz, donde debe estar nueva_clave.php
                    header("Location: ../nueva_clave.php?email=" . urlencode($email) . "&status=enviado");
                    exit();

                } catch (Exception $e) {
                    die("El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}");
                }

            } else {
                echo "<script>alert('Ese correo no está registrado en CartonPets'); window.location='../recuperar.php';</script>";
                exit();
            }
        } catch (PDOException $e) {
            die("Error en el servidor: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Por favor, ingresa un correo válido.'); window.location='../recuperar.php';</script>";
        exit();
    }
} else {
    header("Location: ../recuperar.php");
    exit();
}
?>