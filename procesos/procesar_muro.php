<?php
session_start();
require_once '../includes/db.php';

$correo_logueado = $_SESSION['user_email'] ?? '';
$admin_autorizado = 'cartonpets@gmail.com'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // 1. LÓGICA PARA CREAR/PUBLICAR UN RECUERDO
    if ($accion === 'crear') {
        if (empty($correo_logueado)) {
            header("Location: ../login.php");
            exit();
        }

        $nombre_mascota = trim(htmlspecialchars($_POST['nombre_mascota']));
        $fecha_nacimiento = trim(htmlspecialchars($_POST['fecha_nacimiento'] ?? ''));
        $fecha_fallecimiento = trim(htmlspecialchars($_POST['fecha_fallecimiento'] ?? ''));
        
        // Procesar Archivo de Imagen
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            
            // Generamos un nombre único para evitar colisiones
            $nuevo_nombre_foto = "pet_" . uniqid() . "." . $ext;
            $destino = __DIR__ . "/../assets/img/colaboradores/" . $nuevo_nombre_foto;

            if (move_uploaded_file($file_tmp, $destino)) {
                try {
                    $sql = "INSERT INTO colaboradores (nombre_mascota, fecha_nacimiento, fecha_fallecimiento, foto_ruta, usuario_correo, fecha_registro) 
                            VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre_mascota, $fecha_nacimiento, $fecha_fallecimiento, $nuevo_nombre_foto, $correo_logueado]);

                    header("Location: ../muro.php?status=success");
                    exit();
                } catch (PDOException $e) {
                    die("Error al guardar en la base de datos: " . $e->getMessage());
                }
            } else {
                die("Error al mover el archivo físico al servidor.");
            }
        } else {
            die("Archivo de imagen inválido o no seleccionado.");
        }
    }

    // 2. LÓGICA PARA ELIMINAR EL RECUERDO
    if ($accion === 'eliminar' && isset($_POST['foto_ruta'])) {
        $foto_ruta = $_POST['foto_ruta'];
        try {
            $stmt = $pdo->prepare("SELECT usuario_correo FROM colaboradores WHERE foto_ruta = ?");
            $stmt->execute([$foto_ruta]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro && ($registro['usuario_correo'] === $correo_logueado || $correo_logueado === $admin_autorizado)) {
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
            die("Error al eliminar: " . $e->getMessage());
        }
    }

    // 3. LÓGICA PARA EDITAR EL NOMBRE DE LA MASCOTA
    if ($accion === 'editar' && isset($_POST['foto_ruta']) && isset($_POST['nuevo_nombre'])) {
        $foto_ruta = $_POST['foto_ruta'];
        $nuevo_nombre = trim(htmlspecialchars($_POST['nuevo_nombre']));

        try {
            $stmt = $pdo->prepare("SELECT usuario_correo FROM colaboradores WHERE foto_ruta = ?");
            $stmt->execute([$foto_ruta]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro && ($registro['usuario_correo'] === $correo_logueado || $correo_logueado === $admin_autorizado)) {
                $stmt_upd = $pdo->prepare("UPDATE colaboradores SET nombre_mascota = ? WHERE foto_ruta = ?");
                $stmt_upd->execute([$nuevo_nombre, $foto_ruta]);

                header("Location: ../muro.php?status=updated");
                exit();
            }
        } catch (PDOException $e) {
            die("Error al editar: " . $e->getMessage());
        }
    }
}

header("Location: ../muro.php");
exit();