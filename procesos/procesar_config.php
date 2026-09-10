<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (!current_user_is_admin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $accion = $_POST['accion'] ?? 'guardar_config';

    if ($accion === 'guardar_config') {
        $campos = [
            'descuento_por_mayor',
            'cantidad_minima_mayor',
            'delivery_gratis',
            'delivery_zona',
            'telefono_whatsapp',
            'email_contacto',
        ];

        try {
            foreach ($campos as $campo) {
                if (isset($_POST[$campo])) {
                    $valor = trim((string) $_POST[$campo]);
                    $stmt = $pdo->prepare("INSERT INTO config_negocio (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?");
                    $stmt->execute([$campo, $valor, $valor]);
                }
            }
            redirect('../admin.php?accion=config&msg=' . urlencode("Configuración guardada con éxito."));
        } catch (PDOException $e) {
            error_log('Error config: ' . $e->getMessage());
            redirect('../admin.php?accion=config&error=' . urlencode("Error al guardar."));
        }
    }

    if ($accion === 'aprobar_resena') {
        $resena_id = intval($_POST['resena_id'] ?? 0);
        if ($resena_id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE resenas SET aprobada = 1 WHERE id = ?");
                $stmt->execute([$resena_id]);
                redirect('../admin.php?accion=resenas&msg=' . urlencode("Reseña aprobada."));
            } catch (PDOException $e) {
                error_log('Error aprobar resena: ' . $e->getMessage());
                redirect('../admin.php?accion=resenas&error=' . urlencode("Error al aprobar."));
            }
        }
    }

    if ($accion === 'rechazar_resena') {
        $resena_id = intval($_POST['resena_id'] ?? 0);
        if ($resena_id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE resenas SET aprobada = 0 WHERE id = ?");
                $stmt->execute([$resena_id]);
                redirect('../admin.php?accion=resenas&msg=' . urlencode("Reseña rechazada."));
            } catch (PDOException $e) {
                error_log('Error rechazar resena: ' . $e->getMessage());
                redirect('../admin.php?accion=resenas&error=' . urlencode("Error al rechazar."));
            }
        }
    }
}

redirect('../admin.php');
