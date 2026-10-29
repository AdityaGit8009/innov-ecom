<?php
include 'header.php';
include "config/db.php";

$product_id = (int)($_GET['id'] ?? 1);
$res = mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'");
$product = mysqli_fetch_assoc($res);

if (!$product) {
    echo "<p class='text-center my-5'>Product not found</p>";
    include 'footer.php';
    exit;
}

/* ================= RECENTLY VIEWED PRODUCTS =================
   Latest opened product is stored in session and shown on home/product pages.
============================================================ */
if (!isset($_SESSION['recently_viewed_products']) || !is_array($_SESSION['recently_viewed_products'])) {
    $_SESSION['recently_viewed_products'] = [];
}
$_SESSION['recently_viewed_products'] = array_values(array_diff($_SESSION['recently_viewed_products'], [$product_id]));
array_unshift($_SESSION['recently_viewed_products'], $product_id);
$_SESSION['recently_viewed_products'] = array_slice($_SESSION['recently_viewed_products'], 0, 12);


function pval($arr, $key, $default = '')
{
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}

$images = [];
foreach (['main_image', 'image1', 'image2', 'image3', 'image4'] as $imgField) {
    if (!empty($product[$imgField])) {
        $images[] = "uploads/products/" . $product[$imgField];
    }
}
if (empty($images)) $images[] = "assets/images/no-image.png";

$productTitle      = pval($product, 'title', 'Product');
$productStore      = pval($product, 'store', 'SPI Store');
$productSeller     = pval($product, 'seller', 'SIPL');
$productPrice      = (float)pval($product, 'price', 0);
$productOldPrice   = (float)pval($product, 'old_price', 0);
$productDiscount   = pval($product, 'discount', 0);
$productRating     = pval($product, 'rating', '5.0');
$productRatingsCount = pval($product, 'ratings_count', '0');
$productBought     = pval($product, 'bought', 'Recently bought');
$productStock      = (int)pval($product, 'stock', 0);
$description       = pval($product, 'description', $productTitle . ' is a reliable electronics component suitable for academic projects, DIY circuits, embedded systems, robotics, and practical lab experiments.');
$brand    = pval($product, 'brand', $productStore);
$model    = pval($product, 'model', 'Standard Module');
$category = pval($product, 'category', 'Electronics Component');
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<!-- ============================================================
     PRODUCT PAGE STYLES
============================================================ -->
<style>
    /* ── Reset & Tokens ── */
    :root {
        --brand: #ef662f;
        --brand-dark: #d4521e;
        --brand-light: #fff4ef;
        --brand-mid: #ffdfd0;
        --ink: #0f0f0f;
        --ink-2: #374151;
        --ink-3: #6b7280;
        --line: #e5e7eb;
        --white: #ffffff;
        --success: #16a34a;
        --radius-sm: 8px;
        --radius-md: 14px;
        --radius-lg: 22px;
        --shadow-soft: 0 4px 24px rgba(0, 0, 0, .07);
        --shadow-card: 0 8px 40px rgba(239, 102, 47, .10);
        --transition: .25s cubic-bezier(.4, 0, .2, 1);
    }

    /* ── Base ── */
    .pdp-wrap * {
        box-sizing: border-box;
    }

    .pdp-wrap {
        color: var(--ink-2);
    }

    /* ── Breadcrumb ── */
    .pdp-breadcrumb {
        background: linear-gradient(135deg, #fff4ef 0%, #fff9f7 100%);
        border-bottom: 1px solid var(--brand-mid);
        padding: 14px 0;
    }

    .pdp-breadcrumb .inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink-3);
    }

    .pdp-breadcrumb a {
        color: var(--brand);
        text-decoration: none;
    }

    .pdp-breadcrumb span {
        color: var(--ink-3);
    }

    /* ── Main Grid ── */
    .pdp-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 28px 20px;
    }

    .pdp-grid {
        display: grid;
        grid-template-columns: 420px 1fr 300px;
        gap: 28px;
        align-items: start;
    }

    /* ══════════════════════════
   LEFT — GALLERY
══════════════════════════ */
    .pdp-gallery {
        position: sticky;
        top: 88px;
    }

    .gallery-wrap {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .thumb-col {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 0 0 64px;
    }

    .thumb-item {
        width: 64px;
        height: 72px;
        border: 2px solid var(--line);
        border-radius: var(--radius-sm);
        overflow: hidden;
        cursor: pointer;
        background: var(--white);
        transition: var(--transition);
        padding: 4px;
    }

    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .thumb-item:hover,
    .thumb-item.active {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(239, 102, 47, .15);
    }

    .main-img-box {
        flex: 1;
        height: 400px;
        border: 1.5px solid var(--line);
        border-radius: var(--radius-md);
        overflow: hidden;
        cursor: crosshair;
        background: var(--white);
        position: relative;
    }

    .main-img-box img {
        width: 100%;
        height: 100%;
        object-fit: fill;
        transition: opacity .3s ease;
    }

    /* Badge on main image */
    .img-discount-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: var(--brand);
        color: #fff;

        font-size: 13px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 30px;
        z-index: 2;
        letter-spacing: .5px;
    }

    /* Zoom preview */
    #zoomPreviewNew {
        position: fixed;
        width: 460px;
        height: 400px;
        border: 1.5px solid var(--line);
        border-radius: var(--radius-md);
        background-repeat: no-repeat;
        background-size: 250%;
        background-color: var(--white);
        display: none;
        z-index: 99999;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .16);
        pointer-events: none;
    }

    /* ══════════════════════════
   MIDDLE — DETAILS
══════════════════════════ */
    .pdp-details {
        padding: 0;
    }

    .pdp-store-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--brand-light);
        color: var(--brand);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 30px;
        margin-bottom: 12px;
        border: 1px solid var(--brand-mid);
    }

    .pdp-title {

        font-size: 18px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1.3;
        margin-bottom: 14px;
    }

    /* Rating row */
    .pdp-rating-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .rating-pill {
        background: var(--brand);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        padding: 5px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .rating-text {
        font-size: 13px;
        color: var(--ink-3);
    }

    .bought-badge {
        background: #f0fdf4;
        color: var(--success);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 30px;
        border: 1px solid #bbf7d0;
    }

    /* Divider */
    .pdp-divider {
        border: none;
        border-top: 1.5px solid var(--line);
        margin: 16px 0;
    }

    /* Price block */
    .pdp-price-block {
        margin-bottom: 20px;
    }

    .price-top-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .pdp-discount-tag {
        background: var(--brand);
        color: #fff;

        font-weight: 700;
        font-size: 14px;
        padding: 5px 12px;
        border-radius: 6px;
    }

    .pdp-current-price {

        font-size: 20px;
        font-weight: 700;
        color: var(--brand);
        line-height: 1;
    }

    .pdp-old-price {
        font-size: 14px;
        color: var(--ink-3);
        margin-bottom: 4px;
    }

    .pdp-old-price del {
        letter-spacing: .3px;
    }

    .pdp-tax-note {
        font-size: 13px;
        color: var(--success);
        font-weight: 500;
    }

    /* Info cards */
    .pdp-card {
        border: 1.5px solid var(--brand-mid);
        background: var(--brand-light);
        border-radius: var(--radius-md);
        padding: 18px 20px;
        margin-bottom: 16px;
    }

    .pdp-card-title {

        font-size: 15px;
        font-weight: 700;
        color: var(--brand);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pdp-card-title::before {
        content: '';
        width: 4px;
        height: 16px;
        background: var(--brand);
        border-radius: 4px;
        display: inline-block;
    }

    .pdp-card ul {
        padding-left: 18px;
        margin: 0;
    }

    .pdp-card ul li {
        font-size: 14px;
        color: var(--ink-2);
        margin-bottom: 6px;
        line-height: 1.6;
    }

    .pdp-card ul li:last-child {
        margin-bottom: 0;
    }

    .pdp-card p {
        font-size: 14px;
        color: var(--ink-2);
        line-height: 1.7;
        margin: 0;
    }

    /* Share */
    .pdp-share-wrap {
        position: relative;
        display: inline-block;
    }

    .pdp-share-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid var(--line);
        background: var(--white);
        color: var(--ink-2);
        font-size: 14px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: var(--transition);
    }

    .pdp-share-btn:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .share-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background: var(--white);
        border: 1.5px solid var(--line);
        border-radius: var(--radius-md);
        padding: 8px;
        box-shadow: var(--shadow-soft);
        min-width: 160px;
        z-index: 100;
    }

    .share-dropdown a,
    .share-dropdown button {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: var(--ink-2);
        font-size: 14px;
        font-weight: 500;
        padding: 9px 12px;
        border-radius: 8px;
        background: none;
        border: none;
        cursor: pointer;
        width: 100%;
        transition: var(--transition);
    }

    .share-dropdown a:hover,
    .share-dropdown button:hover {
        background: var(--brand-light);
        color: var(--brand);
    }

    /* ══════════════════════════
   RIGHT — PURCHASE BOX
══════════════════════════ */
    .pdp-purchase {
        position: sticky;
        top: 88px;
    }

    .purchase-card {
        border: 1.5px solid var(--brand-mid);
        border-radius: var(--radius-lg);
        padding: 24px;
        background: var(--white);
        box-shadow: var(--shadow-card);
    }

    .purchase-price {

        font-size: 18px;
        font-weight: 700;
        color: var(--brand);
        margin-bottom: 4px;
    }

    .purchase-tax {
        font-size: 13px;
        color: var(--ink-3);
        margin-bottom: 16px;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        font-size: 14px;
        padding: 6px 14px;
        border-radius: 30px;
        margin-bottom: 18px;
    }

    .stock-badge.in-stock {
        background: #f0fdf4;
        color: var(--success);
        border: 1.5px solid #bbf7d0;
    }

    .stock-badge.out-stock {
        background: #fef2f2;
        color: #dc2626;
        border: 1.5px solid #fecaca;
    }

    .stock-badge .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .stock-badge.in-stock .dot {
        background: var(--success);
    }

    .stock-badge.out-stock .dot {
        background: #dc2626;
    }

    .qty-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink-3);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .qty-control {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--line);
        border-radius: var(--radius-sm);
        overflow: hidden;
        width: fit-content;
        margin-bottom: 16px;
    }

    .qty-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: var(--brand-light);
        color: var(--brand);
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: var(--brand);
        color: #fff;
    }

    .qty-input {
        width: 52px;
        height: 40px;
        border: none;
        border-left: 1.5px solid var(--line);
        border-right: 1.5px solid var(--line);
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        outline: none;
        background: var(--white);
        color: var(--ink);
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
    }

    #stockMsgNew {
        font-size: 12px;
        color: #dc2626;
        font-weight: 500;
        margin-bottom: 12px;
        display: none;
    }

    .btn-cart {
        width: 100%;
        padding: 13px;
        background: var(--brand-light);
        color: var(--brand);
        border: 2px solid var(--brand);
        border-radius: var(--radius-sm);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cart:hover:not(:disabled) {
        background: var(--brand);
        color: #fff;
    }

    .btn-buynow {
        width: 100%;
        padding: 13px;
        background: var(--brand);
        color: #fff;
        border: 2px solid var(--brand);
        border-radius: var(--radius-sm);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-buynow:hover:not(:disabled) {
        background: var(--brand-dark);
        border-color: var(--brand-dark);
    }

    .btn-cart:disabled,
    .btn-buynow:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .seller-info {
        margin-top: 18px;
        border-top: 1.5px solid var(--line);
        padding-top: 14px;
    }

    .seller-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        padding: 4px 0;
        color: var(--ink-3);
    }

    .seller-row strong {
        color: var(--ink-2);
    }

    /* ══════════════════════════
   BOTTOM EXTRA SECTION
══════════════════════════ */
    .pdp-extra {
        margin-top: 40px;
    }

    .extra-card {
        border: 1.5px solid var(--brand-mid);
        border-radius: var(--radius-lg);
        background: var(--white);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .extra-card-head {
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        color: #fff;
        padding: 16px 24px;

        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .extra-card-body {
        padding: 24px;
    }

    /* Info table */
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table tr:not(:last-child) {
        border-bottom: 1px solid var(--line);
    }

    .info-table th {
        width: 200px;
        padding: 12px 16px;
        background: var(--brand-light);
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        text-align: left;
        border-right: 1px solid var(--brand-mid);
    }

    .info-table td {
        padding: 12px 16px;
        font-size: 14px;
        color: var(--ink-2);
    }

    /* ══════════════════════════
   COUPONS
══════════════════════════ */
    .coupon-card {
        border: 1.5px dashed var(--brand);
        background: var(--brand-light);
        border-radius: var(--radius-md);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .coupon-code {

        font-size: 16px;
        font-weight: 800;
        color: var(--brand);
        letter-spacing: 1px;
        background: var(--white);
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid var(--brand-mid);
    }

    .coupon-desc {
        font-size: 14px;
        color: var(--ink-2);
        flex: 1;
    }

    .coupon-off {
        background: var(--brand);
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 5px 12px;
        border-radius: 30px;
        white-space: nowrap;
    }

    /* ══════════════════════════
   REVIEWS
══════════════════════════ */
    .review-wrap {
        border: 1.5px solid var(--line);
        border-radius: var(--radius-lg);
        background: var(--white);
        overflow: hidden;
        margin-top: 28px;
    }

    .review-header-bar {
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        padding: 18px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .review-header-bar h3 {

        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }

    .btn-write-review-new {
        background: rgba(255, 255, 255, .2);
        color: #fff;
        border: 2px solid rgba(255, 255, 255, .5);
        padding: 9px 20px;
        border-radius: var(--radius-sm);

        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        backdrop-filter: blur(8px);
    }

    .btn-write-review-new:hover {
        background: var(--white);
        color: var(--brand);
        border-color: var(--white);
    }

    .review-body {
        padding: 28px;
    }

    .review-summary {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
        padding-bottom: 24px;
        border-bottom: 1.5px solid var(--line);
        margin-bottom: 24px;
    }

    .review-score-box {
        text-align: center;
    }

    .review-big-num {

        font-size: 32px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1;
    }

    .review-big-num span {
        color: var(--brand);
    }

    .review-stars-row {
        color: var(--brand);
        font-size: 20px;
        margin: 6px 0;
    }

    .review-total {
        font-size: 13px;
        color: var(--ink-3);
    }

    /* Bar chart */
    .bar-chart {
        flex: 1;
        min-width: 200px;
    }

    .bar-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .bar-label {
        width: 36px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink-3);
        text-align: right;
    }

    .bar-track {
        flex: 1;
        height: 8px;
        background: var(--line);
        border-radius: 99px;
        overflow: hidden;
    }

    .bar-fill-new {
        height: 100%;
        background: var(--brand);
        border-radius: 99px;
        transition: width 1s cubic-bezier(.4, 0, .2, 1);
    }

    .bar-count {
        width: 28px;
        font-size: 13px;
        color: var(--ink-3);
    }

    /* Filter pills */
    .review-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        align-items: center;
    }

    .filter-pill {
        padding: 7px 16px;
        border: 1.5px solid var(--line);
        border-radius: 30px;
        background: var(--white);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        color: var(--ink-2);
    }

    .filter-pill:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    .filter-pill.active {
        background: var(--brand);
        color: #fff;
        border-color: var(--brand);
    }

    /* Write review form */
    .review-form-box {
        display: none;
        background: var(--brand-light);
        border: 1.5px solid var(--brand-mid);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 28px;
    }

    .review-form-box label {
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .review-form-box select,
    .review-form-box textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid var(--brand-mid);
        border-radius: var(--radius-sm);
        font-size: 14px;
        background: var(--white);
        outline: none;
        color: var(--ink);
        transition: var(--transition);
    }

    .review-form-box select:focus,
    .review-form-box textarea:focus {
        border-color: var(--brand);
    }

    .btn-submit-review {
        background: var(--brand);
        color: #fff;
        border: none;
        padding: 11px 28px;
        border-radius: var(--radius-sm);

        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 14px;
    }

    .btn-submit-review:hover {
        background: var(--brand-dark);
    }

    /* Single review */
    .review-item {
        padding: 20px 0;
        border-top: 1.5px solid var(--line);
    }

    .review-item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .review-star-num {
        background: var(--brand);
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .review-item-title {

        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }

    .review-item-text {
        font-size: 14px;
        color: var(--ink-2);
        line-height: 1.7;
        margin-bottom: 10px;
    }

    .review-meta {
        font-size: 13px;
        color: var(--ink-3);
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .review-meta .sep {
        color: var(--line);
    }

    .review-action-btns {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .reaction-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border: 1.5px solid var(--line);
        border-radius: 30px;
        background: var(--white);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .reaction-btn:hover {
        border-color: var(--brand);
        color: var(--brand);
    }

    /* ══════════════════════════
   RESPONSIVE
══════════════════════════ */
    @media (max-width: 1100px) {
        .pdp-grid {
            grid-template-columns: 360px 1fr 280px;
            gap: 20px;
        }

        #zoomPreviewNew {
            display: none !important;
        }
    }

    @media (max-width: 991px) {
        .pdp-grid {
            grid-template-columns: 1fr 1fr;
        }

        .pdp-purchase {
            grid-column: 1 / -1;
            position: static;
        }

        .pdp-gallery {
            position: static;
        }

        #zoomPreviewNew {
            display: none !important;
        }

        .purchase-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .purchase-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767px) {
        .pdp-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .pdp-container {
            padding: 16px;
        }

        .gallery-wrap {
            flex-direction: column-reverse;
        }

        .thumb-col {
            flex-direction: row;
            gap: 8px;
            flex: auto;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .thumb-item {
            flex: 0 0 60px;
            height: 64px;
        }

        .main-img-box {
            height: 300px;
        }

        .pdp-title {
            font-size: 18px;
        }

        .pdp-current-price {
            font-size: 20px;
        }

        .purchase-card {
            display: block;
        }

        .info-table th {
            width: 130px;
        }

        .review-summary {
            flex-direction: column;
            gap: 20px;
        }

        .review-score-box {
            text-align: left;
        }

        .btn-write-review-new {
            width: 100%;
            text-align: center;
        }

        .extra-card-body {
            padding: 16px;
        }

        .review-body {
            padding: 18px;
        }
    }

    @media (max-width: 480px) {
        .main-img-box {
            height: 260px;
        }

        .review-big-num {
            font-size: 30px;
        }

        .purchase-price {
            font-size: 20px;
        }

        .pdp-title {
            font-size: 17px;
        }
    }
</style>

<!-- ============================================================
     HTML
============================================================ -->

<div id="zoomPreviewNew"></div>

<!-- Breadcrumb -->
<div class="pdp-breadcrumb">
    <div class="inner">
        <a href="index.php">Home</a>
        <span>›</span>
        <a href="products.php"><?= htmlspecialchars($category); ?></a>
        <span>›</span>
        <span><?= htmlspecialchars(mb_strimwidth($productTitle, 0, 40, '…')); ?></span>
    </div>
</div>

<div class="pdp-wrap">
    <div class="pdp-container">

        <!-- ── MAIN GRID ── -->
        <div class="pdp-grid">

            <!-- ── LEFT: GALLERY ── -->
            <div class="pdp-gallery">
                <div class="gallery-wrap">

                    <!-- Thumbnails -->
                    <div class="thumb-col" id="thumbCol">
                        <?php foreach ($images as $i => $img): ?>
                            <div class="thumb-item <?= $i === 0 ? 'active' : ''; ?>"
                                data-index="<?= $i; ?>"
                                onclick="switchImage(<?= $i; ?>)">
                                <img src="<?= htmlspecialchars($img); ?>" alt="thumb">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Main Image -->
                    <div class="main-img-box" id="mainImgBox">
                        <?php if ($productDiscount > 0): ?>
                            <div class="img-discount-badge"><?= round((int)$productDiscount); ?>% OFF</div>
                        <?php endif; ?>
                        <img
                            id="mainProductImg"
                            src="<?= htmlspecialchars($images[0]); ?>"
                            alt="<?= htmlspecialchars($productTitle); ?>">
                    </div>

                </div>
            </div>

            <!-- ── MIDDLE: DETAILS ── -->
            <div class="pdp-details">

                <div class="pdp-store-tag">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <?= htmlspecialchars($productStore); ?>
                </div>

                <h1 class="pdp-title"><?= htmlspecialchars($productTitle); ?></h1>

                <!-- Rating -->
                <div class="pdp-rating-row">
                    <span class="rating-pill"><?= htmlspecialchars($productRating); ?> ★</span>
                    <span class="rating-text"><?= htmlspecialchars($productRatingsCount); ?> ratings</span>
                    <?php if ($productBought !== 'Recently bought'): ?>
                        <span class="bought-badge">✔ <?= htmlspecialchars($productBought); ?></span>
                    <?php endif; ?>
                </div>

                <hr class="pdp-divider">

                <!-- Price -->
                <div class="pdp-price-block">
                    <div class="price-top-row">
                        <?php if ($productDiscount > 0): ?>
                            <span class="pdp-discount-tag"><?= round((int)$productDiscount); ?>% off</span>
                        <?php endif; ?>
                        <div class="pdp-current-price">₹<span id="pdpPrice"><?= format_price($productPrice); ?></span></div>
                    </div>
                    <?php if ($productOldPrice > 0): ?>
                        <div class="pdp-old-price">M.R.P: <del>₹<?= format_price($productOldPrice); ?></del></div>
                    <?php endif; ?>
                    <div class="pdp-tax-note">✓ Inclusive of all taxes</div>
                </div>

                <!-- Highlights -->
                <div class="pdp-card">
                    <div class="pdp-card-title">Product Highlights</div>
                    <ul>
                        <li>Compact and easy-to-use module for electronics projects.</li>
                        <li>Compatible with Arduino, Raspberry Pi, ESP32 and microcontrollers.</li>
                        <li>Ideal for lab practicals, robotics, automation and IoT applications.</li>
                        <li>Quality checked before dispatch for reliability.</li>
                    </ul>
                </div>

                <!-- Description -->
                <div class="pdp-card">
                    <div class="pdp-card-title">Description</div>
                    <p><?= nl2br(htmlspecialchars($description)); ?></p>
                </div>

                <!-- Usage -->
                <div class="pdp-card">
                    <div class="pdp-card-title">Usage Guidelines</div>
                    <ul>
                        <li>Check operating voltage before connecting with any board.</li>
                        <li>Use proper jumper wires and avoid loose connections.</li>
                        <li>Verify pin mapping before uploading code.</li>
                        <li>Keep away from moisture and direct short circuits.</li>
                    </ul>
                </div>

                <!-- Share -->
                <div class="pdp-share-wrap">
                    <button class="pdp-share-btn" id="shareBtn">
                        <i class="fa-solid fa-share-nodes"></i> Share
                    </button>
                    <div class="share-dropdown" id="shareDropdown">
                        <a href="mailto:?subject=Check this out&body=<?= urlencode('http://localhost/Sip_innovation/product.php?id=' . $product_id); ?>">
                            <i class="fa-solid fa-envelope"></i> Email
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://localhost/Sip_innovation/product.php?id=' . $product_id); ?>" target="_blank">
                            <i class="fa-brands fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://localhost/Sip_innovation/product.php?id=' . $product_id); ?>" target="_blank">
                            <i class="fa-brands fa-x-twitter"></i> X / Twitter
                        </a>
                        <button onclick="copyProductLink(event)">
                            <i class="fa-solid fa-link"></i> Copy Link
                        </button>
                    </div>
                </div>

            </div>

            <!-- ── RIGHT: PURCHASE ── -->
            <div class="pdp-purchase">
                <div class="purchase-card">

                    <div class="purchase-price">₹<span id="sidePrice"><?= format_price($productPrice); ?></span></div>
                    <div class="purchase-tax">Inclusive of all taxes</div>

                    <div class="stock-badge <?= $productStock > 0 ? 'in-stock' : 'out-stock'; ?>">
                        <span class="dot"></span>
                        <?= $productStock > 0 ? 'In Stock' : 'Out of Stock'; ?>
                    </div>

                    <div class="purchase-actions">
                        <div class="qty-label">Quantity</div>
                        <div class="qty-control">
                            <button class="qty-btn" id="qtyMinus">−</button>
                            <input class="qty-input" type="number" id="qtyNew" value="1" min="1" max="<?= $productStock; ?>">
                            <button class="qty-btn" id="qtyPlus">+</button>
                        </div>
                        <div id="stockMsgNew"></div>

                        <button class="btn-cart add-to-cart"
                            data-id="<?= $product_id; ?>"
                            <?= $productStock <= 0 ? 'disabled' : ''; ?>>
                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                        </button>

                        <button class="btn-buynow buy-now"
                            data-id="<?= $product_id; ?>"
                            <?= $productStock <= 0 ? 'disabled' : ''; ?>>
                            <i class="fa-solid fa-bolt"></i> Buy Now
                        </button>
                    </div>

                    <div class="seller-info">
                        <div class="seller-row">
                            <span>Ships from</span>
                            <strong><?= htmlspecialchars($productSeller); ?></strong>
                        </div>
                        <div class="seller-row">
                            <span>Sold by</span>
                            <strong><?= htmlspecialchars($productStore); ?></strong>
                        </div>
                        <div class="seller-row">
                            <span>Support</span>
                            <strong>Project & Component Help</strong>
                        </div>
                    </div>

                </div>
            </div>

        </div><!-- /pdp-grid -->


        <!-- ══════════════════════════
         EXTRA INFO SECTION
    ══════════════════════════ -->
        <div class="pdp-extra" id="pdpExtraSection">

            <!-- Product Info Table -->
            <div class="extra-card">
                <div class="extra-card-head">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    Product Information
                </div>
                <div class="extra-card-body">
                    <div class="table-responsive">
                        <table class="info-table">
                            <tbody>
                                <tr>
                                    <th>Product Name</th>
                                    <td><?= htmlspecialchars($productTitle); ?></td>
                                </tr>
                                <tr>
                                    <th>Brand / Store</th>
                                    <td><?= htmlspecialchars($brand); ?></td>
                                </tr>
                                <tr>
                                    <th>Model Type</th>
                                    <td><?= htmlspecialchars($model); ?></td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td><?= htmlspecialchars($category); ?></td>
                                </tr>
                                <tr>
                                    <th>Application</th>
                                    <td>Electronics projects, IoT, automation, robotics and educational practicals.</td>
                                </tr>
                                <tr>
                                    <th>Package Includes</th>
                                    <td>1 × <?= htmlspecialchars($productTitle); ?></td>
                                </tr>
                                <tr>
                                    <th>Availability</th>
                                    <td><?= $productStock > 0 ? '<span style="color:var(--success);font-weight:700">✓ Available</span>' : '<span style="color:#dc2626;font-weight:700">✗ Unavailable</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Recommended For</th>
                                    <td>Students, hobbyists, embedded developers, project makers and electronics labs.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="extra-card">
                <div class="extra-card-head">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="3" width="15" height="13" />
                        <path d="M16 8h4l3 3v5h-7V8z" />
                        <circle cx="5.5" cy="18.5" r="2.5" />
                        <circle cx="18.5" cy="18.5" r="2.5" />
                    </svg>
                    Shipping & Support
                </div>
                <div class="extra-card-body">
                    <p style="margin:0 0 8px;font-size:14px;color:var(--ink-2)">Orders are packed carefully and shipped from <strong><?= htmlspecialchars($productSeller); ?></strong>.</p>
                    <p style="margin:0 0 8px;font-size:14px;color:var(--ink-2)">Verify input voltage, pin configuration and compatibility before connection.</p>
                    <p style="margin:0;font-size:14px;color:var(--ink-2)">For bulk or institute orders, contact the store support team.</p>
                </div>
            </div>

            <!-- Important Note -->
            <div class="extra-card">
                <div class="extra-card-head" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    Important Note
                </div>
                <div class="extra-card-body">
                    <p style="margin:0;font-size:14px;color:var(--ink-2)">Product image may slightly vary depending on available stock batch. Functionality and basic module use remain the same. Always follow standard electronics safety practices while connecting sensors/modules with development boards.</p>
                </div>
            </div>

        </div><!-- /pdp-extra -->


        <!-- ══════════════════════════
         COUPONS
    ══════════════════════════ -->
        <?php
        $couponList = mysqli_query($conn, "SELECT * FROM coupons WHERE status=1 AND (product_id IS NULL OR product_id=0 OR product_id=$product_id) ORDER BY id DESC LIMIT 5");
        if ($couponList && mysqli_num_rows($couponList)):
        ?>
            <div class="extra-card" style="margin-top:20px">
                <div class="extra-card-head" style="background:linear-gradient(135deg,#16a34a,#15803d)">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                    Available Coupons
                </div>
                <div class="extra-card-body" style="display:flex;flex-direction:column;gap:12px">
                    <?php while ($cp = mysqli_fetch_assoc($couponList)):
                        $discVal = !empty($cp['discount_value']) ? $cp['discount_value'] . (($cp['discount_type'] ?? '') === 'percent' ? '%' : '₹') . ' off' : '';
                    ?>
                        <div class="coupon-card">
                            <span class="coupon-code"><?= htmlspecialchars($cp['code']); ?></span>
                            <span class="coupon-desc"><?= htmlspecialchars($cp['title'] ?? 'Special Offer'); ?></span>
                            <?php if ($discVal): ?><span class="coupon-off"><?= htmlspecialchars($discVal); ?></span><?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>


        <!-- ══════════════════════════
         REVIEWS
    ══════════════════════════ -->
        <?php
        $ratingData = mysqli_query($conn, "
        SELECT COUNT(*) total, AVG(rating) avg_rating,
               SUM(CASE WHEN rating=5 THEN 1 ELSE 0 END) five_star,
               SUM(CASE WHEN rating=4 THEN 1 ELSE 0 END) four_star,
               SUM(CASE WHEN rating=3 THEN 1 ELSE 0 END) three_star,
               SUM(CASE WHEN rating=2 THEN 1 ELSE 0 END) two_star,
               SUM(CASE WHEN rating=1 THEN 1 ELSE 0 END) one_star
        FROM product_reviews
        WHERE product_id=$product_id AND status=1
    ");
        $ri = mysqli_fetch_assoc($ratingData);
        $totalReviews = (int)($ri['total'] ?? 0);
        $avgRating    = round((float)($ri['avg_rating'] ?? 0), 1);
        $full = floor($avgRating);

        function percent($v, $t)
        {
            return $t > 0 ? round(($v / $t) * 100) : 0;
        }

        $reviewList = mysqli_query($conn, "
        SELECT * FROM product_reviews
        WHERE product_id=$product_id AND status=1 ORDER BY id DESC
    ");
        ?>

        <div class="review-wrap">

            <div class="review-header-bar">
                <h3>Customer Reviews</h3>
                <button class="btn-write-review-new" onclick="toggleReviewForm()">✍ Write a Review</button>
            </div>

            <div class="review-body">

                <!-- Summary -->
                <div class="review-summary">
                    <div class="review-score-box">
                        <div class="review-big-num"><?= $avgRating; ?><span>★</span></div>
                        <div class="review-stars-row">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= $full ? '★' : '☆'; ?>
                        </div>
                        <div class="review-total"><?= number_format($totalReviews); ?> ratings &amp; reviews</div>
                    </div>

                    <div class="bar-chart">
                        <?php
                        $stars = [5 => $ri['five_star'], 4 => $ri['four_star'], 3 => $ri['three_star'], 2 => $ri['two_star'], 1 => $ri['one_star']];
                        foreach ($stars as $s => $c):
                        ?>
                            <div class="bar-row">
                                <div class="bar-label"><?= $s; ?>★</div>
                                <div class="bar-track">
                                    <div class="bar-fill-new" style="width:<?= percent($c, $totalReviews); ?>%"></div>
                                </div>
                                <div class="bar-count"><?= $c; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Filters -->
                <div class="review-filters">
                    <button class="filter-pill active" data-type="helpful">Most Helpful</button>
                    <button class="filter-pill" data-type="latest">Latest</button>
                    <button class="filter-pill" data-type="positive">Positive</button>
                    <button class="filter-pill" data-type="negative">Negative</button>
                </div>

                <!-- Write Review Form -->
                <div class="review-form-box" id="reviewFormBoxNew">
                    <div style="display:grid;grid-template-columns:160px 1fr;gap:16px;flex-wrap:wrap">
                        <div>
                            <label>Your Rating</label>
                            <select id="reviewRating">
                                <option value="">Select</option>
                                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                <option value="4">⭐⭐⭐⭐ (4)</option>
                                <option value="3">⭐⭐⭐ (3)</option>
                                <option value="2">⭐⭐ (2)</option>
                                <option value="1">⭐ (1)</option>
                            </select>
                        </div>
                        <div>
                            <label>Your Review</label>
                            <textarea id="reviewText" rows="2" placeholder="Share your experience with this product..."></textarea>
                        </div>
                    </div>
                    <button class="btn-submit-review" id="submitReviewBtn">Submit Review</button>
                    <div id="reviewMsg" style="margin-top:10px;font-size:14px;font-weight:600"></div>
                </div>

                <!-- Review List -->
                <div class="review-list-area" id="reviewListArea">
                    <?php if ($reviewList && mysqli_num_rows($reviewList)): ?>
                        <?php while ($rv = mysqli_fetch_assoc($reviewList)):
                            $rid = (int)$rv['id'];
                            $likes    = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM review_reactions WHERE review_id='$rid' AND reaction='like'"));
                            $dislikes = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM review_reactions WHERE review_id='$rid' AND reaction='dislike'"));
                        ?>
                            <div class="review-item"
                                data-rating="<?= (int)$rv['rating']; ?>"
                                data-time="<?= strtotime($rv['created_at']); ?>"
                                data-helpful="<?= $likes; ?>">

                                <div class="review-item-top">
                                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                        <span class="review-star-num"><?= $rv['rating']; ?> ★</span>
                                        <span class="review-item-title"><?= htmlspecialchars($rv['title'] ?? 'Nice Product'); ?></span>
                                    </div>
                                </div>

                                <div class="review-item-text"><?= nl2br(htmlspecialchars($rv['review'])); ?></div>

                                <div class="review-meta">
                                    <span><?= htmlspecialchars($rv['customer_name']); ?></span>
                                    <span class="sep">|</span>
                                    <span>Verified Purchase</span>
                                    <span class="sep">|</span>
                                    <span><?= date('M Y', strtotime($rv['created_at'])); ?></span>
                                </div>

                                <div class="review-action-btns">
                                    <button class="reaction-btn like-review-btn" data-id="<?= $rid; ?>">
                                        👍 Helpful (<span class="like-count"><?= $likes; ?></span>)
                                    </button>
                                    <button class="reaction-btn dislike-review-btn" data-id="<?= $rid; ?>">
                                        👎 (<span class="dislike-count"><?= $dislikes; ?></span>)
                                    </button>
                                </div>

                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:var(--ink-3);padding:20px 0;font-size:14px">No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div><!-- /pdp-container -->
</div><!-- /pdp-wrap -->


<!-- ============================================================
     SCRIPTS
============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    /* ── Image Gallery ── */
    const allImages = <?= json_encode($images); ?>;
    let currentIndex = 0;

    function switchImage(idx) {
        currentIndex = idx;
        const mainImg = document.getElementById('mainProductImg');
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = allImages[idx];
            mainImg.style.opacity = '1';
        }, 150);
        document.querySelectorAll('.thumb-item').forEach((t, i) => {
            t.classList.toggle('active', i === idx);
        });
    }

    /* ── Zoom ── */
    const mainBox = document.getElementById('mainImgBox');
    const zoomBox = document.getElementById('zoomPreviewNew');
    const extraSec = document.getElementById('pdpExtraSection');

    function canZoom() {
        if (window.innerWidth <= 1100) return false;
        if (!extraSec) return false;
        return extraSec.getBoundingClientRect().top > window.innerHeight - 120;
    }

    if (mainBox && zoomBox) {
        mainBox.addEventListener('mouseenter', () => {
            if (!canZoom()) return;
            zoomBox.style.backgroundImage = `url('${allImages[currentIndex]}')`;
            zoomBox.style.display = 'block';
            positionZoom();
        });
        mainBox.addEventListener('mousemove', (e) => {
            if (!canZoom()) {
                zoomBox.style.display = 'none';
                return;
            }
            const r = mainBox.getBoundingClientRect();
            const x = ((e.clientX - r.left) / r.width) * 100;
            const y = ((e.clientY - r.top) / r.height) * 100;
            zoomBox.style.backgroundPosition = `${x}% ${y}%`;
            positionZoom();
        });
        mainBox.addEventListener('mouseleave', () => {
            zoomBox.style.display = 'none';
        });
        window.addEventListener('scroll', () => {
            if (!canZoom()) zoomBox.style.display = 'none';
        });
        window.addEventListener('resize', () => {
            zoomBox.style.display = 'none';
        });
    }

    function positionZoom() {
        const r = mainBox.getBoundingClientRect();
        let l = r.right + 20;
        if (l + 460 > window.innerWidth - 10) l = r.left - 480;
        let t = r.top;
        if (t + 400 > window.innerHeight - 10) t = window.innerHeight - 420;
        if (t < 80) t = 80;
        zoomBox.style.left = l + 'px';
        zoomBox.style.top = t + 'px';
    }

    /* ── Qty + Price ── */
    const productBasePrice = <?= $productPrice; ?>;
    const maxStock = <?= $productStock; ?>;
    const qtyInput = document.getElementById('qtyNew');
    const stockMsg = document.getElementById('stockMsgNew');

    function updatePrice() {
        let qty = parseInt(qtyInput.value) || 1;
        if (qty < 1) {
            qty = 1;
            qtyInput.value = 1;
        }
        if (maxStock > 0 && qty > maxStock) {
            qty = maxStock;
            qtyInput.value = maxStock;
            stockMsg.textContent = `Only ${maxStock} left in stock`;
            stockMsg.style.display = 'block';
        } else {
            stockMsg.style.display = 'none';
        }
        const total = productBasePrice * qty;
        document.getElementById('sidePrice').textContent = total.toLocaleString('en-IN');
        document.getElementById('pdpPrice').textContent = total.toLocaleString('en-IN');
    }

    document.getElementById('qtyPlus').addEventListener('click', () => {
        let v = parseInt(qtyInput.value) || 1;
        if (!maxStock) return;
        qtyInput.value = Math.min(v + 1, maxStock);
        updatePrice();
    });
    document.getElementById('qtyMinus').addEventListener('click', () => {
        let v = parseInt(qtyInput.value) || 1;
        qtyInput.value = Math.max(v - 1, 1);
        updatePrice();
    });
    qtyInput.addEventListener('input', updatePrice);

    /* ── Share ── */
    const shareBtn = document.getElementById('shareBtn');
    const shareDd = document.getElementById('shareDropdown');
    shareBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        shareDd.style.display = shareDd.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', () => {
        shareDd.style.display = 'none';
    });

    function copyProductLink(e) {
        e.preventDefault();
        const link = "<?= 'http://localhost/Sip_innovation/product.php?id=' . $product_id; ?>";
        navigator.clipboard.writeText(link).then(() => {
            shareBtn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            setTimeout(() => {
                shareBtn.innerHTML = '<i class="fa-solid fa-share-nodes"></i> Share';
            }, 2000);
        });
    }

    /* ── Cart & Buy ── */
    document.querySelector('.add-to-cart')?.addEventListener('click', function() {
        const pid = this.dataset.id;
        const qty = document.getElementById('qtyNew').value;
        $.post('AJAX/add_to_cart.php', {
            product_id: pid,
            qty
        }, function(res) {
            if (res.trim() === 'login_required') {
                openAuthModal();
                return;
            }
            if (res.trim() === 'success') {
                updateHeaderCounts();
                ecomToast('👜 Added to cart!');
            }
        });
    });
    document.querySelector('.buy-now')?.addEventListener('click', function() {
        const pid = this.dataset.id;
        const qty = document.getElementById('qtyNew').value;
        window.location.href = `checkout.php?product_id=${pid}&qty=${qty}`;
    });

    /* ── Review Form Toggle ── */
    function toggleReviewForm() {
        const box = document.getElementById('reviewFormBoxNew');
        if (box.style.display === 'block') {
            box.style.display = 'none';
        } else {
            box.style.display = 'block';
            box.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    }

    /* ── Submit Review ── */
    document.getElementById('submitReviewBtn')?.addEventListener('click', function() {
        const rating = document.getElementById('reviewRating').value;
        const review = document.getElementById('reviewText').value;
        const msg = document.getElementById('reviewMsg');
        msg.innerHTML = '';
        $.ajax({
            url: 'submit_review.php',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: <?= $product_id; ?>,
                rating,
                review
            },
            success(res) {
                if (res.status === 'success') {
                    msg.innerHTML = `<span style="color:var(--success)">${res.message}</span>`;
                    setTimeout(() => location.reload(), 800);
                } else {
                    msg.innerHTML = `<span style="color:#dc2626">${res.message}</span>`;
                }
            },
            error() {
                msg.innerHTML = '<span style="color:#dc2626">Something went wrong.</span>';
            }
        });
    });

    /* ── Review Filters ── */
    $(document).on('click', '.filter-pill', function() {
        $('.filter-pill').removeClass('active');
        $(this).addClass('active');
        const type = $(this).data('type');
        const area = document.getElementById('reviewListArea');
        const items = [...document.querySelectorAll('.review-item')];

        items.forEach(i => i.style.display = 'none');

        if (type === 'helpful') {
            items.sort((a, b) => $(b).data('helpful') - $(a).data('helpful'))
                .forEach(i => {
                    i.style.display = 'block';
                    area.appendChild(i);
                });
        } else if (type === 'latest') {
            items.sort((a, b) => $(b).data('time') - $(a).data('time'))
                .forEach(i => {
                    i.style.display = 'block';
                    area.appendChild(i);
                });
        } else if (type === 'positive') {
            items.forEach(i => {
                if ($(i).data('rating') >= 4) i.style.display = 'block';
            });
        } else if (type === 'negative') {
            items.forEach(i => {
                if ($(i).data('rating') <= 2) i.style.display = 'block';
            });
        }
    });

    /* ── Reactions ── */
    $(document).on('click', '.like-review-btn', function() {
        const btn = $(this);
        $.ajax({
            url: 'review_reaction.php',
            type: 'POST',
            dataType: 'json',
            data: {
                review_id: btn.data('id'),
                type: 'like'
            },
            success(res) {
                if (res.status === 'success') btn.find('.like-count').text(res.likes);
            }
        });
    });
    $(document).on('click', '.dislike-review-btn', function() {
        const btn = $(this);
        $.ajax({
            url: 'review_reaction.php',
            type: 'POST',
            dataType: 'json',
            data: {
                review_id: btn.data('id'),
                type: 'dislike'
            },
            success(res) {
                if (res.status === 'success') btn.find('.dislike-count').text(res.dislikes);
            }
        });
    });
</script>

<?php include 'recently_viewed.php'; ?>

<?php include 'footer.php'; ?>