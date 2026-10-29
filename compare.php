<?php
include 'header.php';
include "config/db.php";
require_once "secure_helpers.php";

$user_id = ecom_require_user();

/* FETCH COMPARE PRODUCTS */
$q = mysqli_query(
  $conn,
  "SELECT 
        cmp.id AS compare_id,
        p.id AS product_id,
        p.title,
        p.price,
        p.old_price,
        p.main_image,
        p.rating,
        p.stock,
        cat.name AS category_name
    FROM compare cmp
    JOIN products p ON p.id = cmp.product_id
    LEFT JOIN categories cat ON cat.id = p.category_id
    WHERE cmp.user_id = '$user_id'"
);

$compare_count = mysqli_num_rows($q);
?>
<style>
  table thead th {
    vertical-align: middle !important;
  }

  table thead th img {
    display: block;
    margin: 0 auto 10px;
  }
</style>

<section class="breadcrumb-section">
  <h1>COMPARE PRODUCTS</h1>
  <p class="breadcrumb">
    <a href="index.php">Home</a> <span>| Compare</span>
  </p>
</section>

<div class="cart-main">
  <div class="container my-5">

    <?php if ($compare_count == 0): ?>
      <p class="text-center text-muted">Your compare list is empty.</p>
    <?php else: ?>
      <table class="table align-middle text-center table-striped table-bordered table-responsive">
        <thead class="table-light">
          <tr>
            <th>Product Name</th>
            <?php while ($p = mysqli_fetch_assoc($q)): ?>
              <th data-col="<?= $p['compare_id']; ?>">
                <img src="uploads/products/<?= $p['main_image']; ?>" width="100"><br>
                <b><?= $p['title']; ?></b>
              </th>
            <?php endwhile; ?>
          </tr>
        </thead>
        <tbody>
          <!-- PRICE -->
          <tr>
            <td><b>Price</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td>₹<?= number_format($p['price']); ?></td>
            <?php endwhile; ?>
          </tr>

          <!-- OLD PRICE -->
          <tr>
            <td><b>MRP</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td><?= $p['old_price'] > 0 ? '₹' . number_format($p['old_price']) : '-' ?></td>
            <?php endwhile; ?>
          </tr>

          <!-- CATEGORY -->
          <tr>
            <td><b>Category</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td><?= $p['category_name']; ?></td>
            <?php endwhile; ?>
          </tr>

          <!-- RATING -->
          <tr>
            <td><b>Rating</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td><?= $p['rating']; ?> ⭐</td>
            <?php endwhile; ?>
          </tr>

          <!-- STOCK -->
          <tr>
            <td><b>Availability</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td><?= $p['stock'] > 0 ? '<span class="text-success">In Stock</span>' : '<span class="text-danger">Out of Stock</span>' ?></td>
            <?php endwhile; ?>
          </tr>

          <!-- ACTIONS -->
          <tr>
            <td><b>Action</b></td>
            <?php mysqli_data_seek($q, 0);
            while ($p = mysqli_fetch_assoc($q)): ?>
              <td data-col="<?= $p['compare_id']; ?>">
                <a href="product.php?id=<?= $p['product_id']; ?>" class="btn btn-sm btn-primary mb-1">View</a><br>

                <button class="btn btn-sm btn-success add-cart" data-id="<?= $p['product_id']; ?>">
                  Add to Cart
                </button>

                <button class="btn btn-sm btn-outline-danger remove-compare mt-1" data-id="<?= $p['compare_id']; ?>">Remove</button>
              </td>
            <?php endwhile; ?>
          </tr>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  function showComparePopup(count) {
    $('body').append(`
      <div id="comparePopup" style="
        position: fixed; bottom: 20px; right: 20px; 
        background: var(--white); border: 1px solid var(--cultured); padding: 15px; 
        box-shadow: 0 2px 10px rgba(0,0,0,.2); z-index: 9999;">
        <p>Compare list updated. Total: <b>${count}</b> products.</p>
        <a href="compare.php" class="btn btn-sm btn-primary">Go to Compare</a>
      </div>
    `);

    setTimeout(() => $('#comparePopup').fadeOut(300, function() {
      $(this).remove();
    }), 3000);
  }
  $('.compare-btn').click(function() {

    let pid = $(this).data('id');
    let btn = $(this);

    $.post("ajax/add_to_compare.php", {
      product_id: pid
    }, function(res) {

      res = (res || '').trim();
      if (res === 'login_required') {
        alert('Please login first');
        location.href = 'login.php';
        return;
      }

      if (res === 'success' || res === 'limit') {
        updateHeaderCounts();
        btn.find('ion-icon').css('color', 'var(--salmon-pink)');
        if (res === 'limit') alert('Compare list limit is 4 products.');
      }
    });
  });

  /* ======================
  REMOVE FROM COMPARE
  ====================== */
  $('.remove-compare').click(function() {
    let btn = $(this);
    let compareId = btn.data('id');

    $.post('remove_compare.php', {
      compare_id: compareId
    }, function(res) {

      // pura column remove
      let colIndex = btn.closest('td').index();
      $('table tr').each(function() {
        $(this).find('td,th').eq(colIndex).remove();
      });

      updateHeaderCounts(); // 🔥 compare count update

      // agar ab sirf "Feature" column bacha hai, matlab table empty
      if ($('table thead th').length <= 1) {
        $('.cart-main .container').html('<p class="text-center text-muted">Your compare list is empty.</p>');
      }
    });
  });

  /* ======================
  HEADER COUNT UPDATE
  ====================== */
  function updateHeaderCounts() {
    $.getJSON('get_counts.php', function(res) {
      $('#cartCount').text(res.cart);
      $('#wishlistCount').text(res.wishlist);
      $('#compareCount').text(res.compare);
    });
  }
</script>
<script>
  $(document).on('click', '.add-cart', function() {

    let pid = $(this).data('id');
    let btn = $(this);

    $.post('ajax/add_to_cart.php', {
      product_id: pid,
      quantity: 1
    }, function(res) {

      res = (res || '').trim();

      if (res === 'login_required') {
        alert('Please login first');
        window.location.href = 'login.php';
        return;
      }

      if (res.startsWith('success')) {
        const parts = res.split(':');
        const qtyStr = parts.length > 1 ? ` (${parts[1]})` : '';
        btn.text('Added' + qtyStr);
        btn.prop('disabled', true);

        updateHeaderCounts();

        alert('Product added to cart' + qtyStr);
      } else {
        alert(res);
      }

    });

  });
</script>