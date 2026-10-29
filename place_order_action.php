<?php
session_start();

include 'config/db.php';
include 'config/razorpay.php';
require_once 'secure_helpers.php';
// require_once 'ecom_pricing.php';

include 'header.php';

$user_id = ecom_require_user();

$address_id = (int) ($_POST['address_id'] ?? $_SESSION['checkout_address_id'] ?? 0);

$payment_method = strtoupper($_POST['payment_method'] ?? $_SESSION['checkout_payment'] ?? 'COD');

$razorpay_order_id = trim($_POST['razorpay_order_id'] ?? '');

$razorpay_payment_id = trim($_POST['razorpay_payment_id'] ?? '');

$razorpay_signature = trim($_POST['razorpay_signature'] ?? '');

$is_cod = $payment_method === 'COD' || isset($_POST['cod_order']);

/* =========================
   VALIDATION
========================= */

if ($address_id <= 0) {

    echo "<div class='container my-5'>
            <div class='alert alert-danger'>
                Delivery address missing.
            </div>
          </div>";

    include 'footer.php';
    exit;
}

/* =========================
   RAZORPAY VERIFY
========================= */

if (!$is_cod) {

    $sessionOrderId = $_SESSION['razorpay_order_id'] ?? '';

    if (
        !$razorpay_order_id ||
        !$razorpay_payment_id ||
        !$razorpay_signature ||
        $razorpay_order_id !== $sessionOrderId
    ) {

        echo "<div class='container my-5'>
                <div class='alert alert-danger'>
                    Payment verification failed.
                </div>
              </div>";

        include 'footer.php';
        exit;
    }

    $expectedSignature = hash_hmac(
        'sha256',
        $razorpay_order_id . '|' . $razorpay_payment_id,
        RAZORPAY_KEY_SECRET
    );

    if (!hash_equals($expectedSignature, $razorpay_signature)) {

        echo "<div class='container my-5'>
                <div class='alert alert-danger'>
                    Payment signature mismatch.
                </div>
              </div>";

        include 'footer.php';
        exit;
    }
}

/* =========================
   USER + ADDRESS
========================= */

$user = ecom_get_user($conn, $user_id);

$address = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM customer_addresses 
         WHERE id='$address_id' 
         AND customer_id='$user_id' 
         LIMIT 1"
    )
);

if (!$address) {

    echo "<div class='container my-5'>
            <div class='alert alert-danger'>
                Invalid delivery address.
            </div>
          </div>";

    include 'footer.php';
    exit;
}

/* =========================
   CART DATA
========================= */

$cart = mysqli_query(
    $conn,
    "SELECT 
        c.item_type,
        c.qty,
        c.product_id,
        IF(c.item_type='project', pr.title, p.title) AS title,
        IF(c.item_type='project', pr.image, p.main_image) AS main_image,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge,
        IF(c.item_type='project', 100, p.stock) AS stock
     FROM cart c
     LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
     LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
     WHERE c.user_id='$user_id'
     AND (
        (c.item_type = 'product' AND p.status = 1) OR
        (c.item_type = 'project' AND pr.status = 1)
     )"
);

if (!$cart || mysqli_num_rows($cart) == 0) {

    echo "<div class='container my-5'>
            <div class='alert alert-warning'>
                Cart is empty.
            </div>
          </div>";

    include 'footer.php';
    exit;
}

$items = [];

$subtotal = 0;

$taxTotal = 0;

$productDeliveryTotal = 0;

$stockErrors = [];

while ($row = mysqli_fetch_assoc($cart)) {

    $qty = (int) $row['qty'];

    $stock = (int) ($row['stock'] ?? 0);

    if ($qty <= 0 || $stock < $qty) {

        $stockErrors[] =
            ($row['title'] ?? 'Product') .
            ' (Available: ' . $stock .
            ', Required: ' . $qty . ')';
    }

    $items[] = $row;

    $price = (float)($row['price'] ?? 0);

    $taxPercent = (float)($row['tax_percentage'] ?? 0);

    /*
    Product price already GST included
*/

    $lineTotal = $price * $qty;

    /*
    Included GST sirf display ke liye
*/

    $includedTax = 0;

    if ($taxPercent > 0) {

        $includedTax =
            $lineTotal -
            ($lineTotal / (1 + ($taxPercent / 100)));
    }

    $taxAmount = ($lineTotal * $taxPercent) / (100 + $taxPercent);

    $baseAmount = $lineTotal - $taxAmount;

    $subtotal += $baseAmount;

    $taxTotal += $taxAmount;

    /*
    delivery
*/

    $productDeliveryTotal +=
        (float)($row['delivery_charge'] ?? 0) * $qty;
}

if (!empty($stockErrors)) {

    echo "<div class='container my-5'>
            <div class='alert alert-danger'>
                <b>Stock not available:</b><br>" .
        implode('<br>', array_map('ecom_h', $stockErrors)) .
        "
            </div>

            <a href='cart.php' class='btn btn-primary'>
                Back to Cart
            </a>
          </div>";

    include 'footer.php';
    exit;
}

/* =========================
   TOTALS
========================= */

$delivery = (float)$productDeliveryTotal;

$grand_total = $subtotal + $taxTotal + $delivery;

/* =========================
   VERIFY AMOUNT
========================= */

if (!$is_cod) {

    $sessionAmount = (float) ($_SESSION['razorpay_amount'] ?? 0);

    if (
        $sessionAmount <= 0 ||
        abs($sessionAmount - $grand_total) > 0.50
    ) {

        echo "<div class='container my-5'>
                <div class='alert alert-danger'>
                    Amount mismatch.
                </div>
                
              </div>";

        include 'footer.php';
        exit;
    }
}

/* =========================
   ORDER DETAILS
========================= */

$order_no = 'ORD' . date('YmdHis') . rand(100, 999);

$invoice_no = $is_cod
    ? ('INV' . date('Ymd') . rand(1000, 9999))
    : '';

$tracking_no = $order_no;

$payment_status = $is_cod ? 'Pending' : 'Paid';

$order_status = 'Pending';

$cname = mysqli_real_escape_string($conn, $user['name'] ?? '');

$cemail = mysqli_real_escape_string($conn, $user['email'] ?? '');

$cphone = mysqli_real_escape_string($conn, $user['mobile'] ?? '');

$pm = mysqli_real_escape_string(
    $conn,
    $is_cod ? 'COD' : 'ONLINE'
);

$pid = mysqli_real_escape_string($conn, $razorpay_payment_id);

$roid = mysqli_real_escape_string($conn, $razorpay_order_id);

$rsig = mysqli_real_escape_string($conn, $razorpay_signature);

/* =========================
   SAVE ORDER
========================= */

mysqli_begin_transaction($conn);

try {

    $insertOrder = mysqli_query(
        $conn,
        "INSERT INTO orders (

            order_no,
            invoice_no,
            tracking_no,
            user_id,
            address_id,
            customer_name,
            customer_email,
            customer_phone,
            payment_method,
            payment_status,
            order_status,
            total_amount,
            tax_amount,
            delivery_charge,
            razorpay_order_id,
            razorpay_payment_id,
            razorpay_signature

        ) VALUES (

            '$order_no',
            '$invoice_no',
            '$tracking_no',
            '$user_id',
            '$address_id',
            '$cname',
            '$cemail',
            '$cphone',
            '$pm',
            '$payment_status',
            '$order_status',
            '$grand_total',
            '$taxTotal',
            '$delivery',
            '$roid',
            '$pid',
            '$rsig'
        )"
    );

    if (!$insertOrder) {

        throw new Exception(mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    foreach ($items as $item) {

        $qty = (int) $item['qty'];

        $price = (float)($item['price'] ?? 0);

        $item_tax_percent =
            (float)($item['tax_percentage'] ?? 0);

        /*
    Included GST amount
*/

        $item_tax_amount = 0;

        if ($item_tax_percent > 0) {

            $lineTotal = $price * $qty;

            $item_tax_amount =
                $lineTotal -
                ($lineTotal / (1 + ($item_tax_percent / 100)));
        }

        $prod = (int) $item['product_id'];

        $item_type = mysqli_real_escape_string($conn, $item['item_type']);

        mysqli_query(
            $conn,
            "INSERT INTO order_items (

                order_id,
                item_type,
                product_id,
                qty,
                price,
                tax_percentage,
                tax_amount

            ) VALUES (

                '$order_id',
                '$item_type',
                '$prod',
                '$qty',
                '$price',
                '$item_tax_percent',
                '$item_tax_amount'
            )"
        );

        if ($item['item_type'] === 'product') {
            mysqli_query(
                $conn,
                "UPDATE products 
                 SET stock = GREATEST(stock-$qty,0)
                 WHERE id='$prod'"
            );
        }
    }

    /* TRACKING HISTORY */

    mysqli_query(
        $conn,
        "INSERT INTO order_status_history (
            order_id,
            status,
            note
        ) VALUES (
            '$order_id',
            'Pending',
            'Order placed successfully'
        )"
    );

    /* SEND NOTIFICATION */
    try {
        require_once 'includes/NotificationService.php';
        $ns = new NotificationService();
        $ns->sendOrderConfirmation($cphone, $cemail, [
            'order_no' => $order_no,
            'total' => format_price($grand_total),
            'customer_name' => $cname ?? ($user['name'] ?? 'Customer'),
            'payment_method' => $pm,
            'payment_status' => $payment_status,
            'items' => $items
        ]);
    } catch (Exception $e) {
        // Log notification error but don't fail order
        error_log("Notification failed: " . $e->getMessage());
    }

    /* TRANSACTION */

    mysqli_query(
        $conn,
        "INSERT INTO transactions (

            order_id,
            order_no,
            payment_method,
            amount,
            transaction_id,
            razorpay_order_id,
            razorpay_signature,
            payment_status

        ) VALUES (

            '$order_id',
            '$order_no',
            '$pm',
            '$grand_total',
            '$pid',
            '$roid',
            '$rsig',
            '$payment_status'
        )"
    );

    /* CLEAR CART */

    mysqli_query(
        $conn,
        "DELETE FROM cart WHERE user_id='$user_id'"
    );

    mysqli_commit($conn);
} catch (Exception $e) {

    mysqli_rollback($conn);

    echo "<div class='container my-5'>
            <div class='alert alert-danger'>
                Order not saved :
                " . ecom_h($e->getMessage()) . "
            </div>
          </div>";

    include 'footer.php';

    exit;
}

/* =========================
   CLEAR SESSION
========================= */

unset(
    $_SESSION['checkout_address_id'],
    $_SESSION['checkout_payment'],
    $_SESSION['razorpay_order_id'],
    $_SESSION['razorpay_amount']
);

?>

<style>
    .success-wrapper {
        max-width: 1100px;
        margin: 50px auto;
        padding: 0 15px;
    }

    .success-card {
        background: #fff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
        border: 1px solid #edf2f7;
    }

    .success-top {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        padding: 60px 30px 110px;
        text-align: center;
        color: #fff;
        position: relative;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        font-size: 60px;
        color: #16a34a;
        box-shadow: 0 12px 40px rgba(255, 255, 255, .3);
    }

    .success-top h2 {
        font-size: 40px;
        font-weight: 800;
        margin-top: 25px;
    }

    .success-top p {
        font-size: 16px;
        opacity: .95;
    }

    .success-body {
        margin-top: -70px;
        position: relative;
        /* z-index: 5; */
        padding: 0 30px 35px;
    }

    .order-summary {
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #eef2f7;
        box-shadow: 0 12px 40px rgba(15, 23, 42, .08);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        align-items: stretch;
    }

    .summary-box {
        background: #f8fafc;
        border-radius: 18px;
        padding: 10px;
        border: 1px solid #e2e8f0;
    }

    .summary-box-add {
        background: #f8fafc;
        border-radius: 18px;
        padding: 18px;
        border: 1px solid #e2e8f0;

        /* FULL REMAINING WIDTH */
        grid-column: 2 / -1;

        width: 100%;
        min-height: 100%;
    }

    .summary-box-add h5 {
        font-weight: 800;
        margin-bottom: 15px;
        color: #166534;
        font-size: 18px;
    }

    .summary-box-add p {
        margin-bottom: 6px;
        color: #14532d;
        font-size: 16px;
        line-height: 1.6;
    }

    .summary-box span {
        display: block;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .summary-box b {
        color: #0f172a;
        font-size: 16px;
        word-break: break-word;
    }

    .success-total {
        background: #fff7ed;
        border: 1px dashed #fb923c;
    }

    .success-total b {
        color: #ea580c;
        font-size: 24px;
    }

    /* MOBILE RESPONSIVE */

    @media(max-width:768px) {

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .summary-box-add {
            grid-column: auto;
        }

    }

    .product-area {
        margin-top: 35px;
    }

    .product-area h4 {
        font-weight: 800;
        margin-bottom: 22px;
    }

    .product-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 22px;
        justify-content: center;
        align-items: stretch;
    }

    .product-card {
        width: 100%;
        max-width: 300px;
        min-width: 260px;
        flex: 0 0 300px;
        display: flex;
        flex-direction: column;
    }

    .product-image {
        width: 100%;
        height: 260px;
        background: #f8fafc;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.04);
    }

    .product-info {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.5;
        min-height: 48px;
        margin-bottom: 10px;

        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-meta {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 6px;
    }

    .product-price {
        margin-top: auto;
        font-size: 22px;
        font-weight: 800;
        color: #ff5a1f;
    }

    .success-actions {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 35px;
    }

    .btn-order {
        min-width: 220px;
        padding: 15px 20px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        text-align: center;
        transition: .25s;
    }

    .btn-primary-order {
        background: #ff5a1f;
        color: #fff;
    }

    .btn-primary-order:hover {
        background: #ea580c;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-light-order {
        background: #fff;
        border: 1px solid #dbe2ea;
        color: #111827;
    }

    .btn-light-order:hover {
        background: #f8fafc;
        color: #111827;
        transform: translateY(-2px);
    }

    .delivery-box {
        margin-top: 30px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 18px;
        padding: 18px;
    }

    .delivery-box h5 {
        font-weight: 800;
        margin-bottom: 15px;
        color: #166534;
    }

    .delivery-box p {
        margin-bottom: 6px;
        color: #14532d;
    }

    @media(max-width:768px) {

        .success-top {
            padding: 40px 20px 90px;
        }

        .success-top h2 {
            font-size: 28px;
        }

        .success-body {
            padding: 0 15px 25px;
        }

        .order-summary {
            padding: 20px;
        }

        .btn-order {
            width: 100%;
        }

        .product-grid {
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .product-image {
            height: 170px;
        }

        .product-title {
            font-size: 14px;
            min-height: 42px;
        }

        .product-price {
            font-size: 18px;
        }
    }

    @media(max-width:520px) {

        .product-grid {
            grid-template-columns: 1fr;
        }

        .product-image {
            height: 220px;
        }
    }
</style>

<section class="success-wrapper">

    <div class="success-card">

        <div class="success-top">

            <div class="success-icon">
                ✓
            </div>

            <h2>Order Placed Successfully!</h2>

            <p>
                Thank you for shopping with us.
                Your order has been placed successfully.
            </p>

        </div>

        <div class="success-body">

            <div class="order-summary">

                <h4 class="fw-bold mb-4">
                    Order Summary
                </h4>

                <div class="summary-grid">

                    <div class="summary-box">
                        <span>Order Number</span>
                        <b><?= ecom_h($order_no) ?></b>
                    </div>

                    <?php if ($invoice_no): ?>

                        <div class="summary-box">
                            <span>Invoice Number</span>
                            <b><?= ecom_h($invoice_no) ?></b>
                        </div>

                    <?php endif; ?>

                    <div class="summary-box">
                        <span>Payment Method</span>
                        <b><?= ecom_h($pm) ?></b>
                    </div>

                    <div class="summary-box">
                        <span>Payment Status</span>
                        <b><?= ecom_h($payment_status) ?></b>
                    </div>

                    <?php if (!$is_cod): ?>

                        <div class="summary-box">
                            <span>Payment ID</span>
                            <b><?= ecom_h($razorpay_payment_id) ?></b>
                        </div>

                    <?php endif; ?>

                    <div class="summary-box success-total">
                        <span>Grand Total</span>
                        <b>₹<?= format_price($grand_total) ?></b>
                    </div>
                    <div class="summary-box-add">
                        <!-- DELIVERY ADDRESS -->

                        <!-- <div class="delivery-box"> -->

                        <h5>
                            Delivery Address
                        </h5>

                        <p>
                            <b>
                                <?= ecom_h($address['full_name'] ?? $user['name'] ?? 'Customer') ?>
                            </b>
                        </p>

                        <p>
                            <?=
                            ecom_h(
                                trim(
                                    ($address['house_no'] ?? '') . ' ' .
                                        ($address['address_line1'] ?? '') . ' ' .
                                        ($address['address_line2'] ?? '')
                                )
                            )
                            ?>
                        </p>

                        <p>
                            <?= ecom_h($address['city'] ?? '') ?>,
                            <?= ecom_h($address['state'] ?? '') ?> -
                            <?= ecom_h($address['pincode'] ?? '') ?>
                        </p>

                        <p>
                            Mobile :
                            <?= ecom_h($address['mobile'] ?? $address['phone'] ?? $user['mobile'] ?? 'N/A') ?>
                        </p>

                    </div>
                    <!-- </div> -->

                </div>

                <!-- ACTION BUTTONS -->

                <div class="success-actions">

                    <a href="user_profile.php?tab=latest" class="btn-order btn-primary-order">

                        View Order Details

                    </a>

                    <a href="products.php" class="btn-order btn-light-order">

                        Continue Shopping

                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include 'footer.php'; ?>