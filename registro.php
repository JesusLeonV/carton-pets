<?php
require_once __DIR__ . '/includes/bootstrap.php';
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
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260630c">
    <style>
        body { background-color: #f9f7f2; font-family: 'Inter', sans-serif; }
        .auth-card { max-width: 450px; margin: 60px auto; background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-auth { background-color: #2d2a26; color: white; border-radius: 50px; padding: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: none; width: 100%; transition: 0.3s; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; }
        .logo-sm { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; font-size: 1.1rem; padding: 4px 8px; }
        .pw-toggle:hover { color: #333; }
        .pw-wrap { position: relative; }

    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container flex-grow-1 py-5">
        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-lg-5 d-none d-lg-block">
                <div class="cp-hero-panel h-100 p-5 d-flex flex-column justify-content-between">
                    <div><span class="cp-eyebrow mb-3">Cuenta CartonPets</span><h2 class="display-6 fw-bold">Compra más simple cuando lo necesites.</h2></div>
                    <div class="row g-3">
                        <div class="col-12"><div class="cp-benefit-card text-dark"><strong>Carrito protegido</strong><p class="small cp-muted mb-0">Tus productos quedan asociados a tu cuenta.</p></div></div>
                        <div class="col-12"><div class="cp-benefit-card text-dark"><strong>Memorial comunitario</strong><p class="small cp-muted mb-0">Puedes compartir un recuerdo de tu mascota.</p></div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-8">
        <div class="auth-card card p-4 p-md-5 h-100">
            <div class="text-center mb-4">
                <a href="index.php"><img src="assets/img/logocarton.png" alt="Logo" class="logo-sm mb-3"></a>
                <h3 class="fw-bold">Únete a CartonPets</h3>
                <p class="text-muted small">Crea una cuenta para gestionar tus pedidos</p>
            </div>

            <form action="procesos/procesarregistro.php" method="POST">
                <?php echo csrf_field(); ?>
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
                    <div class="pw-wrap"><input type="password" name="password" id="reg-password" class="form-control" placeholder="••••••••" required><button type="button" class="pw-toggle" onclick="togglePassword('reg-password', this)">👁️</button></div>
                    <div class="form-text mt-2" style="font-size: 0.75rem;">Mínimo 8 caracteres para tu seguridad.</div>
                </div>
                <button type="submit" class="btn-auth mb-3">REGISTRARME</button>
            </form>

            <div class="text-center mt-3">
                <p class="small text-muted">¿Ya tienes una cuenta? <a href="login.php" class="text-dark fw-bold text-decoration-none">Inicia Sesión</a></p>
            </div>
        </div>
            </div>
        </div>
    </div>
    <footer class="cp-footer py-5 mt-auto">
        <div class="container px-lg-5 text-center">
            <div class="cp-footer-brand justify-content-center mb-3">
                <img src="assets/img/logocarton.png" alt="CartonPets">
                <div class="text-start"><h5 class="fw-bold mb-0 text-white">CartonPets</h5><small>Despedida sustentable</small></div>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-3 small"><a href="index.php">Tienda</a><a href="login.php">Ingresar</a><a href="terminos.php">Términos</a><a href="https://wa.me/56951898299" target="_blank">WhatsApp</a></div>
        </div>
    </footer>
    <script>

        function togglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
