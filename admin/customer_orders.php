<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

/* ===== FETCH CUSTOMER ORDERS ===== */
$orders = mysqli_query($conn, "
    SELECT *
    FROM orders
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

    #customerOrdersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #customerOrdersTable thead th {
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

    #customerOrdersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #customerOrdersTable tbody tr:hover {
        background: #f8fafc;
    }

    .order-no {
        font-weight: 800;
        color: #334155;
    }

    .customer-name {
        font-weight: 700;
        color: #334155;
    }

    .amount-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-soft {
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
    }

    .badge-soft-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-soft-info {
        background: #e0f2fe;
        color: #075985;
    }

    .badge-soft-primary {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-cart"></i> Customer Orders
    </h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="customerOrdersTable"
                data-module="customer orders">
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
                        $paymentStatus = $row['payment_status'] ?? 'Pending';
                        $orderStatus = $row['order_status'] ?? 'Pending';

                        $paymentClass = 'warning';

                        if ($paymentStatus == 'Paid') {
                            $paymentClass = 'success';
                        } elseif ($paymentStatus == 'Failed') {
                            $paymentClass = 'danger';
                        } elseif ($paymentStatus == 'Refunded') {
                            $paymentClass = 'info';
                        }

                        $statusClass = 'secondary';

                        if ($orderStatus == 'Pending') {
                            $statusClass = 'warning';
                        } elseif ($orderStatus == 'Confirmed') {
                            $statusClass = 'info';
                        } elseif ($orderStatus == 'Packed') {
                            $statusClass = 'warning';
                        } elseif ($orderStatus == 'Shipped') {
                            $statusClass = 'primary';
                        } elseif ($orderStatus == 'Delivered') {
                            $statusClass = 'success';
                        } elseif ($orderStatus == 'Cancelled' || $orderStatus == 'Returned') {
                            $statusClass = 'danger';
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="order-no">
                                    <?= htmlspecialchars($row['order_no'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <span class="customer-name">
                                    <?= htmlspecialchars($row['customer_name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['customer_email'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['customer_phone'] ?? '') ?>
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
                                <span class="badge-soft badge-soft-<?= $paymentClass ?>">
                                    <?= htmlspecialchars($paymentStatus) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft badge-soft-<?= $statusClass ?>">
                                    <?= htmlspecialchars($orderStatus) ?>
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