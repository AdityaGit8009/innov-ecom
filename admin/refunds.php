<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

$refunds = mysqli_query($conn, "
    SELECT o.id, o.order_no, o.total_amount, o.refund_status, o.refund_reason, o.created_at, u.name, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.refund_status != 'None'
    ORDER BY o.id DESC
");

include "includes/header.php";
?>
<div class="page-title-box mb-4">
    <h4><i class="bx bx-money-withdraw"></i> Refund Requests</h4>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100" id="refundsTable">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = mysqli_fetch_assoc($refunds)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['order_no']) ?></td>
                            <td><?= htmlspecialchars($r['name']) ?><br><small><?= htmlspecialchars($r['email']) ?></small></td>
                            <td>₹<?= number_format((float)$r['total_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($r['refund_reason']) ?></td>
                            <td>
                                <span class="badge bg-<?= $r['refund_status'] == 'Requested' ? 'warning' : ($r['refund_status'] == 'Approved' ? 'info' : ($r['refund_status'] == 'Refunded' ? 'success' : 'danger')) ?>">
                                    <?= htmlspecialchars($r['refund_status']) ?>
                                </span>
                            </td>
                            <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateRefundStatus(<?= $r['id'] ?>, this.value)">
                                    <option value="">Update...</option>
                                    <option value="Approved" <?= $r['refund_status']=='Approved'?'disabled':'' ?>>Approve</option>
                                    <option value="Rejected" <?= $r['refund_status']=='Rejected'?'disabled':'' ?>>Reject</option>
                                    <option value="Refunded" <?= $r['refund_status']=='Refunded'?'disabled':'' ?>>Mark Refunded</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function updateRefundStatus(orderId, status) {
    if (!status) return;
    if (confirm('Change refund status to ' + status + '?')) {
        $.post('ajax/update_refund.php', {order_id: orderId, status: status}, function(res) {
            try {
                let r = JSON.parse(res);
                if(r.status == 'success') location.reload();
                else alert(r.message);
            } catch(e) {
                alert('Server error processing update.');
            }
        });
    }
}
$(document).ready(function() {
    $('#refundsTable').DataTable({ order: [[0, 'desc']] });
});
</script>
<?php include "includes/footer.php"; ?>
<?php ob_end_flush(); ?>
