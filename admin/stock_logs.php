<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

$logs = mysqli_query($conn, "
    SELECT sh.*, p.title
    FROM stock_history sh
    LEFT JOIN products p ON p.id = sh.product_id
    ORDER BY sh.id DESC
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

    #stockLogsTable {
        width: 100% !important;
        font-size: 14px;
    }

    #stockLogsTable thead th {
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

    #stockLogsTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #stockLogsTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-stock-old {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-stock-new {
        background: #dcfce7;
        color: #166534;
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
    <h4><i class="bx bx-history"></i> Stock History</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="stockLogsTable"
                data-module="stock history records" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product Name</th>
                        <th>Old Stock</th>
                        <th>New Stock</th>
                        <th>Changed By</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($l = mysqli_fetch_assoc($logs)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($l['title'] ?? '—') ?></td>

                            <td class="text-center">
                                <span class="badge-stock-old"><?= (int)$l['old_stock'] ?></span>
                            </td>

                            <td class="text-center">
                                <span class="badge-stock-new"><?= (int)$l['new_stock'] ?></span>
                            </td>

                            <td><?= htmlspecialchars($l['changed_by'] ?? '—') ?></td>

                            <td>
                                <?= !empty($l['created_at'])
                                    ? date("d M Y, h:i A", strtotime($l['created_at']))
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