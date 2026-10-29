<?php include 'header.php'; ?>
<?php
$pageTitle = "Privacy & Data Protection Policy - Softpro Innovations";
$pageDescription = "Privacy and data protection policy for Softpro Innovations e-Commerce customers.";

$page = [
    "title" => "PRIVACY & DATA <span class='pink'>PROTECTION POLICY</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "Privacy Policy"
    ]
];
?>

<style>
    .pink { color: #ef662f; font-weight: 800; }

    .policy-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: clamp(22px, 4vw, 38px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        margin-bottom: 40px;
        transition: 0.3s ease;
    }

    .policy-card:hover {
        box-shadow: 0 25px 50px rgba(239, 102, 47, 0.08);
        border-color: #fdba74;
    }

    .policy-card h2 {
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

    .policy-card p,
    .policy-card li {
        color: #2f3a45;
        font-size: 15px;
        line-height: 1.85;
    }

    .policy-card ul {
        padding-left: 22px;
        margin: 14px 0 26px;
    }

    .policy-section-title {
        background: #ff963b;
        color: #17202a;
        border-left: 5px solid #243447;
        border-radius: 7px;
        padding: 16px 20px;
        margin: 28px 0 14px;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: .7px;
    }

    .policy-note {
        margin-top: 24px;
        padding: 16px 18px;
        border-radius: 12px;
        background: rgba(239, 102, 47, .08);
        color: #344054;
        border: 1px solid rgba(239, 102, 47, .16);
    }

    @media (max-width: 575px) {
        .policy-card {
            border-radius: 16px;
            padding: 22px 16px;
        }

        .policy-section-title {
            font-size: 16px;
            padding: 13px 15px;
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
    <div class="policy-card">
        <h2>Privacy & Data Protection Policy</h2>
        <p class="policy-date">Last updated: May 26, 2026</p>
        <p>Softpro Innovations collects only the information required to provide electronics products, project support, training support, order delivery, payment processing and customer service. We use reasonable administrative and technical safeguards to protect your personal data.</p>

        <div class="policy-section-title">1. Information We Collect</div>
        <ul>
            <li>Name, email address, mobile number, billing address and shipping address.</li>
            <li>Order details, cart details, wishlist details, payment status and invoice information.</li>
            <li>Account login details required for authentication and order tracking.</li>
            <li>Messages submitted through enquiry, contact or support forms.</li>
        </ul>

        <div class="policy-section-title">2. How We Use Data</div>
        <ul>
            <li>To create your account and manage your orders.</li>
            <li>To process payment, generate invoices and provide order updates.</li>
            <li>To deliver products, project kits and related services.</li>
            <li>To respond to support requests, complaints and product enquiries.</li>
            <li>To improve website security, user experience and service quality.</li>
        </ul>

        <div class="policy-section-title">3. Data Sharing</div>
        <ul>
            <li>We may share required order information with delivery partners, payment gateway providers and service teams only for completing your order or support request.</li>
            <li>We may disclose information when required by law, legal process or government authority.</li>
            <li>We do not sell your personal data to third parties.</li>
        </ul>

        <div class="policy-section-title">4. Data Retention & Accuracy</div>
        <ul>
            <li>Order, invoice, payment and account records may be maintained for business, audit, tax and legal compliance.</li>
            <li>You are responsible for keeping your profile, address and contact details accurate and updated.</li>
        </ul>

        <div class="policy-section-title">5. Cookies & Website Usage</div>
        <ul>
            <li>Cookies may be used for login sessions, cart management, recently viewed products and website performance.</li>
            <li>You may disable cookies in your browser, but some website features may not work properly.</li>
        </ul>

        <div class="policy-section-title">6. Contact</div>
        <p>For privacy-related questions, email us at <strong>info@softproindia.in</strong> or contact us through the website support page.</p>
        <p class="policy-note">Softpro Innovations may update this policy when required. Updated policy content will be applicable from the date it is published on the website.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
