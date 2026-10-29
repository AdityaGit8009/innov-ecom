<?php
include 'header.php';
include "config/db.php";
require_once "ecom_pricing.php";

// require_once "secure_helpers.php";
$user_id = ecom_require_user();

$q = mysqli_query($conn, "
    SELECT 
        c.id AS cart_id,
        c.qty,
        c.item_type,
        IF(c.item_type='project', pr.title, p.title) AS title,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', pr.image, p.main_image) AS main_image,
        IF(c.item_type='project', 'Project', cat.name) AS category_name,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge, 0)) AS delivery_charge
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
    LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
    LEFT JOIN categories cat ON cat.id = p.category_id
    WHERE c.user_id = '$user_id'
");

$cartItems = [];
while ($item = mysqli_fetch_assoc($q)) {
    $cartItems[] = $item;
}
$totals = ecom_cart_totals($cartItems);
$subTotal = $totals['subtotal'];
$taxTotal = $totals['tax'];
$deliveryTotal = $totals['delivery_total'];
?>

<style>
    .cart-main {
        padding: 40px 0;
        background: #fff;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 30px;
        align-items: flex-start;
    }

    .cart-table-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        overflow-x: auto;
    }

    .cart-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .qty-box {
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border: 0;
        background: #f5f5f5;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        font-weight: bold;
    }

    .qty-btn:hover {
        background: #efefef;
    }

    .qty {
        min-width: 38px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
    }

    .summary-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 90px;
        width: 100%;
    }

    .summary-card h4 {
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
        color: #071329;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        color: #071329;
        margin-bottom: 12px;
    }

    .summary-row.total {
        font-size: 18px;
        font-weight: 700;
        margin-top: 18px;
        margin-bottom: 0;
    }

    .checkout-btn {
        display: block;
        width: 100%;
        margin-top: 22px;
        background: #f45b24;
        color: #fff;
        text-align: center;
        padding: 14px 18px;
        border-radius: 6px;
        font-size: 17px;
        font-weight: 600;
        text-decoration: none;
    }

    .checkout-btn:hover {
        background: #dc4d19;
        color: #fff;
    }

    @media (max-width: 991px) {

        .cart-layout {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }
</style>

<section class="breadcrumb-section">
    <h1>CART</h1>

    <p class="breadcrumb">
        <a href="index.php">Home</a>
        <span>| Cart</span>
    </p>
</section>

<div class="cart-main">

    <div class="container my-5">

        <div class="cart-layout">

            <!-- CART TABLE -->
            <div class="cart-table-card">

                <table class="table align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="text-center">Product Image</th>
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($cartItems) > 0): ?>

                            <?php foreach ($cartItems as $item): ?>

                                <?php
                                $priceInfo = ecom_product_price($item);
                                $price = (float)$priceInfo['sale'];
                                $qty = (int)$item['qty'];
                                $itemTotal = $price * $qty;
                                ?>

                                <tr data-row="<?= $item['cart_id']; ?>">

                                    <!-- IMAGE -->
                                    <td class="text-center">
                                        <?php $folder = ($item['item_type'] === 'project') ? 'projects' : 'products'; ?>
                                        <img
                                            src="uploads/<?= $folder ?>/<?= htmlspecialchars($item['main_image']); ?>"
                                            class="cart-img"
                                            alt="<?= htmlspecialchars($item['title']); ?>">

                                    </td>

                                    <!-- NAME -->
                                    <td>

                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($item['title']); ?>
                                        </h6>

                                        <small class="text-muted">
                                            <?= htmlspecialchars($item['category_name'] ?? 'Electronics'); ?>
                                        </small>

                                    </td>

                                    <!-- PRICE -->
                                    <td
                                        class="text-center item-price"
                                        data-price="<?= $price; ?>">

                                        ₹<?= format_price($price); ?>

                                    </td>

                                    <!-- QUANTITY -->
                                    <td class="text-center">

                                        <div class="qty-box">

                                            <button
                                                type="button"
                                                class="qty-btn minus"
                                                data-id="<?= $item['cart_id']; ?>">

                                                -
                                            </button>

                                            <span class="qty">
                                                <?= $qty; ?>
                                            </span>

                                            <button
                                                type="button"
                                                class="qty-btn plus"
                                                data-id="<?= $item['cart_id']; ?>">

                                                +
                                            </button>

                                        </div>

                                    </td>

                                    <!-- TOTAL -->
                                    <td class="text-center">

                                        ₹<span class="item-total">
                                            <?= format_price($itemTotal); ?>
                                        </span>

                                    </td>

                                    <!-- REMOVE -->
                                    <td class="text-center">

                                        <button
                                            class="btn btn-outline-danger btn-sm remove-btn"
                                            data-id="<?= $item['cart_id']; ?>">

                                            Remove
                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="text-center text-muted py-5">
                                    Your cart is empty
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <?php
            $grandTotal = $subTotal + $taxTotal + $deliveryTotal;
            ?>

            <!-- SUMMARY -->
            <div class="summary-card">
                
                <h4>Order Summary</h4>

                <div class="summary-row">

                    <span>Subtotal</span>

                    <span>
                        ₹<span id="subTotal">
                            <?= format_price($subTotal); ?>
                        </span>
                    </span>

                </div>

                <div class="summary-row">

                    <span>Service Tax + GST</span>

                    <span>
                        ₹<span id="taxTotal">
                            <?= format_price($taxTotal); ?>
                        </span>
                    </span>

                </div>

                <div class="summary-row">

                    <span>Delivery Charge</span>

                    <span>
                        ₹<span id="deliveryCharge">
                            <?= format_price($deliveryTotal); ?>
                        </span>
                    </span>

                </div>

                <hr>

                <div class="summary-row total">

                    <span>Total</span>

                    <span>
                        ₹<span id="grandTotal">
                            <?= format_price($grandTotal); ?>
                        </span>
                    </span>

                </div>

                <a href="checkout.php" class="checkout-btn">
                    Proceed to Checkout
                </a>

            </div>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function formatMoney(amount) {

        let str = parseFloat(amount).toLocaleString('en-IN', {

            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        return str.replace(/\.00$/, '');
    }

    // UPDATE SUMMARY
    function updateSummary(data) {

        $('#subTotal').text(
            formatMoney(data.sub_total)
        );

        $('#taxTotal').text(
            formatMoney(data.tax_total)
        );

        $('#deliveryCharge').text(
            formatMoney(data.delivery_charge)
        );

        $('#grandTotal').text(
            formatMoney(data.grand_total)
        );
    }

    // QUANTITY UPDATE
    $(document).on('click', '.qty-btn', function(e) {

        e.preventDefault();

        let btn = $(this);

        let cartId = btn.data('id');

        let row = btn.closest('tr');

        let qtyElement = row.find('.qty');

        // ACTION
        let type = btn.hasClass('plus') ?
            'plus' :
            'minus';

        btn.prop('disabled', true);

        $.ajax({

            url: 'update_cart.php',

            type: 'POST',

            dataType: 'json',

            data: {

                cart_id: cartId,
                type: type
            },

            success: function(data) {

                console.log(data);

                if (data.status === 'success') {

                    // UPDATE QTY
                    qtyElement.text(data.qty);

                    // UPDATE ROW TOTAL
                    row.find('.item-total').text(
                        formatMoney(data.item_total)
                    );

                    // UPDATE SUMMARY
                    updateSummary(data);

                    // UPDATE HEADER COUNT
                    if (typeof updateHeaderCounts === 'function') {

                        updateHeaderCounts();
                    }

                } else {

                    alert(data.message);
                }
            },

            error: function(xhr) {

                console.log(xhr.responseText);

                alert('Cart update failed');
            },

            complete: function() {

                btn.prop('disabled', false);
            }
        });

    });

    // REMOVE ITEM
    $(document).on('click', '.remove-btn', function(e) {

        e.preventDefault();

        let btn = $(this);

        let cartId = btn.data('id');

        if (!confirm('Remove this item?')) {

            return;
        }

        $.ajax({

            url: 'remove_cart.php',

            type: 'POST',

            dataType: 'json',

            data: {

                cart_id: cartId
            },

            success: function(data) {

                if (data.status === 'success') {

                    btn.closest('tr').remove();

                    updateSummary(data);

                    if (typeof updateHeaderCounts === 'function') {

                        updateHeaderCounts();
                    }

                    if ($('tbody tr[data-row]').length === 0) {

                        $('tbody').html(`
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                Your cart is empty
                            </td>
                        </tr>
                    `);
                    }

                } else {

                    alert(data.message);
                }
            },

            error: function() {

                alert('Remove failed');
            }
        });

    });
</script>