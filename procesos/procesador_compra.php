<?php
// 1. Incluimos la conexión que ya reparamos
require_once 'includes/db.php';
session_start();

// 2. Verificamos que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=debe_iniciar_sesion");
    exit();
}

// 3. Recibimos los datos del formulario (o del carrito)
$usuario_id = $_SESSION['usuario_id'];
$total = $_POST['total_compra']; // Asegúrate que tu formulario envíe este name
$items = $_SESSION['carrito_temporal']; // Ejemplo si guardas los items en sesión

try {
    // AQUÍ EMPIEZA LA TRANSACCIÓN SEGURA
    $pdo->beginTransaction(); 

    // A. Insertar el Pedido General
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'pagado')");
    $stmt->execute([$usuario_id, $total]);
    $pedido_id = $pdo->lastInsertId();

    // B. Procesar cada producto del carrito (recorremos los items)
    // Supongamos que recibes un array de productos
    foreach ($items as $item) {
        $producto_id = $item['id'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];

        // 1. Insertar el detalle para el historial
        $stmtDetalle = $pdo->prepare("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmtDetalle->execute([$pedido_id, $producto_id, $cantidad, $precio]);

        // 2. DESCONTAR STOCK (Seguridad de inventario)
        $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmtStock->execute([$cantidad, $producto_id, $cantidad]);

        if ($stmtStock->rowCount() == 0) {
            // Si no hay stock suficiente, lanzamos error para que el catch haga el rollback
            throw new Exception("No hay suficiente stock para la caja ID: " . $producto_id);
        }
    }

    // C. Limpiar el carrito de la base de datos (ya que ya compró)
    $stmtLimpiar = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
    $stmtLimpiar->execute([$usuario_id]);

    // SI TODO SALIÓ BIEN, CONFIRMAMOS
    $pdo->commit(); 
    
    // Redirigir a éxito
    header("Location: confirmacion.php?id=" . $pedido_id);

} catch (Exception $e) {
    // SI ALGO FALLÓ (Incluso un error de internet), SE DESHACE TODO
    $pdo->rollBack(); 
    header("Location: carrito.php?error=" . urlencode($e->getMessage()));
    exit();
}