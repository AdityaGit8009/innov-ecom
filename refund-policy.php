<?php include 'header.php'; ?>
<?php
$pageTitle = "Refund Policy - Softpro Innovations";
$pageDescription = "Refund policy for orders, services and project kits of Softpro Innovations.";

$page = [
    "title" => "REFUND <span class='pink'>POLICY</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "Refund Policy"
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
        <h2>Refund Policy</h2>
        <p class="policy-date">Last updated: May 26, 2026</p>
        <p>This Refund Policy explains when refund may be considered for electronics products, project kits, services and training/project support purchased from Softpro Innovations.</p>

        <div class="policy-section-title">1. Eligibility for Refund</div>
        <ul>
            <li>Refund may be considered if an eligible order is cancelled before dispatch.</li>
            <li>Refund may be considered if payment was deducted but order was not generated due to technical/payment gateway issue.</li>
            <li>Refund may be considered for wrong product delivery or verified non-working product received in sealed/unused condition.</li>
        </ul>

        <div class="policy-section-title">2. Non-Refundable Cases</div>
        <ul>
            <li>Products damaged due to wrong wiring, reverse polarity, over-voltage, burning, soldering or physical mishandling.</li>
            <li>Used components, opened kits, customized kits, programmed boards and assembled project models.</li>
            <li>Software/source code/digital material already shared with the customer.</li>
            <li>Training, internship, project guidance or service work already started or completed.</li>
            <li>Voluntary cancellation after dispatch or after delivery without valid issue.</li>
        </ul>

        <div class="policy-section-title">3. Verification Process</div>
        <ul>
            <li>Customer must share order ID, product images/video and issue details for verification.</li>
            <li>Softpro Innovations may ask for product return inspection before approving refund.</li>
            <li>Refund approval will depend on inspection and order records.</li>
        </ul>

        <div class="policy-section-title">4. Refund Deductions</div>
        <ul>
            <li>Shipping, packaging, payment gateway or service charges may be deducted where applicable.</li>
            <li>Any missing accessory, damaged package or partial return may reduce the refund amount.</li>
        </ul>

        <div class="policy-section-title">5. Refund Processing</div>
        <ul>
            <li>Approved refunds will be processed to the original payment mode whenever possible.</li>
            <li>Processing time may depend on bank, payment gateway or internal verification process.</li>
            <li>Refund status updates will be shared through registered email/mobile or support communication.</li>
        </ul>

        <div class="policy-section-title">6. Contact for Refund Request</div>
        <p>Submit refund requests with order ID and issue proof at <strong>info@softproindia.in</strong>.</p>
        <p class="policy-note">Softpro Innovations reserves the right to approve, reject or partially approve refund requests after verification.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
