<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/bootstrap.php';

$correo_logueado = $_SESSION['user_email'] ?? '';
$es_admin = isset($_SESSION['user_email']) && strtolower($_SESSION['user_email']) === 'cartonpets@gmail.com';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    verify_csrf();
    $accion = $_POST['accion'];

    if ($accion === 'crear') {
        if (empty($correo_logueado)) {
            header("Location: ../login.php");
            exit();
        }

        $nombre_mascota = trim(htmlspecialchars($_POST['nombre_mascota']));
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $fecha_fallecimiento = trim($_POST['fecha_fallecimiento'] ?? '');
        $testimonio = trim(htmlspecialchars($_POST['testimonio'] ?? ''));

        if ($fecha_nacimiento !== '' && !preg_match('/^\d{4}(-\d{2}(-\d{2})?)?$/', $fecha_nacimiento)) {
            $fecha_nacimiento = '';
        }
        if ($fecha_fallecimiento !== '' && !preg_match('/^\d{4}(-\d{2}(-\d{2})?)?$/', $fecha_fallecimiento)) {
            $fecha_fallecimiento = '';
        }

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                die("Formato de imagen no válido. Use JPG, PNG, GIF o WebP.");
            }

            $file_size = $_FILES['foto']['size'];
            if ($file_size > 5 * 1024 * 1024) {
                die("La imagen no puede superar 5MB.");
            }

            $nuevo_nombre_foto = "pet_" . uniqid() . "." . $ext;
            $destino = __DIR__ . "/../assets/img/colaboradores/" . $nuevo_nombre_foto;

            if (move_uploaded_file($file_tmp, $destino)) {
                try {
                    $sql = "INSERT INTO colaboradores (nombre_mascota, fecha_nacimiento, fecha_fallecimiento, testimonio, foto_ruta, usuario_correo, fecha_registro) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre_mascota, $fecha_nacimiento, $fecha_fallecimiento, $testimonio, $nuevo_nombre_foto, $correo_logueado]);
                    header("Location: ../muro.php?status=success");
                    exit();
                } catch (PDOException $e) {
                    error_log('Error crear memorial: ' . $e->getMessage());
                    die("Error al guardar en la base de datos.");
                }
            } else {
                die("Error al mover el archivo físico al servidor.");
            }
        } else {
            die("Archivo de imagen inválido o no seleccionado.");
        }
    }

    if ($accion === 'eliminar' && isset($_POST['foto_ruta'])) {
        $foto_ruta = basename($_POST['foto_ruta']);
        try {
            $stmt = $pdo->prepare("SELECT usuario_correo FROM colaboradores WHERE foto_ruta = ?");
            $stmt->execute([$foto_ruta]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro && ($registro['usuario_correo'] === $correo_logueado || $es_admin)) {
                $archivo_fisico = __DIR__ . "/../assets/img/colaboradores/" . $foto_ruta;
                if (!empty($foto_ruta) && file_exists($archivo_fisico)) {
                    unlink($archivo_fisico);
                }

                $stmt_del = $pdo->prepare("DELETE FROM colaboradores WHERE foto_ruta = ?");
                $stmt_del->execute([$foto_ruta]);

                header("Location: ../muro.php?status=deleted");
                exit();
            }
        } catch (PDOException $e) {
            error_log('Error eliminar memorial: ' . $e->getMessage());
            die("Error al eliminar.");
        }
    }

    if ($accion === 'editar' && isset($_POST['foto_ruta']) && isset($_POST['nuevo_nombre'])) {
        $foto_ruta = basename($_POST['foto_ruta']);
        $nuevo_nombre = trim(htmlspecialchars($_POST['nuevo_nombre']));

        try {
            $stmt = $pdo->prepare("SELECT usuario_correo FROM colaboradores WHERE foto_ruta = ?");
            $stmt->execute([$foto_ruta]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro && ($registro['usuario_correo'] === $correo_logueado || $es_admin)) {
                $stmt_upd = $pdo->prepare("UPDATE colaboradores SET nombre_mascota = ? WHERE foto_ruta = ?");
                $stmt_upd->execute([$nuevo_nombre, $foto_ruta]);
                header("Location: ../muro.php?status=success");
                exit();
            }
        } catch (PDOException $e) {
            error_log('Error editar memorial: ' . $e->getMessage());
            die("Error al editar.");
        }
    }
}

header("Location: ../muro.php");
