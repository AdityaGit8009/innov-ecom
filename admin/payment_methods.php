<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_method'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = admin_safe($conn, $_POST['method_name'] ?? '');

    if ($name == '') {
        header("Location: payment_methods.php?msg=error");
        exit;
    }

    if ($id <= 0) {
        $res = mysqli_query($conn, "
            INSERT INTO payment_methods (method_name) 
            VALUES ('$name')
        ");
    } else {
        $res = mysqli_query($conn, "
            UPDATE payment_methods 
            SET method_name = '$name' 
            WHERE id = '$id'
        ");
    }

    header("Location: payment_methods.php?msg=" . ($res ? 'ok' : 'error'));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM payment_methods 
        WHERE id = '$id'
    ");

    header("Location: payment_methods.php?msg=deleted");
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'] === 1 ? 1 : 0;

    mysqli_query($conn, "
        UPDATE payment_methods 
        SET status = '$status' 
        WHERE id = '$id'
    ");

    header("Location: payment_methods.php?msg=status");
    exit;
}

/* ===== FETCH ===== */
$methods = mysqli_query($conn, "
    SELECT * 
    FROM payment_methods 
    ORDER BY id DESC
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

    #paymentMethodsTable {
        width: 100% !important;
        font-size: 14px;
    }

    #paymentMethodsTable thead th {
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

    #paymentMethodsTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #paymentMethodsTable tbody tr:hover {
        background: #f8fafc;
    }

    .method-name {
        font-weight: 800;
        color: #334155;
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

    .date-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
    }

    .btn-add-method {
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

    .payment-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .payment-modal .modal-header {
        background: linear-gradient(135deg, #ff6a00, #ff3d00);
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    .payment-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .payment-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .payment-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .payment-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .payment-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .payment-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .payment-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-method {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-method:hover {
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
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-wallet"></i> Payment Methods
    </h4>

    <button class="btn btn-primary btn-add-method" onclick="openAddModal()">
        <i class="bx bx-plus"></i> Add Method
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="paymentMethodsTable"
                data-module="payment methods">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($methods)) {
                        $methodJson = htmlspecialchars(
                            json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $status = (int)($row['status'] ?? 0);
                        $nextStatus = $status ? 0 : 1;
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="method-name">
                                    <?= htmlspecialchars($row['method_name'] ?? '') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$row['id'] ?>&status=<?= $nextStatus ?>"
                                    onclick="return confirm('Change payment method status?')"
                                    class="badge-soft <?= $status ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                    <?= $status ? 'Active' : 'Inactive' ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <span class="date-badge">
                                    <?= !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : 'N/A' ?>
                                </span>
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
                                                onclick='openEditModal(<?= $methodJson ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$row['id'] ?>"
                                                onclick="return confirm('Delete method?')"
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

<!-- MODAL -->
<div class="modal fade payment-modal" id="methodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="methodForm">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="id">

                <label class="form-label">Payment Method</label>
                <input
                    class="form-control"
                    name="method_name"
                    id="method_name"
                    placeholder="Example: COD, UPI, Razorpay"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-save-method" name="save_method">
                    <i class="bx bx-save"></i> Save Method
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById("methodForm").reset();
        document.getElementById("id").value = "";
        document.getElementById("modalTitle").innerText = "Add Payment Method";

        new bootstrap.Modal(document.getElementById("methodModal")).show();
    }

    function openEditModal(d) {
        document.getElementById("id").value = d.id || "";
        document.getElementById("method_name").value = d.method_name || "";
        document.getElementById("modalTitle").innerText = "Edit Payment Method";

        new bootstrap.Modal(document.getElementById("methodModal")).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Payment method saved successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({toast:true,position:'top-end',icon:'success',title:'Payment method saved successfully!',showConfirmButton:false,timer:2500});
            }
        </script>";
    } elseif ($msg === 'deleted') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('warning', 'Payment method deleted successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({toast:true,position:'top-end',icon:'warning',title:'Payment method deleted successfully!',showConfirmButton:false,timer:2500});
            }
        </script>";
    } elseif ($msg === 'status') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Payment method status updated!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({toast:true,position:'top-end',icon:'success',title:'Payment method status updated!',showConfirmButton:false,timer:2500});
            }
        </script>";
    } else {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('error', 'Something went wrong!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({toast:true,position:'top-end',icon:'error',title:'Something went wrong!',showConfirmButton:false,timer:2500});
            }
        </script>";
    }
}

ob_end_flush();
?>