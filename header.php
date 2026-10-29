<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'jwt.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/secure_helpers.php';

if (isset($_COOKIE['auth_token'])) {
    $payload = JWT::decode($_COOKIE['auth_token']);

    if (!$payload) {
        setcookie("auth_token", "", time() - 3600, "/");
        header("Location: index.php");
        exit;
    }

    $role = $payload['role'];

    /* ===== LIVE ACCOUNT STATUS CHECK ===== */
    $login_id = mysqli_real_escape_string($conn, $payload['login_id']);

    $checkUser = mysqli_query($conn, "
    SELECT status 
    FROM login 
    WHERE login_id='$login_id'
    LIMIT 1
");

    $checkData = $checkUser ? mysqli_fetch_assoc($checkUser) : null;

    if (!$checkData || (int)$checkData['status'] !== 1) {

        // session clear
        $_SESSION = [];

        // destroy session
        session_destroy();

        // delete auth cookie
        setcookie("auth_token", "", time() - 3600, "/");

        // redirect logout
        header("Location: logout.php");
        exit;
    }

    if ($role == 'admin' && strpos($_SERVER['PHP_SELF'], 'admin') === false) {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($role == 'user' && strpos($_SERVER['PHP_SELF'], 'admin') !== false) {
        header("Location: index.php");
        exit;
    }
}
$user = null;
if (isset($_COOKIE['auth_token'])) {
    $payload = JWT::decode($_COOKIE['auth_token']);
    if ($payload) {
        $user = $payload;
    }
}

// Cart / wishlist / compare count: guest user ke liye always 0.
$cart_count = 0;
$wishlist_count = 0;
$compare_count = 0;
$user_id_for_counts = ecom_user_id();
if ($user_id_for_counts > 0 && isset($conn)) {
    $uidSafe = (int)$user_id_for_counts;
    $cartRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM cart WHERE user_id='$uidSafe'");
    $wishRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM wishlist WHERE user_id='$uidSafe'");
    $compRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM compare WHERE user_id='$uidSafe'");
    $cart_count = $cartRes ? (int)(mysqli_fetch_assoc($cartRes)['cnt'] ?? 0) : 0;
    $wishlist_count = $wishRes ? (int)(mysqli_fetch_assoc($wishRes)['cnt'] ?? 0) : 0;
    $compare_count = $compRes ? (int)(mysqli_fetch_assoc($compRes)['cnt'] ?? 0) : 0;
}

// Mobile category drawer ke liye wahi category data use hoga jo home sidebar mein hai.
$mobileSidebarCategories = [];
if (isset($conn)) {
    $catRes = mysqli_query($conn, "SELECT id, name FROM categories WHERE status=1 ORDER BY name ASC");
    while ($catRes && ($cat = mysqli_fetch_assoc($catRes))) {
        $catId = (int)$cat['id'];
        $mobileSidebarCategories[$catId] = [
            'title' => trim($cat['name']),
            'link'  => 'products.php?cat_id=' . $catId,
            'sub'   => []
        ];
    }

    $subRes = mysqli_query($conn, "
        SELECT sc.id AS sub_id, sc.category_id, sc.name AS sub_name, COUNT(p.id) AS total_products
        FROM sub_categories sc
        LEFT JOIN products p ON p.sub_category_id = sc.id AND p.status = 1
        WHERE sc.status = 1
        GROUP BY sc.id, sc.category_id, sc.name
        ORDER BY sc.name ASC
    ");
    while ($subRes && ($row = mysqli_fetch_assoc($subRes))) {
        $catId = (int)$row['category_id'];
        if (!isset($mobileSidebarCategories[$catId])) continue;
        $mobileSidebarCategories[$catId]['sub'][] = [
            'title' => trim($row['sub_name']),
            'count' => (int)$row['total_products'],
            'link'  => 'products.php?sub_id=' . (int)$row['sub_id']
        ];
    }
}
?>

<?php
require_once __DIR__ . '/includes/SEO.php';
$seo = new SEO();
if (isset($pageTitle)) $seo->setMeta($pageTitle, $pageDescription ?? '');
if (isset($pageStructuredData)) $seo->addStructuredData($pageStructuredData);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $seo->renderTags(); ?>
    <!-- FAVICON -->
    <link rel="shortcut icon" href="assets/images/logo/1.png">
    <!-- FONTS & ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Swiper CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.cdnfonts.com/css/revue-bt" rel="stylesheet" />

    <!-- CSS FILES -->
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style-home.css">

    <style>
        .auth-modal-open {
            overflow: hidden
        }

        .linkless-action,
        .auth-link-btn {
            border: 0;
            background: transparent;
            color: inherit;
            padding: 0;
            cursor: pointer
        }

        .auth-link-btn strong {
            color: inherit
        }

        .mobile-main-menu {
            position: fixed;
            top: 0;
            left: -110%;
            width: 86%;
            max-width: 360px;
            height: 100vh;
            background: var(--white);
            z-index: 1210;
            box-shadow: 20px 0 50px rgba(15, 23, 42, .18);
            transition: .28s ease;
            overflow: auto;
            padding: 18px
        }

        .mobile-main-menu.active {
            left: 0
        }

        .mobile-main-menu-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 4px 14px;
            border-bottom: 1px solid var(--cultured);
            font-size: 20px;
            color: var(--eerie-black)
        }

        .mobile-main-menu-head button {
            border: 0;
            background: var(--cultured);
            color: var(--salmon-pink);
            border-radius: 12px;
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center
        }

        .mobile-main-menu-list {
            list-style: none;
            margin: 14px 0 0;
            padding: 0
        }

        .mobile-main-menu-list>li {
            margin: 9px 0
        }

        .mobile-menu-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mobile-menu-row>a {
            flex: 1;
        }

        .mobile-menu-toggle {
            width: 42px;
            min-width: 42px;
            height: 42px;
            border: 1px solid var(--cultured);
            border-radius: 14px;
            background: var(--white);
            color: var(--salmon-pink);
            display: grid;
            place-items: center;
            font-size: 18px;
            cursor: pointer;
        }

        .mobile-menu-toggle:hover {
            background: var(--cultured);
            border-color: var(--sandy-brown);
        }

        .mobile-main-menu-list a {
            display: block;
            text-decoration: none;
            color: var(--davys-gray);
            font-weight: 800;
            background: var(--white);
            border: 1px solid var(--cultured);
            border-radius: 15px;
            padding: 12px 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .mobile-main-menu-list a:hover {
            color: var(--salmon-pink);
            border-color: var(--sandy-brown);
            background: var(--cultured)
        }

        .mobile-sub-links {
            display: none;
            padding: 8px 0 0 22px
        }

        .mobile-menu-has-sub.open .mobile-sub-links {
            display: block;
        }

        .mobile-menu-has-sub.open .mobile-menu-toggle ion-icon {
            transform: rotate(45deg);
        }

        .mobile-sub-links a {
            font-size: 13px;
            font-weight: 700;
            padding: 9px 12px;
            margin: 5px 0;
            background: var(--cultured);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto
        }

        .mobile-bottom-navigation .count {
            position: absolute;
            top: -6px;
            right: -7px;
            background: var(--salmon-pink);
            color: var(--white);
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 11px;
            display: grid;
            place-items: center
        }

        .mobile-bottom-navigation .action-btn {
            position: relative
        }

        .mobile-bottom-navigation a {
            color: inherit
        }


        .mobile-menu-quick-actions {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 16px 0 6px;
            padding-top: 14px;
            border-top: 1px solid var(--cultured);
        }

        .mobile-menu-quick-action {
            position: relative;
            min-height: 74px;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            color: var(--eerie-black) !important;
            border: 1px solid var(--cultured);
            border-radius: 16px;
            background: var(--white);
            font-size: 26px;
            font-weight: 800;
            padding: 10px 6px !important;
        }

        .mobile-menu-quick-action small {
            font-size: 11px;
            line-height: 1;
            color: var(--davys-gray);
        }

        .mobile-menu-quick-action .count {
            position: absolute;
            top: 8px;
            right: 12px;
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--salmon-pink);
            color: var(--white);
            font-size: 11px;
            display: grid;
            place-items: center;
        }

        @media (max-width: 768px) {
            .mobile-menu-quick-actions {
                display: grid;
            }
        }


        .global-mobile-category-drawer {
            display: none;
        }

        @media (max-width: 1200px) {
            .global-mobile-category-drawer {
                display: block;
                position: fixed;
                top: 0;
                left: -110%;
                width: 88%;
                max-width: 380px;
                height: 100vh;
                background: var(--white);
                z-index: 1212;
                overflow-y: auto;
                padding: 16px;
                transition: 0.28s ease;
                box-shadow: 20px 0 50px rgba(15, 23, 42, 0.18);
            }

            .global-mobile-category-drawer.active {
                left: 0;
            }

            .global-category-card {
                border: 1px solid var(--cultured);
                border-radius: 22px;
                padding: 16px;
                background: linear-gradient(180deg, var(--white) 0%, var(--cultured) 100%);
            }

            .global-category-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 14px;
                margin-bottom: 14px;
                border-bottom: 1px solid var(--cultured);
            }

            .global-category-title {
                margin: 0;
                color: var(--eerie-black);
                font-size: 18px;
                font-weight: 900;
                letter-spacing: 1.5px;
                text-transform: uppercase;
            }

            .global-category-title span {
                color: var(--salmon-pink);
            }

            .global-category-close {
                width: 36px;
                height: 36px;
                border: 0;
                border-radius: 12px;
                background: var(--cultured);
                color: var(--salmon-pink);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
            }

            .global-category-list,
            .global-sub-list {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .global-category-item {
                margin-bottom: 7px;
            }

            .global-category-btn {
                width: 100%;
                min-height: 44px;
                border: 1px solid var(--cultured);
                border-radius: 16px;
                background: var(--white);
                padding: 8px 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                font-weight: 850;
                color: var(--davys-gray);
            }

            .global-category-btn.active {
                border-color: var(--salmon-pink);
                background: var(--cultured);
                color: var(--salmon-pink);
            }

            .global-category-name {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                text-align: left;
            }

            .global-sub-list {
                display: none;
                padding: 8px 0 4px 18px;
            }

            .global-sub-list.active {
                display: block;
            }

            .global-sub-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                text-decoration: none;
                color: var(--davys-gray);
                background: var(--cultured);
                border: 1px solid var(--cultured);
                border-radius: 13px;
                padding: 9px 10px;
                margin-bottom: 6px;
                font-size: 13px;
                font-weight: 750;
            }

            .global-sub-count {
                min-width: 26px;
                height: 24px;
                border-radius: 999px;
                background: var(--cultured);
                color: var(--salmon-pink);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 900;
            }

            body.sidebar-backdrop-lock {
                overflow: hidden;
            }
        }

        .search-suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 8px;
        }

        .search-suggestion-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: var(--eerie-black);
            transition: background 0.2s;
        }

        .search-suggestion-item:last-child {
            border-bottom: none;
        }

        .search-suggestion-item:hover {
            background: #f8fafc;
            color: var(--salmon-pink);
        }

        .search-suggestion-img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 8px;
            background: #f1f5f9;
            padding: 2px;
        }

        .search-suggestion-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .search-suggestion-title {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-suggestion-cat {
            font-size: 11px;
            color: var(--davys-gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'products-page' : '' ?>">
    <?php
    $logo = [
        "img" => "./assets/images/logo/1.png",
        "company" => "Softpro Innovations (P) LTD",
        "tagline1" => "A Company Founded By Technocrates",
        "tagline2" => "From IIT Kanpur and IET Lucknow"
    ];

    $menu = [
        "Home" => "index.php",
        "About" => [
            "About us" => "about.php",
            "Director's Profile" => "directors.php",
            // "CEO Profile" => "ceo.php",
            "Lead Engineer Profile" => "akashsir.php"
        ],
        "Training/Internship" => "training.php",
        "Products" => "products.php",
        "Services" => "services.php",
        "Buy Projects" => [
            "Mini Projects" => "mini-projects.php",
            "Major Projects" => "major-projects.php"
        ],
        "Lab Setup" => "coe.php",
        "Contact Us" => "contactus.php"
    ];
    ?>

    <div class="overlay" data-overlay></div>


    <!-- Login And Register Modal -->

    <div class="auth-modal" id="authModal">
        <div class="auth-overlay" onclick="closeAuthModal()"></div>

        <div class="auth-content">
            <button class="auth-close" onclick="closeAuthModal()">&times;</button>

            <!-- LOGIN FORM -->
            <div class="login-card" id="loginForm">
                <div class="auth-header">
                    <div class="auth-brand">
                        <!-- <div class="auth-brand-icon">S</div> -->
                        <span class="auth-brand-name">Softpro Innovations</span>
                    </div>
                    <p>Sign in to your account</p>
                </div>

                <span class="auth-tag">User Login</span>

                <form>
                    <input type="hidden" id="login_role" value="user">
                    <div class="input-box">
                        <label>Email Address</label>
                        <div class="input-field-wrap">
                            <i class="fa-regular fa-envelope field-icon"></i>
                            <input type="email" id="login_email" placeholder="Enter your email" required />
                        </div>
                    </div>
                    <div class="input-box">
                        <label>Password</label>
                        <div class="input-field-wrap">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="login_password" placeholder="Enter password" required />
                        </div>
                    </div>
                    <div class="forgot-row">
                        <a href="login.php">Forgot Password?</a>
                    </div>
                    <button type="button" class="login-btn" onclick="loginUser()">Login to Account</button>
                    <p id="loginMsg"></p>
                    <div class="auth-divider"><span>or</span></div>
                    <div class="links">
                        <button type="button" class="auth-link-btn" onclick="showRegister()">
                            Don't have an account? <strong>Register</strong>
                        </button>
                    </div>
                </form>
            </div>

            <!-- REGISTER FORM -->
            <div class="login-card d-none" id="registerForm">
                <div class="auth-header">
                    <div class="auth-brand">
                        <div class="auth-brand-icon">S</div>
                        <span class="auth-brand-name">Softpro Innovations</span>
                    </div>
                </div>

                <span class="auth-tag">New Registration</span>

                <form>
                    <div class="input-box">
                        <label>Username</label>
                        <div class="input-field-wrap">
                            <i class="fa-regular fa-user field-icon"></i>
                            <input type="text" id="reg_username" placeholder="Enter username" required />
                        </div>
                    </div>
                    <div class="input-box">
                        <label>Email</label>
                        <div class="input-field-wrap">
                            <i class="fa-regular fa-envelope field-icon"></i>
                            <input type="email" id="reg_email" placeholder="Enter email" required />
                        </div>
                    </div>
                    <div class="input-box">
                        <label>Password</label>
                        <div class="input-field-wrap">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="reg_password" placeholder="Create password" required />
                        </div>
                    </div>
                    <div class="input-box">
                        <label>Mobile Number</label>
                        <div class="input-field-wrap">
                            <i class="fa-solid fa-phone field-icon"></i>
                            <input type="text" id="reg_mobile" placeholder="Enter mobile number" required />
                        </div>
                    </div>
                    <button type="button" class="login-btn" onclick="registerUser()">Create My Account</button>
                    <p id="regMsg"></p>
                    <div class="auth-divider"><span>or</span></div>
                    <div class="links">
                        <button type="button" class="auth-link-btn" onclick="showLogin()">
                            Already have an account? <strong>Login</strong>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    /* =====================================================
       PRODUCT PURCHASE TOAST
       Fixes:
       - Toast will show on desktop + mobile.
       - Mobile par bottom navigation ke upar show hoga.
       - Broken image nahi aayegi: multiple paths + SVG fallback.
       - Close karne ke baad only current page load me hide hoga,
         sessionStorage me permanently hide nahi hoga.
    ===================================================== */
    $toastData = [];

    function sip_toast_asset($webPath)
    {
        $webPath = trim((string)$webPath);
        if ($webPath === '') return '';

        $clean = preg_replace('#^\./#', '', str_replace('\\', '/', $webPath));
        return file_exists(__DIR__ . '/' . $clean) ? './' . $clean : '';
    }

    function sip_toast_product_image($mainImage)
    {
        $mainImage = trim((string)$mainImage);
        $mainImage = str_replace('\\', '/', $mainImage);
        $mainImage = preg_replace('#^\./#', '', ltrim($mainImage, '/'));

        $fallbacks = [
            'assets/images/no-image.svg',
            'assets/images/no-image.png',
            'assets/images/logo/1.png'
        ];

        $candidates = [];

        if ($mainImage !== '') {
            $base = basename($mainImage);
            $candidates = [
                $mainImage,
                'uploads/products/' . $base,
                'uploads/product/' . $base,
                'uploads/' . $base,
                'assets/images/products/' . $base,
                'assets/images/' . $base
            ];
        }

        foreach (array_merge($candidates, $fallbacks) as $path) {
            $found = sip_toast_asset($path);
            if ($found !== '') return $found;
        }

        return './assets/images/no-image.svg';
    }

    if (isset($conn)) {
        $toast_products = mysqli_query($conn, "
            SELECT id, title, main_image
            FROM products
            WHERE status = 1
            ORDER BY id DESC
            LIMIT 12
        ");

        while ($toast_products && ($tp = mysqli_fetch_assoc($toast_products))) {
            $title = trim((string)($tp['title'] ?? ''));
            $toastData[] = [
                'id'    => (int)$tp['id'],
                'title' => htmlspecialchars($title !== '' ? $title : 'Product', ENT_QUOTES),
                'image' => sip_toast_product_image($tp['main_image'] ?? ''),
                'link'  => 'product.php?id=' . (int)$tp['id']
            ];
        }
    }
    ?>

    <?php if (!empty($toastData)): ?>
        <a href="#" id="toastLink" class="toast-link-wrap" aria-label="Recently bought product">
            <div class="notification-toast" id="productToast">
                <button class="toast-close-btn" id="toastClose" type="button" aria-label="Close toast">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
                <div class="toast-banner">
                    <img src="./assets/images/no-image.svg" alt="Product" id="toastImage" loading="lazy">
                </div>
                <div class="toast-detail">
                    <p class="toast-message">Someone recently bought</p>
                    <p class="toast-title" id="toastTitle">Product</p>
                    <p class="toast-meta"><time id="toastTime">1 Minute</time> ago</p>
                </div>
            </div>
        </a>

        <style>
            #toastLink.toast-link-wrap {
                position: fixed !important;
                left: 20px !important;
                bottom: 20px !important;
                z-index: 1200 !important;
                display: block !important;
                width: min(320px, calc(100vw - 32px)) !important;
                text-decoration: none !important;
                color: inherit !important;
            }

            #productToast.notification-toast {
                position: static !important;
                width: 100% !important;
                background: #fff !important;
                border-radius: 18px !important;
                padding: 14px !important;
                display: flex !important;
                align-items: center !important;
                gap: 14px !important;
                box-shadow: 0 15px 45px rgba(0, 0, 0, 0.16) !important;
                border: 1px solid #ededed !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateY(30px) !important;
                transition: all 0.35s ease !important;
                cursor: pointer !important;
                pointer-events: auto !important;
            }

            #productToast.notification-toast.show {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateY(0) !important;
            }

            #productToast .toast-close-btn {
                position: absolute !important;
                top: 10px !important;
                right: 10px !important;
                width: 24px !important;
                height: 24px !important;
                border: 0 !important;
                background: transparent !important;
                color: #555 !important;
                font-size: 19px !important;
                display: grid !important;
                place-items: center !important;
                cursor: pointer !important;
                z-index: 2 !important;
            }

            #productToast .toast-banner {
                width: 72px !important;
                min-width: 72px !important;
                height: 72px !important;
                border-radius: 12px !important;
                background: #f6f7fb !important;
                border: 1px solid #e8e8e8 !important;
                overflow: hidden !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            #productToast .toast-banner img {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
                object-fit: contain !important;
                object-position: center !important;
                padding: 5px !important;
            }

            #productToast .toast-detail {
                min-width: 0 !important;
                padding-right: 25px !important;
            }

            #productToast .toast-message {
                margin: 0 0 4px !important;
                font-size: 13px !important;
                color: #777 !important;
                line-height: 1.25 !important;
            }

            #productToast .toast-title {
                margin: 0 0 2px !important;
                max-width: 185px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                font-size: 14px !important;
                font-weight: 700 !important;
                color: #222 !important;
                line-height: 1.25 !important;
            }

            #productToast .toast-meta {
                margin: 0 !important;
                font-size: 12px !important;
                color: #8a8a8a !important;
                line-height: 1.25 !important;
            }

            @media (max-width: 1023px) {
                #toastLink.toast-link-wrap {
                    left: 12px !important;
                    right: 12px !important;
                    bottom: 88px !important;
                    width: auto !important;
                    max-width: 330px !important;
                    z-index: 1100 !important;
                }

                #productToast.notification-toast {
                    padding: 12px !important;
                    border-radius: 16px !important;
                    gap: 12px !important;
                }

                #productToast .toast-banner {
                    width: 62px !important;
                    min-width: 62px !important;
                    height: 62px !important;
                }

                #productToast .toast-title {
                    max-width: 190px !important;
                }
            }
        </style>

        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastProducts = <?= json_encode($toastData, JSON_UNESCAPED_SLASHES) ?>;
                if (!Array.isArray(toastProducts) || toastProducts.length === 0) return;

                const toast = document.getElementById('productToast');
                const toastImage = document.getElementById('toastImage');
                const toastTitle = document.getElementById('toastTitle');
                const toastTime = document.getElementById('toastTime');
                const toastLink = document.getElementById('toastLink');
                const toastClose = document.getElementById('toastClose');
                if (!toast || !toastImage || !toastTitle || !toastTime || !toastLink || !toastClose) return;

                let index = 0;
                let toastInterval = null;
                let toastHideTimer = null;
                let toastClosed = false;
                const fallbackImage = './assets/images/no-image.svg';
                const fakeTimes = ['1 Minute', '2 Minutes', '3 Minutes', '5 Minutes', '8 Minutes'];

                function hideToast() {
                    toast.classList.remove('show');
                }

                function stopToast() {
                    toastClosed = true;
                    hideToast();
                    if (toastHideTimer) clearTimeout(toastHideTimer);
                    if (toastInterval) clearInterval(toastInterval);
                }

                function showToast() {
                    if (toastClosed) return;

                    const product = toastProducts[index] || toastProducts[0];
                    const title = (product.title || 'Product').toString().trim() || 'Product';
                    const image = (product.image || fallbackImage).toString().trim() || fallbackImage;

                    toastImage.onerror = function() {
                        this.onerror = null;
                        this.src = fallbackImage;
                    };

                    toastImage.src = image;
                    toastImage.alt = title;
                    toastTitle.textContent = title;
                    toastTime.textContent = fakeTimes[index % fakeTimes.length];
                    toastLink.href = product.link || '#';

                    toast.classList.add('show');

                    if (toastHideTimer) clearTimeout(toastHideTimer);
                    toastHideTimer = setTimeout(hideToast, 5200);

                    index = (index + 1) % toastProducts.length;
                }

                toastClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    stopToast();
                });

                setTimeout(showToast, 1500);
                toastInterval = setInterval(showToast, 7500);
            });
        </script>
    <?php endif; ?>

    <header>

        <!-- ================= HEADER MAIN ================= -->
        <div class="header-main">
            <div class="container">

                <!-- LOGO -->
                <div class="logo">
                    <a href="index.php" class="header-logo">
                        <img src="<?= $logo['img']; ?>" width="80" height="80">
                    </a>
                    <div class="logo-text">
                        <h3 class="company-name" style="font-family: 'Revue BT','RevueBT','Revue', Georgia, serif !important;">
                            <?= $logo['company']; ?>
                        </h3>
                        <p><?= $logo['tagline1']; ?></p>
                        <p><?= $logo['tagline2']; ?></p>
                    </div>
                </div>

                <!-- SEARCH -->
                <form class="header-search-container" method="get" action="products.php" style="position: relative;">
                    <input type="search" name="q" id="headerSearchInput" class="search-field" placeholder="Enter your product name..." autocomplete="off">
                    <button class="search-btn" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <div id="headerSearchSuggestions" class="search-suggestions-dropdown" style="display:none;"></div>
                </form>

                <!-- USER ACTIONS -->
                <div class="header-user-actions">
                    <?php if ($user): ?>
                        <div class="dropdown">
                            <button type="button" class="action-btn linkless-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user-circle fs-2"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="user_profile.php?tab=profile">Profile</a></li>
                                <li><a class="dropdown-item" href="user_profile.php?tab=password">Change Password</a></li>
                                <li><a class="dropdown-item" href="user_profile.php?tab=latest">Orders</a></li>
                                <li><a class="dropdown-item" href="user_profile.php?tab=addresses">Addresses</a></li>
                                <li><a class="dropdown-item" href="user_profile.php?tab=track">Track Order</a></li>
                                <li><a class="dropdown-item" href="wishlist.php">Wishlist</a></li>
                                <li><button class="dropdown-item" type="button" onclick="logout()">Logout</button></li>
                            </ul>
                        </div>
                    <?php else: ?>

                        <button type="button" class="action-btn linkless-action" onclick="openAuthModal()"><i class="fa-regular fa-user"></i></button>
                    <?php endif; ?>
                    <!-- Wishlist -->
                    <button class="action-btn">
                        <a href="wishlist.php">
                            <i class="fa-regular fa-heart"></i>
                            <span class="count" id="wishlist-count"><?= $wishlist_count; ?></span>
                        </a>
                    </button>

                    <!-- Cart -->
                    <button class="action-btn">
                        <a href="cart.php">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span class="count" id="cart-count"><?= $cart_count; ?></span>
                        </a>
                    </button>

                    <!-- Compare -->
                    <button class="action-btn">
                        <a href="compare.php">
                            <i class="fa-solid fa-exchange-alt"></i>
                            <span class="count" id="compare-count"><?= $compare_count; ?></span>
                        </a>
                    </button>

                </div>

            </div>
        </div>

        <!-- ================= DESKTOP MENU ================= -->
        <nav class="desktop-navigation-menu">
            <div class="container-fluid">
                <ul class="desktop-menu-category-list nav-menu">
                    <?php foreach ($menu as $title => $value): ?>
                        <?php if (is_array($value)): ?>
                            <li class="menu-category">
                                <a href="<?= reset($value); ?>" class="menu-title"><?= $title; ?></a>
                                <ul class="dropdown-list">
                                    <?php foreach ($value as $sub => $link): ?>
                                        <li class="dropdown-item">
                                            <a href="<?= $link; ?>"><?= $sub; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="menu-category">
                                <a href="<?= $value; ?>" class="menu-title"><?= $title; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </ul>
            </div>
        </nav>

        <!-- ================= MOBILE OFFCANVAS MAIN MENU ================= -->
        <aside class="mobile-main-menu" data-main-mobile-menu>
            <div class="mobile-main-menu-head">
                <strong>Menu</strong>
                <button type="button" data-main-menu-close aria-label="Close menu"><ion-icon name="close-outline"></ion-icon></button>
            </div>
            <ul class="mobile-main-menu-list">
                <?php foreach ($menu as $title => $value): ?>
                    <?php if (is_array($value)): ?>
                        <li class="mobile-menu-has-sub">
                            <div class="mobile-menu-row">
                                <a href="<?= reset($value); ?>"><?= $title; ?></a>
                                <button type="button" class="mobile-menu-toggle" aria-label="Open <?= htmlspecialchars($title); ?> submenu" aria-expanded="false">
                                    <ion-icon name="add-outline"></ion-icon>
                                </button>
                            </div>
                            <div class="mobile-sub-links">
                                <?php foreach ($value as $sub => $link): ?>
                                    <a href="<?= $link; ?>"><?= $sub; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= $value; ?>"><?= $title; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="mobile-menu-quick-actions" aria-label="Quick links">
                <a href="wishlist.php" class="mobile-menu-quick-action">
                    <i class="fa-regular fa-heart"></i>
                    <span class="count" id="mobile-wishlist-count"><?= $wishlist_count; ?></span>
                    <small>Wishlist</small>
                </a>
                <a href="cart.php" class="mobile-menu-quick-action">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="count" id="mobile-menu-cart-count"><?= $cart_count; ?></span>
                    <small>Cart</small>
                </a>
                <a href="compare.php" class="mobile-menu-quick-action">
                    <i class="fa-solid fa-exchange-alt"></i>
                    <span class="count" id="mobile-compare-count"><?= $compare_count; ?></span>
                    <small>Compare</small>
                </a>
            </div>
        </aside>


        <!-- ================= MOBILE CATEGORY DRAWER: EVERY PAGE ================= -->
        <aside class="global-mobile-category-drawer" data-category-menu>
            <div class="global-category-card">
                <div class="global-category-head">
                    <h2 class="global-category-title">Cate<span>gory</span></h2>
                    <button class="global-category-close" data-category-close type="button" aria-label="Close category">
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>
                <ul class="global-category-list">
                    <?php foreach ($mobileSidebarCategories as $cat): ?>
                        <li class="global-category-item">
                            <button class="global-category-btn" data-global-category-btn type="button">
                                <span class="global-category-name"><?= htmlspecialchars($cat['title']); ?></span>
                                <ion-icon name="add-outline"></ion-icon>
                            </button>
                            <ul class="global-sub-list" data-global-category-panel>
                                <?php if (!empty($cat['sub'])): ?>
                                    <?php foreach ($cat['sub'] as $sub): ?>
                                        <li>
                                            <a href="<?= $sub['link']; ?>" class="global-sub-link">
                                                <span><?= htmlspecialchars($sub['title']); ?></span>
                                                <span class="global-sub-count"><?= (int)$sub['count']; ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <a href="<?= $cat['link']; ?>" class="global-sub-link">
                                            <span>View Products</span>
                                            <span class="global-sub-count">0</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- ================= MOBILE BOTTOM ================= -->
        <div class="mobile-bottom-navigation">
            <button class="action-btn" type="button" data-main-menu-open aria-label="Open menu">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <button class="action-btn" type="button">
                <a href="cart.php"><ion-icon name="bag-handle-outline"></ion-icon><span class="count" id="mobile-cart-count"><?= $cart_count; ?></span></a>
            </button>

            <button class="action-btn" type="button">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon></a>
            </button>

            <div class="action-btn">
                <?php if ($user): ?><a href="user_profile.php?tab=profile"><i class="fa fa-user-circle fs-2"></i></a><?php else: ?><button type="button" class="linkless-action" onclick="openAuthModal()"><i class="fa-regular fa-user"></i></button><?php endif; ?>
            </div>

            <button class="action-btn" type="button" data-category-open aria-label="Open categories">
                <ion-icon name="grid-outline"></ion-icon>
            </button>
        </div>

    </header>

    <script>
        function openAuthModal() {
            const modal = document.getElementById('authModal');
            if (!modal) return;
            modal.classList.add('open');
            document.body.classList.add('auth-modal-open');
            showLogin();
        }

        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            if (!modal) return;
            modal.classList.remove('open');
            document.body.classList.remove('auth-modal-open');
        }

        function showLogin() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            if (loginForm) loginForm.classList.remove('d-none');
            if (registerForm) registerForm.classList.add('d-none');
            const loginMsg = document.getElementById('loginMsg');
            const regMsg = document.getElementById('regMsg');
            if (loginMsg) loginMsg.innerText = '';
            if (regMsg) regMsg.innerText = '';
        }

        function showRegister() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            if (loginForm) loginForm.classList.add('d-none');
            if (registerForm) registerForm.classList.remove('d-none');
            const loginMsg = document.getElementById('loginMsg');
            const regMsg = document.getElementById('regMsg');
            if (loginMsg) loginMsg.innerText = '';
            if (regMsg) regMsg.innerText = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAuthModal();
        });
    </script>

    <script>
        window.SIP_BASE_URL = <?= json_encode((function () {
                                    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
                                    $dir = rtrim($dir, '/');
                                    return ($dir === '' || $dir === '.') ? '' : $dir;
                                })()); ?>;

        function sipUrl(path) {
            path = String(path || '').replace(/^\/+/, '');
            return (window.SIP_BASE_URL || '') + '/' + path;
        }
    </script>
    <script>
        function loginUser() {
            const loginInput = document.getElementById('login_email');
            const passInput = document.getElementById('login_password');
            const msg = document.getElementById('loginMsg');
            const btn = document.querySelector('#loginForm .login-btn');
            const loginId = loginInput ? loginInput.value.trim() : '';
            const password = passInput ? passInput.value : '';

            if (!loginId || !password) {
                if (msg) {
                    msg.innerText = 'Please enter email/mobile and password';
                    msg.style.color = 'red';
                }
                return;
            }

            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Please wait...';
            }
            if (msg) msg.innerText = '';

            fetch(sipUrl('login.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        loginId,
                        password
                    })
                })
                .then(async r => {
                    const text = await r.text();
                    let d = null;
                    try {
                        d = JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server returned invalid response from login.php');
                    }
                    if (!r.ok) {
                        throw new Error(d.message || 'Login request failed');
                    }
                    return d;
                })
                .then(d => {
                    if (d.status === 'success') {
                        if (msg) {
                            msg.innerText = 'Login successful. Redirecting...';
                            msg.style.color = 'green';
                        }
                        window.location.href = sipUrl(d.redirect || 'index.php');
                    } else {
                        if (msg) {
                            msg.innerText = d.message || 'Invalid login details';
                            msg.style.color = 'red';
                        }
                    }
                })
                .catch((err) => {
                    if (msg) {
                        msg.innerText = err && err.message ? err.message : 'Login request failed. Please try again.';
                        msg.style.color = 'red';
                    }
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerText = 'Login to Account';
                    }
                });
        }

        function registerUser() {
            const username = document.getElementById('reg_username').value;
            const email = document.getElementById('reg_email').value;
            const password = document.getElementById('reg_password').value;
            const mobile = document.getElementById('reg_mobile').value;

            fetch(sipUrl('register.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username,
                        email,
                        password,
                        mobile
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('regMsg');

                    if (data.status === 'success') {

                        msg.style.color = 'green';
                        msg.innerText = 'Registration successful. Please login.';

                        setTimeout(() => {
                            showLogin();

                            document.getElementById('login_email').value = email;
                            document.getElementById('login_password').focus();
                        }, 800);

                    } else {
                        msg.style.color = 'red';
                        msg.innerText = data.message;
                    }
                });
        }

        function logout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = 'logout.php';
            }
        }


        document.addEventListener('click', function(event) {
            const toggle = event.target.closest('.mobile-menu-toggle');
            if (!toggle) return;

            event.preventDefault();
            event.stopPropagation();

            const currentItem = toggle.closest('.mobile-menu-has-sub');
            if (!currentItem) return;

            const alreadyOpen = currentItem.classList.contains('open');

            document.querySelectorAll('.mobile-menu-has-sub.open').forEach(function(item) {
                item.classList.remove('open');
                const btn = item.querySelector('.mobile-menu-toggle');
                const icon = item.querySelector('.mobile-menu-toggle ion-icon');
                if (btn) btn.setAttribute('aria-expanded', 'false');
                if (icon) icon.setAttribute('name', 'add-outline');
            });

            if (!alreadyOpen) {
                currentItem.classList.add('open');
                toggle.setAttribute('aria-expanded', 'true');
                const icon = toggle.querySelector('ion-icon');
                if (icon) icon.setAttribute('name', 'remove-outline');
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function updateHeaderCounts() {
            $.getJSON('get_counts.php', function(data) {
                const cart = data.cart || 0;
                const wishlist = data.wishlist || 0;
                const compare = data.compare || 0;
                $("#cart-count, #mobile-cart-count, #mobile-menu-cart-count").text(cart);
                $("#wishlist-count, #mobile-wishlist-count").text(wishlist);
                $("#compare-count, #mobile-compare-count").text(compare);
            });
        }

        // ✅ Page load par call
        $(document).ready(function() {
            updateHeaderCounts();
        });
    </script>
    <script>
        function ecomToast(message) {
            let box = document.getElementById('ecomToastBox');
            if (!box) {
                box = document.createElement('div');
                box.id = 'ecomToastBox';
                box.className = 'ecom-toast';
                document.body.appendChild(box);
            }

            box.innerHTML = `
                <span class="ecom-toast-msg"></span>
                <button type="button" class="ecom-toast-close" aria-label="Close">&times;</button>
            `;
            box.querySelector('.ecom-toast-msg').textContent = message;
            box.style.display = 'flex';

            const closeBtn = box.querySelector('.ecom-toast-close');
            closeBtn.onclick = function(e) {
                e.preventDefault();
                box.style.display = 'none';
                clearTimeout(window.__ecomToastTimer);
            };

            clearTimeout(window.__ecomToastTimer);
            window.__ecomToastTimer = setTimeout(() => {
                box.style.display = 'none';
            }, 2200);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('headerSearchInput');
            const searchDropdown = document.getElementById('headerSearchSuggestions');
            let debounceTimer;

            if (searchInput && searchDropdown) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();

                    if (query.length === 0) {
                        searchDropdown.style.display = 'none';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        fetch(sipUrl(`ajax/search_suggestions.php?q=${encodeURIComponent(query)}`))
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    searchDropdown.innerHTML = data.map(item => `
                                        <a href="${item.link}" class="search-suggestion-item">
                                            <img src="${sipUrl(item.image)}" alt="${item.title}" class="search-suggestion-img" onerror="this.onerror=null; this.src='${sipUrl('assets/images/logo/1.png')}'">
                                            <div class="search-suggestion-info">
                                                <span class="search-suggestion-title">${item.title}</span>
                                                <span class="search-suggestion-cat">${item.category}</span>
                                            </div>
                                        </a>
                                    `).join('');
                                    searchDropdown.style.display = 'block';
                                } else {
                                    searchDropdown.innerHTML = '<div style="padding: 12px 16px; color: #777; font-size: 13px;">No products found</div>';
                                    searchDropdown.style.display = 'block';
                                }
                            })
                            .catch(error => console.error('Error fetching search suggestions:', error));
                    }, 300);
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                        searchDropdown.style.display = 'none';
                    }
                });
            }
        });
    </script>