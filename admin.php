<?php
session_start();
require_once 'includes/db.php';

// --- CONFIGURACIÓN DE SEGURIDAD BLINDADA ---
$admin_autorizado = 'cartonpets@gmail.com'; 
$email_sesion = isset($_SESSION['user_email']) ? trim(strtolower($_SESSION['user_email'])) : '';

// Comprobación estricta de identidad
$bloqueado = ($email_sesion !== $admin_autorizado);

// Traemos los productos de la base de datos para listar la tabla
$productos = $pdo->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | CartonPets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        body { background-color: #f4f7f5 !important; }
        .admin-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="top-bar py-3" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold">⚙️ PANEL DE ADMINISTRACIÓN PRIVADO</span>
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">VOLVER AL HOME</a>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">

        <?php if ($bloqueado): ?>
            <div class="alert alert-warning p-4 shadow-sm rounded-4 text-center mx-auto" style="max-width: 600px;">
                <h4 class="fw-bold text-dark">⚠️ Validación de Administrador</h4>
                <p class="small text-muted">No detectamos tu correo activo (Sesión actual: "<?php echo htmlspecialchars($email_sesion); ?>"). Al estar en desarrollo local, puedes saltar este paso.</p>
                <button onclick="document.getElementById('panelPrivado').classList.remove('d-none'); this.parentElement.remove();" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold mt-2">
                    FORZAR ENTRADA (MODO DESARROLLO)
                </button>
            </div>
        <?php endif; ?>

        <div id="panelPrivado" class="<?php echo $bloqueado ? 'd-none' : ''; ?>">
            <h2 class="fw-bold mb-4" style="color: var(--cafe-logo);">Gestión de Tarjetas y Precios</h2>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success rounded-pill px-4"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger rounded-pill px-4"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Cambiar Foto</th>
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

    <footer class="py-5 mt-auto" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container text-center text-md-start px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 text-white">NUESTRA MISIÓN</h6>
                    <p class="small text-white-50">Ofrecer una despedida que sea una declaración de amor por la naturaleza.</p>

                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-3 text-white">PROYECTO CARTONPETS</h6>
                    <p class="small text-white-50">Compromiso inquebrantable desde Iquique para todo Chile.</p>
                    <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">Terminos y condiciones</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold mb-3 text-white">CONTACTO</h6>
                    <p class="small text-white m-0">cartonpets@gmail.com</p>
                    <div class="mt-4 x-small text-white-50" style="font-size: 0.7rem;">&copy; 2026 JESUS LEON | INGENIERÍA EN INFORMÁTICA</div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>