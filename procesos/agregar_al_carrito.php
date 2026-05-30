<?php
session_start();
// CORRECCIÓN 1: Retrocedemos un nivel para conectar con la base de datos
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    // CORRECCIÓN 2: Retrocedemos un nivel para redirigir al login si no hay sesión
    header("Location: ../login.php?error=debe_iniciar_sesion");
    exit();
}

if (isset($_POST['producto_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    try {
        // Mantenemos tu excelente lógica ON DUPLICATE KEY UPDATE
        $sql = "INSERT INTO carrito (usuario_id, producto_id, cantidad) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE cantidad = cantidad + ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id, $cantidad, $cantidad]);

        // CORRECCIÓN 3: Retrocedemos un nivel para volver al index de la tienda
        header("Location: ../index.php?status=success");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}