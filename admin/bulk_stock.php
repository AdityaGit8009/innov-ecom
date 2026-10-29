<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===============================
   DOWNLOAD STOCK CSV TEMPLATE
================================ */
if (isset($_GET['download_template'])) {
    $fileName = "bulk_stock_update_template.csv";

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");

    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, [
        "Variant ID",
        "Stock"
    ]);

    fputcsv($output, [
        "1",
        "50"
    ]);

    fclose($output);
    exit;
}

/* ===============================
   BULK STOCK UPDATE
================================ */
if (isset($_POST['update_stock'])) {
    if (empty($_FILES['csv']['name']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        header("Location: bulk_stock_update.php?msg=error");
        exit;
    }

    $fileExt = strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION));

    if ($fileExt !== 'csv') {
        header("Location: bulk_stock_update.php?msg=invalid");
        exit;
    }

    $successCount = 0;
    $failedCount = 0;

    $file = fopen($_FILES['csv']['tmp_name'], "r");

    if ($file) {
        fgetcsv($file);

        while (($row = fgetcsv($file, 10000, ",")) !== false) {
            if (count($row) < 2) {
                $failedCount++;
                continue;
            }

            $variant_id = (int)($row[0] ?? 0);
            $stock = (int)($row[1] ?? 0);

            if ($variant_id <= 0 || $stock < 0) {
                $failedCount++;
                continue;
            }

            $q = mysqli_query($conn, "UPDATE variants SET stock=$stock WHERE id=$variant_id");

            if ($q && mysqli_affected_rows($conn) >= 0) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        fclose($file);

        header("Location: bulk_stock_update.php?msg=ok&success=$successCount&failed=$failedCount");
        exit;
    }

    header("Location: bulk_stock_update.php?msg=error");
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

    .btn-upload {
        background: #2563eb;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .btn-upload:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    .btn-template {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
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

    .upload-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
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
        <h4><i class="bx bx-refresh"></i> Bulk Stock Update</h4>
        <small>Update variant stock using CSV file.</small>
    </div>

    <a href="bulk_stock_update.php?download_template=1" class="btn-template">
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
                    <label class="form-label fw-semibold">Upload Stock CSV File</label>
                    <input type="file"
                        name="csv"
                        accept=".csv"
                        required
                        class="form-control">
                </div>

                <div class="upload-actions">
                    <button type="submit" class="btn btn-upload" name="update_stock">
                        <i class="bx bx-upload"></i> Update Stock
                    </button>

                    <a href="bulk_stock_update.php?download_template=1" class="btn-template">
                        <i class="bx bx-download"></i> Download Template
                    </a>
                </div>
            </form>

            <div class="csv-note">
                <strong>CSV column order:</strong><br>
                <code>Variant ID, Stock</code>
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
            showToast('success', 'Stock updated: <?= $success ?>, Failed: <?= $failed ?>');
        <?php } elseif ($_GET['msg'] == 'invalid') { ?>
            showToast('warning', 'Only CSV file is allowed!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Stock update failed!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>