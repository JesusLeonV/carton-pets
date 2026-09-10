<?php
require_once __DIR__ . '/includes/bootstrap.php';

$usuario_id = require_login('login.php');

$stmt_user = $pdo->prepare("SELECT id, nombre, email, created_at FROM usuarios WHERE id = ?");
$stmt_user->execute([$usuario_id]);
$usuario = $stmt_user->fetch();

$stmt_pedidos = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt_pedidos->execute([$usuario_id]);
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

$stmt_memorial = $pdo->prepare("SELECT * FROM colaboradores WHERE usuario_correo = ? ORDER BY fecha_registro DESC");
$stmt_memorial->execute([$usuario['email']]);
$memoriales = $stmt_memorial->fetchAll(PDO::FETCH_ASSOC);

// Stats
$total_gastado = 0;
$pedidos_pagados = 0;
foreach($pedidos as $p) {
    if($p['estado'] === 'pagado' || $p['estado'] === 'entregado') {
        $total_gastado += $p['total'];
        $pedidos_pagados++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- TOP BAR -->
    <div class="top-bar py-3" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <a href="index.php" class="text-white text-decoration-none d-flex align-items-center gap-2">
                <img src="assets/img/logocarton.png" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: contain; border: 2px solid rgba(255,250,241,0.3);">
                <span class="fw-bold">CARTONPETS</span>
            </a>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">TIENDA</a>
                <a href="muro.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">MEMORIAL</a>
                <a href="logout.php" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">SALIR</a>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1 px-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- HEADER CUENTA -->
                <div class="admin-card mb-4 overflow-hidden">
                    <div class="p-4" style="background: linear-gradient(135deg, var(--brand-forest), rgba(111,143,98,0.92)); color: #fffaf1;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2 class="fw-bold mb-1" style="font-size: 1.5rem;"><?php echo e($usuario['nombre']); ?></h2>
                                <p class="mb-1 small" style="opacity: 0.8;"><?php echo e($usuario['email']); ?></p>
                                <p class="mb-0 small" style="opacity: 0.6;">Miembro desde <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?></p>
                            </div>
                            <a href="colaboradores.php" class="btn btn-light rounded-pill px-4 fw-bold">+ SUBIR RECUERDO</a>
                        </div>
                    </div>
                    <div class="row g-3 p-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo count($pedidos); ?></div>
                                <div class="stat-label">Pedidos</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo count($memoriales); ?></div>
                                <div class="stat-label">Recuerdos</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number">$<?php echo number_format($total_gastado, 0, ',', '.'); ?></div>
                                <div class="stat-label">Total Gastado</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABS -->
                <div class="d-flex gap-2 mb-4 justify-content-center">
                    <button onclick="showTab('pedidos')" class="nav-tab-custom nav-link active" id="tab-pedidos">📦 Mis Pedidos</button>
                    <button onclick="showTab('memorial')" class="nav-tab-custom nav-link" id="tab-memorial">🕊️ Mis Recuerdos</button>
                </div>

                <!-- PEDIDOS -->
                <div id="section-pedidos">
                    <?php if(empty($pedidos)): ?>
                        <div class="admin-card text-center py-5">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                            <h4 class="fw-bold" style="color: var(--brand-forest);">Aún no has realizado ningún pedido</h4>
                            <p class="text-muted mb-4">Explora nuestro catálogo y encuentra el ataúd perfecto.</p>
                            <a href="index.php" class="btn btn-dark rounded-pill px-5 fw-bold">IR A LA TIENDA</a>
                        </div>
                    <?php else: ?>
                        <?php foreach($pedidos as $p): ?>
                        <div class="admin-card mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-bold" style="color: var(--brand-forest);">Pedido #<?php echo $p['id']; ?></span>
                                    <span class="badge rounded-pill <?php echo $p['estado'] === 'pagado' ? 'bg-success' : ($p['estado'] === 'entregado' ? 'bg-primary' : 'bg-warning text-dark'); ?> ms-2">
                                        <?php echo ucfirst($p['estado']); ?>
                                    </span>
                                </div>
                                <span class="fw-bold" style="color: var(--brand-forest); font-size: 1.1rem;">$<?php echo number_format($p['total'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="small text-muted mt-2">
                                📅 <?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?>
                                <?php if(!empty($p['direccion'])): ?>
                                    <br>📍 <?php echo e($p['direccion']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- MEMORIAL -->
                <div id="section-memorial" style="display: none;">
                    <?php if(empty($memoriales)): ?>
                        <div class="admin-card text-center py-5">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🕊️</div>
                            <h4 class="fw-bold" style="color: var(--brand-forest);">Aún no has compartido ningún recuerdo</h4>
                            <p class="text-muted mb-4">Comparte el recuerdo de tu mascota en nuestro Muro de la Memoria.</p>
                            <a href="colaboradores.php" class="btn btn-dark rounded-pill px-5 fw-bold">SUBIR RECUERDO</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($memoriales as $m): ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="admin-card h-100">
                                    <div class="foto-wrapper mb-3">
                                        <?php
                                        $img = file_exists("assets/img/colaboradores/" . $m['foto_ruta']) ? "assets/img/colaboradores/" . $m['foto_ruta'] : "assets/img/default-pet.png";
                                        ?>
                                        <img src="<?php echo $img; ?>" class="foto-colaborador" alt="<?php echo e($m['nombre_mascota']); ?>">
                                    </div>
                                    <div class="nombre-mascota"><?php echo e($m['nombre_mascota']); ?></div>
                                    <?php if(!empty($m['fecha_nacimiento']) || !empty($m['fecha_fallecimiento'])): ?>
                                        <div class="small text-muted mt-1">
                                            🕊️ <?php echo e($m['fecha_nacimiento'] ?? ''); ?> – <?php echo e($m['fecha_fallecimiento'] ?? ''); ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="muro.php?mascota=<?php echo urlencode($m['nombre_mascota']); ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 mt-3" style="font-size: 0.75rem;">Ver en Muro</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="cp-footer py-4 mt-auto">
        <div class="container text-center">
            <p class="small mb-0">&copy; <?php echo date('Y'); ?> CartonPets — Despedida Sustentable</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showTab(tab) {
            document.getElementById('section-pedidos').style.display = tab === 'pedidos' ? 'block' : 'none';
            document.getElementById('section-memorial').style.display = tab === 'memorial' ? 'block' : 'none';
            document.getElementById('tab-pedidos').classList.toggle('active', tab === 'pedidos');
            document.getElementById('tab-memorial').classList.toggle('active', tab === 'memorial');
        }
    </script>
</body>
</html>
