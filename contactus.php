<?php
include 'config/db.php';
require_once __DIR__ . '/includes/Mailer.php';

/* FORM SUBMIT */
if (isset($_POST['send_message'])) {

    $rawName    = trim($_POST['name'] ?? '');
    $rawEmail   = trim($_POST['email'] ?? '');
    $rawPhone   = trim($_POST['phone'] ?? '');
    $rawSubject = trim($_POST['subject'] ?? '');
    $rawMessage = trim($_POST['message'] ?? '');

    if ($rawName === '' || !filter_var($rawEmail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', preg_replace('/\D/', '', $rawPhone)) || $rawSubject === '' || strlen($rawMessage) < 5) {
        header("Location: contactus.php?error=1");
        exit;
    }

    $name    = mysqli_real_escape_string($conn, $rawName);
    $email   = mysqli_real_escape_string($conn, $rawEmail);
    $phone   = mysqli_real_escape_string($conn, $rawPhone);
    $subject = mysqli_real_escape_string($conn, $rawSubject);
    $message = mysqli_real_escape_string($conn, $rawMessage);

    mysqli_query(
        $conn,
        "INSERT INTO contact_messages (name,email,phone,subject,message)
         VALUES ('$name','$email','$phone','$subject','$message')"
    );

    $mailer = new SipMailer();

    // Admin ko enquiry email
    $mailer->send(
        $mailer->adminEmail(),
        "New User Enquiry - " . $rawSubject,
        "<p>A new enquiry has been submitted from the website.</p>"
        . "<p><b>Name:</b> " . htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8') . "<br>"
        . "<b>Email:</b> " . htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8') . "<br>"
        . "<b>Phone:</b> " . htmlspecialchars($rawPhone, ENT_QUOTES, 'UTF-8') . "<br>"
        . "<b>Subject:</b> " . htmlspecialchars($rawSubject, ENT_QUOTES, 'UTF-8') . "</p>"
        . "<p><b>Message:</b><br>" . nl2br(htmlspecialchars($rawMessage, ENT_QUOTES, 'UTF-8')) . "</p>",
        $rawEmail
    );

    // User ko acknowledgement email
    $mailer->send(
        $rawEmail,
        "We Received Your Enquiry",
        "<p>Hello <b>" . htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8') . "</b>,</p>"
        . "<p>Thank you for contacting Softpro Innovations. We have received your enquiry and our team will contact you soon.</p>"
        . "<p><b>Your Subject:</b> " . htmlspecialchars($rawSubject, ENT_QUOTES, 'UTF-8') . "</p>"
    );

    header("Location: contactus.php?sent=1");
    exit;
}

include 'header.php';
?>
<style>
@media (max-width: 768px) {
    .contact-container {
        flex-direction: column !important;
        display: flex;
        gap: 30px;
    }
    .contact-form, .contact-info-custom {
        width: 100% !important;
        flex: 1 1 auto !important;
    }
    .small-card-row {
        flex-direction: column !important;
    }
    .info-card.big-card {
        text-align: center;
    }
}
</style>
<?php
$msg = "";

if (isset($_GET['sent'])) {
    $msg = "<p style='color:green'>Message sent successfully!</p>";
}
if (isset($_GET['error'])) {
    $msg = "<p style='color:#ef662f'>Please enter valid details and try again.</p>";
}
?>


<?php
/* ================= PAGE DATA ================= */
$page = [
    "title" => "Contact <span class='pink'>US</span>",
    "breadcrumb" => "Contact US"
];

/* ================= HELPLINE NUMBERS ================= */
$trainingHelpline = [
    "+91 7080462022",
    "+91 8707463063",
    "+91 70801 02006"
];

$technicalHelpline = "+91 70801 02008";

/* ================= OFFICES ================= */
$offices = [
    [
        "title" => "HEAD OFFICE",
        "icon" => "fa-location-dot",
        "address" => "Softpro Tower, Near New Hanuman Temple,
                      Kapoorthala, Aliganj, Lucknow - 226006",
        "phone" => "+91 70801 02007"
    ],
    [
        "title" => "BRANCH OFFICE",
        "icon" => "fa-building",
        "address" => "Softpro House<br>
                      3/213, Sec-J, Jankipuram, Kursi Road<br>
                      Near Gudamba Police Station<br>
                      Lucknow - 226021",
        "phone" => "+91 70804 62022"
    ],
    [
        "title" => "FULL STACK ACADEMY",
        "icon" => "fa-school",
        "address" => "Softpro India Full Stack Academy<br>
                      1/6, Vastu Khand,<br>
                      Gomti Nagar, Lucknow - 226010",
        "phone" => "+91 70804 22022"
    ],
    [
        "title" => "CORPORATE OFFICE (NOIDA)",
        "icon" => "fa-city",
        "address" => "Softpro India Computer Technologies Pvt. Ltd.<br>
                      Creatons Business Park, Ground Floor<br>
                      H-35, Sec 63, Noida, Gautam Buddha Nagar,<br>
                      Uttar Pradesh - 201301",
        "phone" => "+91 70801 02006"
    ]
];

/* ================= SOCIAL LINKS ================= */
$socialLinks = [
    "facebook" => "#",
    "instagram" => "#",
    "linkedin" => "#",
    "twitter" => "#"
];
?>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> <span>| <?= $page['breadcrumb']; ?></span>
    </p>
</section>

<!-- ================= CONTACT SECTION ================= -->
<section class="contact-section">
    <div class="contact-container">

        <!-- LEFT FORM -->
        <div class="contact-form">
            <form method="post" action="">
                <h1 class="heading4">Get in Touch</h1>

                <div class="input-box">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>

                <div class="input-box">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="input-box">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Enter your mobile number" required>
                </div>

                <div class="input-box">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Enter your Subject" required>
                </div>

                <div class="input-box">
                    <label>Your Message</label>
                    <textarea rows="5" name="message" placeholder="Type your message..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="send-btn">
                    Send Message
                </button>
                <?= $msg; ?>
            </form>

            <!-- HELPLINES -->
            <div class="small-card-row">

                <div class="info-card small-card">
                    <i class="fa-solid fa-phone-volume icon fa-shake"></i>
                    <h4>TRAINING HELPLINE</h4>
                    <p>
                        <?php foreach ($trainingHelpline as $number): ?>
                            <?= $number; ?><br>
                        <?php endforeach; ?>
                    </p>
                </div>

                <div class="info-card small-card">
                    <i class="fa-solid fa-user-gear icon"></i>
                    <h4>TECHNICAL HELPLINE</h4>
                    <p><?= $technicalHelpline; ?></p>
                </div>

            </div>
        </div>

        <!-- RIGHT INFO -->
        <div class="contact-info-custom">

            <?php foreach ($offices as $office): ?>
                <div class="info-card big-card">
                    <i class="fa-solid fa-fade <?= $office['icon']; ?> icon"></i>
                    <h3><?= $office['title']; ?></h3>
                    <p><?= $office['address']; ?></p>
                    <p><strong>Phone:</strong> <?= $office['phone']; ?></p>
                </div>
            <?php endforeach; ?>

            <!-- SOCIAL -->
            <div class="info-card" style="padding:20px;">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <?php foreach ($socialLinks as $platform => $link): ?>
                        <a href="<?= $link; ?>">
                            <i class="fa-brands fa-<?= $platform; ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= MAP ================= -->

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d444.830901684302!2d80.94921290874483!3d26.882947441499567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399bfdf6aed70b65%3A0xa7228a71571c6820!2sSoftpro%20India!5e0!3m2!1sen!2sin!4v1766583919870!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
<?php include 'footer.php' ?>