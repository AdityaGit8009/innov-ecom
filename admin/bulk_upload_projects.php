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
    $fileName = "project_bulk_upload_template.csv";

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, [
        "Sr No",
        "Title",
        "Type (mini/major)",
        "Description",
        "Image Name",
        "Base Price",
        "Tax % (GST)",
        "Discount %",
        "Status (1/0)"
    ]);

    fputcsv($output, [
        "1",
        "Smart Home Automation",
        "major",
        "A full featured IoT smart home.",
        "smart_home.jpg",
        "5000",
        "18",
        "10",
        "1"
    ]);

    fclose($output);
    exit;
}

/* ===============================
   BULK CSV UPLOAD PROJECTS
================================ */
if (isset($_POST['upload_csv'])) {

    if (empty($_FILES['csv_file']['name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: bulk_upload_projects.php?msg=error");
        exit;
    }

    $fileTmp = $_FILES['csv_file']['tmp_name'];
    $fileExt = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

    if ($fileExt !== 'csv') {
        header("Location: bulk_upload_projects.php?msg=invalid");
        exit;
    }

    $successCount = 0;
    $failedCount = 0;

    if (($handle = fopen($fileTmp, "r")) !== false) {

        fgetcsv($handle, 10000, ","); // Skip Header

        while (($data = fgetcsv($handle, 10000, ",")) !== false) {

            if (count($data) < 9) {
                $failedCount++;
                continue;
            }

            $title           = clean($conn, $data[1] ?? '');
            $project_type    = clean($conn, $data[2] ?? 'mini');
            $description     = clean($conn, $data[3] ?? '');
            $image           = clean($conn, $data[4] ?? '');
            
            $base_price      = (float)($data[5] ?? 0);
            $tax_percentage  = (float)($data[6] ?? 0);
            $discount_percent= (float)($data[7] ?? 0);
            $status          = (int)($data[8] ?? 1);

            $status = $status == 1 ? 1 : 0;
            if (!in_array($project_type, ['mini', 'major'])) $project_type = 'mini';

            if ($title == '') {
                $failedCount++;
                continue;
            }

            // Calculate exact pricing based on same logic as projects.php
            $default_tax_percentage = 2.00; // Platform tax logic
            $service_tax_amount = ($base_price * $default_tax_percentage) / 100;
            $taxable_amount = $base_price + $service_tax_amount;
            $gst_amount = ($taxable_amount * $tax_percentage) / 100;

            $old_price = $taxable_amount + $gst_amount;
            $discount_amount = ($old_price * $discount_percent) / 100;

            $price = $old_price - $discount_amount;

            $old_price = round($old_price, 2);
            $price = round($price, 2);

            $q = mysqli_query($conn, "
                INSERT INTO projects
                (
                    title, project_type, description, image, 
                    base_price, default_tax_percentage, tax_percentage, discount, old_price, price, status
                ) VALUES (
                    '$title', '$project_type', '$description', '$image',
                    '$base_price', '$default_tax_percentage', '$tax_percentage', '$discount_percent', '$old_price', '$price', $status
                )
            ");

            if ($q) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        fclose($handle);
        header("Location: bulk_upload_projects.php?msg=ok&success=$successCount&failed=$failedCount");
        exit;
    }

    header("Location: bulk_upload_projects.php?msg=error");
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
        <h4><i class="bx bx-upload"></i> Bulk Project Upload</h4>
        <small>Upload projects using CSV file.</small>
    </div>

    <a href="bulk_upload_projects.php?download_template=1" class="btn-template">
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
                        <i class="bx bx-upload"></i> Upload Projects
                    </button>

                    <a href="bulk_upload_projects.php?download_template=1" class="btn-template">
                        <i class="bx bx-download"></i> Download Template
                    </a>
                </div>
            </form>

            <div class="csv-note">
                <strong>CSV column order:</strong><br>
                <code>Sr No, Title, Type (mini/major), Description, Image Name, Base Price, Tax % (GST), Discount %, Status (1/0)</code>
                <br><br>
                <strong>Note:</strong> Upload project images manually to the <code>uploads/projects/</code> folder, mapping names (e.g. <code>project1.jpg</code>) to the "Image Name" column. Final selling prices are calculated automatically based on Tax and Discount.
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
            showToast('success', 'Projects uploaded: <?= $success ?>, Failed: <?= $failed ?>');
        <?php } elseif ($_GET['msg'] == 'invalid') { ?>
            showToast('warning', 'Only CSV file is allowed!');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'CSV upload failed!');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>
