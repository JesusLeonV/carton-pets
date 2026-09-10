<?php
session_start();
require_once '../includes/db.php';

$admin_autorizado = 'cartonpets@gmail.com';
$email_sesion = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';

if ($email_sesion !== $admin_autorizado) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['accion'])) {
    header("Location: ../admin.php");
    exit();
}

require_once '../includes/bootstrap.php';
verify_csrf();

$accion = $_POST['accion'];

if ($accion === 'crear') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (int)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $categoria = $_POST['categoria'] ?? 'otros';
    $tamano = $_POST['tamano'] ?? 'unico';
    $dimensiones = trim($_POST['dimensiones'] ?? 'Medidas Estándar');
    $material = trim($_POST['material'] ?? 'Cartón Corrugado');
    $visible = (int)($_POST['visible'] ?? 1);

    if ($nombre === '' || $precio <= 0) {
        header("Location: ../admin.php?error=" . urlencode("Nombre y precio son obligatorios."));
        exit();
    }

    $nombre_imagen = 'caja_memorial.jpg';
    if (isset($_FILES['foto_producto']) && $_FILES['foto_producto']['error'] === UPLOAD_ERR_OK) {
        $directorio = __DIR__ . '/../assets/img/';
        $extension = pathinfo($_FILES['foto_producto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'caja_' . time() . '_' . uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['foto_producto']['tmp_name'], $directorio . $nombre_archivo)) {
            $nombre_imagen = $nombre_archivo;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen, categoria, dimensiones, tamano, material, visible) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $nombre_imagen, $categoria, $dimensiones, $tamano, $material, $visible]);
        header("Location: ../admin.php?msg=" . urlencode("Producto creado con éxito."));
        exit();
    } catch (PDOException $e) {
        error_log('Error crear producto: ' . $e->getMessage());
        header("Location: ../admin.php?error=" . urlencode("Error al crear producto."));
        exit();
    }
}

if ($accion === 'editar') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (int)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $categoria = $_POST['categoria'] ?? 'otros';
    $tamano = $_POST['tamano'] ?? 'unico';
    $dimensiones = trim($_POST['dimensiones'] ?? 'Medidas Estándar');
    $material = trim($_POST['material'] ?? 'Cartón Corrugado');
    $visible = (int)($_POST['visible'] ?? 1);

    if ($id <= 0 || $nombre === '' || $precio <= 0) {
        header("Location: ../admin.php?error=" . urlencode("Datos inválidos."));
        exit();
    }

    $stmt_img = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt_img->execute([$id]);
    $prod_actual = $stmt_img->fetch(PDO::FETCH_ASSOC);
    $nombre_imagen = $prod_actual ? $prod_actual['imagen'] : 'caja_memorial.jpg';

    if (isset($_FILES['foto_producto']) && $_FILES['foto_producto']['error'] === UPLOAD_ERR_OK) {
        $directorio = __DIR__ . '/../assets/img/';
        $extension = pathinfo($_FILES['foto_producto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'caja_' . $id . '_' . time() . '.' . $extension;
        if (move_uploaded_file($_FILES['foto_producto']['tmp_name'], $directorio . $nombre_archivo)) {
            $nombre_imagen = $nombre_archivo;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=?, categoria=?, dimensiones=?, tamano=?, material=?, visible=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $nombre_imagen, $categoria, $dimensiones, $tamano, $material, $visible, $id]);
        header("Location: ../admin.php?msg=" . urlencode("Producto #$id actualizado."));
        exit();
    } catch (PDOException $e) {
        error_log('Error editar producto: ' . $e->getMessage());
        header("Location: ../admin.php?error=" . urlencode("Error al editar producto."));
        exit();
    }
}

if ($accion === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        header("Location: ../admin.php?error=ID inválido");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../admin.php?msg=" . urlencode("Producto #$id eliminado."));
        exit();
    } catch (PDOException $e) {
        error_log('Error eliminar producto: ' . $e->getMessage());
        header("Location: ../admin.php?error=" . urlencode("No se puede eliminar: tiene pedidos asociados."));
        exit();
    }
}

header("Location: ../admin.php");
