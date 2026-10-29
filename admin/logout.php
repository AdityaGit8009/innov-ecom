<?php
require_once __DIR__ . '/../secure_helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/ActivityLogger.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['admin_id'])) {
    sip_log_admin_activity($conn, 'LOGOUT', 'AUTH', 'Admin logged out', 'admin', (int)$_SESSION['admin_id']);
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();
ecom_clear_auth_cookie();
header('Location: ../index.php');
exit;
