<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
$adminHtmlTitle = 'Activity Logs';
sip_ensure_activity_logs_table($conn);

function al_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$search = trim($_GET['search'] ?? '');
$module = trim($_GET['module'] ?? '');
$action = trim($_GET['action'] ?? '');
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');

$where = [];
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where[] = "(admin_name LIKE '%$s%' OR login_id LIKE '%$s%' OR description LIKE '%$s%' OR record_type LIKE '%$s%' OR details LIKE '%$s%')";
}
if ($module !== '') {
    $m = mysqli_real_escape_string($conn, $module);
    $where[] = "module = '$m'";
}
if ($action !== '') {
    $a = mysqli_real_escape_string($conn, $action);
    $where[] = "action = '$a'";
}
if ($from !== '') {
    $f = mysqli_real_escape_string($conn, $from);
    $where[] = "DATE(created_at) >= '$f'";
}
if ($to !== '') {
    $t = mysqli_real_escape_string($conn, $to);
    $where[] = "DATE(created_at) <= '$t'";
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$logs = mysqli_query($conn, "
    SELECT *
    FROM admin_activity_logs
    $whereSql
    ORDER BY id DESC
    LIMIT 300
");

$modules = mysqli_query($conn, "SELECT DISTINCT module FROM admin_activity_logs ORDER BY module ASC");
$actions = mysqli_query($conn, "SELECT DISTINCT action FROM admin_activity_logs ORDER BY action ASC");

include "includes/header.php";
?>

<style>
    .log-card { border: 0; border-radius: 16px; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
    .log-title { font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; }
    .badge-action { font-size: 12px; padding: 6px 10px; border-radius: 999px; }
    .detail-box { max-width: 360px; white-space: normal; color: #64748b; font-size: 13px; }
    .table td, .table th { vertical-align: middle; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="log-title mb-1"><i class="bx bx-history"></i> Admin Activity Logs</h4>
            <p class="text-muted mb-0">Order status, product changes, login/logout and admin activity records.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Dashboard</a>
    </div>

    <div class="card log-card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="<?= al_h($search) ?>" placeholder="Admin, email, description...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select">
                        <option value="">All Modules</option>
                        <?php while ($m = mysqli_fetch_assoc($modules)): ?>
                            <option value="<?= al_h($m['module']) ?>" <?= $module === $m['module'] ? 'selected' : '' ?>><?= al_h($m['module']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <?php while ($a = mysqli_fetch_assoc($actions)): ?>
                            <option value="<?= al_h($a['action']) ?>" <?= $action === $a['action'] ? 'selected' : '' ?>><?= al_h($a['action']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="<?= al_h($from) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="<?= al_h($to) ?>">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" type="submit"><i class="bx bx-filter-alt"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card log-card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date / Time</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Record</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs && mysqli_num_rows($logs) > 0): ?>
                        <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                            <?php
                                $badge = 'bg-label-secondary';
                                if (in_array($log['action'], ['LOGIN', 'CREATE'], true)) $badge = 'bg-label-success';
                                if (in_array($log['action'], ['UPDATE', 'ORDER_STATUS_UPDATE', 'STATUS_CHANGE'], true)) $badge = 'bg-label-primary';
                                if (in_array($log['action'], ['DELETE', 'LOGOUT'], true)) $badge = 'bg-label-danger';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= date('d M Y', strtotime($log['created_at'])) ?></strong><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($log['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= al_h($log['admin_name'] ?: 'Unknown Admin') ?></strong><br>
                                    <small class="text-muted"><?= al_h($log['login_id']) ?></small>
                                </td>
                                <td><span class="badge <?= $badge ?> badge-action"><?= al_h($log['action']) ?></span></td>
                                <td><?= al_h($log['module']) ?></td>
                                <td>
                                    <?= al_h($log['record_type']) ?>
                                    <?php if (!empty($log['record_id'])): ?>
                                        <br><small class="text-muted">#<?= (int)$log['record_id'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><div class="detail-box"><?= al_h($log['description']) ?></div></td>
                                <td><?= al_h($log['ip_address']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle fs-3 d-block mb-2"></i>
                                No activity logs found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
<?php ob_end_flush(); ?>
