<?php
include 'config/db.php';

$id = (int)($_GET['id'] ?? 0);

$project = mysqli_query($conn, "SELECT * FROM projects WHERE id='$id' AND status=1 LIMIT 1");

if (!$project || mysqli_num_rows($project) == 0) {
  include 'header.php';
  echo '<section class="container my-5"><div class="alert alert-danger">Project not found.</div></section>';
  include 'footer.php';
  exit;
}

$p = mysqli_fetch_assoc($project);

$image = (!empty($p['image']) && file_exists('uploads/projects/' . $p['image']))
  ? 'uploads/projects/' . $p['image']
  : 'assets/images/default-project.webp';

$title           = htmlspecialchars($p['title'] ?? 'Project Details');
$type            = strtolower($p['project_type'] ?? 'mini') === 'major' ? 'Major Project' : 'Mini Project';
$description     = $p['description'] ?? '';
$projectPrice    = (float)($p['price'] ?? 0);
$projectOldPrice = (float)($p['old_price'] ?? 0);
$projectDiscount = (float)($p['discount'] ?? 0);
$projectStock    = 100;

$images = [];
for ($i = 0; $i < 5; $i++) $images[] = $image;

include 'header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<style>
  /* ── Tokens ── */
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
    --r-sm: 8px;
    --r-md: 14px;
    --r-lg: 22px;
    --shadow: 0 4px 24px rgba(0, 0, 0, .07);
    --shadow-card: 0 8px 40px rgba(239, 102, 47, .10);
    --ease: .25s cubic-bezier(.4, 0, .2, 1);
  }

  /* ── Breadcrumb ── */
  .pdp-breadcrumb {
    background: linear-gradient(135deg, #fff4ef 0%, #fff9f7 100%);
    border-bottom: 1px solid var(--brand-mid);
    padding: 13px 0;
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

  /* ── Wrapper ── */
  .proj-wrap * {
    box-sizing: border-box;
  }

  .proj-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 28px 20px;
  }

  /* ── Grid ── */
  .proj-grid {
    display: grid;
    grid-template-columns: 400px 1fr 290px;
    gap: 28px;
    align-items: start;
  }

  /* ══════════════
   GALLERY
══════════════ */
  .proj-gallery {
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
    flex: 0 0 62px;
  }

  .thumb-item {
    width: 62px;
    height: 68px;
    border: 2px solid var(--line);
    border-radius: var(--r-sm);
    overflow: hidden;
    cursor: pointer;
    background: var(--white);
    padding: 3px;
    transition: var(--ease);
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
    height: 390px;
    border: 1.5px solid var(--line);
    border-radius: var(--r-md);
    overflow: hidden;
    background: var(--white);
    position: relative;
    cursor: crosshair;
  }

  .main-img-box img {
    width: 100%;
    height: 100%;
    object-fit: fill;
    transition: opacity .3s ease;
  }

  .proj-type-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: var(--brand);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .7px;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 30px;
    z-index: 2;
  }

  /* Zoom */
  #projZoomBox {
    position: fixed;
    width: 450px;
    height: 390px;
    border: 1.5px solid var(--line);
    border-radius: var(--r-md);
    background-repeat: no-repeat;
    background-size: 250%;
    background-color: var(--white);
    display: none;
    z-index: 99999;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .16);
    pointer-events: none;
  }

  /* ══════════════
   DETAILS
══════════════ */
  .proj-details {
    padding: 0;
  }

  .proj-store-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 30px;
    margin-bottom: 12px;
    border: 1px solid var(--brand-mid);
  }

  .proj-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
    margin-bottom: 14px;
  }

  .proj-divider {
    border: none;
    border-top: 1.5px solid var(--line);
    margin: 14px 0;
  }

  /* Price */
  .proj-price-block {
    margin-bottom: 20px;
  }

  .price-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 5px;
  }

  .disc-tag {
    background: var(--brand);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
  }

  .proj-price {
    font-size: 22px;
    font-weight: 800;
    color: var(--brand);
    line-height: 1;
  }

  .proj-old-price {
    font-size: 13px;
    color: var(--ink-3);
    margin-bottom: 4px;
  }

  .proj-tax {
    font-size: 12px;
    color: var(--success);
    font-weight: 500;
  }

  /* Info cards */
  .proj-card {
    border: 1.5px solid var(--brand-mid);
    background: var(--brand-light);
    border-radius: var(--r-md);
    padding: 16px 18px;
    margin-bottom: 14px;
  }

  .proj-card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--brand);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .proj-card-title::before {
    content: '';
    width: 3px;
    height: 15px;
    background: var(--brand);
    border-radius: 4px;
    display: inline-block;
    flex-shrink: 0;
  }

  .proj-card ul {
    padding-left: 0;
    margin: 0;
    list-style: none;
  }

  .proj-card ul li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
    color: var(--ink-2);
    padding: 5px 0;
    line-height: 1.6;
    border-bottom: 1px solid var(--brand-mid);
  }

  .proj-card ul li:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .proj-card ul li i {
    color: var(--brand);
    margin-top: 3px;
    flex-shrink: 0;
  }

  .proj-card p {
    font-size: 13px;
    color: var(--ink-2);
    line-height: 1.7;
    margin: 0;
  }

  /* ══════════════
   PURCHASE BOX
══════════════ */
  .proj-purchase {
    position: sticky;
    top: 88px;
  }

  .purchase-card {
    border: 1.5px solid var(--brand-mid);
    border-radius: var(--r-lg);
    padding: 22px;
    background: var(--white);
    box-shadow: var(--shadow-card);
  }

  .purchase-price {
    font-size: 22px;
    font-weight: 800;
    color: var(--brand);
    margin-bottom: 3px;
  }

  .purchase-tax {
    font-size: 12px;
    color: var(--ink-3);
    margin-bottom: 14px;
  }

  .stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    font-size: 13px;
    padding: 5px 13px;
    border-radius: 30px;
    margin-bottom: 16px;
  }

  .stock-badge.in {
    background: #f0fdf4;
    color: var(--success);
    border: 1.5px solid #bbf7d0;
  }

  .stock-badge.out {
    background: #fef2f2;
    color: #dc2626;
    border: 1.5px solid #fecaca;
  }

  .stock-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
  }

  .stock-badge.in .stock-dot {
    background: var(--success);
  }

  .stock-badge.out .stock-dot {
    background: #dc2626;
  }

  .qty-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--ink-3);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 7px;
  }

  .qty-wrap {
    display: flex;
    align-items: center;
    border: 1.5px solid var(--line);
    border-radius: var(--r-sm);
    overflow: hidden;
    width: fit-content;
    margin-bottom: 14px;
  }

  .qty-btn {
    width: 38px;
    height: 38px;
    border: none;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 17px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--ease);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .qty-btn:hover {
    background: var(--brand);
    color: #fff;
  }

  .qty-num {
    width: 48px;
    height: 38px;
    border: none;
    border-left: 1.5px solid var(--line);
    border-right: 1.5px solid var(--line);
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    outline: none;
    background: var(--white);
    color: var(--ink);
  }

  .qty-num::-webkit-outer-spin-button,
  .qty-num::-webkit-inner-spin-button {
    -webkit-appearance: none;
  }

  #projStockMsg {
    font-size: 12px;
    color: #dc2626;
    font-weight: 500;
    margin-bottom: 10px;
    display: none;
  }

  .btn-proj-cart {
    width: 100%;
    padding: 12px;
    background: var(--brand-light);
    color: var(--brand);
    border: 2px solid var(--brand);
    border-radius: var(--r-sm);
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--ease);
    margin-bottom: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-proj-cart:hover:not(:disabled) {
    background: var(--brand);
    color: #fff;
  }

  .btn-proj-buy {
    width: 100%;
    padding: 12px;
    background: var(--brand);
    color: #fff;
    border: 2px solid var(--brand);
    border-radius: var(--r-sm);
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--ease);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-proj-buy:hover:not(:disabled) {
    background: var(--brand-dark);
    border-color: var(--brand-dark);
  }

  .btn-proj-cart:disabled,
  .btn-proj-buy:disabled {
    opacity: .45;
    cursor: not-allowed;
  }

  .seller-info {
    margin-top: 16px;
    border-top: 1.5px solid var(--line);
    padding-top: 13px;
  }

  .seller-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    padding: 4px 0;
    color: var(--ink-3);
  }

  .seller-row strong {
    color: var(--ink-2);
    font-size: 13px;
  }

  /* ══════════════
   BOTTOM CARDS
══════════════ */
  .proj-extra {
    margin-top: 36px;
  }

  .extra-card {
    border: 1.5px solid var(--brand-mid);
    border-radius: var(--r-lg);
    background: var(--white);
    overflow: hidden;
    margin-bottom: 18px;
  }

  .extra-head {
    background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
    color: #fff;
    padding: 14px 22px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .extra-body {
    padding: 22px;
  }

  /* What you get grid */
  .get-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 2fr));
    gap: 12px;
  }

  .get-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    background: var(--brand-light);
    border: 1.5px solid var(--brand-mid);
    border-radius: var(--r-sm);
    padding: 10px 14px;
  }

  .get-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    background: var(--brand);
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  .get-text {
    font-size: 13px;
    color: var(--ink-2);
    line-height: 1.5;
    font-weight: 500;
  }

  /* Description */
  .proj-desc {
    font-size: 13px;
    color: var(--ink-2);
    line-height: 1.8;
    text-align: justify;
  }

  /* ══════════════
   RESPONSIVE
══════════════ */
  @media(max-width: 1100px) {
    .proj-grid {
      grid-template-columns: 340px 1fr 270px;
      gap: 20px;
    }

    #projZoomBox {
      display: none !important;
    }
  }

  @media(max-width: 991px) {
    .proj-grid {
      grid-template-columns: 1fr 1fr;
    }

    .proj-purchase {
      grid-column: 1 / -1;
      position: static;
    }

    .proj-gallery {
      position: static;
    }

    #projZoomBox {
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

  @media(max-width: 767px) {
    .proj-grid {
      grid-template-columns: 1fr;
      gap: 18px;
    }

    .proj-container {
      padding: 14px 14px;
    }

    .gallery-wrap {
      flex-direction: column-reverse;
    }

    .thumb-col {
      flex-direction: row;
      overflow-x: auto;
      flex: auto;
      gap: 8px;
      padding-bottom: 3px;
    }

    .thumb-item {
      flex: 0 0 58px;
      height: 62px;
    }

    .main-img-box {
      height: 280px;
    }

    .proj-title {
      font-size: 16px;
    }

    .proj-price {
      font-size: 18px;
    }

    .purchase-card {
      display: block;
    }

    .get-grid {
      grid-template-columns: 1fr 1fr;
    }

    .extra-body {
      padding: 14px;
    }
  }

  @media(max-width: 480px) {
    .main-img-box {
      height: 240px;
    }

    .get-grid {
      grid-template-columns: 1fr;
    }

    .purchase-price {
      font-size: 20px;
    }

    .proj-title {
      font-size: 15px;
    }
  }
</style>

<!-- Breadcrumb -->
<div class="pdp-breadcrumb">
  <div class="inner">
    <a href="index.php">Home</a>
    <span>›</span>
    <?php
    $projectPage = strtolower($p['project_type'] ?? 'mini') === 'major'
      ? 'major-projects.php'
      : 'mini-projects.php';
    ?>

    <a href="<?= $projectPage; ?>">
      Projects
    </a>
    <span>›</span>
    <span><?= htmlspecialchars(mb_strimwidth($p['title'] ?? '', 0, 45, '…')); ?></span>
  </div>
</div>

<!-- Zoom box -->
<div id="projZoomBox"></div>

<div class="proj-wrap">
  <div class="proj-container">

    <div class="proj-grid">

      <!-- ── GALLERY ── -->
      <div class="proj-gallery">
        <div class="gallery-wrap">

          <div class="thumb-col" id="projThumbCol">
            <?php foreach ($images as $i => $img): ?>
              <div class="thumb-item <?= $i === 0 ? 'active' : ''; ?>"
                data-index="<?= $i; ?>"
                onclick="projSwitchImg(<?= $i; ?>)">
                <img src="<?= htmlspecialchars($img); ?>" alt="thumb">
              </div>
            <?php endforeach; ?>
          </div>

          <div class="main-img-box" id="projMainBox">
            <div class="proj-type-badge"><?= htmlspecialchars($type); ?></div>
            <img id="projMainImg"
              src="<?= htmlspecialchars($images[0]); ?>"
              alt="<?= $title; ?>">
          </div>

        </div>
      </div>

      <!-- ── DETAILS ── -->
      <div class="proj-details">

        <div class="proj-store-tag">
          <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
          </svg>
          SPI Innovation Store
        </div>

        <h1 class="proj-title"><?= $title; ?></h1>

        <hr class="proj-divider">

        <!-- Price -->
        <div class="proj-price-block">
          <div class="price-row">
            <?php if ($projectDiscount > 0): ?>
              <span class="disc-tag"><?= round($projectDiscount); ?>% off</span>
            <?php endif; ?>
            <div class="proj-price">₹<span id="projPriceDisplay"><?= format_price($projectPrice); ?></span></div>
          </div>
          <?php if ($projectOldPrice > $projectPrice): ?>
            <div class="proj-old-price">M.R.P: <del>₹<?= format_price($projectOldPrice); ?></del></div>
          <?php endif; ?>
          <div class="proj-tax">✓ Inclusive of all taxes</div>
        </div>

        <!-- What You Get -->
        <div class="proj-card">
          <div class="proj-card-title">What You Will Get</div>
          <ul>
            <li><i class="fa fa-check-circle"></i> Complete working project guidance</li>
            <li><i class="fa fa-check-circle"></i> Practical implementation support</li>
            <li><i class="fa fa-check-circle"></i> Project report and documentation guidance</li>
            <li><i class="fa fa-check-circle"></i> Presentation and viva explanation support</li>
            <li><i class="fa fa-check-circle"></i> Customization support as per requirement</li>
          </ul>
        </div>

        <!-- Description -->
        <div class="proj-card">
          <div class="proj-card-title">Project Description</div>
          <p><?= nl2br(htmlspecialchars($description)); ?></p>
        </div>

      </div>

      <!-- ── PURCHASE BOX ── -->
      <div class="proj-purchase">
        <div class="purchase-card">

          <div class="purchase-price">₹<span id="projSidePrice"><?= format_price($projectPrice); ?></span></div>
          <div class="purchase-tax">Inclusive of all taxes</div>

          <div class="stock-badge <?= $projectStock > 0 ? 'in' : 'out'; ?>">
            <span class="stock-dot"></span>
            <?= $projectStock > 0 ? 'Available' : 'Unavailable'; ?>
          </div>

          <div class="purchase-actions">
            <div class="qty-label">Quantity</div>
            <div class="qty-wrap">
              <button class="qty-btn" id="projQtyMinus">−</button>
              <input class="qty-num" type="number" id="projQty" value="1" min="1" max="<?= $projectStock; ?>">
              <button class="qty-btn" id="projQtyPlus">+</button>
            </div>
            <div id="projStockMsg"></div>

            <button class="btn-proj-cart add-to-cart" data-id="<?= $id; ?>" <?= $projectStock <= 0 ? 'disabled' : ''; ?>>
              <i class="fa-solid fa-cart-shopping"></i> Add to Cart
            </button>

            <button class="btn-proj-buy buy-now" data-id="<?= $id; ?>" <?= $projectStock <= 0 ? 'disabled' : ''; ?>>
              <i class="fa-solid fa-bolt"></i> Buy Now
            </button>
          </div>

          <div class="seller-info">
            <div class="seller-row"><span>Support</span><strong>Project Help & Training</strong></div>
            <div class="seller-row"><span>Format</span><strong>Digital & Physical</strong></div>
            <div class="seller-row"><span>Delivery</span><strong>Instant Access</strong></div>
          </div>

        </div>
      </div>

    </div><!-- /proj-grid -->


    <!-- ══════════════════════
         BOTTOM EXTRA
    ══════════════════════ -->
    <div class="proj-extra" id="projExtraSection">

      <!-- What You Get Detail -->
      <div class="extra-card">
        <div class="extra-head">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="9 11 12 14 22 4" />
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
          </svg>
          Complete Package Details
        </div>
        <div class="extra-body">
          <div class="get-grid">
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-code"></i></div>
              <div class="get-text">Complete source code with comments</div>
            </div>
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-file-alt"></i></div>
              <div class="get-text">Project report & documentation</div>
            </div>
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-chalkboard-teacher"></i></div>
              <div class="get-text">Step-by-step implementation guide</div>
            </div>
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-comments"></i></div>
              <div class="get-text">Viva & presentation preparation</div>
            </div>
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-tools"></i></div>
              <div class="get-text">Customization as per your needs</div>
            </div>
            <div class="get-item">
              <div class="get-icon"><i class="fa fa-headset"></i></div>
              <div class="get-text">Dedicated support & assistance</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Full Description -->
      <?php if (!empty($description)): ?>
        <div class="extra-card">
          <div class="extra-head">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            About This Project
          </div>
          <div class="extra-body">
            <div class="proj-desc"><?= nl2br(htmlspecialchars($description)); ?></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Note -->
      <!-- <div class="extra-card">
        <div class="extra-head" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
          </svg>
          Important Note
        </div>
        <div class="extra-body">
          <p style="margin:0;font-size:13px;color:var(--ink-2);line-height:1.7">
            Project scope and deliverables may vary based on your specific requirements.
            Contact our support team before placing the order for major customizations.
            All guidance is provided digitally unless physical components are specified.
          </p>
        </div>
      </div> -->

    </div>

  </div>
</div><!-- /proj-wrap -->

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
  /* ── Gallery ── */
  const projImgs = <?= json_encode($images); ?>;
  let projCurrent = 0;

  function projSwitchImg(idx) {
    projCurrent = idx;
    const el = document.getElementById('projMainImg');
    el.style.opacity = '0';
    setTimeout(() => {
      el.src = projImgs[idx];
      el.style.opacity = '1';
    }, 150);
    document.querySelectorAll('.thumb-item').forEach((t, i) => {
      t.classList.toggle('active', i === idx);
    });
  }

  /* ── Zoom ── */
  const projMainBox = document.getElementById('projMainBox');
  const projZoomBox = document.getElementById('projZoomBox');
  const projExtraSec = document.getElementById('projExtraSection');

  function projCanZoom() {
    if (window.innerWidth <= 1100) return false;
    if (!projExtraSec) return false;
    return projExtraSec.getBoundingClientRect().top > window.innerHeight - 120;
  }

  if (projMainBox && projZoomBox) {
    projMainBox.addEventListener('mouseenter', () => {
      if (!projCanZoom()) return;
      projZoomBox.style.backgroundImage = `url('${projImgs[projCurrent]}')`;
      projZoomBox.style.display = 'block';
      positionProjZoom();
    });
    projMainBox.addEventListener('mousemove', (e) => {
      if (!projCanZoom()) {
        projZoomBox.style.display = 'none';
        return;
      }
      const r = projMainBox.getBoundingClientRect();
      projZoomBox.style.backgroundPosition =
        `${((e.clientX - r.left) / r.width) * 100}% ${((e.clientY - r.top) / r.height) * 100}%`;
      positionProjZoom();
    });
    projMainBox.addEventListener('mouseleave', () => {
      projZoomBox.style.display = 'none';
    });
    window.addEventListener('scroll', () => {
      if (!projCanZoom()) projZoomBox.style.display = 'none';
    });
    window.addEventListener('resize', () => {
      projZoomBox.style.display = 'none';
    });
  }

  function positionProjZoom() {
    const r = projMainBox.getBoundingClientRect();
    let l = r.right + 20;
    if (l + 450 > window.innerWidth - 10) l = r.left - 470;
    let t = r.top;
    if (t + 390 > window.innerHeight - 10) t = window.innerHeight - 410;
    if (t < 80) t = 80;
    projZoomBox.style.left = l + 'px';
    projZoomBox.style.top = t + 'px';
  }

  /* ── Qty + Price ── */
  const projBasePrice = <?= $projectPrice; ?>;
  const projMaxStock = <?= $projectStock; ?>;
  const projQtyEl = document.getElementById('projQty');
  const projStockMsg = document.getElementById('projStockMsg');

  function projUpdatePrice() {
    let qty = parseInt(projQtyEl.value) || 1;
    if (qty < 1) {
      qty = 1;
      projQtyEl.value = 1;
    }
    if (projMaxStock > 0 && qty > projMaxStock) {
      qty = projMaxStock;
      projQtyEl.value = projMaxStock;
      projStockMsg.textContent = `Only ${projMaxStock} available`;
      projStockMsg.style.display = 'block';
    } else {
      projStockMsg.style.display = 'none';
    }
    const total = projBasePrice * qty;
    document.getElementById('projSidePrice').textContent = total.toLocaleString('en-IN');
    document.getElementById('projPriceDisplay').textContent = total.toLocaleString('en-IN');
  }

  document.getElementById('projQtyPlus').addEventListener('click', () => {
    projQtyEl.value = Math.min((parseInt(projQtyEl.value) || 1) + 1, projMaxStock);
    projUpdatePrice();
  });
  document.getElementById('projQtyMinus').addEventListener('click', () => {
    projQtyEl.value = Math.max((parseInt(projQtyEl.value) || 1) - 1, 1);
    projUpdatePrice();
  });
  projQtyEl.addEventListener('input', projUpdatePrice);

  /* ── Cart & Buy ── */
  document.querySelector('.add-to-cart')?.addEventListener('click', function() {
    const pid = this.dataset.id;
    const qty = document.getElementById('projQty').value;
    $.post('AJAX/add_to_cart.php', {
      product_id: pid,
      item_type: 'project',
      qty
    }, function(res) {
      if (res.trim() === 'login_required') {
        typeof openAuthModal === 'function' ? openAuthModal() : (window.location.href = 'login.php');
        return;
      }
      if (res.trim() === 'success') {
        if (typeof updateHeaderCounts === 'function') updateHeaderCounts();
        if (typeof ecomToast === 'function') ecomToast('👜 Project added to cart!');
      }
    });
  });

  document.querySelector('.buy-now')?.addEventListener('click', function() {
    const pid = this.dataset.id;
    const qty = document.getElementById('projQty').value;
    window.location.href = `checkout.php?product_id=${pid}&item_type=project&qty=${qty}`;
  });
</script>