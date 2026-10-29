<?php

if (!function_exists('sip_invoice_h')) {
    function sip_invoice_h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sip_invoice_price')) {
    function sip_invoice_price($amount): string
    {
        if (function_exists('format_price')) {
            return format_price($amount);
        }
        return number_format((float)$amount, 2);
    }
}

if (!function_exists('sip_invoice_get_order')) {
    function sip_invoice_get_order($conn, int $orderId, ?int $userId = null): ?array
    {
        $orderId = (int)$orderId;
        $whereUser = $userId !== null ? " AND user_id='" . (int)$userId . "'" : '';
        $q = mysqli_query($conn, "SELECT * FROM orders WHERE id='$orderId' $whereUser LIMIT 1");
        if (!$q || mysqli_num_rows($q) === 0) {
            return null;
        }
        return mysqli_fetch_assoc($q);
    }
}

if (!function_exists('sip_invoice_customer_address')) {
    function sip_invoice_customer_address($conn, array $order): string
    {
        $address = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT * FROM customer_addresses WHERE id = '" . (int)($order['address_id'] ?? 0) . "' LIMIT 1"
        ));

        $parts = [];
        if ($address) {
            foreach (['house_no', 'area', 'landmark', 'city', 'state', 'country', 'pincode'] as $field) {
                if (!empty($address[$field])) {
                    $parts[] = $address[$field];
                }
            }
        }

        $full = trim(implode(', ', $parts));
        return $full !== '' ? $full : 'N/A';
    }
}

if (!function_exists('sip_invoice_items')) {
    function sip_invoice_items($conn, int $orderId)
    {
        $orderId = (int)$orderId;
        return mysqli_query($conn, "
            SELECT 
                oi.*,
                IF(oi.item_type='project', pr.title, p.title) AS title,
                IF(oi.item_type='project', pr.base_price, p.base_price) AS base_price,
                IF(oi.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
                IF(oi.item_type='project', pr.discount, p.discount) AS discount
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id AND oi.item_type='product'
            LEFT JOIN projects pr ON pr.id = oi.product_id AND oi.item_type='project'
            WHERE oi.order_id='$orderId'
        ");
    }
}

if (!function_exists('sip_render_invoice_html')) {
    function sip_render_invoice_html($conn, int $orderId, array $options = []): string
    {
        $userId = array_key_exists('user_id', $options) ? (int)$options['user_id'] : null;
        $order = sip_invoice_get_order($conn, $orderId, $userId);
        if (!$order) {
            return '<!doctype html><html><body style="font-family:Arial,sans-serif;">Order not found</body></html>';
        }

        if (empty($order['invoice_no'])) {
            $invoiceNoNew = 'INV' . date('Ymd') . (int)$orderId;
            $invoiceNoEsc = mysqli_real_escape_string($conn, $invoiceNoNew);
            mysqli_query($conn, "UPDATE orders SET invoice_no='$invoiceNoEsc' WHERE id='" . (int)$orderId . "'");
            $order['invoice_no'] = $invoiceNoNew;
        }

        $customerAddress = sip_invoice_customer_address($conn, $order);
        $items = sip_invoice_items($conn, $orderId);

        $invoiceNo = !empty($order['invoice_no']) ? $order['invoice_no'] : ('INV-' . ($order['order_no'] ?? $orderId));
        $invoiceDateSource = !empty($order['delivered_at']) ? $order['delivered_at'] : ($order['created_at'] ?? 'now');
        $invoiceDate = date('d-m-Y', strtotime($invoiceDateSource));
        $showButtons = !empty($options['show_buttons']);
        $backUrl = $options['back_url'] ?? ('order-details.php?order_id=' . (int)$orderId);

        ob_start();
        ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice <?= sip_invoice_h($invoiceNo) ?></title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; margin: 20px; color: #111; background: #fff; }
    .invoice-box { max-width: 1100px; margin: auto; border: 2px solid #000; padding: 18px; background:#fff; }
    .top-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 14px; gap:20px; }
    .company-name { font-size: 28px; font-weight: 700; }
    .small { font-size: 12px; line-height: 20px; }
    .invoice-title { font-size: 34px; font-weight: 700; text-align: right; white-space:nowrap; }
    .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 14px; }
    .info-table td { border: 1px solid #000; padding: 14px; font-size: 13px; vertical-align: top; line-height: 24px; }
    .address-title { font-size: 16px; font-weight: 700; margin-bottom: 10px; color: #111827; border-bottom: 1px dashed #ccc; padding-bottom: 6px; }
    .product-table { width: 100%; border-collapse: collapse; }
    .product-table th, .product-table td { border: 1px solid #000; padding: 8px; font-size: 13px; }
    .product-table th { background: #f3f4f6; text-align: center; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .totals { width: 420px; margin-left: auto; margin-top: 18px; border-collapse: collapse; }
    .totals td { border: 1px solid #000; padding: 10px; font-size: 14px; }
    .grand-total { background: #f3f4f6; font-size: 18px; font-weight: 700; }
    .footer { margin-top: 35px; font-size: 12px; line-height: 22px; }
    .signature { text-align: right; margin-top: 40px; }
    .btn-area { margin:0 auto 20px; max-width:1100px; }
    .btn { display: inline-block; padding: 10px 16px; background: #ff5a1f; color: #fff !important; text-decoration: none; border-radius: 6px; border: 0; cursor: pointer; margin-right: 10px; font-weight:700; }
    @media print { .btn-area { display: none; } body { margin: 0; } }
    @media(max-width:768px){ body{margin:10px}.invoice-box{overflow-x:auto}.top-head{display:block}.invoice-title{text-align:left;margin-top:15px;white-space:normal}.totals{width:100%}.product-table{min-width:980px} }
  </style>
</head>
<body>
  <?php if ($showButtons): ?>
  <div class="btn-area">
    <a href="<?= sip_invoice_h($backUrl) ?>" class="btn">Back</a>
    <button onclick="window.print()" class="btn">Print / Save PDF</button>
  </div>
  <?php endif; ?>

  <div class="invoice-box">
    <div class="top-head">
      <div>
        <div class="company-name">Softpro India Computer Technologies (P) Limited</div>
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
          Invoice No: <b><?= sip_invoice_h($invoiceNo) ?></b><br>
          Date: <b><?= sip_invoice_h($invoiceDate) ?></b><br>
          Order ID: <b><?= sip_invoice_h($order['order_no'] ?? $order['order_number'] ?? $orderId) ?></b>
          <?php if (!empty($order['razorpay_payment_id'])): ?><br>
            Razorpay Payment ID: <b><?= sip_invoice_h($order['razorpay_payment_id']) ?></b>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <table class="info-table">
      <tr>
        <td width="50%">
          <div class="address-title">BILL TO</div>
          <b>Name:</b> <?= sip_invoice_h($order['customer_name'] ?? '') ?><br>
          <b>Mobile:</b> <?= sip_invoice_h($order['customer_phone'] ?? '') ?><br>
          <b>Email:</b> <?= sip_invoice_h($order['customer_email'] ?? '') ?><br>
          <b>Billing Address:</b> <?= nl2br(sip_invoice_h($customerAddress)) ?>
        </td>
        <td width="50%">
          <div class="address-title">SHIP TO</div>
          <b>Name:</b> <?= sip_invoice_h($order['customer_name'] ?? '') ?><br>
          <b>Mobile:</b> <?= sip_invoice_h($order['customer_phone'] ?? '') ?><br>
          <b>Email:</b> <?= sip_invoice_h($order['customer_email'] ?? '') ?><br>
          <b>Shipping Address:</b> <?= nl2br(sip_invoice_h($customerAddress)) ?>
        </td>
      </tr>
    </table>

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
        if ($items) {
            while ($it = mysqli_fetch_assoc($items)):
                $qty = (int)($it['qty'] ?? 0);
                $finalPrice = (float)($it['price'] ?? 0);
                $gstPercent = (float)($it['tax_percentage'] ?? 0);
                $taxableAmount = $gstPercent > 0 ? ($finalPrice / (1 + ($gstPercent / 100))) : $finalPrice;
                $gstAmount = $finalPrice - $taxableAmount;
                $basePrice = $taxableAmount / 1.02;
                $serviceTaxAmount = $taxableAmount - $basePrice;
                $lineTotal = $finalPrice * $qty;
                $subTotal += ($basePrice * $qty);
                $serviceTaxTotal += ($serviceTaxAmount * $qty);
                $taxableTotal += ($taxableAmount * $qty);
                $gstTotal += ($gstAmount * $qty);
                $grandTotal += $lineTotal;
                ?>
        <tr>
          <td class="text-center"><?= $sr++ ?></td>
          <td><?= sip_invoice_h($it['title'] ?? 'Product') ?></td>
          <td class="text-center">8542</td>
          <td class="text-center"><?= $qty ?></td>
          <td class="text-right">₹<?= sip_invoice_price($basePrice) ?></td>
          <td class="text-right">₹<?= sip_invoice_price($serviceTaxAmount) ?></td>
          <td class="text-right">₹<?= sip_invoice_price($taxableAmount) ?></td>
          <td class="text-center"><?= sip_invoice_price($gstPercent) ?>%</td>
          <td class="text-right">₹<?= sip_invoice_price($gstAmount) ?></td>
          <td class="text-right">₹<?= sip_invoice_price($finalPrice) ?></td>
          <td class="text-right">₹<?= sip_invoice_price($lineTotal) ?></td>
        </tr>
        <?php endwhile; } ?>
      </tbody>
    </table>

    <table class="totals">
      <tr><td>Actual Amount</td><td class="text-right">₹<?= sip_invoice_price($subTotal) ?></td></tr>
      <tr><td>Service Tax Total</td><td class="text-right">₹<?= sip_invoice_price($serviceTaxTotal) ?></td></tr>
      <tr><td>Taxable Amount</td><td class="text-right">₹<?= sip_invoice_price($taxableTotal) ?></td></tr>
      <tr><td>GST Total</td><td class="text-right">₹<?= sip_invoice_price($gstTotal) ?></td></tr>
      <tr class="grand-total"><td>Grand Total</td><td class="text-right">₹<?= sip_invoice_price($grandTotal) ?></td></tr>
    </table>

    <div class="footer">
      <b>Payment Method:</b> <?= sip_invoice_h($order['payment_method'] ?? '') ?><br>
      <b>Payment Status:</b> <?= sip_invoice_h($order['payment_status'] ?? '') ?><br>
      <b>Order Status:</b> <?= sip_invoice_h($order['order_status'] ?? '') ?><br><br>
      This is a computer generated invoice.
      <div class="signature">
        <b>For Softpro India Computer Technologies (P) Limited</b><br><br><br>
        Authorised Signatory
      </div>
    </div>
  </div>
</body>
</html>
        <?php
        return trim(ob_get_clean());
    }
}

if (!function_exists('sip_create_invoice_html_file')) {
    function sip_create_invoice_html_file($conn, int $orderId): string
    {
        $html = sip_render_invoice_html($conn, $orderId, ['show_buttons' => false]);
        $order = sip_invoice_get_order($conn, $orderId, null) ?: [];
        $invoiceNo = $order['invoice_no'] ?? ('INV' . date('Ymd') . (int)$orderId);
        $dir = __DIR__ . '/../uploads/invoices';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $safeInvoice = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$invoiceNo);
        $path = $dir . '/invoice_' . $safeInvoice . '.html';
        file_put_contents($path, $html);
        return $path;
    }
}
