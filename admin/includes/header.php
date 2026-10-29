<?php
require_once __DIR__ . '/../check_admin.php';
$adminHtmlTitle = isset($adminHtmlTitle) ? (string) $adminHtmlTitle : 'Dashboard';
$adminDisplayName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!doctype html>

<html
    lang="en"
    class="layout-menu-fixed layout-compact"
    data-assets-path="./assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo htmlspecialchars($adminHtmlTitle, ENT_QUOTES, 'UTF-8'); ?> · SIP Innovation Admin</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <!-- <link rel="icon" type="image/x-icon" href="./assets/img/favicon/favicon.ico" /> -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Iconify Icons -->
    <link rel="stylesheet" href="./assets/vendor/fonts/iconify-icons.css" />

    <!-- Boxicons CSS - Required for bx icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="./assets/vendor/css/core.css" />
    <link rel="stylesheet" href="./assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="./assets/vendor/js/helpers.js"></script>
    <script src="./assets/js/config.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <!-- Common Admin DataTable CSS -->
    <link rel="stylesheet" href="./assets/css/admin-datatable.css">

    <style>
        .app-brand-text.demo { letter-spacing: -0.02em; }
        .layout-menu .menu-inner > .menu-item > .menu-link.menu-toggle { font-weight: 600; }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

                <!-- LOGO -->
                <div class="app-brand demo">
                    <a href="dashboard.php" class="app-brand-link">
                        <span class="app-brand-text demo fw-bold ms-2">SIP Innovation</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="bx bx-chevron-left d-block d-xl-none"></i>
                    </a>
                </div>

                <div class="menu-divider mt-0"></div>
                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <a href="dashboard.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-alt"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <!-- <?php if (admin_can('coupons')): ?>
                    <li class="menu-item">
                        <a href="coupons.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-gift"></i>
                            <div>Coupons</div>
                        </a>
                    </li>
                    <?php endif; ?> -->


                    <!-- PRODUCTS -->
                    <?php if (admin_can('products')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-package"></i>
                            <div>Products Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="products.php" class="menu-link">
                                    <i class="bx bx-store-alt me-2"></i> All Products
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- CATEGORIES -->
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-category-alt"></i>
                            <div>Categories</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="categories.php" class="menu-link">
                                    <i class="bx bx-grid-alt me-2"></i> All Categories
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="sub_categories.php" class="menu-link">
                                    <i class="bx bx-sitemap me-2"></i> Sub-Categories
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- STOCK -->
                    <?php if (admin_can('stock')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                            <div>Stock Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="stock_list.php" class="menu-link">
                                    <i class="bx bx-list-check me-2"></i> Stock List
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="low_stock.php" class="menu-link">
                                    <i class="bx bx-error-circle me-2 text-warning"></i> Low Stock Products
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="out_stock.php" class="menu-link">
                                    <i class="bx bx-block me-2 text-danger"></i> Out of Stock Products
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="stock_logs.php" class="menu-link">
                                    <i class="bx bx-time-five me-2"></i> Stock History / Logs
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- BRANDS -->
                    <?php if (admin_can('products')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-award"></i>
                            <div>Brands</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="brands.php" class="menu-link">
                                    <i class="bx bx-purchase-tag-alt me-2"></i> All Brands
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- ATTRIBUTES -->
                    <?php if (admin_can('products')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-customize"></i>
                            <div>Product Attributes</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="attributes.php" class="menu-link">
                                    <i class="bx bx-cog me-2"></i> Attributes
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="attribute_values.php" class="menu-link">
                                    <i class="bx bx-detail me-2"></i> Attribute Values
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="variants.php" class="menu-link">
                                    <i class="bx bx-git-branch me-2"></i> Variants Management
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- PROJECTS -->
                    <?php if (admin_can('projects')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-briefcase-alt-2"></i>
                            <div>Projects</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="projects.php" class="menu-link">
                                    <i class="bx bx-folder-plus me-2"></i> Projects
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- PRICE -->
                    <?php if (admin_can('price')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-wallet-alt"></i>
                            <div>Price Management</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="product_price.php" class="menu-link">
                                    <i class="bx bx-money-withdraw me-2"></i> Product Price
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="discount_price.php" class="menu-link">
                                    <i class="bx bx-badge-percent me-2"></i> Discount Price
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="tax.php" class="menu-link">
                                    <i class="bx bx-receipt me-2"></i> Tax (GST / VAT)
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- MEDIA -->
                    <?php if (admin_can('products')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-images"></i>
                            <div>Media / Images</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="product_images.php" class="menu-link">
                                    <i class="bx bx-image-add me-2"></i> Product Images
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- SUPPLIERS -->
                    <?php if (admin_can('suppliers')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-buildings"></i>
                            <div>Suppliers / Vendors</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="suppliers.php" class="menu-link">
                                    <i class="bx bx-user-pin me-2"></i> Supplier List
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="purchase_history.php" class="menu-link">
                                    <i class="bx bx-history me-2"></i> Purchase History
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="supplier_products.php" class="menu-link">
                                    <i class="bx bx-box me-2"></i> Supplier Products
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- REPORTS -->
                    <?php if (admin_can('reports')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-line-chart"></i>
                            <div>Inventory Reports</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="stock_report.php" class="menu-link">
                                    <i class="bx bx-bar-chart-square me-2"></i> Stock Report
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="low_stock_report.php" class="menu-link">
                                    <i class="bx bx-error-alt me-2"></i> Low Stock Report
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="inventory_valuation.php" class="menu-link">
                                    <i class="bx bx-calculator me-2"></i> Inventory Valuation
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="product_movement.php" class="menu-link">
                                    <i class="bx bx-transfer-alt me-2"></i> Product Movement
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- BULK -->
                    <?php if (admin_can('products') || admin_can('stock') || admin_can('price')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layer-plus"></i>
                            <div>Bulk Actions</div>
                        </a>
                        <ul class="menu-sub">
                            <?php if (admin_can('products')): ?>
                            <li class="menu-item">
                                <a href="bulk_upload.php" class="menu-link">
                                    <i class="bx bx-cloud-upload me-2"></i> Bulk Upload (CSV / Excel)
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (admin_can('stock')): ?>
                            <li class="menu-item">
                                <a href="bulk_stock.php" class="menu-link">
                                    <i class="bx bx-refresh me-2"></i> Bulk Stock Update
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (admin_can('price')): ?>
                            <li class="menu-item">
                                <a href="bulk_price.php" class="menu-link">
                                    <i class="bx bx-rupee me-2"></i> Bulk Price Update
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- ORDERS -->
                    <?php if (admin_can('orders')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
                            <div>Orders</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="orders.php" class="menu-link">
                                    <i class="bx bx-clipboard me-2"></i> All Orders
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="pending_orders.php" class="menu-link">
                                    <i class="bx bx-time me-2"></i> Pending Orders
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="completed_orders.php" class="menu-link">
                                    <i class="bx bx-check-circle me-2"></i> Completed Orders
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="cancelled_orders.php" class="menu-link">
                                    <i class="bx bx-x-circle me-2"></i> Cancel / Returns
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="refunds.php" class="menu-link text-danger">
                                    <i class="bx bx-money-withdraw me-2"></i> Refunds
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- CUSTOMERS -->
                    <?php if (admin_can('customers')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div>Customers</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="customers.php" class="menu-link">
                                    <i class="bx bx-user-detail me-2"></i> All Customers
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="customer_orders.php" class="menu-link">
                                    <i class="bx bx-cart-alt me-2"></i> Customer Orders
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="customer_addresses.php" class="menu-link">
                                    <i class="bx bx-map-pin me-2"></i> Addresses
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- CONTACT -->
                    <?php if (admin_can('customers')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-phone-call"></i>
                            <div>Contact</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="contact.php" class="menu-link">
                                    <i class="bx bx-envelope me-2"></i> Contact Detail
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- PAYMENTS -->
                    <?php if (admin_can('payments')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-credit-card-front"></i>
                            <div>Payments</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="payment_methods.php" class="menu-link">
                                    <i class="bx bx-credit-card me-2"></i> Payment Methods
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="transactions.php" class="menu-link">
                                    <i class="bx bx-transfer me-2"></i> Transactions
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- SHIPPING -->
                    <?php if (admin_can('shipping')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-trip"></i>
                            <div>Shipping</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="shipping_zones.php" class="menu-link">
                                    <i class="bx bx-map-alt me-2"></i> Shipping Zones
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="shipping_charges.php" class="menu-link">
                                    <i class="bx bx-rupee me-2"></i> Shipping Charges
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="delivery_partners.php" class="menu-link">
                                    <i class="bx bx-cycling me-2"></i> Delivery Partners
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- TRAINING TECHNOLOGIES -->
                    <?php if (admin_can('services')): ?>
                    <li class="menu-item">
                        <a href="manage_training_technologies.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-book-reader"></i>
                            <div>Training Technologies</div>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- COUPONS -->
                    <?php if (admin_can('coupons')): ?>
                    <li class="menu-item">
                        <a href="coupons.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-gift"></i>
                            <div>Coupons</div>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- REVIEWS -->
                    <?php if (admin_can('reviews')): ?>
                    <li class="menu-item">
                        <a href="product_reviews.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-star"></i>
                            <div>Reviews & Ratings</div>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- ADMIN -->
                    <?php if (admin_is_super_admin() || admin_can('admins')): ?>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                            <div>Admin Management</div>
                        </a>
                        <ul class="menu-sub">
                            <?php if (admin_can('admins')): ?>
                            <li class="menu-item">
                                <a href="admins.php" class="menu-link">
                                    <i class="bx bx-user-circle me-2"></i> Admin Users
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="activity_logs.php" class="menu-link">
                                    <i class="bx bx-history me-2"></i> Activity Logs
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (admin_is_super_admin()): ?>
                            <li class="menu-item">
                                <a href="roles.php" class="menu-link">
                                    <i class="bx bx-lock-alt me-2"></i> Roles & Permissions
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <nav
                    class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">

                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base bx bx-menu icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

                        <!-- Search -->
                        <div class="navbar-nav align-items-center me-auto">
                            <div class="nav-item d-flex align-items-center">
                                <span class="w-px-22 h-px-22">
                                    <i class="icon-base bx bx-search icon-md"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control border-0 shadow-none ps-1 ps-sm-2 d-md-block d-none"
                                    placeholder="Search..."
                                    aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a
                                    class="nav-link dropdown-toggle hide-arrow p-0"
                                    href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="./assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="dashboard.php">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="./assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?php echo $adminDisplayName; ?></h6>
                                                    <small class="text-body-secondary">Administrator</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>

                                    <?php if (admin_can('admins')): ?>
                                    <li>
                                        <a class="dropdown-item" href="admins.php">
                                            <i class="icon-base bx bx-user icon-md me-3"></i>
                                            <span>My Profile</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (admin_can('price')): ?>
                                    <li>
                                        <a class="dropdown-item" href="tax.php">
                                            <i class="icon-base bx bx-cog icon-md me-3"></i>
                                            <span>Tax &amp; pricing</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (admin_can('payments')): ?>
                                    <li>
                                        <a class="dropdown-item" href="payment_methods.php">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 icon-base bx bx-credit-card icon-md me-3"></i>
                                                <span class="flex-grow-1 align-middle">Payment methods</span>
                                            </span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="logout()">
                                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                                            <span>Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- /User -->

                        </ul>
                    </div>
                </nav>

                <script>
                    function logout() {
                        if (confirm("Are you sure you want to logout?")) {
                            window.location.href = 'logout.php';
                        }
                    }
                </script>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">