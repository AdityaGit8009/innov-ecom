<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== ADD / EDIT VARIANT ===== */
if (isset($_POST['save_variant'])) {
    $id           = (int)($_POST['variant_id'] ?? 0);
    $product_id   = (int)($_POST['product_id'] ?? 0);
    $variant_name = clean($conn, $_POST['variant_name'] ?? '');
    $stock        = (int)($_POST['stock'] ?? 0);
    $price        = (float)($_POST['price'] ?? 0);

    if ($product_id <= 0 || $variant_name == '' || $stock < 0 || $price < 0) {
        header("Location: variants.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $q = mysqli_query(
            $conn,
            "UPDATE variants 
             SET product_id=$product_id,
                 variant_name='$variant_name',
                 stock=$stock,
                 price=$price
             WHERE id=$id"
        );
    } else {
        $q = mysqli_query(
            $conn,
            "INSERT INTO variants(product_id, variant_name, stock, price)
             VALUES($product_id, '$variant_name', $stock, $price)"
        );
    }

    header("Location: variants.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== DELETE VARIANT ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn, "DELETE FROM variants WHERE id=$id");

    header("Location: variants.php?msg=" . ($q ? "deleted" : "error"));
    exit;
}

/* ===== FETCH VARIANTS ===== */
$variants = mysqli_query($conn, "
    SELECT v.*, p.title AS product_name
    FROM variants v
    LEFT JOIN products p ON p.id = v.product_id
    ORDER BY v.id DESC
");

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY title ASC");

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

    .btn-add-variant {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-variant:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #variantTable {
        width: 100% !important;
        font-size: 14px;
    }

    #variantTable thead th {
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

    #variantTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #variantTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-stock-in {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-stock-low {
        background: #fef3c7;
        color: #92400e;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-stock-out {
        background: #fee2e2;
        color: #991b1b;
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

    #variantModalTitle {
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

        .btn-add-variant {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-git-branch"></i> Product Variants</h4>

    <button type="button" class="btn btn-add-variant" onclick="openVariantModal()">
        <i class="bx bx-plus"></i> Add Variant
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="variantTable"
                data-module="variants" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Variant Name</th>
                        <th>Stock</th>
                        <th>Stock Status</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($variants)) {
                        $variantJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                        if ((int)$row['stock'] == 0) {
                            $stockStatus = "<span class='badge-stock-out'>Out</span>";
                        } elseif ((int)$row['stock'] <= 5) {
                            $stockStatus = "<span class='badge-stock-low'>Low</span>";
                        } else {
                            $stockStatus = "<span class='badge-stock-in'>In</span>";
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($row['product_name'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($row['variant_name'] ?? '') ?></td>

                            <td class="text-center">
                                <strong><?= (int)$row['stock'] ?></strong>
                            </td>

                            <td class="text-center">
                                <?= $stockStatus ?>
                            </td>

                            <td>₹<?= format_price((float)$row['price']) ?></td>

                            <td>
                                <?= !empty($row['created_at'])
                                    ? date("d M Y, h:i A", strtotime($row['created_at']))
                                    : '—' ?>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openVariantModal(<?= $variantJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteVariant(<?= (int)$row['id'] ?>)">
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

<!-- VARIANT MODAL -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="variantForm">
            <div class="modal-header">
                <h5 class="modal-title" id="variantModalTitle">Add Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="variant_id" id="variant_id">

                <label class="form-label">Product</label>
                <select name="product_id" id="variant_product_id" class="form-select mb-3" required>
                    <option value="">Select Product</option>
                    <?php
                    mysqli_data_seek($products, 0);
                    while ($p = mysqli_fetch_assoc($products)) {
                    ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= htmlspecialchars($p['title'] ?? '') ?>
                        </option>
                    <?php } ?>
                </select>

                <label class="form-label">Variant Name</label>
                <input type="text"
                    name="variant_name"
                    id="variant_name"
                    class="form-control mb-3"
                    placeholder="Example: Red / XL / 128GB"
                    required>

                <label class="form-label">Stock</label>
                <input type="number"
                    name="stock"
                    id="variant_stock"
                    class="form-control mb-3"
                    min="0"
                    required>

                <label class="form-label">Price</label>
                <input type="number"
                    step="0.01"
                    name="price"
                    id="variant_price"
                    class="form-control"
                    min="0"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" name="save_variant" class="btn btn-success">
                    <i class="bx bx-save"></i> Save Variant
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openVariantModal(v = null) {
        document.getElementById('variantForm').reset();

        if (v) {
            document.getElementById('variantModalTitle').innerText = 'Edit Variant';
            document.getElementById('variant_id').value = v.id || '';
            document.getElementById('variant_product_id').value = v.product_id || '';
            document.getElementById('variant_name').value = v.variant_name || '';
            document.getElementById('variant_stock').value = v.stock || 0;
            document.getElementById('variant_price').value = v.price || 0;
        } else {
            document.getElementById('variantModalTitle').innerText = 'Add Variant';
            document.getElementById('variant_id').value = '';
            document.getElementById('variant_product_id').value = '';
            document.getElementById('variant_name').value = '';
            document.getElementById('variant_stock').value = '';
            document.getElementById('variant_price').value = '';
        }

        new bootstrap.Modal(document.getElementById('variantModal')).show();
    }

    function deleteVariant(id) {
        Swal.fire({
            title: 'Delete variant?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'variants.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Variant saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Variant deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>