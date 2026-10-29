<?php
/**
 * Admin Activity Logger
 * Logs admin login/logout, product changes, order status changes and other admin actions.
 */

if (!function_exists('sip_ensure_activity_logs_table')) {
    function sip_ensure_activity_logs_table(mysqli $conn): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        $sql = "CREATE TABLE IF NOT EXISTS admin_activity_logs (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            admin_id INT NULL,
            admin_name VARCHAR(150) NULL,
            login_id VARCHAR(190) NULL,
            action VARCHAR(80) NOT NULL,
            module VARCHAR(80) NOT NULL,
            record_type VARCHAR(80) NULL,
            record_id INT NULL,
            description TEXT NULL,
            details LONGTEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_admin_id (admin_id),
            KEY idx_action (action),
            KEY idx_module (module),
            KEY idx_record (record_type, record_id),
            KEY idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        mysqli_query($conn, $sql);
        $done = true;
    }
}

if (!function_exists('sip_activity_client_ip')) {
    function sip_activity_client_ip(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $value = trim((string)$_SERVER[$key]);
                if ($key === 'HTTP_X_FORWARDED_FOR') {
                    $parts = explode(',', $value);
                    $value = trim($parts[0] ?? $value);
                }
                return substr($value, 0, 45);
            }
        }
        return '';
    }
}

if (!function_exists('sip_log_admin_activity')) {
    function sip_log_admin_activity(
        mysqli $conn,
        string $action,
        string $module,
        string $description = '',
        string $recordType = '',
        int $recordId = 0,
        array $details = []
    ): bool {
        sip_ensure_activity_logs_table($conn);

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $adminId = isset($details['_admin_id']) ? (int)$details['_admin_id'] : (int)($_SESSION['admin_id'] ?? 0);
        $adminName = isset($details['_admin_name']) ? (string)$details['_admin_name'] : (string)($_SESSION['admin_name'] ?? '');
        $loginId = isset($details['_login_id']) ? (string)$details['_login_id'] : (string)($_SESSION['admin_login_id'] ?? ($_SESSION['admin_email'] ?? ''));

        unset($details['_admin_id'], $details['_admin_name'], $details['_login_id']);

        $details['page'] = basename($_SERVER['SCRIPT_NAME'] ?? '');
        $details['method'] = $_SERVER['REQUEST_METHOD'] ?? '';

        $actionEsc = mysqli_real_escape_string($conn, substr($action, 0, 80));
        $moduleEsc = mysqli_real_escape_string($conn, substr($module, 0, 80));
        $recordTypeEsc = mysqli_real_escape_string($conn, substr($recordType, 0, 80));
        $descriptionEsc = mysqli_real_escape_string($conn, $description);
        $adminNameEsc = mysqli_real_escape_string($conn, substr($adminName, 0, 150));
        $loginIdEsc = mysqli_real_escape_string($conn, substr($loginId, 0, 190));
        $ipEsc = mysqli_real_escape_string($conn, sip_activity_client_ip());
        $uaEsc = mysqli_real_escape_string($conn, substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255));
        $detailsJson = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $detailsEsc = mysqli_real_escape_string($conn, $detailsJson ?: '{}');

        $adminSql = $adminId > 0 ? (string)$adminId : 'NULL';
        $recordSql = $recordId > 0 ? (string)$recordId : 'NULL';

        $sql = "INSERT INTO admin_activity_logs
            (admin_id, admin_name, login_id, action, module, record_type, record_id, description, details, ip_address, user_agent)
            VALUES
            ($adminSql, '$adminNameEsc', '$loginIdEsc', '$actionEsc', '$moduleEsc', '$recordTypeEsc', $recordSql, '$descriptionEsc', '$detailsEsc', '$ipEsc', '$uaEsc')";

        return (bool)mysqli_query($conn, $sql);
    }
}
