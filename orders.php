<?php
session_start();
include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';

$user_id = (int)ecom_require_user();
include 'header.php';

$latest_q = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' AND order_status!='Cancelled' ORDER BY id DESC LIMIT 1");
$order = $latest_q ? mysqli_fetch_assoc($latest_q) : null;
?>

<?= sip_order_card_css(); ?>

<section class="breadcrumb-section">
    <h1>MY LATEST ORDER</h1>
    <p class="breadcrumb"><a href="index.php">Home</a> <span>| My Orders</span></p>
</section>

<div class="container my-5">
    <div class="sip-order-toolbar">
        <a href="orders.php" class="active">Latest Order</a>
        <a href="previous-orders.php">Previous Orders</a>
        <a href="cancelled-orders.php">Cancelled Orders</a>
        <a href="user_profile.php?tab=track">Track Order</a>
    </div>

    <?php if (!$order): ?>
        <div class="sip-empty">
            <div style="font-size:48px">📦</div>
            <h4>No active order found</h4>
            <p>Your latest active order will appear here. Cancelled orders are available on the Cancelled Orders page.</p>
            <a class="sip-btn sip-btn-primary" href="products.php">Start Shopping</a>
        </div>
    <?php else: ?>

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
        ?>

        <div class="sip-order-card">
            <div class="sip-order-top">
                <div>
                    <h3 class="sip-order-no">Order #<?= sip_h($order['order_no']) ?></h3>
                    <div class="sip-order-meta">
                        Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </div>

                    <?php
                    // ✅ FIXED: COD + ONLINE both support invoice (only if invoice exists + delivered + paid)
                    if (
                        ($order['order_status'] ?? '') === 'Delivered'
                        && !empty($order['invoice_no'])
                        && strtolower(trim($order['payment_status'] ?? '')) === 'paid'
                    ):
                    ?>
                        <div class="sip-order-meta">
                            Invoice: <b><?= sip_h($order['invoice_no']) ?></b>
                        </div>
                    <?php endif; ?>

                </div>

                <span class="sip-pill bg-<?= sip_order_badge($order['order_status']) ?>">
                    <?= sip_h($order['order_status']) ?>
                </span>
            </div>

            <?php while ($item = mysqli_fetch_assoc($items_q)): ?>
                <div class="sip-item">
                    <?php $fld = ($item['item_type']==='project') ? 'projects' : 'products'; ?>
                    <img src="uploads/<?= $fld ?>/<?= sip_h($item['main_image']) ?>" alt="Product">
                    <div>
                        <div class="sip-item-name"><?= sip_h($item['title']) ?></div>
                        <div class="sip-item-sub">
                            ₹<?= format_price((float)$item['price']) ?> × <?= (int)$item['qty'] ?>
                        </div>
                    </div>
                    <?php
                    $qty = (int)$item['qty'];
                    $price = (float)$item['price'];
                    $lineTotal = $price * $qty;
                    ?>

                    <div class="sip-item-total">
                        ₹<?= format_price($lineTotal) ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="sip-order-footer">
                <div class="sip-info-grid">
                    <span>Payment: <b><?= sip_h($order['payment_method']) ?></b></span>
                    <span>Payment Status: <b><?= sip_h($order['payment_status']) ?></b></span>
                    <span>Tracking: <b><?= sip_h($order['tracking_no'] ?: 'N/A') ?></b></span>
                </div>

                <div>
                    <?php
                    $grandTotal = round((float)$order['total_amount'], 2);
                    ?>

                    <div class="sip-total">
                        Total: ₹<?= format_price($grandTotal) ?>
                    </div>

                    <div class="sip-actions mt-2">
                        <a href="order-details.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-outline">
                            View Details
                        </a>

                        <?php
                        // ✅ FIXED INVOICE BUTTON (COD + ONLINE BOTH)
                        if (
                            ($order['order_status'] ?? '') === 'Delivered'
                            && !empty($order['invoice_no'])
                            && strtolower(trim($order['payment_status'] ?? '')) === 'paid'
                        ):
                        ?>
                            <a href="invoice.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-dark">
                                Invoice
                            </a>
                        <?php endif; ?>

                        <?php if ($canCancel): ?>
                            <form method="post" action="cancel_order.php"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to cancel this order?');">

                                <input type="hidden" name="order_id" value="<?= $oid ?>">
                                <input type="hidden" name="cancel_reason" value="Cancelled from latest order page">

                                <button type="submit" class="sip-btn sip-btn-danger">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>