<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($conn) || !$conn) {
    @require_once __DIR__ . '/config/db.php';
}
$rvIds = $_SESSION['recently_viewed_products'] ?? [];
$rvIds = array_values(array_unique(array_map('intval', (array)$rvIds)));
$currentProductIdForRv = isset($product_id) ? (int)$product_id : 0;
$rvIds = array_values(array_filter($rvIds, function ($id) use ($currentProductIdForRv) {
    return $id > 0 && $id !== $currentProductIdForRv;
}));
$rvIds = array_slice($rvIds, 0, 8);
if (!empty($rvIds)):
    $idList = implode(',', $rvIds);
    $rvProducts = mysqli_query($conn, "SELECT id, title, price, old_price, discount, stock, main_image, image1 FROM products WHERE status=1 AND id IN ($idList) ORDER BY FIELD(id, $idList)");
    if ($rvProducts && mysqli_num_rows($rvProducts) > 0):
?>
        <section class="recently-viewed-wrap">
            <div class="recently-viewed-head">
                <div>
                    <span class="rv-kicker">Continue shopping</span>
                    <h2>Recently Viewed Products</h2>
                </div>
                <a href="products.php" class="rv-view-all">View all products</a>
            </div>
            <div class="recently-viewed-grid">
                <?php while ($rv = mysqli_fetch_assoc($rvProducts)):
                    $img = !empty($rv['main_image']) ? $rv['main_image'] : (!empty($rv['image1']) ? $rv['image1'] : 'no-image.png');
                    $imgPath = 'uploads/products/' . $img;
                    if (!is_file(__DIR__ . '/' . $imgPath)) $imgPath = 'assets/images/no-image.png';
                    $stock = (int)($rv['stock'] ?? 0);
                ?>
                    <article class="rv-card">
                        <button type="button" class="rv-quick quick-view-btn" data-id="<?= (int)$rv['id']; ?>" title="Quick view">
                            <ion-icon style="display: inline-block; margin-top: 10px;" name="eye-outline"></ion-icon>
                        </button>
                        <a href="product.php?id=<?= (int)$rv['id']; ?>" class="rv-img-box">
                            <img src="<?= htmlspecialchars($imgPath); ?>" alt="<?= htmlspecialchars($rv['title']); ?>">
                        </a>
                        <div class="rv-info">
                            <a class="rv-title" href="product.php?id=<?= (int)$rv['id']; ?>"><?= htmlspecialchars($rv['title']); ?></a>
                            <div class="rv-price-row">
                                <strong>₹<?= number_format((float)$rv['price'], 2); ?></strong>
                                <?php if (!empty($rv['old_price']) && (float)$rv['old_price'] > (float)$rv['price']): ?>
                                    <del>₹<?= number_format((float)$rv['old_price'], 2); ?></del>
                                <?php endif; ?>
                            </div>
                            <div class="rv-actions">
                                <button type="button" class="cart-btn" data-id="<?= (int)$rv['id']; ?>" <?= $stock <= 0 ? 'disabled' : ''; ?>>
                                    <?= $stock > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
<?php
    endif;
endif;
?>