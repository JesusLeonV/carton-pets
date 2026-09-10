<?php
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Debes iniciar sesión para dejar una reseña']);
    exit;
}

if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido']);
    exit;
}

$producto_id = intval($_POST['producto_id'] ?? 0);
$calificacion = intval($_POST['calificacion'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');

if ($producto_id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Producto inválido']);
    exit;
}

if ($calificacion < 1 || $calificacion > 5) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'La calificación debe ser entre 1 y 5']);
    exit;
}

if (empty($comentario)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El comentario es obligatorio']);
    exit;
}

if (mb_strlen($comentario) > 500) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El comentario no puede superar 500 caracteres']);
    exit;
}

if (mb_strlen($titulo) > 100) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El título no puede superar 100 caracteres']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO resenas (producto_id, usuario_id, calificacion, titulo, comentario, aprobada) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$producto_id, $_SESSION['usuario_id'], $calificacion, $titulo, $comentario]);
    
    echo json_encode(['ok' => true, 'message' => 'Reseña enviada. Será visible tras ser aprobada.']);
} catch (PDOException $e) {
    error_log("Error insertando reseña: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al guardar la reseña']);
}
