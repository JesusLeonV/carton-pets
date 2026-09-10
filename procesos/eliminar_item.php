<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id_carrito = safe_int($_POST['id'] ?? 0);
    $u_id = require_login('../login.php');
    
    // Eliminación segura validando el dueño
    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_carrito, $u_id]);
    
    // Regresa a la vista del carrito
    redirect('../carrito.php?status=removed');
}

redirect('../carrito.php');
