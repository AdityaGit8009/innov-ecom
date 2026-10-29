<?php
session_start();

include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id || $user_id == 1) {
    echo "<script>alert('Please login to access'); location.href='login.php';</script>";
    exit;
}

$user_id = (int)$user_id;

include 'header.php';

$orders_q = mysqli_query($conn, "
    SELECT * 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status = 'Cancelled' 
    ORDER BY id DESC
");
?>

<?= sip_order_card_css(); ?>

<style>
    .sip-order-card {
        overflow: hidden;
    }

    .cancelled-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 18px;
        margin-top: 18px;
        border-top: 1px solid #edf2f7;
        padding-top: 18px;
    }

    .cancelled-products-grid.single-product {
        grid-template-columns: 300px;
        justify-content: flex-start;
    }

    .cancelled-product-card {
        width: 100%;
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .cancelled-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
    }

    .cancelled-badge-small {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #e11d48;
        color: #ffffff;
        font-size: 11px;
        padding: 5px 9px;
        border-radius: 999px;
        font-weight: 700;
        z-index: 2;
    }

    .cancelled-product-img-wrap {
        width: 100%;
        height: 190px;
        border-radius: 13px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cancelled-product-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .cancelled-product-body {
        padding-top: 12px;
    }

    .cancelled-product-title {
        font-size: 15px;
        font-weight: 800;
        color: #06142e;
        margin-bottom: 28px;
        line-height: 1.4;
        min-height: 42px;
    }

    .cancelled-product-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .cancelled-product-info b {
        color: #06142e;
        font-weight: 800;
    }

    .cancelled-product-total {
        margin-top: 12px;
        padding: 10px;
        border-radius: 12px;
        background: #fff1f2;
        color: #be123c;
        font-weight: 900;
        text-align: center;
        font-size: 15px;
    }

    .cancelled-order-summary {
        margin-top: 18px;
        border-top: 1px dashed #e5e7eb;
        padding-top: 16px;
    }

    @media (min-width: 1200px) {
        .cancelled-products-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .cancelled-products-grid.single-product {
            grid-template-columns: 300px;
        }
    }

    @media (min-width: 768px) and (max-width: 1199px) {
        .cancelled-products-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .cancelled-products-grid.single-product {
            grid-template-columns: 300px;
        }
    }

    @media (max-width: 767px) {
        .cancelled-products-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .cancelled-products-grid.single-product {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {
        .cancelled-products-grid {
            grid-template-columns: 1fr;
        }

        .cancelled-product-img-wrap {
            height: 210px;
        }
    }
</style>

<section class="breadcrumb-section">
    <h1>CANCELLED ORDERS</h1>
    <p class="breadcrumb">
        <a href="orders.php">My Orders</a>
        <span>| Cancelled Orders</span>
    </p>
</section>

<div class="container my-5">

    <div class="sip-order-toolbar">
        <a href="orders.php">Latest Order</a>
        <a href="previous-orders.php">Previous Orders</a>
        <a href="cancelled-orders.php" class="active">Cancelled Orders</a>
        <a href="user_profile.php?tab=track">Track Order</a>
    </div>

    <?php if (!$orders_q || mysqli_num_rows($orders_q) == 0): ?>

        <div class="sip-empty">
            <h4>No cancelled orders found</h4>
            <p>Customer or admin cancelled orders will appear here.</p>
        </div>

    <?php endif; ?>

    <?php while ($order = mysqli_fetch_assoc($orders_q)): ?>

        <?php
        $oid = (int)$order['id'];

        $items_q = mysqli_query($conn, "
            SELECT 
                oi.*, 
                IF(oi.item_type='project', pr.title, p.title) AS title, 
                IF(oi.item_type='project', pr.image, p.main_image) AS main_image 
            FROM order_items oi 
            LEFT JOIN products p ON p.id = oi.product_id AND oi.item_type='product'
            LEFT JOIN projects pr ON pr.id = oi.product_id AND oi.item_type='project'
            WHERE oi.order_id = '$oid'
        ");

        $items = [];

        if ($items_q && mysqli_num_rows($items_q) > 0) {
            while ($row = mysqli_fetch_assoc($items_q)) {
                $items[] = $row;
            }
        }

        $product_grid_class = count($items) == 1 ? 'single-product' : '';
        ?>

        <div class="sip-order-card" style="border-left:5px solid #e11d48">

            <div class="sip-order-top">
                <div>
                    <h3 class="sip-order-no">
                        Order #<?= sip_h($order['order_no']) ?>
                    </h3>

                    <div class="sip-order-meta">
                        Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </div>

                    <?php if (!empty($order['cancelled_at'])): ?>
                        <div class="sip-order-meta">
                            Cancelled on
                            <b><?= date('d M Y, h:i A', strtotime($order['cancelled_at'])) ?></b>
                        </div>
                    <?php endif; ?>

                    <div class="sip-order-meta">
                        Cancelled By:
                        <b><?= sip_h($order['cancelled_by'] ?: 'N/A') ?></b>
                    </div>

                    <?php if (!empty($order['cancel_reason'])): ?>
                        <div class="sip-order-meta">
                            Reason:
                            <b><?= sip_h($order['cancel_reason']) ?></b>
                        </div>
                    <?php endif; ?>
                </div>

                <span class="sip-pill bg-danger">Cancelled</span>
            </div>

            <div class="cancelled-products-grid <?= $product_grid_class ?>">

                <?php if (!empty($items)): ?>

                    <?php foreach ($items as $item): ?>

                        <?php
                        $price = isset($item['price']) ? (float)$item['price'] : 0;
                        $qty   = isset($item['qty']) ? (int)$item['qty'] : 0;

                        $item_total = $price * $qty;

                        $product_image = !empty($item['main_image'])
                            ? 'uploads/' . ($item['item_type'] === 'project' ? 'projects' : 'products') . '/' . $item['main_image']
                            : 'assets/images/no-image.png';

                        $product_title = $item['title'] ?? 'Product not available';
                        ?>

                        <div class="cancelled-product-card">
                            <span class="cancelled-badge-small">Cancelled</span>

                            <div class="cancelled-product-img-wrap">
                                <img
                                    src="<?= sip_h($product_image) ?>"
                                    alt="<?= sip_h($product_title) ?>"
                                    class="cancelled-product-img">
                            </div>

                            <div class="cancelled-product-body">
                                <div class="cancelled-product-title">
                                    <?= sip_h($product_title) ?>
                                </div>

                                <div class="cancelled-product-info">
                                    <span>Price</span>
                                    <b>₹<?= format_price($price) ?></b>
                                </div>

                                <div class="cancelled-product-info">
                                    <span>Quantity</span>
                                    <b><?= $qty ?></b>
                                </div>

                                <div class="cancelled-product-total">
                                    Total: ₹<?= format_price($item_total) ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="sip-empty" style="grid-column:1/-1;">
                        <h4>No products found</h4>
                        <p>This cancelled order has no product records.</p>
                    </div>

                <?php endif; ?>

            </div>

            <div class="sip-order-footer cancelled-order-summary">
                <div class="sip-info-grid">
                    <span>
                        Payment:
                        <b><?= sip_h($order['payment_method']) ?></b>
                    </span>

                    <span>
                        Payment Status:
                        <b><?= sip_h($order['payment_status']) ?></b>
                    </span>

                    <span>
                        Invoice:
                        <b><?= sip_h($order['invoice_no'] ?: 'N/A') ?></b>
                    </span>
                </div>

                <div>
                    <div class="sip-total">
                        Total: ₹<?= format_price((float)$order['total_amount']) ?>
                    </div>

                    <div class="sip-actions mt-2">
                        <a href="order-details.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-outline">
                            View Details
                        </a>

                        <?php if (!empty($order['invoice_no']) && ($order['payment_status'] ?? '') === 'Paid'): ?>
                            <a href="invoice.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-dark">
                                Invoice
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    <?php endwhile; ?>

</div>

<?php include 'footer.php'; ?>