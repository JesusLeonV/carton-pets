<?php
session_start();
// Salta un nivel hacia atrás para conectar a la DB
require_once '../includes/db.php';

if (isset($_GET['id']) && isset($_SESSION['usuario_id'])) {
    $id_carrito = $_GET['id'];
    $u_id = $_SESSION['usuario_id'];
    
    // Eliminación segura validando el dueño
    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_carrito, $u_id]);
    
    // Regresa a la vista del carrito
    header("Location: ../carrito.php?status=removed");
    exit();
} else {
    header("Location: ../carrito.php");
    exit();
}
?>