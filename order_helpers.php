<?php
if (!function_exists('sip_h')) {
    function sip_h($value)
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sip_order_badge')) {
    function sip_order_badge($status)
    {
        switch ($status) {
            case 'Delivered':
                return 'success';
            case 'Shipped':
                return 'primary';
            case 'Packed':
                return 'warning text-dark';
            case 'Confirmed':
                return 'info text-dark';
            case 'Cancelled':
            case 'Returned':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('sip_payment_badge')) {
    function sip_payment_badge($status)
    {
        switch ($status) {
            case 'Paid':
                return 'success';
            case 'Failed':
                return 'danger';
            case 'Refunded':
                return 'info text-dark';
            default:
                return 'warning text-dark';
        }
    }
}

if (!function_exists('sip_can_customer_cancel')) {
    function sip_can_customer_cancel($status)
    {
        return !in_array($status, ['Shipped', 'Delivered', 'Cancelled', 'Returned'], true);
    }
}

if (!function_exists('sip_history_insert_once')) {
    function sip_history_insert_once($conn, $order_id, $status, $note = '')
    {
        $order_id = (int) $order_id;
        $status_safe = mysqli_real_escape_string($conn, trim((string) $status));
        $note_safe = mysqli_real_escape_string($conn, trim((string) $note));
        $check = mysqli_query($conn, "SELECT id FROM order_status_history WHERE order_id='$order_id' AND status='$status_safe' LIMIT 1");
        if ($check && mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO order_status_history (order_id,status,note) VALUES ('$order_id','$status_safe','$note_safe')");
        }
    }
}

if (!function_exists('sip_sync_order_history')) {
    function sip_sync_order_history($conn, $order_id, $final_status, $note = '')
    {
        $flow = ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];
        if ($final_status === 'Cancelled') {
            sip_history_insert_once($conn, $order_id, 'Cancelled', $note ?: 'Order cancelled');
            return;
        }
        if ($final_status === 'Returned') {
            sip_history_insert_once($conn, $order_id, 'Returned', $note ?: 'Order returned');
            return;
        }
        $idx = array_search($final_status, $flow, true);
        if ($idx === false) {
            $idx = 0;
        }
        $notes = [
            'Pending' => 'Order placed successfully',
            'Confirmed' => 'Order confirmed',
            'Packed' => 'Order packed',
            'Shipped' => 'Order shipped',
            'Delivered' => 'Order delivered'
        ];
        for ($i = 0; $i <= $idx; $i++) {
            sip_history_insert_once($conn, $order_id, $flow[$i], $notes[$flow[$i]] ?? '');
        }
        if ($note !== '') {
            sip_history_insert_once($conn, $order_id, $final_status, $note);
        }
    }
}

if (!function_exists('sip_history_query')) {
    function sip_history_query($conn, $order_id)
    {
        $order_id = (int) $order_id;
        return mysqli_query($conn, "
            SELECT h1.*
            FROM order_status_history h1
            INNER JOIN (
                SELECT status, MIN(id) AS min_id
                FROM order_status_history
                WHERE order_id='$order_id'
                GROUP BY status
            ) h2 ON h1.id = h2.min_id
            ORDER BY FIELD(h1.status,'Pending','Confirmed','Packed','Shipped','Delivered','Cancelled','Returned'), h1.id ASC
        ");
    }
}

if (!function_exists('sip_order_card_css')) {
    function sip_order_card_css()
    {
        return '<style>
        .sip-order-toolbar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px}.sip-order-toolbar a{padding:10px 16px;border-radius:999px;border:1px solid #ff5a1f;color:#ff5a1f;font-weight:800;text-decoration:none;background:#fff}.sip-order-toolbar a.active,.sip-order-toolbar a:hover{background:#ff5a1f;color:#fff}.sip-order-card{background:#fff;border:1px solid #eee;border-radius:20px;box-shadow:0 15px 40px rgba(15,23,42,.07);padding:22px;margin-bottom:24px}.sip-order-top{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;align-items:flex-start;border-bottom:1px solid #f1f5f9;padding-bottom:16px;margin-bottom:12px}.sip-order-no{font-size:20px;font-weight:900;color:#111827;margin:0}.sip-order-meta{color:#64748b;font-size:14px}.sip-pill{display:inline-block;padding:8px 12px;border-radius:10px;color:#fff;font-weight:800;font-size:13px}.sip-item{display:flex;align-items:center;gap:16px;padding:12px 0;border-bottom:1px solid #f1f5f9}.sip-item:last-child{border-bottom:0}.sip-item img{width:78px;height:78px;object-fit:cover;border-radius:14px;border:1px solid #eee;background:#f8fafc}.sip-item-name{font-weight:900;color:#111827;font-size:17px}.sip-item-sub{color:#64748b;font-size:14px}.sip-item-total{margin-left:auto;font-weight:900;color:#111827;font-size:17px}.sip-order-footer{display:flex;justify-content:space-between;gap:15px;align-items:center;flex-wrap:wrap;background:#fff7ed;border-radius:14px;padding:14px;margin-top:14px}.sip-total{font-size:22px;font-weight:900;color:#111827}.sip-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}.sip-btn{border:0;border-radius:10px;padding:9px 14px;font-weight:800;text-decoration:none;display:inline-block}.sip-btn-primary{background:#ff5a1f;color:#fff}.sip-btn-outline{background:#fff;color:#ff5a1f;border:1px solid #ff5a1f}.sip-btn-danger{background:#e11d48;color:#fff}.sip-btn-dark{background:#111827;color:#fff}.sip-empty{text-align:center;background:#fff;border:1px dashed #cbd5e1;border-radius:20px;padding:50px;color:#64748b}.sip-info-grid{display:flex;gap:18px;flex-wrap:wrap;color:#475569}.sip-info-grid b{color:#111827}@media(max-width:600px){.sip-item{align-items:flex-start}.sip-item-total{width:100%;margin-left:94px}.sip-order-footer{align-items:flex-start}.sip-actions{justify-content:flex-start}}
        </style>';
    }
}

if (!function_exists('sip_order_column_exists')) {
    function sip_order_column_exists($conn, $column)
    {
        $column = mysqli_real_escape_string($conn, $column);
        $q = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE '$column'");
        return $q && mysqli_num_rows($q) > 0;
    }
}

if (!function_exists('sip_restore_order_stock_once')) {
    function sip_restore_order_stock_once($conn, $order_id)
    {
        $order_id = (int) $order_id;
        if ($order_id <= 0)
            return false;

        $hasStockRestored = sip_order_column_exists($conn, 'stock_restored');
        if ($hasStockRestored) {
            $check = mysqli_query($conn, "SELECT stock_restored FROM orders WHERE id='$order_id' LIMIT 1");
            $row = $check ? mysqli_fetch_assoc($check) : null;
            if ((int) ($row['stock_restored'] ?? 0) === 1) {
                return false;
            }
        }

        $items = mysqli_query($conn, "SELECT product_id, qty FROM order_items WHERE order_id='$order_id'");
        if ($items) {
            while ($item = mysqli_fetch_assoc($items)) {
                $product_id = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['qty'] ?? 0);
                if ($product_id > 0 && $qty > 0) {
                    mysqli_query($conn, "UPDATE products SET stock = stock + $qty WHERE id = '$product_id'");
                }
            }
        }

        if ($hasStockRestored) {
            mysqli_query($conn, "UPDATE orders SET stock_restored='1' WHERE id='$order_id'");
        }
        return true;
    }
}
