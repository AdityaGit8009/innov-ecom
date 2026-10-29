<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

/* ===============================
   DOWNLOAD CSV TEMPLATE
================================ */
if (isset($_GET['download_template'])) {
    $fileName = "product_bulk_upload_template.csv";

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");

    /* UTF-8 BOM for Excel support */
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, [
        "Sr No",
        "Main Image",
        "Image 1",
        "Image 2",
        "Image 3",
        "Image 4",
        "Title",
        "Category ID",
        "Sub Category ID",
        "Store",
        "Seller",
        "Price",
        "Old Price",
        "Discount",
        "Rating",
        "Ratings Count",
        "Bought",
        "Stock",
        "Status"
    ]);

    fputcsv($output, [
        "1",
        "main-product.jpg",
        "product-1.jpg",
        "product-2.jpg",
        "product-3.jpg",
        "product-4.jpg",
        "Arduino Mega",
        "1",
        "2",
        "Softpro Store",
        "Softpro",
        "999",
        "1299",
        "10",
        "4.5",
        "120",
        "100+ bought",
        "50",
        "1"
    ]);

    fclose($output);
    exit;
}

/* ===============================
   BULK CSV UPLOAD PRODUCTS
================================ */
if (isset($_POST['upload_csv'])) {

    if (empty($_FILES['csv_file']['name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: bulk_upload.php?msg=error");
        exit;
    }

    $fileTmp = $_FILES['csv_file']['tmp_name'];
    $fileExt = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

    if ($fileExt !== 'csv') {
        header("Location: bulk_upload.php?msg=invalid");
        exit;
    }

    $successCount = 0;
    $failedCount = 0;

    if (($handle = fopen($fileTmp, "r")) !== false) {

        fgetcsv($handle, 10000, ",");

        while (($data = fgetcsv($handle, 10000, ",")) !== false) {

            if (count($data) < 19) {
                $failedCount++;
                continue;
            }

            $main_image      = clean($conn, $data[1] ?? '');
            $image1          = clean($conn, $data[2] ?? '');
            $image2          = clean($conn, $data[3] ?? '');
            $image3          = clean($conn, $data[4] ?? '');
            $image4          = clean($conn, $data[5] ?? '');

            $title           = clean($conn, $data[6] ?? '');
            $category_id     = (int)($data[7] ?? 0);
            $sub_category_id = (int)($data[8] ?? 0);

            $store           = clean($conn, $data[9] ?? '');
            $seller          = clean($conn, $data[10] ?? '');

            $price           = (float)($data[11] ?? 0);
            $old_price       = (float)($data[12] ?? 0);
            $discount        = (float)($data[13] ?? 0);
            $rating          = (float)($data[14] ?? 0);
            $ratings_count   = (int)($data[15] ?? 0);
            $bought          = clean($conn, $data[16] ?? '');
            $stock           = (int)($data[17] ?? 0);
            $status          = (int)($data[18] ?? 1);

            $status = $status == 1 ? 1 : 0;

            if ($title == '') {
                $failedCount++;
                continue;
            }

            $q = mysqli_query($conn, "
                INSERT INTO products
                (
                    main_image, image1, image2, image3, image4,
                    title, category_id, sub_category_id,
                    store, seller,
                    price, old_price, discount,
                    rating, ratings_count, bought,
                    stock, status
                ) VALUES (
                    '$main_image', '$image1', '$image2', '$image3', '$image4',
                    '$title', $category_id, $sub_category_id,
                    '$store', '$seller',
                    '$price', '$old_price', '$discount',
                    '$rating', $ratings_count, '$bought',
                    $stock, $status
                )
            ");

            if ($q) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        fclose($handle);

        header("Location: bulk_upload.php?msg=ok&success=$successCount&failed=$failedCount");
        exit;
    }

    header("Location: bulk_upload.php?msg=error");
    exit;
}

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

    .page-title-box small {
        color: #64748b;
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

    .upload-box {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 22px;
        background: #f8fafc;
    }

    .upload-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #eef2ff;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 12px;
    }

    .form-control {
        border-radius: 9px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
    }

    .btn-upload {
        background: #16a34a;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(22, 163, 74, 0.22);
    }

    .btn-upload:hover {
        background: #15803d;
        color: #ffffff;
    }

    .btn-template {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.22);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-template:hover {
        background: #4f46e5;
        color: #ffffff;
        text-decoration: none;
    }

    .csv-note {
        margin-top: 14px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .csv-note code {
        background: #e2e8f0;
        padding: 2px 6px;
        border-radius: 6px;
        color: #334155;
    }

    .upload-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .btn-upload,
        .btn-template {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="page-title-box">
    <div>
        <h4><i class="bx bx-upload"></i> Bulk Product Upload</h4>
        <small>Upload products using CSV file.</small>
    </div>

    <a href="bulk_upload.php?download_template=1" class="btn-template">
        <i class="bx bx-download"></i> Download CSV Template
    </a>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="upload-box">
            <div class="upload-icon">
                <i class="bx bx-cloud-upload"></i>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload CSV File</label>
                    <input type="file"
                        name="csv_file"
                        class="form-control"
                        accept=".csv"
                        required>
                </div>

                <div class="upload-actions">
                    <button type="submit" name="upload_csv" class="btn btn-upload">
                        <i class="bx bx-upload"></i> Upload Products
                    </button>

                    <a href="bulk_upload.php?download_template=1" class="btn-template">
                        <i class="bx bx-download"></i> Download Template
                    </a>
                </div>
            </form>

            <div class="csv-note">
                <strong>CSV column order:</strong><br>
                <code>Sr No, Main Image, Image 1, Image 2, Image 3, Image 4, Title, Category ID, Sub Category ID, Store, Seller, Price, Old Price, Discount, Rating, Ratings Count, Bought, Stock, Status</code>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') {
            $success = (int)($_GET['success'] ?? 0);
            $failed = (int)($_GET['failed'] ?? 0);
        ?>
            showToast('success', 'Products uploaded: <?= $success ?>, Failed: <?= $failed ?>');
        <?php } elseif ($_GET['msg'] == 'invalid') { ?>
            showToast('warning', 'Only CSV file is allowed!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'CSV upload failed!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>