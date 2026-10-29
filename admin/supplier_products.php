<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD MAPPING ===== */
if (isset($_POST['add_map'])) {
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $product_id  = (int)($_POST['product_id'] ?? 0);
    $cost_price  = (float)($_POST['cost_price'] ?? 0);

    if ($supplier_id <= 0 || $product_id <= 0 || $cost_price < 0) {
        header("Location: supplier_products.php?msg=error");
        exit;
    }

    $q = mysqli_query(
        $conn,
        "INSERT INTO supplier_products(supplier_id, product_id, cost_price)
         VALUES($supplier_id, $product_id, '$cost_price')"
    );

    header("Location: supplier_products.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE MAPPING ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM supplier_products WHERE id=$id");

    header("Location: supplier_products.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== FETCH MAPPING DATA ===== */
$data = mysqli_query($conn, "
    SELECT sp.*, s.name AS supplier, p.title AS product
    FROM supplier_products sp
    LEFT JOIN suppliers s ON s.id = sp.supplier_id
    LEFT JOIN products p ON p.id = sp.product_id
    ORDER BY sp.id DESC
");

/* ===== FETCH SUPPLIERS / PRODUCTS ===== */
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers WHERE status=1 ORDER BY name ASC");
$products  = mysqli_query($conn, "SELECT * FROM products ORDER BY title ASC");

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

    .btn-add-map {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-map:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #supplierProductTable {
        width: 100% !important;
        font-size: 14px;
    }

    #supplierProductTable thead th {
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

    #supplierProductTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #supplierProductTable tbody tr:hover {
        background: #f8fafc;
    }

    .cost-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
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

    .modal-header h5 {
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

        .btn-add-map {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-transfer-alt"></i> Supplier Products</h4>

    <button type="button" class="btn btn-add-map" onclick="openMapModal()">
        <i class="bx bx-plus"></i> Add Mapping
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="supplierProductTable"
                data-module="supplier product mappings" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Supplier</th>
                        <th>Product</th>
                        <th>Cost Price</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($data)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($r['supplier'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($r['product'] ?? '—') ?></td>

                            <td class="text-center">
                                <span class="cost-badge">
                                    ₹<?= format_price((float)($r['cost_price'] ?? 0)) ?>
                                </span>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteMapping(<?= (int)$r['id'] ?>)">
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

<!-- SUPPLIER PRODUCT MODAL -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="mapForm">
            <div class="modal-header">
                <h5>Add Supplier Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select mb-3" required>
                    <option value="">Select Supplier</option>
                    <?php while ($s = mysqli_fetch_assoc($suppliers)) { ?>
                        <option value="<?= (int)$s['id'] ?>">
                            <?= htmlspecialchars($s['name'] ?? '') ?>
                        </option>
                    <?php } ?>
                </select>

                <label class="form-label">Product</label>
                <select name="product_id" class="form-select mb-3" required>
                    <option value="">Select Product</option>
                    <?php while ($p = mysqli_fetch_assoc($products)) { ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= htmlspecialchars($p['title'] ?? '') ?>
                        </option>
                    <?php } ?>
                </select>

                <label class="form-label">Cost Price</label>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="cost_price"
                    class="form-control"
                    placeholder="Enter cost price"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="add_map">
                    <i class="bx bx-save"></i> Save Mapping
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openMapModal() {
        document.getElementById('mapForm').reset();
        new bootstrap.Modal(document.getElementById('mapModal')).show();
    }

    function deleteMapping(id) {
        Swal.fire({
            title: 'Delete mapping?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'supplier_products.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Supplier product mapping saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Supplier product mapping deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>