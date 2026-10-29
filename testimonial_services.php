<?php
// Testimonial data
$testimonial = [
    "name" => "Aditya Pratap",
    "image" => "./assets/images/office-man.webp",
    "message" => "Best Website For Electronics Project & Electronics Components"
];

// CTA data
$cta = [
    "image" => "./assets/images/IoT KIT/WhatsApp Image 2025-01-01 at 18.32.37_4a1836c4.jpg",
    "discount" => "25% Discount",
    "title" => "Softpro IoT Trainer Kit",
    "price" => "Starting @ ₹30,000",
    "button" => "Buy now"
];

// Services data
$services = [
    [
        "icon" => "boat-outline",
        "title" => "All Over Uttar Pradesh",
        "desc" => "For Order Over 1000"
    ],
    [
        "icon" => "rocket-outline",
        "title" => "delivery in Four Days",
        "desc" => "UP Orders Only"
    ],
    [
        "icon" => "call-outline",
        "title" => "Best Online Support",
        "desc" => "Hours: 10:00 AM - 07:00 PM"
    ],
    [
        "icon" => "arrow-undo-outline",
        "title" => "Return Policy",
        "desc" => "Easy & Free Return"
    ],
    [
        "icon" => "ticket-outline",
        "title" => "30% money back",
        "desc" => "For Order Over 5000"
    ]
];
?>

<div>
    <div class="container">
        <div class="testimonials-box">

            <!-- TESTIMONIAL -->
            <div class="testimonial">
                <h2 class="title">testimonial</h2>

                <div class="testimonial-card">
                    <img src="<?php echo $testimonial['image']; ?>"
                         alt="users photo"
                         class="testimonial-banner"
                         width="80" height="80" />

                    <p class="testimonial-name">
                        <?php echo $testimonial['name']; ?>
                    </p>

                    <img src="./assets/images/icons/quotes.svg"
                         alt="quotation"
                         class="quotation-img"
                         width="26" />

                    <p class="testimonial-desc">
                        <?php echo $testimonial['message']; ?>
                    </p>
                </div>
            </div>

            <!-- CTA -->
            <div class="cta-container">
                <img src="<?php echo $cta['image']; ?>"
                     alt="summer collection"
                     class="cta-banner" />

                <a href="services.php" class="cta-content">
                    <p class="discount"><?php echo $cta['discount']; ?></p>
                    <h2 class="cta-title"><?php echo $cta['title']; ?></h2>
                    <p class="cta-text"><?php echo $cta['price']; ?></p>
                    <button class="cta-btn"><?php echo $cta['button']; ?></button>
                </a>
            </div>

            <!-- SERVICES -->
            <div class="service">
                <h2 class="title">Our Services</h2>

                <div class="service-container">
                    <?php foreach ($services as $service) { ?>
                        <a href="services.php" class="service-item">
                            <div class="service-icon">
                                <ion-icon name="<?php echo $service['icon']; ?>"></ion-icon>
                            </div>

                            <div class="service-content">
                                <h3 class="service-title">
                                    <?php echo $service['title']; ?>
                                </h3>
                                <p class="service-desc">
                                    <?php echo $service['desc']; ?>
                                </p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>
