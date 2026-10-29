<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== UPDATE DISCOUNT ===== */
if (isset($_POST['update_discount'])) {
    $pid = (int)($_POST['product_id'] ?? 0);
    $discount = (float)($_POST['discount'] ?? 0);

    if ($pid <= 0 || $discount < 0 || $discount > 100) {
        header("Location: discount_price.php?msg=error");
        exit;
    }

    $q = mysqli_query(
        $conn,
        "UPDATE products 
         SET discount='$discount' 
         WHERE id=$pid"
    );

    header("Location: discount_price.php?msg=" . ($q ? "ok" : "error"));
    exit;
}

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "
    SELECT id, title, price, discount 
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

    #discountTable {
        width: 100% !important;
        font-size: 14px;
    }

    #discountTable thead th {
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

    #discountTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #discountTable tbody tr:hover {
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

    .discount-badge {
        background: #fef3c7;
        color: #92400e;
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
    <h4><i class="bx bx-badge-percent"></i> Discount Management</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="discountTable"
                data-module="discount records"data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Discount %</th>
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
                                <span class="discount-badge">
                                    <?= format_price((float)$p['discount']) ?>%
                                </span>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openDiscountModal(<?= $productJson ?>)'>
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

<!-- DISCOUNT MODAL -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="discountForm">
            <div class="modal-header">
                <h5>Update Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="product_id" id="discount_product_id">

                <label class="form-label">Product</label>
                <input class="form-control mb-3" id="discount_product_name" readonly>

                <label class="form-label">Discount %</label>
                <input type="number"
                    name="discount"
                    id="discount_value"
                    class="form-control"
                    min="0"
                    max="100"
                    step="0.01"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="update_discount">
                    <i class="bx bx-save"></i> Save Discount
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openDiscountModal(p) {
        document.getElementById('discount_product_id').value = p.id || '';
        document.getElementById('discount_product_name').value = p.title || '';
        document.getElementById('discount_value').value = p.discount || 0;

        new bootstrap.Modal(document.getElementById('discountModal')).show();
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Discount updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>