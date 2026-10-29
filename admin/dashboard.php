<?php include "includes/header.php" ?>
<?php
/* =========================================================
   FRONTEND STORE REPORT DASHBOARD
   DB based report only for frontend visible modules:
   Products, Projects, Categories, Training, Users/Customers,
   Orders, Revenue, Reviews, Cart, Wishlist, Compare, Contact.
   Admin-only modules like roles, suppliers, delivery settings are not shown.
   ========================================================= */

if (!function_exists('admin_price')) {
    function admin_price($amount)
    {
        if (function_exists('format_price')) {
            return format_price((float)$amount);
        }
        return number_format((float)$amount, 2);
    }
}

function single_value($conn, $sql, $key = 'total', $default = 0)
{
    $q = mysqli_query($conn, $sql);
    if (!$q) return $default;
    $row = mysqli_fetch_assoc($q);
    return $row[$key] ?? $default;
}

function safe_query($conn, $sql)
{
    $q = mysqli_query($conn, $sql);
    return $q ?: false;
}

/* =========================
   MAIN FRONTEND KPIs
   ========================= */

$total_revenue = single_value($conn, "
    SELECT COALESCE(SUM(total_amount), 0) AS total
    FROM orders
    WHERE payment_status = 'Paid'
      AND order_status NOT IN ('Cancelled', 'Returned')
");

$total_orders = single_value($conn, "SELECT COUNT(*) AS total FROM orders");

$paid_orders = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE payment_status = 'Paid'
");

$pending_orders = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE order_status = 'Pending'
");

$delivered_orders = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE order_status = 'Delivered'
");

$cancelled_orders = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE order_status IN ('Cancelled', 'Returned')
");

$total_customers = single_value($conn, "SELECT COUNT(*) AS total FROM users");

$total_products = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM products
    WHERE status = 1
");

$total_projects = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM projects
    WHERE status = 1
");

$total_categories = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM categories
    WHERE status = 1
");

$total_subcategories = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM sub_categories
    WHERE status = 1
");

$total_training = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM training_technologies
    WHERE status = 1
");

$total_reviews = single_value($conn, "
    SELECT COUNT(*) AS total
    FROM product_reviews
    WHERE status = 1
");

$total_wishlist = single_value($conn, "SELECT COUNT(*) AS total FROM wishlist");
$total_cart = single_value($conn, "SELECT COALESCE(SUM(qty), 0) AS total FROM cart");
$total_compare = single_value($conn, "SELECT COUNT(*) AS total FROM compare");
$total_contacts = single_value($conn, "SELECT COUNT(*) AS total FROM contact_messages");

$avg_order_value = $paid_orders > 0 ? ($total_revenue / $paid_orders) : 0;

/* =========================
   FRONTEND REPORT DATA
   ========================= */

$monthly_sales_q = safe_query($conn, "
    SELECT DATE_FORMAT(created_at, '%b %Y') AS month_name,
           COUNT(*) AS orders_count,
           COALESCE(SUM(total_amount), 0) AS revenue
    FROM orders
    WHERE payment_status = 'Paid'
      AND order_status NOT IN ('Cancelled', 'Returned')
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC
    LIMIT 6
");

$order_status_q = safe_query($conn, "
    SELECT order_status, COUNT(*) AS total
    FROM orders
    GROUP BY order_status
    ORDER BY total DESC
");

$payment_status_q = safe_query($conn, "
    SELECT payment_status, COUNT(*) AS total
    FROM orders
    GROUP BY payment_status
    ORDER BY total DESC
");

$top_products_q = safe_query($conn, "
    SELECT p.title,
           c.name AS category_name,
           COALESCE(SUM(oi.qty), 0) AS sold_qty,
           COALESCE(SUM(oi.price * oi.qty), 0) AS revenue
    FROM order_items oi
    INNER JOIN products p ON p.id = oi.product_id
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE oi.item_type = 'product'
    GROUP BY oi.product_id
    ORDER BY sold_qty DESC, revenue DESC
    LIMIT 5
");

$top_projects_q = safe_query($conn, "
    SELECT pr.title,
           pr.project_type,
           COALESCE(SUM(oi.qty), 0) AS sold_qty,
           COALESCE(SUM(oi.price * oi.qty), 0) AS revenue
    FROM order_items oi
    INNER JOIN projects pr ON pr.id = oi.product_id
    WHERE oi.item_type = 'project'
    GROUP BY oi.product_id
    ORDER BY sold_qty DESC, revenue DESC
    LIMIT 5
");

$category_sales_q = safe_query($conn, "
    SELECT c.name AS category_name,
           COUNT(DISTINCT p.id) AS product_count,
           COALESCE(SUM(oi.qty), 0) AS sold_qty
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id AND p.status = 1
    LEFT JOIN order_items oi ON oi.product_id = p.id AND oi.item_type = 'product'
    WHERE c.status = 1
    GROUP BY c.id
    ORDER BY sold_qty DESC, product_count DESC
    LIMIT 6
");

$low_stock_q = safe_query($conn, "
    SELECT title, stock, price
    FROM products
    WHERE status = 1 AND COALESCE(stock, 0) <= 5
    ORDER BY stock ASC, id DESC
    LIMIT 6
");

$recent_orders_q = safe_query($conn, "
    SELECT id, order_no, customer_name, customer_email, total_amount, payment_status, order_status, created_at
    FROM orders
    ORDER BY id DESC
    LIMIT 6
");

$recent_contacts_q = safe_query($conn, "
    SELECT name, email, subject, created_at
    FROM contact_messages
    ORDER BY id DESC
    LIMIT 5
");
?>

<?php if (!empty($_GET['denied'])): ?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        You do not have permission to open that page.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<style>
    .frontend-report-card {
        border: 1px solid rgba(67, 89, 113, .12);
        box-shadow: 0 2px 8px rgba(67, 89, 113, .08);
    }

    .frontend-report-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 22px;
    }

    .report-table td,
    .report-table th {
        vertical-align: middle;
    }

    .text-truncate-180 {
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="row">
    <div class="col-12 mb-6">
        <div class="card frontend-report-card">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-4 align-items-md-center">
                <div>
                    <h4 class="mb-2">Dashboard Report</h4>
                    <p class="mb-0 text-body-secondary">
                        This dashboard shows only frontend related data: products, projects, categories, training, users, orders, revenue, reviews, cart, wishlist, compare and contact enquiries.
                    </p>
                </div>
                <a href="orders.php" class="btn btn-primary">View Orders</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1">Paid Revenue</p>
                        <h4 class="mb-2">₹<?= admin_price($total_revenue) ?></h4>
                        <small class="text-body-secondary">Delivered/active paid orders</small>
                    </div>
                    <span class="frontend-report-icon bg-label-success"><i class="bx bx-rupee"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1">Total Orders</p>
                        <h4 class="mb-2"><?= number_format((float)$total_orders) ?></h4>
                        <small class="text-body-secondary">All checkout orders</small>
                    </div>
                    <span class="frontend-report-icon bg-label-primary"><i class="bx bx-cart"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1">Customers</p>
                        <h4 class="mb-2"><?= number_format((float)$total_customers) ?></h4>
                        <small class="text-body-secondary">Registered users</small>
                    </div>
                    <span class="frontend-report-icon bg-label-info"><i class="bx bx-user"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1">Average Order Value</p>
                        <h4 class="mb-2">₹<?= admin_price($avg_order_value) ?></h4>
                        <small class="text-body-secondary">Revenue / paid orders</small>
                    </div>
                    <span class="frontend-report-icon bg-label-warning"><i class="bx bx-line-chart"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Products</p>
                <h4 class="mb-0"><?= number_format((float)$total_products) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Projects</p>
                <h4 class="mb-0"><?= number_format((float)$total_projects) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Categories</p>
                <h4 class="mb-0"><?= number_format((float)$total_categories) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Subcategories</p>
                <h4 class="mb-0"><?= number_format((float)$total_subcategories) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Training Tech</p>
                <h4 class="mb-0"><?= number_format((float)$total_training) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Reviews</p>
                <h4 class="mb-0"><?= number_format((float)$total_reviews) ?></h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Wishlist Items</p>
                <h4 class="mb-0"><?= number_format((float)$total_wishlist) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Cart Quantity</p>
                <h4 class="mb-0"><?= number_format((float)$total_cart) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Compare Items</p>
                <h4 class="mb-0"><?= number_format((float)$total_compare) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-body">
                <p class="mb-1">Contact Enquiries</p>
                <h4 class="mb-0"><?= number_format((float)$total_contacts) ?></h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Monthly Revenue Report</h5>
                <span class="badge bg-label-primary">Last 6 Months</span>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Orders</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($monthly_sales_q && mysqli_num_rows($monthly_sales_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($monthly_sales_q)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['month_name']) ?></td>
                                    <td><?= number_format((float)$row['orders_count']) ?></td>
                                    <td class="text-end">₹<?= admin_price($row['revenue']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary">No paid revenue available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    <li class="d-flex justify-content-between mb-3"><span>Paid Orders</span><strong><?= number_format((float)$paid_orders) ?></strong></li>
                    <li class="d-flex justify-content-between mb-3"><span>Pending Orders</span><strong><?= number_format((float)$pending_orders) ?></strong></li>
                    <li class="d-flex justify-content-between mb-3"><span>Delivered Orders</span><strong><?= number_format((float)$delivered_orders) ?></strong></li>
                    <li class="d-flex justify-content-between"><span>Cancelled/Returned</span><strong><?= number_format((float)$cancelled_orders) ?></strong></li>
                </ul>
                <hr>
                <h6 class="mb-3">Payment Status</h6>
                <?php if ($payment_status_q && mysqli_num_rows($payment_status_q) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($payment_status_q)): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($row['payment_status']) ?></span>
                            <strong><?= number_format((float)$row['total']) ?></strong>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-body-secondary mb-0">No payment data.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top_products_q && mysqli_num_rows($top_products_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($top_products_q)): ?>
                                <tr>
                                    <td>
                                        <div class="text-truncate-180"><?= htmlspecialchars($row['title']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                    <td><?= number_format((float)$row['sold_qty']) ?></td>
                                    <td class="text-end">₹<?= admin_price($row['revenue']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary">No product sales available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Top Selling Projects</h5>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top_projects_q && mysqli_num_rows($top_projects_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($top_projects_q)): ?>
                                <tr>
                                    <td>
                                        <div class="text-truncate-180"><?= htmlspecialchars($row['title']) ?></div>
                                    </td>
                                    <td><span class="badge bg-label-info"><?= htmlspecialchars(ucfirst($row['project_type'])) ?></span></td>
                                    <td><?= number_format((float)$row['sold_qty']) ?></td>
                                    <td class="text-end">₹<?= admin_price($row['revenue']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary">No project sales available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Category Wise Product Report</h5>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Products</th>
                            <th>Sold Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($category_sales_q && mysqli_num_rows($category_sales_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($category_sales_q)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td><?= number_format((float)$row['product_count']) ?></td>
                                    <td><?= number_format((float)$row['sold_qty']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary">No category data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Low Stock Products</h5>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($low_stock_q && mysqli_num_rows($low_stock_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($low_stock_q)): ?>
                                <tr>
                                    <td>
                                        <div class="text-truncate-180"><?= htmlspecialchars($row['title']) ?></div>
                                    </td>
                                    <td><span class="badge bg-label-danger"><?= number_format((float)$row['stock']) ?></span></td>
                                    <td class="text-end">₹<?= admin_price($row['price']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary">No low stock products.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Recent Frontend Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table report-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_orders_q && mysqli_num_rows($recent_orders_q) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($recent_orders_q)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['order_no'] ?: ('#' . $row['id'])) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['customer_name'] ?? 'Guest') ?></strong><br>
                                        <small class="text-body-secondary"><?= htmlspecialchars($row['customer_email'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge bg-label-secondary"><?= htmlspecialchars($row['order_status']) ?></span></td>
                                    <td><span class="badge bg-label-primary"><?= htmlspecialchars($row['payment_status']) ?></span></td>
                                    <td class="text-end">₹<?= admin_price($row['total_amount']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-body-secondary">No orders available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12 mb-6">
        <div class="card h-100 frontend-report-card">
            <div class="card-header">
                <h5 class="mb-0">Recent Contact Enquiries</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_contacts_q && mysqli_num_rows($recent_contacts_q) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_contacts_q)): ?>
                        <div class="mb-4 pb-3 border-bottom">
                            <strong><?= htmlspecialchars($row['name'] ?? 'Visitor') ?></strong><br>
                            <small class="text-body-secondary"><?= htmlspecialchars($row['email'] ?? '') ?></small>
                            <p class="mb-0 mt-1"><?= htmlspecialchars($row['subject'] ?? '-') ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-body-secondary mb-0">No contact enquiries.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php" ?>