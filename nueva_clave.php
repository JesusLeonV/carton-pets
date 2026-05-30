<?php 
session_start();
require_once __DIR__ . '/includes/db.php'; 

// --- LOGICA DEL CARRITO (Igual al index por si quieren navegar) ---
$conteo_carrito = 0;
if (isset($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $res = $stmt->fetch();
    $conteo_carrito = $res['total'] ?? 0;
}

$email_get = $_GET['email'] ?? ''; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer Nueva Clave | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f5;
        }
        .navbar-collapse { background-color: #81a069; padding: 1rem; border-radius: 8px; margin-top: 0.5rem; }
        @media (min-width: 768px) {
            .navbar-collapse { background-color: transparent !important; padding: 0; margin-top: 0; }
            .border-start-md { border-left: 1px solid rgba(255, 255, 255, 0.3) !important; padding-left: 1rem !important; }
        }
        .btn-custom-action {
            background-color: #2d2a26 !important;
            color: white !important;
            transition: background-color 0.3s ease;
        }
        .btn-custom-action:hover {
            background-color: #4a453f !important;
        }
        .text-brown-custom {
            color: #2d2a26;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-md navbar-dark py-2 px-lg-5" style="background-color: #81a069 !important;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand m-0" href="index.php">
                <img src="assets/img/logocarton.png" alt="Logo" class="logo-navbar" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
            </a>

            <div class="d-flex align-items-center gap-2 order-md-last">
                <?php if(isset($_SESSION['user_nombre'])): ?>
                    <span class="text-white small fw-bold px-2 py-1 rounded" style="background: rgba(255,255,255,0.1); font-size: 0.75rem;">
                        👋 <?php echo strtoupper(htmlspecialchars(explode(' ', trim($_SESSION['user_nombre']))[0])); ?>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">INGRESAR</a>
                <?php endif; ?>

                <a href="carrito.php" class="btn text-white position-relative px-2 d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16"><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>
                    <?php if($conteo_carrito > 0): ?>
                        <span class="badge rounded-pill bg-light text-dark" style="font-size: 0.65rem; position: absolute; top: -5px; right: -5px;"><?php echo $conteo_carrito; ?></span>
                    <?php endif; ?>
                </a>

                <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            </div>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-md-center gap-2 gap-md-3 pt-2 pt-md-0">
                    <?php if(isset($_SESSION['user_nombre'])): ?>
                        <?php 
                        $email_check = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';
                        if($email_check === 'cartonpets@gmail.com'): 
                        ?>
                            <a class="nav-link text-warning small fw-bold py-2 px-3 border border-warning rounded" href="admin.php">🛠️ EDITAR TIENDA</a>
                        <?php endif; ?>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="muro.php">MEMORIAL</a>
                        <a class="nav-link text-white small fw-bold py-1 px-2 border-start-md" href="logout.php" style="color: #fff !important; background: rgba(255,0,0,0.2); border-radius: 20px;">SALIR</a>
                    <?php else: ?>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="registro.php">REGISTRARSE</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="muro.php">MEMORIAL</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">TÉRMINOS</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="max-width: 450px; width: 100%;">
            <div style="height: 6px; background: var(--cafe-logo, #2d2a26); mb-4"></div>
            <div class="card-body p-4 p-sm-5">
                
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-brown-custom mb-2">Establecer Nueva Clave</h3>
                    <p class="text-muted small">Ingresa el código temporal de 6 dígitos que enviamos a tu bandeja de entrada junto con tu nueva clave de acceso.</p>
                </div>

                <form action="procesos/actualizar_password.php" method="POST" autocomplete="off">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_get); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-brown-custom">Código de 6 dígitos</label>
                        <input type="text" name="token" class="form-control rounded-pill border-2 py-2 px-3 text-center fw-bold" placeholder="Ej: 965369" required autocomplete="one-time-code" style="letter-spacing: 1px;">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-brown-custom">Nueva Contraseña</label>
                        <input type="password" name="nueva_password" class="form-control rounded-pill border-2 py-2 px-3" placeholder="Escribe tu nueva clave" required autocomplete="new-password">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom-action rounded-pill fw-bold py-2">ACTUALIZAR CONTRASEÑA</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="login.php" class="text-decoration-none small text-muted">← Volver al inicio de sesión</a>
                </div>

            </div>
        </div>
    </main>

    <footer class="py-5 mt-auto" style="background-color: #81a069 !important;">
        <div class="container text-center text-md-start px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 text-white">NUESTRA MISIÓN</h6>
                    <p class="small text-white">Ofrecer una despedida que sea una declaración de amor por la naturaleza.</p>
                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-3 text-white">PROYECTO CARTONPETS</h6>
                    <p class="small text-white">Compromiso inquebrantable desde Iquique para todo Chile.</p>
                    <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">Términos y condiciones</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold mb-3 text-white">CONTACTO</h6>
                    <p class="small text-white m-0">cartonpets@gmail.com</p>
                    <div class="mt-4 x-small text-white" style="font-size: 0.7rem; opacity: 0.9;">&copy; 2026 JESUS LEON | INGENIERÍA EN INFORMÁTICA</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>