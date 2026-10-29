<?php
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../secure_helpers.php';
require_once __DIR__ . '/../includes/ActivityLogger.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_COOKIE['auth_token'])) {
    header("Location: ../index.php");
    exit;
}
$payload = JWT::decode($_COOKIE['auth_token']);
if (!$payload || ($payload['role'] ?? '') !== 'admin') {
    ecom_clear_auth_cookie();
    header("Location: ../index.php");
    exit;
}

$adminId = (int)($payload['uid'] ?? 0);
$adminQry = mysqli_query(
    $conn,
    "SELECT l.*, r.role_name, r.permissions FROM login l LEFT JOIN roles r ON r.id=l.role_id WHERE l.id=$adminId AND l.role='admin' AND l.status=1 LIMIT 1"
);
if (!$adminQry || mysqli_num_rows($adminQry) === 0) {
    ecom_clear_auth_cookie();
    header("Location: ../index.php");
    exit;
}
$currentAdmin = mysqli_fetch_assoc($adminQry);
$_SESSION['admin_id'] = (int)$currentAdmin['id'];
$_SESSION['admin_name'] = $currentAdmin['name'] ?: $currentAdmin['login_id'];
$_SESSION['admin_login_id'] = $currentAdmin['login_id'];
$_SESSION['admin_permissions'] = json_decode($currentAdmin['permissions'] ?: '[]', true) ?: [];

if (!defined('ADMIN_SUPER_EMAIL')) {
    define('ADMIN_SUPER_EMAIL', 'webdeveloper8009@gmail.com');
}
$_SESSION['admin_is_super'] = strcasecmp(trim((string)$currentAdmin['login_id']), ADMIN_SUPER_EMAIL) === 0;

function admin_is_super_admin()
{
    return !empty($_SESSION['admin_is_super']);
}

function admin_can($module)
{
    if (admin_is_super_admin()) {
        return true;
    }
    if ($module === false || $module === null) {
        return true;
    }
    $perms = $_SESSION['admin_permissions'] ?? [];
    return in_array('all', $perms, true) || in_array($module, $perms, true);
}

/**
 * Admin script basename => permission key (must exist in roles UI).
 * false = any authenticated admin (e.g. dashboard).
 */
function admin_pages_module_map()
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $map = [
        'dashboard.php' => false,
        'products.php' => 'products',
        'product_images.php' => 'products',
        'brands.php' => 'products',
        'variants.php' => 'products',
        'attributes.php' => 'products',
        'attribute_values.php' => 'products',
        'bulk_upload.php' => 'products',
        'categories.php' => 'categories',
        'sub_categories.php' => 'categories',
        'get_subcategories.php' => 'categories',
        'stock_list.php' => 'stock',
        'low_stock.php' => 'stock',
        'out_stock.php' => 'stock',
        'stock_logs.php' => 'stock',
        'bulk_stock.php' => 'stock',
        'product_price.php' => 'price',
        'discount_price.php' => 'price',
        'tax.php' => 'price',
        'bulk_price.php' => 'price',
        'projects.php' => 'projects',
        'suppliers.php' => 'suppliers',
        'supplier_products.php' => 'suppliers',
        'purchase_history.php' => 'suppliers',
        'edit_supplier.php' => 'suppliers',
        'delete_supplier.php' => 'suppliers',
        'stock_report.php' => 'reports',
        'low_stock_report.php' => 'reports',
        'inventory_valuation.php' => 'reports',
        'product_movement.php' => 'reports',
        'orders.php' => 'orders',
        'pending_orders.php' => 'orders',
        'completed_orders.php' => 'orders',
        'cancelled_orders.php' => 'orders',
        'order_view.php' => 'orders',
        'customers.php' => 'customers',
        'customer_orders.php' => 'customers',
        'customer_addresses.php' => 'customers',
        'contact.php' => 'customers',
        'toggle_default_address.php' => 'customers',
        'payment_methods.php' => 'payments',
        'transactions.php' => 'payments',
        'shipping_zones.php' => 'shipping',
        'shipping_charges.php' => 'shipping',
        'delivery_partners.php' => 'shipping',
        'coupons.php' => 'coupons',
        'product_reviews.php' => 'reviews',
        'manage_training_technologies.php' => 'services',
        'manage_technology_content.php' => 'services',
        'save_training_technology.php' => 'services',
        'save_technology_content.php' => 'services',
        'download_course_template.php' => 'services',
        'admins.php' => 'admins',
        'activity_logs.php' => 'admins',
    ];
    return $map;
}

function admin_enforce_route_permissions()
{
    if (admin_is_super_admin()) {
        return;
    }

    $base = basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');

    if ($base === 'roles.php') {
        header('Location: dashboard.php?denied=1');
        exit;
    }

    if ($base === 'dashboard.php') {
        return;
    }

    $map = admin_pages_module_map();
    if (!isset($map[$base])) {
        header('Location: dashboard.php?denied=1');
        exit;
    }

    $module = $map[$base];
    if ($module === false) {
        return;
    }

    if (!admin_can($module)) {
        header('Location: dashboard.php?denied=1');
        exit;
    }
}

admin_enforce_route_permissions();
