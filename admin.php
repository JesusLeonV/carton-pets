<?php
require_once __DIR__ . '/includes/bootstrap.php';

$admin_autorizado = 'cartonpets@gmail.com';
$email_sesion = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';
if ($email_sesion !== $admin_autorizado) {
    header("Location: index.php");
    exit();
}

$accion = $_GET['accion'] ?? 'dashboard';
$producto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = $_GET['msg'] ?? '';
$error_msg = $_GET['error'] ?? '';

// Stats for dashboard
$stats = [
    'total_productos' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'total_pedidos' => $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(),
    'total_clientes' => $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'total_resenas' => $pdo->query("SELECT COUNT(*) FROM resenas WHERE aprobada = 1")->fetchColumn(),
    'ventas_total' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE estado IN ('pagado', 'entregado')")->fetchColumn(),
    'pedidos_pendientes' => $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'")->fetchColumn(),
    'productos_bajo_stock' => $pdo->query("SELECT COUNT(*) FROM productos WHERE stock < 3 AND visible = 1")->fetchColumn(),
];

// Recent orders
$pedidos_recientes = $pdo->query("SELECT p.*, u.nombre as nombre_usuario FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Low stock products
$productos_bajo_stock = $pdo->query("SELECT * FROM productos WHERE stock < 3 AND visible = 1 ORDER BY stock ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Pending reviews
$resenas_pendientes = $pdo->query("SELECT r.*, u.nombre as autor FROM resenas r INNER JOIN usuarios u ON r.usuario_id = u.id WHERE r.aprobada = 0 ORDER BY r.creado_en DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Config
$config = $pdo->query("SELECT * FROM config_negocio ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$config_map = [];
foreach ($config as $c) { $config_map[$c['clave']] = $c['valor']; }

$tamanos = ['mini' => 'Mini', 'pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande', 'unico' => 'Único'];
$categorias = ['gatos' => 'Gatos', 'roedores' => 'Roedores', 'otros' => 'Perros'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f7f5 !important; }
        .stat-card { background: linear-gradient(135deg, rgba(111,143,98,0.08), rgba(111,143,98,0.03)); border-radius: var(--brand-radius); padding: 1.25rem; text-align: center; border: 1px solid rgba(111,143,98,0.1); transition: var(--transition); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(74,61,43,0.1); }
        .stat-number { font-size: 1.8rem; font-weight: 900; color: var(--brand-forest); }
        .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--brand-muted); letter-spacing: 0.05em; }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.75rem; }
        .stat-icon.green { background: rgba(111,143,98,0.15); }
        .stat-icon.yellow { background: rgba(244,162,97,0.15); }
        .stat-icon.blue { background: rgba(69,123,157,0.15); }
        .stat-icon.red { background: rgba(178,59,59,0.15); }
        .chart-card { background: white; border-radius: var(--brand-radius); padding: 1.5rem; box-shadow: 0 8px 32px rgba(74,61,43,0.08); }
        .activity-card { background: white; border-radius: var(--brand-radius); padding: 1.25rem; box-shadow: 0 8px 32px rgba(74,61,43,0.08); }
        .activity-item { display: flex; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid rgba(36,53,43,0.08); }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
        .activity-dot.green { background: #6f8f62; }
        .activity-dot.yellow { background: #f4a261; }
        .activity-dot.red { background: #b23b3b; }
        .activity-dot.blue { background: #457b9d; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- TOP BAR -->
    <div class="top-bar py-3" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold" style="font-size: 0.85rem;">⚙️ PANEL DE ADMINISTRACIÓN</span>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">HOME</a>
                <a href="mi-cuenta.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">MI CUENTA</a>
            </div>
        </div>
    </div>

    <main class="container-fluid px-lg-5 my-4 flex-grow-1">

        <?php if($mensaje): ?>
            <div class="alert alert-success rounded-pill px-4 text-center mx-auto mb-4" style="max-width: 600px;"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if($error_msg): ?>
            <div class="alert alert-danger rounded-pill px-4 text-center mx-auto mb-4" style="max-width: 600px;"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- NAV TABS -->
        <ul class="nav nav-pills mb-4 gap-2 flex-wrap">
            <li><a class="nav-tab-custom nav-link <?php echo $accion === 'dashboard' ? 'active' : ''; ?>" href="admin.php">📊 Dashboard</a></li>
            <li><a class="nav-tab-custom nav-link <?php echo $accion === 'productos' ? 'active' : ''; ?>" href="admin.php?accion=productos">📦 Productos</a></li>
            <li><a class="nav-tab-custom nav-link <?php echo $accion === 'pedidos' ? 'active' : ''; ?>" href="admin.php?accion=pedidos">🛒 Pedidos</a></li>
            <li><a class="nav-tab-custom nav-link <?php echo $accion === 'resenas' ? 'active' : ''; ?>" href="admin.php?accion=resenas">⭐ Reseñas</a></li>
            <li><a class="nav-tab-custom nav-link <?php echo $accion === 'config' ? 'active' : ''; ?>" href="admin.php?accion=config">⚙️ Config</a></li>
        </ul>

        <?php if($accion === 'dashboard'): ?>
        <!-- DASHBOARD -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green">📦</div>
                    <div class="stat-number"><?php echo $stats['total_productos']; ?></div>
                    <div class="stat-label">Productos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon yellow">🛒</div>
                    <div class="stat-number"><?php echo $stats['total_pedidos']; ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue">👥</div>
                    <div class="stat-number"><?php echo $stats['total_clientes']; ?></div>
                    <div class="stat-label">Clientes</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green">💰</div>
                    <div class="stat-number">$<?php echo number_format($stats['ventas_total'], 0, ',', '.'); ?></div>
                    <div class="stat-label">Ventas Totales</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- CHART -->
            <div class="col-lg-8">
                <div class="chart-card h-100">
                    <h6 class="fw-bold mb-3" style="color: var(--brand-forest);">Ventas Recientes</h6>
                    <canvas id="ventasChart" height="200"></canvas>
                </div>
            </div>

            <!-- ACTIVITY -->
            <div class="col-lg-4">
                <div class="activity-card h-100">
                    <h6 class="fw-bold mb-3" style="color: var(--brand-forest);">Actividad Reciente</h6>
                    <?php if(empty($pedidos_recientes)): ?>
                        <p class="text-muted small text-center py-3">Sin actividad reciente</p>
                    <?php else: ?>
                        <?php foreach($pedidos_recientes as $p): ?>
                        <div class="activity-item">
                            <div class="activity-dot <?php echo $p['estado'] === 'pagado' ? 'green' : ($p['estado'] === 'entregado' ? 'blue' : 'yellow'); ?>"></div>
                            <div>
                                <div class="small fw-bold"><?php echo htmlspecialchars($p['cliente_nombre'] ?? 'Cliente'); ?></div>
                                <div class="small text-muted">$<?php echo number_format($p['total'], 0, ',', '.'); ?> · <?php echo ucfirst($p['estado']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <!-- LOW STOCK -->
            <div class="col-md-6">
                <div class="activity-card">
                    <h6 class="fw-bold mb-3" style="color: var(--brand-forest);">⚠️ Stock Bajo</h6>
                    <?php if(empty($productos_bajo_stock)): ?>
                        <p class="text-muted small text-center py-3">Todos los productos tienen stock adecuado</p>
                    <?php else: ?>
                        <?php foreach($productos_bajo_stock as $p): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="small fw-bold"><?php echo htmlspecialchars($p['nombre']); ?></div>
                                <div class="small text-muted">Stock: <?php echo $p['stock']; ?></div>
                            </div>
                            <a href="admin.php?accion=editar&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PENDING REVIEWS -->
            <div class="col-md-6">
                <div class="activity-card">
                    <h6 class="fw-bold mb-3" style="color: var(--brand-forest);">⭐ Reseñas Pendientes</h6>
                    <?php if(empty($resenas_pendientes)): ?>
                        <p class="text-muted small text-center py-3">No hay reseñas pendientes</p>
                    <?php else: ?>
                        <?php foreach($resenas_pendientes as $r): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="small fw-bold"><?php echo htmlspecialchars($r['autor']); ?></div>
                                <div class="small text-muted"><?php echo str_repeat('★', $r['calificacion']); ?> · <?php echo htmlspecialchars(mb_substr($r['comentario'], 0, 40)); ?>...</div>
                            </div>
                            <form method="POST" action="procesos/procesar_config.php" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="accion" value="aprobar_resena">
                                <input type="hidden" name="resena_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Aprobar</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php elseif($accion === 'productos'): ?>
        <!-- PRODUCTOS LIST -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: var(--brand-forest);">📦 Productos</h2>
            <a href="admin.php?accion=crear" class="btn btn-dark rounded-pill px-4 fw-bold">+ NUEVO</a>
        </div>
        <div class="admin-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>#</th><th>Img</th><th>Nombre</th><th>Cat</th><th>Tam</th><th>Precio</th><th>Stock</th><th>Visible</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $productos = $pdo->query("SELECT * FROM productos ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
                        foreach($productos as $p):
                        ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $p['id']; ?></td>
                            <td><img src="assets/img/<?php echo htmlspecialchars(safe_image($p['imagen'])); ?>"></td>
                            <td class="fw-bold text-secondary"><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo $categorias[$p['categoria']] ?? $p['categoria']; ?></td>
                            <td><?php echo $tamanos[$p['tamano']] ?? $p['tamano']; ?></td>
                            <td class="fw-bold">$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                            <td><?php echo $p['stock']; ?></td>
                            <td><?php echo $p['visible'] ? '✅' : '❌'; ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="admin.php?accion=editar&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                                    <form method="POST" action="procesos/procesar_producto.php" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif($accion === 'pedidos'): ?>
        <!-- PEDIDOS -->
        <div class="admin-card">
            <h3 class="fw-bold mb-4" style="color: var(--brand-forest);">🛒 Gestión de Pedidos</h3>
            <?php
            $pedidos = $pdo->query("SELECT p.*, u.nombre as nombre_usuario FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
            if(empty($pedidos)):
            ?>
                <p class="text-muted text-center py-4">No hay pedidos registrados.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th><th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $p): ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $p['id']; ?></td>
                            <td><?php echo htmlspecialchars($p['cliente_nombre'] ?? $p['nombre_usuario'] ?? 'Cliente'); ?><br><span class="small text-muted"><?php echo htmlspecialchars($p['cliente_email']); ?></span></td>
                            <td class="fw-bold">$<?php echo number_format($p['total'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $p['estado'] === 'pagado' ? 'bg-success' : ($p['estado'] === 'entregado' ? 'bg-primary' : 'bg-warning text-dark'); ?>">
                                    <?php echo ucfirst($p['estado']); ?>
                                </span>
                            </td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                            <td>
                                <form method="POST" action="procesos/procesar_pedido.php" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="pedido_id" value="<?php echo $p['id']; ?>">
                                    <select name="nuevo_estado" class="form-select form-select-sm rounded-pill d-inline" style="width: auto;">
                                        <option value="pendiente" <?php echo $p['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="pagado" <?php echo $p['estado'] === 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                                        <option value="entregado" <?php echo $p['estado'] === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">OK</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php elseif($accion === 'resenas'): ?>
        <!-- RESEÑAS -->
        <div class="admin-card">
            <h3 class="fw-bold mb-4" style="color: var(--brand-forest);">⭐ Reseñas</h3>
            <?php
            $resenas = $pdo->query("SELECT r.*, u.nombre as autor FROM resenas r INNER JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
            if(empty($resenas)):
            ?>
                <p class="text-muted text-center py-4">No hay reseñas.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>#</th><th>Autor</th><th>Calificación</th><th>Título</th><th>Estado</th><th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($resenas as $r): ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['autor']); ?></td>
                            <td><?php echo str_repeat('★', $r['calificacion']); ?><?php echo str_repeat('☆', 5 - $r['calificacion']); ?></td>
                            <td><?php echo htmlspecialchars($r['titulo'] ?? '-'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $r['aprobada'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    <?php echo $r['aprobada'] ? 'Aprobada' : 'Pendiente'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="procesos/procesar_config.php" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="accion" value="<?php echo $r['aprobada'] ? 'rechazar_resena' : 'aprobar_resena'; ?>">
                                    <input type="hidden" name="resena_id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo $r['aprobada'] ? 'btn-outline-danger' : 'btn-success'; ?> rounded-pill px-3">
                                        <?php echo $r['aprobada'] ? 'Rechazar' : 'Aprobar'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php elseif($accion === 'crear' || $accion === 'editar'): ?>
        <!-- FORMULARIO PRODUCTO -->
        <div class="admin-card mx-auto" style="max-width: 700px;">
            <h3 class="fw-bold mb-4" style="color: var(--brand-forest);">
                <?php echo $accion === 'crear' ? '➕ Nuevo Producto' : '✏️ Editar #' . $producto_id; ?>
            </h3>
            <form method="POST" action="procesos/procesar_producto.php" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="accion" value="<?php echo $accion; ?>">
                <?php if($accion === 'editar'): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                    $stmt->execute([$producto_id]);
                    $producto_editar = $stmt->fetch();
                    ?>
                    <input type="hidden" name="id" value="<?php echo $producto_id; ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control rounded-pill" required value="<?php echo htmlspecialchars($producto_editar['nombre'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Precio ($)</label>
                        <input type="number" name="precio" class="form-control rounded-pill" required min="0" value="<?php echo $producto_editar['precio'] ?? ''; ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($producto_editar['descripcion'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Categoría</label>
                        <select name="categoria" class="form-select rounded-pill" required>
                            <?php foreach($categorias as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo ($producto_editar['categoria'] ?? '') === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tamaño</label>
                        <select name="tamano" class="form-select rounded-pill" required>
                            <?php foreach($tamanos as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo ($producto_editar['tamano'] ?? '') === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Stock</label>
                        <input type="number" name="stock" class="form-control rounded-pill" required min="0" value="<?php echo $producto_editar['stock'] ?? 0; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Dimensiones</label>
                        <input type="text" name="dimensiones" class="form-control rounded-pill" placeholder="Ej: 30x20x15 cm" value="<?php echo htmlspecialchars($producto_editar['dimensiones'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Material</label>
                        <input type="text" name="material" class="form-control rounded-pill" value="<?php echo htmlspecialchars($producto_editar['material'] ?? 'Cartón Corrugado'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Imagen</label>
                        <input type="file" name="foto_producto" class="form-control rounded-pill" accept="image/*" <?php echo $accion === 'crear' ? 'required' : ''; ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Visibilidad</label>
                        <select name="visible" class="form-select rounded-pill">
                            <option value="1" <?php echo ($producto_editar['visible'] ?? 1) ? 'selected' : ''; ?>>Visible</option>
                            <option value="0" <?php echo !($producto_editar['visible'] ?? 1) ? 'selected' : ''; ?>>Oculto</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold"><?php echo $accion === 'crear' ? 'CREAR' : 'GUARDAR'; ?></button>
                    <a href="admin.php?accion=productos" class="btn btn-outline-secondary rounded-pill px-4">CANCELAR</a>
                </div>
            </form>
        </div>

        <?php elseif($accion === 'config'): ?>
        <!-- CONFIGURACIÓN -->
        <div class="admin-card mx-auto" style="max-width: 700px;">
            <h3 class="fw-bold mb-4" style="color: var(--brand-forest);">⚙️ Configuración del Negocio</h3>
            <form method="POST" action="procesos/procesar_config.php">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="accion" value="guardar_config">

                <h5 class="fw-bold mb-3" style="color: var(--brand-earth);">Venta al Por Mayor</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Descuento (%)</label>
                        <input type="number" name="descuento_por_mayor" class="form-control rounded-pill" value="<?php echo htmlspecialchars($config_map['descuento_por_mayor'] ?? '10'); ?>" min="0" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Cantidad Mínima</label>
                        <input type="number" name="cantidad_minima_mayor" class="form-control rounded-pill" value="<?php echo htmlspecialchars($config_map['cantidad_minima_mayor'] ?? '3'); ?>" min="1">
                    </div>
                </div>

                <h5 class="fw-bold mb-3" style="color: var(--brand-earth);">Delivery</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Delivery Gratis</label>
                        <select name="delivery_gratis" class="form-select rounded-pill">
                            <option value="1" <?php echo ($config_map['delivery_gratis'] ?? '1') === '1' ? 'selected' : ''; ?>>Sí, gratis</option>
                            <option value="0" <?php echo ($config_map['delivery_gratis'] ?? '1') === '0' ? 'selected' : ''; ?>>No, cobrar</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Zona de Cobertura</label>
                        <input type="text" name="delivery_zona" class="form-control rounded-pill" value="<?php echo htmlspecialchars($config_map['delivery_zona'] ?? ''); ?>">
                    </div>
                </div>

                <h5 class="fw-bold mb-3" style="color: var(--brand-earth);">Contacto</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">WhatsApp</label>
                        <input type="text" name="telefono_whatsapp" class="form-control rounded-pill" value="<?php echo htmlspecialchars($config_map['telefono_whatsapp'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email Contacto</label>
                        <input type="email" name="email_contacto" class="form-control rounded-pill" value="<?php echo htmlspecialchars($config_map['email_contacto'] ?? ''); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">GUARDAR CONFIGURACIÓN</button>
            </form>
        </div>
        <?php endif; ?>

    </main>

    <!-- CHART JS -->
    <?php if($accion === 'dashboard'): ?>
    <script>
        // Fetch chart data
        fetch('procesos/obtener_ventas_chart.php')
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('ventasChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ventas ($)',
                            data: data.valores,
                            borderColor: '#6f8f62',
                            backgroundColor: 'rgba(111,143,98,0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#6f8f62'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }
                        }
                    }
                });
            })
            .catch(() => {
                document.getElementById('ventasChart').parentElement.innerHTML = '<p class="text-muted small text-center py-4">Gráfico no disponible</p>';
            });
    </script>
    <?php endif; ?>

</body>
</html>
