<?php
if (!function_exists('ecom_money')) {
    function ecom_money($amount)
    {
        $formatted = format_price((float)$amount);
        return preg_replace('/\.00$/', '', $formatted);
    }
}

if (!function_exists('ecom_get_product_coupon')) {
    function ecom_get_product_coupon($productId)
    {
        global $conn;
        if (!$conn || $productId <= 0) return null;

        static $coupons_cache = null;

        if ($coupons_cache === null) {
            $coupons_cache = [];
            // Fetch all active coupons mapped to specific products or all products (0)
            $q = mysqli_query($conn, "SELECT * FROM coupons WHERE status = 1");
            if ($q) {
                while ($c = mysqli_fetch_assoc($q)) {
                    $pid = (int)$c['product_id'];
                    if (!isset($coupons_cache[$pid])) {
                        $coupons_cache[$pid] = [];
                    }
                    $coupons_cache[$pid][] = $c;
                }
            }
        }

        // Prioritize specific product coupon, fallback to global (product_id = 0)
        if (!empty($coupons_cache[$productId])) {
            return $coupons_cache[$productId][0]; // Return the first matching
        }
        
        if (!empty($coupons_cache[0])) {
            return $coupons_cache[0][0]; // Return the first global matching
        }

        return null;
    }
}

if (!function_exists('ecom_product_price')) {
    function ecom_product_price($product)
    {
        $base = (float)($product['base_price'] ?? $product['product_base_price'] ?? 0);
        $final = (float)($product['price'] ?? 0);
        $defaultTaxPercent = (float)($product['default_tax_percentage'] ?? 2.00);
        $gstPercent = (float)($product['tax_percentage'] ?? 0);

        if ($base <= 0 && $final > 0) {
            $divisor = (1 + ($defaultTaxPercent / 100)) * (1 + ($gstPercent / 100));
            $base = $divisor > 0 ? ($final / $divisor) : $final;
        }

        if ($base > 0) {
            $serviceTaxAmount = ($base * $defaultTaxPercent) / 100;
            $taxableAmount    = $base + $serviceTaxAmount;
            $gstAmount        = ($taxableAmount * $gstPercent) / 100;
            $final            = $taxableAmount + $gstAmount;
        }

        $tax = max(0, $final - $base);

        return [
            'base'                => $base,
            'sale'                => $final,
            'original'            => (float)($product['old_price'] ?? $final),
            'tax'                 => $tax,
            'tax_percent'         => $defaultTaxPercent + $gstPercent,
            'default_tax_percent' => $defaultTaxPercent,
            'gst_percent'         => $gstPercent
        ];
    }
}

if (!function_exists('ecom_cart_totals')) {
    function ecom_cart_totals($cartRows)
    {
        $subtotal = 0;
        $taxTotal = 0;
        $itemsTotal = 0;
        $deliveryTotal = 0;
        foreach ($cartRows as $row) {
            $qty = (int)($row['qty'] ?? 1);
            $p = ecom_product_price($row);
            $subtotal += $p['base'] * $qty;
            $taxTotal += $p['tax'] * $qty;
            $itemsTotal += $p['sale'] * $qty;
            $deliveryTotal += (float)($row['delivery_charge'] ?? 0) * $qty;
        }
        return [
            'subtotal' => $subtotal,
            'tax' => $taxTotal,
            'items_total' => $itemsTotal,
            'delivery_total' => $deliveryTotal
        ];
    }
}

if (!function_exists('ecom_delivery_quote')) {
    function ecom_delivery_quote($conn, $pincode = '', $subtotal = 0, $weight = 0)
    {
        $subtotal = (float)$subtotal;
        $weight = max(0.1, (float)$weight);
        $freeAbove = 999.00;
        if ($subtotal >= $freeAbove) {
            return ['serviceable' => true, 'charge' => 0.00, 'partner' => 'Free Delivery', 'message' => 'Free delivery applied'];
        }
        $charge = 0.00;
        $zone = 'Default';
        if ($pincode !== '') {
            $pin = mysqli_real_escape_string($conn, preg_replace('/\D/', '', $pincode));
            if (strlen($pin) === 6) {
                $zq = mysqli_query($conn, "SELECT sc.charge, sz.zone_name FROM shipping_charges sc LEFT JOIN shipping_zones sz ON sz.id=sc.zone_id WHERE '$weight' BETWEEN sc.min_weight AND sc.max_weight ORDER BY sc.charge ASC LIMIT 1");
                if ($zq && ($zr = mysqli_fetch_assoc($zq))) {
                    $charge = (float)$zr['charge'];
                    $zone = $zr['zone_name'] ?: 'Configured Zone';
                }
            }
        }
        return ['serviceable' => true, 'charge' => $charge, 'partner' => 'Product Delivery Charge', 'message' => $zone];
    }
}
