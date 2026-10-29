<?php
include 'config/db.php';
include 'header.php';

$search = mysqli_real_escape_string($conn, trim($_GET['search'] ?? ''));

$where = "WHERE status = 1 AND project_type = 'mini'";

if ($search !== '') {
  $where .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

$projects = mysqli_query(
  $conn,
  "SELECT * FROM projects $where ORDER BY id DESC"
);
?>

<style>
  .projects-hero {
    padding: 80px 0;
    background:
      linear-gradient(135deg, rgba(15, 23, 42, 0.88), rgba(239, 102, 47, 0.82)),
      url('./assets/images/banner/banner-1.jpg');
    background-size: cover;
    background-position: center;
    color: var(--theme-white);
  }

  .projects-hero h1 {
    font-size: 46px;
    font-weight: 800;
    margin-bottom: 15px;
    color: var(--theme-white);
  }

  .projects-hero p {
    max-width: 850px;
    font-size: 16px;
    line-height: 1.8;
    color: var(--theme-white);
    margin-bottom: 0 !important;
  }

  .projects-section {
    padding: 60px 0;
    background: var(--theme-light);
  }

  .project-filter-card {
    background: var(--theme-white);
    border-radius: 18px;
    padding: 10px;
    border: 1px solid var(--theme-light);
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
    margin-bottom: 35px;
  }

  .project-filter-card .form-control {
    height: 50px;
    border-radius: 12px;
    border: 1px solid var(--theme-light);
    box-shadow: none;
  }

  .project-filter-card .form-control:focus {
    border-color: var(--theme-primary);
    box-shadow: 0 0 0 4px rgba(239, 102, 47, 0.12);
  }

  .project-search-btn {
    height: 50px;
    border: none;
    border-radius: 12px;
    background: var(--theme-primary);
    color: var(--theme-white);
    font-weight: 700;
    padding: 0 10px;
  }

  .project-search-btn:hover {
    background: var(--theme-dark);
  }

  .project-card {
    height: 100%;
    background: var(--theme-white);
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid var(--theme-light);
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.07);
    transition: 0.25s ease;
  }

  .project-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.13);
  }

  .project-img-box {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: var(--theme-light);
  }

  .project-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.25s ease;
  }

  .project-card:hover .project-img-box img {
    transform: scale(1.08);
  }

  .project-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: var(--theme-primary);
    color: var(--theme-white);
    padding: 6px 13px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
  }

  .project-body {
    padding: 22px;
  }

  .project-body h3 {
    font-size: 20px;
    font-weight: 800;
    color: var(--theme-dark);
    margin-bottom: 10px;
    line-height: 1.4;
  }

  .project-body p {
    color: var(--theme-muted);
    font-size: 14px;
    line-height: 1.8;
    margin-bottom: 18px !important;
  }

  .project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
  }

  .project-meta span {
    background: var(--theme-light);
    color: var(--theme-primary);
    border-radius: 50px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
  }

  .project-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--theme-primary);
    color: #fff !important;
    padding: 11px 18px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 800;
    transition: 0.2s ease;
  }

  .project-btn:hover {
    background: var(--theme-dark);
    transform: translateY(-1px);
  }

  .empty-projects {
    background: var(--theme-white);
    border-radius: 18px;
    padding: 45px 25px;
    text-align: center;
    border: 1px dashed var(--theme-muted);
    color: var(--theme-muted);
  }

  .empty-projects i {
    font-size: 48px;
    color: var(--theme-primary);
    margin-bottom: 15px;
  }

  .price-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
  }

  .price-box .price {
    font-size: 22px;
    font-weight: 800;
    color: var(--theme-primary);
    margin-bottom: 0 !important;
  }

  .price-box del {
    font-size: 14px;
    color: var(--theme-muted);
    font-weight: 600;
  }

  .tax-text {
    font-size: 11px;
    color: var(--theme-primary);
    font-weight: 700;
    margin-bottom: 15px;
  }

  .badge-discount {
    position: absolute;
    top: 14px;
    right: 14px;
    background: var(--theme-primary);
    color: var(--theme-white);
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 800;
    z-index: 2;
  }

  .btn-group-custom {
    display: flex;
    gap: 10px;
  }

  .btn-cart {
    background: var(--theme-light);
    color: var(--theme-dark);
    border-radius: 12px;
    border: none;
    padding: 10px 15px;
    font-weight: 700;
    transition: 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-cart:hover {
    background: var(--theme-light);
    color: var(--theme-primary);
  }

  .btn-buy {
    flex: 1;
    text-align: center;
    justify-content: center;
    background: var(--theme-primary);
    color: #fff !important;
    border-radius: 12px;
    padding: 10px 15px;
    font-weight: 800;
    text-decoration: none;
    transition: 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-buy:hover {
    background: var(--theme-dark);
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .projects-hero {
      padding: 55px 0;
    }

    .projects-hero h1 {
      font-size: 32px;
    }

    .project-img-box {
      height: 190px;
    }

    .project-filter-card {
      padding: 15px;
    }

    .project-filter-card .row>div {
      margin-bottom: 15px;
    }

    .project-filter-card .project-search-btn {
      width: 100% !important;
    }
  }

  /* =========================
PROJECT CART / WISHLIST BUTTON UI
========================= */

  .btn-group-custom {
    display: flex;
    gap: 10px;
    align-items: center;
  }

  .btn-cart,
  .btn-wishlist {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc, #eef2ff);
    color: #0f172a;
    font-size: 17px;
    transition: 0.3s ease;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
  }

  .btn-cart:hover {
    background: linear-gradient(135deg, #ef662f, #ff8c42);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(239, 102, 47, 0.28);
  }

  .btn-wishlist:hover {
    background: linear-gradient(135deg, #ef662f, #ff8c42);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 77, 109, 0.28);
  }

  .btn-buy {
    flex: 1;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff !important;
    font-weight: 800;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: 0.3s ease;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
  }

  .btn-buy:hover {
    background: linear-gradient(135deg, #ef662f, #ff8c42);
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(239, 102, 47, 0.3);
  }

  /* =========================
PROJECT CARD PREMIUM LOOK
========================= */

  .project-card {
    height: 100%;
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #edf2f7;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
    transition: 0.35s ease;
    position: relative;
  }

  .project-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 22px 45px rgba(15, 23, 42, 0.12);
    border-color: rgba(239, 102, 47, 0.2);
  }

  .project-img-box {
    position: relative;
    height: 240px;
    overflow: hidden;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  }

  .project-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.45s ease;
  }

  .project-card:hover .project-img-box img {
    transform: scale(1.08);
  }

  .project-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #ef662f, #ff8c42);
    color: #fff;
    padding: 7px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 6px 18px rgba(239, 102, 47, 0.22);
  }

  .badge-discount {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #ef662f, #ff8c42);
    color: #fff;
    padding: 7px 13px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 800;
    z-index: 2;
    box-shadow: 0 6px 18px rgba(255, 77, 109, 0.22);
  }

  .project-body {
    padding: 24px;
  }

  .project-body h3 {
    font-size: 21px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 12px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .project-body p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.8;
    margin-bottom: 18px !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
  }

  .project-meta span {
    background: #f8fafc;
    color: #ef662f;
    border: 1px solid rgba(239, 102, 47, 0.1);
    border-radius: 50px;
    padding: 7px 13px;
    font-size: 12px;
    font-weight: 700;
  }

  .price-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
  }

  .price-box .price {
    font-size: 26px;
    font-weight: 900;
    color: #ef662f;
    margin-bottom: 0 !important;
  }

  .price-box del {
    font-size: 14px;
    color: #94a3b8;
    font-weight: 700;
  }

  .tax-text {
    font-size: 11px;
    color: #ef662f;
    font-weight: 700;
    margin-bottom: 18px;
  }

  /* =========================
RESPONSIVE
========================= */

  @media (max-width: 768px) {

    .project-img-box {
      height: 210px;
    }

    .project-body {
      padding: 20px;
    }

    .project-body h3 {
      font-size: 18px;
    }

    .btn-buy {
      font-size: 13px;
    }
  }
</style>

<section class="breadcrumb-section">
  <h1>MINI PROJECTS</h1>
  <p class="breadcrumb">
    <a href="index.php">Home</a> <span>| Mini Projects</span>
  </p>
</section>

<section class="projects-section">
  <div class="container">

    <!-- <div class="project-filter-card">
      <form method="GET" class="row g-3 align-items-center">
        <div class="col-md-10">
          <input type="text"
            name="search"
            class="form-control"
            placeholder="Search mini projects..."
            value="<?= htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
          <button type="submit" class="project-search-btn w-100">
            <i class="fa fa-search"></i> Search
          </button>
        </div>
      </form>
    </div> -->

    <div class="row g-4">
      <?php if ($projects && mysqli_num_rows($projects) > 0): ?>
        <?php while ($p = mysqli_fetch_assoc($projects)): ?>
          <?php
          $image = !empty($p['image']) && file_exists('uploads/projects/' . $p['image'])
            ? 'uploads/projects/' . $p['image']
            : 'assets/images/default-project.webp';

          $title = htmlspecialchars($p['title'] ?? 'Mini Project');
          $description = strip_tags($p['description'] ?? '');
          // $shortDesc = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
          ?>
          <div class="col-lg-4 col-md-6">
            <div class="project-card">
              <div class="project-img-box">
                <img src="<?= htmlspecialchars($image); ?>" alt="<?= $title; ?>">
                <span class="project-badge">Mini Project</span>
                <?php if ((float)$p['discount'] > 0): ?>
                  <span class="badge-discount"><?= round((float)$p['discount']) ?>% OFF</span>
                <?php endif; ?>
              </div>

              <div class="project-body">
                <h3><?= $title; ?></h3>

                <p><?= htmlspecialchars($description); ?></p>

                <div class="project-meta">
                  <span><i class="fa fa-layer-group"></i> Beginner</span>
                  <span><i class="fa fa-diagram-project"></i> Practical</span>
                </div>

                <div class="price-box">
                  <p class="price">₹<?= format_price((float)$p['price']); ?></p>
                  <?php if ((float)$p['old_price'] > (float)$p['price']): ?>
                    <del>₹<?= format_price((float)$p['old_price']); ?></del>
                  <?php endif; ?>
                </div>
                <div class="tax-text">Inclusive of all taxes</div>

                <div class="btn-group-custom">

                  <!-- WISHLIST -->
                  <button class="btn-wishlist wishlist-btn"
                    data-id="<?= $p['id']; ?>">
                    <i class="fa fa-heart"></i>
                  </button>

                  <!-- CART -->
                  <button class="btn-cart cart-btn"
                    data-id="<?= $p['id']; ?>"
                    data-type="project">
                    <i class="fa fa-cart-plus"></i>
                  </button>

                  <!-- BUY NOW -->
                  <a href="project-details.php?id=<?= (int)$p['id']; ?>" class="btn-buy">
                    <i class="fa fa-bolt"></i>
                    Buy Now
                  </a>

                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="empty-projects">
            <i class="fa fa-folder-open"></i>
            <h4>No Mini Projects Found</h4>
            <p>Currently no mini projects are available. Please check again later.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include 'footer.php'; ?>