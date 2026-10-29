<?php
// ================= BANNER DATA =================
$banner = [
    "video_id" => "OppFdGBSLdg",
    "subtitle" => "Turn Curiosity into Smart Solutions with Softpro IoT Kit",
    "title" => "A complete IoT learning kit by Softpro India for building,
                programming, and connecting smart devices to the cloud."
];

?>

<!-- ================= BANNER ================= -->
<div class="banner">
    <div class="container">
        <div class="slider-container has-scrollbar">
            <div class="slider-item">
                <div class="video-wrapper">
                    <iframe width="560" height="315"
                        src="https://www.youtube.com/embed/<?= $banner['video_id']; ?>?autoplay=1&mute=1&controls=0&loop=1&playlist=<?= $banner['video_id']; ?>"
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="banner-content">
                    <small>
                        <b>
                            <p class="banner-subtitle">
                                <?= $banner['subtitle']; ?>
                            </p>
                        </b>
                    </small>

                    <small>
                        <p class="banner-title" style="color: var(--theme-white)">
                            <?= $banner['title']; ?>
                        </p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ================= END HEADER ================= -->
<!-- Video -->
<?php
// ================= DYNAMIC CATEGORY DATA =================
if (!isset($conn)) {
    include 'config/db.php';
}
$categories = [];
$catQuery = "SELECT c.id, c.name AS title, COUNT(p.id) AS total_products,
                    (SELECT p2.main_image FROM products p2 WHERE p2.category_id=c.id AND p2.status=1 AND p2.main_image IS NOT NULL AND p2.main_image<>'' ORDER BY p2.id DESC LIMIT 1) AS image
             FROM categories c
             LEFT JOIN products p ON p.category_id=c.id AND p.status=1
             WHERE c.status=1
             GROUP BY c.id, c.name
             ORDER BY c.name ASC";
$catResult = mysqli_query($conn, $catQuery);
while ($row = mysqli_fetch_assoc($catResult)) {
    $img = trim((string)($row['image'] ?? ''));
    $categories[] = [
        'id' => (int)$row['id'],
        'title' => trim($row['title']),
        'count' => (int)$row['total_products'],
        'image' => $img ? 'uploads/products/' . $img : '',
        'link' => 'products.php?cat_id=' . (int)$row['id']
    ];
}
?>


<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
<style>
    .category-card-link {
        text-decoration: none;
        color: inherit
    }

    .category-card-link:hover {
        border-color: #ff7a3d;
        box-shadow: 0 12px 30px rgba(255, 90, 31, .12)
    }

    .category-item-title {
        max-width: 155px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .category-content-flex {
        min-width: 0
    }

    .category-content-box {
        min-width: 0
    }

    .category-img-box ion-icon {
        font-size: 24px;
        color: #ff5a1f
    }
</style>

<!-- ================= CATEGORY ================= -->
<div class="category">
    <div class="container">
        <div class="category-item-container has-scrollbar">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">

                    <?php foreach ($categories as $cat): ?>
                        <div class="swiper-slide">
                            <a href="<?= htmlspecialchars($cat['link']); ?>" class="category-item category-card-link" title="<?= htmlspecialchars($cat['title']); ?>">
                                <div class="category-img-box">
                                    <?php if (!empty($cat['image'])): ?>
                                        <img src="<?= htmlspecialchars($cat['image']); ?>" alt="<?= htmlspecialchars($cat['title']); ?>" width="30">
                                    <?php else: ?>
                                        <ion-icon name="cube-outline"></ion-icon>
                                    <?php endif; ?>
                                </div>

                                <div class="category-content-box">
                                    <div class="category-content-flex">
                                        <h3 class="category-item-title"><?= htmlspecialchars($cat['title']); ?></h3>
                                        <p class="category-item-amount">(<?= (int)$cat['count']; ?>)</p>
                                    </div>

                                    <span class="category-btn">Show all</span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 1,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        // loop: true,
        spaceBetween: 8,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 12,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 14,
            },
        },
    });
</script>