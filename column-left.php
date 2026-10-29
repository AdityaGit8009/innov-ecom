<?php
include 'config/db.php';

$sidebarCategories = [];

$catSql = "SELECT id, name FROM categories WHERE status=1 ORDER BY name ASC";
$catRes = mysqli_query($conn, $catSql);

while ($cat = mysqli_fetch_assoc($catRes)) {
    $catId = (int)$cat['id'];

    $sidebarCategories[$catId] = [
        'title' => trim($cat['name']),
        'link'  => 'products.php?cat_id=' . $catId,
        'sub'   => []
    ];
}

$subSql = "SELECT 
            sc.id AS sub_id, 
            sc.category_id, 
            sc.name AS sub_name, 
            COUNT(p.id) AS total_products
           FROM sub_categories sc
           LEFT JOIN products p 
            ON p.sub_category_id = sc.id 
            AND p.status = 1
           WHERE sc.status = 1
           GROUP BY sc.id, sc.category_id, sc.name
           ORDER BY sc.name ASC";

$subRes = mysqli_query($conn, $subSql);

while ($row = mysqli_fetch_assoc($subRes)) {
    $catId = (int)$row['category_id'];

    if (!isset($sidebarCategories[$catId])) continue;

    $sidebarCategories[$catId]['sub'][] = [
        'title' => trim($row['sub_name']),
        'count' => (int)$row['total_products'],
        'link'  => 'products.php?sub_id=' . (int)$row['sub_id']
    ];
}
$bestSellers = mysqli_query($conn, "
    SELECT id, title, price, old_price, main_image, rating 
    FROM products 
    WHERE status = 1 
    ORDER BY 
        CAST(REPLACE(REPLACE(bought,'+',''),' bought in past month','') AS UNSIGNED) DESC, 
        rating DESC 
    LIMIT 6
");
?>

<style>
    /* =====================================================
       CATEGORY SIDEBAR COMPLETE FIX
    ===================================================== */

    .sp-category-wrapper {
        width: 100%;
        position: relative;
        z-index: 2;
    }

    .sp-category-wrapper,
    .sp-category-wrapper * {
        box-sizing: border-box;
    }

    .sp-sidebar {
        width: 100%;
        position: relative;
        overflow: visible;
    }

    .sp-category-card {
        width: 100%;
        padding: 18px;
        border-radius: 24px;
        background: linear-gradient(180deg, var(--white) 0%, var(--cultured) 100%);
        border: 1px solid var(--cultured);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        overflow: visible;
        position: relative;
    }

    .sp-sidebar-head {
        padding-bottom: 14px;
        margin-bottom: 15px;
        border-bottom: 1px solid var(--cultured);
        position: relative;
        z-index: 2;
        background: transparent;
    }

    .sp-sidebar-title {
        margin: 0;
        color: var(--eerie-black);
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 1.8px;
        text-transform: uppercase;
    }

    .sp-sidebar-title span {
        color: var(--salmon-pink);
    }

    .sp-sidebar-close {
        display: none;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 12px;
        background: var(--cultured);
        color: var(--salmon-pink);
        font-size: 22px;
        align-items: center;
        justify-content: center;
    }

    .sp-category-list {
        list-style: none;
        margin: 0;
        padding: 0;
        position: relative;
        overflow: visible;
    }

    .sp-category-item {
        list-style: none;
        margin: 0 0 5px 0;
        padding: 0;
        position: relative;
        display: block;
        width: 100%;
        height: auto;
        overflow: visible;
        clear: both;
    }

    .sp-category-btn {
        width: 100%;
        min-height: 40px;
        padding: 0px 0px;
        border: 1px solid var(--cultured);
        border-radius: 18px;
        background: var(--white);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
        cursor: pointer;
        transition: 0.25s ease;
        position: relative;
        z-index: 2;
    }

    .sp-category-btn:hover {
        background: var(--cultured);
        border-color: var(--sandy-brown);
        box-shadow: 0 12px 30px rgba(239,102,47,0.11);
    }

    .sp-category-btn.active {
        background: linear-gradient(135deg, var(--cultured) 0%, var(--white) 100%);
        border-color: var(--salmon-pink);
        box-shadow: 0 14px 34px rgba(239,102,47,0.16);
    }

    .sp-cat-left {
        min-width: 0;
        flex: 1;
    }

    .sp-cat-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 14px;
        background: var(--cultured);
        color: var(--salmon-pink);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--sandy-brown);
        font-size: 20px;
        transition: 0.25s ease;
    }

    .sp-category-btn.active .sp-cat-icon {
        background: var(--salmon-pink);
        color: var(--white);
        box-shadow: 0 8px 18px rgba(239,102,47,0.26);
    }

    .sp-cat-title {
        margin: 0;
        color: var(--davys-gray);
        font-size: 15px;
        font-weight: 850;
        line-height: 1.2;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sp-category-btn.active .sp-cat-title {
        color: var(--salmon-pink);
    }

    .sp-toggle-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        background: var(--cultured);
        color: var(--davys-gray);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.25s ease;
        font-size: 20px;
    }

    .sp-category-btn.active .sp-toggle-icon {
        background: var(--salmon-pink);
        color: var(--white);
    }

    .sp-category-btn .sp-minus {
        display: none;
    }

    .sp-category-btn.active .sp-plus {
        display: none;
    }

    .sp-category-btn.active .sp-minus {
        display: inline-block;
    }

    /* =====================================================
       SUBCATEGORY PANEL - NO OVERLAP
    ===================================================== */

    .sp-sub-list {
        display: none;
        list-style: none;
        margin: 10px 0 0 0;
        padding: 12px;
        width: 100%;
        height: auto;
        position: static;
        clear: both;
        float: none;
        overflow: visible;
        background: var(--white);
        border: 1px solid var(--cultured);
        border-radius: 18px;
        box-shadow: inset 0 0 0 1px rgba(239,102,47,0.03);
        z-index: 1;
    }

    .sp-sub-list.active {
        display: block;
        animation: spOpen 0.22s ease;
    }

    @keyframes spOpen {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sp-sub-item {
        list-style: none;
        margin: 0 0 9px 0;
        padding: 0;
        width: 100%;
        display: block;
        position: static;
        clear: both;
        float: none;
        overflow: visible;
    }

    .sp-sub-item:last-child {
        margin-bottom: 0;
    }

    .sp-sub-link {
        width: 100%;
        min-height: 44px;
        padding: 10px 11px;
        border-radius: 14px;
        background: var(--cultured);
        border: 1px solid var(--cultured);
        color: var(--davys-gray);
        text-decoration: none;
        transition: 0.22s ease;
        overflow: hidden;
    }

    .sp-sub-link:hover {
        background: var(--cultured);
        color: var(--salmon-pink);
        border-color: var(--sandy-brown);
        transform: translateX(3px);
    }

    .sp-sub-title {
        margin: 0;
        flex: 1;
        min-width: 0;
        color: inherit;
        font-size: 13px;
        font-weight: 850;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sp-sub-count {
        min-width: 30px;
        height: 25px;
        padding: 0 8px;
        border-radius: 999px;
        background: var(--white);
        color: var(--salmon-pink);
        border: 1px solid var(--sandy-brown);
        font-size: 12px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .sp-sub-link:hover .sp-sub-count {
        background: #ff5a1f;
        color: #ffffff;
        border-color: #ff5a1f;
    }

    /* =====================================================
       BEST SELLERS FIXED DESIGN
    ===================================================== */

    .sp-best-sellers {
        background: var(--white) !important;
        border: 1px solid var(--cultured) !important;
        border-radius: 22px !important;
        padding: 18px !important;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.07) !important;
        position: relative !important;
        z-index: 1 !important;
    }

    .sp-showcase-heading {
        font-size: 17px !important;
        font-weight: 900 !important;
        color: var(--eerie-black) !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin: 0 0 16px 0 !important;
    }

    .sp-showcase-container {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
    }

    .sp-showcase {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 11px !important;
        border-radius: 16px !important;
        border: 1px solid var(--cultured) !important;
        background: var(--white) !important;
        transition: 0.22s ease !important;
        overflow: hidden !important;
    }

    .sp-showcase:hover {
        border-color: var(--sandy-brown) !important;
        background: var(--cultured) !important;
        transform: translateY(-2px) !important;
    }

    .sp-showcase-img-box {
        width: 74px !important;
        height: 74px !important;
        min-width: 74px !important;
        border-radius: 15px !important;
        overflow: hidden !important;
        background: var(--cultured) !important;
        border: 1px solid var(--cultured) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .sp-showcase-img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block !important;
    }

    .sp-showcase-content {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    .sp-showcase-title-link {
        text-decoration: none !important;
        display: block !important;
    }

    .sp-showcase-title {
        font-size: 13px !important;
        font-weight: 900 !important;
        color: var(--eerie-black) !important;
        margin: 0 0 6px 0 !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }

    .sp-showcase-rating {
        display: flex !important;
        align-items: center !important;
        gap: 1px !important;
        color: #f59e0b !important;
        font-size: 14px !important;
        line-height: 1 !important;
        margin-bottom: 7px !important;
        white-space: nowrap !important;
    }

    .sp-showcase-rating ion-icon {
        display: inline-flex !important;
        width: 14px !important;
        height: 14px !important;
        min-width: 14px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .sp-price-box {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
        line-height: 1 !important;
    }

    .sp-price-box del {
        color: #94a3b8 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    .sp-price-box .sp-price {
        color: #ff5a1f !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        margin: 0 !important;
    }

    .sp-best-sellers a,
    .sp-category-card a {
        text-decoration: none;
    }

    /* =====================================================
       DESKTOP STICKY ONLY
    ===================================================== */

    @media (min-width: 769px) {
        .sp-category-wrapper {
            position: sticky !important;
            top: 10px !important;
            align-self: flex-start !important;
            z-index: 20 !important;
        }

        .sp-sidebar {
            position: relative !important;
            overflow-y: visible !important;
            overflow-x: hidden !important;
            padding-right: 4px !important;
        }

        .sp-cat-title {
            max-width: 170px;
        }
    }

    /* Sticky parent fix */
    .product-container,
    .product-container .container,
    .sp-category-wrapper,
    .sp-sidebar {
        transform: none !important;
        contain: none !important;
    }

    /* =====================================================
       MOBILE DRAWER + BLANK SPACE FIX
    ===================================================== */

    @media (max-width: 768px) {

        /* Home page ka desktop sidebar mobile mein hide rahega.
           Mobile category drawer ab header.php se har page par common aa raha hai,
           isliye home page par duplicate category nahi dikhegi. */
        .sp-category-wrapper,
        .product-container.sp-category-wrapper {
            display: none !important;
        }

        .sp-sidebar {
            display: none !important;
        }

        .sp-sidebar-close {
            display: inline-flex !important;
        }

        .sp-category-card {
            border-radius: 22px;
            padding: 16px;
        }

        .sp-sidebar-title {
            font-size: 18px;
        }

        .sp-cat-title {
            font-size: 14px;
        }

        .sp-best-sellers {
            display: none !important;
        }
    }
</style>

<div class="product-container sp-category-wrapper">
    <div class="container">
        <div class="sidebar sp-sidebar" data-category-menu>

            <div class="sp-category-card">

                <div class="sp-sidebar-head d-flex justify-content-between align-items-center">
                    <h2 class="sp-sidebar-title">Cate<span>gory</span></h2>

                    <button class="sp-sidebar-close" data-category-close type="button" aria-label="Close category">
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>

                <ul class="sp-category-list">
                    <?php foreach ($sidebarCategories as $cat): ?>
                        <li class="sp-category-item">

                            <button class="sp-category-btn d-flex align-items-center justify-content-between gap-2" data-sp-accordion-btn type="button">

                                <div class="sp-cat-left d-flex align-items-center gap-3">
                                    <span class="sp-cat-icon">
                                        <ion-icon name="layers-outline"></ion-icon>
                                    </span>

                                    <p class="sp-cat-title">
                                        <?= htmlspecialchars($cat['title']); ?>
                                    </p>
                                </div>

                                <span class="sp-toggle-icon">
                                    <ion-icon name="add-outline" class="sp-plus"></ion-icon>
                                    <ion-icon name="remove-outline" class="sp-minus"></ion-icon>
                                </span>

                            </button>

                            <ul class="sp-sub-list" data-sp-accordion-panel>

                                <?php if (!empty($cat['sub'])): ?>

                                    <?php foreach ($cat['sub'] as $sub): ?>
                                        <li class="sp-sub-item">
                                            <a href="<?= $sub['link']; ?>" class="sp-sub-link d-flex align-items-center justify-content-between gap-2">
                                                <p class="sp-sub-title"><?= htmlspecialchars($sub['title']); ?></p>
                                                <span class="sp-sub-count"><?= (int)$sub['count']; ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <li class="sp-sub-item">
                                        <a href="<?= $cat['link']; ?>" class="sp-sub-link d-flex align-items-center justify-content-between gap-2">
                                            <p class="sp-sub-title">No subcategory yet</p>
                                            <span class="sp-sub-count">0</span>
                                        </a>
                                    </li>

                                <?php endif; ?>

                            </ul>

                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>

            <div class="sp-best-sellers mt-4">
                <h3 class="sp-showcase-heading">Best Sellers</h3>

                <div class="sp-showcase-wrapper">
                    <div class="sp-showcase-container">

                        <?php if ($bestSellers && mysqli_num_rows($bestSellers) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($bestSellers)): ?>
                                <div class="sp-showcase">

                                    <a href="product.php?id=<?= (int)$item['id']; ?>" class="sp-showcase-img-box">
                                        <img
                                            src="uploads/products/<?= htmlspecialchars($item['main_image']); ?>"
                                            class="sp-showcase-img"
                                            alt="<?= htmlspecialchars($item['title']); ?>">
                                    </a>

                                    <div class="sp-showcase-content">

                                        <a href="product.php?id=<?= (int)$item['id']; ?>" class="sp-showcase-title-link">
                                            <h4 class="sp-showcase-title">
                                                <?= htmlspecialchars($item['title']); ?>
                                            </h4>
                                        </a>

                                        <div class="sp-showcase-rating">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= round((float)$item['rating'])
                                                    ? '<ion-icon name="star"></ion-icon>'
                                                    : '<ion-icon name="star-outline"></ion-icon>';
                                            }
                                            ?>
                                        </div>

                                        <div class="sp-price-box">
                                            <?php if (!empty($item['old_price']) && (float)$item['old_price'] > (float)$item['price']): ?>
                                                <del>₹<?= format_price((float)$item['old_price']); ?></del>
                                            <?php endif; ?>

                                            <span class="sp-price">₹<?= format_price((float)$item['price']); ?></span>
                                        </div>

                                    </div>

                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No best seller found.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.addEventListener("click", function(e) {
            const btn = e.target.closest("[data-sp-accordion-btn]");

            if (!btn) {
                return;
            }

            e.preventDefault();

            const parentLi = btn.closest(".sp-category-item");
            const submenu = parentLi ? parentLi.querySelector("[data-sp-accordion-panel]") : null;

            if (!submenu) {
                return;
            }

            const alreadyOpen = btn.classList.contains("active");

            document.querySelectorAll("[data-sp-accordion-btn]").forEach(function(item) {
                item.classList.remove("active");
            });

            document.querySelectorAll("[data-sp-accordion-panel]").forEach(function(item) {
                item.classList.remove("active");
                item.style.display = "none";
            });

            if (!alreadyOpen) {
                btn.classList.add("active");
                submenu.classList.add("active");
                submenu.style.display = "block";
            }
        });
    });
</script>