<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #927246; font-family: 'Inter', sans-serif; }
        .auth-card { max-width: 450px; margin: 40px auto; background: white; border-radius: 24px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
        .btn-auth { background-color: #2d2a26; color: white; border-radius: 50px; padding: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: none; width: 100%; transition: 0.3s; }
        .btn-auth:hover { background-color: #4a453f; color: white; }
        .form-control { border-radius: 12px; padding: 12px; border: 1px solid #e5dcd3; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(45, 42, 38, 0.1); border-color: #2d2a26; }
        .logo-responsive { width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 2px solid #e5dcd3; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3 border-bottom border-secondary" style="background-color: var(--cafe-logo, #81a069) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <div class="logo-top-left">
                <a href="index.php">
                    <img src="assets/img/logocarton.png" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
            </div>
            <div class="user-utilities">
                <a href="login.php" class="small text-white text-decoration-none fw-bold border border-light rounded-pill px-3 py-1" style="font-size: 0.75rem; background: rgba(255,255,255,0.1);">INGRESAR</a>
            </div>
        </div>
    </div>

    <div class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="auth-card card p-4 p-md-5 w-100">
            <div class="text-center mb-4">
                <img src="assets/img/logocarton.png" alt="Logo" class="logo-responsive mb-3">
                <h3 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Recuperar Acceso</h3>
                <p class="text-muted small">Introduce tu correo y te enviaremos las instrucciones de restablecimiento.</p>
            </div>

            <?php if(isset($_GET['status']) && $_GET['status'] === 'enviado'): ?>
                <div class="alert alert-success small py-2 text-center fw-bold" style="border-radius: 12px;">
                    📩 Código enviado. Revisa tu bandeja de entrada.
                </div>
            <?php endif; ?>

            <form action="procesos/procesar_recuperacion.php" method="POST">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
                <button type="submit" class="btn-auth mb-3">Enviar Instrucciones</button>
            </form>

            <div class="text-center mt-2">
                <a href="login.php" class="small text-secondary text-decoration-none fw-semibold">← Volver al Login</a>
            </div>
        </div>
    </div>

    <footer class="py-5 mt-auto" style="background-color: var(--cafe-logo, #81a069) !important;">
        <div class="container text-center text-md-start px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 text-white">NUESTRA MISIÓN</h6>
                    <p class="small text-white-50">Ofrecer una despedida que sea una declaración de amor por la naturaleza.</p>
                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-3 text-white">PROYECTO CARTONPETS</h6>
                    <p class="small text-white-50">Compromiso inquebrantable desde Iquique para todo Chile.</p>
                    <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">Términos y condiciones</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold mb-3 text-white">CONTACTO</h6>
                    <p class="small text-white m-0">cartonpets@gmail.com</p>
                    <div class="mt-4 x-small text-white-50" style="font-size: 0.7rem;">&copy; 2026 JESUS LEON | INGENIERÍA EN INFORMÁTICA</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>