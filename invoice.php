<?php
session_start();

include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';
// require_once 'ecom_pricing.php';

$user_id = ecom_require_user();

$order_id = (int)($_GET['order_id'] ?? 0);

$order = mysqli_fetch_assoc(mysqli_query(
  $conn,
  "SELECT * FROM orders 
   WHERE id='$order_id' 
   AND user_id='$user_id'
   LIMIT 1"
));

if (!$order) {
  echo "Order not found";
  exit;
}
/*
|------------------------------------------------------------------
| FETCH CUSTOMER ADDRESS FROM customer_addresses TABLE
|------------------------------------------------------------------
*/

$address = mysqli_fetch_assoc(mysqli_query(
  $conn,
  "SELECT * FROM customer_addresses 
     WHERE id = '" . (int)$order['address_id'] . "'
     LIMIT 1"
));

/*
|------------------------------------------------------------------
| BUILD FULL ADDRESS
|------------------------------------------------------------------
*/

$customerAddress = '';

if ($address) {

  $addressParts = [];

  if (!empty($address['house_no'])) {
    $addressParts[] = $address['house_no'];
  }

  if (!empty($address['area'])) {
    $addressParts[] = $address['area'];
  }

  if (!empty($address['landmark'])) {
    $addressParts[] = $address['landmark'];
  }

  if (!empty($address['city'])) {
    $addressParts[] = $address['city'];
  }

  if (!empty($address['state'])) {
    $addressParts[] = $address['state'];
  }

  if (!empty($address['country'])) {
    $addressParts[] = $address['country'];
  }

  if (!empty($address['pincode'])) {
    $addressParts[] = $address['pincode'];
  }

  $customerAddress = implode(', ', $addressParts);
}

if (empty(trim($customerAddress))) {
  $customerAddress = 'N/A';
}


/*
|--------------------------------------------------------------------------
| ONLY DELIVERED ORDER CAN ACCESS INVOICE
|--------------------------------------------------------------------------
*/
if (($order['order_status'] ?? '') !== 'Delivered') {

  echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Invoice Locked</title>

        <style>
            body{
                margin:0;
                padding:0;
                font-family:Arial,sans-serif;
                background:var(--cultured);
                display:flex;
                align-items:center;
                justify-content:center;
                height:100vh;
            }

            .lock-box{
                width:100%;
                max-width:520px;
                background:var(--white);
                border-radius:18px;
                padding:35px;
                text-align:center;
                box-shadow:0 10px 35px rgba(0,0,0,0.08);
                border-top:5px solid var(--salmon-pink);
            }

            .icon{
                font-size:60px;
                margin-bottom:15px;
            }

            h2{
                margin:0 0 12px;
                color:var(--eerie-black);
                font-size:28px;
            }

            p{
                color:var(--sonic-silver);
                line-height:26px;
                margin-bottom:25px;
                font-size:15px;
            }

            .btn{
                display:inline-block;
                padding:12px 22px;
                background:var(--salmon-pink);
                color:var(--white);
                text-decoration:none;
                border-radius:10px;
                font-weight:600;
            }

            .btn:hover{
                background:var(--sandy-brown);
            }

            .status{
                display:inline-block;
                margin-top:12px;
                padding:7px 14px;
                background:var(--cultured);
                color:var(--sandy-brown);
                border-radius:30px;
                font-size:13px;
                font-weight:700;
            }
        </style>
    </head>

    <body>

        <div class='lock-box'>

            <div class='icon'>📦</div>

            <h2>Invoice Locked</h2>

            <p>
                Invoice print/download will be available only after your order is
                <b>Delivered</b>.
            </p>

            <div class='status'>
                Current Status: " . sip_h($order['order_status']) . "
            </div>

            <br><br>

            <a href='order-details.php?order_id=" . $order_id . "' class='btn'>
                Back to Order
            </a>

        </div>

    </body>
    </html>";

  exit;
}

$items = mysqli_query(
  $conn,
  "SELECT 
        oi.*,
        IF(oi.item_type='project', pr.title, p.title) AS title,
        IF(oi.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(oi.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(oi.item_type='project', pr.discount, p.discount) AS discount
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id AND oi.item_type='product'
    LEFT JOIN projects pr ON pr.id = oi.product_id AND oi.item_type='project'
    WHERE oi.order_id='$order_id'"
);

$invoiceNo = !empty($order['invoice_no'])
  ? $order['invoice_no']
  : ('INV-' . $order['order_no']);

$invoiceDate = date('d-m-Y', strtotime($order['created_at']));
?>

<!-- <pre>
<?php print_r($order); ?>
</pre> -->

<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">

  <title>
    Invoice <?= sip_h($invoiceNo) ?>
  </title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      color: #111;
      background: #fff;
    }

    .invoice-box {
      max-width: 1100px;
      margin: auto;
      border: 2px solid #000;
      padding: 18px;
    }

    .top-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #000;
      padding-bottom: 12px;
      margin-bottom: 14px;
    }

    .company-name {
      font-size: 28px;
      font-weight: 700;
    }

    .small {
      font-size: 12px;
      line-height: 20px;
    }

    .invoice-title {
      font-size: 34px;
      font-weight: 700;
      text-align: right;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      margin-bottom: 14px;
    }

    .info-table td {
      border: 1px solid #000;
      padding: 14px;
      font-size: 13px;
      vertical-align: top;
      line-height: 24px;
    }

    .address-title {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 10px;
      color: #111827;
      border-bottom: 1px dashed #ccc;
      padding-bottom: 6px;
    }

    .product-table {
      width: 100%;
      border-collapse: collapse;
    }

    .product-table th,
    .product-table td {
      border: 1px solid #000;
      padding: 8px;
      font-size: 13px;
    }

    .product-table th {
      background: #f3f4f6;
      text-align: center;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .totals {
      width: 420px;
      margin-left: auto;
      margin-top: 18px;
      border-collapse: collapse;
    }

    .totals td {
      border: 1px solid #000;
      padding: 10px;
      font-size: 14px;
    }

    .grand-total {
      background: #f3f4f6;
      font-size: 18px;
      font-weight: 700;
    }

    .footer {
      margin-top: 35px;
      font-size: 12px;
      line-height: 22px;
    }

    .signature {
      text-align: right;
      margin-top: 40px;
    }

    .btn-area {
      margin-bottom: 20px;
    }

    .btn {
      display: inline-block;
      padding: 10px 16px;
      background: #ff5a1f;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      border: 0;
      cursor: pointer;
      margin-right: 10px;
    }

    @media print {

      .btn-area {
        display: none;
      }

      body {
        margin: 0;
      }
    }
  </style>

</head>

<body>

  <div class="btn-area">

    <a href="order-details.php?order_id=<?= $order_id ?>" class="btn">
      Back
    </a>

    <button onclick="window.print()" class="btn">
      Print / Save PDF
    </button>

  </div>

  <div class="invoice-box">

    <!-- HEADER -->
    <div class="top-head">

      <div>

        <div class="company-name">
          Softpro India Computer Technologies (P) Limited
        </div>

        <div class="small">
          <span style="font-weight:700;">
            Near New Hanuman Temple <br>
            Kapoorthala, Aliganj - 226006 <br>
            Lucknow (UP) - 226006 <br>
            Call: +91 7080102007 <br>
            Email: hr@softproindia.in
          </span>
        </div>

      </div>

      <div class="invoice-title">

        TAX INVOICE

        <div class="small" style="margin-top:10px;">

          Invoice No:
          <b><?= sip_h($invoiceNo) ?></b><br>

          Date:
          <b><?= $invoiceDate ?></b><br>

          Order ID:
          <b><?= sip_h($order['order_no']) ?></b>

          <?php if (!empty($order['razorpay_payment_id'])): ?>
            <br>

            Razorpay Payment ID:
            <b><?= sip_h($order['razorpay_payment_id']) ?></b>
          <?php endif; ?>

        </div>

      </div>

    </div>

    <!-- BILL TO & SHIP TO -->


    <table class="info-table">

      <tr>

        <!-- BILL TO -->
        <td width="50%">

          <div class="address-title">
            BILL TO
          </div>

          <b>Name:</b>
          <?= sip_h($order['customer_name'] ?? '') ?>
          <br>

          <b>Mobile:</b>
          <?= sip_h($order['customer_phone'] ?? '') ?>
          <br>

          <b>Email:</b>
          <?= sip_h($order['customer_email'] ?? '') ?>
          <br>

          <b>Billing Address:</b>

          <?= nl2br(sip_h($customerAddress)) ?>
        </td>

        <!-- SHIP TO -->
        <td width="50%">

          <div class="address-title">
            SHIP TO
          </div>

          <b>Name:</b>
          <?= sip_h($order['customer_name'] ?? '') ?>
          <br>

          <b>Mobile:</b>
          <?= sip_h($order['customer_phone'] ?? '') ?>
          <br>

          <b>Email:</b>
          <?= sip_h($order['customer_email'] ?? '') ?>
          <br>
          <b>Shipping Address:</b>
          <?= nl2br(sip_h($customerAddress)) ?>

        </td>

      </tr>

    </table>

    <!-- PRODUCT TABLE -->
    <table class="product-table">

      <thead>

        <tr>

          <th>SR</th>
          <th>Product</th>
          <th>HSN</th>
          <th>Qty</th>
          <th>Actual Price</th>
          <th>Service Tax</th>
          <th>Taxable Amount</th>
          <th>GST %</th>
          <th>GST Amount</th>
          <th>Final Price</th>
          <th>Total</th>

        </tr>

      </thead>

      <tbody>

        <?php

        $sr = 1;

        $subTotal = 0;
        $serviceTaxTotal = 0;
        $taxableTotal = 0;
        $gstTotal = 0;
        $grandTotal = 0;

        while ($it = mysqli_fetch_assoc($items)):

          $qty = (int)$it['qty'];

          /*
    |---------------------------------------------------------
    | FINAL PRICE FROM ORDER ITEMS (ALREADY GST INCLUDED)
    |---------------------------------------------------------
    */
          $finalPrice = (float)$it['price'];

          /*
    |---------------------------------------------------------
    | GST %
    |---------------------------------------------------------
    */
          $gstPercent = (float)$it['tax_percentage'];

          /*
    |---------------------------------------------------------
    | REMOVE GST FROM FINAL PRICE
    |---------------------------------------------------------
    */
          $taxableAmount = $finalPrice / (1 + ($gstPercent / 100));

          /*
    |---------------------------------------------------------
    | GST AMOUNT
    |---------------------------------------------------------
    */
          $gstAmount = $finalPrice - $taxableAmount;

          /*
    |---------------------------------------------------------
    | SERVICE TAX (2%)
    |---------------------------------------------------------
    */
          $serviceTaxPercent = 2;

          $basePrice = $taxableAmount / 1.02;

          $serviceTaxAmount = $taxableAmount - $basePrice;

          /*
    |---------------------------------------------------------
    | LINE TOTAL
    |---------------------------------------------------------
    */
          $lineTotal = $finalPrice * $qty;

          /*
    |---------------------------------------------------------
    | TOTALS
    |---------------------------------------------------------
    */
          $subTotal += ($basePrice * $qty);

          $serviceTaxTotal += ($serviceTaxAmount * $qty);

          $taxableTotal += ($taxableAmount * $qty);

          $gstTotal += ($gstAmount * $qty);

          $grandTotal += $lineTotal;

        ?>
          <tr>

            <td class="text-center">
              <?= $sr++ ?>
            </td>

            <td>
              <?= sip_h($it['title']) ?>
            </td>

            <td class="text-center">
              8542
            </td>

            <td class="text-center">
              <?= $qty ?>
            </td>

            <td class="text-right">
              ₹<?= format_price($basePrice) ?>
            </td>

            <td class="text-right">
              ₹<?= format_price($serviceTaxAmount) ?>
            </td>

            <td class="text-right">
              ₹<?= format_price($taxableAmount) ?>
            </td>

            <td class="text-center">
              <?= format_price($gstPercent) ?>%
            </td>

            <td class="text-right">
              ₹<?= format_price($gstAmount) ?>
            </td>

            <td class="text-right">
              ₹<?= format_price($finalPrice) ?>
            </td>

            <td class="text-right">
              ₹<?= format_price($lineTotal) ?>
            </td>

          </tr>

        <?php endwhile; ?>

      </tbody>

    </table>

    <!-- TOTALS -->
    <table class="totals">

      <tr>

        <td>
          Actual Amount
        </td>

        <td class="text-right">
          ₹<?= format_price($subTotal) ?>
        </td>

      </tr>

      <tr>

        <td>
          Service Tax Total
        </td>

        <td class="text-right">
          ₹<?= format_price($serviceTaxTotal) ?>
        </td>

      </tr>

      <tr>

        <td>
          Taxable Amount
        </td>

        <td class="text-right">
          ₹<?= format_price($taxableTotal) ?>
        </td>

      </tr>

      <tr>

        <td>
          GST Total
        </td>

        <td class="text-right">
          ₹<?= format_price($gstTotal) ?>
        </td>

      </tr>

      <tr class="grand-total">

        <td>
          Grand Total
        </td>

        <td class="text-right">
          ₹<?= format_price($grandTotal) ?>
        </td>

      </tr>

    </table>

    <!-- FOOTER -->
    <div class="footer">

      <b>Payment Method:</b>
      <?= sip_h($order['payment_method']) ?>

      <br>

      <b>Payment Status:</b>
      <?= sip_h($order['payment_status']) ?>

      <br>

      <b>Order Status:</b>
      <?= sip_h($order['order_status']) ?>

      <br><br>

      This is a computer generated invoice.

      <div class="signature">

        <b>
          For Softpro India Computer Technologies (P) Limited
        </b>

        <br><br><br>

        Authorised Signatory

      </div>

    </div>

  </div>

</body>

</html>