<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

$orders = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE order_status = 'Delivered'
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

    #completedOrdersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #completedOrdersTable thead th {
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

    #completedOrdersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #completedOrdersTable tbody tr:hover {
        background: #f8fafc;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
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
    <h4><i class="bx bx-check-circle"></i> Completed Orders</h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="completedOrdersTable"
                data-module="completed orders">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Delivered On</th>
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

                            <td>
                                <?= htmlspecialchars($row['customer_name'] ?? '') ?>
                                <?php if (!empty($row['customer_phone'])) { ?>
                                    <br><small><?= htmlspecialchars($row['customer_phone']) ?></small>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <span class="amount-badge">
                                    ₹<?= format_price((float)($row['total_amount'] ?? 0)) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['payment_method'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft-success">
                                    <?= htmlspecialchars($row['payment_status'] ?? 'Paid') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php
                                if (!empty($row['delivered_at'])) {
                                    echo date("d M Y", strtotime($row['delivered_at']));
                                } elseif (!empty($row['created_at'])) {
                                    echo date("d M Y", strtotime($row['created_at']));
                                }
                                ?>
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