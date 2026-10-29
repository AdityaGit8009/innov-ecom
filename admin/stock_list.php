<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== UPDATE STOCK ===== */
if (isset($_POST['update_stock'])) {
    $pid   = (int)($_POST['product_id'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($pid <= 0 || $stock < 0) {
        header("Location: stock_list.php?msg=error");
        exit;
    }

    $oldQuery = mysqli_query($conn, "SELECT stock FROM products WHERE id=$pid");
    $oldRow = mysqli_fetch_assoc($oldQuery);

    if (!$oldRow) {
        header("Location: stock_list.php?msg=error");
        exit;
    }

    $oldStock = (int)$oldRow['stock'];

    $update = mysqli_query(
        $conn,
        "UPDATE products 
         SET stock=$stock, created_at=NOW() 
         WHERE id=$pid"
    );

    if ($update) {
        mysqli_query(
            $conn,
            "INSERT INTO stock_history(product_id, old_stock, new_stock, changed_by)
             VALUES($pid, $oldStock, $stock, 'Admin')"
        );
    }

    header("Location: stock_list.php?msg=" . ($update ? "ok" : "error"));
    exit;
}

/* ===== FETCH PRODUCTS WITH CATEGORY ===== */
$products = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.id DESC
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

    #stockTable {
        width: 100% !important;
        font-size: 14px;
    }

    #stockTable thead th {
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

    #stockTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #stockTable tbody tr:hover {
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
    <h4><i class="bx bx-list-ul"></i> Stock List</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="stockTable"
                data-module="stock records" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($p = mysqli_fetch_assoc($products)) {
                        if ((int)$p['stock'] == 0) {
                            $status = "<span class='badge-stock-out'>Out</span>";
                        } elseif ((int)$p['stock'] <= 5) {
                            $status = "<span class='badge-stock-low'>Low</span>";
                        } else {
                            $status = "<span class='badge-stock-in'>In</span>";
                        }

                        $productJson = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($p['title'] ?? '') ?></td>

                            <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>

                            <td class="text-center">
                                <strong><?= (int)$p['stock'] ?></strong>
                            </td>

                            <td class="text-center">
                                <?= $status ?>
                            </td>

                            <td>
                                <?= !empty($p['created_at'])
                                    ? date("d M Y, h:i A", strtotime($p['created_at']))
                                    : '—' ?>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick='openStockModal(<?= $productJson ?>)'>
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

<!-- UPDATE STOCK MODAL -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="stockForm">
            <div class="modal-header">
                <h5>Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="product_id" id="product_id">

                <label class="form-label">Product</label>
                <input class="form-control mb-3" id="product_name" readonly>

                <label class="form-label">Stock Quantity</label>
                <input type="number"
                    name="stock"
                    id="stock_qty"
                    class="form-control"
                    min="0"
                    required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="update_stock">
                    <i class="bx bx-save"></i> Save Stock
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openStockModal(p) {
        document.getElementById('product_id').value = p.id || '';
        document.getElementById('product_name').value = p.title || '';
        document.getElementById('stock_qty').value = p.stock || 0;

        new bootstrap.Modal(document.getElementById('stockModal')).show();
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Stock updated successfully!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>