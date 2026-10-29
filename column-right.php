<?php
include "config/db.php";

/* =========================
HELPER FUNCTIONS
========================= */

function showRating($rating)
{
    for ($i = 1; $i <= 5; $i++) {
        echo $i <= round($rating)
            ? '<ion-icon name="star"></ion-icon>'
            : '<ion-icon name="star-outline"></ion-icon>';
    }
}

function productBadge($discount, $stock = null)
{
    if ($stock !== null && $stock <= 0) {
        return '<p class="showcase-badge black" style="background: var(--eerie-black); color: white;">Out Of Stock</p>';
    }
    if ($discount > 0) {
        $discount = number_format((float)$discount, 0);
        return '<p class="showcase-badge">' . $discount . '% OFF</p>';
    }
    return '';
}

/* =========================
NEW PRODUCTS
========================= */

$newProducts = mysqli_query(
    $conn,
    "SELECT p.*, c.name AS category_name 
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 1
    ORDER BY p.created_at DESC
    LIMIT 16"
);

/* =========================
NEW ARRIVALS
========================= */

$newArrivals = mysqli_query(
    $conn,
    "SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 1
    ORDER BY p.created_at DESC
    LIMIT 4"
);


/* =========================
TRENDING
========================= */

$trending = mysqli_query(
    $conn,
    "SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 1
    ORDER BY p.bought DESC
    LIMIT 4"
);


/* =========================
TOP RATED
========================= */

$topRated = mysqli_query(
    $conn,
    "SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 1
    ORDER BY p.rating DESC
    LIMIT 4"
);


/* =========================
DEAL OF THE DAY
========================= */

$deal = mysqli_query(
    $conn,
    "SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.status = 1
    ORDER BY p.discount DESC, p.created_at DESC
    LIMIT 1"
);
?>

<!-- =========================
NEW PRODUCTS
========================= -->
<style>
    /* Premium Product Featured UI */
    .product-featured .showcase {
        border-radius: 20px;
        background: linear-gradient(145deg, var(--white), var(--cultured));
        border: 1px solid var(--cultured);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 20px;
        transition: 0.3s ease;
    }

    .product-featured .showcase:hover {
        box-shadow: 0 15px 35px rgba(239, 102, 47, 0.15);
        border-color: var(--sandy-brown);
    }

    .product-featured .showcase-img {
        border-radius: 15px;
        margin-bottom: 20px;
        background: var(--white);
        padding: 10px;
        border: 1px solid var(--cultured);
    }

    .product-featured .showcase-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--eerie-black);
        margin-bottom: 10px;
    }

    .product-featured .price-box .price {
        font-size: 18px;
        color: var(--salmon-pink);
        font-weight: 900;
    }

    .product-featured .add-cart-btn {
        background: linear-gradient(135deg, var(--salmon-pink), var(--sandy-brown));
        color: var(--white);
        font-weight: 800;
        border-radius: 12px;
        padding: 12px 20px;
        /* width: 100%; */
        transition: transform 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .product-featured .add-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(239, 102, 47, 0.3);
    }

    /* Minimal Blocks UI */
    .product-minimal .showcase {
        border-radius: 16px;
        border: 1px solid var(--cultured);
        padding: 12px;
        background: var(--white);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }

    .product-minimal .showcase:hover {
        border-color: var(--sandy-brown);
        background: var(--cultured);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(239, 102, 47, 0.08);
    }

    .product-minimal .showcase-img {
        border-radius: 12px;
        background: var(--cultured);
        padding: 5px;
    }

    .product-minimal .showcase-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--davys-gray);
        margin-bottom: 5px;
    }

    /* Main Product Showcase Overrides */
    .product-grid .showcase {
        border-radius: 20px;
        overflow: hidden;
        background: var(--white);
        border: 1px solid var(--cultured);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        transition: 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .product-grid .showcase-content {
        display: flex;
        flex-direction: column;
        padding: 12px 15px 15px;
    }

    .product-grid .showcase-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        /* min-height: 42px; */
        margin-bottom: 8px;
    }

    .product-grid .buy-now-wrap {
        padding-top: 12px;
    }

    .product-grid .price-box .price {
        font-size: 18px !important;
    }

    .product-grid .showcase:hover {
        border-color: var(--cultured);
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    .buy-now-wrap .buy-now-btn {
        background: linear-gradient(135deg, var(--eerie-black), var(--onyx));
        border-radius: 12px;
        font-weight: 700;
        padding: 8px 15px;
        transition: 0.3s ease;
        color: var(--white) !important;
    }

    .buy-now-wrap .buy-now-btn:hover {
        background: linear-gradient(135deg, var(--salmon-pink), var(--sandy-brown));
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(239, 102, 47, 0.3);
    }
</style>

<div class="product-box">

    <h2 class="title">New Products</h2>

    <div class="product-grid">

        <?php while ($p = mysqli_fetch_assoc($newProducts)): ?>

            <?php
            $productId = (int)$p['id'];

            $mainImage = !empty($p['main_image'])
                ? $p['main_image']
                : 'no-image.png';

            $hoverImage = !empty($p['image1'])
                ? $p['image1']
                : $mainImage;

            // $price = number_format((float)$p['price'], 0);

            // $oldPrice = number_format((float)$p['old_price'], 0);

            $discount = (float)$p['discount'];
            $stock = (int)$p['stock'];
            ?>

            <div class="showcase">

                <div class="showcase-banner">

                    <img
                        src="uploads/products/<?= htmlspecialchars($mainImage); ?>"
                        class="product-img default"
                        width="300"
                        alt="<?= htmlspecialchars($p['title']); ?>">

                    <img
                        src="uploads/products/<?= htmlspecialchars($hoverImage); ?>"
                        class="product-img hover"
                        width="300"
                        alt="<?= htmlspecialchars($p['title']); ?>">

                    <?= productBadge($discount, $stock); ?>

                    <div class="showcase-actions">

                        <!-- WISHLIST -->
                        <button
                            class="btn-action wishlist-btn"
                            data-id="<?= $productId; ?>">

                            <ion-icon name="heart-outline"></ion-icon>
                        </button>

                        <!-- VIEW PRODUCT -->
                        <button type="button" class="btn-action quick-view-btn" data-id="<?= $productId; ?>" title="Quick view">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>

                        <!-- ADD TO CART -->
                        <button
                            class="btn-action cart-btn"
                            data-id="<?= $productId; ?>"
                            <?= $stock <= 0 ? 'disabled' : ''; ?>>

                            <ion-icon name="bag-add-outline"></ion-icon>
                        </button>

                        <!-- COMPARE -->
                        <button
                            class="btn-action compare-btn"
                            data-id="<?= $productId; ?>">

                            <ion-icon name="repeat-outline"></ion-icon>
                        </button>

                    </div>

                </div>

                <div class="showcase-content">

                    <p class="showcase-category">
                        <?= htmlspecialchars($p['category_name'] ?? ''); ?>
                    </p>

                    <h3 class="showcase-title">
                        <?= htmlspecialchars($p['title']); ?>
                    </h3>

                    <div class="showcase-rating">
                        <?php showRating($p['rating']); ?>
                    </div>

                    <div class="price-box">
                        <p class="price">
                            ₹<?= format_price((float)$p['price']); ?>
                        </p>

                        <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
                            <del>₹<?= format_price((float)$p['old_price']); ?></del>
                        <?php endif; ?>
                    </div>

                    <!-- BUY NOW -->
                    <div class="buy-now-wrap">
                        <a href="product.php?id=<?= $productId; ?>" class="buy-now-btn">
                            Buy Now
                        </a>
                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    </div>


    <!-- =========================
    NEW ARRIVALS
    ========================= -->

    <div class="product-minimal mt-3">

        <div class="product-showcase">

            <h2 class="title">New Arrivals</h2>

            <div class="showcase-wrapper has-scrollbar">

                <div class="showcase-container">

                    <?php while ($p = mysqli_fetch_assoc($newArrivals)): ?>

                        <?php
                        $mainImage = !empty($p['main_image'])
                            ? $p['main_image']
                            : 'no-image.png';
                        ?>

                        <div class="showcase">

                            <a href="product.php?id=<?= (int)$p['id']; ?>" style="display:block;">
                                <img
                                    src="uploads/products/<?= htmlspecialchars($mainImage); ?>"
                                    class="showcase-img"
                                    width="70"
                                    alt="<?= htmlspecialchars($p['title']); ?>">
                            </a>

                            <div class="showcase-content">

                                <p class="showcase-category">
                                    <?= htmlspecialchars($p['category_name']); ?>
                                </p>

                                <a href="product.php?id=<?= (int)$p['id']; ?>" style="text-decoration:none; color:inherit;">
                                    <h4 class="showcase-title">
                                        <?= htmlspecialchars($p['title']); ?>
                                    </h4>
                                </a>

                                <div class="price-box">
                                    <p class="price">₹<?= format_price((float)$p['price']); ?></p>
                                    <?php if ($p['old_price'] > $p['price']): ?>
                                        <del>₹<?= format_price((float)$p['old_price']); ?></del>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </div>


        <!-- =========================
        TRENDING
        ========================= -->

        <div class="product-showcase">

            <h2 class="title">Trending</h2>

            <div class="showcase-wrapper has-scrollbar">

                <div class="showcase-container">

                    <?php while ($p = mysqli_fetch_assoc($trending)): ?>

                        <?php
                        $mainImage = !empty($p['main_image'])
                            ? $p['main_image']
                            : 'no-image.png';
                        ?>

                        <div class="showcase">

                            <a href="product.php?id=<?= (int)$p['id']; ?>" style="display:block;">
                                <img
                                    src="uploads/products/<?= htmlspecialchars($mainImage); ?>"
                                    class="showcase-img"
                                    width="70"
                                    alt="<?= htmlspecialchars($p['title']); ?>">
                            </a>

                            <div class="showcase-content">

                                <p class="showcase-category">
                                    <?= htmlspecialchars($p['category_name']); ?>
                                </p>

                                <a href="product.php?id=<?= (int)$p['id']; ?>" style="text-decoration:none; color:inherit;">
                                    <h4 class="showcase-title">
                                        <?= htmlspecialchars($p['title']); ?>
                                    </h4>
                                </a>

                                <div class="price-box">
                                    <p class="price">₹<?= format_price((float)$p['price']); ?></p>
                                    <?php if ($p['old_price'] > $p['price']): ?>
                                        <del>₹<?= format_price((float)$p['old_price']); ?></del>
                                    <?php endif; ?>
                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </div>


        <!-- =========================
        TOP RATED
        ========================= -->

        <div class="product-showcase">

            <h2 class="title">Top Rated</h2>

            <div class="showcase-wrapper has-scrollbar">

                <div class="showcase-container">

                    <?php while ($p = mysqli_fetch_assoc($topRated)): ?>

                        <?php
                        $mainImage = !empty($p['main_image'])
                            ? $p['main_image']
                            : 'no-image.png';
                        ?>

                        <div class="showcase">

                            <a href="product.php?id=<?= (int)$p['id']; ?>" style="display:block;">
                                <img
                                    src="uploads/products/<?= htmlspecialchars($mainImage); ?>"
                                    class="showcase-img"
                                    width="70"
                                    alt="<?= htmlspecialchars($p['title']); ?>">
                            </a>

                            <div class="showcase-content">

                                <a href="product.php?id=<?= (int)$p['id']; ?>" style="text-decoration:none; color:inherit;">
                                    <h4 class="showcase-title">
                                        <?= htmlspecialchars($p['title']); ?>
                                    </h4>
                                </a>

                                <div class="showcase-rating d-flex align-items-center">
                                    <?php showRating($p['rating']); ?>
                                </div>

                                <div class="price-box">
                                    <p class="price">₹<?= format_price((float)$p['price']); ?></p>
                                </div>
                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
    DEAL OF THE DAY
    ========================= -->

    <div class="product-featured">

        <h2 class="title">Deal of the day</h2>

        <?php while ($p = mysqli_fetch_assoc($deal)): ?>

            <?php
            $dealProductId = (int)$p['id'];

            $dealStock = (int)$p['stock'];

            $mainImage = !empty($p['main_image'])
                ? $p['main_image']
                : 'no-image.png';
            ?>

            <div class="showcase">

                <img
                    src="uploads/products/<?= htmlspecialchars($mainImage); ?>"
                    class="showcase-img"
                    alt="<?= htmlspecialchars($p['title']); ?>">

                <div class="showcase-content">

                    <h1 class="showcase-title">
                        <?= htmlspecialchars($p['title']); ?>
                    </h1>

                    <p class="showcase-desc">
                        Seller: <?= htmlspecialchars($p['seller']); ?>
                    </p>

                    <div class="price-box">
                        <p class="price">
                            ₹<?= format_price((float)$p['price']); ?>
                        </p>
                        <?php if ($p['old_price'] > $p['price']): ?>
                            <del>₹<?= format_price((float)$p['old_price']); ?></del>
                        <?php endif; ?>
                    </div>

                    <div class="showcase-rating">
                        <?php showRating($p['rating']); ?>
                    </div>

                    <button
                        type="button"
                        class="add-cart-btn cart-btn"
                        data-id="<?= $dealProductId; ?>"
                        <?= $dealStock <= 0 ? 'disabled' : ''; ?>>

                        <?= $dealStock <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>

                    </button>

                    <div class="showcase-status">

                        <p>
                            Sold:
                            <b><?= htmlspecialchars($p['bought']); ?></b>
                        </p>

                        <p>
                            Available:
                            <b><?= $dealStock; ?></b>
                        </p>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    /* =========================
    WISHLIST
    ========================= */

    $('.wishlist-btn-old-disabled').click(function() {

        let pid = $(this).data('id');

        $.post("ajax/add_to_wishlist.php", {
            product_id: pid
        }, function(res) {

            if (res.trim() === "login_required") {
                openAuthModal();
                return;
            }

            updateHeaderCounts();

            ecomToast("❤️ Added to Wishlist");

        });

    });


    /* =========================
    CART
    ========================= */

    $(document).on('click', '.cart-btn-old-disabled', function() {

        let pid = $(this).data('id');

        $.post("ajax/add_to_cart.php", {
            product_id: pid
        }, function(res) {

            res = $.trim(res);

            if (res === "login_required") {
                openAuthModal();
                return;
            }

            if (res === "out_of_stock") {
                ecomToast("❌ Product out of stock");
                return;
            }

            if (res === "success" || res === "updated" || res !== "") {

                updateHeaderCounts();

                ecomToast("👜 Added to Cart");

            }

        });

    });


    /* =========================
    COMPARE
    ========================= */

    $('.compare-btn-old-disabled').click(function() {

        let pid = $(this).data('id');

        $.post("ajax/add_to_compare.php", {
            product_id: pid
        }, function(res) {

            if (res.trim() === 'success') {

                updateHeaderCounts();

                ecomToast("✅ Product added to Compare");

            }

        });

    });
</script>