<?php
session_start();

include 'config/db.php';
require_once 'secure_helpers.php';
include 'order_helpers.php';

/* =====================================================
   AUTH + USER RESOLVE
   Fix: session id login table ka ho ya users table ka,
   final $user_id always users.id hoga.
===================================================== */
$auth_id = (int) ecom_require_user();

function sip_get_user_by_auth($conn, $auth_id)
{
    $auth_id = (int) $auth_id;

    $u = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM users 
        WHERE id = '$auth_id' 
        LIMIT 1
    "));

    if ($u) {
        return $u;
    }

    $login = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM login 
        WHERE id = '$auth_id' 
        AND role = 'user' 
        LIMIT 1
    "));

    if ($login) {
        $email = mysqli_real_escape_string($conn, $login['login_id'] ?? '');
        $mobile = mysqli_real_escape_string($conn, $login['mobile'] ?? '');

        $u = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * FROM users 
            WHERE email = '$email' 
            OR mobile = '$mobile' 
            LIMIT 1
        "));

        if ($u) {
            return $u;
        }
    }

    return null;
}

$user = sip_get_user_by_auth($conn, $auth_id);

if (!$user) {
    echo "<script>alert('User profile not found. Please login again.'); location.href='logout.php';</script>";
    exit;
}

$user_id = (int) $user['id'];

$userName = trim($user['name'] ?? '');
$userEmail = trim($user['email'] ?? '');
$userMobile = trim($user['mobile'] ?? '');

if ($userName === '') {
    $userName = 'Customer';
}

if ($userEmail === '') {
    $userEmail = '';
}

$hasGender = ecom_table_has_column($conn, 'users', 'gender');
$hasImage = ecom_table_has_column($conn, 'users', 'image');

/* =====================================================
   ACTIVE TAB
===================================================== */
$allowedTabs = ['profile', 'password', 'addresses', 'latest', 'previous', 'cancelled', 'track', 'wishlist', 'compare'];
$tab = $_GET['tab'] ?? 'profile';

if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'profile';
}

/* =====================================================
   ADDRESS DELETE MESSAGE
===================================================== */
$addressMsg = $_SESSION['address_msg'] ?? '';
$addressMsgType = $_SESSION['address_msg_type'] ?? '';

unset($_SESSION['address_msg'], $_SESSION['address_msg_type']);

/* =====================================================
   UPDATE PROFILE AJAX
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    header('Content-Type: application/json');

    $oldUser = sip_get_user_by_auth($conn, $auth_id);

    if (!$oldUser) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($oldUser['email'] ?? '');
    $mobile = trim($oldUser['mobile'] ?? '');
    $gender = trim($oldUser['gender'] ?? '');

    if ($name === '') {
        echo json_encode(['status' => 'error', 'message' => 'Name required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Enter a valid email address']);
        exit;
    }

    $nameEsc = mysqli_real_escape_string($conn, $name);
    $emailEsc = mysqli_real_escape_string($conn, $email);
    $mobileEsc = mysqli_real_escape_string($conn, $mobile);
    $realUserId = (int) $oldUser['id'];

    $dup = mysqli_query($conn, "
        SELECT id 
        FROM users 
        WHERE id <> '$realUserId' 
        AND (email = '$emailEsc' OR mobile = '$mobileEsc') 
        LIMIT 1
    ");

    if ($dup && mysqli_num_rows($dup) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email or mobile already exists']);
        exit;
    }

    // Profile edit se sirf name update hoga. Email/mobile/gender locked rahenge.
    $fields = "name='$nameEsc'";

    $q = mysqli_query($conn, "
        UPDATE users 
        SET $fields 
        WHERE id = '$realUserId'
    ");

    if ($q) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated',
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'gender' => $gender
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Profile not updated']);
    }

    exit;
}

/* =====================================================
   CHANGE PASSWORD AJAX
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    header('Content-Type: application/json');

    $old = trim($_POST['old_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');

    if ($old === '' || $new === '') {
        echo json_encode(['status' => 'error', 'message' => 'Both password fields required']);
        exit;
    }

    if (strlen($new) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password minimum 6 characters']);
        exit;
    }

    $loginId = mysqli_real_escape_string($conn, $user['email'] ?? '');
    $mobile = mysqli_real_escape_string($conn, $user['mobile'] ?? '');

    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM login 
        WHERE role = 'user' 
        AND (login_id = '$loginId' OR mobile = '$mobile') 
        LIMIT 1
    "));

    if (!$row || !password_verify($old, $row['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Old password is wrong']);
        exit;
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $q = mysqli_query($conn, "UPDATE login SET password='$hash' WHERE id='{$row['id']}'");

    echo json_encode(
        $q
            ? ['status' => 'success', 'message' => 'Password updated']
            : ['status' => 'error', 'message' => 'Password not changed']
    );

    exit;
}

/* =====================================================
   UPDATE PROFILE IMAGE AJAX
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile_image') {
    header('Content-Type: application/json');

    if (!$hasImage) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Image column missing in users table'
        ]);
        exit;
    }

    if (empty($_FILES['profile_image']['name'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please select image']);
        exit;
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    $fileName = $_FILES['profile_image']['name'];
    $fileTmp = $_FILES['profile_image']['tmp_name'];
    $fileSize = (int) $_FILES['profile_image']['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, WEBP allowed']);
        exit;
    }

    if ($fileSize > 2 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Image size must be less than 2MB']);
        exit;
    }

    $uploadDir = 'uploads/profile/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        if (
            !empty($user['image']) &&
            file_exists($user['image']) &&
            $user['image'] !== './assets/images/office-man.webp'
        ) {
            @unlink($user['image']);
        }

        $safePath = mysqli_real_escape_string($conn, $uploadPath);

        $q = mysqli_query($conn, "
            UPDATE users 
            SET image = '$safePath' 
            WHERE id = '$user_id'
        ");

        echo json_encode(
            $q
                ? [
                    'status' => 'success',
                    'message' => 'Profile image updated',
                    'image' => $uploadPath
                ]
                : [
                    'status' => 'error',
                    'message' => 'Image not updated'
                ]
        );

        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
    exit;
}

/* =====================================================
   DELETE ADDRESS
   User sirf apna address delete kar payega.
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_address') {
    $address_id = (int) ($_POST['address_id'] ?? 0);

    if ($address_id <= 0) {
        $_SESSION['address_msg'] = 'Invalid address selected.';
        $_SESSION['address_msg_type'] = 'danger';
        header('Location: user_profile.php?tab=addresses');
        exit;
    }

    $addressQ = mysqli_query($conn, "
        SELECT id, is_default 
        FROM customer_addresses 
        WHERE id = '$address_id' 
        AND customer_id = '$user_id' 
        LIMIT 1
    ");

    if (!$addressQ || mysqli_num_rows($addressQ) === 0) {
        $_SESSION['address_msg'] = 'Address not found or access denied.';
        $_SESSION['address_msg_type'] = 'danger';
        header('Location: user_profile.php?tab=addresses');
        exit;
    }

    $addressRow = mysqli_fetch_assoc($addressQ);
    $wasDefault = !empty($addressRow['is_default']);

    $deleteQ = mysqli_query($conn, "
        DELETE FROM customer_addresses 
        WHERE id = '$address_id' 
        AND customer_id = '$user_id'
    ");

    if ($deleteQ) {
        if ($wasDefault) {
            $nextAddressQ = mysqli_query($conn, "
                SELECT id 
                FROM customer_addresses 
                WHERE customer_id = '$user_id' 
                ORDER BY id DESC 
                LIMIT 1
            ");

            if ($nextAddressQ && mysqli_num_rows($nextAddressQ) > 0) {
                $nextAddress = mysqli_fetch_assoc($nextAddressQ);
                $nextAddressId = (int) $nextAddress['id'];

                mysqli_query($conn, "
                    UPDATE customer_addresses 
                    SET is_default = 0 
                    WHERE customer_id = '$user_id'
                ");

                mysqli_query($conn, "
                    UPDATE customer_addresses 
                    SET is_default = 1 
                    WHERE id = '$nextAddressId' 
                    AND customer_id = '$user_id'
                ");
            }
        }

        $_SESSION['address_msg'] = 'Address deleted successfully.';
        $_SESSION['address_msg_type'] = 'success';
    } else {
        $_SESSION['address_msg'] = 'Address not deleted. Please try again.';
        $_SESSION['address_msg_type'] = 'danger';
    }

    header('Location: user_profile.php?tab=addresses');
    exit;
}

/* =====================================================
   PAGE DATA
===================================================== */
$user = sip_get_user_by_auth($conn, $auth_id);
$user_id = (int) $user['id'];

$addresses = mysqli_query($conn, "
    SELECT * 
    FROM customer_addresses 
    WHERE customer_id = '$user_id' 
    ORDER BY is_default DESC, id DESC
");

$ordersCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) cnt 
    FROM orders 
    WHERE user_id = '$user_id'
"))['cnt'] ?? 0;

$activeOrdersCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) cnt 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status != 'Cancelled'
"))['cnt'] ?? 0;

$cancelledOrdersCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) cnt 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status = 'Cancelled'
"))['cnt'] ?? 0;

$wishlistCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS cnt
    FROM wishlist w
    INNER JOIN products p ON p.id = w.product_id
    WHERE w.user_id = '$user_id'
"))['cnt'] ?? 0;

$compareCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) cnt 
    FROM compare 
    WHERE user_id = '$user_id'
"))['cnt'] ?? 0;

$latestOrderQ = mysqli_query($conn, "
    SELECT * 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status != 'Cancelled' 
    ORDER BY id DESC 
    LIMIT 1
");

$latestOrder = $latestOrderQ ? mysqli_fetch_assoc($latestOrderQ) : null;

// Order confirmation modal disabled: order success page/message will remain, no popup modal on My Orders.
$orderConfirmedModal = null;
if (!empty($_SESSION['sip_order_confirmed_modal'])) {
    unset($_SESSION['sip_order_confirmed_modal']);
}

$previousOrdersQ = mysqli_query($conn, "
    SELECT * 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status != 'Cancelled' 
    ORDER BY id DESC 
    LIMIT 999 OFFSET 1
");

$cancelledOrdersQ = mysqli_query($conn, "
    SELECT * 
    FROM orders 
    WHERE user_id = '$user_id' 
    AND order_status = 'Cancelled' 
    ORDER BY id DESC
");

$trackOrder = null;
$trackSearched = false;

if ($tab === 'track' && isset($_GET['track_order_no'])) {
    $trackSearched = true;

    $order_no = mysqli_real_escape_string($conn, trim($_GET['track_order_no'] ?? ''));

    if ($order_no !== '') {
        $trackOrder = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * 
            FROM orders 
            WHERE user_id = '$user_id' 
            AND (
                order_no = '$order_no' 
                OR tracking_no = '$order_no' 
                OR invoice_no = '$order_no'
            ) 
            LIMIT 1
        "));
    }
}

$defaultProfileImage = './assets/images/office-man.webp';

if ($hasImage && !empty($user['image']) && file_exists($user['image'])) {
    $profileImage = $user['image'];
} else {
    $profileImage = $defaultProfileImage;
}

/* =====================================================
   ORDER CARD FUNCTION
===================================================== */
function sip_profile_order_card($conn, $order, $type = 'active')
{
    if (!$order) {
        return;
    }

    $oid = (int) $order['id'];

    $items_q = mysqli_query($conn, "
        SELECT 
            oi.*, 
            IF(oi.item_type='project', pr.title, p.title) AS title, 
            IF(oi.item_type='project', pr.image, p.main_image) AS main_image 
        FROM order_items oi 
        LEFT JOIN products p ON p.id = oi.product_id AND oi.item_type='product'
        LEFT JOIN projects pr ON pr.id = oi.product_id AND oi.item_type='project'
        WHERE oi.order_id = '$oid'
    ");

    $canCancel = $type !== 'cancelled' && sip_can_customer_cancel($order['order_status'] ?? 'Pending');
?>

    <div class="sip-order-card profile-order-card <?= $type === 'cancelled' ? 'profile-cancelled-card' : '' ?>">

        <div class="sip-order-top">
            <div>
                <h3 class="sip-order-no">
                    Order #<?= sip_h($order['order_no'] ?? '') ?>
                </h3>

                <div class="sip-order-meta">
                    Placed on
                    <?= !empty($order['created_at']) ? date('d M Y, h:i A', strtotime($order['created_at'])) : 'N/A' ?>
                </div>

                <?php if (!empty($order['invoice_no']) && strtolower(trim($order['payment_status'] ?? '')) === 'paid'): ?>
                    <div class="sip-order-meta">
                        Invoice:
                        <b><?= sip_h($order['invoice_no']) ?></b>
                    </div>
                <?php endif; ?>

                <?php if ($type === 'cancelled'): ?>
                    <?php if (!empty($order['cancelled_at'])): ?>
                        <div class="sip-order-meta">
                            Cancelled on:
                            <b><?= date('d M Y, h:i A', strtotime($order['cancelled_at'])) ?></b>
                        </div>
                    <?php endif; ?>

                    <div class="sip-order-meta">
                        Cancelled By:
                        <b><?= sip_h($order['cancelled_by'] ?: 'N/A') ?></b>
                    </div>

                    <?php if (!empty($order['cancel_reason'])): ?>
                        <div class="sip-order-meta">
                            Reason:
                            <b><?= sip_h($order['cancel_reason']) ?></b>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <span
                class="sip-pill bg-<?= $type === 'cancelled' ? 'danger' : sip_order_badge($order['order_status'] ?? 'Pending') ?>">
                <?= sip_h($order['order_status'] ?? 'Pending') ?>
            </span>
        </div>

        <?php if ($items_q && mysqli_num_rows($items_q) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($items_q)): ?>
                <?php
                $productImage = !empty($item['main_image'])
                    ? 'uploads/' . ($item['item_type'] === 'project' ? 'projects' : 'products') . '/' . $item['main_image']
                    : 'assets/images/no-image.png';

                $productTitle = !empty($item['title'])
                    ? $item['title']
                    : 'Product not available';

                $price = (float) $item['price'];
                $qty = (int) $item['qty'];
                ?>

                <div class="sip-item">
                    <img src="<?= sip_h($productImage) ?>" alt="<?= sip_h($productTitle) ?>">

                    <div>
                        <div class="sip-item-name">
                            <?= sip_h($productTitle) ?>
                        </div>

                        <div class="sip-item-sub">
                            <?= money($item['price']) ?> × <?= (int) $item['qty'] ?>
                        </div>
                    </div>

                    <div class="sip-item-total">
                        ₹<?= format_price($price * $qty) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sip-empty py-3">
                <p class="mb-0">No product record found.</p>
            </div>
        <?php endif; ?>

        <div class="sip-order-footer">
            <div class="sip-info-grid">
                <span>
                    Payment:
                    <b><?= sip_h($order['payment_method'] ?? 'N/A') ?></b>
                </span>

                <span>
                    Payment Status:
                    <b><?= sip_h($order['payment_status'] ?? 'N/A') ?></b>
                </span>

                <span>
                    Tracking:
                    <b><?= sip_h($order['tracking_no'] ?: 'N/A') ?></b>
                </span>
            </div>

            <div>
                <div class="sip-total">
                    Total: ₹<?= format_price((float) ($order['total_amount'] ?? 0)) ?>
                </div>

                <div class="sip-actions mt-2">
                    <a href="order-details.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-outline">
                        View Details
                    </a>

                    <?php if (!empty($order['invoice_no']) && strtolower(trim($order['payment_status'] ?? '')) === 'paid'): ?>
                        <a href="invoice.php?order_id=<?= $oid ?>" class="sip-btn sip-btn-dark">
                            Invoice
                        </a>
                    <?php endif; ?>

                    <?php if (strtolower(trim($order['payment_status'] ?? '')) === 'paid' && !in_array(strtolower($order['order_status'] ?? ''), ['cancelled'])): ?>
                        <?php if (($order['refund_status'] ?? 'None') === 'None'): ?>
                            <button type="button" class="sip-btn sip-btn-outline" onclick="openRefundModal(<?= $oid ?>)">
                                Request Refund
                            </button>
                        <?php else: ?>
                            <span class="sip-btn sip-btn-dark" style="pointer-events: none; opacity: 0.8;">
                                Refund: <?= sip_h($order['refund_status']) ?>
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($canCancel): ?>
                        <form method="post" action="cancel_order.php" style="display:inline-block"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <input type="hidden" name="order_id" value="<?= $oid ?>">
                            <input type="hidden" name="cancel_reason" value="Cancelled from profile page">
                            <button type="submit" class="sip-btn sip-btn-danger">
                                Cancel
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

<?php
}

include 'header.php';

function money($amount)
{
    return '₹' . format_price((float) $amount);
}
?>

<?= sip_order_card_css(); ?>

<section class="breadcrumb-section">
    <h1>MY ACCOUNT</h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a>
        <span>| Profile</span>
    </p>
</section>

<div class="container my-5 account-layout">

    <!-- SIDEBAR SAME RAHEGA -->
    <aside class="ecom-card account-menu">

        <div class="text-center mb-4">
            <div class="profile-img-box">
                <img id="profilePreview" class="profile-avatar" src="<?= ecom_h($profileImage) ?>?t=<?= time(); ?>"
                    alt="Profile">

                <form id="profileImageForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile_image">

                    <input type="file" name="profile_image" id="profileImageInput" accept="image/*" hidden>

                    <button type="button" class="profile-edit-btn"
                        onclick="document.getElementById('profileImageInput').click();" title="Update Image">
                        <i class="fa fa-pen"></i>
                    </button>
                </form>
            </div>

            <div id="imageMsg" class="small fw-semibold mt-2"></div>

            <h5 class="mt-3 mb-0" id="sidebarUserName">
                <?= ecom_h($userName) ?>
            </h5>

            <?php if ($userEmail !== ''): ?>
                <small class="text-muted" id="sidebarUserEmail">
                    <?= ecom_h($userEmail) ?>
                </small>
            <?php else: ?>
                <small class="text-muted" id="sidebarUserEmail">
                    <?= ecom_h($userMobile) ?>
                </small>
            <?php endif; ?>
        </div>

        <a class="<?= $tab === 'profile' ? 'active' : '' ?>" href="user_profile.php?tab=profile">
            <i class="fa fa-user"></i> Profile Information
        </a>

        <a class="<?= $tab === 'password' ? 'active' : '' ?>" href="user_profile.php?tab=password">
            <i class="fa fa-key"></i> Change Password
        </a>

        <a class="<?= $tab === 'addresses' ? 'active' : '' ?>" href="user_profile.php?tab=addresses">
            <i class="fa fa-location-dot"></i> Manage Addresses
        </a>

        <a class="<?= $tab === 'latest' ? 'active' : '' ?>" href="user_profile.php?tab=latest">
            <i class="fa fa-box"></i> My Orders
        </a>

        <a class="<?= $tab === 'track' ? 'active' : '' ?>" href="user_profile.php?tab=track">
            <i class="fa fa-truck-fast"></i> Track Order
        </a>

        <a href="logout.php" onclick="return confirm('Kya aap logout karna chahte hain?')">
            <i class="fa fa-right-from-bracket"></i> Logout
        </a>

    </aside>

    <!-- RIGHT CONTENT ONLY SELECTED TAB -->
    <main class="account-content">

        <!-- STATS COMMON -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="ecom-card account-stat-card">
                    <small class="text-muted">Total Orders</small>
                    <h3 class="mb-0 ecom-orange"><?= (int) $ordersCount ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="ecom-card account-stat-card">
                    <small class="text-muted">Active Orders</small>
                    <h3 class="mb-0 ecom-orange"><?= (int) $activeOrdersCount ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="ecom-card account-stat-card">
                    <small class="text-muted">Cancelled Orders</small>
                    <h3 class="mb-0 text-danger"><?= (int) $cancelledOrdersCount ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="ecom-card account-stat-card">
                    <small class="text-muted">Wishlist Items</small>
                    <h3 class="mb-0 ecom-orange"><?= (int) $wishlistCount ?></h3>
                </div>
            </div>
        </div>

        <?php if ($tab === 'profile'): ?>

            <!-- PROFILE INFORMATION -->
            <div class="ecom-card mb-4 profile-info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="ecom-title mb-0">Personal Information</h4>

                    <div class="profile-action-buttons">
                        <button type="button" class="ecom-btn-outline" id="profileEditBtn">
                            <i class="fa fa-pen"></i> Edit
                        </button>

                        <button type="submit" form="profileForm" class="ecom-btn d-none" id="profileSaveBtn">
                            Save Profile
                        </button>
                    </div>
                </div>

                <form id="profileForm" class="row g-3">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input class="form-control profile-field profile-name-field" name="name" value="<?= ecom_h($userName) ?>"
                            required disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input class="form-control profile-readonly-field" type="email" name="email"
                            value="<?= ecom_h($userEmail) ?>" readonly disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile</label>
                        <input class="form-control profile-readonly-field" name="mobile" value="<?= ecom_h($userMobile) ?>"
                            readonly disabled>
                    </div>

                    <?php if ($hasGender): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <select class="form-select profile-readonly-field" name="gender" disabled>
                                <option value="">Select</option>
                                <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <span id="profileMsg" class="fw-semibold"></span>
                    </div>
                </form>
            </div>

        <?php elseif ($tab === 'password'): ?>

            <div class="ecom-card mb-4 profile-info-card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="ecom-title mb-0">Change Password</h4>
                        <p class="text-muted mb-0">Update your account password securely.</p>
                    </div>
                    <a href="user_profile.php?tab=profile" class="ecom-btn-outline">Back to Profile</a>
                </div>

                <form id="passwordForm" class="row g-3">
                    <input type="hidden" name="action" value="change_password">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Old Password</label>
                        <input class="form-control" type="password" name="old_password" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input class="form-control" type="password" name="new_password" required>
                    </div>

                    <div class="col-12">
                        <button class="ecom-btn-outline" type="submit">Update Password</button>
                        <span id="passMsg" class="ms-2 fw-semibold"></span>
                    </div>
                </form>
            </div>

        <?php elseif ($tab === 'addresses'): ?>

            <div class="ecom-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="ecom-title mb-0">Saved Addresses</h4>

                    <a href="manage-addresses.php?return=profile" class="ecom-btn-outline">
                        + Add Address
                    </a>
                </div>

                <?php if ($addressMsg !== ''): ?>
                    <div class="alert alert-<?= ecom_h($addressMsgType ?: 'success') ?> mb-3">
                        <?= ecom_h($addressMsg) ?>
                    </div>
                <?php endif; ?>

                <?php if ($addresses && mysqli_num_rows($addresses)): ?>
                    <div class="row g-3">
                        <?php while ($ad = mysqli_fetch_assoc($addresses)): ?>
                            <div class="col-md-6">
                                <div class="profile-address-card h-100">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <b><?= ecom_h($ad['type'] ?? 'Home') ?></b>

                                        <?php if (!empty($ad['is_default'])): ?>
                                            <span class="badge bg-success">Default</span>
                                        <?php endif; ?>
                                    </div>

                                    <h6 class="mt-2 mb-1"><?= ecom_h($ad['name'] ?? '') ?></h6>

                                    <p class="text-muted small mb-2">
                                        <?= ecom_h($ad['city'] ?? '') ?>,
                                        <?= ecom_h($ad['state'] ?? '') ?>
                                        -
                                        <?= ecom_h($ad['pincode'] ?? '') ?>
                                        <br>
                                        Phone: <?= ecom_h($ad['phone'] ?? '') ?>
                                    </p>

                                    <div class="d-flex gap-2 flex-wrap mt-3">
                                        <a href="manage-addresses.php?id=<?= (int) $ad['id'] ?>&return=profile"
                                            class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form method="post" action="user_profile.php?tab=addresses"
                                            onsubmit="return confirm('Are you sure you want to delete this address?');"
                                            style="display:inline-block;">
                                            <input type="hidden" name="action" value="delete_address">
                                            <input type="hidden" name="address_id" value="<?= (int) $ad['id'] ?>">

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state text-center p-4">
                        <div style="font-size:42px;">📍</div>
                        <h5 class="fw-bold mt-2">No address added yet</h5>
                        <p class="text-muted">Add your delivery address for faster checkout.</p>

                        <a href="manage-addresses.php?return=profile" class="ecom-btn">
                            Add Address
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <style>
                .profile-address-card {
                    border: 1px solid var(--cultured);
                    border-radius: 14px;
                    padding: 16px;
                    background: var(--white);
                    transition: 0.25s ease;
                }

                .profile-address-card:hover {
                    border-color: var(--salmon-pink);
                    background: var(--cultured);
                }

                .profile-section-title {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 12px;
                    margin: 0 0 14px;
                    flex-wrap: wrap;
                }

                .profile-section-title h4 {
                    margin: 0;
                    color: var(--eerie-black);
                    font-size: 20px;
                    font-weight: 900;
                }

                .profile-section-title div {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }

                .profile-section-title a {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 42px;
                    padding: 10px 18px;
                    border-radius: 10px;
                    background: var(--cultured);
                    border: 1px solid var(--salmon-pink);
                    color: var(--salmon-pink);
                    font-size: 14px;
                    font-weight: 800;
                    text-decoration: none;
                    transition: all 0.25s ease;
                    box-shadow: 0 4px 12px rgba(255, 90, 31, 0.08);
                    white-space: nowrap;
                }

                .profile-section-title a:hover {
                    background: var(--salmon-pink);
                    color: var(--white);
                    transform: translateY(-2px);
                    box-shadow: 0 10px 24px rgba(255, 90, 31, 0.22);
                }

                .profile-section-title a:active {
                    transform: scale(0.98);
                }
            </style>

        <?php elseif ($tab === 'latest'): ?>

            <div class="profile-section-title">
                <h4>Latest Order</h4>
                <div>
                    <a href="user_profile.php?tab=previous">
                        <i class="fa fa-clock-rotate-left"></i> Previous Orders
                    </a>

                    <a href="user_profile.php?tab=cancelled">
                        <i class="fa fa-ban"></i> Cancelled Orders
                    </a>
                </div>
            </div>

            <?php if (!$latestOrder): ?>
                <div class="sip-empty mb-4">
                    <div style="font-size:42px">📦</div>
                    <h4>No active order found</h4>
                    <p>Your latest active order will appear here.</p>
                    <a class="sip-btn sip-btn-primary" href="products.php">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php sip_profile_order_card($conn, $latestOrder, 'latest'); ?>
            <?php endif; ?>

        <?php elseif ($tab === 'previous'): ?>

            <div class="profile-section-title">
                <h4>Previous Orders</h4>
                <a href="user_profile.php?tab=latest">
                    <i class="fa fa-box"></i> Latest Order
                </a>
            </div>

            <?php if (!$previousOrdersQ || mysqli_num_rows($previousOrdersQ) == 0): ?>
                <div class="sip-empty mb-4">
                    <h4>No previous orders found</h4>
                    <p>Only older active orders will be listed here.</p>
                </div>
            <?php else: ?>
                <?php while ($prevOrder = mysqli_fetch_assoc($previousOrdersQ)): ?>
                    <?php sip_profile_order_card($conn, $prevOrder, 'previous'); ?>
                <?php endwhile; ?>
            <?php endif; ?>

        <?php elseif ($tab === 'cancelled'): ?>

            <div class="profile-section-title">
                <h4>Cancelled Orders</h4>
                <a href="user_profile.php?tab=latest">Latest Order</a>
            </div>

            <?php if (!$cancelledOrdersQ || mysqli_num_rows($cancelledOrdersQ) == 0): ?>
                <div class="sip-empty mb-4">
                    <h4>No cancelled orders found</h4>
                    <p>Customer or admin cancelled orders will appear here.</p>
                </div>
            <?php else: ?>
                <?php while ($cancelOrder = mysqli_fetch_assoc($cancelledOrdersQ)): ?>
                    <?php sip_profile_order_card($conn, $cancelOrder, 'cancelled'); ?>
                <?php endwhile; ?>
            <?php endif; ?>

        <?php elseif ($tab === 'track'): ?>

            <div class="ecom-card">
                <h4 class="ecom-title mb-3">Track Your Order</h4>

                <form method="get" class="d-flex gap-2 flex-wrap">
                    <input type="hidden" name="tab" value="track">

                    <input class="form-control flex-grow-1" name="track_order_no"
                        value="<?= ecom_h($_GET['track_order_no'] ?? '') ?>"
                        placeholder="Enter Order No / Tracking No / Invoice No" required>

                    <button class="ecom-btn" type="submit">
                        Track
                    </button>
                </form>
            </div>

            <?php if ($trackSearched): ?>
                <?php if ($trackOrder): ?>
                    <?php
                    $currentStatus = $trackOrder['order_status'] ?? 'Pending';
                    $isSpecial = in_array($currentStatus, ['Cancelled', 'Returned'], true);

                    $steps = $isSpecial
                        ? ['Pending', 'Confirmed', 'Packed', 'Shipped', $currentStatus]
                        : ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];

                    $idx = array_search($currentStatus, $steps, true);

                    if ($idx === false) {
                        $idx = 0;
                    }
                    ?>

                    <div class="ecom-card mt-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <h4 class="ecom-title mb-1">
                                    Order #<?= ecom_h($trackOrder['order_no']) ?>
                                </h4>

                                <?php if (!empty($trackOrder['invoice_no'])): ?>
                                    <small>
                                        Invoice:
                                        <b><?= ecom_h($trackOrder['invoice_no']) ?></b>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <span class="badge bg-<?= sip_order_badge($currentStatus) ?> p-2">
                                <?= ecom_h($currentStatus) ?>
                            </span>
                        </div>

                        <div class="tracking-steps <?= $isSpecial ? 'cancel' : '' ?>">
                            <?php foreach ($steps as $i => $st): ?>
                                <div class="track-step <?= $i <= $idx ? 'done' : '' ?>">
                                    <div class="track-dot"><?= $i + 1 ?></div>
                                    <p><?= ecom_h($st) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <p>
                            <b>Courier:</b>
                            <?= ecom_h($trackOrder['courier_name'] ?: 'Not assigned yet') ?>
                        </p>

                        <p>
                            <b>Tracking No:</b>
                            <?= ecom_h($trackOrder['tracking_no'] ?: 'Not generated yet') ?>
                        </p>

                        <?php if (!empty($trackOrder['expected_delivery_date'])): ?>
                            <p>
                                <b>Expected Delivery:</b>
                                <?= date('d M Y', strtotime($trackOrder['expected_delivery_date'])) ?>
                            </p>
                        <?php endif; ?>

                        <a class="ecom-btn-outline" href="order-details.php?order_id=<?= (int) $trackOrder['id'] ?>">
                            View Full Details
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger mt-3">
                        No order found with this number.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php endif; ?>

    </main>
</div>

<style>
    .account-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 22px;
        align-items: flex-start;
    }

    .account-content {
        min-width: 0;
    }

    .account-menu {
        position: sticky;
        top: 20px;
        min-height: 520px;
    }

    .profile-img-box {
        width: 82px;
        height: 82px;
        margin: 0 auto;
        position: relative;
    }

    .profile-avatar {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--white);
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.18);
        background: var(--cultured);
    }

    .profile-edit-btn {
        width: 31px;
        height: 31px;
        border-radius: 50%;
        border: 2px solid var(--white);
        background: var(--salmon-pink);
        color: var(--white);
        position: absolute;
        right: -2px;
        bottom: -2px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        box-shadow: 0 5px 14px rgba(255, 90, 31, 0.35);
        cursor: pointer;
    }

    .profile-edit-btn:hover {
        background: var(--sandy-brown);
    }

    #sidebarUserName {
        font-size: 16px;
        font-weight: 800;
        color: var(--eerie-black);
        word-break: break-word;
    }

    #sidebarUserEmail {
        display: block;
        font-size: 12px;
        word-break: break-word;
    }

    .account-menu a {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-info-card .form-control:disabled,
    .profile-info-card .form-select:disabled {
        background-color: var(--cultured);
        color: var(--sonic-silver);
        border-color: var(--cultured);
        cursor: not-allowed;
    }

    .profile-info-card .form-control:not(:disabled),
    .profile-info-card .form-select:not(:disabled) {
        background-color: var(--white);
        border-color: var(--salmon-pink);
        box-shadow: none;
    }

    .profile-action-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .account-stat-card {
        min-height: 86px;
    }

    .profile-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin: 0 0 14px;
    }

    .profile-section-title h4 {
        margin: 0;
        color: var(--eerie-black);
        font-size: 20px;
        font-weight: 900;
    }

    .profile-section-title a {
        color: var(--salmon-pink);
        font-weight: 800;
        font-size: 13px;
        text-decoration: none;
    }

    .profile-order-card {
        margin-bottom: 18px;
    }

    .profile-cancelled-card {
        border-left: 5px solid var(--salmon-pink);
    }

    .tracking-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 35px 25px;
    }

    .tracking-steps:before {
        content: "";
        position: absolute;
        top: 18px;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--cultured);
        border-radius: 10px;
    }

    .track-step {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1;
    }

    .track-dot {
        width: 38px;
        height: 38px;
        margin: 0 auto 8px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cultured);
        color: var(--sonic-silver);
        font-weight: 900;
    }

    .track-step.done .track-dot {
        background: var(--salmon-pink);
        color: var(--white);
    }

    .track-step.done p {
        color: var(--salmon-pink);
    }

    .tracking-steps.cancel .track-step.done .track-dot {
        background: var(--salmon-pink);
    }

    .tracking-steps.cancel .track-step.done p {
        color: var(--salmon-pink);
    }

    .track-step p {
        font-weight: 900;
        font-size: 13px;
        margin: 0;
    }

    @media(max-width: 991px) {
        .account-layout {
            grid-template-columns: 1fr;
        }

        .account-menu {
            position: static;
            min-height: auto;
        }
    }

    @media(max-width: 700px) {
        .tracking-steps {
            overflow: auto;
            gap: 18px;
            margin: 25px 0;
        }

        .tracking-steps:before {
            display: none;
        }

        .track-step {
            min-width: 80px;
        }
    }

    @media(max-width: 575px) {

        .profile-info-card .d-flex,
        .ecom-card .d-flex {
            align-items: flex-start !important;
            gap: 10px;
            flex-wrap: wrap;
        }

        .profile-action-buttons {
            width: 100%;
        }

        .profile-action-buttons button {
            width: 100%;
        }

        .profile-section-title {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* =========================
       REFUND MODAL THEME
    ========================= */

    .refund-modal-dialog {
        max-width: 520px;
    }

    .refund-modal-content {
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.18);
    }

    .refund-modal-header {
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px;
        background: linear-gradient(135deg, #ff5e2d, #ff8a00);
        color: #fff;
    }

    .refund-modal-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .refund-modal-title {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }

    .refund-modal-subtitle {
        margin-top: 4px;
        font-size: 13px;
        opacity: 0.9;
    }

    .refund-close-btn {
        position: absolute;
        top: 16px;
        right: 16px;
        opacity: 1;
        box-shadow: none !important;
    }

    .refund-modal-body {
        padding: 26px;
    }

    .refund-input-group {
        margin-bottom: 20px;
    }

    .refund-label {
        display: block;
        margin-bottom: 10px;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .refund-textarea {
        border: 1.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        min-height: 140px;
        resize: none;
        font-size: 15px;
        transition: all 0.25s ease;
        box-shadow: none !important;
    }

    .refund-textarea:focus {
        border-color: #ff5e2d;
        box-shadow: 0 0 0 4px rgba(255, 94, 45, 0.12) !important;
    }

    .refund-note {
        display: block;
        margin-top: 10px;
        color: #6b7280;
        font-size: 12px;
    }

    .refund-action-box {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .refund-cancel-btn {
        min-width: 120px;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.25s ease;
    }

    .refund-cancel-btn:hover {
        background: #f3f4f6;
    }

    .refund-submit-btn {
        min-width: 170px;
        height: 48px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #ff5e2d, #ff8a00);
        color: #fff;
        font-size: 15px;
        font-weight: 800;
        transition: all 0.25s ease;
        box-shadow: 0 10px 25px rgba(255, 94, 45, 0.28);
    }

    .refund-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(255, 94, 45, 0.38);
    }

    .refund-submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    @media(max-width: 575px) {
        .refund-modal-header {
            padding: 20px;
        }

        .refund-modal-title {
            font-size: 20px;
        }

        .refund-modal-body {
            padding: 20px;
        }

        .refund-action-box {
            flex-direction: column;
        }

        .refund-cancel-btn,
        .refund-submit-btn {
            width: 100%;
        }
    }
</style>

<script>
    $(function() {
        let originalProfileData = {
            name: $('[name="name"]').val() || ''
        };

        function enableProfileEdit() {
            $('[name="name"]').prop('disabled', false).prop('readonly', false);

            $('[name="email"]').prop('disabled', true).prop('readonly', true);
            $('[name="mobile"]').prop('disabled', true).prop('readonly', true);
            $('[name="gender"]').prop('disabled', true);

            $('#profileEditBtn').addClass('d-none');
            $('#profileSaveBtn').removeClass('d-none');

            $('#profileMsg').text('');

            $('[name="name"]').focus();
        }

        function disableProfileEdit() {
            $('[name="name"]').prop('disabled', true).prop('readonly', true);
            $('[name="email"]').prop('disabled', true).prop('readonly', true);
            $('[name="mobile"]').prop('disabled', true).prop('readonly', true);
            $('[name="gender"]').prop('disabled', true);

            $('#profileEditBtn').removeClass('d-none');
            $('#profileSaveBtn').addClass('d-none');
        }

        function getCurrentProfileData() {
            return {
                name: $('[name="name"]').val() || ''
            };
        }

        function isProfileChanged() {
            let current = getCurrentProfileData();

            return current.name !== originalProfileData.name;
        }

        $('#profileEditBtn').on('click', function() {
            enableProfileEdit();
        });

        $('.profile-name-field').on('input change', function() {
            if (isProfileChanged()) {
                $('#profileSaveBtn').removeClass('d-none');
            } else {
                $('#profileSaveBtn').addClass('d-none');
            }
        });

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();

            $('[name="name"]').prop('disabled', false);

            $.post('user_profile.php?tab=profile', $(this).serialize(), function(r) {
                $('#profileMsg')
                    .text(r.message || (r.status === 'success' ? 'Profile updated' : 'Failed'))
                    .css('color', r.status === 'success' ? 'green' : 'red');

                if (r.status === 'success') {
                    $('[name="name"]').val(r.name);
                    $('[name="email"]').val(r.email);
                    $('[name="mobile"]').val(r.mobile);

                    originalProfileData = getCurrentProfileData();

                    $('#sidebarUserName').text(r.name || 'Customer');
                    $('#sidebarUserEmail').text(r.email || '');

                    disableProfileEdit();

                    if (typeof ecomToast === 'function') {
                        ecomToast('Profile updated');
                    }
                } else {
                    $('[name="name"]').prop('disabled', false);
                }
            }, 'json').fail(function() {
                $('#profileMsg').text('Something went wrong').css('color', 'red');
                $('[name="name"]').prop('disabled', false);
            });
        });

        $('#passwordForm').on('submit', function(e) {
            e.preventDefault();

            $.post('user_profile.php?tab=profile', $(this).serialize(), function(r) {
                $('#passMsg')
                    .text(r.message || (r.status === 'success' ? 'Password updated' : 'Failed'))
                    .css('color', r.status === 'success' ? 'green' : 'red');

                if (r.status === 'success') {
                    $('#passwordForm')[0].reset();

                    if (typeof ecomToast === 'function') {
                        ecomToast('Password updated');
                    }
                }
            }, 'json').fail(function() {
                $('#passMsg').text('Something went wrong').css('color', 'red');
            });
        });

        $('#profileImageInput').on('change', function() {
            if (!this.files || !this.files[0]) {
                return;
            }

            let formData = new FormData($('#profileImageForm')[0]);

            $.ajax({
                url: 'user_profile.php?tab=profile',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#imageMsg').text('Uploading...').css('color', '#ff5e2d');
                },
                success: function(r) {
                    $('#imageMsg')
                        .text(r.message || 'Done')
                        .css('color', r.status === 'success' ? 'green' : 'red');

                    if (r.status === 'success') {
                        $('#profilePreview').attr('src', r.image + '?t=' + new Date().getTime());

                        if (typeof ecomToast === 'function') {
                            ecomToast('Profile image updated');
                        }
                    }

                    $('#profileImageInput').val('');
                },
                error: function() {
                    $('#imageMsg').text('Something went wrong').css('color', 'red');
                    $('#profileImageInput').val('');
                }
            });
        });
    });
</script>

<!-- MODERN REFUND MODAL -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered refund-modal-dialog">
        <div class="modal-content refund-modal-content border-0">

            <div class="refund-modal-header">
                <div class="refund-modal-icon">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>

                <div>
                    <h5 class="refund-modal-title">Request Refund</h5>
                    <p class="refund-modal-subtitle mb-0">
                        Submit your refund request for review
                    </p>
                </div>

                <button type="button" class="btn-close btn-close-white refund-close-btn"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body refund-modal-body">

                <form id="refundForm">
                    <input type="hidden" name="order_id" id="refundOrderId">

                    <div class="refund-input-group">
                        <label class="refund-label">
                            Refund Reason
                        </label>

                        <textarea class="form-control refund-textarea" name="reason" id="refundReason" rows="5"
                            required placeholder="Explain your issue in detail..."></textarea>

                        <small class="refund-note">
                            Refund requests are usually reviewed within 24-48 hours.
                        </small>
                    </div>

                    <div class="refund-action-box">
                        <button type="button" class="refund-cancel-btn" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="refund-submit-btn" id="refundSubmitBtn">
                            <span class="refund-btn-text">
                                Submit Request
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    let refundModalInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        const refundModalEl = document.getElementById('refundModal');

        refundModalEl.classList.remove('show');
        refundModalEl.style.display = 'none';
        refundModalEl.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('modal-open');

        document.querySelectorAll('.modal-backdrop').forEach(el => {
            el.remove();
        });
        
        document.body.appendChild(refundModalEl);

        refundModalInstance = new bootstrap.Modal(refundModalEl, {
            backdrop: true,
            keyboard: true
        });
    });

    function openRefundModal(orderId) {
        if (!refundModalInstance) return;

        $('#refundOrderId').val(orderId);
        $('#refundReason').val('');

        refundModalInstance.show();
    }

    $('#refundForm').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#refundSubmitBtn');

        btn.prop('disabled', true);

        btn.html(`
            <span class="spinner-border spinner-border-sm me-2"></span>
            Submitting...
        `);

        $.ajax({
            url: 'ajax/request_refund.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function(res) {
                if (res.status === 'success') {
                    refundModalInstance.hide();
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    if (typeof ecomToast === 'function') {
                        ecomToast('Refund request submitted');
                    }

                    setTimeout(() => {
                        location.reload();
                    }, 400);
                } else {
                    alert(res.message || 'Something went wrong');

                    btn.prop('disabled', false).html(`
                        <span class="refund-btn-text">
                            Submit Request
                        </span>
                    `);
                }
            },

            error: function() {
                alert('Server error');

                btn.prop('disabled', false).html(`
                    <span class="refund-btn-text">
                        Submit Request
                    </span>
                `);
            }
        });
    });
</script>

<?php include 'footer.php'; ?>