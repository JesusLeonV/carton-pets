<?php
session_start();
require_once 'includes/db.php';

// Validar que el usuario esté logueado para ver su carrito
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$conteo_carrito = 0;

// Obtener los productos del carrito con los detalles de la tabla productos
$stmt = $pdo->prepare("SELECT c.id as carrito_id, c.cantidad, p.nombre, p.precio, p.imagen, 
                       IFNULL(p.dimensiones, 'Medidas Estándar') as dimensiones, 
                       p.id as producto_id 
                       FROM carrito c 
                       INNER JOIN productos p ON c.producto_id = p.id 
                       WHERE c.usuario_id = ?");
$stmt->execute([$usuario_id]);
$items_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales y conteo de items
$total_pagar = 0;
foreach ($items_carrito as $item) {
    $total_pagar += $item['precio'] * $item['cantidad'];
    $conteo_carrito += $item['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Bolsa | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #927246; font-family: 'Inter', sans-serif; }
        .cart-item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 1px solid #e5dcd3; }
        .cart-card { background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-remove { color: #dc3545; text-decoration: none; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .btn-remove:hover { text-decoration: underline; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3 border-bottom border-secondary" style="background-color: var(--cafe-logo, #81a069) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <div class="logo-top-left">
                <a href="index.php">
                    <img src="assets/img/logocarton.png" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
            </div>
            <div class="user-utilities d-flex align-items-center gap-3">
                <a href="index.php" class="small text-white text-decoration-none fw-bold border border-light rounded-pill px-3 py-1" style="font-size: 0.75rem; background: rgba(255,255,255,0.1);">VOLVER A TIENDA</a>
                <?php if(isset($_SESSION['user_nombre'])): ?>
                    <span class="small fw-bold text-white-50 border-start border-secondary ps-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">👋 <?php echo strtoupper(htmlspecialchars(explode(' ', trim($_SESSION['user_nombre']))[0])); ?></span>
                    <a href="logout.php" class="small text-danger text-decoration-none fw-bold border-start border-secondary ps-3" style="font-size: 0.75rem;">SALIR</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h2 class="fw-bold text-white mb-4" style="letter-spacing: -0.5px;">Tu Bolsa de Compra</h2>
                
                <div class="cart-card card p-4">
                    <?php if (empty($items_carrito)): ?>
                        <div class="text-center py-5">
                            <h5 class="text-muted fw-bold">Tu bolsa está vacía actualmente.</h5>
                            <p class="small text-muted mb-4">Encuentra el modelo ideal para honrar el ciclo natural de la vida.</p>
                            <a href="index.php" class="btn btn-dark rounded-pill px-5 fw-bold">VER PRODUCTOS</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                                        <th colspan="2">Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    foreach ($items_carrito as $item):
                                        $subtotal = $item['precio'] * $item['cantidad'];
                                        $total += $subtotal;
                                        $img_producto = !empty($item['imagen']) ? $item['imagen'] : 'caja_memorial.jpg';
                                    ?>
                                    <tr>
                                        <td style="width: 100px;">
                                            <img src="assets/img/<?php echo $img_producto; ?>" class="cart-item-img" alt="Producto">
                                        </td>
                                        <td>
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                            <small class="text-success fw-bold" style="font-size: 0.8rem;"><?php echo htmlspecialchars($item['dimensiones'] ?? 'Medidas Estándar'); ?></small>
                                        </td>
                                        <td class="text-center fw-bold text-secondary"><?php echo $item['cantidad']; ?></td>
                                        <td class="fw-bold text-dark">$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                        <td class="text-end">
                                            <a href="procesos/eliminar_item.php?id=<?php echo $item['carrito_id']; ?>" class="btn-remove">ELIMINAR</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="border-top pt-4 mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div class="mb-3 mb-md-0">
                                <h3 class="fw-bold mb-0 text-dark">Total: $<?php echo number_format($total, 0, ',', '.'); ?></h3>
                                <p class="small text-muted mb-0">Gestión de compra segura para Iquique y todo Chile.</p>
                            </div>
                            <div class="d-flex gap-3">
                                <a href="index.php" class="btn btn-outline-dark rounded-pill px-4 fw-bold py-2" style="font-size: 0.85rem;">SEGUIR COMPRANDO</a>
                                <a href="checkout.php" class="btn btn-dark rounded-pill px-4 fw-bold py-2" style="font-size: 0.85rem;">IR AL PAGO</a>
                            </div>
                        </div>
                    <?php endif; ?>
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