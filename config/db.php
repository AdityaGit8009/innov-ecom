<?php

$dbLocal = __DIR__ . '/config.local.php';
if (is_readable($dbLocal)) {
    $cfg = require $dbLocal;
} else {
    $cfg = [
        'db_host' => getenv('DB_HOST') ?: 'localhost',
        'db_user' => getenv('DB_USER') ?: 'root',
        'db_pass' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'db_name' => getenv('DB_NAME') ?: 'spiinnovation',
    ];  
}

$host = $cfg['db_host'] ?? 'localhost';
$user = $cfg['db_user'] ?? 'root';
$pass = $cfg['db_pass'] ?? '';
$name = $cfg['db_name'] ?? 'spiinnovation';

$conn = mysqli_connect($host, $user, $pass, $name);
if (!$conn) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Database connection failed. Check config/config.local.php or DB_* environment variables.');
}

mysqli_set_charset($conn, 'utf8mb4');

if (!function_exists('format_price')) {
    function format_price($amount) {
        $formatted = number_format((float)$amount, 2);
        return preg_replace('/\.00$/', '', $formatted);
    }
}
