<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pdo->exec("SET lc_time_names = 'es_ES'");

$sql = "SELECT DATE_FORMAT(fecha_registro, '%M %Y') as mes_anio, id, nombre_mascota, fecha_nacimiento, fecha_fallecimiento, testimonio, foto_ruta, usuario_correo 
        FROM colaboradores 
        ORDER BY fecha_registro DESC";

$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

$correo_logueado = $_SESSION['user_email'] ?? '';
$es_admin = isset($_SESSION['user_email']) && strtolower($_SESSION['user_email']) === 'cartonpets@gmail.com';

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muro de Recuerdo | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260714">
    <style>
        .memorial-card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 36px rgba(74,61,43,0.1); transition: transform 0.25s ease, box-shadow 0.25s ease; border: 1px solid rgba(255,250,241,0.7); }
        .memorial-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(74,61,43,0.18); }
        .foto-wrapper { width: 100%; height: 280px; overflow: hidden; position: relative; }
        .foto-colaborador { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .memorial-card:hover .foto-colaborador { transform: scale(1.05); }
        .nombre-mascota { font-size: 1.1rem; font-weight: 800; color: var(--brand-forest); margin: 0.5rem 0 0.25rem; }
        .fechas-memorial { font-size: 0.8rem; color: var(--brand-muted); }
        .testimonio-text { font-size: 0.85rem; color: var(--brand-muted); font-style: italic; line-height: 1.5; padding: 0 1rem; }
        .panel-gestion { background: rgba(0,0,0,0.03); border-top: 1px solid rgba(0,0,0,0.05); padding: 0.75rem; }
        .btn-gestion { font-size: 0.7rem; padding: 4px 12px; border-radius: 999px; font-weight: 700; }
        .mes-separator { display: flex; align-items: center; gap: 1rem; margin: 3rem 0 1.5rem; font-weight: 900; letter-spacing: 0.08em; color: var(--brand-forest); text-transform: uppercase; font-size: 0.9rem; }
        .mes-separator::before, .mes-separator::after { content: ""; height: 1px; flex: 1; background: var(--brand-line); }
        .share-btn { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; text-decoration: none; transition: all 0.2s; }
        .share-btn:hover { transform: translateY(-1px); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3" style="background-color: #2d2a26;">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php"><img src="assets/img/logocarton.png" style="width: 50px; height: 50px; border-radius: 50%; object-fit: contain;"></a>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">TIENDA</a>
                <a href="colaboradores.php" class="btn btn-sm btn-light rounded-pill px-4 fw-bold">SUBIR RECUERDO</a>
                <?php if(isset($_SESSION['user_nombre'])): ?>
                    <a href="mi-cuenta.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">MI CUENTA</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <section class="cp-hero-panel p-4 p-lg-5 mb-5 text-center">
            <span class="cp-eyebrow mb-3">Comunidad CartonPets</span>
            <h1 class="fw-bold display-5 mb-3">Muro del Recuerdo</h1>
            <p class="mx-auto mb-0" style="max-width: 720px; color: rgba(255,250,241,0.82);">Un espacio para honrar a quienes dejaron huella. Comparte una foto y conserva su memoria junto a nuestra comunidad.</p>
        </section>

        <?php if($status === 'success'): ?>
            <div class="alert alert-success rounded-pill text-center mx-auto" style="max-width: 500px;">Tu recuerdo ha sido publicado con éxito.</div>
        <?php elseif($status === 'deleted'): ?>
            <div class="alert alert-info rounded-pill text-center mx-auto" style="max-width: 500px;">Recuerdo eliminado.</div>
        <?php endif; ?>

        <?php if(empty($registros)): ?>
            <div class="text-center py-5">
                <p class="text-white">Aún no hay recuerdos. ¡Sé el primero!</p>
                <a href="colaboradores.php" class="btn btn-light rounded-pill px-5 fw-bold">SUBIR FOTO</a>
            </div>
        <?php endif; ?>

        <?php foreach ($registros as $mes => $mascotas): ?>
            <div class="mes-separator"><?php echo htmlspecialchars($mes); ?></div>
            <div class="row g-4 mb-5">
                <?php foreach ($mascotas as $m):
                    $img_src = (file_exists("assets/img/colaboradores/" . $m['foto_ruta'])) ? "assets/img/colaboradores/" . $m['foto_ruta'] : "assets/img/default-pet.png";
                    $es_dueno_o_admin = ($m['usuario_correo'] === $correo_logueado || $es_admin);
                    $url_compartir = 'https://cartonpets.cl/muro.php?mascota=' . urlencode($m['nombre_mascota']);
                ?>
                    <div class="col-sm-6 col-lg-3">
                        <div class="memorial-card h-100 d-flex flex-column">
                            <div class="foto-wrapper">
                                <img src="<?php echo $img_src; ?>" class="foto-colaborador" alt="<?php echo htmlspecialchars($m['nombre_mascota']); ?>">
                            </div>
                            <div class="text-center p-3 flex-grow-1 d-flex flex-column">
                                <div class="nombre-mascota"><?php echo htmlspecialchars($m['nombre_mascota']); ?></div>
                                <div class="fechas-memorial mb-2">
                                    🕊️
                                    <?php
                                    $fn = $m['fecha_nacimiento'];
                                    $ff = $m['fecha_fallecimiento'];
                                    if (!empty($fn)) echo htmlspecialchars($fn);
                                    if (!empty($fn) && !empty($ff)) echo ' – ';
                                    if (!empty($ff)) echo htmlspecialchars($ff);
                                    ?>
                                </div>
                                <?php if(!empty($m['testimonio'])): ?>
                                    <p class="testimonio-text flex-grow-1">"<?php echo htmlspecialchars($m['testimonio']); ?>"</p>
                                <?php endif; ?>
                                <div class="mt-auto pt-2">
                                    <a href="<?php echo $url_compartir; ?>" class="share-btn" style="background: rgba(111,143,98,0.12); color: var(--brand-forest);" title="Compartir">
                                        🔗 Compartir
                                    </a>
                                </div>
                            </div>
                            <?php if ($es_dueno_o_admin): ?>
                                <div class="panel-gestion d-flex justify-content-center gap-2">
                                    <button onclick="editarNombre('<?php echo htmlspecialchars($m['foto_ruta']); ?>', '<?php echo htmlspecialchars($m['nombre_mascota']); ?>')" class="btn btn-outline-dark btn-gestion">✏️ Editar</button>
                                    <form method="POST" action="procesos/procesar_muro.php" onsubmit="return confirm('¿Eliminar este recuerdo?');" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="foto_ruta" value="<?php echo htmlspecialchars($m['foto_ruta']); ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-gestion">🗑️ Eliminar</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </main>

    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form method="POST" action="procesos/procesar_muro.php">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="foto_ruta" id="editar_foto_ruta">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Editar nombre</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="nuevo_nombre" id="editar_nombre" class="form-control rounded-pill" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="py-4 mt-auto" style="background-color: #2d2a26;">
        <div class="container text-center text-white-50 small">
            &copy; 2026 CartonPets — Despedida Sustentable
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarNombre(fotoRuta, nombreActual) {
            document.getElementById('editar_foto_ruta').value = fotoRuta;
            document.getElementById('editar_nombre').value = nombreActual;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }
    </script>
</body>
</html>
