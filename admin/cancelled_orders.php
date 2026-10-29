<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

$orders = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE order_status = 'Cancelled'
    ORDER BY id DESC
");
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

  #cancelledOrdersTable {
    width: 100% !important;
    font-size: 14px;
  }

  #cancelledOrdersTable thead th {
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

  #cancelledOrdersTable tbody td {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px;
    color: #64748b;
    border-color: #e5e7eb;
  }

  #cancelledOrdersTable tbody tr:hover {
    background: #f8fafc;
  }

  .order-no {
    font-weight: 800;
    color: #334155;
  }

  .order-invoice {
    font-size: 12px;
    color: #94a3b8;
    display: block;
    margin-top: 3px;
  }

  .customer-name {
    font-weight: 700;
    color: #334155;
  }

  .customer-phone {
    font-size: 12px;
    color: #64748b;
  }

  .amount-badge {
    background: #fee2e2;
    color: #991b1b;
    padding: 6px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
  }

  .badge-soft-danger {
    background: #fee2e2;
    color: #991b1b;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
  }

  .badge-soft-warning {
    background: #fef3c7;
    color: #92400e;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
  }

  .badge-soft-success {
    background: #dcfce7;
    color: #166534;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
  }

  .badge-soft-info {
    background: #e0f2fe;
    color: #075985;
    padding: 7px 10px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    font-size: 12px;
  }

  .reason-text {
    max-width: 220px;
    white-space: normal;
    line-height: 1.5;
    color: #64748b;
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

  .empty-box {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    padding: 45px;
    text-align: center;
    color: #64748b;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.06);
  }

  .empty-box h5 {
    color: #334155;
    font-weight: 800;
    margin-bottom: 8px;
  }

  .btn-all-orders {
    border-radius: 10px;
    font-weight: 700;
    padding: 8px 14px;
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
    <i class="bx bx-x-circle"></i> Cancelled Orders
  </h4>

  <a href="orders.php" class="btn btn-primary btn-sm btn-all-orders">
    <i class="bx bx-list-ul"></i> All Orders
  </a>
</div>

<?php if (!$orders || mysqli_num_rows($orders) == 0): ?>

  <div class="empty-box">
    <h5>No cancelled orders found</h5>
    <p class="mb-0">Admin or customer cancelled orders will appear here.</p>
  </div>

<?php else: ?>

  <div class="card admin-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle datatable w-100"
          id="cancelledOrdersTable"
          data-module="cancelled orders">
          <thead>
            <tr class="text-center">
              <th>Sr. No.</th>
              <th>Order No</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Cancelled By</th>
              <th>Cancelled On</th>
              <th>Reason</th>
              <th>Date</th>
              <th width="80">Action</th>
            </tr>
          </thead>

          <tbody>
            <?php
            $i = 1;
            while ($row = mysqli_fetch_assoc($orders)):
              $paymentStatus = $row['payment_status'] ?? 'Pending';

              $paymentClass = 'warning';
              if ($paymentStatus == 'Paid') {
                $paymentClass = 'success';
              } elseif ($paymentStatus == 'Failed') {
                $paymentClass = 'danger';
              } elseif ($paymentStatus == 'Refunded') {
                $paymentClass = 'info';
              }
            ?>
              <tr>
                <td class="text-center"><?= $i++ ?></td>

                <td>
                  <span class="order-no">
                    <?= htmlspecialchars($row['order_no'] ?? '') ?>
                  </span>
                  <span class="order-invoice">
                    Inv: <?= htmlspecialchars($row['invoice_no'] ?? 'N/A') ?>
                  </span>
                </td>

                <td>
                  <span class="customer-name">
                    <?= htmlspecialchars($row['customer_name'] ?? '') ?>
                  </span>
                  <br>
                  <span class="customer-phone">
                    <?= htmlspecialchars($row['customer_phone'] ?? '') ?>
                  </span>
                  <?php if (!empty($row['customer_email'])): ?>
                    <br>
                    <small class="text-muted">
                      <?= htmlspecialchars($row['customer_email']) ?>
                    </small>
                  <?php endif; ?>
                </td>

                <td class="text-center">
                  <span class="amount-badge">
                    ₹<?= format_price((float)($row['total_amount'] ?? 0)) ?>
                  </span>
                </td>

                <td class="text-center">
                  <span class="badge-soft-<?= $paymentClass ?>">
                    <?= htmlspecialchars($paymentStatus) ?>
                  </span>
                  <br>
                  <small><?= htmlspecialchars($row['payment_method'] ?? '') ?></small>
                </td>

                <td class="text-center">
                  <span class="badge-soft-danger">
                    <?= htmlspecialchars($row['order_status'] ?? 'Cancelled') ?>
                  </span>
                </td>

                <td class="text-center">
                  <?= htmlspecialchars($row['cancelled_by'] ?? 'N/A') ?>
                </td>

                <td class="text-center">
                  <?= !empty($row['cancelled_at']) ? date('d M Y, h:i A', strtotime($row['cancelled_at'])) : 'N/A' ?>
                </td>

                <td>
                  <div class="reason-text">
                    <?= !empty($row['cancel_reason']) ? htmlspecialchars($row['cancel_reason']) : 'N/A' ?>
                  </div>
                </td>

                <td class="text-center">
                  <?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '' ?>
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
                        <a class="dropdown-item"
                          href="order_view.php?id=<?= (int)$row['id'] ?>">
                          <i class="bx bx-show"></i> View
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>

        </table>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php include "includes/footer.php"; ?>

<?php ob_end_flush(); ?>