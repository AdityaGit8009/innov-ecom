<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== STATUS UPDATE ===== */
if (isset($_GET['status'], $_GET['id'])) {
  $id = (int)$_GET['id'];
  $status = (int)$_GET['status'] === 1 ? 1 : 0;

  mysqli_query($conn, "
        UPDATE product_reviews 
        SET status = '$status' 
        WHERE id = '$id'
    ");

  header("Location: product_reviews.php?msg=ok");
  exit;
}

/* ===== DELETE REVIEW ===== */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  mysqli_query($conn, "
        DELETE FROM product_reviews 
        WHERE id = '$id'
    ");

  header("Location: product_reviews.php?msg=deleted");
  exit;
}

/* ===== FETCH REVIEWS ===== */
$rows = mysqli_query($conn, "
    SELECT 
        pr.*,
        p.title AS product_title,
        u.name AS user_name
    FROM product_reviews pr
    LEFT JOIN products p ON p.id = pr.product_id
    LEFT JOIN users u ON u.id = pr.user_id
    ORDER BY pr.id DESC
");

include "includes/header.php";
?>
<style>
  .page-title-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
  }

  .page-title-box h4 {
    margin: 0;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .admin-card {
    background: #ffffff;
    border: 0;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);

    /* IMPORTANT */
    overflow: visible !important;
  }

  .admin-card .card-body {
    padding: 22px;

    /* IMPORTANT */
    overflow: visible !important;
  }

  .table-responsive {
    overflow-x: auto !important;
    overflow-y: visible !important;
  }

  #productReviewsTable {
    width: 100% !important;
    font-size: 14px;
  }

  #productReviewsTable thead th {
    background: #fff3ec !important;
    color: #2b160b !important;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
    padding: 14px 12px;
    border-color: #ead8cd;
  }

  #productReviewsTable tbody td {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px;
    color: #64748b;
    border-color: #e5e7eb;

    /* IMPORTANT */
    overflow: visible !important;
  }

  #productReviewsTable tbody tr {
    overflow: visible !important;
  }

  #productReviewsTable tbody tr:hover {
    background: #f8fafc;
  }

  .product-title {
    font-weight: 800;
    color: #334155;
    max-width: 240px;
    white-space: normal;
    line-height: 1.4;
  }

  .customer-name {
    font-weight: 700;
    color: #334155;
  }

  .rating-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 7px 11px;
    border-radius: 30px;
    font-weight: 800;
    display: inline-block;
    font-size: 13px;
    white-space: nowrap;
  }

  .review-text {
    max-width: 320px;
    white-space: normal;
    line-height: 1.5;
    color: #64748b;
  }

  .badge-soft {
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
    text-decoration: none;
    white-space: nowrap;
  }

  .badge-soft-success {
    background: #dcfce7;
    color: #166534;
  }

  .badge-soft-warning {
    background: #fef3c7;
    color: #92400e;
  }

  .action-dropdown {
    position: relative;
  }

  .action-dropdown .btn {
    border: 0;
    background: transparent;
    color: #334155;
    font-size: 20px;
    padding: 4px 8px;
    border-radius: 8px;
  }

  .action-dropdown .btn:hover,
  .action-dropdown .btn:focus {
    background: #f1f5f9;
    color: #0f172a;
    box-shadow: none;
  }

  .action-dropdown .dropdown-toggle::after {
    display: none;
  }

  .action-dropdown .dropdown-menu {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
    padding: 8px;
    min-width: 165px;

    /* IMPORTANT FIX */
    position: absolute !important;
    inset: auto 0 auto auto !important;
    transform: none !important;
    z-index: 999999 !important;
  }

  .action-dropdown .dropdown-item {
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #334155;
  }

  .action-dropdown .dropdown-item:hover {
    background: #f8fafc;
  }

  .action-dropdown .dropdown-item.text-danger:hover {
    background: #fee2e2;
    color: #991b1b !important;
  }

  @media (max-width: 767px) {
    .admin-card .card-body {
      padding: 14px;
    }

    .page-title-box h4 {
      font-size: 18px;
    }

    .review-text {
      max-width: 240px;
    }
  }
</style>

<div class="page-title-box">
  <h4>
    <i class="bx bx-star"></i> Product Reviews & Ratings
  </h4>
</div>

<div class="card admin-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle datatable w-100"
        id="productReviewsTable"
        data-module="product reviews" data-action-dropdown="1">

        <thead>
          <tr class="text-center">
            <th>Sr. No.</th>
            <th>Product</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Status</th>
            <th width="80">Action</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $i = 1;
          while ($r = mysqli_fetch_assoc($rows)) {
            $customerName = $r['user_name'] ?: ($r['customer_name'] ?? '-');
            $status = (int)($r['status'] ?? 0);
          ?>
            <tr>
              <td class="text-center"><?= $i++ ?></td>

              <td>
                <div class="product-title">
                  <?= htmlspecialchars($r['product_title'] ?? 'N/A') ?>
                </div>
              </td>

              <td>
                <span class="customer-name">
                  <?= htmlspecialchars($customerName) ?>
                </span>
              </td>

              <td class="text-center">
                <span class="rating-badge">
                  <?= (int)($r['rating'] ?? 0) ?> ★
                </span>
              </td>

              <td>
                <div class="review-text">
                  <?= htmlspecialchars($r['review'] ?? '') ?>
                </div>
              </td>

              <td class="text-center">
                <?php if ($status === 1): ?>
                  <span class="badge-soft badge-soft-success">Approved</span>
                <?php else: ?>
                  <span class="badge-soft badge-soft-warning">Pending</span>
                <?php endif; ?>
              </td>

              <td class="text-center">

                <a href="?id=<?= (int)$r['id'] ?>&status=1"
                  class="btn btn-success btn-sm mb-1">
                  <i class="bx bx-check"></i>Approved
                </a>

                <a href="?id=<?= (int)$r['id'] ?>&status=0"
                  class="btn btn-warning btn-sm mb-1">
                  <i class="bx bx-time"></i>Pending
                </a>

                <a href="?delete=<?= (int)$r['id'] ?>"
                  onclick="return confirm('Delete review?')"
                  class="btn btn-danger btn-sm mb-1">
                  <i class="bx bx-trash"></i>Delete
                </a>

              </td>
            </tr>
          <?php } ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
  $msg = $_GET['msg'];

  if ($msg === 'ok') {
    echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Review status updated successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Review status updated successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
  } elseif ($msg === 'deleted') {
    echo "<script>
            if (typeof showToast === 'function') {
                showToast('warning', 'Review deleted successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Review deleted successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
  }
}

ob_end_flush();
?>