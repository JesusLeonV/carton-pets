<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$admin_autorizado = 'cartonpets@gmail.com';
if (!current_user_is_admin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $pedido_id = safe_int($_POST['pedido_id'] ?? 0);
    $nuevo_estado = $_POST['nuevo_estado'] ?? '';

    if ($pedido_id > 0 && in_array($nuevo_estado, ['pendiente', 'pagado', 'entregado'])) {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $pedido_id]);
            redirect('../admin.php?accion=pedidos&msg=' . urlencode("Pedido #$pedido_id actualizado a '$nuevo_estado'."));
        } catch (PDOException $e) {
            error_log('Error actualizar pedido: ' . $e->getMessage());
            redirect('../admin.php?accion=pedidos&error=' . urlencode("Error al actualizar pedido."));
        }
    }
}

redirect('../admin.php?accion=pedidos');
