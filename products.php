<?php
include "config/db.php";
include "header.php";

function showRating($rating)
{
  for ($i = 1; $i <= 5; $i++) {
    echo $i <= round((float)$rating)
      ? '<ion-icon name="star"></ion-icon>'
      : '<ion-icon name="star-outline"></ion-icon>';
  }
}

function productBadge($discount)
{
  return ((int)$discount > 0)
    ? '<p class="showcase-badge">' . (int)$discount . '%</p>'
    : '';
}

$where = ["p.status=1"];

$q = mysqli_real_escape_string($conn, trim($_GET['q'] ?? ''));

if ($q !== '') {
  $where[] = "(p.title LIKE '%$q%' OR c.name LIKE '%$q%' OR s.name LIKE '%$q%')";
}

if (!empty($_GET['cat_id'])) {
  $where[] = "p.category_id=" . (int)$_GET['cat_id'];
}

if (!empty($_GET['sub_id'])) {
  $where[] = "p.sub_category_id=" . (int)$_GET['sub_id'];
}

if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
  $where[] = "p.price >= " . (float)$_GET['min_price'];
}

if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
  $where[] = "p.price <= " . (float)$_GET['max_price'];
}

if (!empty($_GET['rating'])) {
  $where[] = "p.rating >= " . (float)$_GET['rating'];
}

if (!empty($_GET['discount'])) {
  $where[] = "p.discount >= " . (int)$_GET['discount'];
}

if (!empty($_GET['stock'])) {

  if ($_GET['stock'] === 'in') {
    $where[] = "p.stock > 0";
  }

  if ($_GET['stock'] === 'out') {
    $where[] = "p.stock <= 0";
  }
}

$sort = $_GET['sort'] ?? 'new';

$order = "p.created_at DESC";

if ($sort === 'price_low') {
  $order = "p.price ASC";
}

if ($sort === 'price_high') {
  $order = "p.price DESC";
}

if ($sort === 'rating') {
  $order = "p.rating DESC";
}

if ($sort === 'discount') {
  $order = "p.discount DESC";
}

$whereSql = implode(' AND ', $where);

$newProducts = mysqli_query(
  $conn,
  "SELECT p.*, 
          c.name category_name, 
          s.name subcategory_name
   FROM products p
   LEFT JOIN categories c ON c.id=p.category_id
   LEFT JOIN sub_categories s ON s.id=p.sub_category_id
   WHERE $whereSql
   ORDER BY $order"
);

$cats = mysqli_query(
  $conn,
  "SELECT id,name 
   FROM categories 
   WHERE status=1 
   ORDER BY name"
);

$allSubs = mysqli_query(
  $conn,
  "SELECT id,name,category_id 
   FROM sub_categories
   WHERE status=1
   ORDER BY name"
);

$subcategories = [];

while ($sub = mysqli_fetch_assoc($allSubs)) {

  $catId = $sub['category_id'];

  if (!isset($subcategories[$catId])) {
    $subcategories[$catId] = [];
  }

  $subcategories[$catId][] = [
    'id'   => $sub['id'],
    'name' => $sub['name']
  ];
}

?>


<style>
  .shop-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 25px;
    margin-top: 25px;
    align-items: start;
  }

  /* FILTER SIDEBAR */
  .filter-sidebar {
    position: sticky;
    top: 20px;
    background: #fff;
    border-radius: 24px;
    border: 1px solid var(--cultured);
    padding: 22px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
    max-height: calc(100vh - 110px);
    overflow-y: auto;
  }

  .filter-sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .filter-sidebar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, .15);
    border-radius: 20px;
  }

  .filter-title {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .filter-group {
    margin-bottom: 18px;
  }

  .filter-group label {
    display: block;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 7px;
    color: var(--davys-gray);
  }

  .filter-sidebar .form-control,
  .filter-sidebar .form-select,
  .offcanvas .form-control,
  .offcanvas .form-select {
    border-radius: 14px;
    border: 1px solid var(--cultured);
    min-height: 48px;
    box-shadow: none !important;
  }

  .filter-sidebar .form-control:focus,
  .filter-sidebar .form-select:focus,
  .offcanvas .form-control:focus,
  .offcanvas .form-select:focus {
    border-color: var(--salmon-pink);
  }

  .filter-btn {
    width: 100%;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--salmon-pink), var(--sandy-brown));
    color: #fff;
    font-weight: 800;
    padding: 13px;
    transition: .3s;
  }

  .filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(239, 102, 47, .25);
  }

  .clear-filter-btn {
    width: 100%;
    display: block;
    margin-top: 12px;
    padding: 12px;
    border-radius: 14px;
    background: #f4f4f4;
    color: #111;
    text-align: center;
    font-weight: 700;
    text-decoration: none;
    transition: .3s;
  }

  .clear-filter-btn:hover {
    background: #ececec;
    color: #111;
  }

  /* MOBILE FILTER BUTTON */
  .mobile-filter-btn {
    display: none;
    width: 100%;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--eerie-black), var(--onyx));
    color: #fff;
    font-weight: 800;
    padding: 14px 20px;
    margin-top: 20px;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  /* OFFCANVAS */
  .offcanvas {
    border-radius: 24px 24px 0 0;
  }

  .offcanvas-header {
    border-bottom: 1px solid #eee;
    padding: 18px 20px;
  }

  .offcanvas-title {
    font-size: 20px;
    font-weight: 800;
  }

  .offcanvas-body {
    padding: 20px;
  }

  /* PRODUCT GRID */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
  }

  .product-grid .showcase {
    border-radius: 22px;
    background: var(--white);
    border: 1px solid var(--cultured);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: .3s ease;
    /* height: 100%; */
    height: auto;
    /* display: flex; */
    flex-direction: column;
    overflow: hidden;
  }

  .product-grid .showcase:hover {
    transform: translateY(-5px);
    border-color: var(--sandy-brown);
    box-shadow: 0 18px 35px rgba(239, 102, 47, 0.15);
  }

  .showcase-content {
    padding: 18px;
    display: flex;
    flex-direction: column;
    /* height: 100%; */
  }

  .showcase-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .price-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 8px;
  }

  .price-box .price {
    font-size: 22px;
    font-weight: 800;
    color: var(--eerie-black);
  }

  .tax-line {
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
  }

  .stock-line {
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
  }

  .stock-out {
    color: red;
  }

  .buy-now-wrap {
    /* margin-top: auto; */
    padding-top: 15px;
  }

  .buy-now-btn {
    display: block;
    width: 100%;
    text-align: center;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--eerie-black), var(--onyx));
    color: #fff;
    font-weight: 800;
    padding: 12px;
    transition: .3s;
    text-decoration: none;
  }

  .buy-now-btn:hover {
    background: linear-gradient(135deg, var(--salmon-pink), var(--sandy-brown));
    color: #fff;
    transform: translateY(-2px);
  }

  @media (max-width: 991px) {

    .shop-layout {
      grid-template-columns: 1fr;
    }

    .filter-sidebar {
      display: none;
    }

    .mobile-filter-btn {
      display: flex;
    }
  }

  @media (max-width: 768px) {

    .product-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }

    .showcase-content {
      padding: 14px;
    }

    .showcase-title {
      font-size: 14px;
    }

    .price-box .price {
      font-size: 18px;
    }

    .buy-now-btn {
      padding: 10px;
      font-size: 14px;
    }
  }
</style>

<div class="container">

  <!-- MOBILE FILTER BUTTON -->
  <button class="mobile-filter-btn"
    data-bs-toggle="offcanvas"
    data-bs-target="#mobileFilter">

    <ion-icon name="options-outline"></ion-icon>
    Filters

  </button>

  <div class="shop-layout">

    <!-- LEFT FILTER -->
    <aside class="filter-sidebar">

      <div class="filter-title">
        <ion-icon name="options-outline"></ion-icon>
        Filters
      </div>

      <form method="get">

        <div class="filter-group">
          <label>Search</label>

          <input type="text"
            class="form-control"
            name="q"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            placeholder="Search products">
        </div>

        <div class="filter-group">
          <label>Category</label>

          <select class="form-select category-select"
            name="cat_id">

            <option value="">All Categories</option>

            <?php
            mysqli_data_seek($cats, 0);

            while ($c = mysqli_fetch_assoc($cats)):
            ?>

              <option value="<?= $c['id'] ?>"
                <?= ($_GET['cat_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>

                <?= htmlspecialchars($c['name']) ?>

              </option>

            <?php endwhile; ?>

          </select>
        </div>

        <div class="filter-group">
          <label>Subcategory</label>

          <select class="form-select subcategory-select"
            name="sub_id">

            <option value="">All Subcategories</option>

          </select>
        </div>

        <div class="row">
          <div class="col-6">
            <div class="filter-group">
              <label>Min ₹</label>

              <input type="number"
                class="form-control"
                name="min_price"
                value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
            </div>
          </div>

          <div class="col-6">
            <div class="filter-group">
              <label>Max ₹</label>

              <input type="number"
                class="form-control"
                name="max_price"
                value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
            </div>
          </div>
        </div>

        <div class="filter-group">
          <label>Rating</label>

          <select class="form-select" name="rating">

            <option value="">All Ratings</option>

            <?php for ($r = 5; $r >= 1; $r--): ?>

              <option value="<?= $r ?>"
                <?= ($_GET['rating'] ?? '') == $r ? 'selected' : '' ?>>

                <?= $r ?>★+

              </option>

            <?php endfor; ?>

          </select>
        </div>

        <div class="filter-group">
          <label>Discount</label>

          <select class="form-select" name="discount">

            <option value="">All Discounts</option>

            <?php foreach ([5, 10, 20, 30, 40, 50] as $d): ?>

              <option value="<?= $d ?>"
                <?= ($_GET['discount'] ?? '') == $d ? 'selected' : '' ?>>

                <?= $d ?>%+

              </option>

            <?php endforeach; ?>

          </select>
        </div>

        <div class="filter-group">
          <label>Stock</label>

          <select class="form-select" name="stock">

            <option value="">All</option>

            <option value="in"
              <?= ($_GET['stock'] ?? '') === 'in' ? 'selected' : '' ?>>
              In Stock
            </option>

            <option value="out"
              <?= ($_GET['stock'] ?? '') === 'out' ? 'selected' : '' ?>>
              Out of Stock
            </option>

          </select>
        </div>

        <div class="filter-group">
          <label>Sort By</label>

          <select class="form-select" name="sort">

            <option value="new">Newest</option>

            <option value="price_low"
              <?= $sort === 'price_low' ? 'selected' : '' ?>>
              Price Low
            </option>

            <option value="price_high"
              <?= $sort === 'price_high' ? 'selected' : '' ?>>
              Price High
            </option>

            <option value="rating"
              <?= $sort === 'rating' ? 'selected' : '' ?>>
              Top Rated
            </option>

            <option value="discount"
              <?= $sort === 'discount' ? 'selected' : '' ?>>
              Highest Discount
            </option>

          </select>
        </div>

        <button class="filter-btn">
          Apply Filters
        </button>

        <a href="products.php" class="clear-filter-btn">
          Clear Filters
        </a>

      </form>

    </aside>

    <!-- PRODUCTS SECTION -->
    <div>

      <!-- MOBILE OFFCANVAS -->
      <div class="offcanvas offcanvas-bottom h-100"
        tabindex="-1"
        id="mobileFilter">

        <div class="offcanvas-header">

          <h5 class="offcanvas-title">
            Filter Products
          </h5>

          <button type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"></button>

        </div>

        <div class="offcanvas-body">

          <form method="get">

            <div class="filter-group">
              <label>Search</label>

              <input type="text"
                class="form-control"
                name="q"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                placeholder="Search products">
            </div>

            <div class="filter-group">
              <label>Category</label>

              <select class="form-select mobile-category-select"
                name="cat_id">

                <option value="">All Categories</option>

                <?php
                mysqli_data_seek($cats, 0);

                while ($c = mysqli_fetch_assoc($cats)):
                ?>

                  <option value="<?= $c['id'] ?>"
                    <?= ($_GET['cat_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>

                    <?= htmlspecialchars($c['name']) ?>

                  </option>

                <?php endwhile; ?>

              </select>
            </div>

            <div class="filter-group">
              <label>Subcategory</label>

              <select class="form-select mobile-subcategory-select"
                name="sub_id">

                <option value="">All Subcategories</option>

              </select>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="filter-group">
                  <label>Min ₹</label>

                  <input type="number"
                    class="form-control"
                    name="min_price"
                    value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                </div>
              </div>

              <div class="col-6">
                <div class="filter-group">
                  <label>Max ₹</label>

                  <input type="number"
                    class="form-control"
                    name="max_price"
                    value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                </div>
              </div>
            </div>

            <div class="filter-group">
              <label>Rating</label>

              <select class="form-select" name="rating">

                <option value="">All Ratings</option>

                <?php for ($r = 5; $r >= 1; $r--): ?>

                  <option value="<?= $r ?>"
                    <?= ($_GET['rating'] ?? '') == $r ? 'selected' : '' ?>>

                    <?= $r ?>★+

                  </option>

                <?php endfor; ?>

              </select>
            </div>

            <div class="filter-group">
              <label>Discount</label>

              <select class="form-select" name="discount">

                <option value="">All Discounts</option>

                <?php foreach ([5, 10, 20, 30, 40, 50] as $d): ?>

                  <option value="<?= $d ?>"
                    <?= ($_GET['discount'] ?? '') == $d ? 'selected' : '' ?>>

                    <?= $d ?>%+

                  </option>

                <?php endforeach; ?>

              </select>
            </div>

            <div class="filter-group">
              <label>Stock</label>

              <select class="form-select" name="stock">

                <option value="">All</option>

                <option value="in"
                  <?= ($_GET['stock'] ?? '') === 'in' ? 'selected' : '' ?>>
                  In Stock
                </option>

                <option value="out"
                  <?= ($_GET['stock'] ?? '') === 'out' ? 'selected' : '' ?>>
                  Out of Stock
                </option>

              </select>
            </div>

            <div class="filter-group">
              <label>Sort By</label>

              <select class="form-select" name="sort">

                <option value="new">Newest</option>

                <option value="price_low"
                  <?= $sort === 'price_low' ? 'selected' : '' ?>>
                  Price Low
                </option>

                <option value="price_high"
                  <?= $sort === 'price_high' ? 'selected' : '' ?>>
                  Price High
                </option>

                <option value="rating"
                  <?= $sort === 'rating' ? 'selected' : '' ?>>
                  Top Rated
                </option>

                <option value="discount"
                  <?= $sort === 'discount' ? 'selected' : '' ?>>
                  Highest Discount
                </option>

              </select>
            </div>

            <button class="filter-btn">
              Apply Filters
            </button>

            <a href="products.php"
              class="clear-filter-btn">
              Clear Filters
            </a>

          </form>

        </div>
      </div>

      <!-- PRODUCT GRID -->
      <div class="product-grid">

        <?php if ($newProducts && mysqli_num_rows($newProducts)): ?>

          <?php while ($p = mysqli_fetch_assoc($newProducts)): ?>

            <div class="showcase">

              <div class="showcase-banner">

                <img src="uploads/products/<?= htmlspecialchars($p['main_image']) ?>"
                  class="product-img default"
                  width="300">

                <img src="uploads/products/<?= htmlspecialchars($p['image1'] ?: $p['main_image']) ?>"
                  class="product-img hover"
                  width="300">

                <?= productBadge($p['discount']) ?>

                <div class="showcase-actions">

                  <button class="btn-action wishlist-btn"
                    data-id="<?= $p['id'] ?>">

                    <ion-icon name="heart-outline"></ion-icon>

                  </button>

                  <button type="button" class="btn-action quick-view-btn" data-id="<?= $p['id'] ?>" title="Quick view">

                    <ion-icon name="eye-outline"></ion-icon>

                  </button>

                  <button class="btn-action cart-btn"
                    data-id="<?= $p['id'] ?>">

                    <ion-icon name="bag-add-outline"></ion-icon>

                  </button>

                  <button class="btn-action compare-btn"
                    data-id="<?= $p['id'] ?>">

                    <ion-icon name="repeat-outline"></ion-icon>

                  </button>

                </div>

              </div>

              <div class="showcase-content">

                <p class="showcase-category">
                  <?= htmlspecialchars($p['category_name']) ?>
                </p>

                <h3 class="showcase-title">
                  <?= htmlspecialchars($p['title']) ?>
                </h3>

                <div class="showcase-rating">
                  <?php showRating($p['rating']); ?>
                </div>

                <div class="price-box">

                  <p class="price">
                    ₹<?= format_price((float)$p['price']) ?>
                  </p>

                  <?php if (!empty($p['old_price']) && (float)$p['old_price'] > (float)$p['price']): ?>

                    <del>
                      ₹<?= format_price((float)$p['old_price']) ?>
                    </del>

                  <?php endif; ?>

                </div>

                <div class="tax-line">
                  Inclusive of All taxes
                </div>

                <div class="stock-line <?= (int)$p['stock'] > 0 ? 'stock-in' : 'stock-out' ?>">

                  <?= (int)$p['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>

                </div>

                <div class="buy-now-wrap">

                  <a href="product.php?id=<?= $p['id'] ?>"
                    class="buy-now-btn">

                    Buy Now

                  </a>

                </div>

              </div>

            </div>

          <?php endwhile; ?>

        <?php else: ?>

          <div class="alert alert-warning w-100">
            No products found.
          </div>

        <?php endif; ?>

      </div>

    </div>

  </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $('.wishlist-btn-old-disabled').click(function() {

    let pid = $(this).data('id');

    $.post('ajax/add_to_wishlist.php', {
      product_id: pid
    }, function(res) {

      if (res.trim() === 'login_required') {
        openAuthModal();
        return;
      }

      updateHeaderCounts();

      ecomToast('❤️ Added to Wishlist');

    });
  });

  $('.cart-btn-old-disabled').click(function() {

    let pid = $(this).data('id');

    $.post('ajax/add_to_cart.php', {
      product_id: pid
    }, function(res) {

      if (res.trim() === 'login_required') {
        openAuthModal();
        return;
      }

      updateHeaderCounts();

      ecomToast('👜 Added to Cart');

    });
  });

  $('.compare-btn-old-disabled').click(function() {

    let pid = $(this).data('id');

    $.post('ajax/add_to_compare.php', {
      product_id: pid
    }, function(res) {

      if (res.trim() === 'login_required') {
        openAuthModal();
        return;
      }

      if (res.trim() === 'limit') {

        ecomToast('Compare limit is 4 products');

        return;
      }

      updateHeaderCounts();

      ecomToast('✅ Added to Compare');

    });
  });

  const subcategories = <?= json_encode($subcategories) ?>;

  function loadSubcategories(categorySelector, subcategorySelector) {

    const category = document.querySelector(categorySelector);
    const subcategory = document.querySelector(subcategorySelector);

    if (!category || !subcategory) return;

    function renderSubcategories() {

      let categoryId = category.value;

      let currentSub =
        "<?= (int)($_GET['sub_id'] ?? 0) ?>";

      subcategory.innerHTML =
        '<option value="">All Subcategories</option>';

      if (categoryId && subcategories[categoryId]) {

        subcategories[categoryId].forEach(function(sub) {

          let selected =
            currentSub == sub.id ? 'selected' : '';

          subcategory.innerHTML += `
            <option value="${sub.id}" ${selected}>
              ${sub.name}
            </option>
          `;
        });
      }
    }

    renderSubcategories();

    category.addEventListener('change', function() {

      renderSubcategories();

    });
  }

  // DESKTOP
  loadSubcategories(
    '.category-select',
    '.subcategory-select'
  );

  // MOBILE
  loadSubcategories(
    '.mobile-category-select',
    '.mobile-subcategory-select'
  );
</script>