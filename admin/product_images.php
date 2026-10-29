<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== SAFE VALUE ===== */
function clean($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== UPDATE PRODUCT IMAGE ===== */
if (isset($_POST['update_image'])) {
    $pid = (int)($_POST['product_id'] ?? 0);
    $image_field = clean($conn, $_POST['image_field'] ?? '');
    $old_image = clean($conn, $_POST['old_image'] ?? '');

    $allowedFields = ['main_image', 'image1', 'image2', 'image3', 'image4'];

    if ($pid <= 0 || !in_array($image_field, $allowedFields, true)) {
        header("Location: product_images.php?msg=error");
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowedExt, true)) {
            header("Location: product_images.php?msg=error");
            exit;
        }

        $uploadDir = "../uploads/products/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $safeFileName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file['name']);
        $new_name = time() . '_' . rand(1000, 9999) . '_' . $safeFileName;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $new_name)) {
            if (!empty($old_image) && file_exists($uploadDir . $old_image)) {
                @unlink($uploadDir . $old_image);
            }

            $q = mysqli_query($conn, "
                UPDATE products 
                SET $image_field = '$new_name' 
                WHERE id = '$pid'
            ");

            header("Location: product_images.php?msg=" . ($q ? "ok" : "error"));
            exit;
        }
    }

    header("Location: product_images.php?msg=error");
    exit;
}

/* ===== FETCH PRODUCTS ===== */
$products = mysqli_query($conn, "
    SELECT id, title, main_image, image1, image2, image3, image4 
    FROM products 
    ORDER BY id DESC
");

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

    #productImageTable {
        width: 100% !important;
        font-size: 14px;
    }

    #productImageTable thead th {
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

    #productImageTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #productImageTable tbody tr:hover {
        background: #f8fafc;
    }

    .product-title {
        font-weight: 800;
        color: #334155;
        max-width: 240px;
        white-space: normal;
        line-height: 1.4;
    }

    .product-img-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .product-img-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 3px;
    }

    .no-img-box {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 22px;
    }

    .btn-update-img {
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 10px;
    }

    .image-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .image-modal .modal-header {
        background: linear-gradient(135deg, #ff6a00, #ff3d00);
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    .image-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .image-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .image-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .image-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .image-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .image-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .image-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-image {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-image:hover {
        background: #e94d14;
        color: #fff;
    }

    #current_image_preview {
        width: 220px;
        height: 130px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 4px;
    }

    #current_image_empty {
        width: 220px;
        height: 130px;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #94a3b8;
        display: none;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .product-img-preview,
        .no-img-box {
            width: 65px;
            height: 65px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-image"></i> Product Images Management
    </h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="productImageTable"
                data-module="product images"
                data-disable-columns="2,3,4,5,6">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Product</th>
                        <th>Main Image</th>
                        <th>Image 1</th>
                        <th>Image 2</th>
                        <th>Image 3</th>
                        <th>Image 4</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($p = mysqli_fetch_assoc($products)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <div class="product-title">
                                    <?= htmlspecialchars($p['title'] ?? '') ?>
                                </div>
                            </td>

                            <?php
                            for ($j = 0; $j <= 4; $j++) {
                                $field = $j == 0 ? 'main_image' : 'image' . $j;
                                $img = $p[$field] ?? '';
                                $imgPath = "../uploads/products/" . $img;

                                $safeTitle = htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8');
                                $safeImg = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                                $safeField = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
                            ?>
                                <td class="text-center">
                                    <div class="product-img-box">
                                        <?php if (!empty($img) && file_exists($imgPath)) { ?>
                                            <img src="<?= htmlspecialchars($imgPath) ?>"
                                                class="product-img-preview"
                                                alt="Product Image">
                                        <?php } else { ?>
                                            <span class="no-img-box">
                                                <i class="bx bx-image"></i>
                                            </span>
                                        <?php } ?>

                                        <button type="button"
                                            class="btn btn-primary btn-sm btn-update-img"
                                            onclick="openImageModal(
                    <?= (int)$p['id'] ?>,
                    '<?= $safeField ?>',
                    '<?= $safeImg ?>',
                    '<?= $safeTitle ?>'
                )">
                                            <i class="bx bx-edit"></i> Update
                                        </button>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- IMAGE MODAL -->
<div class="modal fade image-modal" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" enctype="multipart/form-data" class="modal-content" id="imageForm">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-image-add"></i> Update Product Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="product_id" id="image_product_id">
                <input type="hidden" name="image_field" id="image_field">
                <input type="hidden" name="old_image" id="old_image">

                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input class="form-control" id="image_product_name" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select New Image</label>
                    <input type="file"
                        name="image"
                        class="form-control"
                        accept="image/*"
                        required>
                </div>

                <label class="form-label">Current Image</label>
                <div>
                    <img id="current_image_preview" src="" alt="Current Image">
                    <div id="current_image_empty">
                        No Image Available
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" class="btn btn-save-image" name="update_image">
                    <i class="bx bx-save"></i> Save Image
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    function openImageModal(id, field, oldImage, title) {
        document.getElementById('imageForm').reset();

        document.getElementById('image_product_id').value = id || '';
        document.getElementById('image_field').value = field || '';
        document.getElementById('old_image').value = oldImage || '';
        document.getElementById('image_product_name').value = title || '';

        const preview = document.getElementById('current_image_preview');
        const emptyBox = document.getElementById('current_image_empty');

        if (oldImage) {
            preview.src = '../uploads/products/' + oldImage;
            preview.style.display = 'inline-block';
            emptyBox.style.display = 'none';
        } else {
            preview.src = '';
            preview.style.display = 'none';
            emptyBox.style.display = 'inline-flex';
        }

        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    <?php if (isset($_GET['msg'])) { ?>
        <?php if ($_GET['msg'] == 'ok') { ?>
            if (typeof showToast === 'function') {
                showToast('success', 'Product image updated successfully!');
            }
        <?php } elseif ($_GET['msg'] == 'error') { ?>
            if (typeof showToast === 'function') {
                showToast('error', 'Something went wrong!');
            }
        <?php } ?>
    <?php } ?>
</script>

<?php ob_end_flush(); ?>