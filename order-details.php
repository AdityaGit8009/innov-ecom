<?php
session_start();
include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';
$user_id = ecom_require_user();
$order_id = (int) ($_GET['order_id'] ?? 0);
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id' LIMIT 1"));
if (!$order) {
    include 'header.php';
    echo "<div class='container my-5'><div class='alert alert-danger'>Order not found.</div></div>";
    include 'footer.php';
    exit;
}
$items = mysqli_query($conn, "SELECT oi.*, IF(oi.item_type='project', pr.title, p.title) AS title, IF(oi.item_type='project', pr.image, p.main_image) AS main_image FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id AND oi.item_type='product' LEFT JOIN projects pr ON pr.id=oi.product_id AND oi.item_type='project' WHERE oi.order_id='$order_id'");
$history = sip_history_query($conn, $order_id);
include 'header.php';
$currentStatus = $order['order_status'] ?? 'Pending';
$isSpecial = in_array($currentStatus, ['Cancelled', 'Returned'], true);
$steps = $isSpecial ? ['Pending', 'Confirmed', 'Packed', 'Shipped', $currentStatus] : ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];
$currentIndex = array_search($currentStatus, $steps, true);
if ($currentIndex === false)
    $currentIndex = 0;
$customerCanCancel = sip_can_customer_cancel($currentStatus);
?>
<?= sip_order_card_css(); ?>
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1.7fr .9fr;
        gap: 24px
    }

    .detail-card {
        background: var(--white);
        border: 1px solid var(--cultured);
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(15, 23, 42, .06);
        padding: 22px
    }

    .tracking-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 38px 35px 30px
    }

    .tracking-steps:before {
        content: "";
        position: absolute;
        top: 18px;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--cultured);
        border-radius: 10px
    }

    .track-step {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1
    }

    .track-dot {
        width: 38px;
        height: 38px;
        margin: 0 auto 8px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cultured);
        color: var(--sonic-silver);
        font-weight: 900
    }

    .track-step.done .track-dot {
        background: var(--salmon-pink);
        color: var(--white)
    }

    .track-step.done p {
        color: var(--salmon-pink)
    }

    .track-step p {
        font-weight: 900;
        font-size: 13px;
        margin: 0
    }

    .tracking-steps.cancel .track-step.done .track-dot {
        background: var(--salmon-pink)
    }

    .tracking-steps.cancel .track-step.done p {
        color: var(--salmon-pink)
    }

    .history-line {
        border-left: 3px solid var(--salmon-pink);
        padding-left: 14px;
        margin-bottom: 16px
    }

    .history-line.cancel {
        border-color: var(--salmon-pink)
    }

    .btn-cancel-order {
        background: var(--salmon-pink);
        color: var(--white);
        border: 0;
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 900
    }

    .btn-cancel-order:hover {
        background: var(--sandy-brown);
        color: var(--white)
    }

    @media(max-width:900px) {
        .detail-grid {
            grid-template-columns: 1fr
        }

        .tracking-steps {
            margin: 30px 0;
            overflow: auto;
            gap: 18px
        }

        .track-step {
            min-width: 80px
        }

        .tracking-steps:before {
            display: none
        }
    }
</style>
<section class="breadcrumb-section">
    <h1>ORDER DETAILS</h1>
    <p class="breadcrumb"><a href="orders.php">My Orders</a> <span>| <?= ecom_h($order['order_no']) ?></span></p>
</section>
<div class="container my-5">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
        <div class="alert alert-success">Order cancelled successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cannot_cancel'): ?>
        <div class="alert alert-danger">This order cannot be cancelled now.</div><?php endif; ?>
    <div class="detail-card mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="ecom-title mb-1">Order #<?= ecom_h($order['order_no']) ?></h4><small
                    class="text-muted">Placed on
                    <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>

            </div>
            <div class="text-end"><span
                    class="badge bg-<?= sip_order_badge($currentStatus) ?> align-self-start p-2"><?= ecom_h($currentStatus) ?></span><?php if ($customerCanCancel): ?><?php endif; ?>
            </div>
        </div>
        <div class="tracking-steps <?= $isSpecial ? 'cancel' : '' ?>">
            <?php foreach ($steps as $i => $st): ?>
                <div class="track-step <?= $i <= $currentIndex ? 'done' : '' ?>">
                    <div class="track-dot"><?= $i + 1 ?></div>
                    <p><?= ecom_h($st) ?></p>
                </div><?php endforeach; ?>
        </div>
        <div class="alert alert-info mb-0"><b>Courier:</b> <?= ecom_h($order['courier_name'] ?: 'Not assigned yet') ?>
            &nbsp; <b>Tracking No:</b> <?= ecom_h($order['tracking_no'] ?: $order['order_no']) ?>
            <?php if (!empty($order['expected_delivery_date'])): ?> &nbsp; <b>Expected:</b>
                <?= date('d M Y', strtotime($order['expected_delivery_date'])) ?><?php endif; ?>
        </div>
    </div>
    <div class="detail-grid">
        <div class="detail-card">
            <h5 class="ecom-title mb-3">Products</h5><?php while ($it = mysqli_fetch_assoc($items)): ?>
                <div class="sip-item"><img src="uploads/<?= ($it['item_type'] === 'project') ? 'projects' : 'products'; ?>/<?= ecom_h($it['main_image']) ?>" alt="Product">
                    <div>
                        <div class="sip-item-name"><?= ecom_h($it['title']) ?></div>
                        <div class="sip-item-sub">₹<?= number_format((float) $it['price']) ?> × <?= (int) $it['qty'] ?>
                        </div>
                    </div>
                    <?php
                                                            $lineTotal = round(((float)$it['price'] * (int)$it['qty']), 2);
                    ?>

                    <div class="sip-item-total">
                        ₹<?= format_price($lineTotal) ?>
                    </div>
                </div><?php endwhile; ?>
            <?php
            $grandTotal = (float)($order['total_amount'] ?? 0);
            $grandTotal = round($grandTotal, 2);
            ?>

            <h5 class="text-end mt-3">
                Grand Total: ₹<?= format_price($grandTotal) ?>
            </h5>
        </div>
        <div>
            <div class="detail-card mb-4">
                <h5 class="ecom-title">Payment</h5>
                <p class="mb-1">Method: <b><?= ecom_h($order['payment_method']) ?></b></p>
                <p class="mb-1">Status: <b><?= ecom_h($order['payment_status']) ?></b></p>
                <?php if (!empty($order['razorpay_payment_id'])): ?>
                    <p class="mb-1">Transaction: <b><?= ecom_h($order['razorpay_payment_id']) ?></b></p>
                <?php endif; ?>
                <?php
                $status = $order['order_status'] ?? '';
                $invoice = $order['invoice_no'] ?? '';
                $payment = strtolower(trim($order['payment_status'] ?? ''));
                $method = strtoupper(trim($order['payment_method'] ?? ''));

                $showInvoice = false;

                /* ONLINE ORDER RULE */
                if ($method === 'ONLINE') {
                    if ($status === 'Delivered' && !empty($invoice) && $payment === 'paid') {
                        $showInvoice = true;
                    } else {

                        $showInvoice = true;
                    }
                }

                // /* COD ORDER RULE */
                // if ($method === 'COD') {
                //     // COD me payment status irrelevant hota hai
                //     if ($status === 'Delivered') {
                //         $showInvoice = true;
                //     }    
                // }

                if ($showInvoice):
                ?>
                    <a href="invoice.php?order_id=<?= $order_id ?>" class="sip-btn sip-btn-dark mt-2">
                        Download Invoice
                    </a>
                <?php endif; ?>
            </div>
            <div class="detail-card">
                <h5 class="ecom-title">Tracking History</h5>
                <?php if ($history && mysqli_num_rows($history)):
                    while ($h = mysqli_fetch_assoc($history)): ?>
                        <div class="history-line <?= $h['status'] == 'Cancelled' ? 'cancel' : '' ?>">
                            <b><?= ecom_h($h['status']) ?></b><br><small
                                class="text-muted"><?= date('d M Y, h:i A', strtotime($h['created_at'])) ?></small><?php if (!empty($h['note'])): ?>
                                <p class="mb-0 small"><?= ecom_h($h['note']) ?></p><?php endif; ?>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p class="text-muted">No tracking update yet.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>