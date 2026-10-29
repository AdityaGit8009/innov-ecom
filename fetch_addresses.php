<?php
include "config/db.php";
include "jwt.php";

// Decode JWT
$user = null;
if (isset($_COOKIE['auth_token'])) {
    $user = JWT::decode($_COOKIE['auth_token']);
}
$user_id = $user['login_id'] ?? 0;
if (!$user_id) exit;

// Fetch addresses ordered by default first
$res = mysqli_query($conn, "SELECT * FROM customer_addresses WHERE customer_id='$user_id' ORDER BY is_default DESC, id DESC");

while ($row = mysqli_fetch_assoc($res)) {
?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="address-card p-3 h-100">
            <div class="d-flex justify-content-between">
                <h6 class="fw-bold"><?= htmlspecialchars($row['name']); ?></h6>
                <?= $row['is_default'] ? '<span class="badge bg-success">Default</span>' : '' ?>
            </div>
            <p class="text-muted"><?= htmlspecialchars($row['type']); ?></p>
            <p><?= htmlspecialchars($row['city']); ?>, <?= htmlspecialchars($row['state']); ?> - <?= htmlspecialchars($row['pincode']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']); ?></p>
            <button class="btn btn-sm btn-outline-danger" onclick="removeAddress(<?= $row['id'] ?>)">Remove</button>
        </div>
    </div>
<?php } ?>