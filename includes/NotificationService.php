<?php
require_once __DIR__ . '/Mailer.php';
require_once __DIR__ . '/InvoicePdf.php';

class NotificationService
{
    private SipMailer $mailer;

    public function __construct()
    {
        $this->mailer = new SipMailer();
    }

    private function esc($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function money($value): string
    {
        return number_format((float)$value, 2);
    }

    private function orderItemsHtml(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $rows = '';
        foreach ($items as $item) {
            $title = $this->esc($item['title'] ?? 'Product');
            $qty = (int)($item['qty'] ?? 0);
            $price = $this->money($item['price'] ?? 0);
            $total = $this->money(((float)($item['price'] ?? 0)) * $qty);
            $rows .= "<tr>"
                . "<td style='padding:10px;border-bottom:1px solid #e5e7eb;'>{$title}</td>"
                . "<td style='padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;'>{$qty}</td>"
                . "<td style='padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;'>₹{$price}</td>"
                . "<td style='padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;'>₹{$total}</td>"
                . "</tr>";
        }

        return "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin:18px 0;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;'>"
            . "<thead><tr style='background:#111827;color:#fff;'>"
            . "<th align='left' style='padding:10px;'>Item</th><th style='padding:10px;'>Qty</th><th align='right' style='padding:10px;'>Price</th><th align='right' style='padding:10px;'>Total</th>"
            . "</tr></thead><tbody>{$rows}</tbody></table>";
    }

    /**
     * Send Order Confirmation to user and order alert to admin.
     */
    public function sendOrderConfirmation($userPhone, $userEmail, $orderDetails)
    {
        $orderNo = $orderDetails['order_no'] ?? 'N/A';
        $total = $orderDetails['total'] ?? '0';
        $customerName = $orderDetails['customer_name'] ?? 'Customer';
        $paymentMethod = $orderDetails['payment_method'] ?? 'N/A';
        $paymentStatus = $orderDetails['payment_status'] ?? 'N/A';
        $itemsHtml = $this->orderItemsHtml($orderDetails['items'] ?? []);

        if (!empty($userEmail) && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $this->mailer->send(
                $userEmail,
                "Order Placed Successfully - {$orderNo}",
                "<p>Hello <b>" . $this->esc($customerName) . "</b>,</p>"
                . "<p>Thank you for your order. Your order has been placed successfully and our team will process it shortly.</p>"
                . "<div style='background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;padding:16px;margin:16px 0;'>"
                . "<b>Order No:</b> " . $this->esc($orderNo) . "<br>"
                . "<b>Total:</b> ₹" . $this->esc($total) . "<br>"
                . "<b>Payment Method:</b> " . $this->esc($paymentMethod) . "<br>"
                . "<b>Payment Status:</b> " . $this->esc($paymentStatus) . "</div>"
                . $itemsHtml
                . "<p>You can track this order from your profile. Invoice PDF will be sent automatically after delivery.</p>"
            );
        }

        $this->mailer->send(
            $this->mailer->adminEmail(),
            "New Order Received - {$orderNo}",
            "<p>A new order has been placed on the website.</p>"
            . "<p><b>Customer:</b> " . $this->esc($customerName) . "<br>"
            . "<b>Email:</b> " . $this->esc($userEmail) . "<br>"
            . "<b>Mobile:</b> " . $this->esc($userPhone) . "<br>"
            . "<b>Order No:</b> " . $this->esc($orderNo) . "<br>"
            . "<b>Total:</b> ₹" . $this->esc($total) . "<br>"
            . "<b>Payment Method:</b> " . $this->esc($paymentMethod) . "<br>"
            . "<b>Payment Status:</b> " . $this->esc($paymentStatus) . "</p>" . $itemsHtml,
            (string)$userEmail
        );

        return true;
    }

    public function sendWelcomeEmail($userEmail, $name): bool
    {
        return $this->mailer->send(
            $userEmail,
            'Welcome to Softpro Innovations',
            '<p>Hello <b>' . $this->esc($name) . '</b>,</p>'
            . '<p>Welcome to Softpro Innovations. Your account has been created successfully.</p>'
            . '<p>You can now login, manage your profile, place orders, track orders, save wishlist products, and download invoices after delivery.</p>'
            . '<p style="margin-top:18px;"><b>Thank you for joining us!</b></p>'
        );
    }

    public function sendDeliveredInvoice($conn, int $orderId): bool
    {
        $orderId = (int)$orderId;
        $order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id='$orderId' LIMIT 1"));
        if (!$order) {
            throw new Exception('Order not found for delivered invoice email.');
        }

        $email = trim((string)($order['customer_email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $invoicePath = sip_create_invoice_pdf($conn, $orderId);
        $invoiceNo = $order['invoice_no'] ?: ('INV' . date('Ymd') . $orderId);
        $orderNo = $order['order_no'] ?? 'N/A';
        $customerName = $order['customer_name'] ?? 'Customer';

        return $this->mailer->send(
            $email,
            "Order Delivered - Invoice {$invoiceNo}",
            "<p>Hello <b>" . $this->esc($customerName) . "</b>,</p>"
            . "<p>Your order <b>#" . $this->esc($orderNo) . "</b> has been delivered successfully.</p>"
            . "<div style='background:#ecfdf5;border:1px solid #bbf7d0;border-radius:14px;padding:16px;margin:16px 0;'>"
            . "<b>Invoice No:</b> " . $this->esc($invoiceNo) . "<br>"
            . "<b>Grand Total:</b> ₹" . $this->money($order['total_amount'] ?? 0) . "<br>"
            . "<b>Payment Status:</b> " . $this->esc($order['payment_status'] ?? 'Paid') . "</div>"
            . "<p>Your invoice PDF is attached with this email.</p>"
            . "<p>Thank you for shopping with Softpro Innovations.</p>",
            '',
            [[
                'path' => $invoicePath,
                'name' => 'Invoice-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string)$invoiceNo) . '.pdf',
                'type' => 'application/pdf'
            ]]
        );
    }

    public function sendShippingUpdate($userPhone, $userEmail, $trackingNo, $status)
    {
        return $this->mailer->send(
            $userEmail,
            "Shipping Update",
            "<p>Your order is now <b>" . $this->esc($status) . "</b>.</p>"
            . "<p><b>Tracking No:</b> " . $this->esc($trackingNo) . "</p>"
        );
    }

    public function sendPaymentSuccess($userPhone, $userEmail, $orderNo)
    {
        return $this->mailer->send(
            $userEmail,
            "Payment Success",
            "<p>Payment received successfully for order <b>#" . $this->esc($orderNo) . "</b>.</p>"
        );
    }

    public function sendOffer($userPhone, $userEmail, $offerDetails)
    {
        return $this->mailer->send(
            $userEmail,
            "Special Offer Just For You!",
            "<p>Exclusive Offer: <b>" . $this->esc($offerDetails['title'] ?? '') . "</b></p>"
            . "<p>Use code <b>" . $this->esc($offerDetails['code'] ?? '') . "</b>.</p>"
        );
    }
}
