<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== UPDATE PRICE ===== */
if (isset($_POST['update_price'])) {
    $pid = (int)($_POST['product_id'] ?? 0);
    $new_price = (float)($_POST['price'] ?? 0);

    if ($pid <= 0 || $new_price < 0) {
        header("Location: product_price.php?msg=error");
        exit;
    }

    $oldQuery = mysqli_query($conn, "SELECT price FROM products WHERE id=$pid");
    $oldRow = mysqli_fetch_assoc($oldQuery);

    if (!$oldRow) {
        header("Location: product_price.php?msg=error");
        exit;
    }

    $old_price = (float)$oldRow['price'];

    $q = mysqli_query($conn, "
        UPDATE products 
        SET price='$new_price', old_price='$old_price'
        WHERE id=$pid
    ");

    header("Location: product_price.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "
    SELECT id, title, price, old_price 
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

    #productPriceTable {
        width: 100% !important;
        font-size: 14px;
    }

    #productPriceTable thead th {
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

    #productPriceTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #productPriceTable tbody tr:hover {
        background: #f8fafc;
    }

    .price-current {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .price-old {
        background: #e2e8f0;
        color: #475569;
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
    <h4><i class="bx bx-money-withdraw"></i> Product Price Management</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="productPriceTable"
                data-module="product prices" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Current Price</th>
                        <th>Old Price</th>
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
                                <span class="price-current">
                                    ₹<?= format_price((float)$p['price']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="price-old">
                                    ₹<?= format_price((float)$p['old_price']) ?>
                                </span>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openPriceModal(<?= $productJson ?>)'>
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

<!-- PRICE MODAL -->
<div class="modal fade" id="priceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="priceForm">
            <div class="modal-header">
                <h5>Update Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="product_id" id="price_product_id">

                <label class="form-label">Product</label>
                <input class="form-control mb-3" id="price_product_name" readonly>

                <label class="form-label">New Price</label>
                <input type="number"
                    name="price"
                    id="new_price"
                    class="form-control"
                    step="0.01"
                    min="0"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="update_price">
                    <i class="bx bx-save"></i> Save Price
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openPriceModal(p) {
        document.getElementById('price_product_id').value = p.id || '';
        document.getElementById('price_product_name').value = p.title || '';
        document.getElementById('new_price').value = p.price || 0;

        new bootstrap.Modal(document.getElementById('priceModal')).show();
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Product price updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>