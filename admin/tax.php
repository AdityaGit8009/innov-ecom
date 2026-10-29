<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== UPDATE TAX ===== */
if (isset($_POST['update_tax'])) {
    $pid = (int)($_POST['product_id'] ?? 0);
    $tax = (float)($_POST['tax'] ?? 0);

    if ($pid <= 0 || $tax < 0) {
        header("Location: tax.php?msg=error");
        exit;
    }

    $q = mysqli_query(
        $conn,
        "UPDATE products 
         SET tax_percentage='$tax',
             price = ROUND(COALESCE(NULLIF(base_price,0), price) + ((COALESCE(NULLIF(base_price,0), price) * COALESCE(default_tax_percentage,2)) / 100) + ((COALESCE(NULLIF(base_price,0), price) * '$tax') / 100), 2),
             old_price = CASE WHEN COALESCE(discount,0) > 0 THEN ROUND((COALESCE(NULLIF(base_price,0), price) + ((COALESCE(NULLIF(base_price,0), price) * COALESCE(default_tax_percentage,2)) / 100) + ((COALESCE(NULLIF(base_price,0), price) * '$tax') / 100)) + COALESCE(discount,0), 2) ELSE 0 END
         WHERE id=$pid"
    );

    header("Location: tax.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "
    SELECT id, title, base_price, default_tax_percentage, price, tax_percentage 
    FROM products 
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

    #taxTable {
        width: 100% !important;
        font-size: 14px;
    }

    #taxTable thead th {
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

    #taxTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #taxTable tbody tr:hover {
        background: #f8fafc;
    }

    .price-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .tax-badge {
        background: #dbeafe;
        color: #1d4ed8;
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
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-receipt"></i> Tax / GST Management</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="taxTable"
                data-module="tax records" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Tax %</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($p = mysqli_fetch_assoc($products)) {
                        $productJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($p['title'] ?? '') ?></td>

                            <td class="text-center">
                                <span class="price-badge">
                                    ₹<?= format_price((float)$p['price']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="tax-badge">
                                    <?= format_price((float)$p['tax_percentage']) ?>%
                                </span>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openTaxModal(<?= $productJson ?>)'>
                                    <i class="bx bx-edit"></i> Update
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- TAX MODAL -->
<div class="modal fade" id="taxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="taxForm">
            <div class="modal-header">
                <h5>Update Tax %</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="product_id" id="tax_product_id">

                <label class="form-label">Product</label>
                <input class="form-control mb-3" id="tax_product_name" readonly>

                <label class="form-label">Tax %</label>
                <input type="number"
                    name="tax"
                    id="tax_value"
                    class="form-control"
                    min="0"
                    step="0.01"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="update_tax">
                    <i class="bx bx-save"></i> Save Tax
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openTaxModal(p) {
        document.getElementById('tax_product_id').value = p.id || '';
        document.getElementById('tax_product_name').value = p.title || '';
        document.getElementById('tax_value').value = p.tax_percentage || 0;

        new bootstrap.Modal(document.getElementById('taxModal')).show();
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Tax updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>