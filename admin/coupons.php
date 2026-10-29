<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
  return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== ADD / UPDATE COUPON ===== */
if (isset($_POST['save'])) {
  $id = (int)($_POST['id'] ?? 0);

  $title = admin_safe($conn, $_POST['title'] ?? '');
  $code = admin_safe($conn, $_POST['code'] ?? '');
  $discount_type = admin_safe($conn, $_POST['discount_type'] ?? 'percent');

  $discount_value = (float)($_POST['discount_value'] ?? 0);
  $product_id = (int)($_POST['product_id'] ?? 0);
  $min_order_amount = (float)($_POST['min_order_amount'] ?? 0);
  $status = (int)($_POST['status'] ?? 1);

  if ($title == '' || $code == '') {
    header("Location: coupons.php?msg=error");
    exit;
  }

  if (!in_array($discount_type, ['percent', 'flat'], true)) {
    $discount_type = 'percent';
  }

  $status = $status === 1 ? 1 : 0;

  if ($id > 0) {
    $res = mysqli_query($conn, "
            UPDATE coupons SET 
                title = '$title',
                code = '$code',
                discount_type = '$discount_type',
                discount_value = '$discount_value',
                product_id = '$product_id',
                min_order_amount = '$min_order_amount',
                status = '$status'
            WHERE id = '$id'
        ");
  } else {
    $res = mysqli_query($conn, "
            INSERT INTO coupons
            (
                title,
                code,
                discount_type,
                discount_value,
                product_id,
                min_order_amount,
                status
            )
            VALUES
            (
                '$title',
                '$code',
                '$discount_type',
                '$discount_value',
                '$product_id',
                '$min_order_amount',
                '$status'
            )
        ");
  }

  header("Location: coupons.php?msg=" . ($res ? "ok" : "error"));
  exit;
}

/* ===== DELETE COUPON ===== */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  mysqli_query($conn, "
        DELETE FROM coupons 
        WHERE id = '$id'
    ");

  header("Location: coupons.php?msg=deleted");
  exit;
}

/* ===== FETCH DATA ===== */
$rows = mysqli_query($conn, "
    SELECT 
        c.*, 
        p.title AS product_title
    FROM coupons c
    LEFT JOIN products p ON p.id = c.product_id
    ORDER BY c.id DESC
");

$products = mysqli_query($conn, "
    SELECT id, title 
    FROM products 
    ORDER BY title ASC
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
    overflow: hidden;
  }

  .admin-card .card-body {
    padding: 22px;
  }

  #couponsTable {
    width: 100% !important;
    font-size: 14px;
  }

  #couponsTable thead th {
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

  #couponsTable tbody td {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px;
    color: #64748b;
    border-color: #e5e7eb;
  }

  #couponsTable tbody tr:hover {
    background: #f8fafc;
  }

  .coupon-title {
    font-weight: 800;
    color: #334155;
  }

  .coupon-code {
    background: #fef3c7;
    color: #92400e;
    padding: 7px 11px;
    border-radius: 30px;
    font-weight: 800;
    display: inline-block;
    font-size: 12px;
    letter-spacing: 0.5px;
  }

  .type-badge {
    background: #e0f2fe;
    color: #075985;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
    text-transform: capitalize;
  }

  .value-badge {
    background: #dcfce7;
    color: #166534;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 800;
    display: inline-block;
    font-size: 13px;
  }

  .product-text {
    max-width: 260px;
    white-space: normal;
    line-height: 1.5;
    color: #64748b;
    font-weight: 600;
  }

  .min-order-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
  }

  .badge-soft {
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
    text-decoration: none;
  }

  .badge-soft-success {
    background: #dcfce7;
    color: #166534;
  }

  .badge-soft-danger {
    background: #fee2e2;
    color: #991b1b;
  }

  .btn-add-coupon {
    border-radius: 10px;
    font-weight: 700;
    padding: 8px 14px;
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
    min-width: 150px;
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

  .coupon-modal .modal-content {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
  }

  .coupon-modal .modal-header {
    background: linear-gradient(135deg, #ff6a00, #ff3d00);
    color: #fff;
    border: 0;
    padding: 18px 22px;
      border-radius: 18px 18px 0 0;
}

  .coupon-modal .modal-title {
    font-weight: 800;
    font-size: 18px;
  }

  .coupon-modal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 1;
  }

  .coupon-modal .modal-body {
    padding: 22px;
    background: #fff;
  }

  .coupon-modal .form-label {
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
    font-size: 14px;
  }

  .coupon-modal .form-control,
  .coupon-modal .form-select {
    min-height: 46px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    box-shadow: none;
    font-size: 14px;
  }

  .coupon-modal .form-control:focus,
  .coupon-modal .form-select:focus {
    border-color: #ff5a1f;
    box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
  }

  .coupon-modal .modal-footer {
    border: 0;
    padding: 0 22px 22px;
  }

  .btn-save-coupon {
    background: #ff5a1f;
    color: #fff;
    border: 0;
    border-radius: 10px;
    padding: 11px 24px;
    font-weight: 800;
  }

  .btn-save-coupon:hover {
    background: #e94d14;
    color: #fff;
  }

  @media (max-width: 767px) {
    .admin-card .card-body {
      padding: 14px;
    }

    .page-title-box h4 {
      font-size: 18px;
    }
  }
</style>

<div class="page-title-box">
  <h4>
    <i class="bx bx-purchase-tag"></i> Coupons & Offers
  </h4>

  <button class="btn btn-primary btn-add-coupon" onclick="openCoupon({})">
    <i class="bx bx-plus"></i> Add Coupon
  </button>
</div>

<div class="card admin-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle datatable w-100"
        id="couponsTable"
        data-module="coupons">

        <thead>
          <tr class="text-center">
            <th>Sr. No.</th>
            <th>Title</th>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Product</th>
            <th>Min Order</th>
            <th>Status</th>
            <th width="80">Action</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $i = 1;
          while ($r = mysqli_fetch_assoc($rows)) {
            $couponJson = htmlspecialchars(
              json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
              ENT_QUOTES,
              'UTF-8'
            );

            $discountType = $r['discount_type'] ?? 'percent';
            $discountValue = (float)($r['discount_value'] ?? 0);

            if ($discountType === 'percent') {
              $valueText = format_price($discountValue) . "%";
            } else {
              $valueText = "₹" . format_price($discountValue);
            }
          ?>
            <tr>
              <td class="text-center"><?= $i++ ?></td>

              <td>
                <span class="coupon-title">
                  <?= htmlspecialchars($r['title'] ?? '') ?>
                </span>
              </td>

              <td class="text-center">
                <span class="coupon-code">
                  <?= htmlspecialchars($r['code'] ?? '') ?>
                </span>
              </td>

              <td class="text-center">
                <span class="type-badge">
                  <?= htmlspecialchars($discountType) ?>
                </span>
              </td>

              <td class="text-center">
                <span class="value-badge">
                  <?= $valueText ?>
                </span>
              </td>

              <td>
                <div class="product-text">
                  <?= htmlspecialchars($r['product_title'] ?: 'All Products') ?>
                </div>
              </td>

              <td class="text-center">
                <span class="min-order-badge">
                  ₹<?= format_price((float)($r['min_order_amount'] ?? 0)) ?>
                </span>
              </td>

              <td class="text-center">
                <span class="badge-soft <?= !empty($r['status']) ? 'badge-soft-success' : 'badge-soft-danger' ?>">
                  <?= !empty($r['status']) ? 'Active' : 'Inactive' ?>
                </span>
              </td>

              <td class="text-center">
                <div class="dropdown action-dropdown">
                  <button class="btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>

                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <button class="dropdown-item"
                        type="button"
                        onclick='openCoupon(<?= $couponJson ?>)'>
                        <i class="bx bx-edit"></i> Edit
                      </button>
                    </li>

                    <li>
                      <a href="?delete=<?= (int)$r['id'] ?>"
                        onclick="return confirm('Delete coupon?')"
                        class="dropdown-item text-danger">
                        <i class="bx bx-trash"></i> Delete
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<!-- COUPON MODAL -->
<div class="modal fade coupon-modal" id="couponModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content" id="couponForm">
      <div class="modal-header">
        <h5 class="modal-title" id="couponModalTitle">Add Coupon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="cid">

        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control" name="title" id="ctitle" placeholder="Example: New Year Offer" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Code</label>
          <input class="form-control" name="code" id="ccode" placeholder="Example: SAVE20" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Product</label>
          <select class="form-select" name="product_id" id="cproduct">
            <option value="0">All Products</option>
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <option value="<?= (int)$p['id'] ?>">
                <?= htmlspecialchars($p['title'] ?? '') ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Discount Type</label>
            <select class="form-select" name="discount_type" id="ctype">
              <option value="percent">Percent</option>
              <option value="flat">Flat</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Discount Value</label>
            <input class="form-control" type="number" step="0.01" min="0" name="discount_value" id="cvalue">
          </div>

          <div class="col-md-6">
            <label class="form-label">Min Order Amount</label>
            <input class="form-control" type="number" step="0.01" min="0" name="min_order_amount" id="cmin">
          </div>

          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="cstatus">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          Cancel
        </button>

        <button class="btn btn-save-coupon" name="save">
          <i class="bx bx-save"></i> Save Coupon
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openCoupon(c) {
    document.getElementById('couponForm').reset();

    document.getElementById('cid').value = c.id || '';
    document.getElementById('ctitle').value = c.title || '';
    document.getElementById('ccode').value = c.code || '';
    document.getElementById('cproduct').value = c.product_id || 0;
    document.getElementById('ctype').value = c.discount_type || 'percent';
    document.getElementById('cvalue').value = c.discount_value || '';
    document.getElementById('cmin').value = c.min_order_amount || '';
    document.getElementById('cstatus').value = c.status ?? 1;

    document.getElementById('couponModalTitle').innerText = c.id ? 'Edit Coupon' : 'Add Coupon';

    new bootstrap.Modal(document.getElementById('couponModal')).show();
  }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
  $msg = $_GET['msg'];

  if ($msg === 'ok') {
    echo "<script>
            if (typeof showToast === 'function') {
                showToast('success', 'Coupon saved successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Coupon saved successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
  } elseif ($msg === 'deleted') {
    echo "<script>
            if (typeof showToast === 'function') {
                showToast('warning', 'Coupon deleted successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Coupon deleted successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
  } else {
    echo "<script>
            if (typeof showToast === 'function') {
                showToast('error', 'Something went wrong!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
  }
}

ob_end_flush();
?>