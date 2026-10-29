<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<?php
// ================= PAGE INFO =================
$page = [
    "title" => "CEO <span class='pink'>PROFILE</span>",
    "breadcrumb" => "CEO PROFILE"
];

// ================= CEO PROFILE =================
$ceo = [
    "name"  => "Yashi Asthana",
    "image" => "./assets/images/ceomam.jpg",
    "about" => "
        <b style='font-size:20px;'>Yashi Asthana</b>, Chief Executive Officer - Softpro Group of
        Companies (the youngest CEO of Softpro Group so far), is working with the organization
        since past approx. 16 years with full devotion and dedication.
        She completed her higher education from Jiwaji University, Gwalior in Computer Science.
        She started her career with IT Giant <b>Wipro Technologies, Mysore</b> and later joined
        Softpro India.
        <br><br>
        She started her career as <b>Executive HR & Operations</b> and was promoted to
        <b>Assistant Manager – HR</b> within 06 months due to her hard work and dedication.
    "
];

// ================= EXTRA CONTENT =================
$ceoJourney = "
    The innovative ideas, creativity, leadership skills and strong will power in her
    personality have always been appreciated by the management resulting into tremendous
    growth in her career creating
    <b class='pink'>“A Journey from an HR – Executive to a CEO”</b>.
    She has been a keynote speaker and has delivered lectures on
    <b class='pink'>Entrepreneurship, Leadership Skills, Innovation & Employment in IT Sector</b>.
";

// ================= CEO MESSAGE =================
$ceoMessage = "
    Softpro India provides technical support in software development, IT staffing,
    education and R&D. Our focus is scalable and cost-effective IT solutions using global
    delivery models.
    <br><br>
    Clients who gain maximum value from us are those who utilize our entire portfolio of
    skills, experience and resources. We believe in teamwork and excellence.
    <b class='pink'>Softpro India has grown with every successful project delivered</b>.
    <br><br>
    I welcome suggestions, feedback and communication to strengthen our bond further.
";

// ================= GALLERY =================
$gallery = [
    ["img"=>"1.jpg","name"=>"Dr. Anil D Sahasrabudhe","post"=>"Chairman - AICTE"],
    ["img"=>"2.jpg","name"=>"Dr. V.K. Pathak","post"=>"VC - AKTU, Lucknow"],
    ["img"=>"3.jpg","name"=>"Dr. V.K. Saraswat","post"=>"Member - Niti Aayog"],
    ["img"=>"4.jpg","name"=>"Shri Ashutosh Tandon","post"=>"Honorable Technical Minister"],
    ["img"=>"5.jpg","name"=>"Alfred Vilili","post"=>"Counselor, Malawi Embassy"],
    ["img"=>"6.jpg","name"=>"F.M. Kidwai","post"=>"Honorable Former Technical Minister"],
    ["img"=>"y9.jpg","name"=>"Prosper C Mopiwa","post"=>"Dep. Director, E-Government Malawi"],
    ["img"=>"7.jpg","name"=>"Prof. D.S. Yadav","post"=>"Ex-Director REC, Banda"],
    ["img"=>"8.jpg","name"=>"Er. Dinesh Yadav","post"=>"Ex SP - Noida (GB Nagar)"]
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
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
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
.pink { color: #ef662f; }

.gallery { padding: 20px 30px; margin: 0 auto; width: 95%; }
.gallery-grid .g-item {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
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
    .dir-sir-img img { float: none; display: block; margin: 0 auto 20px; width: 100%; max-width: 100%; }
    .main-content { padding: 20px; }
}
</style>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> | <span><?= $page['breadcrumb']; ?></span>
    </p>
</section>

<!-- ================= CEO PROFILE ================= -->
<div class="main-content" data-aos="fade-up" data-aos-duration="2000">
    <div class="dir-sir-img">
        <img src="<?= $ceo['image']; ?>" alt="<?= $ceo['name']; ?>">
        <p><?= $ceo['about']; ?></p>
    </div>

    <p><?= $ceoJourney; ?></p>

    <h1 class="professional">CEO's <span class="pink">Message :</span></h1>
    <p><?= $ceoMessage; ?></p>
</div>

<!-- ================= CEO GALLERY ================= -->
<div class="gallery">
    <h1 class="heading3 professional">CEO'S <span class="pink">GALLERY</span></h1>

    <div class="gallery-grid">
        <?php foreach($gallery as $item): ?>
            <div class="g-item" data-aos="fade-up" data-aos-duration="2000">
                <a href="./assets/images/ceo/<?= $item['img']; ?>" class="glightbox" data-gallery="ceo-gallery"><img src="./assets/images/ceo/<?= $item['img']; ?>" alt="" style="cursor: zoom-in;"></a>
                <div class="img-body">
                    <h6><a href="about.php"><?= $item['name']; ?></a></h6>
                    <p><?= $item['post']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof GLightbox!=='undefined'){GLightbox({selector:'.glightbox',touchNavigation:true,loop:true});}});</script>
<?php include 'footer.php'; ?>

