<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../carrito.php');
}

verify_csrf();

$usuario_id = require_login('../login.php?error=debe_iniciar_sesion');

$telefono = trim((string) ($_POST['telefono'] ?? ''));
$direccion = trim((string) ($_POST['direccion'] ?? ''));
$notas = trim((string) ($_POST['notas'] ?? ''));

if ($telefono === '' || $direccion === '') {
    redirect('../carrito.php?error=datos');
}
if (strlen($telefono) < 8) {
    redirect('../carrito.php?error=telefono');
}
if (strlen($direccion) < 5) {
    redirect('../carrito.php?error=direccion');
}

try {
    $pdo->beginTransaction();

    $stmtUsuario = $pdo->prepare('SELECT id, nombre, email FROM usuarios WHERE id = ? LIMIT 1');
    $stmtUsuario->execute([$usuario_id]);
    $usuario = $stmtUsuario->fetch();

    if (!$usuario) {
        throw new RuntimeException('Usuario no encontrado.');
    }

    $stmtItems = $pdo->prepare(
        'SELECT c.producto_id, c.cantidad, p.precio, p.stock, p.nombre
         FROM carrito c
         INNER JOIN productos p ON c.producto_id = p.id
         WHERE c.usuario_id = ? FOR UPDATE'
    );
    $stmtItems->execute([$usuario_id]);
    $items = $stmtItems->fetchAll();

    if (!$items || count($items) === 0) {
        throw new RuntimeException('El carrito está vacío.');
    }

    $total = 0;
    $descuento_total = 0;
    $descuento_pct = get_config_int('descuento_por_mayor', 10);
    $cant_min = get_config_int('cantidad_minima_mayor', 3);

    foreach ($items as $item) {
        $cantidad = (int) $item['cantidad'];
        if ($cantidad < 1 || (int) $item['stock'] < $cantidad) {
            throw new RuntimeException('Stock insuficiente para: ' . $item['nombre']);
        }

        $subtotal = ((int) $item['precio']) * $cantidad;
        if ($cantidad >= $cant_min && $descuento_pct > 0) {
            $desc = (int) round($subtotal * $descuento_pct / 100);
            $descuento_total += $desc;
            $subtotal -= $desc;
        }
        $total += $subtotal;
    }

    if ($total <= 0) {
        throw new RuntimeException('Total inválido.');
    }

    $delivery_info = delivery_gratis() ? 'Delivery gratis - ' . get_config('delivery_zona', 'Iquique') : 'Coordinar envío';

    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, cliente_nombre, cliente_email, total, estado, direccion, telefono, notas, metodo_pago) VALUES (?, ?, ?, ?, 'pendiente', ?, ?, ?, 'manual')");
    $stmt->execute([
        $usuario_id,
        $usuario['nombre'] ?: 'Cliente CartonPets',
        $usuario['email'],
        $total,
        $direccion . ' [' . $delivery_info . ']',
        $telefono,
        $notas
    ]);
    $pedido_id = $pdo->lastInsertId();

    foreach ($items as $item) {
        $producto_id = (int) $item['producto_id'];
        $cantidad = (int) $item['cantidad'];

        $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmtStock->execute([$cantidad, $producto_id, $cantidad]);

        if ($stmtStock->rowCount() === 0) {
            throw new RuntimeException('Sin stock: ' . $item['nombre']);
        }
    }

    $stmtLimpiar = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
    $stmtLimpiar->execute([$usuario_id]);

    $pdo->commit();

    error_log("Pedido #$pedido_id - Usuario #$usuario_id - Total: $$total (Descuento mayorista: $$descuento_total)");

    redirect('../carrito.php?pedido=' . urlencode((string) $pedido_id));

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error compra: ' . $e->getMessage());
    redirect('../carrito.php?error=compra');
}
