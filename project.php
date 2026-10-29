<?php
include "config/db.php";

$projects = mysqli_query(
    $conn,
    "SELECT * FROM projects
     WHERE status = 1
     ORDER BY id DESC
     LIMIT 10"
);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

<style>
    .projects-section {
        /* padding: 90px 0 80px; */
        background: var(--white);
        overflow: hidden;
    }

    .projects-head {
        max-width: 720px;
        margin: 0 auto 45px;
        text-align: center;
    }

    .projects-title {
        font-size: 27px;
        font-weight: 600;
        color: var(--eerie-black);
        margin-bottom: 15px;
        letter-spacing: 0.4px;
        text-transform: capitalize;
    }

    .projects-subtitle {
        color: var(--sonic-silver);
        font-size: 16px;
        line-height: 1.8;
        max-width: 640px;
        margin: 0 auto;
    }

    .projects-title::after {
        display: none;
    }

    .swiper {
        padding: 10px 0 40px;
    }

    .swiper-slide {
        height: auto;
        display: flex;
        align-items: stretch;
    }

    .project-card {
        width: 100%;
        background: linear-gradient(180deg, var(--white) 0%, #fdf9f7 100%);
        border-radius: 28px;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: transform .35s ease, box-shadow .35s ease, border-color .25s ease;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .project-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 32px 70px rgba(15, 23, 42, .12);
        border-color: rgba(239, 102, 47, .25);
    }

    .project-image-wrap {
        position: relative;
        overflow: hidden;
    }

    .project-image {
        width: 100%;
        height: 260px;
        object-fit: cover;
        transition: transform .5s ease;
    }

    .project-card:hover .project-image {
        transform: scale(1.05);
    }

    .project-badge {
        position: absolute;
        top: 18px;
        left: 18px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(226, 232, 240, 0.9);
        color: var(--eerie-black);
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        z-index: 2;
        backdrop-filter: blur(8px);
    }

    .project-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(15, 23, 42, 0.42) 100%);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        transition: opacity .3s ease;
        padding: 24px;
    }

    .project-card:hover .project-overlay {
        opacity: 1;
    }

    .view-btn {
        background: rgba(255, 255, 255, 0.95);
        color: var(--eerie-black);
        text-decoration: none;
        padding: 12px 26px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid rgba(226, 232, 240, 0.9);
        transition: background .25s ease, color .25s ease, transform .15s ease, box-shadow .15s ease;
    }

    .view-btn:hover {
        background: var(--salmon-pink);
        color: var(--white);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(239, 102, 47, .18);
    }

    .project-content {
        padding: 26px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* 
    .project-name {
        font-size: 22px;
        font-weight: 900;
        color: var(--eerie-black);
        line-height: 1.3;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        word-break: break-word;
        min-height: 64px;
    }

    .project-desc {
        color: var(--sonic-silver);
        font-size: 15px;
        line-height: 1.8;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        min-height: 76px;
        margin-bottom: 18px;
    } */

    .price-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: auto;
        flex-wrap: wrap;
    }

    .price {
        font-size: 28px;
        font-weight: 900;
        color: var(--salmon-pink);
        margin: 0;
    }

    .price-box del {
        font-size: 15px;
        color: var(--sonic-silver);
        font-weight: 600;
    }

    .tax-text {
        margin-top: 12px;
        font-size: 13px;
        color: var(--ocean-green);
        font-weight: 700;
    }

    .btn-group-custom {
        display: flex;
        gap: 12px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .btn-cart {
        width: 52px;
        height: 52px;
        border: none;
        border-radius: 16px;
        background: var(--cultured);
        color: var(--eerie-black);
        font-size: 18px;
        cursor: pointer;
        transition: background .2s ease, color .2s ease, transform .15s ease;
    }

    .btn-cart:hover {
        background: var(--salmon-pink);
        color: var(--white);
        transform: translateY(-2px);
    }

    .btn-buy {
        flex: 1;
        min-width: 140px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 800;
        font-size: 15px;
        background: linear-gradient(135deg, var(--salmon-pink), var(--sandy-brown));
        color: var(--white);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .btn-buy:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(239, 102, 47, .16);
    }

    .swiper-pagination-bullet {
        background: var(--cultured);
        opacity: 1;
        transition: width .25s ease;
    }

    .swiper-pagination-bullet-active {
        background: var(--salmon-pink);
        width: 26px;
        border-radius: 999px;
    }

    @media(max-width:1200px) {
        .project-image {
            height: 240px;
        }
    }

    @media(max-width:992px) {
        .projects-section {
            padding: 70px 0 60px;
        }

        .project-card {
            border-radius: 24px;
        }

        .project-image {
            height: 220px;
        }
    }

    @media(max-width:768px) {
        .projects-section {
            padding: 55px 0;
        }

        .project-image {
            height: 210px;
        }

        .project-content {
            padding: 18px;
        }

        .project-name {
            font-size: 20px;
            /* min-height: auto; */
        }

        .project-desc {
            font-size: 14px;
            /* min-height: 68px; */
        }

        .price {
            font-size: 24px;
        }

        .btn-group-custom {
            margin-top: 16px;
        }
    }

    .project-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--eerie-black);
        line-height: 1.3;
        margin-bottom: 10px;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        min-height: auto !important;
        height: auto !important;
    }

    .project-desc {
        color: var(--sonic-silver);
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 12px;

        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;

        overflow: hidden;
        text-overflow: ellipsis;

        min-height: auto !important;
        height: auto !important;
    }
</style>

<section class="projects-section">

    <div class="container">

        <div class="projects-head">
            <h3 class="projects-title">
                Projects
            </h3>
        </div>

        <div class="swiper mySwiper2">

            <div class="swiper-wrapper">

                <?php while ($p = mysqli_fetch_assoc($projects)): ?>

                    <div class="swiper-slide">

                        <div class="project-card">

                            <div class="project-image-wrap">

                                <img
                                    src="uploads/projects/<?= $p['image']; ?>"
                                    alt="<?= htmlspecialchars($p['title']); ?>"
                                    class="project-image">

                                <?php if ((float)$p['discount'] > 0): ?>
                                    <span class="badge-discount">
                                        <?= round((float)$p['discount']); ?>% OFF
                                    </span>
                                <?php endif; ?>

                                <div class="project-overlay">

                                    <a href="project-details.php?id=<?= $p['id']; ?>"
                                        class="view-btn">

                                        View Details

                                    </a>

                                </div>

                            </div>

                            <div class="project-content">

                                <h3 class="project-name">
                                    <?= htmlspecialchars($p['title']); ?>
                                </h3>

                                <p class="project-desc">

                                    <?= strlen($p['description']) > 110
                                        ? substr($p['description'], 0, 110) . '...'
                                        : $p['description']; ?>

                                </p>

                                <div class="price-box">

                                    <p class="price">
                                        ₹<?= format_price((float)$p['price']); ?>
                                    </p>

                                    <?php if ((float)$p['old_price'] > (float)$p['price']): ?>

                                        <del>
                                            ₹<?= format_price((float)$p['old_price']); ?>
                                        </del>

                                    <?php endif; ?>

                                </div>

                                <div class="tax-text">
                                    Inclusive of all taxes
                                </div>

                                <div class="btn-group-custom">

                                    <button
                                        class="btn-cart cart-btn"
                                        data-id="<?= $p['id']; ?>"
                                        data-type="project">

                                        <i class="fa fa-shopping-cart"></i>

                                    </button>

                                    <a href="project-details.php?id=<?= $p['id']; ?>"
                                        class="btn-buy">
                                        Buy Now
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
    new Swiper(".mySwiper2", {

        slidesPerView: 4,
        spaceBetween: 25,
        loop: true,
        speed: 900,

        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },

        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },

        breakpoints: {

            0: {
                slidesPerView: 1.1,
                spaceBetween: 15,
            },

            576: {
                slidesPerView: 2,
                spaceBetween: 18,
            },

            768: {
                slidesPerView: 3,
                spaceBetween: 20,
            },

            1200: {
                slidesPerView: 4,
                spaceBetween: 25,
            }

        }

    });



</script>