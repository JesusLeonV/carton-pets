<?php
require_once __DIR__ . '/includes/bootstrap.php';

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
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260714">
    <style>
        body { background-color: #81a069 !important; display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        .content-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .memorial-upload-card { background: white !important; border-radius: 25px !important; padding: 40px !important; width: 100%; max-width: 480px; box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important; text-align: center; }
        .logo-fix { width: 100px !important; height: 100px !important; border-radius: 50%; border: 4px solid #85695f; margin-bottom: 20px; }
        .btn-custom { background-color: #85695f !important; color: white !important; border-radius: 50px !important; padding: 12px !important; font-weight: bold !important; border: none !important; }
        .btn-custom:hover { background-color: #5d4037 !important; color: white !important; }
        .form-control-custom { background-color: #f8f9fa !important; border: 1px solid #e5dcd3 !important; padding: 12px !important; border-radius: 15px !important; }
        .form-control-custom:focus { border-color: #85695f !important; box-shadow: 0 0 0 3px rgba(133,105,95,0.15) !important; }
        .preview-img { width: 200px; height: 200px; object-fit: cover; border-radius: 20px; border: 3px dashed #ccc; display: none; margin: 1rem auto; }
    </style>
</head>
<body>

    <div class="content-wrapper">
        <div class="row g-4 align-items-stretch justify-content-center w-100">
            <div class="col-lg-5 d-none d-lg-block">
                <div class="cp-hero-panel h-100 p-5 d-flex flex-column justify-content-end">
                    <span class="cp-eyebrow mb-3">Memorial</span>
                    <h2 class="display-6 fw-bold mb-3">Comparte una huella que merece ser recordada.</h2>
                    <p style="color: rgba(255,250,241,0.82);">Sube una foto, nombre y fechas. El recuerdo quedará visible en el muro comunitario de CartonPets.</p>
                </div>
            </div>
            <div class="col-lg-5 col-md-8">
        <div class="memorial-upload-card h-100">
            <img src="assets/img/logocarton.png" alt="Logo" class="logo-fix">
            
            <h3 class="fw-bold mb-1" style="color: #85695f;">COMPARTIR RECUERDO</h3>
            <p class="text-muted small mb-4">Sube una foto para nuestro muro comunitario.</p>
            
            <form action="procesos/procesar_muro.php" method="POST" enctype="multipart/form-data" class="text-start" id="formMemorial">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="accion" value="crear">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: #85695f;">NOMBRE DE TU MASCOTA *</label>
                    <input type="text" name="nombre_mascota" class="form-control form-control-custom" placeholder="Ej: Pelusa" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold" style="color: #85695f;">FECHA DE NACIMIENTO</label>
                        <input type="date" name="fecha_nacimiento" class="form-control form-control-custom">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold" style="color: #85695f;">FECHA DE FALLECIMIENTO</label>
                        <input type="date" name="fecha_fallecimiento" class="form-control form-control-custom">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: #85695f;">TESTIMONIO / MENSAJE</label>
                    <textarea name="testimonio" class="form-control form-control-custom" rows="3" placeholder="Comparte un recuerdo especial de tu mascota..."></textarea>
                </div>

                <div class="mb-4 text-center">
                    <label class="form-label small fw-bold d-block" style="color: #85695f;">FOTO DE TU MASCOTA *</label>
                    <label for="fotoInput" class="d-block" style="cursor: pointer;">
                        <img id="previewImg" class="preview-img" alt="Vista previa">
                        <div id="uploadPlaceholder" class="p-4 border border-2 border-dashed rounded-4" style="border-color: #ccc; color: #999;">
                            📷 Click para seleccionar foto
                        </div>
                    </label>
                    <input type="file" name="foto" id="fotoInput" accept="image/*" class="d-none" required>
                </div>

                <button type="submit" class="btn btn-custom w-100 rounded-pill fw-bold py-3">PUBLICAR RECUERDO</button>
            </form>

            <a href="muro.php" class="d-block mt-3 small text-muted text-decoration-none">← Volver al Muro</a>
        </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    document.getElementById('previewImg').src = ev.target.result;
                    document.getElementById('previewImg').style.display = 'block';
                    document.getElementById('uploadPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
