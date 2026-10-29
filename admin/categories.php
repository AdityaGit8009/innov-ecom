<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_category'])) {
    $id   = (int)($_POST['cat_id'] ?? 0);
    $name = clean($conn, $_POST['name'] ?? '');

    if ($name == '') {
        header("Location: categories.php?msg=error");
        exit;
    }

    if ($id == 0) {
        $q = mysqli_query($conn, "INSERT INTO categories (name, status) VALUES ('$name', 1)");
    } else {
        $q = mysqli_query($conn, "UPDATE categories SET name='$name' WHERE id=$id");
    }

    header("Location: categories.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $q = mysqli_query($conn, "DELETE FROM categories WHERE id=$id");

    header("Location: categories.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    mysqli_query($conn, "UPDATE categories SET status=$status WHERE id=$id");

    header("Location: categories.php?msg=status");
    exit;
}

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");

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

    .btn-add-category {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-category:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #categoryTable {
        width: 100% !important;
        font-size: 14px;
    }

    #categoryTable thead th {
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

    #categoryTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #categoryTable tbody tr:hover {
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

    #catTitle {
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

        .btn-add-category {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-category"></i> Categories</h4>

    <button type="button" class="btn btn-add-category" onclick="openAddCat()">
        <i class="bx bx-plus"></i> Add Category
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="categoryTable"
                data-module="categories" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($c = mysqli_fetch_assoc($cats)) {
                        $catJson = htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($c['name'] ?? '') ?></td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$c['id'] ?>&status=<?= !empty($c['status']) ? 0 : 1 ?>"
                                    class="<?= !empty($c['status']) ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                    <?= !empty($c['status']) ? 'Active' : 'Inactive' ?>
                                </a>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    onclick='openEditCat(<?= $catJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <a href="?id=<?= (int)$c['id'] ?>&status=<?= !empty($c['status']) ? 0 : 1 ?>"
                                    class="btn btn-info btn-sm">
                                    <i class="bx <?= !empty($c['status']) ? 'bx-hide' : 'bx-show' ?>"></i>
                                    <?= !empty($c['status']) ? 'Inactive' : 'Active' ?>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteCategory(<?= (int)$c['id'] ?>)">
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

<!-- CATEGORY MODAL -->
<div class="modal fade" id="catModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="categoryForm">
            <div class="modal-header">
                <h5 id="catTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="cat_id" id="edit_cat_id">

                <label class="form-label">Category Name</label>
                <input class="form-control"
                    name="name"
                    id="edit_cat_name"
                    placeholder="Enter category name"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="save_category">
                    <i class="bx bx-save"></i> Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openAddCat() {
        document.getElementById('categoryForm').reset();
        document.getElementById('edit_cat_id').value = "";
        document.getElementById('edit_cat_name').value = "";
        document.getElementById('catTitle').innerText = "Add Category";

        new bootstrap.Modal(document.getElementById('catModal')).show();
    }

    function openEditCat(d) {
        document.getElementById('edit_cat_id').value = d.id || "";
        document.getElementById('edit_cat_name').value = d.name || "";
        document.getElementById('catTitle').innerText = "Edit Category";

        new bootstrap.Modal(document.getElementById('catModal')).show();
    }

    function deleteCategory(id) {
        Swal.fire({
            title: 'Delete category?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'categories.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Category saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Category deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Category status updated!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>