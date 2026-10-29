<?php include 'header.php'; ?>
<?php
$pageTitle = "Terms & Conditions - Softpro Innovations";
$pageDescription = "Terms and conditions for purchases, services and use of Softpro Innovations website.";

$page = [
    "title" => "TERMS & <span class='pink'>CONDITIONS</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "Terms & Conditions"
    ]
];
?>

<style>
    .pink { color: #ef662f; font-weight: 800; }
    .policy-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: clamp(22px, 4vw, 38px); box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05); margin-bottom: 40px; transition: 0.3s ease; }
    .policy-card:hover { box-shadow: 0 25px 50px rgba(239, 102, 47, 0.08); border-color: #fdba74; }
    .policy-card h2 { color: #243447; font-size: clamp(24px, 3vw, 34px); font-weight: 900; margin: 0 0 10px; letter-spacing: .5px; }
    .policy-date { color: #667085; font-size: 13px; margin-bottom: 24px; }
    .policy-card p, .policy-card li { color: #2f3a45; font-size: 15px; line-height: 1.85; }
    .policy-card ul { padding-left: 22px; margin: 14px 0 26px; }
    .policy-section-title { background: #ff963b; color: #17202a; border-left: 5px solid #243447; border-radius: 7px; padding: 16px 20px; margin: 28px 0 14px; font-size: 18px; font-weight: 800; letter-spacing: .7px; }
    .policy-note { margin-top: 24px; padding: 16px 18px; border-radius: 12px; background: rgba(239, 102, 47, .08); color: #344054; border: 1px solid rgba(239, 102, 47, .16); }
    @media (max-width: 575px) { .policy-card { border-radius: 16px; padding: 22px 16px; } .policy-section-title { font-size: 16px; padding: 13px 15px; } }
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
        <h2>Terms & Conditions</h2>
        <p class="policy-date">Last updated: May 26, 2026</p>
        <p>By using the Softpro Innovations website, creating an account, placing an order or requesting any project/training support, you agree to the following terms and conditions.</p>

        <div class="policy-section-title">1. Account & User Responsibility</div>
        <ul>
            <li>You must provide correct name, email, mobile number and address details.</li>
            <li>You are responsible for maintaining the confidentiality of your account login details.</li>
            <li>Any activity performed through your account will be treated as your responsibility.</li>
        </ul>

        <div class="policy-section-title">2. Product Information & Availability</div>
        <ul>
            <li>Product images are for representation. Actual product color, model, packaging or minor design may vary.</li>
            <li>Stock availability may change depending on warehouse, supplier or bulk order conditions.</li>
            <li>Softpro Innovations may update product price, description, offer or stock status anytime.</li>
        </ul>

        <div class="policy-section-title">3. Orders, Payments & Invoice</div>
        <ul>
            <li>Orders are confirmed only after successful payment or approved order confirmation.</li>
            <li>Payment must be completed through available payment methods shown at checkout.</li>
            <li>Invoice will be generated according to the billing details provided by the customer.</li>
            <li>Wrong address, incomplete contact details or payment failure may delay order processing.</li>
        </ul>

        <div class="policy-section-title">4. Delivery & Project Support</div>
        <ul>
            <li>Delivery time may vary depending on location, courier service, stock and public holidays.</li>
            <li>Project kits, components and service support are provided as per selected product/service details.</li>
            <li>Technical support is limited to the scope mentioned with the product, training, internship or project service.</li>
        </ul>

        <div class="policy-section-title">5. Cancellation, Return & Refund</div>
        <ul>
            <li>Cancellation and refund will be handled according to the Cancellation Policy and Refund Policy pages.</li>
            <li>Used, damaged, burnt, soldered, physically broken or tampered electronics components may not be eligible for return/refund.</li>
            <li>Approved refunds will be processed to the original payment mode whenever possible.</li>
        </ul>

        <div class="policy-section-title">6. Company Rights</div>
        <ul>
            <li>Softpro Innovations may modify website content, pricing, services, policies and product listings without prior notice.</li>
            <li>Misuse of website, fake orders, fraudulent payment activity or abusive behavior may result in order cancellation or account restriction.</li>
        </ul>

        <p class="policy-note">For questions regarding these terms, contact <strong>info@softproindia.in</strong>.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
