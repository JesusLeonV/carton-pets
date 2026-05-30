<?php
session_start();
require_once 'includes/db.php';

$admin_autorizado = 'cartonpets@gmail.com'; 
$email_sesion = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';
$acceso_bloqueado = ($email_sesion !== $admin_autorizado);

$productos = $pdo->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edición de Productos | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        body { background-color: var(--verde-suave, #f4f7f5) !important; }
        .admin-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold">⚙️ EDITOR DE INVENTARIO</span>
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">VOLVER AL HOME</a>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        
        <?php if ($acceso_bloqueado): ?>
            <div class="alert alert-warning p-4 shadow-sm rounded-4 text-center mx-auto" style="max-width: 600px;">
                <h4 class="fw-bold text-dark">⚠️ Control de Acceso Perimetral</h4>
                <p class="small text-muted">El servidor no detectó tu correo activo (Actual: "<?php echo htmlspecialchars($email_sesion); ?>"). Como estás en desarrollo local, puedes forzar la entrada temporalmente.</p>
                <button onclick="document.getElementById('panelEdicion').classList.remove('d-none'); this.parentElement.remove();" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold mt-2">
                    IGNORAR Y ENTRAR IGUAL (MODO DESARROLLO)
                </button>
            </div>
        <?php endif; ?>

        <div id="panelEdicion" class="<?php echo $acceso_bloqueado ? 'd-none' : ''; ?>">
            <h2 class="fw-bold mb-4" style="color: var(--cafe-logo);">Gestión de Tarjetas</h2>

            <div class="admin-card">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Subir Foto</th>
                                <th>Descripción / Medidas</th>
                                <th>Precio ($)</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><img src="assets/img/<?php echo !empty($p['imagen']) ? $p['imagen'] : 'caja_memorial.jpg'; ?>"></td>
                                <td class="fw-bold text-secondary"><?php echo htmlspecialchars($p['nombre']); ?></td>
                                
                                <form action="procesos/procesar_producto.php" method="POST" enctype="multipart/form-data">
                                    <td><input type="file" name="foto_producto" class="form-control form-control-sm rounded-pill" accept="image/*"></td>
                                    <td><input type="text" name="descripcion" class="form-control form-control-sm rounded-pill px-3" value="<?php echo htmlspecialchars($p['descripcion']); ?>" style="min-width: 250px;"></td>
                                    <td><input type="number" name="precio" class="form-control form-control-sm rounded-pill text-center fw-bold" value="<?php echo $p['precio']; ?>" style="width: 110px;"></td>
                                    <td>
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" name="actualizar" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">Guardar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 mt-auto" style="background-color: var(--cafe-logo) !important;">
        <div class="container text-center text-white-50 small">
            CartonPets &copy; 2026 | Modo Editor Logueado
        </div>
    </footer>
</body>
</html>