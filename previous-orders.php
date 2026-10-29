<?php
session_start();
include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';

$user_id = (int)ecom_require_user();

include 'header.php';

$orders_q = mysqli_query(
    $conn,
    "SELECT * FROM orders 
     WHERE user_id='$user_id' 
     AND order_status!='Cancelled' 
     ORDER BY id DESC 
     LIMIT 999 OFFSET 1"
);
?>

<?= sip_order_card_css(); ?>

<section class="breadcrumb-section">
    <h1>PREVIOUS ORDERS</h1>
    <p class="breadcrumb">
        <a href="orders.php">My Orders</a> <span>| Previous Records</span>
    </p>
</section>

<div class="container my-5">

    <div class="sip-order-toolbar">
        <a href="orders.php">Latest Order</a>
        <a href="previous-orders.php" class="active">Previous Orders</a>
        <a href="cancelled-orders.php">Cancelled Orders</a>
        <a href="user_profile.php?tab=track">Track Order</a>
    </div>

    <?php if (!$orders_q || mysqli_num_rows($orders_q) == 0): ?>
        <div class="sip-empty">
            <h4>No previous orders found</h4>
            <p>Only older active orders will be listed here.</p>
        </div>
    <?php endif; ?>

    <?php while ($order = mysqli_fetch_assoc($orders_q)): ?>

        <?php
        $oid = (int)$order['id'];

        $items_q = mysqli_query(
            $conn,
            "SELECT oi.*, 
                IF(oi.item_type='project', pr.title, p.title) AS title, 
                IF(oi.item_type='project', pr.image, p.main_image) AS main_image 
             FROM order_items oi 
             LEFT JOIN products p ON p.id=oi.product_id AND oi.item_type='product'
             LEFT JOIN projects pr ON pr.id=oi.product_id AND oi.item_type='project'
             WHERE oi.order_id='$oid'"
        );

        $canCancel = sip_can_customer_cancel($order['order_status'] ?? 'Pending');

        $status = $order['order_status'] ?? '';
        $payment = strtolower(trim($order['payment_status'] ?? ''));
        $invoice = $order['invoice_no'] ?? '';

        // echo $status . ' - ' . $payment . ' - ' . $invoice;
        // die();
        ?>

        <div class="sip-order-card">

            <!-- ORDER TOP -->
            <div class="sip-order-top">
                <div>
                    <h3 class="sip-order-no">
                        Order #<?= sip_h($order['order_no']) ?>
                    </h3>

                    <div class="sip-order-meta">
                        Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </div>

                    <!-- ✅ FIXED INVOICE LOGIC -->
                    <?php if (
                        $status === 'Delivered'
                        && !empty($invoice)
                        && $payment === 'paid'
                    ): ?>
                        <div class="sip-order-meta">
                            Invoice: <b><?= sip_h($invoice) ?></b>
                        </div>
                    <?php endif; ?>
                </div>

                <span class="sip-pill bg-<?= sip_order_badge($status) ?>">
                    <?= sip_h($status) ?>
                </span>
            </div>

            <!-- ITEMS -->
            <?php
            $grandTotal = 0;
            ?>

            <?php while ($item = mysqli_fetch_assoc($items_q)): ?>

                <?php
                $price = (float)$item['price'];
                $qty = (int)$item['qty'];
                $item_total = $price * $qty;
                $grandTotal += $item_total;
                ?>

                <div class="sip-item">
                    <img src="uploads/<?= ($item['item_type'] === 'project') ? 'projects' : 'products'; ?>/<?= sip_h($item['main_image']) ?>" alt="Product">

                    <div>
                        <div class="sip-item-name">
                            <?= sip_h($item['title']) ?>
                        </div>

                        <div class="sip-item-sub">
                            ₹<?= format_price($price) ?> × <?= $qty ?>
                        </div>
                    </div>

                    <div class="sip-item-total">
                        ₹<?= format_price($item_total) ?>
                    </div>
                </div>

            <?php endwhile; ?>

            <!-- FOOTER -->
            <div class="sip-order-footer">

                <div class="sip-info-grid">
                    <span>Payment: <b><?= sip_h($order['payment_method']) ?></b></span>
                    <span>Payment Status: <b><?= sip_h($order['payment_status']) ?></b></span>
                    <span>Tracking: <b><?= sip_h($order['tracking_no'] ?: 'N/A') ?></b></span>
                </div>

                <div>
                    <div class="sip-total">
                        Total: ₹<?= format_price($grandTotal) ?>
                    </div>

                    <div class="sip-actions mt-2">

                        <a href="order-details.php?order_id=<?= $oid ?>"
                            class="sip-btn sip-btn-outline">
                            View Details
                        </a>

                        <!-- ✅ FIXED INVOICE BUTTON -->
                        <?php if (
                            $status === 'Delivered'
                            && !empty($invoice)
                            && $payment === 'paid'
                        ): ?>
                            <a href="invoice.php?order_id=<?= $oid ?>"
                                class="sip-btn sip-btn-dark">
                                Invoice
                            </a>
                        <?php endif; ?>

                        <?php if ($canCancel): ?>
                            <form method="post"
                                action="cancel_order.php"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to cancel this order?');">

                                <input type="hidden" name="order_id" value="<?= $oid ?>">
                                <input type="hidden" name="cancel_reason" value="Cancelled from previous orders page">

                                <button type="submit" class="sip-btn sip-btn-danger">
                                    Cancel
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        </div>

    <?php endwhile; ?>

</div>

<?php include 'footer.php'; ?>