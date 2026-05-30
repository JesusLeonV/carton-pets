<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #f9f7f2; font-family: 'Inter', sans-serif; }
        .auth-card { max-width: 450px; margin: 60px auto; background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-auth { background-color: #2d2a26; color: white; border-radius: 50px; padding: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: none; width: 100%; transition: 0.3s; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; }
        .logo-sm { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card card p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="index.php"><img src="assets/img/logocarton.png" alt="Logo" class="logo-sm mb-3"></a>
                <h3 class="fw-bold">Únete a CartonPets</h3>
                <p class="text-muted small">Crea una cuenta para gestionar tus pedidos</p>
            </div>

            <form action="procesos/procesarregistro.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Peréz" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    <div class="form-text mt-2" style="font-size: 0.75rem;">Mínimo 8 caracteres para tu seguridad.</div>
                </div>
                <button type="submit" class="btn-auth mb-3">REGISTRARME</button>
            </form>

            <div class="text-center mt-3">
                <p class="small text-muted">¿Ya tienes una cuenta? <a href="login.php" class="text-dark fw-bold text-decoration-none">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
    <footer class="py-5 mt-auto" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container text-center text-md-start px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 text-white">NUESTRA MISIÓN</h6>
                    <p class="small text-white-50">Ofrecer una despedida que sea una declaración de amor por la naturaleza.</p>

                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-3 text-white">PROYECTO CARTONPETS</h6>
                    <p class="small text-white-50">Compromiso inquebrantable desde Iquique para todo Chile.</p>
                    <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">Terminos y condiciones</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold mb-3 text-white">CONTACTO</h6>
                    <p class="small text-white m-0">cartonpets@gmail.com</p>
                    <div class="mt-4 x-small text-white-50" style="font-size: 0.7rem;">&copy; 2026 JESUS LEON | INGENIERÍA EN INFORMÁTICA</div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>