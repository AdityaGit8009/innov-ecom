<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_sub'])) {
    $id   = (int)($_POST['sub_id'] ?? 0);
    $cat  = (int)($_POST['category_id'] ?? 0);
    $name = clean($conn, $_POST['name'] ?? '');

    if ($cat == 0 || $name == '') {
        header("Location: sub_categories.php?msg=error");
        exit;
    }

    if ($id == 0) {
        $q = mysqli_query(
            $conn,
            "INSERT INTO sub_categories (category_id, name, status)
             VALUES ($cat, '$name', 1)"
        );
    } else {
        $q = mysqli_query(
            $conn,
            "UPDATE sub_categories
             SET category_id=$cat, name='$name'
             WHERE id=$id"
        );
    }

    header("Location: sub_categories.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $q = mysqli_query($conn, "DELETE FROM sub_categories WHERE id=$id");

    header("Location: sub_categories.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    mysqli_query($conn, "UPDATE sub_categories SET status=$status WHERE id=$id");

    header("Location: sub_categories.php?msg=status");
    exit;
}

/* ===== FETCH SUB CATEGORIES ===== */
$subs = mysqli_query($conn, "
    SELECT s.*, c.name AS cat_name
    FROM sub_categories s
    LEFT JOIN categories c ON c.id = s.category_id
    ORDER BY s.id DESC
");

/* ===== FETCH ACTIVE CATEGORIES ===== */
$cats = mysqli_query($conn, "SELECT * FROM categories WHERE status=1 ORDER BY name ASC");

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

    .btn-add-subcategory {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-subcategory:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #subCategoryTable {
        width: 100% !important;
        font-size: 14px;
    }

    #subCategoryTable thead th {
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

    #subCategoryTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #subCategoryTable tbody tr:hover {
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

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .form-control,
    .form-select {
        border-radius: 9px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus,
    .form-select:focus {
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

    #subTitle {
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

        .btn-add-subcategory {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-list-ul"></i> Sub Categories</h4>

    <button type="button" class="btn btn-add-subcategory" onclick="openAddSub()">
        <i class="bx bx-plus"></i> Add Sub Category
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="subCategoryTable"
                data-module="sub categories" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Status</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($s = mysqli_fetch_assoc($subs)) {
                        $subJson = htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($s['cat_name'] ?? '') ?></td>

                            <td><?= htmlspecialchars($s['name'] ?? '') ?></td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$s['id'] ?>&status=<?= !empty($s['status']) ? 0 : 1 ?>"
                                    class="<?= !empty($s['status']) ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                    <?= !empty($s['status']) ? 'Active' : 'Inactive' ?>
                                </a>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    onclick='openEditSub(<?= $subJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <a href="?id=<?= (int)$s['id'] ?>&status=<?= !empty($s['status']) ? 0 : 1 ?>"
                                    class="btn btn-info btn-sm">
                                    <i class="bx <?= !empty($s['status']) ? 'bx-hide' : 'bx-show' ?>"></i>
                                    <?= !empty($s['status']) ? 'Inactive' : 'Active' ?>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteSubCategory(<?= (int)$s['id'] ?>)">
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

<!-- SUB CATEGORY MODAL -->
<div class="modal fade" id="subModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="subCategoryForm">
            <div class="modal-header">
                <h5 id="subTitle">Add Sub Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="sub_id" id="edit_sub_id">

                <label class="form-label">Category</label>
                <select class="form-select mb-3" name="category_id" id="edit_category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    mysqli_data_seek($cats, 0);
                    while ($c = mysqli_fetch_assoc($cats)) {
                    ?>
                        <option value="<?= (int)$c['id'] ?>">
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php } ?>
                </select>

                <label class="form-label">Sub Category Name</label>
                <input class="form-control"
                    name="name"
                    id="edit_sub_name"
                    placeholder="Enter sub category name"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="save_sub">
                    <i class="bx bx-save"></i> Save Sub Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openAddSub() {
        document.getElementById('subCategoryForm').reset();
        document.getElementById('edit_sub_id').value = "";
        document.getElementById('edit_sub_name').value = "";
        document.getElementById('edit_category_id').value = "";
        document.getElementById('subTitle').innerText = "Add Sub Category";

        new bootstrap.Modal(document.getElementById('subModal')).show();
    }

    function openEditSub(d) {
        document.getElementById('edit_sub_id').value = d.id || "";
        document.getElementById('edit_sub_name').value = d.name || "";
        document.getElementById('edit_category_id').value = d.category_id || "";
        document.getElementById('subTitle').innerText = "Edit Sub Category";

        new bootstrap.Modal(document.getElementById('subModal')).show();
    }

    function deleteSubCategory(id) {
        Swal.fire({
            title: 'Delete sub category?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'sub_categories.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Sub category saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Sub category deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Sub category status updated!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>