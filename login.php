<?php
require_once __DIR__ . '/includes/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260630c">
    <style>
        /* Fondo verde ecológico unificado con el carrito */
        body { background-color: #927246; font-family: 'Inter', sans-serif; }
        
        /* Tarjeta de autenticación estilizada */
        .auth-card { 
            max-width: 450px; 
            margin: 40px auto; 
            background: white; 
            border-radius: 24px; 
            border: none; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.15); 
        }
        
        /* Botón principal café corporativo */
        .btn-auth { 
            background-color: #2d2a26; 
            color: white; 
            border-radius: 50px; 
            padding: 12px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            border: none; 
            width: 100%; 
            transition: 0.3s; 
        }
        .btn-auth:hover { background-color: #4a453f; color: white; }
        
        /* Campos del formulario */
        .form-control { border-radius: 12px; padding: 12px; border: 1px solid #e5dcd3; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(45, 42, 38, 0.1); border-color: #2d2a26; }
        
        /* Logotipo centralizado */
        .logo-responsive { width: 85px; height: 85px; border-radius: 50%; object-fit: contain; }
    
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; font-size: 1.1rem; padding: 4px 8px; }
        .pw-toggle:hover { color: #333; }
        .pw-wrap { position: relative; }

    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3 border-bottom border-secondary" style="background-color: var(--cafe-logo, #81a069) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <div class="logo-top-left">
                <a href="index.php">
                    <img src="assets/img/logocarton.png" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: contain;">
                </a>
            </div>
            <div class="user-utilities">
                <a href="registro.php" class="small text-white text-decoration-none fw-bold border border-light rounded-pill px-3 py-1" style="font-size: 0.75rem; background: rgba(255,255,255,0.1);">REGISTRARSE</a>
            </div>
        </div>
    </div>

    <div class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="row g-4 align-items-stretch w-100 justify-content-center">
            <div class="col-lg-5 d-none d-lg-block">
                <div class="cp-hero-panel h-100 p-5 d-flex flex-column justify-content-end">
                    <span class="cp-eyebrow mb-3">Área cliente</span>
                    <h2 class="fw-bold display-6 mb-3">Gestiona tus pedidos y tu memorial.</h2>
                    <p style="color: rgba(255,250,241,0.82);">Accede para comprar más rápido, revisar tu carrito y compartir recuerdos en el muro comunitario.</p>
                </div>
            </div>
            <div class="col-lg-5 col-md-8">
        <div class="auth-card card p-4 p-md-5 w-100 h-100">
            <div class="text-center mb-4">
                <img src="assets/img/logocarton.png" alt="Logo" class="logo-responsive mb-3">
                <h3 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Bienvenido</h3>
                <p class="text-muted small">Ingresa tus credenciales para continuar</p>
            </div>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger small py-2 text-center fw-bold" style="border-radius: 12px;">
                    ⚠️ Credenciales incorrectas. Inténtalo de nuevo.
                </div>
            <?php endif; ?>

            <form action="procesos/procesador_login.php" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-secondary text-uppercase m-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">Contraseña</label>
                        <a href="recuperar.php" class="text-muted small text-decoration-none fw-semibold" style="font-size: 0.75rem; transition: color 0.2s;" onmouseover="this.style.color='#2d2a26'" onmouseout="this.style.color='#6c757d'">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="pw-wrap"><input type="password" name="password" id="login-password" class="form-control" placeholder="••••••••" required><button type="button" class="pw-toggle" onclick="togglePassword('login-password', this)">👁️</button></div>
                </div>
                
                <button type="submit" class="btn-auth mb-3">ENTRAR</button>
            </form>

            <div class="text-center mt-2">
                <p class="small text-muted mb-2">¿No tienes cuenta? <a href="registro.php" class="text-dark fw-bold text-decoration-none">Crea una aquí</a></p>
                <a href="index.php" class="small text-secondary text-decoration-none fw-semibold">← Volver a la tienda</a>
            </div>
        </div>
            </div>
        </div>
    </div>

    <footer class="cp-footer py-5 mt-auto">
        <div class="container px-lg-5 text-center">
            <div class="cp-footer-brand justify-content-center mb-3">
                <img src="assets/img/logocarton.png" alt="CartonPets">
                <div class="text-start"><h5 class="fw-bold mb-0 text-white">CartonPets</h5><small>Compra segura y atención directa</small></div>
            </div>
            <p class="small mb-3">¿Necesitas ayuda inmediata? Escríbenos por WhatsApp o vuelve al catálogo.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3 small">
                <a href="index.php">Tienda</a><a href="registro.php">Crear cuenta</a><a href="terminos.php">Términos</a><a href="https://wa.me/56951898299" target="_blank">WhatsApp</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
