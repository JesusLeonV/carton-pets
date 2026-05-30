<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Recuerdo | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Organiza el cuerpo en una columna vertical */
        body { 
            background-color: #81a069 !important; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            margin: 0; 
        }
        /* Contenedor intermedio para centrar perfectamente la tarjeta */
        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .memorial-upload-card { 
            background: white !important; 
            border-radius: 25px !important; 
            padding: 40px !important; 
            width: 100%; 
            max-width: 450px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important; 
            text-align: center; 
        }
        .logo-fix { width: 120px !important; height: 120px !important; border-radius: 50%; border: 4px solid #85695f; margin-bottom: 20px; }
        .btn-custom { background-color: #85695f !important; color: white !important; border-radius: 50px !important; padding: 12px !important; font-weight: bold !important; border: none !important; }
        .btn-custom:hover { background-color: #5d4037 !important; color: white !important; }
        .form-control-custom { background-color: #f8f9fa !important; border: none !important; padding: 12px !important; border-radius: 15px !important; }
    </style>
</head>
<body>

    <div class="content-wrapper">
        <div class="memorial-upload-card">
            <img src="assets/img/logocarton.png" alt="Logo" class="logo-fix">
            
            <h3 class="fw-bold mb-1" style="color: #85695f;">COMPARTIR RECUERDO</h3>
            <p class="text-muted small mb-4">Sube una foto para nuestro muro comunitario.</p>
            
            <form action="procesos/procesar_muro.php" method="POST" enctype="multipart/form-data" class="text-start">
                <input type="hidden" name="accion" value="crear">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: #85695f;">NOMBRE DE TU MASCOTA</label>
                    <input type="text" name="nombre_mascota" class="form-control form-control-custom" placeholder="Ej: Pelusa" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold" style="color: #85695f;">NACIMIENTO</label>
                        <input type="text" name="fecha_nacimiento" class="form-control form-control-custom" placeholder="Ej: 2015 o 12/04/2015">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold" style="color: #85695f;">FALLECIMIENTO</label>
                        <input type="text" name="fecha_fallecimiento" class="form-control form-control-custom" placeholder="Ej: 2026 o 18/05/2026">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold" style="color: #85695f;">SELECCIONAR FOTO</label>
                    <input type="file" name="foto" class="form-control form-control-custom" accept="image/*" required>
                </div>
                
                <button type="submit" class="btn btn-custom w-100 mb-3">PUBLICAR EN EL MURO</button>
            </form>
            
            <div class="d-flex justify-content-center gap-3 mt-2">
                <a href="muro.php" class="small fw-bold text-decoration-none" style="color: #85695f;">VER EL MURO</a>
                <span class="text-muted">|</span>
                <a href="index.php" class="small text-muted text-decoration-none">VOLVER A TIENDA</a>
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
