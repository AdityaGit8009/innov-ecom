<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_supplier'])) {
    $id      = (int)($_POST['id'] ?? 0);
    $name    = clean($conn, $_POST['name'] ?? '');
    $email   = clean($conn, $_POST['email'] ?? '');
    $phone   = clean($conn, $_POST['phone'] ?? '');
    $address = clean($conn, $_POST['address'] ?? '');

    if ($name == '') {
        header("Location: suppliers.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $q = mysqli_query(
            $conn,
            "UPDATE suppliers SET 
                name='$name',
                email='$email',
                phone='$phone',
                address='$address'
             WHERE id=$id"
        );
    } else {
        $q = mysqli_query(
            $conn,
            "INSERT INTO suppliers(name, email, phone, address, status)
             VALUES('$name', '$email', '$phone', '$address', 1)"
        );
    }

    header("Location: suppliers.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== TOGGLE STATUS ===== */
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];

    $res = mysqli_query($conn, "SELECT status FROM suppliers WHERE id=$id");
    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        header("Location: suppliers.php?msg=error");
        exit;
    }

    $newStatus = !empty($row['status']) ? 0 : 1;

    $q = mysqli_query($conn, "UPDATE suppliers SET status=$newStatus WHERE id=$id");

    header("Location: suppliers.php?msg=" . ($q ? "status" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM suppliers WHERE id=$id");

    header("Location: suppliers.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== FETCH SUPPLIERS ===== */
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");

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

    .btn-add-supplier {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-supplier:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #supplierTable {
        width: 100% !important;
        font-size: 14px;
    }

    #supplierTable thead th {
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

    #supplierTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #supplierTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .form-control {
        border-radius: 9px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
    }

    .modal-content {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    #modalTitle {
        font-weight: 800;
        color: #334155;
        margin: 0;
    }

    .modal-body {
        padding: 22px;
    }

    .modal-footer {
        border-top: 1px solid #e5e7eb;
        padding: 16px 22px;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .btn-add-supplier {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-store-alt"></i> Suppliers / Vendors</h4>

    <button type="button" class="btn btn-add-supplier" onclick="openSupplierModal()">
        <i class="bx bx-plus"></i> Add Supplier
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="supplierTable"
                data-module="suppliers" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($s = mysqli_fetch_assoc($suppliers)) {
                        $supplierJson = htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($s['name'] ?? '') ?></td>

                            <td><?= htmlspecialchars($s['email'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>

                            <td class="text-center">
                                <a href="?toggle_status=<?= (int)$s['id'] ?>" style="text-decoration:none">
                                    <?php if (!empty($s['status'])) { ?>
                                        <span class="badge-soft-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge-soft-danger">Inactive</span>
                                    <?php } ?>
                                </a>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='editSupplier(<?= $supplierJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <a href="?toggle_status=<?= (int)$s['id'] ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="bx <?= !empty($s['status']) ? 'bx-hide' : 'bx-show' ?>"></i>
                                    <?= !empty($s['status']) ? 'Inactive' : 'Active' ?>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteSupplier(<?= (int)$s['id'] ?>)">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- SUPPLIER MODAL -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="supplierForm">
            <div class="modal-header">
                <h5 id="modalTitle">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="supplier_id">

                <label class="form-label">Supplier Name</label>
                <input class="form-control mb-3"
                    name="name"
                    id="name"
                    placeholder="Supplier Name"
                    required>

                <label class="form-label">Email</label>
                <input class="form-control mb-3"
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email">

                <label class="form-label">Phone</label>
                <input class="form-control mb-3"
                    name="phone"
                    id="phone"
                    placeholder="Phone">

                <label class="form-label">Address</label>
                <textarea class="form-control"
                    name="address"
                    id="address"
                    rows="3"
                    placeholder="Address"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="save_supplier">
                    <i class="bx bx-save"></i> Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openSupplierModal() {
        document.getElementById('supplierForm').reset();
        document.getElementById('modalTitle').innerText = "Add Supplier";
        document.getElementById('supplier_id').value = '';

        new bootstrap.Modal(document.getElementById('supplierModal')).show();
    }

    function editSupplier(s) {
        document.getElementById('modalTitle').innerText = "Edit Supplier";

        document.getElementById('supplier_id').value = s.id || '';
        document.getElementById('name').value = s.name || '';
        document.getElementById('email').value = s.email || '';
        document.getElementById('phone').value = s.phone || '';
        document.getElementById('address').value = s.address || '';

        new bootstrap.Modal(document.getElementById('supplierModal')).show();
    }

    function deleteSupplier(id) {
        Swal.fire({
            title: 'Delete supplier?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'suppliers.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Supplier saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Supplier deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Supplier status updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>