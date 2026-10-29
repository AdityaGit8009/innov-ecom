<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE HELPER ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim($value ?? ''));
}

/* ===== IMAGE UPLOAD ===== */
function uploadProjectImage($field, $old = "")
{
    if (!empty($_FILES[$field]['name'])) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $fileName = $_FILES[$field]['name'];
        $fileTmp  = $_FILES[$field]['tmp_name'];
        $fileSize = $_FILES[$field]['size'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            return $old;
        }

        if ($fileSize > 2 * 1024 * 1024) {
            return $old;
        }

        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $fileName);
        $uploadDir = "../uploads/projects/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($fileTmp, $uploadDir . $safeName)) {
            return $safeName;
        }
    }

    return $old;
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_project'])) {

    $id           = (int)($_POST['project_id'] ?? 0);
    $title        = clean($conn, $_POST['title'] ?? '');
    $project_type = clean($conn, $_POST['project_type'] ?? 'mini');
    $desc         = clean($conn, $_POST['description'] ?? '');
    $old_image    = clean($conn, $_POST['old_image'] ?? '');

    if (!in_array($project_type, ['mini', 'major'])) {
        $project_type = 'mini';
    }

    if ($title == '') {
        header("Location: projects.php?msg=title_required");
        exit;
    }

    $image = uploadProjectImage('image', $old_image);

    $base_price = (float)($_POST['price'] ?? 0);
    $default_tax_percentage = 2.00;
    $tax_percentage = (float)($_POST['tax_percentage'] ?? 0);
    $discount_percentage = (float)($_POST['discount'] ?? 0);

    $service_tax_amount = ($base_price * $default_tax_percentage) / 100;
    $taxable_amount = $base_price + $service_tax_amount;
    $gst_amount = ($taxable_amount * $tax_percentage) / 100;

    $old_price = $taxable_amount + $gst_amount;
    $discount_amount = ($old_price * $discount_percentage) / 100;

    $price = $old_price - $discount_amount;

    $old_price = round($old_price, 2);
    $price = round($price, 2);

    if ($id == 0) {
        $res = mysqli_query(
            $conn,
            "INSERT INTO projects (title, project_type, description, image, base_price, default_tax_percentage, tax_percentage, discount, old_price, price, status)
             VALUES ('$title', '$project_type', '$desc', '$image', '$base_price', '$default_tax_percentage', '$tax_percentage', '$discount_percentage', '$old_price', '$price', 1)"
        );
    } else {
        $res = mysqli_query(
            $conn,
            "UPDATE projects SET
                title='$title',
                project_type='$project_type',
                description='$desc',
                image='$image',
                base_price='$base_price',
                default_tax_percentage='$default_tax_percentage',
                tax_percentage='$tax_percentage',
                discount='$discount_percentage',
                old_price='$old_price',
                price='$price'
             WHERE id=$id"
        );
    }

    header("Location: projects.php?msg=" . ($res ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    $old = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT image FROM projects WHERE id=$deleteId LIMIT 1")
    );

    if (!empty($old['image']) && file_exists("../uploads/projects/" . $old['image'])) {
        @unlink("../uploads/projects/" . $old['image']);
    }

    $res = mysqli_query($conn, "DELETE FROM projects WHERE id=$deleteId");

    header("Location: projects.php?msg=" . ($res ? "deleted" : "error"));
    exit;
}

/* ===== STATUS ===== */
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];
    $status = $status == 1 ? 1 : 0;

    $res = mysqli_query($conn, "UPDATE projects SET status=$status WHERE id=$id");

    header("Location: projects.php?msg=" . ($res ? "status" : "error"));
    exit;
}

/* ===== FETCH ===== */
$projects = mysqli_query($conn, "SELECT * FROM projects ORDER BY id DESC");

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

    .btn-add-project {
        background: #6366f1;
        border: 0;
        color: #ffffff;
        border-radius: 9px;
        padding: 10px 18px;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(99, 102, 241, 0.25);
    }

    .btn-add-project:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    #projectTable {
        width: 100% !important;
        font-size: 14px;
    }

    #projectTable thead th {
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

    #projectTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #projectTable tbody tr:hover {
        background: #f8fafc;
    }

    .project-img {
        width: 70px;
        height: 55px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
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

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }

    .badge-soft-primary {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-soft-dark {
        background: #e5e7eb;
        color: #111827;
        padding: 6px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
    }

    .project-desc {
        max-width: 330px;
        white-space: normal;
        line-height: 1.45;
    }

    .form-control,
    .form-select {
        border-radius: 9px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
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

        .btn-add-project {
            width: 100%;
        }
    }
</style>

<div class="page-title-box">
    <div>
        <h4><i class="bx bx-layer"></i> Projects</h4>
        <small>Manage mini and major projects from here.</small>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="bulk_upload_projects.php" class="btn btn-primary" style="border-radius: 9px; box-shadow: 0 8px 18px rgba(13, 110, 253, 0.25); font-weight: 700; padding: 10px 18px;">
            <i class="bx bx-upload"></i> Bulk Upload
        </a>
        <button type="button" class="btn btn-add-project" onclick="openAddModal()">
            <i class="bx bx-plus"></i> Add Project
        </button>
    </div>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="projectTable"
                data-module="projects"
                data-disable-columns="1" data-action-dropdown="1">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Final Price</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($p = mysqli_fetch_assoc($projects)) {
                        $imgPath = "../uploads/projects/" . ($p['image'] ?? '');
                        $projectImage = (!empty($p['image']) && file_exists($imgPath))
                            ? $imgPath
                            : "../assets/images/default-project.webp";

                        $type = $p['project_type'] ?? 'mini';
                        $projectJson = htmlspecialchars(json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

                        $shortDesc = strlen($p['description'] ?? '') > 80
                            ? substr($p['description'], 0, 80) . '...'
                            : ($p['description'] ?? '');
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++; ?></td>

                            <td class="text-center">
                                <img src="<?= htmlspecialchars($projectImage); ?>"
                                    class="project-img"
                                    alt="Project">
                            </td>

                            <td class="text-center">
                                <?php if ($type == 'major') { ?>
                                    <span class="badge-soft-dark">Major Project</span>
                                <?php } else { ?>
                                    <span class="badge-soft-primary">Mini Project</span>
                                <?php } ?>
                            </td>

                            <td><?= htmlspecialchars($p['title'] ?? ''); ?></td>

                            <td>₹<?= format_price((float)($p['price'] ?? 0)); ?></td>

                            <td>
                                <div class="project-desc">
                                    <?= htmlspecialchars($shortDesc); ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <a href="?id=<?= (int)$p['id']; ?>&status=<?= !empty($p['status']) ? 0 : 1; ?>"
                                    class="<?= !empty($p['status']) ? 'badge-soft-success' : 'badge-soft-secondary'; ?>">
                                    <?= !empty($p['status']) ? 'Active' : 'Inactive'; ?>
                                </a>
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    onclick='openEditModal(<?= $projectJson; ?>)'>
                                    <i class="bx bx-edit"></i> Edit
                                </button>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteProject(<?= (int)$p['id']; ?>)">
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

<!-- PROJECT MODAL -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" enctype="multipart/form-data" class="modal-content" id="projectForm">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title">Add Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <input type="hidden" name="project_id" id="project_id">
                <input type="hidden" name="old_image" id="old_image">

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Project Title</label>
                    <input class="form-control"
                        name="title"
                        id="title"
                        placeholder="Enter project title"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Project Type</label>
                    <select class="form-select" name="project_type" id="project_type" required>
                        <option value="mini">Mini Project</option>
                        <option value="major">Major Project</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Base Price</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="Base Price" oninput="autoPriceCalc()">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Discount %</label>
                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" placeholder="Discount %" oninput="autoPriceCalc()">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">GST %</label>
                    <input type="number" step="0.01" class="form-control" name="tax_percentage" id="tax_percentage" placeholder="Tax %" oninput="autoPriceCalc()">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Final Price</label>
                    <input type="text" class="form-control" id="final_price_preview" placeholder="Final Price" readonly>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Project Description</label>
                    <textarea class="form-control"
                        name="description"
                        id="description"
                        rows="5"
                        placeholder="Enter project description"></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Project Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="text-muted">Allowed: JPG, JPEG, PNG, WEBP. Max size: 2MB.</small>
                </div>

                <div class="col-md-12" id="previewBox" style="display:none;">
                    <label class="form-label fw-semibold">Current Image</label><br>
                    <img id="previewImage"
                        src=""
                        width="110"
                        height="80"
                        style="object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button type="submit" class="btn btn-success" name="save_project">
                    <i class="bx bx-save"></i> Save Project
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openAddModal() {
        const form = document.getElementById("projectForm");
        form.reset();

        document.getElementById("project_id").value = "";
        document.getElementById("old_image").value = "";
        document.getElementById("project_type").value = "mini";
        document.getElementById("modalTitle").innerText = "Add Project";

        document.getElementById("previewBox").style.display = "none";
        document.getElementById("previewImage").src = "";

        autoPriceCalc();

        new bootstrap.Modal(document.getElementById("projectModal")).show();
    }

    function autoPriceCalc() {
        const base = parseFloat(document.getElementById('price').value || 0);
        const gst = parseFloat(document.getElementById('tax_percentage').value || 0);
        const discountPercent = parseFloat(document.getElementById('discount').value || 0);
        const defaultTax = 2;
        const serviceTaxAmount = (base * defaultTax) / 100;
        const taxableAmount = base + serviceTaxAmount;
        const gstAmount = (taxableAmount * gst) / 100;
        let originalPrice = taxableAmount + gstAmount;
        const discountAmount = (originalPrice * discountPercent) / 100;
        let finalPrice = originalPrice - discountAmount;
        document.getElementById('final_price_preview').value = finalPrice.toFixed(2);
    }

    function openEditModal(p) {
        document.getElementById("modalTitle").innerText = "Edit Project";

        document.getElementById("project_id").value = p.id || "";
        document.getElementById("title").value = p.title || "";
        document.getElementById("description").value = p.description || "";
        document.getElementById("old_image").value = p.image || "";
        document.getElementById("project_type").value = p.project_type || "mini";
        
        document.getElementById("price").value = p.base_price || "";
        document.getElementById("discount").value = p.discount || "";
        document.getElementById("tax_percentage").value = p.tax_percentage || "";
        autoPriceCalc();

        if (p.image) {
            document.getElementById("previewBox").style.display = "block";
            document.getElementById("previewImage").src = "../uploads/projects/" + p.image;
        } else {
            document.getElementById("previewBox").style.display = "none";
            document.getElementById("previewImage").src = "";
        }

        new bootstrap.Modal(document.getElementById("projectModal")).show();
    }

    function deleteProject(id) {
        Swal.fire({
            title: 'Delete project?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'projects.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            showToast('success', 'Project saved successfully.');
        <?php } elseif ($_GET['msg'] == 'deleted') { ?>
            showToast('success', 'Project deleted successfully.');
        <?php } elseif ($_GET['msg'] == 'status') { ?>
            showToast('success', 'Project status updated successfully.');
        <?php } elseif ($_GET['msg'] == 'title_required') { ?>
            showToast('warning', 'Project title is required.');
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            showToast('error', 'Something went wrong. Please try again.');
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>