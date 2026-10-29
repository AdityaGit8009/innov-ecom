<?php
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

function h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$technology_id = (int) ($_GET['id'] ?? 0);

$techQ = mysqli_query($conn, "SELECT * FROM training_technologies WHERE id='$technology_id' LIMIT 1");
$tech = $techQ ? mysqli_fetch_assoc($techQ) : null;

if (!$tech) {
  echo '<div class="alert alert-danger">Technology not found.</div>';
  include "includes/footer.php";
  exit;
}

$featureQ = mysqli_query($conn, "SELECT * FROM technology_features WHERE technology_id='$technology_id' ORDER BY sort_order ASC, id ASC");
$companyQ = mysqli_query($conn, "SELECT * FROM technology_companies WHERE technology_id='$technology_id' ORDER BY sort_order ASC, id ASC");
$phaseQ = mysqli_query($conn, "SELECT * FROM technology_phases WHERE technology_id='$technology_id' ORDER BY sort_order ASC, id ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">
    <i class="bx bx-layer"></i>
    Manage Content: <?= h($tech['title']); ?>
  </h4>

  <a href="manage_training_technologies.php" class="btn btn-secondary btn-sm">
    Back
  </a>
</div>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">Content saved successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_excel'): ?>
  <div class="alert alert-danger">Invalid file. Please upload only .csv template file.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'empty_course'): ?>
  <div class="alert alert-danger">
    Course content empty hai. CSV file me Phase, Module aur Topic properly fill karo.
  </div>
<?php endif; ?>

<form action="save_technology_content.php" method="POST" enctype="multipart/form-data" onsubmit="prepareCourseJson()">
  <input type="hidden" name="technology_id" value="<?= $technology_id; ?>">
  <input type="hidden" name="course_json" id="course_json">

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Feature Cards</h5>

      <div id="featureBox">
        <?php if ($featureQ && mysqli_num_rows($featureQ) > 0): ?>
          <?php while ($feature = mysqli_fetch_assoc($featureQ)): ?>
            <div class="row g-2 mb-2 feature-row">
              <div class="col-lg-3">
                <input type="text" name="feature_icon[]" class="form-control" placeholder="fa-building"
                  value="<?= h($feature['icon']); ?>">
              </div>

              <div class="col-lg-4">
                <input type="text" name="feature_title[]" class="form-control" placeholder="Title"
                  value="<?= h($feature['title']); ?>">
              </div>

              <div class="col-lg-4">
                <input type="text" name="feature_desc[]" class="form-control" placeholder="Description"
                  value="<?= h($feature['description']); ?>">
              </div>

              <div class="col-lg-1">
                <button type="button" class="btn btn-danger w-100 remove-row">X</button>
              </div>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addFeature()">
        <i class="bx bx-plus"></i> Add Feature
      </button>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Company Logos</h5>

      <div class="row">
        <?php if ($companyQ && mysqli_num_rows($companyQ) > 0): ?>
          <?php while ($company = mysqli_fetch_assoc($companyQ)): ?>
            <div class="col-lg-2 col-md-3 col-6 text-center mb-3">
              <div class="border rounded p-2 h-100">
                <img src="../<?= h($company['logo']); ?>" height="60" class="mb-2">
                <br>
                <label class="small">
                  <input type="checkbox" name="old_company_logos[]" value="<?= h($company['logo']); ?>" checked>
                  Keep
                </label>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-muted">No company logo added.</p>
          </div>
        <?php endif; ?>
      </div>

      <label class="form-label">Add New Company Logos</label>
      <input type="file" name="company_logos[]" class="form-control" multiple>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">Course Phases, Modules & Topics</h5>


      <div class="alert alert-info">
        <b>Excel Bulk Upload Format:</b><br>
        Excel file me first row heading honi chahiye:<br>
        <b>Phase | Module | Topic</b><br><br>
        <b>Note:</b> Excel upload karne par purana accordion content replace ho jayega.
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label fw-bold mb-0">
            Bulk Upload Accordion Content Using CSV
          </label>

          <a href="download_course_template.php" class="btn btn-sm btn-outline-success">
            <i class="bx bx-download"></i> Download Template
          </a>
        </div>

        <input type="file" name="bulk_course_excel" class="form-control" accept=".csv">

        <small class="text-muted">
          Excel blank rahega to neeche wala manual accordion content save hoga.
        </small>
      </div>

      <hr>


      <div id="phaseBox">
        <?php if ($phaseQ && mysqli_num_rows($phaseQ) > 0): ?>
          <?php while ($phase = mysqli_fetch_assoc($phaseQ)): ?>
            <?php
            $phase_id = (int) $phase['id'];
            $moduleQ = mysqli_query($conn, "SELECT * FROM technology_modules WHERE phase_id='$phase_id' ORDER BY sort_order ASC, id ASC");
            ?>

            <div class="phase-card border rounded p-3 mb-3">
              <div class="row g-2 mb-2">
                <div class="col-lg-11">
                  <input type="text" class="form-control phase-title" value="<?= h($phase['phase_title']); ?>"
                    placeholder="Phase Title">
                </div>

                <div class="col-lg-1">
                  <button type="button" class="btn btn-danger w-100 remove-row">X</button>
                </div>
              </div>

              <div class="module-box">
                <?php if ($moduleQ && mysqli_num_rows($moduleQ) > 0): ?>
                  <?php while ($module = mysqli_fetch_assoc($moduleQ)): ?>
                    <?php
                    $module_id = (int) $module['id'];
                    $topicQ = mysqli_query($conn, "SELECT * FROM technology_topics WHERE module_id='$module_id' ORDER BY sort_order ASC, id ASC");

                    $topics = [];
                    if ($topicQ && mysqli_num_rows($topicQ) > 0) {
                      while ($topic = mysqli_fetch_assoc($topicQ)) {
                        $topics[] = $topic['topic_text'];
                      }
                    }
                    ?>

                    <div class="module-card border rounded p-2 mb-2 bg-light">
                      <input type="text" class="form-control mb-2 module-title" value="<?= h($module['module_title']); ?>"
                        placeholder="Module Title">

                      <textarea class="form-control topic-text" rows="4"
                        placeholder="One topic per line"><?= h(implode("\n", $topics)); ?></textarea>

                      <button type="button" class="btn btn-sm btn-danger mt-2 remove-row">
                        Remove Module
                      </button>
                    </div>
                  <?php endwhile; ?>
                <?php endif; ?>
              </div>

              <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addModule(this)">
                <i class="bx bx-plus"></i> Add Module
              </button>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>

      <button type="button" class="btn btn-sm btn-primary" onclick="addPhase()">
        <i class="bx bx-plus"></i> Add Phase
      </button>
    </div>
  </div>

  <div class="text-end mb-4">
    <button type="submit" class="btn btn-success">
      <i class="bx bx-save"></i> Save Content
    </button>

    <a href="manage_training_technologies.php" class="btn btn-secondary">
      Cancel
    </a>
  </div>
</form>

<script>
  function addFeature() {
    document.getElementById('featureBox').insertAdjacentHTML('beforeend', `
        <div class="row g-2 mb-2 feature-row">
            <div class="col-lg-3">
                <input type="text" name="feature_icon[]" class="form-control" placeholder="fa-building">
            </div>

            <div class="col-lg-4">
                <input type="text" name="feature_title[]" class="form-control" placeholder="Title">
            </div>

            <div class="col-lg-4">
                <input type="text" name="feature_desc[]" class="form-control" placeholder="Description">
            </div>

            <div class="col-lg-1">
                <button type="button" class="btn btn-danger w-100 remove-row">X</button>
            </div>
        </div>
    `);
  }

  function addPhase() {
    document.getElementById('phaseBox').insertAdjacentHTML('beforeend', `
        <div class="phase-card border rounded p-3 mb-3">
            <div class="row g-2 mb-2">
                <div class="col-lg-11">
                    <input type="text" class="form-control phase-title" placeholder="Phase Title">
                </div>

                <div class="col-lg-1">
                    <button type="button" class="btn btn-danger w-100 remove-row">X</button>
                </div>
            </div>

            <div class="module-box"></div>

            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addModule(this)">
                <i class="bx bx-plus"></i> Add Module
            </button>
        </div>
    `);
  }

  function addModule(btn) {
    let box = btn.closest('.phase-card').querySelector('.module-box');

    box.insertAdjacentHTML('beforeend', `
        <div class="module-card border rounded p-2 mb-2 bg-light">
            <input type="text" class="form-control mb-2 module-title" placeholder="Module Title">

            <textarea class="form-control topic-text" rows="4" placeholder="One topic per line"></textarea>

            <button type="button" class="btn btn-sm btn-danger mt-2 remove-row">
                Remove Module
            </button>
        </div>
    `);
  }

  function prepareCourseJson() {
    let phases = [];

    document.querySelectorAll('.phase-card').forEach(function(phaseCard) {
      let phaseTitleInput = phaseCard.querySelector('.phase-title');

      if (!phaseTitleInput) {
        return;
      }

      let phaseTitle = phaseTitleInput.value.trim();

      if (phaseTitle === '') {
        return;
      }

      let modules = [];

      phaseCard.querySelectorAll('.module-card').forEach(function(moduleCard) {
        let moduleTitle = moduleCard.querySelector('.module-title').value.trim();
        let topicText = moduleCard.querySelector('.topic-text').value.trim();

        if (moduleTitle === '') {
          return;
        }

        let topics = topicText
          .split('\n')
          .map(item => item.trim())
          .filter(item => item !== '');

        modules.push({
          module_title: moduleTitle,
          topics: topics
        });
      });

      phases.push({
        phase_title: phaseTitle,
        modules: modules
      });
    });

    document.getElementById('course_json').value = JSON.stringify(phases);
  }

  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
      let target =
        e.target.closest('.feature-row') ||
        e.target.closest('.module-card') ||
        e.target.closest('.phase-card');

      if (target) {
        target.remove();
      }
    }
  });
</script>

<?php include "includes/footer.php"; ?>