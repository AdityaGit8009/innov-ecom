<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

$data = mysqli_query($conn, "
    SELECT 
        l.*, 
        p.title,
        v.variant_name
    FROM stock_logs l
    LEFT JOIN products p ON p.id = l.product_id
    LEFT JOIN variants v ON v.id = l.variant_id
    ORDER BY l.id DESC
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

    .badge-in {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-out {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .qty-badge {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .note-text {
        max-width: 260px;
        white-space: normal;
        line-height: 1.45;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-transfer"></i> Product Movement</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="productMovementTable"
                data-module="product movement records" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Note</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($data)) {
                        $type = strtoupper(trim($r['type'] ?? ''));
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($r['title'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($r['variant_name'] ?? '—') ?></td>

                            <td class="text-center">
                                <span class="<?= $type == 'IN' ? 'badge-in' : 'badge-out' ?>">
                                    <?= htmlspecialchars($type ?: '—') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="qty-badge"><?= (int)($r['quantity'] ?? 0) ?></span>
                            </td>

                            <td>
                                <div class="note-text">
                                    <?= htmlspecialchars($r['note'] ?? '—') ?>
                                </div>
                            </td>

                            <td>
                                <?= !empty($r['created_at'])
                                    ? date("d M Y, h:i A", strtotime($r['created_at']))
                                    : '—' ?>
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