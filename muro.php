<?php
session_start();
require_once 'includes/db.php';

$pdo->exec("SET lc_time_names = 'es_ES'");

$sql = "SELECT DATE_FORMAT(fecha_registro, '%M %Y') as mes_anio, nombre_mascota, fecha_nacimiento, fecha_fallecimiento, foto_ruta, usuario_correo 
        FROM colaboradores 
        ORDER BY fecha_registro DESC";

$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

$correo_logueado = $_SESSION['user_email'] ?? '';
$admin_autorizado = 'cartonpets@gmail.com'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muro de Recuerdo | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        .btn-gestion { font-size: 0.7rem; padding: 3px 10px; }
        .panel-gestion { background: rgba(0,0,0,0.03); border-top: 1px solid rgba(0,0,0,0.05); padding-top: 8px; margin-top: 8px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3" style="background-color: #2d2a26;">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php"><img src="assets/img/logocarton.png" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white;"></a>
            <a href="colaboradores.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">SUBIR MI FOTO</a>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <h2 class="text-center fw-bold text-white mb-5">Muro del Recuerdo</h2>

        <?php if(empty($registros)): ?>
            <div class="text-center py-5">
                <p class="text-white">Aún no hay recuerdos. ¡Sé el primero!</p>
                <a href="colaboradores.php" class="btn btn-light rounded-pill px-5 fw-bold">SUBIR FOTO</a>
            </div>
        <?php endif; ?>

        <?php foreach ($registros as $mes => $mascotas): ?>
            <div class="mes-separator text-uppercase fw-bold mb-4" style="color: #2d2a26; border-bottom: 2px solid #ccc;"><?php echo htmlspecialchars($mes); ?></div>
            <div class="row g-4 mb-5">
                <?php foreach ($mascotas as $m): 
                    $img_src = (file_exists("assets/img/colaboradores/" . $m['foto_ruta'])) ? "assets/img/colaboradores/" . $m['foto_ruta'] : "assets/img/default-pet.png";
                    $es_dueno_o_admin = ($m['usuario_correo'] === $correo_logueado || $correo_logueado === $admin_autorizado);
                ?>
                    <div class="col-md-3 text-center">
                        <div class="p-3 bg-white rounded shadow-sm">
                            <img src="<?php echo $img_src; ?>" class="img-fluid mb-2">
                            <div class="fw-bold text-uppercase"><?php echo htmlspecialchars($m['nombre_mascota']); ?></div>
                            <div class="small">🕊️ <?php echo htmlspecialchars($m['fecha_nacimiento']); ?> – <?php echo htmlspecialchars($m['fecha_fallecimiento']); ?></div>
                            
                            <?php if ($es_dueno_o_admin): ?>
                                <div class="panel-gestion">
                                    <button onclick="editarNombre('<?php echo $m['foto_ruta']; ?>', '<?php echo htmlspecialchars($m['nombre_mascota']); ?>')" class="btn btn-outline-secondary btn-sm">EDITAR</button>
                                    <form action="procesos/procesar_muro.php" method="POST" class="d-inline">
                                        <input type="hidden" name="foto_ruta" value="<?php echo $m['foto_ruta']; ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn btn-danger btn-sm">ELIMINAR</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </main>

    <form id="formEditar" action="procesos/procesar_muro.php" method="POST" style="display:none;">
        <input type="hidden" name="foto_ruta" id="editFotoRuta">
        <input type="hidden" name="nuevo_nombre" id="editNuevoNombre">
        <input type="hidden" name="accion" value="editar">
    </form>

    <script>
        function editarNombre(fotoRuta, nombreActual) {
            let nuevoNombre = prompt("Modifica el nombre:", nombreActual);
            if (nuevoNombre) {
                document.getElementById('editFotoRuta').value = fotoRuta;
                document.getElementById('editNuevoNombre').value = nuevoNombre;
                document.getElementById('formEditar').submit();
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            var contenedorModal = document.getElementById('modalInvitacion');
            if (contenedorModal) {
                var miModal = new bootstrap.Modal(contenedorModal);
                miModal.show();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>