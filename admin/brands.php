<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== DELETE BRAND ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM brands WHERE id=$id");

    header("Location: brands.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== TOGGLE STATUS ===== */
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    $brandQuery = mysqli_query($conn, "SELECT status FROM brands WHERE id=$id");
    $brand = mysqli_fetch_assoc($brandQuery);

    if (!$brand) {
        header("Location: brands.php?msg=error");
        exit;
    }

    $newStatus = !empty($brand['status']) ? 0 : 1;

    $q = mysqli_query($conn, "UPDATE brands SET status=$newStatus WHERE id=$id");

    header("Location: brands.php?msg=" . ($q ? "status" : "error"));
    exit;
}

/* ===== ADD / EDIT BRAND ===== */
if (isset($_POST['save_brand'])) {
    $id   = (int)($_POST['brand_id'] ?? 0);
    $name = clean($conn, $_POST['name'] ?? '');

    if ($name == '') {
        header("Location: brands.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $q = mysqli_query($conn, "UPDATE brands SET name='$name' WHERE id=$id");
    } else {
        $q = mysqli_query($conn, "INSERT INTO brands(name, status) VALUES('$name', 1)");
    }

    header("Location: brands.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== FETCH BRANDS ===== */
$brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY id DESC");

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

    .btn-add-brand {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-brand:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #brandTable {
        width: 100% !important;
        font-size: 14px;
    }

    #brandTable thead th {
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

    #brandTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #brandTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
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

    #brandModalTitle {
        font-weight: 800;
        color: #334155;
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

        .btn-add-brand {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-purchase-tag-alt"></i> All Brands</h4>

    <button type="button" class="btn btn-add-brand" onclick="openBrandModal()">
        <i class="bx bx-plus"></i> Add Brand
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="brandTable"
                data-module="brands" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Brand Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($b = mysqli_fetch_assoc($brands)) {
                        $brandJson = htmlspecialchars(json_encode($b), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($b['name'] ?? '') ?></td>

                            <td class="text-center">
                                <?php if (!empty($b['status'])) { ?>
                                    <span class="badge-soft-success">Active</span>
                                <?php } else { ?>
                                    <span class="badge-soft-danger">Inactive</span>
                                <?php } ?>
                            </td>

                            <td>
                                <?= !empty($b['created_at'])
                                    ? date("d M Y, h:i A", strtotime($b['created_at']))
                                    : '—' ?>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openBrandModal(<?= $brandJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <a href="brands.php?toggle=<?= (int)$b['id'] ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="bx bx-toggle-left"></i>
                                    <?= !empty($b['status']) ? 'Inactive' : 'Active' ?>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteBrand(<?= (int)$b['id'] ?>)">
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

<!-- BRAND MODAL -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="brandForm">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalTitle">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="brand_id" id="brand_id">

                <label class="form-label">Brand Name</label>
                <input type="text"
                    name="name"
                    id="brand_name"
                    class="form-control"
                    placeholder="Enter brand name"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" name="save_brand" class="btn btn-success">
                    <i class="bx bx-save"></i> Save Brand
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openBrandModal(brand = null) {
        document.getElementById('brandForm').reset();

        if (brand) {
            document.getElementById('brandModalTitle').innerText = 'Edit Brand';
            document.getElementById('brand_id').value = brand.id || '';
            document.getElementById('brand_name').value = brand.name || '';
        } else {
            document.getElementById('brandModalTitle').innerText = 'Add Brand';
            document.getElementById('brand_id').value = '';
            document.getElementById('brand_name').value = '';
        }

        new bootstrap.Modal(document.getElementById('brandModal')).show();
    }

    function deleteBrand(id) {
        Swal.fire({
            title: 'Delete brand?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'brands.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Brand saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Brand deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Brand status updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>