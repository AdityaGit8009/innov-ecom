<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / EDIT ATTRIBUTE ===== */
if (isset($_POST['save_attribute'])) {
    $id   = (int)($_POST['attribute_id'] ?? 0);
    $name = clean($conn, $_POST['name'] ?? '');

    if ($name == '') {
        header("Location: attributes.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $q = mysqli_query($conn, "UPDATE attributes SET name='$name' WHERE id=$id");
    } else {
        $q = mysqli_query($conn, "INSERT INTO attributes(name) VALUES('$name')");
    }

    header("Location: attributes.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE ATTRIBUTE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM attributes WHERE id=$id");

    header("Location: attributes.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== FETCH ATTRIBUTES ===== */
$attributes = mysqli_query($conn, "SELECT * FROM attributes ORDER BY id DESC");

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

    .btn-add-attribute {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-attribute:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #attributeTable {
        width: 100% !important;
        font-size: 14px;
    }

    #attributeTable thead th {
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

    #attributeTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #attributeTable tbody tr:hover {
        background: #f8fafc;
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

    #attributeModalTitle {
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

        .btn-add-attribute {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-cog"></i> Product Attributes</h4>

    <button type="button" class="btn btn-add-attribute" onclick="openAttributeModal()">
        <i class="bx bx-plus"></i> Add Attribute
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="attributeTable"
                data-module="attributes" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Attribute Name</th>
                        <th>Created At</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($attributes)) {
                        $attrJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($row['name'] ?? '') ?></td>

                            <td>
                                <?= !empty($row['created_at'])
                                    ? date("d M Y, h:i A", strtotime($row['created_at']))
                                    : '—' ?>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openAttributeModal(<?= $attrJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteAttribute(<?= (int)$row['id'] ?>)">
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

<!-- ATTRIBUTE MODAL -->
<div class="modal fade" id="attributeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="attributeForm">
            <div class="modal-header">
                <h5 class="modal-title" id="attributeModalTitle">Add Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="attribute_id" id="attribute_id">

                <label class="form-label">Attribute Name</label>
                <input type="text"
                    name="name"
                    id="attribute_name"
                    class="form-control"
                    placeholder="Enter attribute name"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" name="save_attribute" class="btn btn-success">
                    <i class="bx bx-save"></i> Save Attribute
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openAttributeModal(attr = null) {
        document.getElementById('attributeForm').reset();

        if (attr) {
            document.getElementById('attributeModalTitle').innerText = 'Edit Attribute';
            document.getElementById('attribute_id').value = attr.id || '';
            document.getElementById('attribute_name').value = attr.name || '';
        } else {
            document.getElementById('attributeModalTitle').innerText = 'Add Attribute';
            document.getElementById('attribute_id').value = '';
            document.getElementById('attribute_name').value = '';
        }

        new bootstrap.Modal(document.getElementById('attributeModal')).show();
    }

    function deleteAttribute(id) {
        Swal.fire({
            title: 'Delete attribute?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'attributes.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Attribute saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Attribute deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>