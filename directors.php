<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<?php
// Breadcrumb
$page = [
    "title" => "DIRECTOR's <span class='pink'>PROFILE</span>",
    "breadcrumb" => "DIRECTOR PROFILE"
];

// Director Profile
$director = [
    "image" => "./assets/images/ajaysir.jpg",
    "name"  => "Er. Ajay Chaudhary",
    "intro" => "
        <b>Er. Ajay Chaudhary</b>, aged 42 years, graduated in Electronics from
        <b class='pink'>Institute of Engineering & Technology (IET), Lucknow</b>.
        He carries 17 years of experience as a Technical person, Network Professional,
        Manager and Techno consultant. He held key positions in
        <b class='pink'>SGPGI Lucknow, Apollo Group, Petronas Group (Malaysia & Singapore)</b>.<br><br>
        <b>Er. Chaudhary</b> started his professional journey in 1997 under the guidance
        of Dr. Bhandari at SGPGI, Lucknow and later joined Apollo Group.
    "
];

// Profile Paragraphs
$profileText = [
    "Dr. Bhandari came up with the idea to start India’s first online portal to impart
    education on medical science and proposed the same to
    <b class='pink'>Dr. P C Reddy - Chairman, Apollo Group</b>.
    This led to the foundation of Medvarsity Online Limited.",

    "Previously associated with Petronas (Malaysia), he laid the foundation of Softpro
    Group with its first company Softpro India in early 2004."
];

// Gallery Data
$gallery = [
    ["img"=>"sir7.jpg","name"=>"Shri Ashutosh Tandon","post"=>"Honorable Technical Minister"],
    ["img"=>"sir8.jpg","name"=>"Dr. V.K. Pathak","post"=>"VC - AKTU, Lucknow"],
    ["img"=>"sir14.jpg","name"=>"Shri Bhuvanesh Kumar","post"=>"Secretary, Department of Technical Education"],
    ["img"=>"sir4.jpg","name"=>"Prof S.P. Singh","post"=>"VC - LU, Lucknow"],
    ["img"=>"sir5.jpg","name"=>"Dr. Nishith Rai","post"=>"VC - Dr.SMNRU, Lucknow"],
    ["img"=>"sir16.jpg","name"=>"Prof. N B Singh","post"=>"VC - HBTU, Kanpur"],
    ["img"=>"sir12.jpg","name"=>"Prof. S P Shukla","post"=>"Director - REC, Banda"],
    ["img"=>"sir11.jpg","name"=>"Prof. Neelam Srivastava","post"=>"Director - REC, Kannauj"],
    ["img"=>"sir10.jpg","name"=>"Prof. H K Paliwal","post"=>"Director - IET, Lucknow"],
    ["img"=>"sir13.jpg","name"=>"Shri R C Rajput","post"=>"Director - BTEUP, Lucknow"],
    ["img"=>"sir6.jpg","name"=>"Dr. V.K. Saraswat","post"=>"Member - Niti Aayog"],
    ["img"=>"sir15.jpg","name"=>"Prof. V.K. Singh","post"=>"VC - DDU, Gorakhpur"],
    ["img"=>"sir9.jpg","name"=>"Prof. J P Pandey","post"=>"Director - KNIT, Sultanpur"],
    ["img"=>"sir17.jpg","name"=>"Dr. Kumar Keshav","post"=>"MD - Lucknow Metro, Lucknow"],
    ["img"=>"sir1.jpg","name"=>"Prof. D K Singh","post"=>"Ex-Director, REC, Azamgarh"]
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

<!-- ================= DIRECTOR PROFILE ================= -->
<div class="main-content" data-aos="fade-up" data-aos-duration="2000">
    <div class="dir-sir-img">
        <img src="<?= $director['image']; ?>" alt="Director">
        <p><?= $director['intro']; ?></p>
    </div>

    <?php foreach ($profileText as $text): ?>
        <p><?= $text; ?></p><br>
    <?php endforeach; ?>

    <h1 class="professional">Professional <span class="pink">Highlights</span></h1>
    <p>
        More than 05 years’ onsite experience in Tanzania, Kenya, South Africa,
        Singapore and Sri Lanka on Telecommunication & IT projects.
    </p>

    <h1 class="pink professional">Achievements</h1>
    <p>
        Founder member of Medvarsity Online Limited, implemented major HIS projects
        and initiated India’s first Telemedicine pilot project.
    </p>
</div>

<!-- ================= GALLERY ================= -->
<div class="gallery">
    <h1 class="heading3 professional">DIRECTOR'S <span class="pink">GALLERY</span></h1>

    <div class="gallery-grid">
        <?php foreach ($gallery as $item): ?>
            <div class="g-item" data-aos="fade-up" data-aos-duration="2000">
                <a href="./assets/images/<?= $item['img']; ?>" class="glightbox" data-gallery="director-gallery"><img src="./assets/images/<?= $item['img']; ?>" alt="" style="cursor: zoom-in;"></a>
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

