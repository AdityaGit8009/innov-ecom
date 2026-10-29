<?php include 'header.php'; ?>
<?php
// Breadcrumb
$page = [
    "title" => "ABOUT <span class='pink'>US</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "About Us"
    ]
];
// Company Section
$company = [
    "heading" => "THE COMPANY",
    "image" => "./assets/images/softpro.jpg",
    "content" => [
        "Softpro Innovations Pvt Ltd , is a privately held company located in Lucknow, India.
        Softpro Innovations Pvt Ltd is a Design and manufacturing company based in fields of
        Robotics & Embedded Systems, Industrial Automation & Home Building, Telecommunication &
        Networking, Energy Consulting & Power Electronics. Softpro Innovations Pvt Ltd undertakes
        breakthrough internationally recognized research in partnership with industry, government
        and researchers to create value for India. Seamlessly integrating precision and ingenuity,
        Softpro’s abilities stem from our creative leadership in hard-core technology and strength
        in design. Augmenting these capabilities is our expertise across our practice areas to
        provide point services and end-to-end solutions across the product lifecycle.",

        "We provide customized design solutions to companies across the globe. We ensure
        cost-effective, time-to-market solutions through a highly motivated skilled workforce
        driven by strong design principles, highest levels of quality and ethical business practices.",

        "Touching people’s lives in myriad ways, Softpro Innovations Pvt Ltd can impact directly
        or subtly, leaving a mark of excellence in its wake."
    ]
];

// Mission Section
$mission = [
    "heading" => "OUR MISSION",
    "image" => "./assets/images/mission2.jpg",
    "content" => [
        "Softpro Innovations aims to create world-class and innovative products that empower the global community.",
        "We specialize in industrial automation and software development—from software-only projects to fully integrated hardware and software solutions.",
        "Our industry experts optimize business processes and design scalable, high-performance systems.",
        "Our priority is to deliver superior quality in our products and services."
    ]
];

// Core Values
$coreValues = [
    "Integrity" => "Honesty in how we deal with our clients, each other and with the world.",
    "Candor" => "Be open and upfront in all our conversations. Keep clients updated on the real situation.",
    "Service" => "Seek to empower and enable our clients and ensure success after launch.",
    "Kindness" => "Go the extra mile. Speak the truth with grace.",
    "Competence" => "Benchmark with the best and keep innovating.",
    "Growth" => "Success is a journey, not a destination."
];
?>

<style>
    /* ================= PREMIUM UI FOR ABOUT PAGE ================= */

    
    .premium-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        margin-bottom: 40px;
        transition: 0.3s ease;
    }
    .premium-card:hover {
        box-shadow: 0 25px 50px rgba(239, 102, 47, 0.08);
        border-color: #fdba74;
    }
    .pink { color: #ef662f; font-weight: 800; }
    
    /* ================= ONLY MISSION + CORE VALUE FIX ================= */

    /* ================= ONLY MISSION FIX ================= */

    .mission {
        display: grid !important;
        grid-template-columns: 365px 1fr !important;
        gap: 20px !important;
        align-items: center !important;
        margin-bottom: 28px !important;
    }

    .mission .image {
        width: 365px !important;
    }

    .mission .image img {
        width: 365px !important;
        height: 218px !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }

    .mission .content {
        width: 100% !important;
    }

    .mission .content .fline {
        margin: 0 0 5px 0 !important;
        font-size: 17px !important;
        line-height: 1.45 !important;
        text-align: justify !important;
    }

    /* Tablet */
    @media (max-width: 991px) {
        .mission {
            grid-template-columns: 320px 1fr !important;
            gap: 18px !important;
        }

        .mission .image,
        .mission .image img {
            width: 320px !important;
        }

        .mission .image img {
            height: 205px !important;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        .mission {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }

        .mission .image {
            width: 100% !important;
        }

        .mission .image img {
            width: 100% !important;
            height: auto !important;
            max-height: none !important;
            object-fit: contain !important;
        }

        .mission .content .fline {
            font-size: 15px !important;
            text-align: left !important;
        }
    }

    /* Core Values Layout */
    .core-value {
        display: grid !important;
        grid-template-columns: 1fr 400px !important;
        gap: 28px !important;
        align-items: start !important;
        margin-bottom: 35px !important;
    }

    .core-value .content {
        width: 100% !important;
        padding-top: 8px !important;
    }

    .core-value .image {
        width: 400px !important;
    }

    .core-value .image img {
        width: 400px !important;
        height: 300px !important;
        object-fit: contain !important;
        display: block !important;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }

    .core-value ul {
        margin: 0 !important;
        padding-left: 20px !important;
    }

    .core-value ul li {
        margin-bottom: 8px !important;
        font-size: 16px !important;
        line-height: 1.45 !important;
        text-align: justify !important;
    }

    .core-value ul li b {
        color: #f15a24 !important;
    }

    /* Heading gap fix */
    .main-content h2.pink.mt-3 {
        margin-top: 22px !important;
        margin-bottom: 12px !important;
    }

    /* Tablet */
    @media (max-width: 991px) {
        .mission {
            grid-template-columns: 320px 1fr !important;
            gap: 18px !important;
        }

        .mission .image,
        .mission .image img {
            width: 320px !important;
        }

        .mission .image img {
            height: 260px !important;
        }

        .core-value {
            grid-template-columns: 1fr 330px !important;
            gap: 18px !important;
        }

        .core-value .image,
        .core-value .image img {
            width: 330px !important;
        }

        .core-value .image img {
            height: 260px !important;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {

        .mission,
        .core-value {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }

        .mission .image,
        .core-value .image {
            width: 100% !important;
        }

        .mission .image img,
        .core-value .image img {
            width: 100% !important;
            height: auto !important;
            max-height: none !important;
        }

        .mission .content .fline,
        .core-value ul li {
            font-size: 15px !important;
            text-align: left !important;
        }
    }
</style>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="<?= $page['breadcrumb']['Home']; ?>">Home</a> |
        <span><?= $page['breadcrumb']['current']; ?></span>
    </p>
</section>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">

    <!-- COMPANY -->
    <h2 class="pink"><?= $company['heading']; ?></h2>
    <div class="company premium-card">
        <div class="content">
            <?php foreach ($company['content'] as $para): ?>
                <p class="fline"><?= $para; ?></p>
            <?php endforeach; ?>
        </div>
        <div class="image">
            <img src="<?= $company['image']; ?>" alt="Company Image" style="cursor: zoom-in; border-radius: 16px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
        </div>
    </div>

    <!-- MISSION -->
    <h2 class="pink mt-3"><?= $mission['heading']; ?></h2>
    <div class="mission premium-card">
        <div class="image" data-aos="fade-up">
            <img src="<?= $mission['image']; ?>" alt="Mission" style="cursor: zoom-in;">
        </div>

        <div class="content" data-aos="fade-down">
            <?php foreach ($mission['content'] as $para): ?>
                <p class="fline"><?= $para; ?></p>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CORE VALUES -->
    <h2 class="pink mt-3">Core Values</h2>
    <div class="core-value premium-card">
        <div class="content" data-aos="fade-down">
            <ul>
                <?php foreach ($coreValues as $title => $desc): ?>
                    <li><b><?= $title; ?></b> - <?= $desc; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="image" data-aos="fade-up">
            <img src="./assets/images/corevalue.jpg" alt="Core Values" style="cursor: zoom-in;">
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>