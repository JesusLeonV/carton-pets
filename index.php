<?php
session_start();
require_once 'includes/db.php';

// --- LOGICA DEL CARRITO ---
$conteo_carrito = 0;
if (isset($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $res = $stmt->fetch();
    $conteo_carrito = $res['total'] ?? 0;
}

// --- OBTENER PRODUCTOS DE LA DB (Sincronizado a ataudes) ---
$stmt = $pdo->query("SELECT * FROM productos");
$ataudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejo de cookies
$mostrarCookies = !isset($_COOKIE['cookies_aceptadas']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CartonPets | Despedida Sustentable</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        .navbar-collapse { background-color: var(--cafe-logo, #2d2a26); padding: 1rem; border-radius: 8px; margin-top: 0.5rem; }
        @media (min-width: 768px) {
            .navbar-collapse { background-color: transparent !important; padding: 0; margin-top: 0; }
            .border-start-md { border-left: 1px solid rgba(255, 255, 255, 0.2) !important; padding-left: 1rem !important; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-md navbar-dark py-2 px-lg-5" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand m-0" href="index.php">
                <img src="assets/img/logocarton.png" alt="Logo" class="logo-navbar" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid white; transition: transform 0.3s ease;">
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
                        <a class="nav-link text-danger small fw-bold py-1 px-2 border-start-md" href="logout.php">SALIR</a>
                    <?php else: ?>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="registro.php">REGISTRARSE</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="muro.php">MEMORIAL</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">TÉRMINOS</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div id="bannerCarousel" class="carousel slide carousel-fade style-gap-fix" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active"><img src="assets/img/banner_publicitario1.png" class="d-block w-100 img-fluid"></div>
            <div class="carousel-item"><img src="assets/img/banner_publicitario2.png" class="d-block w-100 img-fluid"></div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <section class="intro-text text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">Un adiós consciente, un retorno a la tierra.</h1>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Ofrecemos una alternativa digna y ecológica para el último adiós. Nuestros ataúdes de cartón corrugado son un tributo de paz y serenidad, diseñados para honrar el ciclo natural de la vida.</p>
        </section>

        <div class="row g-4 justify-content-center mb-5">
            <?php foreach ($ataudes as $ataud): ?>
            <div class="col-md-6 col-lg-4">
                <div class="product-card card shadow-sm border-0 h-100">
                    <img src="assets/img/<?php echo !empty($ataud['imagen']) ? $ataud['imagen'] : 'caja_memorial.jpg'; ?>" class="card-img-top">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h4 class="fw-bold m-0"><?php echo htmlspecialchars($ataud['nombre']); ?></h4>
                            <span class="fs-5 fw-bold text-dark">$<?php echo number_format($ataud['precio'], 0, ',', '.'); ?></span>
                        </div>
                        <p class="small fw-bold mb-2 texto-descripcion-ataud"><?php echo htmlspecialchars($ataud['descripcion']); ?></p>
                        
                        <form action="procesos/agregar_al_carrito.php" method="POST" class="mt-auto">
                            <input type="hidden" name="producto_id" value="<?php echo $ataud['id']; ?>">
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="number" name="cantidad" value="1" min="1" class="form-control rounded-pill text-center border-2 py-2">
                                </div>
                                <div class="col-8">
                                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">AÑADIR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="cookieBanner" class="cookie-banner shadow-lg" style="display: <?php echo $mostrarCookies ? 'block' : 'none'; ?>; position: fixed; bottom: 0; width: 100%; background: white; z-index: 999; border-top: 2px solid #2d2a26;">
        <div class="container d-md-flex align-items-center justify-content-between py-3 px-lg-5">
            <p class="mb-md-0 small text-muted">Utilizamos cookies para acompañar tu experiencia con respeto y calidez. ¿Aceptas el uso en nuestro sistema?</p>
            <button onclick="aceptarCookies()" class="btn btn-dark btn-sm rounded-pill px-4">ACEPTAR</button>
        </div>
    </div>

    <div class="modal fade" id="modalInvitacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div style="height: 6px; background: var(--cafe-logo, #2d2a26);"></div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <img src="assets/img/duda.png" alt="Reflexión" class="img-fluid rounded-3" style="max-height: 180px; object-fit: cover;">
                    </div>
                    <h4 class="fw-bold mb-3" style="color: var(--cafe-logo, #2d2a26); letter-spacing: 0.5px;">¿Llevas una huella eterna en tu corazón?</h4>
                    <p class="text-muted small px-2" style="line-height: 1.6; font-size: 0.95rem;">
                        Si tuviste o tienes a un compañero especial que ya partió, sabemos lo profundo de ese lazo. ¿Te gustaría honrar su vida compartiendo su hermosa fotografía en nuestro muro comunitario para recordarlo por siempre?
                    </p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4 pt-2">
                        <a href="colaboradores.php" class="btn btn-dark rounded-pill px-4 py-2 fw-bold small">
                            SÍ, QUIERO HONRARLO
                        </a>
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary small border" data-bs-dismiss="modal">
                            No, no tengo / Ahora no
                        </button>
                    </div>
                </div>
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

    <a href="https://wa.me/56951898299" class="btn-whatsapp-float" target="_blank">
        <img src="assets/img/logowatsap.png" alt="WhatsApp">
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function aceptarCookies() {
            document.cookie = "cookies_aceptadas=true; max-age=" + (60*60*24*30) + "; path=/";
            document.getElementById('cookieBanner').style.display = 'none';
        }

        document.addEventListener("DOMContentLoaded", function() {
            var contenedorModal = document.getElementById('modalInvitacion');
            if (contenedorModal) {
                var miModal = new bootstrap.Modal(contenedorModal);
                miModal.show();
            }
        });
    </script>
</body>
</html>