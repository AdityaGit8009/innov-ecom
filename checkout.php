<?php
session_start();

include 'config/db.php';
require_once 'secure_helpers.php';

$user_id = (int)ecom_require_user();

/* =========================
   PLACE ORDER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    $address_id     = (int)($_POST['address_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');

    if ($address_id <= 0 || $payment_method === '') {

        echo "<script>alert('Address or payment missing');</script>";
    } else {

        $_SESSION['checkout_address_id'] = $address_id;
        $_SESSION['checkout_payment']    = $payment_method;

        header("Location: place_order.php");
        exit;
    }
}

/* =========================
   CART ITEMS
========================= */

$products = [];

$res = mysqli_query($conn, "
    SELECT c.qty, c.item_type, c.product_id,
        IF(c.item_type='project', pr.title, p.title) AS title,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', pr.discount, p.discount) AS discount,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge, 0)) AS delivery_charge,
        IF(c.item_type='project', pr.image, p.main_image) AS main_image
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
    LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
    WHERE c.user_id = '$user_id'
");

/*
|--------------------------------------------------------------------------
| PRICE SYSTEM
|--------------------------------------------------------------------------
| Product price already GST included
| GST sirf informational purpose ke liye show hoga
| Total mein dobara add nahi hoga
|--------------------------------------------------------------------------
*/

$subtotal   = 0; // without GST
$taxTotal   = 0; // included GST
$delivery   = 0;
$itemsTotal = 0; // final product total with GST

while ($r = mysqli_fetch_assoc($res)) {

    $price           = (float)$r['price'];
    $old_price       = (float)$r['old_price'];
    $qty             = (int)$r['qty'];
    $tax_percentage  = (float)($r['tax_percentage'] ?? 0);
    $delivery_charge = (float)($r['delivery_charge'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT TOTAL
    |--------------------------------------------------------------------------
    */
    $lineTotal = $price * $qty;

    /*
    |--------------------------------------------------------------------------
    | INCLUDED GST CALCULATION
    |--------------------------------------------------------------------------
    */
    $includedTax = 0;

    if ($tax_percentage > 0) {

        $includedTax =
            ($lineTotal * $tax_percentage) /
            (100 + $tax_percentage);
    }

    /*
    |--------------------------------------------------------------------------
    | BASE PRICE WITHOUT GST
    |--------------------------------------------------------------------------
    */
    $baseAmount = $lineTotal - $includedTax;

    /*
    |--------------------------------------------------------------------------
    | TOTALS
    |--------------------------------------------------------------------------
    */
    $subtotal   += $baseAmount;

    $taxTotal   += $includedTax;

    $delivery   += $delivery_charge;

    $itemsTotal += $lineTotal;

    $products[] = [
        'product_id'      => $r['product_id'] ?? 0,
        'name'            => $r['title'],
        'price'           => $price,
        'old_price'       => $old_price,
        'discount'        => $r['discount'],
        'qty'             => $qty,

        'tax_percentage'  => $tax_percentage,

        'delivery_charge' => $delivery_charge,

        'line_total'      => $lineTotal,

        'base_amount'     => $baseAmount,

        'tax_amount'      => $includedTax,

        'image'           => 'uploads/' . ($r['item_type'] === 'project' ? 'projects' : 'products') . '/' . $r['main_image']
    ];
}

if (empty($products)) {

    echo "<script>alert('Your cart is empty'); location.href='cart.php';</script>";
    exit;
}

/*
|--------------------------------------------------------------------------
| FINAL TOTAL
|--------------------------------------------------------------------------
*/
$grandTotal = $itemsTotal + $delivery;

/* =========================
   ADDRESSES
========================= */
$addresses = [];

$a = mysqli_query($conn, "
    SELECT *
    FROM customer_addresses
    WHERE customer_id = '$user_id'
    ORDER BY is_default DESC, id DESC
");

while ($row = mysqli_fetch_assoc($a)) {

    $addresses[] = $row;
}

$defaultAddressId = '';
$defaultPin = '';

if (!empty($addresses)) {

    $defaultAddressId = (int)$addresses[0]['id'];
    $defaultPin = $addresses[0]['pincode'] ?? '';
}

$deliveryQuote = [

    'serviceable' => true,
    'charge'      => $delivery,
    'partner'     => 'Product Delivery Charge',
    // 'message'     => 'Product-wise delivery charge'
];

include 'header.php';
?>

<section class="breadcrumb-section">

    <h1>CHECKOUT</h1>

    <p class="breadcrumb">
        <a href="index.php">Home</a>
        <span>| Checkout</span>
    </p>

</section>

<div class="container my-5 checkout-page">

    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-lg-8">

            <div class="checkout-left-sticky">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="fw-bold mb-0">Saved Addresses</h4>

                    <a href="manage-addresses.php?return=checkout" class="btn btn-danger btn-sm">
                        + Add Address
                    </a>

                </div>

                <?php if (empty($addresses)): ?>

                    <div class="ecom-card text-center p-4">

                        <div style="font-size:44px;">📍</div>

                        <h5 class="fw-bold mt-2">
                            No address found
                        </h5>

                        <p class="text-muted mb-3">
                            Please add address before placing order.
                        </p>

                        <a href="manage-addresses.php?return=checkout" class="btn btn-danger">
                            Add Address
                        </a>

                    </div>

                <?php else: ?>

                    <div class="row g-3">

                        <?php foreach ($addresses as $index => $ad): ?>

                            <?php
                            $isChecked = !empty($ad['is_default']) || $index === 0;
                            ?>

                            <div class="col-md-6">

                                <label
                                    class="checkout-address-card <?= $isChecked ? 'active' : '' ?>"
                                    id="addressCard<?= (int)$ad['id']; ?>">

                                    <input
                                        class="form-check-input mt-1"
                                        type="radio"
                                        name="selected_address"
                                        value="<?= (int)$ad['id']; ?>"
                                        <?= $isChecked ? 'checked' : ''; ?>
                                        onclick="selectAddress(
                                        <?= (int)$ad['id']; ?>,
                                        '<?= htmlspecialchars($ad['pincode'] ?? ''); ?>'
                                    )">

                                    <div class="flex-grow-1">

                                        <div class="d-flex justify-content-between align-items-center gap-2">

                                            <strong>
                                                <?= htmlspecialchars($ad['name'] ?? ''); ?>
                                            </strong>

                                            <?php if (!empty($ad['is_default'])): ?>

                                                <span class="badge bg-success">
                                                    Default
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                        <div class="text-muted small mt-2">

                                            <?= htmlspecialchars($ad['city'] ?? ''); ?>,
                                            <?= htmlspecialchars($ad['state'] ?? ''); ?>
                                            -
                                            <?= htmlspecialchars($ad['pincode'] ?? ''); ?>

                                            <br>

                                            📞 <?= htmlspecialchars($ad['phone'] ?? ''); ?>

                                            <br>

                                            Type:
                                            <?= htmlspecialchars($ad['type'] ?? 'Home'); ?>

                                        </div>

                                        <div class="mt-3">

                                            <a
                                                href="manage-addresses.php?id=<?= (int)$ad['id']; ?>&return=checkout"
                                                class="btn btn-sm btn-outline-primary">

                                                Edit

                                            </a>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">

            <div class="checkout-summary-sticky">

                <h5 class="fw-bold mb-3">
                    Your Cart
                </h5>

                <?php foreach ($products as $p): ?>

                    <?php
                    $price     = (float)$p['price'];
                    $qty       = (int)$p['qty'];
                    $lineTotal = (float)$p['line_total'];
                    ?>

                    <div class="card mb-2 border-0 shadow-sm">

                        <div class="card-body d-flex gap-2">

                            <img
                                src="<?= htmlspecialchars($p['image']); ?>"
                                style="width:60px;height:60px;object-fit:contain;border:1px solid #eee;border-radius:8px;"
                                alt="Product">

                            <div class="w-100">

                                <strong>
                                    <?= htmlspecialchars($p['name']); ?> (<?= $qty; ?>)
                                </strong>

                                <!-- PRICE -->
                                <div class="price-box mt-1">

                                    <p class="price mb-1 me-2">
                                        ₹<?= format_price($price); ?>
                                    </p>

                                    <?php if (!empty($p['old_price']) && (float)$p['old_price'] > $price): ?>

                                        <del class="text-muted">
                                            ₹<?= format_price((float)$p['old_price']); ?>
                                        </del>

                                    <?php endif; ?>


                                </div>




                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

                <!-- PAYMENT -->
                <div class="card mt-3 border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="fw-bold mb-2">
                            Payment Method
                        </h5>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="payment_method_radio"
                                value="COD"
                                checked>

                            <label class="form-check-label">
                                Cash on Delivery
                            </label>

                        </div>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="payment_method_radio"
                                value="ONLINE">

                            <label class="form-check-label">
                                Online Payment (Razorpay)
                            </label>

                        </div>

                    </div>

                </div>

                <!-- TOTAL -->
                <div class="card mt-3 border-0 shadow-sm">

                    <div class="card-body checkout-total-card">

                        <div class="summary-line">
                            <span>Subtotal (Included Service Tax)</span>

                            <span>
                                ₹<?= format_price($subtotal); ?>
                            </span>
                        </div>

                        <div class="summary-line text-success">

                            <span>GST</span>

                            <span>
                                ₹<?= format_price($taxTotal); ?>
                            </span>

                        </div>

                        <div class="summary-line">

                            <span>Delivery</span>

                            <span>
                                ₹<span id="deliveryAmount">
                                    <?= format_price($delivery); ?>
                                </span>
                            </span>

                        </div>

                        <!-- <div
                            id="deliveryMsg"
                            class="delivery-info">

                            <?= htmlspecialchars($deliveryQuote['message']); ?>

                        </div> -->

                        <div class="summary-total">

                            <span>Total</span>

                            <span>
                                ₹<span id="checkoutTotal">
                                    <?= format_price($grandTotal); ?>
                                </span>
                            </span>

                        </div>

                        <form method="POST" id="placeOrderForm">

                            <input type="hidden" name="place_order" value="1">

                            <input
                                type="hidden"
                                name="address_id"
                                id="address_id"
                                value="<?= $defaultAddressId; ?>">

                            <input
                                type="hidden"
                                name="payment_method"
                                id="payment_method"
                                value="COD">

                            <button
                                type="submit"
                                class="btn place-order-btn w-100"
                                <?= empty($addresses) ? 'disabled' : ''; ?>>

                                Place Order

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<style>
    .checkout-page {

        --checkout-primary: #ef662f;
        --checkout-secondary: #ff8a3d;
        --checkout-bg: #fff7f2;
        --checkout-card: #ffffff;
        --checkout-border: #ffe2d4;
        --checkout-text: #1f2937;
        --checkout-muted: #6b7280;
    }

    /* PAGE */
    .checkout-page {

        position: relative;
    }

    /* LEFT STICKY */
    .checkout-left-sticky {

        position: sticky;
        top: 30px;

        max-height: calc(100vh - 115px);

        overflow-y: auto;

        padding-right: 6px;
    }

    .checkout-left-sticky::-webkit-scrollbar {

        width: 5px;
    }

    .checkout-left-sticky::-webkit-scrollbar-thumb {

        background: rgba(239, 102, 47, 0.35);
        border-radius: 20px;
    }

    /* RIGHT STICKY */
    .checkout-summary-sticky {

        position: sticky;
        top: 95px;
    }

    /* ADDRESS CARD */
    .checkout-address-card {

        width: 100%;

        min-height: 180px;

        display: flex;
        align-items: flex-start;
        gap: 14px;

        padding: 20px;

        border-radius: 22px;

        border: 1px solid var(--checkout-border);

        background:
            linear-gradient(145deg,
                #ffffff,
                #fff8f5);

        cursor: pointer;

        transition: 0.3s ease;

        box-shadow:
            0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .checkout-address-card:hover,
    .checkout-address-card.active {

        border-color: var(--checkout-primary);

        background:
            linear-gradient(145deg,
                #fff8f4,
                #ffffff);

        transform: translateY(-3px);

        box-shadow:
            0 18px 40px rgba(239, 102, 47, 0.14);
    }

    /* RADIO */
    .checkout-address-card .form-check-input {

        width: 20px;
        height: 20px;

        margin-top: 4px;

        border: 2px solid var(--checkout-primary);

        cursor: pointer;
    }

    .checkout-address-card .form-check-input:checked {

        background-color: var(--checkout-primary);
        border-color: var(--checkout-primary);
    }

    /* ADDRESS TEXT */
    .checkout-address-card strong {

        font-size: 16px;
        color: var(--checkout-text);
    }

    .checkout-address-card .text-muted {

        color: var(--checkout-muted) !important;

        line-height: 1.7;
    }

    /* DEFAULT BADGE */
    .badge.bg-success {

        background:
            linear-gradient(135deg,
                #16a34a,
                #22c55e) !important;

        border-radius: 30px;

        padding: 6px 12px;

        font-size: 11px;

        letter-spacing: .4px;
    }

    /* CARD */
    .checkout-page .card {

        border: none !important;

        border-radius: 24px;

        overflow: hidden;

        background: var(--checkout-card);

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .checkout-page .card-body {

        padding: 18px;
    }

    /* PRODUCT IMAGE */
    .checkout-page .card img {

        width: 60px;
        height: 60px;

        object-fit: contain;

        background: #fff8f3;

        border: 1px solid #ffe7dc;

        border-radius: 14px;

        padding: 6px;
    }

    /* PRICE */
    .price {

        color: var(--checkout-primary);

        font-weight: 800;

        font-size: 20px;
    }

    .price-box del {

        font-size: 13px;

        color: #9ca3af;
    }

    /* PAYMENT */
    .form-check {

        padding: 12px 30px;

        border-radius: 14px;

        border: 1px solid #f3f4f6;

        margin-bottom: 12px;

        transition: 0.25s ease;

        background: #fff;
    }

    .form-check:hover {

        border-color: var(--checkout-primary);

        background: #fff8f4;
    }

    .form-check-input:checked {

        background-color: var(--checkout-primary);

        border-color: var(--checkout-primary);
    }

    /* TOTAL CARD */
    .checkout-total-card {

        padding: 24px;
    }

    .summary-line {

        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 16px;

        font-size: 15px;

        color: var(--checkout-muted);
    }

    /* DELIVERY INFO */
    .delivery-info {

        margin-top: 10px;

        padding: 12px 14px;

        border-radius: 14px;

        background: #fff8f4;

        border: 1px dashed #ffb489;

        color: var(--checkout-primary);

        font-size: 13px;
    }

    /* TOTAL */
    .summary-total {

        margin-top: 24px;

        padding-top: 20px;

        border-top: 2px dashed #ffd4bf;

        display: flex;
        justify-content: space-between;
        align-items: center;

        font-size: 28px;

        font-weight: 800;

        color: var(--checkout-text);
    }

    /* PLACE ORDER BUTTON */
    .place-order-btn {

        width: 100%;

        height: 56px;

        margin-top: 24px;

        border: none;

        border-radius: 18px;

        font-size: 17px;

        font-weight: 700;

        color: #fff;

        background:
            linear-gradient(135deg,
                var(--checkout-primary),
                var(--checkout-secondary));

        transition: 0.3s ease;

        box-shadow:
            0 12px 30px rgba(239, 102, 47, 0.25);
    }

    .place-order-btn:hover {

        transform: translateY(-3px);

        box-shadow:
            0 18px 40px rgba(239, 102, 47, 0.35);
    }

    /* ADD ADDRESS BUTTON */
    .btn-danger {

        border: none !important;

        border-radius: 14px;

        padding: 10px 18px;

        font-weight: 600;

        background:
            linear-gradient(135deg,
                var(--checkout-primary),
                var(--checkout-secondary));
    }

    .btn-danger:hover {

        opacity: 0.95;
    }

    /* EDIT BUTTON */
    .btn-outline-primary {

        border-radius: 12px;

        border-color: var(--checkout-primary);

        color: var(--checkout-primary);

        font-weight: 600;
    }

    .btn-outline-primary:hover {

        background: var(--checkout-primary);

        border-color: var(--checkout-primary);
    }

    /* HEADING */
    .checkout-page h4,
    .checkout-page h5 {

        color: var(--checkout-text);
    }

    /* MOBILE */
    @media(max-width:991px) {

        .checkout-summary-sticky,
        .checkout-left-sticky {

            position: static;

            max-height: unset;

            overflow: visible;
        }

        .checkout-address-card {

            min-height: auto;
        }

        .summary-total {

            font-size: 24px;
        }
    }
</style>

<script>
    const itemsTotal = <?= (float)$itemsTotal; ?>;

    function formatMoney(amount) {
        let str = parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return str.replace(/\.00$/, '');
    }

    function selectAddress(id, pin) {

        document.getElementById('address_id').value = id;

        document.querySelectorAll('.checkout-address-card').forEach(card => {

            card.classList.remove('active');
        });

        const selectedCard = document.getElementById('addressCard' + id);

        if (selectedCard) {

            selectedCard.classList.add('active');
        }

        if (pin) {

            updateDeliveryQuote(pin);
        }
    }

    document.querySelectorAll('input[name="payment_method_radio"]').forEach(el => {

        el.addEventListener('change', function() {

            document.getElementById('payment_method').value = this.value;
        });
    });

    document.getElementById('placeOrderForm').addEventListener('submit', function(e) {

        if (!document.getElementById('address_id').value) {

            e.preventDefault();

            alert('Please select an address');
        }
    });

    function updateDeliveryQuote(pin) {

        fetch('api_delivery_quote.php?pincode=' + encodeURIComponent(pin))

            .then(r => r.json())

            .then(d => {

                if (d.status === 'success') {

                    const charge = parseFloat(d.delivery.charge || 0);

                    const grandTotal = itemsTotal + charge;

                    document.getElementById('deliveryAmount').innerText =
                        formatMoney(charge);

                    document.getElementById('deliveryMsg').innerText =
                        (d.delivery.partner || '') +
                        ' - ' +
                        (d.delivery.message || '');

                    document.getElementById('checkoutTotal').innerText =
                        formatMoney(grandTotal);
                }
            })

            .catch(() => {

                console.log('Delivery update failed');
            });
    }
</script>

<?php include 'footer.php'; ?>