<?php
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT DATE(fecha) as dia, SUM(total) as total_ventas 
        FROM pedidos 
        WHERE estado IN ('pagado', 'entregado') 
            AND fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(fecha) 
        ORDER BY dia ASC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $valores = [];
    
    foreach ($rows as $row) {
        $labels[] = date('d/m', strtotime($row['dia']));
        $valores[] = (float)$row['total_ventas'];
    }
    
    echo json_encode(['labels' => $labels, 'valores' => $valores]);
} catch (PDOException $e) {
    echo json_encode(['labels' => [], 'valores' => []]);
}
