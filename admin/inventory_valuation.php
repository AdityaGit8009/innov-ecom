<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

$data = mysqli_query($conn, "
    SELECT 
        p.title,
        v.variant_name,
        v.stock,
        v.price,
        (v.stock * v.price) AS total_value
    FROM variants v
    LEFT JOIN products p ON p.id = v.product_id
    ORDER BY total_value DESC
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

    .badge-stock {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .price-badge {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .amount-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .grand-total-row th {
        background: #fff3ec !important;
        color: #2b160b !important;
        font-weight: 800;
        padding: 14px 12px;
        border-color: #ead8cd;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-money"></i> Inventory Valuation</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="inventoryValuationTable"
                data-module="inventory valuation records">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Stock</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    $grand = 0;

                    while ($r = mysqli_fetch_assoc($data)) {
                        $stock = (int)($r['stock'] ?? 0);
                        $price = (float)($r['price'] ?? 0);
                        $totalValue = (float)($r['total_value'] ?? 0);
                        $grand += $totalValue;
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($r['title'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($r['variant_name'] ?? '—') ?></td>

                            <td class="text-center">
                                <span class="badge-stock"><?= $stock ?></span>
                            </td>

                            <td class="text-center">
                                <span class="price-badge">₹<?= format_price($price) ?></span>
                            </td>

                            <td class="text-center">
                                <span class="amount-badge">₹<?= format_price($totalValue) ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr class="grand-total-row">
                        <th colspan="5" class="text-end">Grand Total</th>
                        <th class="text-center">₹<?= format_price($grand) ?></th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<?php ob_end_flush(); ?>