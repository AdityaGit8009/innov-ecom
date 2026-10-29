<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";
include_once "../order_helpers.php";
require_once __DIR__ . "/../includes/NotificationService.php";

function safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim($value ?? ''));
}

function ensureOrderHistory($conn, $orderId, $status, $note = '')
{
    $orderId = (int)$orderId;
    $status = safe($conn, $status);
    $note = safe($conn, $note);

    $check = mysqli_query($conn, "
        SELECT id 
        FROM order_status_history 
        WHERE order_id = '$orderId' 
        AND status = '$status'
        LIMIT 1
    ");

    if ($check && mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "
            INSERT INTO order_status_history (order_id, status, note)
            VALUES ('$orderId', '$status', '$note')
        ");
    }
}

function syncOrderTimeline($conn, $orderId, $finalStatus, $note = '')
{
    $flow = ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];

    if ($finalStatus == 'Cancelled') {
        ensureOrderHistory($conn, $orderId, 'Cancelled', $note ?: 'Order cancelled');
        return;
    }

    if ($finalStatus == 'Returned') {
        ensureOrderHistory($conn, $orderId, 'Returned', $note ?: 'Order returned');
        return;
    }

    $index = array_search($finalStatus, $flow);

    if ($index === false) {
        ensureOrderHistory($conn, $orderId, 'Pending', 'Order placed successfully');
        return;
    }

    for ($i = 0; $i <= $index; $i++) {
        $autoNote = '';

        if ($flow[$i] == 'Pending') {
            $autoNote = 'Order placed successfully';
        } elseif ($flow[$i] == 'Confirmed') {
            $autoNote = 'Order confirmed by admin';
        } elseif ($flow[$i] == 'Packed') {
            $autoNote = 'Order packed';
        } elseif ($flow[$i] == 'Shipped') {
            $autoNote = 'Order shipped';
        } elseif ($flow[$i] == 'Delivered') {
            $autoNote = 'Order delivered';
        }

        ensureOrderHistory($conn, $orderId, $flow[$i], $autoNote);
    }

    if ($note != '') {
        ensureOrderHistory($conn, $orderId, $finalStatus, $note);
    }
}

/* ================= UPDATE ORDER ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $id = (int)($_POST['order_id'] ?? 0);

    $allowedOrderStatus = [
        'Pending',
        'Confirmed',
        'Packed',
        'Shipped',
        'Delivered',
        'Cancelled',
        'Returned'
    ];

    $allowedPaymentStatus = [
        'Pending',
        'Paid',
        'Failed',
        'Refunded'
    ];

    $order_status = $_POST['order_status'] ?? 'Pending';

    if (!in_array($order_status, $allowedOrderStatus)) {
        $order_status = 'Pending';
    }

    $payment_method = '';
    $oldStatus = 'Pending';
    $oldOrder = [];

    $oldOrderQuery = mysqli_query($conn, "
        SELECT order_number, customer_name, payment_method, payment_status, order_status 
        FROM orders 
        WHERE id = '$id' 
        LIMIT 1
    ");

    if ($oldOrderQuery && mysqli_num_rows($oldOrderQuery) > 0) {
        $oldOrder = mysqli_fetch_assoc($oldOrderQuery);
        $payment_method = strtoupper(trim($oldOrder['payment_method'] ?? ''));
        $oldStatus = $oldOrder['order_status'] ?? 'Pending';
    }

    if (in_array($oldStatus, ['Cancelled', 'Returned'], true) && !in_array($order_status, ['Cancelled', 'Returned'], true)) {
        header("Location: orders.php?msg=locked");
        exit;
    }

    $payment_status = $_POST['payment_status'] ?? 'Pending';

    if (!in_array($payment_status, $allowedPaymentStatus)) {
        $payment_status = 'Pending';
    }

    if ($payment_method === 'COD') {
        if ($order_status === 'Delivered') {
            $payment_status = 'Paid';
        } else {
            $payment_status = 'Pending';
        }
    }

    if ($payment_method === 'COD' && ($order_status === 'Cancelled' || $order_status === 'Returned')) {
        $payment_status = 'Pending';
    }

    $order_status = safe($conn, $order_status);
    $payment_status = safe($conn, $payment_status);
    $tracking_no = safe($conn, $_POST['tracking_no'] ?? '');
    $courier_name = safe($conn, $_POST['courier_name'] ?? '');
    $expected_delivery = safe($conn, $_POST['expected_delivery'] ?? '');
    $note = safe($conn, $_POST['note'] ?? '');

    $cancelSql = "";

    if ($order_status === 'Cancelled' || $order_status === 'Returned') {
        $cancelReason = $note ?: ($order_status === 'Cancelled' ? 'Cancelled by admin' : 'Returned by admin');
        $cancelReason = safe($conn, $cancelReason);
        $cancelSql = ", cancelled_by = 'Admin', cancel_reason = '$cancelReason', cancelled_at = IFNULL(cancelled_at, NOW())";
    }

    $expectedSql = $expected_delivery != ""
        ? "expected_delivery_date = '$expected_delivery'"
        : "expected_delivery_date = NULL";

    $deliveredSql = "";
    $invoiceSql = "";

    if ($order_status === 'Delivered') {
        $deliveredSql = ", delivered_at = IFNULL(delivered_at, NOW())";
        $invoiceSql = ", invoice_no = IF(invoice_no IS NULL OR invoice_no = '', CONCAT('INV', DATE_FORMAT(NOW(), '%Y%m%d'), id), invoice_no)";
    }

    $update = mysqli_query($conn, "
        UPDATE orders 
        SET 
            order_status = '$order_status',
            payment_status = '$payment_status',
            tracking_no = '$tracking_no',
            courier_name = '$courier_name',
            $expectedSql
            $deliveredSql
            $invoiceSql
            $cancelSql
        WHERE id = '$id'
    ");

    if (!$update) {
        die("Order update failed: " . mysqli_error($conn));
    }

    if (in_array($order_status, ['Cancelled', 'Returned'], true)) {
        sip_restore_order_stock_once($conn, $id);
    }

    mysqli_query($conn, "
        UPDATE transactions 
        SET payment_status = '$payment_status' 
        WHERE order_id = '$id'
    ");

    syncOrderTimeline($conn, $id, $order_status, $note);

    sip_log_admin_activity($conn, 'ORDER_STATUS_UPDATE', 'ORDERS', 'Order status updated from ' . $oldStatus . ' to ' . $order_status, 'order', $id, [
        'order_number' => $oldOrder['order_number'] ?? '',
        'customer_name' => $oldOrder['customer_name'] ?? '',
        'old_order_status' => $oldStatus,
        'new_order_status' => $order_status,
        'old_payment_status' => $oldOrder['payment_status'] ?? '',
        'new_payment_status' => $payment_status,
        'tracking_no' => $tracking_no,
        'courier_name' => $courier_name,
        'expected_delivery' => $expected_delivery,
        'note' => $note
    ]);

    if ($order_status === 'Delivered' && $oldStatus !== 'Delivered') {
        try {
            $ns = new NotificationService();
            $ns->sendDeliveredInvoice($conn, $id);
        } catch (Exception $e) {
            error_log('Delivered invoice email failed for order ' . $id . ': ' . $e->getMessage());
        }
    }

    header("Location: orders.php?msg=updated");
    exit;
}

/* ================= DELETE ORDER ================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $deletedOrder = mysqli_fetch_assoc(mysqli_query($conn, "SELECT order_number, customer_name, total_amount, order_status FROM orders WHERE id = '$id' LIMIT 1"));

    mysqli_query($conn, "DELETE FROM order_items WHERE order_id = '$id'");
    mysqli_query($conn, "DELETE FROM order_status_history WHERE order_id = '$id'");
    $orderDelete = mysqli_query($conn, "DELETE FROM orders WHERE id = '$id'");
    if ($orderDelete) {
        sip_log_admin_activity($conn, 'DELETE', 'ORDERS', 'Order deleted: ' . ($deletedOrder['order_number'] ?? ('ID ' . $id)), 'order', $id, [
            'order_number' => $deletedOrder['order_number'] ?? '',
            'customer_name' => $deletedOrder['customer_name'] ?? '',
            'total_amount' => $deletedOrder['total_amount'] ?? '',
            'order_status' => $deletedOrder['order_status'] ?? ''
        ]);
    }

    header("Location: orders.php?msg=deleted");
    exit;
}

/* ================= FETCH ORDERS ================= */
$ordersQuery = mysqli_query($conn, "
    SELECT * 
    FROM orders 
    ORDER BY id DESC
");

$orders = [];

while ($row = mysqli_fetch_assoc($ordersQuery)) {
    $orders[] = $row;
}
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

    #ordersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #ordersTable thead th {
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

    #ordersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #ordersTable tbody tr:hover {
        background: #f8fafc;
    }

    .order-no {
        font-weight: 800;
        color: #334155;
    }

    .order-invoice {
        font-size: 12px;
        color: #94a3b8;
        display: block;
        margin-top: 3px;
    }

    .customer-name {
        font-weight: 700;
        color: #334155;
    }

    .customer-phone {
        font-size: 12px;
        color: #64748b;
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

    .tracking-text {
        font-size: 12px;
        line-height: 1.5;
        color: #64748b;
    }

    .tracking-text b {
        color: #334155;
    }

    .action-dropdown .btn {
        border: 0;
        background: transparent;
        color: #334155;
        font-size: 20px;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .action-dropdown .btn:hover,
    .action-dropdown .btn:focus {
        background: #f1f5f9;
        color: #0f172a;
        box-shadow: none;
    }

    .action-dropdown .dropdown-menu {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
        padding: 8px;
        min-width: 160px;
    }

    .action-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
    }

    .action-dropdown .dropdown-item:hover {
        background: #f8fafc;
    }

    .action-dropdown .dropdown-item.text-danger:hover {
        background: #fee2e2;
        color: #991b1b !important;
    }

    /* ================= MODAL DESIGN ================= */

    .tracking-modal .modal-dialog {
        /* max-width: 950px; */
    }

    .tracking-modal .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18);
    }

    .tracking-modal .modal-header {
        /* background: linear-gradient(135deg, #ff6a00, #ff3d00); */
        padding: 20px 24px;
        border: 0;
    }

    .tracking-modal .modal-title {
        color: #000;
        font-size: 20px;
        font-weight: 800;
    }

    .tracking-modal .btn-close {
        /* filter: brightness(0) invert(1); */
        opacity: 1;
    }

    .tracking-modal .modal-body {
        padding: 24px;
        background: #f8fafc;
        max-height: 72vh;
        overflow-y: auto;
    }

    /* GRID */

    .modal-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .modal-grid .full-col {
        grid-column: span 2;
    }

    /* LABEL */

    .tracking-modal .form-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 7px;
    }

    /* INPUT */

    .tracking-modal .form-control,
    .tracking-modal .form-select {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #dbe2ea;
        background: #fff;
        font-size: 14px;
        color: #0f172a;
        box-shadow: none;
        transition: all .2s ease;
    }

    .tracking-modal textarea.form-control {
        min-height: 110px;
        resize: none;
        padding-top: 12px;
    }

    .tracking-modal .form-control:focus,
    .tracking-modal .form-select:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 4px rgba(255, 90, 31, .12);
    }

    /* FOOTER */

    .tracking-modal .modal-footer {
        border: 0;
        padding: 0 24px 24px;
        background: #f8fafc;
    }

    .btn-save-tracking {
        /* background: linear-gradient(135deg, #ff6a00, #ff3d00); */
        background-color: #71dd37;
        color: #fff;
        border: 0;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        transition: .2s;
    }

    .btn-save-tracking:hover {
        transform: translateY(-1px);
        background-color: #71dd37;
        opacity: .95;
        color: #fff;
    }

    .small-help {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #64748b;
    }

    /* SCROLLBAR */

    .tracking-modal .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .tracking-modal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }

    /* MOBILE */

    @media(max-width:768px) {

        .tracking-modal .modal-dialog {
            margin: 12px;
        }

        .modal-grid {
            grid-template-columns: 1fr;
        }

        .modal-grid .full-col {
            grid-column: span 1;
        }

        .tracking-modal .modal-body {
            padding: 18px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-cart"></i> Orders & Tracking
    </h4>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'locked'): ?>
        <div class="alert alert-warning">
            Cancelled/Returned order ko dobara active status me move nahi kar sakte.
        </div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success">
            Order updated successfully.
        </div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">
            Order deleted successfully.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="ordersTable"
                data-module="orders">
                <thead>
                    <tr class="text-center">
                        <th>Sr.</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Tracking</th>
                        <th>Date</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($orders as $row): ?>
                        <?php
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

                        if ($orderStatus == 'Confirmed') {
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
                                <span class="order-invoice">
                                    Inv: <?= htmlspecialchars($row['invoice_no'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <td>
                                <span class="customer-name">
                                    <?= htmlspecialchars($row['customer_name'] ?? '') ?>
                                </span>
                                <br>
                                <span class="customer-phone">
                                    <?= htmlspecialchars($row['customer_phone'] ?? '') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="amount-badge">
                                    ₹<?= format_price((float)($row['total_amount'] ?? 0)) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft badge-soft-<?= $paymentClass ?>">
                                    <?= htmlspecialchars($paymentStatus) ?>
                                </span>
                                <br>
                                <small><?= htmlspecialchars($row['payment_method'] ?? '') ?></small>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft badge-soft-<?= $statusClass ?>">
                                    <?= htmlspecialchars($orderStatus) ?>
                                </span>
                            </td>

                            <td>
                                <div class="tracking-text">
                                    <b>Courier:</b> <?= htmlspecialchars($row['courier_name'] ?: 'N/A') ?><br>
                                    <b>Track:</b> <?= htmlspecialchars($row['tracking_no'] ?: 'N/A') ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '' ?>
                            </td>

                            <td class="text-center">
                                <div class="dropdown action-dropdown">
                                    <button class="btn dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="order_view.php?id=<?= (int)$row['id'] ?>">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                        </li>

                                        <li>
                                            <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#trackModal<?= (int)$row['id'] ?>">
                                                <i class="bx bx-edit"></i> Update
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$row['id'] ?>"
                                                class="dropdown-item text-danger"
                                                onclick="return confirm('Delete order?')">
                                                <i class="bx bx-trash"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php foreach ($orders as $row): ?>
    <?php
    $isCodOrder = strtoupper(trim($row['payment_method'] ?? '')) === 'COD';
    ?>

    <div class="modal fade tracking-modal"
        id="trackModal<?= (int)$row['id'] ?>"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-md modal-dialog-centered">
            <form method="post" class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Update Tracking - <?= htmlspecialchars($row['order_no'] ?? '') ?>
                    </h5>

                    <button type="button"
                        class="btn-close text-dark"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="order_id" value="<?= (int)$row['id'] ?>">
                    <input type="hidden" name="update_order" value="1">

                    <div class="modal-grid">

                        <!-- PAYMENT STATUS -->

                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>

                            <select
                                name="payment_status"
                                class="form-select"
                                <?= $isCodOrder ? 'disabled' : '' ?>>

                                <?php foreach (['Pending', 'Paid', 'Failed', 'Refunded'] as $pst): ?>
                                    <option value="<?= $pst ?>"
                                        <?= ($row['payment_status'] ?? '') === $pst ? 'selected' : '' ?>>
                                        <?= $pst ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                            <?php if ($isCodOrder): ?>

                                <input type="hidden"
                                    name="payment_status"
                                    value="<?= htmlspecialchars($row['payment_status'] ?? 'Pending') ?>">

                                <small class="small-help">
                                    COD payment automatic hai:
                                    Delivered hone par Paid,
                                    usse pehle Pending.
                                </small>

                            <?php else: ?>

                                <small class="small-help">
                                    Online payment status admin manually update kar sakta hai.
                                </small>

                            <?php endif; ?>
                        </div>

                        <!-- ORDER STATUS -->

                        <div class="mb-3">
                            <label class="form-label">Order Status</label>

                            <select name="order_status" class="form-select">

                                <?php foreach (['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered', 'Cancelled', 'Returned'] as $st): ?>

                                    <option value="<?= $st ?>"
                                        <?= ($row['order_status'] ?? '') === $st ? 'selected' : '' ?>>

                                        <?= $st ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- COURIER -->

                        <div class="mb-3 full-col">
                            <label class="form-label">Courier Name</label>

                            <input
                                type="text"
                                name="courier_name"
                                class="form-control"
                                placeholder="Example: Delhivery, Blue Dart, DTDC"
                                value="<?= htmlspecialchars($row['courier_name'] ?? '') ?>">
                        </div>

                        <!-- TRACKING -->

                        <div class="mb-3 full-col">
                            <label class="form-label">Tracking No</label>

                            <input
                                type="text"
                                name="tracking_no"
                                class="form-control"
                                placeholder="Enter tracking number"
                                value="<?= htmlspecialchars($row['tracking_no'] ?? '') ?>">
                        </div>

                        <!-- EXPECTED DELIVERY -->

                        <div class="mb-3">
                            <label class="form-label">Expected Delivery</label>

                            <input
                                type="date"
                                name="expected_delivery"
                                class="form-control"
                                value="<?= htmlspecialchars($row['expected_delivery_date'] ?? '') ?>">
                        </div>

                        <!-- ORDER NUMBER -->

                        <div class="mb-3">
                            <label class="form-label">Order No</label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($row['order_no'] ?? '') ?>"
                                readonly>
                        </div>

                        <!-- NOTE -->

                        <div class="mb-3 full-col">
                            <label class="form-label">Tracking Note</label>

                            <textarea
                                name="note"
                                class="form-control"
                                placeholder="Example: Parcel handed over to courier"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                        class="btn btn-save-tracking">

                        <i class="bx bx-save"></i>
                        Save Update

                    </button>

                </div>

            </form>
        </div>
    </div>

<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tracking-modal').forEach(function(modal) {
            document.body.appendChild(modal);
        });
    });
</script>

<?php include "includes/footer.php"; ?>

<?php ob_end_flush(); ?>