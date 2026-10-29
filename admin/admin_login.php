<?php
require_once '../jwt.php';
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';
require_once __DIR__ . '/../secure_helpers.php';
require_once __DIR__ . '/../includes/ActivityLogger.php';

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$loginId = mysqli_real_escape_string($conn, trim($data['loginId'] ?? ''));
$password = trim($data['password'] ?? '');

if ($loginId === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Email/mobile and password required']);
    exit;
}

$q = "SELECT l.*, r.role_name, r.permissions
      FROM login l
      LEFT JOIN roles r ON r.id = l.role_id
      WHERE (l.login_id='$loginId' OR l.mobile='$loginId') AND l.role='admin'
      LIMIT 1";
$res = mysqli_query($conn, $q);

if (!$res || mysqli_num_rows($res) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Admin not found in login table']);
    exit;
}

$admin = mysqli_fetch_assoc($res);
if ((int)$admin['status'] !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'Admin account inactive']);
    exit;
}

$valid = password_verify($password, $admin['password']);
// fallback for old plain text password only during migration
if (!$valid && hash_equals((string)$admin['password'], $password)) { $valid = true; }
if (!$valid) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
    exit;
}

$_SESSION['admin_id'] = (int)$admin['id'];
$_SESSION['admin_name'] = $admin['name'] ?: $admin['login_id'];
$_SESSION['admin_email'] = $admin['login_id'];
$_SESSION['admin_login_id'] = $admin['login_id'];
$_SESSION['admin_role_id'] = (int)$admin['role_id'];
$_SESSION['admin_permissions'] = json_decode($admin['permissions'] ?: '[]', true) ?: [];

$token = JWT::encode([
    'uid' => (int)$admin['id'],
    'login_id' => $admin['login_id'],
    'role' => 'admin',
    'role_id' => (int)$admin['role_id'],
    'exp' => time() + 86400
]);
ecom_set_auth_cookie($token, 86400);

sip_log_admin_activity($conn, 'LOGIN', 'AUTH', 'Admin logged in successfully', 'admin', (int)$admin['id'], [
    '_admin_id' => (int)$admin['id'],
    '_admin_name' => $admin['name'] ?: $admin['login_id'],
    '_login_id' => $admin['login_id'],
    'role_id' => (int)$admin['role_id']
]);

echo json_encode(['status' => 'success', 'token' => $token, 'redirect' => 'dashboard.php']);
