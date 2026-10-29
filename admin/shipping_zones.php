<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_zone'])) {
    $id = (int)($_POST['id'] ?? 0);
    $zone_name = admin_safe($conn, $_POST['zone_name'] ?? '');
    $states = admin_safe($conn, $_POST['states'] ?? '');

    if ($zone_name == '') {
        header("Location: shipping_zones.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $res = mysqli_query($conn, "
            UPDATE shipping_zones 
            SET zone_name = '$zone_name', states = '$states'
            WHERE id = '$id'
        ");
    } else {
        $res = mysqli_query($conn, "
            INSERT INTO shipping_zones (zone_name, states) 
            VALUES ('$zone_name', '$states')
        ");
    }

    header("Location: shipping_zones.php?msg=" . ($res ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM shipping_zones 
        WHERE id = '$id'
    ");

    header("Location: shipping_zones.php?msg=deleted");
    exit;
}

/* ===== FETCH ===== */
$zones = mysqli_query($conn, "
    SELECT * 
    FROM shipping_zones 
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

    #shippingZonesTable {
        width: 100% !important;
        font-size: 14px;
    }

    #shippingZonesTable thead th {
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

    #shippingZonesTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #shippingZonesTable tbody tr:hover {
        background: #f8fafc;
    }

    .zone-name {
        font-weight: 800;
        color: #334155;
    }

    .states-text {
        max-width: 420px;
        white-space: normal;
        line-height: 1.5;
        color: #64748b;
    }

    .btn-add-zone {
        border-radius: 10px;
        font-weight: 700;
        padding: 8px 14px;
    }

    .date-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
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

    .action-dropdown .dropdown-item:hover {
        background: #f8fafc;
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

    .shipping-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .shipping-modal textarea.form-control {
        min-height: 95px;
        resize: none;
    }

    .shipping-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .shipping-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-zone {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-zone:hover {
        background: #e94d14;
        color: #fff;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-map-pin"></i> Shipping Zones
    </h4>

    <button class="btn btn-primary btn-add-zone" onclick="openAddZoneModal()">
        <i class="bx bx-plus"></i> Add Zone
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="shippingZonesTable"
                data-module="shipping zones" data-action-dropdown="1">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Zone</th>
                        <th>States</th>
                        <th>Created</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($z = mysqli_fetch_assoc($zones)) {
                        $zoneJson = htmlspecialchars(
                            json_encode($z, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="zone-name">
                                    <?= htmlspecialchars($z['zone_name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <div class="states-text">
                                    <?= htmlspecialchars($z['states'] ?? 'N/A') ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="date-badge">
                                    <?= !empty($z['created_at']) ? date("d M Y", strtotime($z['created_at'])) : 'N/A' ?>
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
                                                onclick='openEditZoneModal(<?= $zoneJson ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$z['id'] ?>"
                                                onclick="return confirm('Delete shipping zone?')"
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

<div class="modal fade shipping-modal" id="zoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="zoneForm">
            <div class="modal-header">
                <h5 class="modal-title" id="zoneModalTitle">Add Shipping Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="zone_id">

                <div class="mb-3">
                    <label class="form-label">Zone Name</label>
                    <input class="form-control" name="zone_name" id="zone_name" placeholder="Example: North Zone" required>
                </div>

                <div class="mb-0">
                    <label class="form-label">States</label>
                    <textarea class="form-control" name="states" id="states" placeholder="Example: Uttar Pradesh, Delhi, Haryana"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button class="btn btn-save-zone" name="save_zone">
                    <i class="bx bx-save"></i> Save Zone
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddZoneModal() {
        document.getElementById("zoneForm").reset();
        document.getElementById("zone_id").value = "";
        document.getElementById("zoneModalTitle").innerText = "Add Shipping Zone";

        new bootstrap.Modal(document.getElementById("zoneModal")).show();
    }

    function openEditZoneModal(z) {
        document.getElementById("zone_id").value = z.id || "";
        document.getElementById("zone_name").value = z.zone_name || "";
        document.getElementById("states").value = z.states || "";
        document.getElementById("zoneModalTitle").innerText = "Edit Shipping Zone";

        new bootstrap.Modal(document.getElementById("zoneModal")).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Shipping zone saved successfully!');}</script>";
    } elseif ($msg === 'deleted') {
        echo "<script>if(typeof showToast==='function'){showToast('warning','Shipping zone deleted successfully!');}</script>";
    } else {
        echo "<script>if(typeof showToast==='function'){showToast('error','Something went wrong!');}</script>";
    }
}

ob_end_flush();
?>