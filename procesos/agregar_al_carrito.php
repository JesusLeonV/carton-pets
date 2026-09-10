<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$usuario_id = require_login('../login.php?error=debe_iniciar_sesion');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $producto_id = safe_int($_POST['producto_id'] ?? 0);
    $cantidad = safe_int($_POST['cantidad'] ?? 1, 1, 99);

    try {
        $stmtProducto = $pdo->prepare("SELECT stock FROM productos WHERE id = ? LIMIT 1");
        $stmtProducto->execute([$producto_id]);
        $producto = $stmtProducto->fetch();

        if (!$producto || (int) $producto['stock'] <= 0) {
            redirect('../index.php?error=sin_stock');
        }

        $cantidad = min($cantidad, (int) $producto['stock']);

        $sql = "INSERT INTO carrito (usuario_id, producto_id, cantidad) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE cantidad = LEAST(cantidad + VALUES(cantidad), ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id, $cantidad, (int) $producto['stock']]);

        redirect('../index.php?status=success');
    } catch (PDOException $e) {
        error_log('Error agregar carrito CartonPets: ' . $e->getMessage());
        redirect('../index.php?error=carrito');
    }
}

redirect('../index.php');
