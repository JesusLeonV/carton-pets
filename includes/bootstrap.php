<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

require_once __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit();
}

function require_login(string $redirectTo = 'login.php'): int
{
    if (empty($_SESSION['usuario_id'])) {
        redirect($redirectTo);
    }

    return (int) $_SESSION['usuario_id'];
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        exit('Solicitud no válida. Vuelve a intentarlo.');
    }
}

function safe_int($value, int $min = 1, int $max = PHP_INT_MAX): int
{
    $value = filter_var($value, FILTER_VALIDATE_INT);
    if ($value === false) {
        return $min;
    }

    return max($min, min($max, (int) $value));
}

function safe_image(?string $filename, string $fallback = 'caja_memorial.jpg'): string
{
    $filename = basename((string) $filename);
    if ($filename === '' || !preg_match('/\A[a-zA-Z0-9._-]+\z/', $filename)) {
        return $fallback;
    }

    return $filename;
}

function current_user_is_admin(): bool
{
    return isset($_SESSION['user_email']) && strtolower((string) $_SESSION['user_email']) === 'cartonpets@gmail.com';
}

// --- CONFIG NEGOCIO ---
function get_config(string $clave, string $default = ''): string
{
    global $pdo;
    static $cache = [];
    if (isset($cache[$clave])) {
        return $cache[$clave];
    }
    try {
        $stmt = $pdo->prepare("SELECT valor FROM config_negocio WHERE clave = ?");
        $stmt->execute([$clave]);
        $row = $stmt->fetch();
        $cache[$clave] = $row ? (string) $row['valor'] : $default;
    } catch (PDOException $e) {
        $cache[$clave] = $default;
    }
    return $cache[$clave];
}

function get_config_int(string $clave, int $default = 0): int
{
    return (int) get_config($clave, (string) $default);
}

// --- WHOLESALE / MAYORISTA ---
function calcular_descuento_mayorista(int $precio_unitario, int $cantidad): array
{
    $descuento_pct = get_config_int('descuento_por_mayor', 10);
    $cantidad_min = get_config_int('cantidad_minima_mayor', 3);

    if ($cantidad >= $cantidad_min && $descuento_pct > 0) {
        $descuento = (int) round($precio_unitario * $cantidad * $descuento_pct / 100);
        $total_con_descuento = ($precio_unitario * $cantidad) - $descuento;
        return [
            'aplica' => true,
            'porcentaje' => $descuento_pct,
            'descuento' => $descuento,
            'total_original' => $precio_unitario * $cantidad,
            'total_final' => $total_con_descuento,
        ];
    }

    return [
        'aplica' => false,
        'porcentaje' => 0,
        'descuento' => 0,
        'total_original' => $precio_unitario * $cantidad,
        'total_final' => $precio_unitario * $cantidad,
    ];
}

// --- DELIVERY ---
function delivery_gratis(): bool
{
    return get_config_int('delivery_gratis', 1) === 1;
}
