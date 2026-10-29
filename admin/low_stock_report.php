<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

$limit = 10;

$data = mysqli_query($conn, "
    SELECT 
        p.id,
        p.title,
        COALESCE(SUM(v.stock), 0) AS total_stock
    FROM products p
    LEFT JOIN variants v ON v.product_id = p.id
    GROUP BY p.id, p.title
    HAVING total_stock <= $limit
    ORDER BY total_stock ASC
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

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-error-circle text-warning"></i> Low Stock Report (≤ <?= (int)$limit ?>)</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="lowStockReportTable"
                data-module="low stock report records">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Stock Left</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($data)) {
                        $totalStock = (int)($r['total_stock'] ?? 0);
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($r['title'] ?? '') ?></td>

                            <td class="text-center">
                                <span class="<?= $totalStock <= 0 ? 'badge-stock-out' : 'badge-stock-low' ?>">
                                    <?= $totalStock ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<?php ob_end_flush(); ?>