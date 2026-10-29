<?php include 'header.php'; ?>
<?php
include 'config/db.php';

$slug = $_GET['slug'] ?? '';

if ($slug == '') {
  echo "<h3 class='text-center my-5'>Technology not found.</h3>";
  include 'footer.php';
  exit;
}

$slug = mysqli_real_escape_string($conn, $slug);

$techQ = mysqli_query($conn, "SELECT * FROM training_technologies 
    WHERE slug='$slug' AND status=1 
    LIMIT 1");

$tech = mysqli_fetch_assoc($techQ);

if (!$tech) {
  echo "<h3 class='text-center my-5'>Technology not found.</h3>";
  include 'footer.php';
  exit;
}

$technology_id = (int)$tech['id'];

$features = [];
$featureQ = mysqli_query($conn, "SELECT * FROM technology_features 
    WHERE technology_id='$technology_id' AND status=1 
    ORDER BY sort_order ASC, id ASC");

while ($row = mysqli_fetch_assoc($featureQ)) {
  $features[] = $row;
}

$companies = [];
$companyQ = mysqli_query($conn, "SELECT * FROM technology_companies 
    WHERE technology_id='$technology_id' AND status=1 
    ORDER BY sort_order ASC, id ASC");

while ($row = mysqli_fetch_assoc($companyQ)) {
  $companies[] = $row;
}

$phases = [];
$phaseQ = mysqli_query($conn, "SELECT * FROM technology_phases 
    WHERE technology_id='$technology_id' AND status=1 
    ORDER BY sort_order ASC, id ASC");

while ($phase = mysqli_fetch_assoc($phaseQ)) {
  $phase_id = (int)$phase['id'];

  $modules = [];
  $moduleQ = mysqli_query($conn, "SELECT * FROM technology_modules 
        WHERE phase_id='$phase_id' AND status=1 
        ORDER BY sort_order ASC, id ASC");

  while ($module = mysqli_fetch_assoc($moduleQ)) {
    $module_id = (int)$module['id'];

    $topics = [];
    $topicQ = mysqli_query($conn, "SELECT * FROM technology_topics 
            WHERE module_id='$module_id' AND status=1 
            ORDER BY sort_order ASC, id ASC");

    while ($topic = mysqli_fetch_assoc($topicQ)) {
      $topics[] = $topic;
    }

    $module['topics'] = $topics;
    $modules[] = $module;
  }

  $phase['modules'] = $modules;
  $phases[] = $phase;
}

$detailTitle = !empty($tech['detail_title']) ? $tech['detail_title'] : $tech['title'];
$detailImage = !empty($tech['detail_image']) ? $tech['detail_image'] : $tech['image'];
$detailDescription = !empty($tech['detail_description']) ? $tech['detail_description'] : ($tech['short_text'] ?? '');
?>

<style>
:root{
  --td-orange:#f05a28;
  --td-orange2:#ff7a1a;
  --td-dark:#18181b;
  --td-text:#30343b;
  --td-muted:#6b7280;
  --td-border:#eceff3;
  --td-soft:#fff7f2;
}

.tech-detail-page{
  background:linear-gradient(180deg,#fff 0%,#fff8f3 46%,#fff 100%);
  padding-bottom:55px;
}

.tech-detail-container{
  width:min(1180px,92%);
  margin:auto;
}

.tech-detail-hero{
  display:grid;
  grid-template-columns:minmax(280px,540px) 1fr;
  gap:30px;
  align-items:stretch;
  margin:55px auto 42px;
  background:#fff;
  border:1px solid var(--td-border);
  border-radius:28px;
  padding:28px;
  box-shadow:0 20px 50px rgba(15,23,42,.08);
  position:relative;
  overflow:hidden;
}

.tech-detail-hero:before{
  content:"";
  position:absolute;
  right:-90px;
  top:-90px;
  width:260px;
  height:260px;
  border-radius:50%;
  background:radial-gradient(circle,rgba(240,90,40,.16),transparent 70%);
  pointer-events:none;
}

.tech-detail-image{
  border-radius:24px;
  overflow:hidden;
  height:100%;
  background:transparent;
  display:flex;
  align-items:center;
  justify-content:center;
}

.tech-detail-image img{
  width:100%;
  height:100%;
  object-fit:contain;
  display:block;
}

.tech-detail-content{
  position:relative;
  z-index:1;
  display:flex;
  flex-direction:column;
  justify-content:center;
}

.tech-detail-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:8px 13px;
  border-radius:999px;
  color:var(--td-orange);
  background:#fff4ed;
  border:1px solid #ffd8c7;
  font-size:12px;
  font-weight:900;
  text-transform:uppercase;
  letter-spacing:.55px;
  margin-bottom:13px;
}

.tech-detail-content h2{
  color:#171717;
  font-size:30px;
  line-height:1.22;
  letter-spacing:-.03em;
  font-weight:950;
  margin-bottom:14px;
}

.tech-detail-content p{
  color:var(--td-muted);
  line-height:1.78;
  font-size:15px;
  text-align:justify;
  margin-bottom:12px;
}

.tech-batch-text{
  display:inline-flex;
  align-items:center;
  gap:8px;
  color:#12854a;
  background:#effdf5;
  border:1px solid #c9f3dc;
  border-radius:12px;
  padding:10px 13px;
  font-weight:850;
  margin:8px 0 18px;
}

.tech-detail-actions{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
}

.tech-btn{
  min-height:45px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  border-radius:13px;
  padding:11px 18px;
  font-weight:900;
  text-decoration:none!important;
  border:1px solid transparent;
  transition:.25s ease;
}

.tech-btn-primary{
  color:#fff!important;
  background:linear-gradient(135deg,var(--td-orange),var(--td-orange2));
  box-shadow:0 13px 26px rgba(240,90,40,.25);
}

.tech-btn-primary:hover{
  transform:translateY(-2px);
  box-shadow:0 18px 36px rgba(240,90,40,.34);
}

.tech-btn-outline{
  color:var(--td-orange)!important;
  background:#fff;
  border-color:#ffd4c2;
}

.tech-btn-outline:hover{
  background:#fff5ef;
}

.tech-feature-grid{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:18px;
  margin-bottom:45px;
}

.tech-feature-card{
  height:100%;
  background:#fff;
  border:1px solid var(--td-border);
  border-radius:22px;
  padding:22px 18px;
  text-align:center;
  box-shadow:0 14px 34px rgba(15,23,42,.07);
  transition:.28s ease;
}

.tech-feature-card:hover{
  transform:translateY(-5px);
  border-color:#ffd4c2;
  box-shadow:0 18px 42px rgba(240,90,40,.13);
}

.tech-feature-icon{
  width:54px;
  height:54px;
  margin:0 auto 13px;
  border-radius:18px;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  background:linear-gradient(135deg,var(--td-orange),var(--td-orange2));
  font-size:22px;
}

.tech-feature-card h6{
  font-weight:900;
  color:#1e1e1e;
  margin-bottom:7px;
}

.tech-feature-card small{
  color:var(--td-muted);
  line-height:1.55;
}

.tech-section-heading{
  text-align:center;
  margin:14px auto 28px;
}

.tech-section-heading span{
  color:var(--td-orange);
  background:#fff4ed;
  border:1px solid #ffd8c7;
  border-radius:999px;
  padding:7px 12px;
  text-transform:uppercase;
  font-weight:900;
  font-size:12px;
  letter-spacing:.6px;
}

.tech-section-heading h3{
  margin:12px 0 0;
  color:#171717;
  font-weight:950;
  letter-spacing:-.03em;
}

.tech-syllabus-shell{
  background:#fff;
  border:1px solid var(--td-border);
  border-radius:28px;
  padding:26px;
  box-shadow:0 18px 46px rgba(15,23,42,.07);
}

.tech-phase-block{
  padding:18px;
  border-radius:22px;
  background:linear-gradient(180deg,#fff,#fffaf7);
  border:1px solid #f1f2f5;
  margin-bottom:18px;
}

.tech-phase-block:last-child{
  margin-bottom:0;
}

.tech-phase-title{
  display:flex;
  align-items:center;
  gap:10px;
  color:#222;
  font-size:17px;
  font-weight:950;
  margin-bottom:14px;
}

.tech-phase-title i{
  color:var(--td-orange);
}

.tech-accordion .accordion-item{
  border:1px solid #edf0f4!important;
  border-radius:16px!important;
  overflow:hidden;
  margin-bottom:10px;
  background:#fff;
  box-shadow:0 7px 18px rgba(15,23,42,.04);
}

.tech-accordion .accordion-button{
  padding:15px 18px;
  color:#24262d;
  font-weight:850;
  font-size:14px;
  box-shadow:none!important;
  background:#fff;
}

.tech-accordion .accordion-button:not(.collapsed){
  color:var(--td-orange);
  background:#fff4ed;
}

.tech-accordion .accordion-button:focus{
  box-shadow:none!important;
  border-color:transparent!important;
}

.tech-accordion .accordion-body{
  padding:18px 22px;
  color:var(--td-muted);
  background:#fff;
}

.tech-topic-list{
  margin:0;
  padding-left:19px;
}

.tech-topic-list li{
  padding:7px 0;
  line-height:1.6;
}

.tech-company-grid{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  gap:16px;
  margin-top:20px;
}

.tech-company-card{
  background:#fff;
  border:1px solid var(--td-border);
  border-radius:18px;
  height:105px;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:16px;
  box-shadow:0 12px 28px rgba(15,23,42,.06);
}

.tech-company-card img{
  max-width:100%;
  max-height:70px;
  object-fit:contain;
}

@media (max-width:991px){
  .tech-detail-hero{
    grid-template-columns:1fr;
    margin-top:35px;
  }

  .tech-feature-grid{
    grid-template-columns:repeat(2,minmax(0,1fr));
  }

  .tech-company-grid{
    grid-template-columns:repeat(3,minmax(0,1fr));
  }
}

@media (max-width:575px){
  .tech-detail-container{
    width:94%;
  }

  .tech-detail-hero,
  .tech-syllabus-shell{
    padding:18px;
    border-radius:22px;
  }

  .tech-detail-content h2{
    font-size:23px;
  }

  .tech-feature-grid,
  .tech-company-grid{
    grid-template-columns:1fr;
  }

  .tech-btn{
    width:100%;
  }

  .tech-phase-block{
    padding:13px;
  }

  .tech-accordion .accordion-button{
    font-size:13px;
    padding:13px 14px;
  }
}
</style>

<section class="breadcrumb-section">
  <h1><?= htmlspecialchars($detailTitle); ?></h1>
  <p class="breadcrumb">
    <a href="training.php">Training/Internship</a>
    <span>| <?= htmlspecialchars($tech['title']); ?></span>
  </p>
</section>

<main class="tech-detail-page">
  <div class="tech-detail-container">
    <section class="tech-detail-hero">
      <div class="tech-detail-image">
        <?php if (!empty($detailImage)): ?>
          <img src="<?= htmlspecialchars($detailImage); ?>" alt="<?= htmlspecialchars($tech['title']); ?>">
        <?php endif; ?>
      </div>

      <div class="tech-detail-content">
        <span class="tech-detail-badge">
          <i class="fa-solid fa-microchip"></i> Industrial Training Course
        </span>

        <h2><?= htmlspecialchars($detailTitle); ?></h2>

        <?php if (!empty($detailDescription)): ?>
          <p><?= nl2br(htmlspecialchars($detailDescription)); ?></p>
        <?php endif; ?>

        <?php if (!empty($tech['batch_text'])): ?>
          <div class="tech-batch-text">
            <i class="fa-solid fa-calendar-check"></i>
            <?= htmlspecialchars($tech['batch_text']); ?>
          </div>
        <?php endif; ?>

        <div class="tech-detail-actions">
          <a href="training.php#register" class="tech-btn tech-btn-primary">
            <i class="fa-solid fa-cart-shopping"></i> Enroll Now
          </a>

          <a href="javascript:void(0)"
             class="tech-btn tech-btn-outline"
             onclick="if(navigator.share){navigator.share({title:document.title,url:location.href});}else{navigator.clipboard.writeText(location.href);alert('Course link copied');}">
            <i class="fa-solid fa-share-nodes"></i> Share Course
          </a>
        </div>
      </div>
    </section>

    <?php if (!empty($features)): ?>
      <section class="tech-feature-grid">
        <?php foreach ($features as $feature): ?>
          <article class="tech-feature-card">
            <div class="tech-feature-icon">
              <?php if (!empty($feature['icon'])): ?>
                <i class="fa-solid <?= htmlspecialchars($feature['icon']); ?>"></i>
              <?php else: ?>
                <i class="fa-solid fa-circle-check"></i>
              <?php endif; ?>
            </div>

            <h6><?= htmlspecialchars($feature['title']); ?></h6>

            <?php if (!empty($feature['description'])): ?>
              <small><?= htmlspecialchars($feature['description']); ?></small>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <section class="tech-section-heading">
      <span>Course Syllabus</span>
      <h3>What Will You Learn?</h3>
    </section>

    <section class="tech-syllabus-shell">
      <div class="accordion tech-accordion" id="courseAccordion">
        <?php if (!empty($phases)): ?>
          <?php foreach ($phases as $phase): ?>
            <div class="tech-phase-block">
              <h5 class="tech-phase-title">
                <i class="fa-solid fa-layer-group"></i>
                <?= htmlspecialchars($phase['phase_title']); ?>
              </h5>

              <?php foreach ($phase['modules'] as $module): ?>
                <?php $collapseId = "module_" . (int)$module['id']; ?>

                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading_<?= (int)$module['id']; ?>">
                    <button class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= $collapseId; ?>"
                            aria-expanded="false"
                            aria-controls="<?= $collapseId; ?>">
                      <?= htmlspecialchars($module['module_title']); ?>
                    </button>
                  </h2>

                  <div id="<?= $collapseId; ?>"
                       class="accordion-collapse collapse"
                       aria-labelledby="heading_<?= (int)$module['id']; ?>"
                       data-bs-parent="#courseAccordion">

                    <div class="accordion-body">
                      <?php if (!empty($module['topics'])): ?>
                        <ol class="tech-topic-list">
                          <?php foreach ($module['topics'] as $topic): ?>
                            <li><?= htmlspecialchars($topic['topic_text']); ?></li>
                          <?php endforeach; ?>
                        </ol>
                      <?php else: ?>
                        <p class="mb-0 text-muted">Topics will be updated soon.</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted mb-0">Syllabus will be updated soon.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php if (!empty($companies)): ?>
      <section class="tech-section-heading mt-5">
        <span>Hiring Partners</span>
        <h3>Placement Companies</h3>
      </section>

      <section class="tech-company-grid">
        <?php foreach ($companies as $company): ?>
          <div class="tech-company-card">
            <img src="<?= htmlspecialchars($company['logo']); ?>" alt="Company Logo">
          </div>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php include 'footer.php'; ?>