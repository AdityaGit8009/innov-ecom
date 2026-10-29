<?php

if (!function_exists('sip_pdf_escape')) {
    function sip_pdf_escape($text)
    {
        $text = (string)($text ?? '');
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}

if (!function_exists('sip_invoice_money')) {
    function sip_invoice_money($amount)
    {
        return number_format((float)$amount, 2);
    }
}

if (!function_exists('sip_invoice_h')) {
    function sip_invoice_h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sip_invoice_fetch_data')) {
    /**
     * Fetches the same invoice data/calculation that invoice.php shows to user.
     */
    function sip_invoice_fetch_data($conn, int $orderId): array
    {
        $orderId = (int)$orderId;
        $order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id='$orderId' LIMIT 1"));
        if (!$order) {
            throw new Exception('Order not found for invoice PDF.');
        }

        if (empty($order['invoice_no'])) {
            $invoiceNo = 'INV' . date('Ymd') . $orderId;
            $invoiceNoEsc = mysqli_real_escape_string($conn, $invoiceNo);
            mysqli_query($conn, "UPDATE orders SET invoice_no='$invoiceNoEsc' WHERE id='$orderId'");
            $order['invoice_no'] = $invoiceNo;
        }

        $address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer_addresses WHERE id='" . (int)($order['address_id'] ?? 0) . "' LIMIT 1"));
        $addressParts = [];
        if ($address) {
            foreach (['house_no', 'area', 'landmark', 'city', 'state', 'country', 'pincode'] as $field) {
                if (!empty($address[$field])) {
                    $addressParts[] = $address[$field];
                }
            }
        }
        $customerAddress = trim(implode(', ', $addressParts));
        if ($customerAddress === '') {
            $customerAddress = 'N/A';
        }

        $itemResult = mysqli_query($conn, "
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

        $items = [];
        $sr = 1;
        $subTotal = 0;
        $serviceTaxTotal = 0;
        $taxableTotal = 0;
        $gstTotal = 0;
        $grandTotal = 0;

        if ($itemResult) {
            while ($it = mysqli_fetch_assoc($itemResult)) {
                $qty = (int)($it['qty'] ?? 0);
                $finalPrice = (float)($it['price'] ?? 0); // Same as invoice.php: final price from order_items, already GST included.
                $gstPercent = (float)($it['tax_percentage'] ?? 0);
                $taxableAmount = $gstPercent > -100 ? ($finalPrice / (1 + ($gstPercent / 100))) : $finalPrice;
                $gstAmount = $finalPrice - $taxableAmount;
                $basePrice = $taxableAmount / 1.02; // Same 2% service tax logic from invoice.php.
                $serviceTaxAmount = $taxableAmount - $basePrice;
                $lineTotal = $finalPrice * $qty;

                $subTotal += ($basePrice * $qty);
                $serviceTaxTotal += ($serviceTaxAmount * $qty);
                $taxableTotal += ($taxableAmount * $qty);
                $gstTotal += ($gstAmount * $qty);
                $grandTotal += $lineTotal;

                $items[] = [
                    'sr' => $sr++,
                    'title' => (string)($it['title'] ?? 'Product'),
                    'hsn' => '8542',
                    'qty' => $qty,
                    'base_price' => $basePrice,
                    'service_tax' => $serviceTaxAmount,
                    'taxable_amount' => $taxableAmount,
                    'gst_percent' => $gstPercent,
                    'gst_amount' => $gstAmount,
                    'final_price' => $finalPrice,
                    'line_total' => $lineTotal,
                ];
            }
        }

        return [
            'order' => $order,
            'address' => $customerAddress,
            'items' => $items,
            'totals' => [
                'actual_amount' => $subTotal,
                'service_tax_total' => $serviceTaxTotal,
                'taxable_amount' => $taxableTotal,
                'gst_total' => $gstTotal,
                'grand_total' => $grandTotal,
            ],
            'invoice_no' => !empty($order['invoice_no']) ? $order['invoice_no'] : ('INV-' . ($order['order_no'] ?? $orderId)),
            'invoice_date' => date('d-m-Y', strtotime($order['created_at'] ?? 'now')),
        ];
    }
}

if (!function_exists('sip_render_invoice_html')) {
    /**
     * HTML layout intentionally matches invoice.php user view, without Back/Print buttons.
     */
    function sip_render_invoice_html(array $data): string
    {
        $order = $data['order'];
        $items = $data['items'];
        $totals = $data['totals'];
        $invoiceNo = $data['invoice_no'];
        $invoiceDate = $data['invoice_date'];
        $customerAddress = $data['address'];

        ob_start();
        ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice <?= sip_invoice_h($invoiceNo) ?></title>
  <style>
    * { box-sizing: border-box; }
    @page { size: A4 landscape; margin: 10mm; }
    body { font-family: Arial, sans-serif; margin: 0; color: #111; background: #fff; }
    .invoice-box { width: 100%; max-width: 1100px; margin: auto; border: 2px solid #000; padding: 18px; }
    .top-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 14px; }
    .company-name { font-size: 28px; font-weight: 700; }
    .small { font-size: 12px; line-height: 20px; }
    .invoice-title { font-size: 34px; font-weight: 700; text-align: right; }
    .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 14px; }
    .info-table td { border: 1px solid #000; padding: 14px; font-size: 13px; vertical-align: top; line-height: 24px; }
    .address-title { font-size: 16px; font-weight: 700; margin-bottom: 10px; color: #111827; border-bottom: 1px dashed #ccc; padding-bottom: 6px; }
    .product-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .product-table th, .product-table td { border: 1px solid #000; padding: 7px 5px; font-size: 11px; word-wrap: break-word; }
    .product-table th { background: #f3f4f6; text-align: center; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .totals { width: 420px; margin-left: auto; margin-top: 18px; border-collapse: collapse; }
    .totals td { border: 1px solid #000; padding: 10px; font-size: 14px; }
    .grand-total { background: #f3f4f6; font-size: 18px; font-weight: 700; }
    .footer { margin-top: 35px; font-size: 12px; line-height: 22px; }
    .signature { text-align: right; margin-top: 40px; }
  </style>
</head>
<body>
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
          Order ID: <b><?= sip_invoice_h($order['order_no'] ?? '') ?></b>
          <?php if (!empty($order['razorpay_payment_id'])): ?>
            <br>Razorpay Payment ID: <b><?= sip_invoice_h($order['razorpay_payment_id']) ?></b>
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
          <th style="width:4%;">SR</th>
          <th style="width:21%;">Product</th>
          <th style="width:6%;">HSN</th>
          <th style="width:5%;">Qty</th>
          <th style="width:10%;">Actual Price</th>
          <th style="width:10%;">Service Tax</th>
          <th style="width:11%;">Taxable Amount</th>
          <th style="width:7%;">GST %</th>
          <th style="width:9%;">GST Amount</th>
          <th style="width:9%;">Final Price</th>
          <th style="width:8%;">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td class="text-center"><?= (int)$it['sr'] ?></td>
            <td><?= sip_invoice_h($it['title']) ?></td>
            <td class="text-center"><?= sip_invoice_h($it['hsn']) ?></td>
            <td class="text-center"><?= (int)$it['qty'] ?></td>
            <td class="text-right">₹<?= sip_invoice_money($it['base_price']) ?></td>
            <td class="text-right">₹<?= sip_invoice_money($it['service_tax']) ?></td>
            <td class="text-right">₹<?= sip_invoice_money($it['taxable_amount']) ?></td>
            <td class="text-center"><?= sip_invoice_money($it['gst_percent']) ?>%</td>
            <td class="text-right">₹<?= sip_invoice_money($it['gst_amount']) ?></td>
            <td class="text-right">₹<?= sip_invoice_money($it['final_price']) ?></td>
            <td class="text-right">₹<?= sip_invoice_money($it['line_total']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <table class="totals">
      <tr><td>Actual Amount</td><td class="text-right">₹<?= sip_invoice_money($totals['actual_amount']) ?></td></tr>
      <tr><td>Service Tax Total</td><td class="text-right">₹<?= sip_invoice_money($totals['service_tax_total']) ?></td></tr>
      <tr><td>Taxable Amount</td><td class="text-right">₹<?= sip_invoice_money($totals['taxable_amount']) ?></td></tr>
      <tr><td>GST Total</td><td class="text-right">₹<?= sip_invoice_money($totals['gst_total']) ?></td></tr>
      <tr class="grand-total"><td>Grand Total</td><td class="text-right">₹<?= sip_invoice_money($totals['grand_total']) ?></td></tr>
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

if (!function_exists('sip_command_exists')) {
    function sip_command_exists(string $command): bool
    {
        if (!function_exists('shell_exec')) {
            return false;
        }
        $where = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'command -v';
        $result = @shell_exec($where . ' ' . escapeshellarg($command) . ' 2>/dev/null');
        return is_string($result) && trim($result) !== '';
    }
}

if (!function_exists('sip_html_to_pdf_with_browser')) {
    /**
     * Generates a proper visual PDF from the same invoice HTML using installed Chrome/Chromium when available.
     */
    function sip_html_to_pdf_with_browser(string $html, string $pdfPath): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $tmpHtml = tempnam(sys_get_temp_dir(), 'sip_invoice_') . '.html';
        file_put_contents($tmpHtml, $html);

        $candidates = [
            'google-chrome',
            'google-chrome-stable',
            'chromium',
            'chromium-browser',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            'wkhtmltopdf',
            '/usr/bin/wkhtmltopdf',
        ];

        foreach ($candidates as $cmd) {
            $isFullPath = strpos($cmd, '/') !== false || strpos($cmd, '\\') !== false;
            if ($isFullPath) {
                if (!is_file($cmd) || !is_executable($cmd)) {
                    continue;
                }
            } elseif (!sip_command_exists($cmd)) {
                continue;
            }

            $output = [];
            $code = 1;
            if (stripos($cmd, 'wkhtmltopdf') !== false) {
                $command = escapeshellcmd($cmd) . ' --encoding utf-8 --orientation Landscape ' . escapeshellarg($tmpHtml) . ' ' . escapeshellarg($pdfPath) . ' 2>&1';
            } else {
                $command = escapeshellcmd($cmd) . ' --headless --no-sandbox --disable-gpu --disable-dev-shm-usage --print-to-pdf=' . escapeshellarg($pdfPath) . ' ' . escapeshellarg($tmpHtml) . ' 2>&1';
            }
            @exec($command, $output, $code);

            if ($code === 0 && is_file($pdfPath) && filesize($pdfPath) > 1000) {
                @unlink($tmpHtml);
                return true;
            }
        }

        @unlink($tmpHtml);
        return false;
    }
}

if (!function_exists('sip_build_simple_pdf')) {
    /**
     * Fallback valid PDF. It keeps the same invoice fields/calculation if browser PDF is unavailable on hosting.
     */
    function sip_build_simple_pdf(array $lines)
    {
        $content = "BT\n/F1 9 Tf\n36 806 Td\n11 TL\n";
        foreach ($lines as $line) {
            $content .= '(' . sip_pdf_escape($line) . ") Tj\nT*\n";
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xref . "\n%%EOF";
        return $pdf;
    }
}

if (!function_exists('sip_create_invoice_pdf')) {
    /**
     * Creates invoice PDF attachment for delivered email.
     * The PDF is generated from the same invoice layout/details that user sees on invoice.php.
     */
    function sip_create_invoice_pdf($conn, int $orderId): string
    {
        $data = sip_invoice_fetch_data($conn, $orderId);
        $order = $data['order'];

        $dir = __DIR__ . '/../uploads/invoices';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $safeInvoice = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$data['invoice_no']);
        if ($safeInvoice === '') {
            $safeInvoice = 'INV' . date('Ymd') . $orderId;
        }
        $path = $dir . '/invoice_' . $safeInvoice . '.pdf';

        $html = sip_render_invoice_html($data);
        if (sip_html_to_pdf_with_browser($html, $path)) {
            return $path;
        }

        // Fallback: still sends a valid PDF with same invoice data if hosting has no Chrome/wkhtmltopdf.
        $lines = [];
        $lines[] = 'Softpro India Computer Technologies (P) Limited';
        $lines[] = 'TAX INVOICE';
        $lines[] = 'Invoice No: ' . $data['invoice_no'] . '    Date: ' . $data['invoice_date'] . '    Order ID: ' . ($order['order_no'] ?? 'N/A');
        if (!empty($order['razorpay_payment_id'])) {
            $lines[] = 'Razorpay Payment ID: ' . $order['razorpay_payment_id'];
        }
        $lines[] = 'Bill/Ship To: ' . ($order['customer_name'] ?? '') . ' | ' . ($order['customer_phone'] ?? '') . ' | ' . ($order['customer_email'] ?? '');
        $lines[] = 'Address: ' . $data['address'];
        $lines[] = str_repeat('-', 134);
        $lines[] = str_pad('SR', 4) . str_pad('Product', 32) . str_pad('HSN', 7) . str_pad('Qty', 5) . str_pad('Actual', 11) . str_pad('Service', 11) . str_pad('Taxable', 12) . str_pad('GST%', 7) . str_pad('GST Amt', 11) . str_pad('Final', 11) . 'Total';
        $lines[] = str_repeat('-', 134);
        foreach ($data['items'] as $item) {
            $lines[] = str_pad((string)$item['sr'], 4)
                . str_pad(substr((string)$item['title'], 0, 30), 32)
                . str_pad($item['hsn'], 7)
                . str_pad((string)$item['qty'], 5)
                . str_pad(sip_invoice_money($item['base_price']), 11)
                . str_pad(sip_invoice_money($item['service_tax']), 11)
                . str_pad(sip_invoice_money($item['taxable_amount']), 12)
                . str_pad(sip_invoice_money($item['gst_percent']), 7)
                . str_pad(sip_invoice_money($item['gst_amount']), 11)
                . str_pad(sip_invoice_money($item['final_price']), 11)
                . sip_invoice_money($item['line_total']);
        }
        $lines[] = str_repeat('-', 134);
        $lines[] = 'Actual Amount: Rs. ' . sip_invoice_money($data['totals']['actual_amount']);
        $lines[] = 'Service Tax Total: Rs. ' . sip_invoice_money($data['totals']['service_tax_total']);
        $lines[] = 'Taxable Amount: Rs. ' . sip_invoice_money($data['totals']['taxable_amount']);
        $lines[] = 'GST Total: Rs. ' . sip_invoice_money($data['totals']['gst_total']);
        $lines[] = 'Grand Total: Rs. ' . sip_invoice_money($data['totals']['grand_total']);
        $lines[] = 'Payment Method: ' . ($order['payment_method'] ?? '') . ' | Payment Status: ' . ($order['payment_status'] ?? '') . ' | Order Status: ' . ($order['order_status'] ?? '');
        $lines[] = 'This is a computer generated invoice. Authorised Signatory.';

        file_put_contents($path, sip_build_simple_pdf($lines));
        return $path;
    }
}
