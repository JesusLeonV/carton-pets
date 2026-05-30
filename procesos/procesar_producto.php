<?php
session_start();
require_once '../includes/db.php';

$admin_autorizado = 'cartonpets@gmail.com'; 
$email_sesion = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';

if ($email_sesion !== $admin_autorizado) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nuevo_precio = $_POST['precio'];
    $nueva_desc = $_POST['descripcion'];

    $stmt_img = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt_img->execute([$id]);
    $prod_actual = $stmt_img->fetch(PDO::FETCH_ASSOC);
    $nombre_imagen = $prod_actual['imagen'];

    if (isset($_FILES['foto_producto']) && $_FILES['foto_producto']['error'] == 0) {
        $directorio_destino = __DIR__ . "/../assets/img/";
        $extension = pathinfo($_FILES["foto_producto"]["name"], PATHINFO_EXTENSION);
        $nombre_archivo_nuevo = "caja_" . $id . "_" . time() . "." . $extension;
        if (move_uploaded_file($_FILES["foto_producto"]["tmp_name"], $directorio_destino . $nombre_archivo_nuevo)) {
            $nombre_imagen = $nombre_archivo_nuevo;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE productos SET precio = ?, descripcion = ?, imagen = ? WHERE id = ?");
        $stmt->execute([$nuevo_precio, $nueva_desc, $nombre_imagen, $id]);
        header("Location: ../admin.php?msg=" . urlencode("Producto ID #$id modificado con éxito."));
        exit();
    } catch (PDOException $e) {
        header("Location: ../admin.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit();
    }
}