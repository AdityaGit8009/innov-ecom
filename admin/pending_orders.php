<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

$orders = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE order_status = 'Pending'
    ORDER BY id DESC
");
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

    #pendingOrdersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #pendingOrdersTable thead th {
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

    #pendingOrdersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #pendingOrdersTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-soft-warning {
        background: #fef3c7;
        color: #92400e;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
        padding: 7px 10px;
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

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }
    }
</style>

<div class="page-title-box">
    <h4><i class="bx bx-time"></i> Pending Orders</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="pendingOrdersTable"
                data-module="pending orders" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($orders)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <b><?= htmlspecialchars($row['order_no'] ?? '') ?></b>
                            </td>

                            <td><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>

                            <td><?= htmlspecialchars($row['customer_email'] ?? '') ?></td>

                            <td><?= htmlspecialchars($row['customer_phone'] ?? '') ?></td>

                            <td class="text-center">
                                <span class="amount-badge">
                                    ₹<?= format_price((float)($row['total_amount'] ?? 0)) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['payment_method'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft-warning">
                                    <?= htmlspecialchars($row['payment_status'] ?? 'Pending') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft-secondary">
                                    <?= htmlspecialchars($row['order_status'] ?? 'Pending') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?= !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : '' ?>
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