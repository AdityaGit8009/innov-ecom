<?php include 'header.php'; ?>
<?php
$pageTitle = "Cancellation Policy - Softpro Innovations";
$pageDescription = "Cancellation policy for orders and services of Softpro Innovations.";

$page = [
    "title" => "CANCELLATION <span class='pink'>POLICY</span>",
    "breadcrumb" => [
        "Home" => "index.php",
        "current" => "Cancellation Policy"
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
        <h2>Cancellation Policy</h2>
        <p class="policy-date">Last updated: May 26, 2026</p>
        <p>Cancellation of orders, project kits, components or service requests is subject to the following rules.</p>

        <div class="policy-section-title">1. Order Cancellation Before Dispatch</div>
        <ul>
            <li>You may request cancellation before the order is packed or dispatched.</li>
            <li>If the order has not been processed, the cancellation request may be approved.</li>
            <li>After cancellation approval, refund will be handled as per the Refund Policy.</li>
        </ul>

        <div class="policy-section-title">2. Cancellation After Dispatch</div>
        <ul>
            <li>Orders already shipped or handed over to courier cannot be cancelled from our side.</li>
            <li>You may refuse delivery only when permitted by the courier process and after informing support.</li>
            <li>Shipping, handling, gateway or packaging charges may be deducted where applicable.</li>
        </ul>

        <div class="policy-section-title">3. Non-Cancellable Items</div>
        <ul>
            <li>Customized project kits, assembled kits, programmed boards and specially arranged components.</li>
            <li>Bulk order items purchased specifically on customer request.</li>
            <li>Training, internship, project guidance or service work already started.</li>
            <li>Digital files, source code, documentation or downloadable project material already delivered.</li>
        </ul>

        <div class="policy-section-title">4. Cancellation by Softpro Innovations</div>
        <ul>
            <li>We may cancel an order due to stock unavailability, pricing error, payment failure, delivery location issue or suspicious/fraudulent activity.</li>
            <li>If payment was received for a cancelled eligible order, refund will be processed as per the Refund Policy.</li>
        </ul>

        <div class="policy-section-title">5. How to Request Cancellation</div>
        <p>Send your cancellation request with order ID, registered mobile number and reason to <strong>info@softproindia.in</strong> or contact the support team from the website.</p>
        <p class="policy-note">Cancellation approval depends on current order status. Management decision will be final in disputed cases.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
