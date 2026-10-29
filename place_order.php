<?php
include 'config/db.php';
include 'config/razorpay.php';
require_once 'secure_helpers.php';
include 'header.php';

$user_id        = ecom_require_user();
$address_id     = (int)($_SESSION['checkout_address_id'] ?? 0);
$payment_method = strtoupper($_SESSION['checkout_payment'] ?? 'COD');

if ($address_id <= 0) {

    echo "<script>alert('Please select delivery address'); location.href='checkout.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT name,email,mobile
    FROM users
    WHERE id='$user_id'
    LIMIT 1
"));

$address = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM customer_addresses
    WHERE id='$address_id'
    AND customer_id='$user_id'
    LIMIT 1
"));

if (!$address) {

    echo "<script>alert('Selected address is invalid'); location.href='checkout.php';</script>";
    exit;
}

/* =========================
   CART ITEMS
========================= */

$cart = mysqli_query($conn, "
    SELECT
        c.qty,
        c.item_type,
        c.product_id,
        IF(c.item_type='project', pr.title, p.title) AS title,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge,
        IF(c.item_type='project', 100, p.stock) AS stock,
        IF(c.item_type='project', pr.image, p.main_image) AS main_image
    FROM cart c
    LEFT JOIN products p ON p.id=c.product_id AND c.item_type='product'
    LEFT JOIN projects pr ON pr.id=c.product_id AND c.item_type='project'
    WHERE c.user_id='$user_id'
    AND (
        (c.item_type = 'product' AND p.status = 1) OR
        (c.item_type = 'project' AND pr.status = 1)
    )
");

$items = [];

$subtotal             = 0;
$taxTotal             = 0;
$productDeliveryTotal = 0;

$stockErrors = [];

while ($c = mysqli_fetch_assoc($cart)) {

    $qty              = (int)$c['qty'];
    $stock            = (int)$c['stock'];
    $price            = (float)$c['price'];
    $old_price        = (float)$c['old_price'];
    $tax_percentage   = (float)$c['tax_percentage'];
    $delivery_charge  = (float)$c['delivery_charge'];

    if ($qty <= 0 || $stock < $qty) {

        $stockErrors[] =
            ($c['title'] ?? 'Product') .
            ' (Available: ' . $stock .
            ', Required: ' . $qty . ')';
    }

    /* PRICE ALREADY TAX INCLUDED */

    $lineTotal = $price * $qty;

    /* INCLUDED GST निकालना */
    $taxAmount = ($lineTotal * $tax_percentage) / (100 + $tax_percentage);

    /* SUBTOTAL WITHOUT GST */
    $baseAmount = $lineTotal - $taxAmount;

    $subtotal += $baseAmount;

    $taxTotal += $taxAmount;

    $productDeliveryTotal += ($delivery_charge * $qty);

    $items[] = [

        'product_id'       => $c['product_id'],
        'title'            => $c['title'],
        'price'            => $price,
        'old_price'        => $old_price,
        'qty'              => $qty,
        'stock'            => $stock,
        'tax_percentage'   => $tax_percentage,
        'delivery_charge'  => $delivery_charge,
        'line_total'       => $lineTotal,
        'base_amount'      => $baseAmount,
        'tax_amount'       => $taxAmount,
        'image'            => 'uploads/' . ($c['item_type'] === 'project' ? 'projects' : 'products') . '/' . $c['main_image']
    ];
}

if (empty($items)) {

    echo "<script>alert('Cart is empty'); location.href='cart.php';</script>";
    exit;
}

$delivery   = (float)$productDeliveryTotal;
/* TOTAL = PRODUCT PRICE + DELIVERY */

$grand_total = 0;

foreach ($items as $it) {

    $grand_total += ($it['price'] * $it['qty']);
}

$grand_total += $delivery;

$deliveryQuote = [

    'serviceable' => true,
    'charge'      => $delivery,
    'partner'     => 'Product Delivery Charge',
    // 'message'     => 'Product-wise delivery charge'
];
?>

<section class="breadcrumb-section">

    <h1>PLACE ORDER</h1>

    <p class="breadcrumb">
        <a href="checkout.php">Checkout</a>
        <span>| Place Order</span>
    </p>

</section>

<section class="container my-5 place-order-page">

    <?php if (!empty($stockErrors)): ?>

        <div class="alert alert-danger">

            <b>Stock not available:</b><br>

            <?= implode('<br>', array_map('ecom_h', $stockErrors)) ?>

        </div>

        <a href="cart.php" class="btn btn-primary">
            Back to Cart
        </a>

    <?php else: ?>

        <form method="POST" action="place_order_action.php" id="codForm">

            <input type="hidden" name="address_id" value="<?= $address_id ?>">

            <input
                type="hidden"
                name="payment_method"
                value="<?= ecom_h($payment_method) ?>">

            <div class="row g-4">

                <!-- LEFT -->
                <div class="col-lg-7">

                    <!-- CUSTOMER -->
                    <div class="card border-0 shadow-sm mb-3 custom-card">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Customer Details
                            </h5>

                            <p class="mb-2">
                                <b>Name:</b>
                                <?= ecom_h($user['name'] ?? '') ?>
                            </p>

                            <p class="mb-2">
                                <b>Email:</b>
                                <?= ecom_h($user['email'] ?? '') ?>
                            </p>

                            <p class="mb-0">
                                <b>Mobile:</b>
                                <?= ecom_h($user['mobile'] ?? '') ?>
                            </p>

                        </div>

                    </div>

                    <!-- ADDRESS -->
                    <div class="card border-0 shadow-sm mb-3 custom-card">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Delivery Address
                            </h5>

                            <p class="mb-0">

                                <?= ecom_h($address['name']) ?><br>

                                <?= ecom_h($address['city']) ?>,
                                <?= ecom_h($address['state']) ?><br>

                                Pincode:
                                <?= ecom_h($address['pincode']) ?><br>

                                Phone:
                                <?= ecom_h($address['phone']) ?>

                            </p>

                        </div>

                    </div>

                    <!-- PAYMENT -->
                    <div class="card border-0 shadow-sm custom-card">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Payment Method
                            </h5>

                            <div class="payment-method-box">

                                <?= $payment_method === 'COD'
                                    ? 'Cash on Delivery'
                                    : 'Online Payment - Razorpay'; ?>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-5">

                    <div class="card border-0 shadow-sm order-summary-card">

                        <div class="card-body">

                            <h4 class="fw-bold mb-4">
                                Order Summary
                            </h4>

                            <?php foreach ($items as $c): ?>

                                <?php
                                $lineTotal = (float)$c['line_total'];
                                ?>

                                <div class="order-product">

                                    <div class="d-flex gap-3">

                                        <img
                                            src="<?= ecom_h($c['image']) ?>"
                                            class="order-product-img"
                                            alt="Product">

                                        <div class="flex-grow-1">

                                            <h6 class="mb-1 fw-bold">
                                                <?= ecom_h($c['title']) ?>
                                            </h6>

                                            <div class="small text-muted mb-1">
                                                Qty:
                                                <?= (int)$c['qty'] ?>
                                            </div>

                                            <div class="product-price">

                                                ₹<?= format_price((float)$c['price']) ?>

                                                <?php if ((float)$c['old_price'] > (float)$c['price']): ?>

                                                    <del>
                                                        ₹<?= format_price((float)$c['old_price']) ?>
                                                    </del>

                                                <?php endif; ?>

                                            </div>

                                        </div>



                                    </div>

                                </div>

                            <?php endforeach; ?>

                            <!-- TOTALS -->
                            <div class="summary-totals">

                                <div class="summary-line">

                                    <span>Subtotal</span>

                                    <span>
                                        ₹<?= format_price($subtotal) ?>
                                    </span>

                                </div>

                                <div class="summary-line">

                                    <span>Tax</span>

                                    <span>
                                        ₹<?= format_price($taxTotal) ?>
                                    </span>

                                </div>

                                <div class="summary-line">

                                    <span>Delivery</span>

                                    <span>
                                        ₹<?= format_price($delivery) ?>
                                    </span>

                                </div>

                                <!-- <div class="delivery-msg">

                                    <?= ecom_h(
                                        $deliveryQuote['partner'] .
                                            ' - ' .
                                            $deliveryQuote['message']
                                    ) ?>

                                </div> -->

                                <div class="summary-final">

                                    <span>Total</span>

                                    <span>
                                        ₹<?= format_price($grand_total) ?>
                                    </span>

                                </div>

                            </div>

                            <!-- BUTTON -->
                            <?php if ($payment_method === 'COD'): ?>

                                <button
                                    type="submit"
                                    name="cod_order"
                                    value="1"
                                    class="btn btn-success w-100 place-btn mt-3">

                                    Place COD Order

                                </button>

                            <?php else: ?>

                                <button
                                    type="button"
                                    class="btn btn-success w-100 place-btn mt-3"
                                    id="rzpPayBtn">

                                    Pay Securely with Razorpay

                                </button>

                                <div
                                    id="paymentMsg"
                                    class="small text-danger mt-2">
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    <?php endif; ?>

</section>

<style>
    .custom-card,
    .order-summary-card {

        border-radius: 18px;
        overflow: hidden;

        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.06),
            0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .custom-card .card-body,
    .order-summary-card .card-body {

        padding: 22px;
    }

    .payment-method-box {

        padding: 14px 16px;

        border-radius: 12px;

        background: #fff7ed;
        border: 1px solid #fed7aa;

        color: #c2410c;
        font-weight: 700;
    }

    .order-product {

        padding-bottom: 16px;
        margin-bottom: 16px;

        border-bottom: 1px solid #f1f5f9;
    }

    .order-product:last-child {

        margin-bottom: 0;
    }

    .order-product-img {

        width: 75px;
        height: 75px;

        object-fit: contain;

        border-radius: 12px;
        border: 1px solid #eee;

        background: #fff;
        padding: 5px;
    }

    .product-price {

        color: #ef662f;
        font-size: 16px;
        font-weight: 700;
    }

    .product-price del {

        color: #9ca3af;
        font-size: 13px;
        margin-left: 6px;
    }

    .summary-totals {

        margin-top: 25px;
        padding-top: 18px;

        border-top: 2px dashed #e5e7eb;
    }

    .summary-line,
    .summary-final {

        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 14px;
    }

    .summary-line {

        color: #374151;
        font-size: 15px;
    }

    .summary-final {

        margin-top: 20px;
        padding-top: 16px;

        border-top: 1px dashed #d1d5db;

        font-size: 24px;
        font-weight: 800;

        color: #111827;
    }

    .delivery-msg {

        padding: 10px 12px;

        background: #fff7ed;
        border: 1px dashed #fdba74;

        border-radius: 10px;

        font-size: 12px;
        color: #9a3412;

        margin-top: 5px;
    }

    .place-btn {

        height: 52px;

        border: none;
        border-radius: 14px;

        font-size: 16px;
        font-weight: 700;

        background:
            linear-gradient(135deg,
                #16a34a,
                #22c55e);

        transition: 0.25s ease;
    }

    .place-btn:hover {

        transform: translateY(-2px);

        background:
            linear-gradient(135deg,
                #15803d,
                #16a34a);

        box-shadow:
            0 12px 25px rgba(34, 197, 94, 0.25);
    }

    @media(max-width:991px) {

        .summary-final {

            font-size: 22px;
        }

        .custom-card .card-body,
        .order-summary-card .card-body {

            padding: 18px;
        }
    }
</style>

<?php if ($payment_method !== 'COD'): ?>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        document.getElementById('rzpPayBtn').addEventListener('click', function() {

            const btn = this;
            const msg = document.getElementById('paymentMsg');

            btn.disabled = true;
            btn.innerText = 'Creating secure payment...';

            msg.innerText = '';

            fetch('razorpay_create_order.php', {

                    method: 'POST'

                })

                .then(async res => {

                    const text = await res.text();

                    let data;

                    try {

                        data = JSON.parse(text);

                    } catch (e) {

                        console.error(text);

                        throw new Error(text || 'Invalid server response');
                    }

                    if (!res.ok || data.status !== 'success') {

                        throw new Error(
                            data.message ||
                            data.debug ||
                            'Razorpay order creation failed'
                        );
                    }

                    return data;
                })

                .then(data => {

                    const options = {

                        key: data.key,
                        amount: data.amount,
                        currency: data.currency,

                        name: data.store_name,

                        description: 'Order Payment',

                        order_id: data.order_id,

                        handler: function(response) {

                            const form = document.createElement('form');

                            form.method = 'POST';

                            form.action = 'place_order_action.php';

                            form.innerHTML = `

<input type="hidden" name="address_id" value="<?= $address_id ?>">

<input type="hidden" name="payment_method" value="ONLINE">

<input type="hidden" name="razorpay_order_id" value="${response.razorpay_order_id || ''}">

<input type="hidden" name="razorpay_payment_id" value="${response.razorpay_payment_id || ''}">

<input type="hidden" name="razorpay_signature" value="${response.razorpay_signature || ''}">
`;

                            document.body.appendChild(form);

                            form.submit();
                        },

                        prefill: {

                            name: "<?= ecom_h($user['name'] ?? '') ?>",

                            email: "<?= ecom_h($user['email'] ?? '') ?>",

                            contact: "<?= ecom_h($user['mobile'] ?? '') ?>"
                        },

                        theme: {

                            color: '#16a34a'
                        },

                        modal: {

                            ondismiss: function() {

                                btn.disabled = false;

                                btn.innerText = 'Pay Securely with Razorpay';
                            }
                        }
                    };

                    const rzp = new Razorpay(options);

                    rzp.open();
                })

                .catch(err => {

                    msg.innerText =
                        err.message || 'Razorpay order creation failed';

                    btn.disabled = false;

                    btn.innerText = 'Pay Securely with Razorpay';
                });
        });
    </script>

<?php endif; ?>

<?php include 'footer.php'; ?>