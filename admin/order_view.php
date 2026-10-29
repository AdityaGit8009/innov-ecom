<?php
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";
include_once "../order_helpers.php";

$id = (int)($_GET['id'] ?? 0);
$order_q = mysqli_query($conn, "SELECT * FROM orders WHERE id='$id' LIMIT 1");
$order = $order_q ? mysqli_fetch_assoc($order_q) : null;

if (!$order) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    include "includes/footer.php";
    exit;
}

$items = mysqli_query($conn, "
    SELECT oi.*, 
        IF(oi.item_type='project', pr.title, p.title) AS title, 
        IF(oi.item_type='project', pr.image, p.main_image) AS main_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id=p.id AND oi.item_type = 'product'
    LEFT JOIN projects pr ON oi.product_id=pr.id AND oi.item_type = 'project'
    WHERE oi.order_id='$id'
");
$history = mysqli_query($conn, "SELECT * FROM order_status_history WHERE order_id='$id' ORDER BY id ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bx bx-receipt"></i> Order #<?= sip_h($order['order_no']) ?></h4>
    <a href="orders.php" class="btn btn-secondary btn-sm">Back</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Customer Detail</h5>
                <b>Name:</b> <?= sip_h($order['customer_name']) ?><br>
                <b>Email:</b> <?= sip_h($order['customer_email']) ?><br>
                <b>Phone:</b> <?= sip_h($order['customer_phone']) ?><br>
                <b>User ID:</b> <?= (int)$order['user_id'] ?><br>
                <b>Address ID:</b> <?= (int)$order['address_id'] ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Order / Payment Detail</h5>
                <b>Invoice:</b> <?= sip_h($order['invoice_no'] ?? 'N/A') ?><br>
                <b>Tracking:</b> <?= sip_h($order['tracking_no'] ?? 'N/A') ?><br>
                <b>Courier:</b> <?= sip_h($order['courier_name'] ?? 'N/A') ?><br>
                <b>Payment:</b> <?= sip_h($order['payment_method']) ?> / <?= sip_h($order['payment_status']) ?><br>
                <b>Status:</b> <span class="badge bg-<?= sip_order_badge($order['order_status']) ?>"><?= sip_h($order['order_status']) ?></span>
                <?php if (($order['order_status'] ?? '') === 'Cancelled'): ?>
                    <hr>
                    <b>Cancelled By:</b> <?= sip_h($order['cancelled_by'] ?? 'N/A') ?><br>
                    <b>Cancelled On:</b> <?= !empty($order['cancelled_at']) ? date('d M Y, h:i A', strtotime($order['cancelled_at'])) : 'N/A' ?><br>
                    <b>Reason:</b> <?= sip_h($order['cancel_reason'] ?? 'N/A') ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Products</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items && mysqli_num_rows($items) > 0): ?>
                        <?php while ($i = mysqli_fetch_assoc($items)): ?>
                            <tr>
                                <td><?= sip_h($i['title'] ?? 'Product not available') ?></td>
                                <td>₹<?= format_price((float)$i['price']) ?></td>
                                <td><?= (int)$i['qty'] ?></td>
                                <td>₹<?= format_price((float)$i['price'] * (int)$i['qty']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <h5 class="text-end">Grand Total: ₹<?= format_price((float)$order['total_amount']) ?></h5>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Status History</h5>
        <?php if ($history && mysqli_num_rows($history) > 0): ?>
            <ul class="mb-0">
                <?php while ($h = mysqli_fetch_assoc($history)): ?>
                    <li class="mb-2"><b><?= sip_h($h['status']) ?></b> - <?= date('d M Y, h:i A', strtotime($h['created_at'])) ?><?php if (!empty($h['note'])): ?> <small class="text-muted">(<?= sip_h($h['note']) ?>)</small><?php endif; ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted mb-0">No status history available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
