<?php
require_once 'includes/bootstrap.php';

$conteo_carrito = 0;
if (isset($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $res = $stmt->fetch();
    $conteo_carrito = $res['total'] ?? 0;
}

// Filtros
$filtro_categoria = $_GET['categoria'] ?? '';
$filtro_tamano = $_GET['tamano'] ?? '';
$filtro_orden = $_GET['orden'] ?? 'nombre';

$where = ["visible = 1"];
$params = [];
if ($filtro_categoria !== '' && in_array($filtro_categoria, ['gatos','roedores','otros'])) { $where[] = "categoria = ?"; $params[] = $filtro_categoria; }
if ($filtro_tamano !== '' && in_array($filtro_tamano, ['mini','pequeno','mediano','grande','unico'])) { $where[] = "tamano = ?"; $params[] = $filtro_tamano; }

$orden_valido = ['nombre' => 'nombre ASC', 'precio_asc' => 'precio ASC', 'precio_desc' => 'precio DESC', 'reciente' => 'created_at DESC'];
$orden_sql = $orden_valido[$filtro_orden] ?? 'nombre ASC';

$sql = "SELECT * FROM productos WHERE " . implode(' AND ', $where) . " ORDER BY $orden_sql";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ataudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_resultados = count($ataudes);

// Banners dinámicos
$banners = $pdo->query("SELECT * FROM banner_config WHERE activo = 1 ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);

// Resenas aprobadas
$resenas = $pdo->query("SELECT r.*, u.nombre as autor FROM resenas r INNER JOIN usuarios u ON r.usuario_id = u.id WHERE r.aprobada = 1 ORDER BY r.creado_en DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

// Stats
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos WHERE visible = 1")->fetchColumn();
$total_clientes = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_resenas = $pdo->query("SELECT COUNT(*) FROM resenas WHERE aprobada = 1")->fetchColumn();
$mostrarCookies = !isset($_COOKIE['cookies_aceptadas']);

$tamanos = ['mini' => 'Mini', 'pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande', 'unico' => 'Único'];
$categorias = ['gatos' => 'Gatos', 'roedores' => 'Roedores', 'otros' => 'Perros'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CartonPets | Despedida Sustentable para tu Mascota</title>
    <meta name="description" content="Ataúdes ecológicos de cartón corrugado para mascotas en Iquique y todo Chile. Despedidas dignas, respetuosas y sustentables.">
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <style>
        .hero-section { position: relative; background: linear-gradient(160deg, var(--brand-forest) 0%, #1a2e22 50%, var(--brand-leaf) 100%); overflow: hidden; }
        .hero-section::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at 80% 20%, rgba(145,167,120,0.25), transparent 50%), radial-gradient(circle at 20% 80%, rgba(138,104,70,0.15), transparent 50%); pointer-events: none; }
        .hero-content { position: relative; z-index: 2; }
        .hero-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 1rem; border-radius: 999px; background: rgba(255,250,241,0.1); color: rgba(255,250,241,0.8); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.06em; backdrop-filter: blur(8px); border: 1px solid rgba(255,250,241,0.1); }
        .hero-title { font-size: clamp(2rem, 4.5vw, 3.5rem); font-weight: 900; color: #fffaf1; letter-spacing: -0.03em; line-height: 1.1; text-wrap: balance; }
        .hero-subtitle { font-size: clamp(0.95rem, 1.8vw, 1.15rem); color: rgba(255,250,241,0.7); max-width: 500px; line-height: 1.6; }
        .hero-cta { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.8rem 1.8rem; border-radius: 999px; background: #fffaf1; color: var(--brand-forest); font-weight: 800; font-size: 0.9rem; text-decoration: none; transition: var(--transition); box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .hero-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); color: var(--brand-forest); }
        .hero-cta-secondary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.8rem 1.8rem; border-radius: 999px; border: 2px solid rgba(255,250,241,0.25); color: #fffaf1; font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: var(--transition); }
        .hero-cta-secondary:hover { border-color: rgba(255,250,241,0.6); background: rgba(255,250,241,0.06); color: #fffaf1; }
        .hero-stat-card { text-align: center; padding: 1rem; background: rgba(255,250,241,0.06); border-radius: 16px; border: 1px solid rgba(255,250,241,0.08); backdrop-filter: blur(8px); }
        .hero-stat-number { font-size: 1.6rem; font-weight: 900; color: #fffaf1; }
        .hero-stat-label { font-size: 0.7rem; color: rgba(255,250,241,0.5); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

        .section-divider { display: flex; align-items: center; gap: 1rem; margin: 3rem 0 2rem; }
        .section-divider::before, .section-divider::after { content: ""; height: 1px; flex: 1; background: var(--brand-line); }
        .section-divider span { font-size: 0.72rem; font-weight: 800; color: var(--brand-muted); text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap; }

        .filter-bar { background: rgba(255,250,241,0.97); border-radius: var(--brand-radius); padding: 1rem 1.25rem; box-shadow: 0 4px 16px rgba(74,61,43,0.05); border: 1px solid rgba(255,255,255,0.6); margin-bottom: 2rem; }
        .filter-chip { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.9rem; border-radius: 999px; border: 1.5px solid var(--brand-line); background: transparent; color: var(--brand-forest); font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: var(--transition); }
        .filter-chip:hover, .filter-chip.active { background: var(--brand-forest); color: #fffaf1; border-color: var(--brand-forest); }

        .product-card-v2 { background: #fffaf1; border-radius: var(--brand-radius); overflow: hidden; border: 1px solid rgba(255,255,255,0.6); box-shadow: 0 8px 28px rgba(74,61,43,0.07); transition: var(--transition); display: flex; flex-direction: column; }
        .product-card-v2:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(74,61,43,0.14); }
        .product-img-wrap { position: relative; background: linear-gradient(135deg, #f8f2e8, #efe0cc); padding: 1.5rem; display: flex; align-items: center; justify-content: center; min-height: 200px; }
        .product-img-wrap img { max-width: 100%; max-height: 170px; object-fit: contain; transition: transform 0.4s ease; }
        .product-card-v2:hover .product-img-wrap img { transform: scale(1.06); }
        .product-badge { position: absolute; top: 0.8rem; left: 0.8rem; padding: 0.25rem 0.65rem; border-radius: 999px; background: var(--brand-forest); color: #fffaf1; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.03em; }
        .product-body { padding: 1.2rem 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .product-name { font-size: 0.95rem; font-weight: 800; color: var(--brand-forest); margin-bottom: 0.3rem; line-height: 1.3; }
        .product-desc { font-size: 0.8rem; color: var(--brand-muted); line-height: 1.5; margin-bottom: 0.75rem; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-meta { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
        .product-tag { padding: 0.2rem 0.5rem; border-radius: 999px; background: rgba(111,143,98,0.08); color: var(--brand-forest); font-size: 0.65rem; font-weight: 700; }
        .product-footer { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: auto; }
        .product-price { font-size: 1.25rem; font-weight: 900; color: var(--brand-forest); }
        .product-price span { font-size: 0.7rem; font-weight: 600; color: var(--brand-muted); display: block; }
        .btn-add-cart { padding: 0.5rem 1.2rem; border-radius: 999px; background: var(--brand-forest); color: #fffaf1; border: none; font-weight: 800; font-size: 0.78rem; cursor: pointer; transition: var(--transition); white-space: nowrap; }
        .btn-add-cart:hover { background: #17251d; transform: translateY(-1px); }
        .btn-add-cart:disabled { background: #ccc; cursor: not-allowed; transform: none; }

        .testimonial-card { background: rgba(255,250,241,0.97); border-radius: var(--brand-radius); padding: 1.5rem; border: 1px solid rgba(255,255,255,0.6); box-shadow: 0 6px 20px rgba(74,61,43,0.05); transition: var(--transition); }
        .testimonial-card:hover { transform: translateY(-3px); box-shadow: 0 12px 36px rgba(74,61,43,0.1); }
        .testimonial-stars { color: #f4a261; font-size: 0.85rem; letter-spacing: 2px; }
        .testimonial-text { font-size: 0.85rem; color: var(--brand-muted); line-height: 1.6; font-style: italic; margin: 0.6rem 0; }
        .testimonial-author { font-size: 0.8rem; font-weight: 700; color: var(--brand-forest); }

        .benefit-card { background: rgba(255,250,241,0.97); border-radius: var(--brand-radius); padding: 1.5rem; text-align: center; border: 1px solid rgba(255,255,255,0.6); box-shadow: 0 6px 20px rgba(74,61,43,0.05); transition: var(--transition); }
        .benefit-card:hover { transform: translateY(-3px); }
        .benefit-icon { width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, rgba(111,143,98,0.12), rgba(111,143,98,0.04)); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.75rem; }
        .benefit-title { font-size: 0.9rem; font-weight: 800; color: var(--brand-forest); margin-bottom: 0.3rem; }
        .benefit-text { font-size: 0.78rem; color: var(--brand-muted); line-height: 1.5; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-dark py-2 px-lg-5" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand m-0" href="index.php">
                <img src="assets/img/logocarton.png" alt="CartonPets" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,250,241,0.3);">
            </a>
            <div class="d-flex align-items-center gap-2 order-md-last">
                <?php if(isset($_SESSION['user_nombre'])): ?>
                    <a href="mi-cuenta.php" class="text-white small fw-bold px-2 py-1 rounded text-decoration-none" style="background: rgba(255,255,255,0.1); font-size: 0.72rem;">
                        👋 <?php echo strtoupper(htmlspecialchars(explode(' ', trim($_SESSION['user_nombre']))[0])); ?>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.72rem;">INGRESAR</a>
                <?php endif; ?>
                <a href="carrito.php" class="btn text-white position-relative px-2 d-flex align-items-center" style="font-size: 0.85rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>
                    <?php if($conteo_carrito > 0): ?>
                        <span class="badge rounded-pill bg-light text-dark" style="font-size: 0.6rem; position: absolute; top: -4px; right: -4px;"><?php echo $conteo_carrito; ?></span>
                    <?php endif; ?>
                </a>
                <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            </div>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-md-center gap-2 gap-md-3 pt-2 pt-md-0">
                    <?php if(isset($_SESSION['user_nombre'])): ?>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="muro.php">MEMORIAL</a>
                        <?php if(current_user_is_admin()): ?>
                            <a class="nav-link text-warning small fw-bold py-1 px-2" href="admin.php">ADMIN</a>
                        <?php endif; ?>
                        <a class="nav-link text-danger small fw-bold py-1 px-2 border-start-md" href="logout.php">SALIR</a>
                    <?php else: ?>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="registro.php">REGISTRARSE</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="muro.php">MEMORIAL</a>
                        <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">TÉRMINOS</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- CARRUSEL -->
    <?php if(!empty($banners)): ?>
    <div id="bannerCarousel" class="carousel slide carousel-fade style-gap-fix" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach($banners as $i => $b): ?>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($banners as $i => $b): ?>
                <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                    <a href="<?php echo e($b['enlace']); ?>">
                        <img src="assets/img/<?php echo e($b['imagen']); ?>" class="d-block w-100" alt="<?php echo e($b['titulo']); ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- HERO -->
    <section class="hero-section py-5">
        <div class="container py-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-content">
                    <span class="hero-badge mb-4 animate-in">🌿 Ecológico · Digno · Natural</span>
                    <h1 class="hero-title mb-4 animate-in animate-delay-1">Despedidas sustentables que honran su memoria</h1>
                    <p class="hero-subtitle mb-4 animate-in animate-delay-2">Ataúdes ecológicos de cartón corrugado para tu mascota. Una alternativa digna, natural y respetuosa con el medio ambiente.</p>
                    <div class="d-flex gap-3 flex-wrap animate-in animate-delay-3">
                        <a href="#catalogo" class="hero-cta">VER CATÁLOGO →</a>
                        <a href="muro.php" class="hero-cta-secondary">Muro del Recuerdo</a>
                    </div>
                    <div class="row g-2 mt-4 animate-in animate-delay-3">
                        <div class="col-4"><div class="hero-stat-card"><div class="hero-stat-number"><?php echo $total_productos; ?>+</div><div class="hero-stat-label">Productos</div></div></div>
                        <div class="col-4"><div class="hero-stat-card"><div class="hero-stat-number">100%</div><div class="hero-stat-label">Ecológico</div></div></div>
                        <div class="col-4"><div class="hero-stat-card"><div class="hero-stat-number">Iquique</div><div class="hero-stat-label">Despacho</div></div></div>
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block hero-content">
                    <img src="assets/img/ataud_perro_med.jpg" alt="Ataúd ecológico CartonPets" style="max-width: 380px; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); transform: rotate(2deg);">
                </div>
            </div>
        </div>
    </section>

    <!-- BENEFICIOS -->
    <section class="container my-5">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="benefit-card h-100">
                    <div class="benefit-icon">🌿</div>
                    <div class="benefit-title">100% Ecológico</div>
                    <div class="benefit-text">Cartón corrugado biodegradable</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-card h-100">
                    <div class="benefit-icon">🚚</div>
                    <div class="benefit-title">Envío Gratis</div>
                    <div class="benefit-text">En pedidos sobre 3 unidades</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-card h-100">
                    <div class="benefit-icon">🏷️</div>
                    <div class="benefit-title">Descuento Mayor</div>
                    <div class="benefit-text">10% off comprando 3+ unidades</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-card h-100">
                    <div class="benefit-icon">📞</div>
                    <div class="benefit-title">Atención Directa</div>
                    <div class="benefit-text">WhatsApp y correo siempre</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CATÁLOGO -->
    <main class="container my-5 flex-grow-1" id="catalogo">
        <div class="section-divider"><span>📦 Nuestros Productos</span></div>

        <!-- FILTROS -->
        <div class="filter-bar">
            <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 align-items-center">
                <a href="index.php" class="filter-chip <?php echo $filtro_categoria === '' ? 'active' : ''; ?>">Todos</a>
                <a href="?categoria=gatos" class="filter-chip <?php echo $filtro_categoria === 'gatos' ? 'active' : ''; ?>">🐱 Gatos</a>
                <a href="?categoria=roedores" class="filter-chip <?php echo $filtro_categoria === 'roedores' ? 'active' : ''; ?>">🐹 Roedores</a>
                <a href="?categoria=otros" class="filter-chip <?php echo $filtro_categoria === 'otros' ? 'active' : ''; ?>">🐕 Perros</a>
                <span class="mx-1" style="color: var(--brand-line);">|</span>
                <select name="tamano" class="form-select form-select-sm rounded-pill" style="width: auto; font-size: 0.78rem;" onchange="this.form.submit()">
                    <option value="">Tamaño</option>
                    <?php foreach($tamanos as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php echo $filtro_tamano === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="orden" class="form-select form-select-sm rounded-pill" style="width: auto; font-size: 0.78rem;" onchange="this.form.submit()">
                    <option value="nombre" <?php echo $filtro_orden === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                    <option value="precio_asc" <?php echo $filtro_orden === 'precio_asc' ? 'selected' : ''; ?>>Menor precio</option>
                    <option value="precio_desc" <?php echo $filtro_orden === 'precio_desc' ? 'selected' : ''; ?>>Mayor precio</option>
                </select>
                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($filtro_categoria); ?>">
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="small fw-bold" style="color: var(--brand-muted);"><?php echo $total_resultados; ?> producto<?php echo $total_resultados !== 1 ? 's' : ''; ?></span>
        </div>

        <?php if(empty($ataudes)): ?>
            <div class="text-center py-5">
                <h3 class="fw-bold" style="color: var(--brand-forest);">No encontramos productos</h3>
                <p style="color: var(--brand-muted);">Intenta ajustar los filtros o <a href="index.php" style="color: var(--brand-forest); font-weight: 700;">ver todo</a>.</p>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ataudes as $i => $ataud): ?>
            <div class="col-md-6 col-lg-4">
                <div class="product-card-v2 h-100">
                    <div class="product-img-wrap">
                        <span class="product-badge"><?php echo $ataud['tamano'] !== 'unico' ? ucfirst($ataud['tamano']) : $ataud['categoria']; ?></span>
                        <img src="assets/img/<?php echo htmlspecialchars(safe_image($ataud['imagen'])); ?>" alt="<?php echo htmlspecialchars($ataud['nombre']); ?>">
                    </div>
                    <div class="product-body">
                        <div class="product-name"><?php echo htmlspecialchars($ataud['nombre']); ?></div>
                        <div class="product-desc"><?php echo htmlspecialchars($ataud['descripcion']); ?></div>
                        <div class="product-meta">
                            <span class="product-tag"><?php echo htmlspecialchars($ataud['material'] ?? 'Cartón Corrugado'); ?></span>
                            <?php if(!empty($ataud['dimensiones']) && $ataud['dimensiones'] !== 'Medidas Estándar'): ?>
                                <span class="product-tag"><?php echo htmlspecialchars($ataud['dimensiones']); ?></span>
                            <?php endif; ?>
                            <span class="product-tag"><?php echo $ataud['stock'] > 0 ? 'Stock: ' . $ataud['stock'] : 'Agotado'; ?></span>
                        </div>
                        <form action="procesos/agregar_al_carrito.php" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="producto_id" value="<?php echo $ataud['id']; ?>">
                            <div class="product-footer">
                                <div class="product-price"><span>desde</span>$<?php echo number_format($ataud['precio'], 0, ',', '.'); ?></div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo $ataud['stock']; ?>" class="form-control form-control-sm rounded-pill text-center" style="width: 52px; font-size: 0.78rem;">
                                    <button type="submit" class="btn-add-cart" <?php echo $ataud['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        <?php echo $ataud['stock'] > 0 ? 'Añadir' : 'Agotado'; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <!-- TESTIMONIOS -->
    <?php if(!empty($resenas)): ?>
    <section class="container my-5">
        <div class="section-divider"><span>⭐ Lo que dicen nuestros clientes</span></div>
        <div class="row g-3">
            <?php foreach($resenas as $r): ?>
            <div class="col-md-6 col-lg-3">
                <div class="testimonial-card h-100">
                    <div class="testimonial-stars"><?php echo str_repeat('★', $r['calificacion']); ?><?php echo str_repeat('☆', 5 - $r['calificacion']); ?></div>
                    <?php if(!empty($r['titulo'])): ?>
                        <div class="fw-bold mt-2" style="font-size: 0.85rem; color: var(--brand-forest);"><?php echo e($r['titulo']); ?></div>
                    <?php endif; ?>
                    <p class="testimonial-text">"<?php echo e($r['comentario']); ?>"</p>
                    <div class="testimonial-author"><?php echo e($r['autor']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA WHATSAPP -->
    <section class="cp-hero-panel mx-3 mx-lg-5 my-5 p-4 p-lg-5 text-center">
        <span class="cp-eyebrow mb-3">¿Necesitas ayuda?</span>
        <h2 class="fw-bold display-6 mb-3">Coordinamos todo contigo</h2>
        <p class="mx-auto mb-4" style="max-width: 550px; color: rgba(255,250,241,0.75);">Escríbenos por WhatsApp o correo y te atendemos de inmediato. Estamos en Iquique con despacho a toda la región.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="https://wa.me/56951898299" class="btn btn-light rounded-pill px-4 fw-bold" target="_blank">📱 WhatsApp</a>
            <a href="mailto:cartonpets@gmail.com" class="btn btn-outline-light rounded-pill px-4 fw-bold">📧 Correo</a>
        </div>
    </section>

    <!-- COOKIE BANNER -->
    <div id="cookieBanner" class="cookie-banner shadow-lg" style="display: <?php echo $mostrarCookies ? 'block' : 'none'; ?>; position: fixed; bottom: 0; width: 100%; background: white; z-index: 999; border-top: 2px solid #2d2a26;">
        <div class="container d-md-flex align-items-center justify-content-between py-3 px-lg-5">
            <p class="mb-md-0 small text-muted">Utilizamos cookies para acompañar tu experiencia con respeto y calidez.</p>
            <button onclick="aceptarCookies()" class="btn btn-dark btn-sm rounded-pill px-4">ACEPTAR</button>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="cp-footer py-5 mt-auto">
        <div class="container px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="cp-footer-brand mb-3">
                        <img src="assets/img/logocarton.png" alt="CartonPets">
                        <h6 class="mb-0">CARTONPETS</h6>
                    </div>
                    <p class="small" style="color: rgba(255,250,241,0.55); line-height: 1.6;">Ofrecemos una alternativa digna y ecológica para el último adiós. Nuestros ataúdes de cartón corrugado son un tributo de paz y serenidad.</p>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3">Tienda</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="#catalogo" class="small">Catálogo</a>
                        <a href="?categoria=gatos" class="small">Gatos</a>
                        <a href="?categoria=otros" class="small">Perros</a>
                        <a href="?categoria=roedores" class="small">Roedores</a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3">Empresa</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="muro.php" class="small">Muro del Recuerdo</a>
                        <a href="colaboradores.php" class="small">Subir Recuerdo</a>
                        <a href="terminos.php" class="small">Términos</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <p class="small mb-1" style="color: rgba(255,250,241,0.55);">📧 cartonpets@gmail.com</p>
                    <p class="small mb-1" style="color: rgba(255,250,241,0.55);">📱 +56 9 5189 8299</p>
                    <p class="small mb-3" style="color: rgba(255,250,241,0.55);">📍 Iquique, Chile</p>
                    <a href="https://wa.me/56951898299" class="btn btn-sm rounded-pill px-4 fw-bold" style="background: rgba(255,250,241,0.12); color: #fffaf1; border: 1px solid rgba(255,250,241,0.2);" target="_blank">ESCRIBIR POR WHATSAPP</a>
                </div>
            </div>
            <div class="cp-footer-bottom mt-5 pt-4 text-center">
                <p class="small mb-0">&copy; <?php echo date('Y'); ?> CartonPets — Despedida Sustentable.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/56951898299" class="btn-whatsapp-float" target="_blank">
        <img src="assets/img/logowatsap.png" alt="WhatsApp">
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function aceptarCookies() {
            document.cookie = "cookies_aceptadas=true; max-age=" + (60*60*24*30) + "; path=/";
            document.getElementById('cookieBanner').style.display = 'none';
        }
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const t = document.querySelector(a.getAttribute('href'));
                if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>
