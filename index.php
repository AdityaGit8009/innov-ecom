    <?php include 'header.php'; ?>

    <style>
        .modal.closed {
            display: none !important;
        }

        @media (min-width: 769px) and (max-width: 1023px) {

            /* Reduce large gap under video for category/product area on medium screens */
            .product-container {
                margin-top: 6px !important;
                padding-top: 0 !important;
            }

            .product-container .container {
                padding-top: 0 !important;
            }

            /* Keep product gallery and blocks more compact (if present on home) */
            .product-gallery-wrap {
                gap: 6px !important;
            }

            .productThumbSwiper {
                height: 220px !important;
            }

            .productThumbSwiper .swiper-slide {
                height: 50px !important;
            }

            .thumb-wrapper {
                height: 52px;
            }

            .product-main-image {
                height: 220px !important;
            }

            .product-main-image,
            .productThumbSwiper {
                transition: height .18s ease;
            }

            .product-gallery-sticky,
            .purchase-box {
                top: 50px !important;
            }

            .product-info-card,
            .product-extra-details {
                padding: 8px !important;
            }

            .product-title {
                font-size: 20px;
            }
        }
    </style>

    <!-- MODAL -->
    <div class="modal closed" data-modal>

        <div class="modal-close-overlay" data-modal-overlay></div>

        <div class="modal-content">

            <button class="modal-close-btn" data-modal-close>
                <ion-icon name="close-outline"></ion-icon>
            </button>

            <div class="newsletter-img">
                <img src="./assets/images/modal.png"
                    alt="director Sir Message"
                    width="650"
                    height="340" />
            </div>

        </div>

    </div>

    <main>

        <?php include 'video.php'; ?>

        <div class="product-container">

            <div class="container">

                <?php include 'column-left.php'; ?>

                <?php include 'column-right.php'; ?>

            </div>

        </div>

        <?php include 'recently_viewed.php'; ?>

        <?php include 'testimonial_services.php'; ?>

        <?php include 'project.php'; ?>

    </main>

    <?php include 'footer.php'; ?>