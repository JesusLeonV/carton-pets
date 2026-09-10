<?php
require_once __DIR__ . '/includes/bootstrap.php';

$usuario_id = require_login('login.php');
$conteo_carrito = 0;

$stmt = $pdo->prepare("SELECT c.id as carrito_id, c.cantidad, p.nombre, p.precio, p.imagen, 
                       IFNULL(p.dimensiones, 'Medidas Estándar') as dimensiones, 
                       p.id as producto_id, p.stock
                       FROM carrito c 
                       INNER JOIN productos p ON c.producto_id = p.id 
                       WHERE c.usuario_id = ?");
$stmt->execute([$usuario_id]);
$items_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_pagar = 0;
$total_descuento = 0;
$descuentos_items = [];
$tiene_descuento_mayor = false;

foreach ($items_carrito as $item) {
    $desc = calcular_descuento_mayorista((int)$item['precio'], (int)$item['cantidad']);
    $descuentos_items[$item['carrito_id']] = $desc;
    $total_pagar += $desc['total_final'];
    $total_descuento += $desc['descuento'];
    $conteo_carrito += $item['cantidad'];
    if ($desc['aplica']) $tiene_descuento_mayor = true;
}

$deliveryGratis = delivery_gratis();
$pedido_id = $_GET['pedido'] ?? null;
$error_compra = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Bolsa | CartonPets</title>
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
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">← VOLVER A TIENDA</a>
        </div>
    </div>

    <div class="container my-5 flex-grow-1 px-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <?php if($pedido_id): ?>
                <div class="cp-hero-panel p-5 text-center mb-4">
                    <span class="cp-eyebrow mb-3">✅ Pedido confirmado</span>
                    <h1 class="fw-bold mb-3">¡Pedido #<?php echo e($pedido_id); ?> confirmado!</h1>
                    <p class="mx-auto" style="max-width: 500px; opacity: 0.85;">Coordinaremos la entrega y el pago contigo. Recibirás un correo con los detalles.</p>
                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <a href="mi-cuenta.php" class="btn btn-light rounded-pill px-4 fw-bold">VER MIS PEDIDOS</a>
                        <a href="index.php" class="btn btn-outline-light rounded-pill px-4 fw-bold">SEGUIR COMPRANDO</a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($error_compra): ?>
                <div class="alert alert-danger rounded-pill text-center mb-4"><?php echo e($error_compra); ?></div>
                <?php endif; ?>

                <!-- HEADER -->
                <div class="cp-hero-panel p-4 p-lg-5 mb-4">
                    <span class="cp-eyebrow mb-3">Resumen de compra</span>
                    <h1 class="fw-bold mb-2" style="font-size: 1.8rem;">Tu bolsa CartonPets</h1>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <?php if($tiene_descuento_mayor): ?>
                            <span class="cp-price-pill" style="background: linear-gradient(135deg, #f4a261, #e76f51);">🏷️ Descuento por mayor aplicado</span>
                        <?php endif; ?>
                        <?php if($deliveryGratis): ?>
                            <span class="cp-price-pill" style="background: linear-gradient(135deg, #2a9d8f, #264653);">🚚 Delivery GRATIS</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(empty($items_carrito) && !$pedido_id): ?>
                <!-- CARRITO VACÍO -->
                <div class="admin-card p-5 text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🛒</div>
                    <h3 class="fw-bold" style="color: var(--brand-forest);">Tu bolsa está vacía</h3>
                    <p class="text-muted mb-4">Explora nuestro catálogo y encuentra el ataúd perfecto para tu mascota.</p>
                    <a href="index.php" class="btn btn-dark rounded-pill px-5 fw-bold">IR A LA TIENDA</a>
                </div>

                <?php elseif(!empty($items_carrito)): ?>

                <!-- INFO WHOLESALE -->
                <?php
                $descuento_pct = get_config_int('descuento_por_mayor', 10);
                $cant_min = get_config_int('cantidad_minima_mayor', 3);
                ?>
                <div class="cp-panel p-3 mb-3" style="background: linear-gradient(135deg, rgba(244,162,97,0.08), rgba(231,111,81,0.05)); border: 1px solid rgba(244,162,97,0.2);">
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 1.5rem;">🏷️</span>
                        <div>
                            <div class="fw-bold" style="color: var(--brand-forest);">Venta al Por Mayor</div>
                            <div class="small text-muted">Compra <?php echo $cant_min; ?> o más unidades del mismo producto y obtén <?php echo $descuento_pct; ?>% de descuento automático.</div>
                        </div>
                    </div>
                </div>

                <!-- ITEMS -->
                <div class="admin-card p-4">
                    <?php foreach($items_carrito as $item):
                        $desc = $descuentos_items[$item['carrito_id']];
                    ?>
                    <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                        <img src="assets/img/<?php echo htmlspecialchars(safe_image($item['imagen'])); ?>" class="cart-item-img" alt="<?php echo e($item['nombre']); ?>">
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="color: var(--brand-forest);"><?php echo e($item['nombre']); ?></div>
                            <div class="small text-muted"><?php echo e($item['dimensiones']); ?></div>
                        </div>
                        <div class="text-end">
                            <?php if($desc['aplica']): ?>
                                <div style="text-decoration: line-through; color: #999; font-size: 0.85rem;">$<?php echo number_format($desc['total_original'], 0, ',', '.'); ?></div>
                                <div class="cp-price-pill" style="background: #2a9d8f;">-<?php echo $desc['porcentaje']; ?>% = $<?php echo number_format($desc['total_final'], 0, ',', '.'); ?></div>
                            <?php else: ?>
                                <div class="fw-bold" style="color: var(--brand-forest);">$<?php echo number_format($item['precio'] * $item['cantidad'], 0, ',', '.'); ?></div>
                            <?php endif; ?>
                            <div class="small text-muted">x<?php echo $item['cantidad']; ?> · $<?php echo number_format($item['precio'], 0, ',', '.'); ?> c/u</div>
                        </div>
                        <form method="POST" action="procesos/eliminar_item.php" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo $item['carrito_id']; ?>">
                            <button type="submit" class="btn-remove border-0 bg-transparent" style="color: #b23b3b; font-size: 1.2rem;">✕</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- RESUMEN -->
                <div class="admin-card p-4 mt-3">
                    <h5 class="fw-bold mb-3" style="color: var(--brand-forest);">Resumen</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal (<?php echo $conteo_carrito; ?> unidades)</span>
                        <span class="fw-bold">$<?php echo number_format($total_pagar + $total_descuento, 0, ',', '.'); ?></span>
                    </div>
                    <?php if($total_descuento > 0): ?>
                    <div class="d-flex justify-content-between mb-2" style="color: #2a9d8f; font-weight: 700;">
                        <span>Descuento por mayor (-<?php echo $descuento_pct; ?>%)</span>
                        <span>-$<?php echo number_format($total_descuento, 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Envío</span>
                        <span class="fw-bold" style="color: <?php echo $deliveryGratis ? '#2a9d8f' : 'inherit'; ?>;"><?php echo $deliveryGratis ? 'GRATIS' : 'A coordinar'; ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-4 fw-bold" style="color: var(--brand-forest);">$<?php echo number_format($total_pagar, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <!-- FORMULARIO COMPRA -->
                <div class="admin-card p-4 mt-3">
                    <h5 class="fw-bold mb-3" style="color: var(--brand-forest);">📦 Datos de entrega</h5>
                    <form method="POST" action="procesos/procesador_compra.php">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Teléfono *</label>
                                <input type="tel" name="telefono" class="form-control rounded-pill" placeholder="+569..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Dirección *</label>
                                <input type="text" name="direccion" class="form-control rounded-pill" placeholder="Calle, número, comuna" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Notas</label>
                                <textarea name="notas" class="form-control" rows="2" placeholder="Instrucciones especiales de entrega..."></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <div class="fs-4 fw-bold" style="color: var(--brand-forest);">$<?php echo number_format($total_pagar, 0, ',', '.'); ?></div>
                                <?php if($deliveryGratis): ?>
                                    <div class="small" style="color: #2a9d8f; font-weight: 700;">✓ Delivery gratis incluido</div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-dark rounded-pill px-5 py-3 fw-bold">CONFIRMAR PEDIDO</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <form method="POST" action="procesos/vaciar_carrito.php" onsubmit="return confirm('¿Vaciar toda la bolsa?');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-link text-muted small">Vaciar bolsa</button>
                        </form>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="cp-footer py-4 mt-auto">
        <div class="container text-center">
            <p class="small mb-0">&copy; <?php echo date('Y'); ?> CartonPets — Despedida Sustentable</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
