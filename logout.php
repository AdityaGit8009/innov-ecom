<?php
require_once __DIR__ . '/secure_helpers.php';
session_start();

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

header('Location: index.php');
exit;