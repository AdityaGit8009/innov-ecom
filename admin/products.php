<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===== UPLOAD IMAGE ===== */
function uploadImage($field, $old = "")
{
    if (!empty($_FILES[$field]['name'])) {
        $folder = "../uploads/products/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            return $old;
        }

        $name = time() . '_' . $field . '_' . rand(1000, 9999) . '.' . $ext;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $folder . $name)) {
            return $name;
        }
    }

    return $old;
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_product'])) {

    $id = (int)($_POST['product_id'] ?? 0);

    $title  = clean($conn, $_POST['title'] ?? '');
    $store  = clean($conn, $_POST['store'] ?? '');
    $seller = clean($conn, $_POST['seller'] ?? '');

    // Admin enters actual/base product price.
    // Formula: base price + 2% service tax, then GST on that taxable amount.
    // Example: 400 + 2% = 408; GST 18% on 408 = 73.44; final price = ceil(481.44) = 482.
    // Admin enters actual/base product price.
    // Formula:
    // 1. Add 2% default service tax
    // 2. Apply GST on taxable amount
    // 3. Apply discount in percentage
    // 4. Final price after discount

    $base_price = (float)($_POST['price'] ?? 0);

    $default_tax_percentage = 2.00;

    // GST entered by admin
    $tax_percentage = (float)($_POST['tax_percentage'] ?? 0);

    // Discount entered in percentage
    $discount_percentage = (float)($_POST['discount'] ?? 0);

    // Step 1 → 2% service tax
    $service_tax_amount = ($base_price * $default_tax_percentage) / 100;

    // Step 2 → taxable amount
    $taxable_amount = $base_price + $service_tax_amount;

    // Step 3 → GST amount
    $gst_amount = ($taxable_amount * $tax_percentage) / 100;

    // Original product price before discount
    $old_price = $taxable_amount + $gst_amount;

    // Step 4 → discount amount from percentage
    $discount_amount = ($old_price * $discount_percentage) / 100;

    // Final selling price after discount
    $price = $old_price - $discount_amount;

    // Round to 2 decimal places (best practice for money)
    $old_price = round($old_price, 2);
    $price = round($price, 2);

    // Delivery charge
    $delivery_charge = (float)($_POST['delivery_charge'] ?? 0);

    $rating        = (float)($_POST['rating'] ?? 0);
    $ratings_count = (int)($_POST['ratings_count'] ?? 0);
    $bought        = clean($conn, $_POST['bought'] ?? '');
    $stock         = (int)($_POST['stock'] ?? 0);

    $category_id     = (int)($_POST['category_id'] ?? 0);
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);

    $main_image = uploadImage('main_image', $_POST['old_main_image'] ?? '');
    $image1     = uploadImage('image1', $_POST['old_image1'] ?? '');
    $image2     = uploadImage('image2', $_POST['old_image2'] ?? '');
    $image3     = uploadImage('image3', $_POST['old_image3'] ?? '');
    $image4     = uploadImage('image4', $_POST['old_image4'] ?? '');

    if ($id == 0) {
        $sql = "INSERT INTO products 
            (title, store, seller, base_price, default_tax_percentage, price, old_price, discount, tax_percentage, delivery_charge, rating, ratings_count, bought, stock, main_image, image1, image2, image3, image4, category_id, sub_category_id, status) 
            VALUES
            ('$title', '$store', '$seller', '$base_price', '$default_tax_percentage', '$price', '$old_price', '$discount_percentage', '$tax_percentage', '$delivery_charge', '$rating', '$ratings_count', '$bought', '$stock', '$main_image', '$image1', '$image2', '$image3', '$image4', '$category_id', '$sub_category_id', 1)";

        $res = mysqli_query($conn, $sql);
        if ($res) {
            $newProductId = (int)mysqli_insert_id($conn);
            sip_log_admin_activity($conn, 'CREATE', 'PRODUCTS', 'Product created: ' . $title, 'product', $newProductId, [
                'title' => $title,
                'price' => $price,
                'stock' => $stock,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id
            ]);
        }
    } else {
        $oldProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, price, stock, status FROM products WHERE id=$id LIMIT 1"));

        $sql = "UPDATE products SET 
            title='$title',
            store='$store',
            seller='$seller',
            base_price='$base_price',
            default_tax_percentage='$default_tax_percentage',
            price='$price',
            old_price='$old_price',
            discount='$discount_percentage',
            tax_percentage='$tax_percentage',
            delivery_charge='$delivery_charge',
            rating='$rating',
            ratings_count='$ratings_count',
            bought='$bought',
            stock='$stock',
            main_image='$main_image',
            image1='$image1',
            image2='$image2',
            image3='$image3',
            image4='$image4',
            category_id='$category_id',
            sub_category_id='$sub_category_id'
            WHERE id=$id";

        $res = mysqli_query($conn, $sql);
        if ($res) {
            sip_log_admin_activity($conn, 'UPDATE', 'PRODUCTS', 'Product updated: ' . $title, 'product', $id, [
                'old_title' => $oldProduct['title'] ?? '',
                'new_title' => $title,
                'old_price' => $oldProduct['price'] ?? '',
                'new_price' => $price,
                'old_stock' => $oldProduct['stock'] ?? '',
                'new_stock' => $stock
            ]);
        }
    }

    header("Location: products.php?msg=" . ($res ? 'ok' : 'error'));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $oldProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, price, stock FROM products WHERE id=$id LIMIT 1"));
    $del = mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    if ($del) {
        sip_log_admin_activity($conn, 'DELETE', 'PRODUCTS', 'Product deleted: ' . ($oldProduct['title'] ?? ('ID ' . $id)), 'product', $id, [
            'title' => $oldProduct['title'] ?? '',
            'price' => $oldProduct['price'] ?? '',
            'stock' => $oldProduct['stock'] ?? ''
        ]);
    }

    header("Location: products.php?msg=" . ($del ? 'deleted' : 'error'));
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    $oldProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, status FROM products WHERE id=$id LIMIT 1"));
    $statusUpdate = mysqli_query($conn, "UPDATE products SET status=$status WHERE id=$id");
    if ($statusUpdate) {
        sip_log_admin_activity($conn, 'STATUS_CHANGE', 'PRODUCTS', 'Product status changed: ' . ($oldProduct['title'] ?? ('ID ' . $id)), 'product', $id, [
            'old_status' => $oldProduct['status'] ?? '',
            'new_status' => $status
        ]);
    }

    header("Location: products.php?msg=status");
    exit;
}

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "
    SELECT 
        p.*, 
        c.name AS category_name, 
        s.name AS subcategory_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN sub_categories s ON p.sub_category_id = s.id
    ORDER BY p.id DESC
");

/* ===== FETCH CATEGORIES ===== */
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

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

    .page-title-box h5 {
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

    .btn-add-product {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-product:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #productTable {
        width: 100% !important;
        font-size: 14px;
    }

    #productTable thead th {
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

    #productTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #productTable tbody tr:hover {
        background: #f8fafc;
    }

    .product-img {
        width: 52px;
        height: 52px;
        object-fit: contain;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: 4px;
    }

    .empty-img {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 18px;
        border: 1px solid #e5e7eb;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .form-control,
    .form-select {
        border-radius: 9px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .modal-content {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    #modalTitle {
        font-weight: 800;
        color: #334155;
    }

    .modal-body {
        padding: 22px;
    }

    .modal-footer {
        border-top: 1px solid #e5e7eb;
        padding: 16px 22px;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .btn-add-product {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <h5><i class="bx bx-package"></i> Products</h5>

    <button type="button" class="btn btn-add-product" onclick="openAddModal()">
        <i class="bx bx-plus"></i> Add Product
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="productTable"
                data-module="products"
                data-disable-columns="1,2,3,4,5" data-action-dropdown="1">

                <thead>
                    <tr class="text-center">
                        <th>Sr No.</th>
                        <th>Main Image</th>
                        <th>Image 1</th>
                        <th>Image 2</th>
                        <th>Image 3</th>
                        <th>Image 4</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Store</th>
                        <th>Seller</th>
                        <th>Actual Price</th>
                        <th>Old Price</th>
                        <th>Discount %</th>
                        <th>GST</th>
                        <th>Delivery</th>
                        <th>Final Price</th>
                        <th>Rating</th>
                        <th>Ratings Count</th>
                        <th>Bought</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($products)) {
                        $final_price = (float)$row['price'];
                        $editJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td class="text-center">
                                <?php if (!empty($row['main_image'])) { ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['main_image']) ?>" class="product-img" alt="Product">
                                <?php } else { ?>
                                    <span class="empty-img"><i class="bx bx-image"></i></span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <?php if (!empty($row['image1'])) { ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['image1']) ?>" class="product-img" alt="Product">
                                <?php } else { ?>
                                    <span class="empty-img"><i class="bx bx-image"></i></span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <?php if (!empty($row['image2'])) { ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['image2']) ?>" class="product-img" alt="Product">
                                <?php } else { ?>
                                    <span class="empty-img"><i class="bx bx-image"></i></span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <?php if (!empty($row['image3'])) { ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['image3']) ?>" class="product-img" alt="Product">
                                <?php } else { ?>
                                    <span class="empty-img"><i class="bx bx-image"></i></span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <?php if (!empty($row['image4'])) { ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['image4']) ?>" class="product-img" alt="Product">
                                <?php } else { ?>
                                    <span class="empty-img"><i class="bx bx-image"></i></span>
                                <?php } ?>
                            </td>

                            <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['subcategory_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['store'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['seller'] ?? '') ?></td>

                            <td>₹<?= format_price((float)($row['base_price'] ?? $row['price'])) ?></td>
                            <td>₹<?= format_price((float)$row['old_price']) ?></td>
                            <td><?= format_price((float)$row['discount']) ?>%</td>
                            <td>2% + <?= format_price((float)$row['tax_percentage']) ?>%</td>
                            <td>₹<?= format_price((float)($row['delivery_charge'] ?? 0)) ?></td>
                            <td>₹<?= format_price($final_price) ?></td>

                            <td><?= htmlspecialchars($row['rating'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['ratings_count'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['bought'] ?? '') ?></td>

                            <td class="text-center">
                                <?php if ((int)$row['stock'] <= 5) { ?>
                                    <span class="badge-soft-danger">Low</span>
                                <?php } else { ?>
                                    <span class="badge-soft-success"><?= (int)$row['stock'] ?></span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$row['id'] ?>&status=<?= !empty($row['status']) ? 0 : 1 ?>"
                                    class="<?= !empty($row['status']) ? 'badge-soft-success' : 'badge-soft-secondary' ?>">
                                    <?= !empty($row['status']) ? 'Active' : 'Inactive' ?>
                                </a>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    onclick='openEditModal(<?= $editJson ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <a href="?id=<?= (int)$row['id'] ?>&status=<?= !empty($row['status']) ? 0 : 1 ?>"
                                    class="btn btn-info btn-sm">
                                    <i class="bx <?= !empty($row['status']) ? 'bx-hide' : 'bx-show' ?>"></i>
                                    <?= !empty($row['status']) ? 'Inactive' : 'Active' ?>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteProduct(<?= (int)$row['id'] ?>)">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- PRODUCT MODAL -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form method="post" enctype="multipart/form-data" class="modal-content" id="productForm">

            <div class="modal-header">
                <h5 id="modalTitle">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">

                <input type="hidden" name="product_id" id="product_id">
                <input type="hidden" name="old_main_image" id="old_image0">

                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <input type="hidden" name="old_image<?= $i ?>" id="old_image<?= $i ?>">
                <?php endfor; ?>

                <div class="col-md-6">
                    <label class="form-label">Product Title</label>
                    <input class="form-control" name="title" id="title" placeholder="Product Title" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Store</label>
                    <input class="form-control" name="store" id="store" placeholder="Store">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Seller</label>
                    <input class="form-control" name="seller" id="seller" placeholder="Seller">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Actual Product Price</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="Actual Product Price" oninput="autoPriceCalc()">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Old Price</label>
                    <input type="number" step="0.01" class="form-control" name="old_price" id="old_price" placeholder="Auto calculated" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Discount %</label>
                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" placeholder="Discount %" oninput="autoPriceCalc()">
                </div>

                <div class="col-md-3">
                    <label class="form-label">GST % <small class="text-muted">(+2% default tax)</small></label>
                    <input type="number" step="0.01" class="form-control" name="tax_percentage" id="tax_percentage" placeholder="Tax %" oninput="autoPriceCalc()">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Delivery Charge</label>
                    <input type="number" step="0.01" class="form-control" name="delivery_charge" id="delivery_charge" placeholder="Delivery Charge">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stock</label>
                    <input type="number" class="form-control" name="stock" id="stock" placeholder="Stock">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Main Price incl. 2% + GST</label>
                    <input class="form-control" id="final_price_preview" placeholder="Main Price" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <input type="number" step="0.1" class="form-control" name="rating" id="rating" placeholder="Rating">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Ratings Count</label>
                    <input type="number" class="form-control" name="ratings_count" id="ratings_count" placeholder="Ratings Count">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Bought Text</label>
                    <input class="form-control" name="bought" id="bought" placeholder="Example: 100+ bought">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" id="category_id" onchange="loadSubCategories()">
                        <option value="">Select Category</option>
                        <?php
                        mysqli_data_seek($categories, 0);
                        while ($c = mysqli_fetch_assoc($categories)) {
                        ?>
                            <option value="<?= (int)$c['id'] ?>">
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Subcategory</label>
                    <select class="form-select" name="sub_category_id" id="sub_category_id">
                        <option value="">Select Subcategory</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Main Image</label>
                    <input type="file" class="form-control" name="main_image">
                </div>

                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="col-md-4">
                        <label class="form-label">Image <?= $i ?></label>
                        <input type="file" class="form-control" name="image<?= $i ?>">
                    </div>
                <?php endfor; ?>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="save_product">
                    <i class="bx bx-save"></i> Save Product
                </button>
            </div>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openAddModal() {
        document.getElementById("productForm").reset();

        document.getElementById("product_id").value = "";
        document.getElementById("old_image0").value = "";
        document.getElementById("old_image1").value = "";
        document.getElementById("old_image2").value = "";
        document.getElementById("old_image3").value = "";
        document.getElementById("old_image4").value = "";

        document.getElementById("sub_category_id").innerHTML = '<option value="">Select Subcategory</option>';
        document.getElementById("modalTitle").innerText = "Add Product";

        new bootstrap.Modal(document.getElementById('productModal')).show();
    }

    // ================= AUTO PRICE CALCULATION =================

    function autoPriceCalc() {
        const base = parseFloat(document.getElementById('price').value || 0);
        const gst = parseFloat(document.getElementById('tax_percentage').value || 0);
        const discountPercent = parseFloat(document.getElementById('discount').value || 0);
        const defaultTax = 2;

        // 2% service tax
        const serviceTaxAmount = (base * defaultTax) / 100;

        // taxable amount
        const taxableAmount = base + serviceTaxAmount;

        // GST amount
        const gstAmount = (taxableAmount * gst) / 100;

        // Original price before discount
        let originalPrice = taxableAmount + gstAmount;

        // Discount amount
        const discountAmount = (originalPrice * discountPercent) / 100;

        // Final price
        let finalPrice = originalPrice - discountAmount;

        // Round to 2 decimals
        originalPrice = parseFloat(originalPrice.toFixed(2));
        finalPrice = parseFloat(finalPrice.toFixed(2));

        // Show values
        document.getElementById('old_price').value = originalPrice;
        document.getElementById('final_price_preview').value = finalPrice;
    }

    function openEditModal(p) {
        document.getElementById("modalTitle").innerText = "Edit Product";

        Object.keys(p).forEach(function(key) {
            const input = document.getElementById(key);
            if (input) {
                input.value = p[key] ?? '';
            }
        });

        if (p.base_price) {
            document.getElementById('price').value = p.base_price;
        }
        document.getElementById('product_id').value = p.id || '';
        document.getElementById('old_image0').value = p.main_image || '';
        document.getElementById('old_image1').value = p.image1 || '';
        document.getElementById('old_image2').value = p.image2 || '';
        document.getElementById('old_image3').value = p.image3 || '';
        document.getElementById('old_image4').value = p.image4 || '';

        document.getElementById('category_id').value = p.category_id || '';

        loadSubCategories(p.sub_category_id || '');
        autoPriceCalc();

        new bootstrap.Modal(document.getElementById('productModal')).show();
    }

    function loadSubCategories(selectedSubId = '') {
        let catId = document.getElementById('category_id').value;
        let subcatSelect = document.getElementById('sub_category_id');

        subcatSelect.innerHTML = '<option value="">Loading...</option>';

        if (!catId) {
            subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
            return;
        }

        fetch('get_subcategories.php?category_id=' + catId)
            .then(res => res.json())
            .then(data => {
                subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';

                data.forEach(sc => {
                    let selected = selectedSubId == sc.id ? 'selected' : '';
                    subcatSelect.innerHTML += `<option value="${sc.id}" ${selected}>${sc.name}</option>`;
                });
            })
            .catch(() => {
                subcatSelect.innerHTML = '<option value="">No subcategory found</option>';
            });
    }

    function deleteProduct(id) {
        Swal.fire({
            title: 'Delete product?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'products.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Product saved successfully!');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Product deleted successfully!');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Product status updated!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>