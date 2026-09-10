<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $usuario_id = require_login('../login.php');
    $stmt = $pdo->prepare('DELETE FROM carrito WHERE usuario_id = ?');
    $stmt->execute([$usuario_id]);
}

redirect('../carrito.php');
