<?php
session_start();
include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';
$user_id = ecom_require_user();
$order = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_no = mysqli_real_escape_string($conn, trim($_POST['order_no'] ?? ''));
    $order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' AND (order_no='$order_no' OR tracking_no='$order_no' OR invoice_no='$order_no') LIMIT 1"));
}
include 'header.php';
?>
<?= sip_order_card_css(); ?>
<style>
    .tracking-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 35px 25px
    }

    .tracking-steps:before {
        content: "";
        position: absolute;
        top: 18px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e5e7eb;
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
        background: #e5e7eb;
        color: #64748b;
        font-weight: 900
    }

    .track-step.done .track-dot {
        background: #ff5a1f;
        color: #fff
    }

    .track-step.done p {
        color: #ff5a1f
    }

    .tracking-steps.cancel .track-step.done .track-dot {
        background: #e11d48
    }

    .tracking-steps.cancel .track-step.done p {
        color: #e11d48
    }

    .track-step p {
        font-weight: 900;
        font-size: 13px;
        margin: 0
    }

    @media(max-width:700px) {
        .tracking-steps {
            overflow: auto;
            gap: 18px;
            margin: 25px 0
        }

        .tracking-steps:before {
            display: none
        }

        .track-step {
            min-width: 80px
        }
    }
</style>
<section class="breadcrumb-section">
    <h1>TRACK ORDER</h1>
    <p class="breadcrumb"><a href="index.php">Home</a> <span>| Track Order</span></p>
</section>
<div class="container my-5 account-layout">
    <aside class="ecom-card account-menu"><a href="user_profile.php"><i class="fa fa-user"></i> Profile</a><a href="manage-addresses.php"><i class="fa fa-location-dot"></i> Addresses</a><a href="orders.php"><i class="fa fa-box"></i> Latest Order</a><a href="previous-orders.php"><i class="fa fa-clock"></i> Previous Orders</a><a href="cancelled-orders.php"><i class="fa fa-ban"></i> Cancelled Orders</a><a class="active" href="track_order.php"><i class="fa fa-truck-fast"></i> Track Order</a></aside>
    <main>
        <div class="ecom-card mb-4">
            <h4 class="ecom-title">Track Your Order</h4>
            <form method="post" class="d-flex gap-2 flex-wrap"><input class="form-control flex-grow-1" name="order_no" placeholder="Enter Order No / Tracking No / Invoice No" required><button class="ecom-btn">Track</button></form>
        </div>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if ($order):
                $currentStatus = $order['order_status'] ?? 'Pending';
                $isSpecial = in_array($currentStatus, ['Cancelled', 'Returned'], true);
                $steps = $isSpecial ? ['Pending', 'Confirmed', 'Packed', 'Shipped', $currentStatus] : ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];
                $idx = array_search($currentStatus, $steps, true);
                if ($idx === false) $idx = 0;
            ?>
                <div class="ecom-card">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="ecom-title mb-1">Order #<?= ecom_h($order['order_no']) ?></h4><?php if (!empty($order['invoice_no'])): ?><small>Invoice: <b><?= ecom_h($order['invoice_no']) ?></b></small><?php endif; ?>
                        </div><span class="badge bg-<?= sip_order_badge($currentStatus) ?> p-2"><?= ecom_h($currentStatus) ?></span>
                    </div>
                    <div class="tracking-steps <?= $isSpecial ? 'cancel' : '' ?>"><?php foreach ($steps as $i => $st): ?><div class="track-step <?= $i <= $idx ? 'done' : '' ?>">
                                <div class="track-dot"><?= $i + 1 ?></div>
                                <p><?= ecom_h($st) ?></p>
                            </div><?php endforeach; ?></div>
                    <p><b>Courier:</b> <?= ecom_h($order['courier_name'] ?: 'Not assigned yet') ?></p>
                    <p><b>Tracking No:</b> <?= ecom_h($order['tracking_no'] ?: 'Not generated yet') ?></p><?php if (!empty($order['expected_delivery_date'])): ?><p><b>Expected Delivery:</b> <?= date('d M Y', strtotime($order['expected_delivery_date'])) ?></p><?php endif; ?><a class="ecom-btn-outline" href="order-details.php?order_id=<?= (int)$order['id'] ?>">View Full Details</a>
                </div>
            <?php else: ?><div class="alert alert-danger">No order found with this number.</div><?php endif; ?>
        <?php endif; ?>
    </main>
</div>
<?php include 'footer.php'; ?>