<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_charge'])) {
    $id = (int)($_POST['id'] ?? 0);
    $zone_id = (int)($_POST['zone_id'] ?? 0);
    $min_weight = (float)($_POST['min_weight'] ?? 0);
    $max_weight = (float)($_POST['max_weight'] ?? 0);
    $charge = (float)($_POST['charge'] ?? 0);

    if ($zone_id <= 0 || $charge < 0) {
        header("Location: shipping_charges.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $res = mysqli_query($conn, "
            UPDATE shipping_charges 
            SET 
                zone_id = '$zone_id',
                min_weight = '$min_weight',
                max_weight = '$max_weight',
                charge = '$charge'
            WHERE id = '$id'
        ");
    } else {
        $res = mysqli_query($conn, "
            INSERT INTO shipping_charges (zone_id, min_weight, max_weight, charge)
            VALUES ('$zone_id', '$min_weight', '$max_weight', '$charge')
        ");
    }

    header("Location: shipping_charges.php?msg=" . ($res ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM shipping_charges 
        WHERE id = '$id'
    ");

    header("Location: shipping_charges.php?msg=deleted");
    exit;
}

/* ===== FETCH ===== */
$zones = mysqli_query($conn, "
    SELECT * 
    FROM shipping_zones 
    ORDER BY zone_name ASC
");

$charges = mysqli_query($conn, "
    SELECT sc.*, sz.zone_name 
    FROM shipping_charges sc
    LEFT JOIN shipping_zones sz ON sc.zone_id = sz.id
    ORDER BY sc.id DESC
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

    #shippingChargesTable {
        width: 100% !important;
        font-size: 14px;
    }

    #shippingChargesTable thead th {
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

    #shippingChargesTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #shippingChargesTable tbody tr:hover {
        background: #f8fafc;
    }

    .zone-badge {
        background: #e0f2fe;
        color: #075985;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 800;
        display: inline-block;
        font-size: 12px;
    }

    .weight-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
    }

    .amount-badge {
        background: #dcfce7;
        color: #166534;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 800;
        display: inline-block;
        font-size: 13px;
    }

    .btn-add-charge {
        border-radius: 10px;
        font-weight: 700;
        padding: 8px 14px;
    }

    .action-dropdown .btn {
        border: 0;
        background: transparent;
        color: #334155;
        font-size: 20px;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .action-dropdown .btn:hover,
    .action-dropdown .btn:focus {
        background: #f1f5f9;
        color: #0f172a;
        box-shadow: none;
    }

    .action-dropdown .dropdown-menu {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
        padding: 8px;
        min-width: 150px;
    }

    .action-dropdown .dropdown-item {
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
    }

    .action-dropdown .dropdown-item.text-danger:hover {
        background: #fee2e2;
        color: #991b1b !important;
    }

    .shipping-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .shipping-modal .modal-header {
        background: linear-gradient(135deg, #ff6a00, #ff3d00);
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    .shipping-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .shipping-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .shipping-modal .modal-body {
        padding: 22px;
    }

    .shipping-modal .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        font-size: 14px;
    }

    .shipping-modal .form-control,
    .shipping-modal .form-select {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .shipping-modal .form-control:focus,
    .shipping-modal .form-select:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .shipping-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-charge {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-charge:hover {
        background: #e94d14;
        color: #fff;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-rupee"></i> Shipping Charges
    </h4>

    <button class="btn btn-primary btn-add-charge" onclick="openAddChargeModal()">
        <i class="bx bx-plus"></i> Add Charge
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="shippingChargesTable"
                data-module="shipping charges" data-action-dropdown="1">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Zone</th>
                        <th>Weight Range</th>
                        <th>Charge</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($c = mysqli_fetch_assoc($charges)) {
                        $chargeJson = htmlspecialchars(
                            json_encode($c, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td class="text-center">
                                <span class="zone-badge">
                                    <?= htmlspecialchars($c['zone_name'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="weight-badge">
                                    <?= format_price((float)($c['min_weight'] ?? 0)) ?>
                                    -
                                    <?= format_price((float)($c['max_weight'] ?? 0)) ?> Kg
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="amount-badge">
                                    ₹<?= format_price((float)($c['charge'] ?? 0)) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="dropdown action-dropdown">
                                    <button class="btn dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item"
                                                type="button"
                                                onclick='openEditChargeModal(<?= $chargeJson ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$c['id'] ?>"
                                                onclick="return confirm('Delete shipping charge?')"
                                                class="dropdown-item text-danger">
                                                <i class="bx bx-trash"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<div class="modal fade shipping-modal" id="chargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="chargeForm">
            <div class="modal-header">
                <h5 class="modal-title" id="chargeModalTitle">Add Shipping Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="charge_id">

                <div class="mb-3">
                    <label class="form-label">Zone</label>
                    <select name="zone_id" id="zone_id" class="form-select" required>
                        <option value="">Select Zone</option>
                        <?php
                        mysqli_data_seek($zones, 0);
                        while ($z = mysqli_fetch_assoc($zones)) {
                        ?>
                            <option value="<?= (int)$z['id'] ?>">
                                <?= htmlspecialchars($z['zone_name'] ?? '') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Min Weight</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="min_weight" id="min_weight" placeholder="0.00">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Max Weight</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="max_weight" id="max_weight" placeholder="5.00">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Charge</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="charge" id="charge" placeholder="₹">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button class="btn btn-save-charge" name="save_charge">
                    <i class="bx bx-save"></i> Save Charge
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddChargeModal() {
        document.getElementById("chargeForm").reset();
        document.getElementById("charge_id").value = "";
        document.getElementById("chargeModalTitle").innerText = "Add Shipping Charge";

        new bootstrap.Modal(document.getElementById("chargeModal")).show();
    }

    function openEditChargeModal(c) {
        document.getElementById("charge_id").value = c.id || "";
        document.getElementById("zone_id").value = c.zone_id || "";
        document.getElementById("min_weight").value = c.min_weight || "";
        document.getElementById("max_weight").value = c.max_weight || "";
        document.getElementById("charge").value = c.charge || "";
        document.getElementById("chargeModalTitle").innerText = "Edit Shipping Charge";

        new bootstrap.Modal(document.getElementById("chargeModal")).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Shipping charge saved successfully!');}</script>";
    } elseif ($msg === 'deleted') {
        echo "<script>if(typeof showToast==='function'){showToast('warning','Shipping charge deleted successfully!');}</script>";
    } else {
        echo "<script>if(typeof showToast==='function'){showToast('error','Something went wrong!');}</script>";
    }
}

ob_end_flush();
?>