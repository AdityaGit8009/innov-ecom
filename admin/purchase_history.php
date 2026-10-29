<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== FETCH PURCHASE HISTORY ===== */
$history = mysqli_query($conn, "
    SELECT p.*, s.name AS supplier
    FROM supplier_purchases p
    LEFT JOIN suppliers s ON s.id = p.supplier_id
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

    #purchaseHistoryTable {
        width: 100% !important;
        font-size: 14px;
    }

    #purchaseHistoryTable thead th {
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

    #purchaseHistoryTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #purchaseHistoryTable tbody tr:hover {
        background: #f8fafc;
    }

    .amount-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .invoice-badge {
        background: #e2e8f0;
        color: #475569;
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
    <h4><i class="bx bx-history"></i> Purchase History</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="purchaseHistoryTable"
                data-module="purchase history records" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Supplier</th>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($history)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td><?= htmlspecialchars($r['supplier'] ?? '—') ?></td>

                            <td class="text-center">
                                <span class="invoice-badge">
                                    <?= htmlspecialchars($r['invoice_no'] ?? '—') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="amount-badge">
                                    ₹<?= format_price((float)($r['total_amount'] ?? 0)) ?>
                                </span>
                            </td>

                            <td>
                                <?= !empty($r['purchase_date'])
                                    ? date("d M Y", strtotime($r['purchase_date']))
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