<?php include 'header.php'; ?>
<?php
$pageTitle = "Site Map - Softpro Innovations";
$pageDescription = "Important pages and quick navigation links for Softpro Innovations website.";

$page = [
    "title" => "SITE <span class='pink'>MAP</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "Site Map"
    ]
];
?>

<style>
    .pink { color: #ef662f; font-weight: 800; }

    .site-map-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: clamp(22px, 4vw, 38px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        margin-bottom: 40px;
        transition: 0.3s ease;
    }

    .site-map-card:hover {
        box-shadow: 0 25px 50px rgba(239, 102, 47, 0.08);
        border-color: #fdba74;
    }

    .site-map-card h2 {
        color: #243447;
        font-size: clamp(24px, 3vw, 34px);
        font-weight: 900;
        margin: 0 0 10px;
        letter-spacing: .5px;
    }

    .policy-date {
        color: #667085;
        font-size: 13px;
        margin-bottom: 24px;
    }

    .site-map-card p,
    .site-map-card li {
        color: #2f3a45;
        font-size: 15px;
        line-height: 1.85;
    }

    .site-map-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
        margin-top: 24px;
    }

    .site-map-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 22px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .07);
        border: 1px solid #eef0f3;
        transition: all .3s ease;
    }

    .site-map-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 38px rgba(239, 102, 47, .10);
        border-color: #fdba74;
    }

    .site-map-box h3 {
        color: #17202a;
        font-size: 18px;
        font-weight: 900;
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #ef662f;
    }

    .site-map-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .site-map-box li {
        line-height: 1.55;
        margin-bottom: 10px;
    }

    .site-map-box a {
        color: #344054;
        text-decoration: none;
        transition: color .25s ease;
    }

    .site-map-box a:hover {
        color: #ef662f;
    }

    @media (max-width: 1199px) {
        .site-map-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 991px) {
        .site-map-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 575px) {
        .site-map-card {
            border-radius: 16px;
            padding: 22px 16px;
        }
        .site-map-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .site-map-box {
            padding: 20px 18px;
        }
    }
</style>

<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="<?= $page['breadcrumb']['Home']; ?>">Home</a> |
        <span><?= $page['breadcrumb']['current']; ?></span>
    </p>
</section>

<div class="main-content">
    <div class="site-map-card">
        <h2>Site Map</h2>
        <p class="policy-date">Last updated: May 26, 2026</p>
        <p>Use this page to quickly open important pages of Softpro Innovations website.</p>

        <div class="site-map-grid">

            <div class="site-map-box">
                <h3>Main Pages</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Softpro</a></li>
                    <li><a href="associations.php">Our Associations</a></li>
                    <li><a href="advisory-board.php">Advisory Board</a></li>
                    <li><a href="directors.php">Directors Profile</a></li>
                    <li><a href="contactus.php">Contact Us</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="site-map.php">Site Map</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Training</h3>
                <ul>
                    <li><a href="training.php">B.Tech Training</a></li>
                    <li><a href="training.php">B.Tech EC/EE Training</a></li>
                    <li><a href="training.php">Vocational Training</a></li>
                    <li><a href="training.php">Diploma EC/EE Training</a></li>
                    <li><a href="training.php">Industrial Training</a></li>
                    <li><a href="training.php">Apprenticeship Program</a></li>
                    <li><a href="training.php">Faculty Development Program</a></li>
                    <li><a href="coe.php">Center Of Excellence</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Programs</h3>
                <ul>
                    <li><a href="apprenticeship.php">Apprenticeship</a></li>
                    <li><a href="faculty-development-program.php">Faculty Development Program</a></li>
                    <li><a href="full-stack-certification-program.php">Full Stack Certification Program</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Technology</h3>
                <ul>
                    <li><a href="python-ml.php">Python ML</a></li>
                    <li><a href="python-data-science.php">Python Data Science</a></li>
                    <li><a href="python-django.php">Python Django</a></li>
                    <li><a href="php-laravel.php">PHP Laravel</a></li>
                    <li><a href="java-springboot.php">Java SpringBoot</a></li>
                    <li><a href="embedded.php">Embedded Systems</a></li>
                    <li><a href="arduino.php">Arduino</a></li>
                    <li><a href="pcb-designing.php">PCB Designing</a></li>
                    <li><a href="mern-stack.php">MERN Stack</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Softpro</h3>
                <ul>
                    <li><a href="awards.php">Awards</a></li>
                    <li><a href="newsletters.php">Newsletters</a></li>
                    <li><a href="certification-programs.php">Certification Programs</a></li>
                    <li><a href="recognitions.php">Recognitions</a></li>
                    <li><a href="recruiters.php">Recruiters</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="press.php">Press</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Products & Projects</h3>
                <ul>
                    <li><a href="products.php">All Products</a></li>
                    <li><a href="mini-projects.php">Mini Projects</a></li>
                    <li><a href="major-projects.php">Major Projects</a></li>
                    <li><a href="project.php">Project Support</a></li>
                    <li><a href="services.php">Services</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Customer</h3>
                <ul>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="compare.php">Compare</a></li>
                    <li><a href="user_profile.php?tab=latest">My Orders</a></li>
                    <li><a href="track_order.php">Track Order</a></li>
                </ul>
            </div>

            <div class="site-map-box">
                <h3>Policies</h3>
                <ul>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms-and-conditions.php">Terms & Conditions</a></li>
                    <li><a href="cancellation-policy.php">Cancellation Policy</a></li>
                    <li><a href="refund-policy.php">Refund Policy</a></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
