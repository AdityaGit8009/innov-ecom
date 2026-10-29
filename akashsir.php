<?php include 'header.php' ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<?php
// ================= PAGE INFO =================
$page = [
    "title" => "LEAD ENGINEER <span class='pink'>PROFILE</span>",
    "breadcrumb" => "Lead Engineer"
];

// ================= PROFILE =================
$engineer = [
    "name"  => "Er. Akash Maurya",
    "image" => "./assets/images/WhatsApp_Image_2025-09-29_at_17.12.50_7a559ad6.jpg",
    "intro" => "
        <b style='font-size:20px;'>Er. Akash Maurya</b>, under the visionary leadership and
        strategic guidance of <b class='pink'>Er. Ajay Chaudhary, Director – Softpro Group</b>,
        is actively developing and leading innovative technology solutions through
        <b class='pink'>Softpro Innovations Pvt. Ltd.</b>.
        <br><br>
        His mentorship has played a pivotal role in shaping his professional discipline,
        technical excellence, leadership mindset, and long-term vision.
    "
];
// ================= SECTIONS =================
$recognition = "
    Akash Maurya, popularly known as the <b>“Robot Boy of Jaunpur”</b>, gained national
    recognition for developing an interactive humanoid robot during his academic journey.
    His innovation demonstrated real-time human–machine interaction and social relevance.
";

$highlights = "
    Er. Akash Maurya is a highly skilled <b class='pink'>Embedded Engineer at Softpro India</b>,
    with expertise in Embedded Systems, IoT, Robotics and Artificial Intelligence.
    He bridges academic research, industrial applications, and technical education.
";

$awards = [
    "20+ Awards in Science & Technology Exhibitions",
    "State-Level Winner – Science Exhibitions",
    "Exhibitions organized by Council of Science & Technology, Uttar Pradesh",
    "Widely recognized for robotics innovation"
];

$roles = [
    "Embedded Engineer – Softpro India",
    "Technical Mentor – Diploma & B.Tech Students",
    "Summer Training Instructor"
];

$research = "
    <b>Research & Innovation Areas:</b><br>
    • Internet of Things (IoT)<br>
    • Robotics (Humanoid & Multifunctional Robots)<br>
    • Artificial Intelligence (Vision-based Systems)
";

// ================= GALLERY =================
$gallery = [
    ["img" => "1.png", "name" => "Shri Ashutosh Tandon", "post" => "Honorable Technical Minister"],
    ["img" => "2.png", "name" => "Dr. V.K. Pathak", "post" => "VC - AKTU, Lucknow"],
    ["img" => "3.png", "name" => "Shri Bhuvanesh Kumar", "post" => "Secretary, Dept. of Technical Education"],
    ["img" => "4.png", "name" => "Prof S.P. Singh", "post" => "VC - LU, Lucknow"],
    ["img" => "5.png", "name" => "Dr. Nishith Rai", "post" => "VC - Dr. SMNRU, Lucknow"],
    ["img" => "6.png", "name" => "Prof. N B Singh", "post" => "VC - HBTU, Kanpur"],
    ["img" => "7.png", "name" => "Prof. S P Shukla", "post" => "Director - REC, Banda"],
    ["img" => "8.png", "name" => "Prof. Neelam Srivastava", "post" => "Director - REC, Kannauj"],
    ["img" => "9.png", "name" => "Prof. H K Paliwal", "post" => "Director - IET, Lucknow"],
    ["img" => "10.png", "name" => "Shri R C Rajput", "post" => "Director - BTEUP, Lucknow"],
    ["img" => "11.png", "name" => "Dr. V.K. Saraswat", "post" => "Member - Niti Aayog"],
    ["img" => "12.png", "name" => "Prof. V.K. Singh", "post" => "VC - DDU, Gorakhpur"],
    ["img" => "13.png", "name" => "Prof. J P Pandey", "post" => "Director - KNIT, Sultanpur"],
    ["img" => "14.png", "name" => "Dr. Kumar Keshav", "post" => "MD - Lucknow Metro"],
    ["img" => "15.png", "name" => "Prof. D K Singh", "post" => "Ex-Director, REC, Azamgarh"]
];
?>

<!-- ================= PREMIUM CSS ================= -->
<style>
    .main-content {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        margin: 30px auto;
        width: 95%;
    }

    .dir-sir-img img {
        border-radius: 20px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        float: left;
        margin-right: 30px;
        margin-bottom: 20px;
        max-width: 350px;
    }

    .professional {
        font-size: 28px;
        font-weight: 800;
        color: #1f2937;
        margin-top: 30px;
        margin-bottom: 15px;
        border-left: 5px solid #ef662f;
        padding-left: 15px;
    }

    .pink {
        color: #ef662f;
    }

    .gallery {
        padding: 20px 30px;
        margin: 0 auto;
        width: 95%;
    }

    .gallery-grid .g-item {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        background: white;
        border: 1px solid #f1f5f9;
    }

    .gallery-grid .g-item img {
        transition: transform 0.4s ease;
    }

    .gallery-grid .g-item:hover img {
        transform: scale(1.08);
    }

    @media(max-width: 768px) {
        .dir-sir-img img {
            float: none;
            display: block;
            margin: 0 auto 20px;
            width: 100%;
            max-width: 100%;
        }

        .main-content {
            padding: 20px;
        }
    }
</style>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> | <span><?= $page['breadcrumb']; ?></span>
    </p>
</section>

<!-- ================= PROFILE ================= -->
<div class="main-content" data-aos="fade-up" data-aos-duration="2000">
    <div class="dir-sir-img">
        <img src="<?= $engineer['image']; ?>" alt="<?= $engineer['name']; ?>">
        <p><?= $engineer['intro']; ?></p>
    </div>

    <h1 class="professional h5 pink">Public Recognition & Media Identity</h1>
    <p><?= $recognition; ?></p>

    <h1 class="professional">Professional <span class="pink">Highlights</span></h1>
    <p><?= $highlights; ?></p>

    <h1 class="pink professional">Awards & Recognitions</h1>
    <ul style="color: var(--theme-muted);">
        <?php foreach ($awards as $award): ?>
            <li><?= $award; ?></li>
        <?php endforeach; ?>
    </ul>

    <h1 class="professional">Professional <span class="pink">Role</span></h1>
    <ul style="color: var(--theme-muted);">
        <?php foreach ($roles as $role): ?>
            <li><?= $role; ?></li>
        <?php endforeach; ?>
    </ul>

    <p><?= $research; ?></p>

    <p>
        <b class="pink h3">Affiliation & Supervision</b><br>
        <b>Softpro Innovations Pvt. Ltd.</b><br>
        <i>Innovation | Technology | Training | Research | E-Commerce</i><br><br>
        <b>Under the guidance of:</b><br>
        Er. Ajay Chaudhary<br>
        Director – Softpro Group
    </p>
</div>

<!-- ================= GALLERY ================= -->
<div class="gallery">
    <h1 class="heading3 professional">LEAD ENGINEER <span class="pink">GALLERY</span></h1>

    <div class="gallery-grid">
        <?php foreach ($gallery as $g): ?>
            <div class="g-item" data-aos="fade-up" data-aos-duration="2000">
                <a href="./assets/images/pics/pics/<?= $g['img']; ?>" class="glightbox" data-gallery="lead-engineer-gallery"><img src="./assets/images/pics/pics/<?= $g['img']; ?>" alt="" style="cursor: zoom-in;"></a>
                <div class="img-body">
                    <h6><a href="about.php"><?= $g['name']; ?></a></h6>
                    <p><?= $g['post']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof GLightbox !== 'undefined') {
            GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true
            });
        }
    });
</script>
<?php include 'footer.php' ?>