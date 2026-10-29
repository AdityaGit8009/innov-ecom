<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
$adminHtmlTitle = 'Admin users';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== SAVE ADMIN ===== */
if (isset($_POST['save_admin'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = admin_safe($conn, $_POST['name'] ?? '');
    $emailRaw = trim($_POST['email'] ?? '');
    $email = admin_safe($conn, $emailRaw);
    $mobile = admin_safe($conn, $_POST['mobile'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 0);
    $pass = trim($_POST['password'] ?? '');

    $editingSuper = false;
    $dbRoleIdForSuper = 0;
    if ($id > 0) {
        $tmeta = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT login_id, role_id FROM login WHERE id='" . (int)$id . "' AND role='admin' LIMIT 1"
        ));
        if ($tmeta) {
            $editingSuper = strcasecmp(trim((string)$tmeta['login_id']), ADMIN_SUPER_EMAIL) === 0;
            $dbRoleIdForSuper = (int)($tmeta['role_id'] ?? 0);
        }
    }

    if ($editingSuper && !admin_is_super_admin()) {
        header("Location: admins.php?msg=forbidden_super_edit");
        exit;
    }

    if ($editingSuper && admin_is_super_admin()) {
        $email = admin_safe($conn, ADMIN_SUPER_EMAIL);
        $role_id = $dbRoleIdForSuper > 0 ? $dbRoleIdForSuper : $role_id;
    }

    if ($name == '' || $email == '' || $role_id <= 0) {
        header("Location: admins.php?msg=error");
        exit;
    }

    if (!admin_is_super_admin()) {
        if (strcasecmp($emailRaw, ADMIN_SUPER_EMAIL) === 0) {
            header("Location: admins.php?msg=forbidden_super_email");
            exit;
        }
        $rolePermRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT permissions FROM roles WHERE id='" . (int)$role_id . "' LIMIT 1"));
        $rolePerms = json_decode($rolePermRow['permissions'] ?? '[]', true) ?: [];
        if (in_array('all', $rolePerms, true)) {
            header("Location: admins.php?msg=forbidden_role_all");
            exit;
        }
    }

    if ($id === 0) {
        if ($pass === '') {
            header("Location: admins.php?msg=password_required");
            exit;
        }

        if (strcasecmp($emailRaw, ADMIN_SUPER_EMAIL) === 0) {
            $dupSuperQ = mysqli_query(
                $conn,
                "SELECT id FROM login WHERE role='admin' AND LOWER(TRIM(login_id))=LOWER('" . admin_safe($conn, ADMIN_SUPER_EMAIL) . "') LIMIT 1"
            );
            if ($dupSuperQ && mysqli_num_rows($dupSuperQ) > 0) {
                header("Location: admins.php?msg=forbidden_super_email");
                exit;
            }
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $hash = admin_safe($conn, $hash);

        $res = mysqli_query($conn, "
            INSERT INTO login 
            (name, login_id, password, mobile, role, role_id, status) 
            VALUES 
            ('$name', '$email', '$hash', '$mobile', 'admin', '$role_id', 1)
        ");
    } else {
        $res = mysqli_query($conn, "
            UPDATE login SET 
                name = '$name',
                login_id = '$email',
                mobile = '$mobile',
                role_id = '$role_id'
            WHERE id = '$id' 
            AND role = 'admin'
        ");

        if ($res && $pass !== '') {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $hash = admin_safe($conn, $hash);

            $res = mysqli_query($conn, "
                UPDATE login 
                SET password = '$hash' 
                WHERE id = '$id' 
                AND role = 'admin'
            ");
        }
    }

    header("Location: admins.php?msg=" . ($res ? 'ok' : 'error'));
    exit;
}

/* ===== DELETE ADMIN ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $targetDel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT login_id FROM login WHERE id='$id' AND role='admin' LIMIT 1"));
    if ($targetDel && strcasecmp((string)$targetDel['login_id'], ADMIN_SUPER_EMAIL) === 0) {
        header("Location: admins.php?msg=cannot_delete_super");
        exit;
    }

    if ($id !== (int)($_SESSION['admin_id'] ?? 0)) {
        mysqli_query($conn, "
            DELETE FROM login 
            WHERE id = '$id' 
            AND role = 'admin'
        ");
    }

    header("Location: admins.php?msg=deleted");
    exit;
}

/* ===== STATUS UPDATE ===== */
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'] === 1 ? 1 : 0;

    $targetSt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT login_id FROM login WHERE id='$id' AND role='admin' LIMIT 1"));
    if ($targetSt && strcasecmp(trim((string)$targetSt['login_id']), ADMIN_SUPER_EMAIL) === 0) {
        header("Location: admins.php?msg=cannot_change_super_status");
        exit;
    }

    if ($id !== (int)($_SESSION['admin_id'] ?? 0)) {
        mysqli_query($conn, "
            UPDATE login 
            SET status = '$status' 
            WHERE id = '$id' 
            AND role = 'admin'
        ");
    }

    header("Location: admins.php?msg=status");
    exit;
}

/* ===== FETCH DATA ===== */
$admins = mysqli_query($conn, "
    SELECT 
        l.*, 
        r.role_name 
    FROM login l 
    LEFT JOIN roles r ON r.id = l.role_id 
    WHERE l.role = 'admin' 
    ORDER BY l.id DESC
");

$roles = mysqli_query($conn, "
    SELECT * 
    FROM roles 
    WHERE status = 'active' 
    ORDER BY role_name ASC
");

include "includes/header.php";
?>

<style>
    .page-title-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .page-title-box h4 {
        margin: 0;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-card {
        background: #ffffff;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .admin-card .card-body {
        padding: 22px;
    }

    #adminsTable {
        width: 100% !important;
        font-size: 14px;
    }

    #adminsTable thead th {
        background: #fff3ec !important;
        color: #2b160b !important;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
        vertical-align: middle;
        padding: 14px 12px;
        border-color: #ead8cd;
    }

    #adminsTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #adminsTable tbody tr:hover {
        background: #f8fafc;
    }

    .admin-name {
        font-weight: 800;
        color: #334155;
    }

    .login-id-text {
        font-weight: 700;
        color: #334155;
    }

    .role-badge {
        background: #e0f2fe;
        color: #075985;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 800;
        display: inline-block;
        font-size: 12px;
    }

    .badge-soft {
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
        text-decoration: none;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .info-alert {
        border: 0;
        border-radius: 12px;
        background: #e0f2fe;
        color: #075985;
        font-weight: 600;
        padding: 11px 14px;
        margin-bottom: 16px;
    }

    .btn-add-admin {
        border-radius: 10px;
        font-weight: 700;
        padding: 8px 14px;
    }

    .action-dropdown .btn {
        border: 0;
        background: transparent;
        color: #334155;
        font-size: 20px;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .action-dropdown .btn:hover,
    .action-dropdown .btn:focus {
        background: #f1f5f9;
        color: #0f172a;
        box-shadow: none;
    }

    .action-dropdown .dropdown-toggle::after {
        display: none;
    }

    .action-dropdown .dropdown-menu {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
        padding: 8px;
        min-width: 160px;
    }

    .action-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
    }

    .action-dropdown .dropdown-item:hover {
        background: #f8fafc;
    }

    .action-dropdown .dropdown-item.text-danger:hover {
        background: #fee2e2;
        color: #991b1b !important;
    }

    .admin-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .admin-modal .modal-header {
        /* background: linear-gradient(135deg, #ff6a00, #ff3d00); */
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
    }

    .admin-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .admin-modal .btn-close {
        /* The above code is a CSS filter property that is being applied to an element. It is setting
        the brightness of the element to 0 (making it completely black) and inverting the colors of
        the element. */
        /* filter: brightness(0) invert(1); */
        opacity: 1;
    }

    .admin-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .admin-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .admin-modal .form-control,
    .admin-modal .form-select {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .admin-modal .form-control:focus,
    .admin-modal .form-select:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .admin-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-admin {
        background: #71dd37;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-admin:hover {
        background: #e94d14;
        color: #fff;
    }

    .small-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 5px;
        display: block;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-user"></i> Admin Login Users
    </h4>

    <button class="btn btn-primary btn-add-admin" onclick="openAddModal()">
        <i class="bx bx-plus"></i> Add Admin
    </button>
</div>



<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="adminsTable"
                data-module="admins">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email/Login ID</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($a = mysqli_fetch_assoc($admins)) {
                        $adminJson = htmlspecialchars(
                            json_encode($a, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $status = (int)($a['status'] ?? 0);
                        $nextStatus = $status ? 0 : 1;
                        $isSuperAccount = strcasecmp(trim((string)($a['login_id'] ?? '')), ADMIN_SUPER_EMAIL) === 0;
                        $viewerSuper = admin_is_super_admin();
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="admin-name">
                                    <?= htmlspecialchars($a['name'] ?: '-') ?>
                                    <?php if ($isSuperAccount): ?>
                                        <span class="badge bg-dark ms-1">Super</span>
                                    <?php endif; ?>
                                </span>
                            </td>

                            <td>
                                <span class="login-id-text">
                                    <?= htmlspecialchars($a['login_id'] ?? '') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($a['mobile'] ?: '-') ?>
                            </td>

                            <td class="text-center">
                                <span class="role-badge">
                                    <?= htmlspecialchars($a['role_name'] ?: 'Admin') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php if ($isSuperAccount): ?>
                                    <span class="badge-soft <?= $status ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                        <?= $status ? 'Active' : 'Inactive' ?>
                                    </span>
                                    <small class="text-muted d-block mt-1">Locked</small>
                                <?php else: ?>
                                    <a href="?id=<?= (int)$a['id'] ?>&status=<?= $nextStatus ?>"
                                        onclick="return confirm('Change admin status?')"
                                        class="badge-soft <?= $status ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                        <?= $status ? 'Active' : 'Inactive' ?>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php if ($isSuperAccount && !$viewerSuper): ?>
                                    <span class="text-muted small">Protected</span>
                                <?php elseif ($isSuperAccount && $viewerSuper): ?>
                                    <div class="dropdown action-dropdown">
                                        <button class="btn dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item"
                                                    type="button"
                                                    onclick='openEditModal(<?= $adminJson ?>)'>
                                                    <i class="bx bx-edit"></i> Edit
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="dropdown action-dropdown">
                                        <button class="btn dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item"
                                                    type="button"
                                                    onclick='openEditModal(<?= $adminJson ?>)'>
                                                    <i class="bx bx-edit"></i> Edit
                                                </button>
                                            </li>

                                            <li>
                                                <a href="?delete=<?= (int)$a['id'] ?>"
                                                    onclick="return confirm('Delete admin login?')"
                                                    class="dropdown-item text-danger">
                                                    <i class="bx bx-trash"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- ADMIN MODAL -->
<div class="modal fade admin-modal" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="adminForm">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Admin</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="name" id="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email/Login ID</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input class="form-control" name="mobile" id="mobile">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                    <small class="small-help">
                        Edit mode me blank chhodne par old password same rahega.
                    </small>
                </div>

                <div class="mb-0">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role_id" id="role_id" required>
                        <option value="">Select Role</option>
                        <?php while ($r = mysqli_fetch_assoc($roles)): ?>
                            <option value="<?= (int)$r['id'] ?>">
                                <?= htmlspecialchars($r['role_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-save-admin" name="save_admin">
                    <i class="bx bx-save"></i> Save Admin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('adminForm').reset();

        document.getElementById('id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Admin';
        document.getElementById('email').readOnly = false;
        document.getElementById('role_id').disabled = false;
        document.getElementById('role_id').title = '';

        new bootstrap.Modal(document.getElementById('adminModal')).show();
    }

    function openEditModal(a) {
        document.getElementById('id').value = a.id || '';
        document.getElementById('name').value = a.name || '';
        document.getElementById('email').value = a.login_id || '';
        document.getElementById('mobile').value = a.mobile || '';
        document.getElementById('role_id').value = a.role_id || '';
        document.getElementById('password').value = '';

        var superEmail = '<?= htmlspecialchars(strtolower(ADMIN_SUPER_EMAIL), ENT_QUOTES, 'UTF-8') ?>';
        var isSuper = ((a.login_id || '').trim().toLowerCase() === superEmail);
        document.getElementById('email').readOnly = isSuper;
        document.getElementById('role_id').disabled = false;
        document.getElementById('role_id').title = isSuper ? 'Super admin: saved role is fixed on server' : '';

        document.getElementById('modalTitle').innerText = 'Edit Admin';

        new bootstrap.Modal(document.getElementById('adminModal')).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Admin saved successfully!');}</script>";
    } elseif ($msg === 'status') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Admin status updated successfully!');}</script>";
    } elseif ($msg === 'deleted') {
        echo "<script>if(typeof showToast==='function'){showToast('warning','Admin deleted successfully!');}</script>";
    } elseif ($msg === 'password_required') {
        echo "<script>if(typeof showToast==='function'){showToast('error','Password is required for new admin!');}</script>";
    } elseif ($msg === 'forbidden_super_email' || $msg === 'forbidden_super_edit') {
        echo "<script>if(typeof showToast==='function'){showToast('error','Super admin account is protected.');}</script>";
    } elseif ($msg === 'forbidden_role_all') {
        echo "<script>if(typeof showToast==='function'){showToast('error','You cannot assign a full-access role.');}</script>";
    } elseif ($msg === 'cannot_delete_super') {
        echo "<script>if(typeof showToast==='function'){showToast('error','Super admin cannot be deleted.');}</script>";
    } elseif ($msg === 'cannot_change_super_status') {
        echo "<script>if(typeof showToast==='function'){showToast('error','Super admin status cannot be changed from here.');}</script>";
    } else {
        echo "<script>if(typeof showToast==='function'){showToast('error','Something went wrong!');}</script>";
    }
}

ob_end_flush();
?>