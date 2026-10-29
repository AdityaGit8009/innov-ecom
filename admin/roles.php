<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== SAVE ROLE ===== */
if (isset($_POST['save_role'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = admin_safe($conn, $_POST['role_name'] ?? '');

    $permissionsArray = $_POST['permissions'] ?? [];
    $permissionsArray = is_array($permissionsArray) ? $permissionsArray : [];
    if (!admin_is_super_admin()) {
        $permissionsArray = array_values(array_filter($permissionsArray, static function ($p) {
            return $p !== 'all';
        }));
    }
    $permissions = admin_safe($conn, json_encode($permissionsArray));

    if ($name == '') {
        header("Location: roles.php?msg=error");
        exit;
    }

    if ($id <= 0) {
        $res = mysqli_query($conn, "
            INSERT INTO roles (role_name, permissions) 
            VALUES ('$name', '$permissions')
        ");
    } else {
        $res = mysqli_query($conn, "
            UPDATE roles 
            SET role_name = '$name', permissions = '$permissions' 
            WHERE id = '$id'
        ");
    }

    header("Location: roles.php?msg=" . ($res ? 'ok' : 'error'));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM roles 
        WHERE id = '$id'
    ");

    header("Location: roles.php?msg=deleted");
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'] === 1 ? 'active' : 'inactive';

    mysqli_query($conn, "
        UPDATE roles 
        SET status = '$status' 
        WHERE id = '$id'
    ");

    header("Location: roles.php?msg=status");
    exit;
}

/* ===== FETCH ===== */
$roles = mysqli_query($conn, "
    SELECT * 
    FROM roles 
    ORDER BY id DESC
");

$modules = [
    "all",
    "products",
    "categories",
    "stock",
    "price",
    "projects",
    "suppliers",
    "reports",
    "orders",
    "customers",
    "payments",
    "shipping",
    "coupons",
    "reviews",
    "services",
    "settings",
    "admins",
];

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

    #rolesTable {
        width: 100% !important;
        font-size: 14px;
    }

    #rolesTable thead th {
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

    #rolesTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #rolesTable tbody tr:hover {
        background: #f8fafc;
    }

    .role-name {
        font-weight: 800;
        color: #334155;
    }

    .permission-box {
        max-width: 520px;
        white-space: normal;
        line-height: 1.7;
    }

    .permission-badge {
        display: inline-block;
        background: #e0f2fe;
        color: #075985;
        padding: 5px 9px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        margin: 2px;
        text-transform: capitalize;
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

    .btn-add-role {
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
        min-width: 165px;
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

    .role-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .role-modal .modal-header {
        /* background: linear-gradient(135deg, #ff6a00, #ff3d00); */
        /* background-color: #71dd37; */
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
    }

    .role-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .role-modal .btn-close {
        /* filter: brightness(0) invert(1); */
        opacity: 1;
    }

    .role-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .role-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .role-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .role-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(135px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .permission-check {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px;
        color: #334155;
        font-weight: 700;
        background: #f8fafc;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: capitalize;
    }

    .permission-check input {
        width: 16px;
        height: 16px;
    }

    .role-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-role {
        background: #71dd37;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-role:hover {
        background: #e94d14;
        color: #fff;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }

        .permission-box {
            max-width: 260px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-lock"></i> Roles & Permissions
    </h4>

    <button class="btn btn-primary btn-add-role" onclick="openAddModal()">
        <i class="bx bx-plus"></i> Add Role
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="rolesTable"
                data-module="roles">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($roles)) {
                        $perms = json_decode($r['permissions'], true) ?? [];

                        $roleJson = htmlspecialchars(
                            json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $isActive = ($r['status'] ?? '') === 'active';
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="role-name">
                                    <?= htmlspecialchars($r['role_name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <div class="permission-box">
                                    <?php if (!empty($perms)): ?>
                                        <?php foreach ($perms as $perm): ?>
                                            <span class="permission-badge">
                                                <?= htmlspecialchars($perm) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No permission</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$r['id'] ?>&status=<?= $isActive ? 0 : 1 ?>"
                                    onclick="return confirm('Change role status?')"
                                    class="badge-soft <?= $isActive ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                </a>
                            </td>

                            <td class="text-center">
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
                                                onclick='openEditModal(<?= $roleJson ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$r['id'] ?>"
                                                onclick="return confirm('Delete role?')"
                                                class="dropdown-item text-danger">
                                                <i class="bx bx-trash"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- ROLE MODAL -->
<div class="modal fade role-modal" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="post" class="modal-content" id="roleForm">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <div class="mb-3">
                    <label class="form-label">Role Name</label>
                    <input class="form-control" name="role_name" id="role_name" placeholder="Example: Manager" required>
                </div>

                <label class="form-label">Permissions</label>

                <div class="permission-grid">
                    <?php foreach ($modules as $m): ?>
                        <?php if ($m === 'all' && !admin_is_super_admin()) {
                            continue;
                        } ?>
                        <label class="permission-check">
                            <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($m) ?>">
                            <?= htmlspecialchars($m === 'all' ? 'Full access (all modules)' : ucfirst(str_replace('_', ' ', $m))) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-save-role" name="save_role">
                    <i class="bx bx-save"></i> Save Role
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('roleForm').reset();

        document.getElementById('id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Role';

        new bootstrap.Modal(document.getElementById('roleModal')).show();
    }

    function openEditModal(r) {
        document.getElementById('roleForm').reset();

        document.getElementById('id').value = r.id || '';
        document.getElementById('role_name').value = r.role_name || '';

        let perms = [];

        try {
            perms = JSON.parse(r.permissions || "[]");
        } catch (e) {
            perms = [];
        }

        document.querySelectorAll("input[name='permissions[]']").forEach(function(c) {
            c.checked = perms.includes(c.value);
        });

        document.getElementById('modalTitle').innerText = 'Edit Role';

        new bootstrap.Modal(document.getElementById('roleModal')).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Role saved successfully!');}</script>";
    } elseif ($msg === 'status') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Role status updated successfully!');}</script>";
    } elseif ($msg === 'deleted') {
        echo "<script>if(typeof showToast==='function'){showToast('warning','Role deleted successfully!');}</script>";
    } else {
        echo "<script>if(typeof showToast==='function'){showToast('error','Something went wrong!');}</script>";
    }
}

ob_end_flush();
?>