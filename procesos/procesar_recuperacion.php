<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// =========================================================================
// CORRECCIÓN DE RUTAS: Importamos PHPMailer desde la raíz del proyecto
// =========================================================================
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $email = trim(strtolower($_POST['email'] ?? ''));

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $token = (string) random_int(100000, 999999);
                $tokenHash = password_hash($token, PASSWORD_DEFAULT);
                $tokenExpira = date('Y-m-d H:i:s', time() + 900);
                
                $update = $pdo->prepare("UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE email = ?");
                $update->execute([$tokenHash, $tokenExpira, $email]);

                $mail = new PHPMailer(true);

                try {
                    $smtpUser = getenv('SMTP_USER') ?: 'cartonpets@gmail.com';
                    $smtpPass = getenv('SMTP_PASS') ?: '';

                    if ($smtpPass === '') {
                        throw new Exception('SMTP_PASS no configurado.');
                    }

                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpUser;
                    $mail->Password   = $smtpPass;
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
                    
                    redirect("../nueva_clave.php?email=" . urlencode($email) . "&status=enviado");

                } catch (Exception $e) {
                    error_log('Error correo recuperacion CartonPets: ' . $e->getMessage() . ' ' . $mail->ErrorInfo);
                    redirect('../recuperar.php?error=correo');
                }

            } else {
                redirect('../recuperar.php?status=enviado');
            }
        } catch (PDOException $e) {
            error_log('Error recuperacion CartonPets: ' . $e->getMessage());
            redirect('../recuperar.php?error=server');
        }
    } else {
        redirect('../recuperar.php?error=email');
    }
} else {
    redirect('../recuperar.php');
}
