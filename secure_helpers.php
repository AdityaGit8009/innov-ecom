<?php
if (!function_exists('ecom_h')) {
function ecom_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
}

if (!function_exists('ecom_user_id')) {
function ecom_user_id() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!empty($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }

    /*
       If PHP session expires but auth_token cookie is still available,
       recover the logged-in user from JWT + login/users tables.
    */
    global $conn;
    if (!empty($_COOKIE['auth_token']) && isset($conn)) {
        $jwtPath = __DIR__ . '/jwt.php';
        if (file_exists($jwtPath)) {
            require_once $jwtPath;
            $payload = JWT::decode($_COOKIE['auth_token']);
            if (!empty($payload['role']) && $payload['role'] === 'user' && !empty($payload['login_id'])) {
                $loginId = mysqli_real_escape_string($conn, $payload['login_id']);
                $q = mysqli_query($conn, "SELECT id FROM users WHERE email='$loginId' OR mobile='$loginId' LIMIT 1");
                if ($q && ($row = mysqli_fetch_assoc($q))) {
                    $_SESSION['user_id'] = (int)$row['id'];
                    return (int)$row['id'];
                }
            }
        }
    }

    return 0;
}
}

if (!function_exists('ecom_require_user')) {
function ecom_require_user() {
    $uid = ecom_user_id();
    if ($uid <= 0) {
        echo "<script>alert('Please login first'); location.href='index.php';</script>";
        exit;
    }
    return $uid;
}
}

if (!function_exists('ecom_get_user')) {
function ecom_get_user($conn, $user_id) {
    $user_id = (int)$user_id;
    $q = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' LIMIT 1");
    return $q ? mysqli_fetch_assoc($q) : null;
}
}

if (!function_exists('ecom_table_has_column')) {
function ecom_table_has_column($conn, $table, $column) {
    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $q = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $q && mysqli_num_rows($q) > 0;
}
}

if (!function_exists('ecom_order_badge')) {
function ecom_order_badge($status) {
    $status = strtolower((string)$status);
    if ($status === 'delivered') return 'success';
    if ($status === 'shipped' || $status === 'packed') return 'warning text-dark';
    if ($status === 'cancelled' || $status === 'returned') return 'danger';
    if ($status === 'confirmed' || $status === 'processing') return 'info text-dark';
    return 'secondary';
}
}

if (!function_exists('ecom_set_auth_cookie')) {
    function ecom_set_auth_cookie($token, $lifetimeSeconds = 86400)
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('auth_token', $token, [
            'expires' => time() + (int) $lifetimeSeconds,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

if (!function_exists('ecom_clear_auth_cookie')) {
    function ecom_clear_auth_cookie()
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('auth_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
?>
