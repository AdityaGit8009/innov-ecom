<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/*
| Registered customer source of truth:
| users.id = session/customer id used in cart, wishlist, addresses and orders.
| login.login_id / login.mobile stores password + active/inactive status.
*/

/* ================= SAVE CUSTOMER ================= */
if (isset($_POST['save_customer'])) {
    $id      = (int)($_POST['customer_id'] ?? 0);
    $name    = admin_safe($conn, $_POST['name'] ?? '');
    $email   = admin_safe($conn, $_POST['email'] ?? '');
    $mobile  = admin_safe($conn, $_POST['mobile'] ?? '');
    $country = admin_safe($conn, $_POST['country'] ?? '');
    $address = admin_safe($conn, $_POST['address'] ?? '');

    if ($id > 0) {
        $old = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT email, mobile 
            FROM users 
            WHERE id = '$id' 
            LIMIT 1
        "));

        $oldEmail = admin_safe($conn, $old['email'] ?? '');
        $oldMobile = admin_safe($conn, $old['mobile'] ?? '');

        $res = mysqli_query($conn, "
            UPDATE users SET
                name = '$name',
                email = '$email',
                mobile = '$mobile',
                country = '$country',
                address = '$address'
            WHERE id = '$id'
        ");

        mysqli_query($conn, "
            UPDATE login SET 
                login_id = '$email',
                mobile = '$mobile'
            WHERE role = 'user' 
            AND (login_id = '$oldEmail' OR mobile = '$oldMobile')
        ");
    } else {
        $password = password_hash($mobile ?: '123456', PASSWORD_DEFAULT);
        $pass_safe = admin_safe($conn, $password);

        $res = mysqli_query($conn, "
            INSERT INTO users (name, email, mobile, country, address)
            VALUES ('$name', '$email', '$mobile', '$country', '$address')
        ");

        if ($res) {
            mysqli_query($conn, "
                INSERT INTO login (login_id, password, mobile, role, status)
                VALUES ('$email', '$pass_safe', '$mobile', 'user', 1)
            ");
        }
    }

    header("Location: customers.php?msg=" . (!empty($res) ? 'saved' : 'error'));
    exit;
}

/* ================= ACTIVE / INACTIVE CUSTOMER ================= */
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'] === 1 ? 1 : 0;

    $u = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT email, mobile 
        FROM users 
        WHERE id = '$id' 
        LIMIT 1
    "));

    if ($u) {
        $email = admin_safe($conn, $u['email']);
        $mobile = admin_safe($conn, $u['mobile']);

        mysqli_query($conn, "
            UPDATE login 
            SET status = '$status' 
            WHERE role = 'user' 
            AND (login_id = '$email' OR mobile = '$mobile')
        ");
    }

    header("Location: customers.php?msg=status");
    exit;
}

/* ================= SOFT DELETE CUSTOMER ================= */
/* Delete ke jagah inactive kiya gaya hai, order history safe rahegi. */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $u = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT email, mobile 
        FROM users 
        WHERE id = '$id' 
        LIMIT 1
    "));

    if ($u) {
        $email = admin_safe($conn, $u['email']);
        $mobile = admin_safe($conn, $u['mobile']);

        mysqli_query($conn, "
            UPDATE login 
            SET status = '0' 
            WHERE role = 'user' 
            AND (login_id = '$email' OR mobile = '$mobile')
        ");
    }

    header("Location: customers.php?msg=inactive");
    exit;
}

/* ================= FETCH CUSTOMERS ================= */
$customers = mysqli_query($conn, "
    SELECT 
        u.*,
        COALESCE(l.status, 1) AS login_status,
        (
            SELECT COUNT(*) 
            FROM orders o 
            WHERE o.user_id = u.id
        ) AS total_orders
    FROM users u
    LEFT JOIN login l 
        ON l.role = 'user' 
        AND (l.login_id = u.email OR l.mobile = u.mobile)
    ORDER BY u.id DESC
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

    #customersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #customersTable thead th {
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

    #customersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #customersTable tbody tr:hover {
        background: #f8fafc;
    }

    .customer-name {
        font-weight: 800;
        color: #334155;
    }

    .customer-contact {
        font-size: 12px;
        color: #64748b;
        display: block;
        margin-top: 3px;
    }

    .address-text {
        max-width: 260px;
        white-space: normal;
        line-height: 1.5;
        color: #64748b;
    }

    .badge-soft {
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
        text-decoration: none;
    }

    .badge-soft-info {
        background: #e0f2fe;
        color: #075985;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-add-customer {
        border-radius: 10px;
        font-weight: 700;
        padding: 8px 14px;
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

    .action-dropdown .dropdown-menu {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
        padding: 8px;
        min-width: 175px;
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

    .customer-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .customer-modal .modal-header {
        background: linear-gradient(135deg, #ff6a00, #ff3d00);
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    .customer-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .customer-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .customer-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .customer-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .customer-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .customer-modal textarea.form-control {
        min-height: 90px;
        resize: none;
    }

    .customer-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .customer-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-customer {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-customer:hover {
        background: #e94d14;
        color: #fff;
    }

    .small-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 8px;
        display: block;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }

        .address-text {
            max-width: 220px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-user"></i> Registered Customers
    </h4>

    <button class="btn btn-primary btn-add-customer" onclick="openAddModal()">
        <i class="bx bx-plus"></i> Add Customer
    </button>
</div>

<div class="info-alert">
    Account block/unblock yahin se manage hoga. Block karne par customer login nahi kar payega, lekin order history safe rahegi.
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="customersTable"
                data-module="customers">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email / Mobile</th>
                        <th>Address</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($customers)) {
                        $fullAddress = trim(($row['country'] ?? '') . ' ' . ($row['address'] ?? ''));
                        $statusText = (int)$row['login_status'] ? 'Unblocked' : 'Blocked';
                        $nextStatus = (int)$row['login_status'] ? 0 : 1;
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="customer-name">
                                    <?= htmlspecialchars($row['name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['email'] ?? '') ?>
                                <span class="customer-contact">
                                    <?= htmlspecialchars($row['mobile'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <div class="address-text">
                                    <?= htmlspecialchars($fullAddress ?: 'N/A') ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft badge-soft-info">
                                    <?= (int)$row['total_orders'] ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$row['id'] ?>&status=<?= $nextStatus ?>"
                                    onclick="return confirm('Change customer login status?')"
                                    class="badge-soft <?= (int)$row['login_status'] ? 'badge-soft-success' : 'badge-soft-danger' ?>">
                                    <?= $statusText ?>
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
                                                onclick='openEditModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$row['id'] ?>"
                                                onclick="return confirm('Customer account block karna hai?')"
                                                class="dropdown-item text-danger">
                                                <i class="bx bx-user-x"></i> Block
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

<!-- CUSTOMER MODAL -->
<div class="modal fade customer-modal" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="customer_id" id="customer_id">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="name" id="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" name="email" id="email" type="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input class="form-control" name="mobile" id="mobile" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <input class="form-control" name="country" id="country">
                </div>

                <div class="mb-0">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="address"></textarea>
                </div>

                <small class="small-help">
                    New customer ke liye default password mobile number rahega. Customer baad me password change kar sakta hai.
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-save-customer" name="save_customer">
                    <i class="bx bx-save"></i> Save Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.querySelector('#customerModal form').reset();

        document.getElementById('customer_id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Customer';

        new bootstrap.Modal(document.getElementById('customerModal')).show();
    }

    function openEditModal(c) {
        document.getElementById('modalTitle').innerText = 'Edit Customer';

        document.getElementById('customer_id').value = c.id || '';
        document.getElementById('name').value = c.name || '';
        document.getElementById('email').value = c.email || '';
        document.getElementById('mobile').value = c.mobile || '';
        document.getElementById('country').value = c.country || '';
        document.getElementById('address').value = c.address || '';

        new bootstrap.Modal(document.getElementById('customerModal')).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'saved') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Customer saved successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Customer saved successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
    } elseif ($msg === 'status') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Customer status updated successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Customer status updated successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
    } elseif ($msg === 'inactive') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Customer account inactive successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Customer account inactive successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
    } else {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('error', 'Something went wrong!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
    }
}
ob_end_flush();
?>