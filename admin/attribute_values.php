<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / EDIT VALUE ===== */
if (isset($_POST['save_value'])) {
    $id           = (int)($_POST['value_id'] ?? 0);
    $attribute_id = (int)($_POST['attribute_id'] ?? 0);
    $value        = clean($conn, $_POST['value'] ?? '');

    if ($attribute_id <= 0 || $value == '') {
        header("Location: attribute_values.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $q = mysqli_query(
            $conn,
            "UPDATE attribute_values 
             SET attribute_id=$attribute_id, value='$value' 
             WHERE id=$id"
        );
    } else {
        $q = mysqli_query(
            $conn,
            "INSERT INTO attribute_values(attribute_id, value) 
             VALUES($attribute_id, '$value')"
        );
    }

    header("Location: attribute_values.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE VALUE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM attribute_values WHERE id=$id");

    header("Location: attribute_values.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== FETCH VALUES ===== */
$values = mysqli_query($conn, "
    SELECT v.*, a.name AS attribute_name
    FROM attribute_values v
    LEFT JOIN attributes a ON a.id = v.attribute_id
    ORDER BY v.id DESC
");

/* ===== FETCH ATTRIBUTES ===== */
$attributes = mysqli_query($conn, "SELECT * FROM attributes ORDER BY name ASC");

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

    .btn-add-value {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-value:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #attributeValueTable {
        width: 100% !important;
        font-size: 14px;
    }

    #attributeValueTable thead th {
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

    #attributeValueTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #attributeValueTable tbody tr:hover {
        background: #f8fafc;
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

    #valueModalTitle {
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

        .btn-add-value {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-detail"></i> Attribute Values</h4>

    <button type="button" class="btn btn-add-value" onclick="openValueModal()">
        <i class="bx bx-plus"></i> Add Value
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="attributeValueTable"
                data-module="attribute values" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Attribute</th>
                        <th>Value</th>
                        <th>Created At</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($values)) {
                        $valueJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($row['attribute_name'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($row['value'] ?? '') ?></td>

                            <td>
                                <?= !empty($row['created_at'])
                                    ? date("d M Y, h:i A", strtotime($row['created_at']))
                                    : '—' ?>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openValueModal(<?= $valueJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteValue(<?= (int)$row['id'] ?>)">
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

<!-- ATTRIBUTE VALUE MODAL -->
<div class="modal fade" id="valueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="valueForm">
            <div class="modal-header">
                <h5 class="modal-title" id="valueModalTitle">Add Value</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="value_id" id="value_id">

                <label class="form-label">Attribute</label>
                <select name="attribute_id" id="value_attribute_id" class="form-select mb-3" required>
                    <option value="">Select Attribute</option>
                    <?php
                    mysqli_data_seek($attributes, 0);
                    while ($attr = mysqli_fetch_assoc($attributes)) {
                    ?>
                        <option value="<?= (int)$attr['id'] ?>">
                            <?= htmlspecialchars($attr['name'] ?? '') ?>
                        </option>
                    <?php } ?>
                </select>

                <label class="form-label">Value</label>
                <input type="text"
                    name="value"
                    id="value_name"
                    class="form-control"
                    placeholder="Enter value"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" name="save_value" class="btn btn-success">
                    <i class="bx bx-save"></i> Save Value
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openValueModal(val = null) {
        document.getElementById('valueForm').reset();

        if (val) {
            document.getElementById('valueModalTitle').innerText = 'Edit Value';
            document.getElementById('value_id').value = val.id || '';
            document.getElementById('value_attribute_id').value = val.attribute_id || '';
            document.getElementById('value_name').value = val.value || '';
        } else {
            document.getElementById('valueModalTitle').innerText = 'Add Value';
            document.getElementById('value_id').value = '';
            document.getElementById('value_attribute_id').value = '';
            document.getElementById('value_name').value = '';
        }

        new bootstrap.Modal(document.getElementById('valueModal')).show();
    }

    function deleteValue(id) {
        Swal.fire({
            title: 'Delete value?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'attribute_values.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Attribute value saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Attribute value deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>