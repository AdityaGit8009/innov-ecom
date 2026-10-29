<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

function admin_safe($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)($value ?? '')));
}

/* ===== ADD / UPDATE ===== */
if (isset($_POST['save_partner'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = admin_safe($conn, $_POST['name'] ?? '');
    $contact = admin_safe($conn, $_POST['contact'] ?? '');
    $tracking_url = admin_safe($conn, $_POST['tracking_url'] ?? '');

    if ($name == '') {
        header("Location: delivery_partners.php?msg=error");
        exit;
    }

    if ($id > 0) {
        $res = mysqli_query($conn, "
            UPDATE delivery_partners 
            SET 
                name = '$name',
                contact = '$contact',
                tracking_url = '$tracking_url'
            WHERE id = '$id'
        ");
    } else {
        $res = mysqli_query($conn, "
            INSERT INTO delivery_partners (name, contact, tracking_url)
            VALUES ('$name', '$contact', '$tracking_url')
        ");
    }

    header("Location: delivery_partners.php?msg=" . ($res ? "ok" : "error"));
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM delivery_partners 
        WHERE id = '$id'
    ");

    header("Location: delivery_partners.php?msg=deleted");
    exit;
}

/* ===== FETCH ===== */
$partners = mysqli_query($conn, "
    SELECT * 
    FROM delivery_partners 
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

    #deliveryPartnersTable {
        width: 100% !important;
        font-size: 14px;
    }

    #deliveryPartnersTable thead th {
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

    #deliveryPartnersTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #deliveryPartnersTable tbody tr:hover {
        background: #f8fafc;
    }

    .partner-name {
        font-weight: 800;
        color: #334155;
    }

    .contact-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
    }

    .track-link {
        background: #e0f2fe;
        color: #075985;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        text-decoration: none;
    }

    .track-link:hover {
        background: #bae6fd;
        color: #075985;
    }

    .btn-add-partner {
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

    .shipping-modal .form-control {
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        box-shadow: none;
        font-size: 14px;
    }

    .shipping-modal .form-control:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, .15);
    }

    .shipping-modal .modal-footer {
        border: 0;
        padding: 0 22px 22px;
    }

    .btn-save-partner {
        background: #ff5a1f;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 800;
    }

    .btn-save-partner:hover {
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
        <i class="bx bx-truck"></i> Delivery Partners
    </h4>

    <button class="btn btn-primary btn-add-partner" onclick="openAddPartnerModal()">
        <i class="bx bx-plus"></i> Add Partner
    </button>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="deliveryPartnersTable"
                data-module="delivery partners" data-action-dropdown="1">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Partner Name</th>
                        <th>Contact</th>
                        <th>Tracking</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($p = mysqli_fetch_assoc($partners)) {
                        $partnerJson = htmlspecialchars(
                            json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="partner-name">
                                    <?= htmlspecialchars($p['name'] ?? '') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="contact-badge">
                                    <?= htmlspecialchars($p['contact'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php if (!empty($p['tracking_url'])): ?>
                                    <a class="track-link"
                                        href="<?= htmlspecialchars($p['tracking_url']) ?>"
                                        target="_blank">
                                        <i class="bx bx-link-external"></i> Track
                                    </a>
                                <?php else: ?>
                                    <span class="contact-badge">N/A</span>
                                <?php endif; ?>
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
                                                onclick='openEditPartnerModal(<?= $partnerJson ?>)'>
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$p['id'] ?>"
                                                onclick="return confirm('Delete delivery partner?')"
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

<div class="modal fade shipping-modal" id="partnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content" id="partnerForm">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerModalTitle">Add Delivery Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="partner_id">

                <div class="mb-3">
                    <label class="form-label">Partner Name</label>
                    <input class="form-control" name="name" id="partner_name" placeholder="Example: Delhivery" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input class="form-control" name="contact" id="partner_contact" placeholder="Contact number / email">
                </div>

                <div class="mb-0">
                    <label class="form-label">Tracking URL</label>
                    <input class="form-control" name="tracking_url" id="tracking_url" placeholder="https://example.com/track">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                <button class="btn btn-save-partner" name="save_partner">
                    <i class="bx bx-save"></i> Save Partner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddPartnerModal() {
        document.getElementById("partnerForm").reset();
        document.getElementById("partner_id").value = "";
        document.getElementById("partnerModalTitle").innerText = "Add Delivery Partner";

        new bootstrap.Modal(document.getElementById("partnerModal")).show();
    }

    function openEditPartnerModal(p) {
        document.getElementById("partner_id").value = p.id || "";
        document.getElementById("partner_name").value = p.name || "";
        document.getElementById("partner_contact").value = p.contact || "";
        document.getElementById("tracking_url").value = p.tracking_url || "";
        document.getElementById("partnerModalTitle").innerText = "Edit Delivery Partner";

        new bootstrap.Modal(document.getElementById("partnerModal")).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'ok') {
        echo "<script>if(typeof showToast==='function'){showToast('success','Delivery partner saved successfully!');}</script>";
    } elseif ($msg === 'deleted') {
        echo "<script>if(typeof showToast==='function'){showToast('warning','Delivery partner deleted successfully!');}</script>";
    } else {
        echo "<script>if(typeof showToast==='function'){showToast('error','Something went wrong!');}</script>";
    }
}

ob_end_flush();
?>