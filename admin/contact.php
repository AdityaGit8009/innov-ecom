<?php
ob_start();
require_once __DIR__ . '/check_admin.php';

/* ===== DELETE MESSAGE ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM contact_messages 
        WHERE id = '$id'
    ");

    header("Location: contact_messages.php?msg=deleted");
    exit;
}

/* ===== FETCH MESSAGES ===== */
$messages = mysqli_query($conn, "
    SELECT * 
    FROM contact_messages 
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

    #contactMessagesTable {
        width: 100% !important;
        font-size: 14px;
    }

    #contactMessagesTable thead th {
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

    #contactMessagesTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #contactMessagesTable tbody tr:hover {
        background: #f8fafc;
    }

    .message-name {
        font-weight: 800;
        color: #334155;
    }

    .message-email {
        color: #64748b;
        font-size: 13px;
    }

    .subject-badge {
        background: #e0f2fe;
        color: #075985;
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
        max-width: 170px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .message-preview {
        max-width: 280px;
        white-space: normal;
        line-height: 1.5;
        color: #64748b;
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

    .contact-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
    }

    .contact-modal .modal-header {
        background: linear-gradient(135deg, #ff6a00, #ff3d00);
        color: #fff;
        border: 0;
        padding: 18px 22px;
        border-radius: 18px 18px 0 0;
}

    .contact-modal .modal-title {
        font-weight: 800;
        font-size: 18px;
    }

    .contact-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .contact-modal .modal-body {
        padding: 22px;
        background: #fff;
    }

    .contact-view-box {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
        background: #f8fafc;
    }

    .contact-view-box:last-child {
        margin-bottom: 0;
    }

    .contact-label {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .contact-value {
        color: #334155;
        font-weight: 700;
        word-break: break-word;
    }

    .contact-message-full {
        color: #475569;
        line-height: 1.7;
        white-space: pre-wrap;
        font-weight: 500;
    }

    @media (max-width: 767px) {
        .admin-card .card-body {
            padding: 14px;
        }

        .page-title-box h4 {
            font-size: 18px;
        }

        .message-preview {
            max-width: 220px;
        }
    }
</style>

<div class="page-title-box">
    <h4>
        <i class="bx bx-envelope"></i> Contact Messages
    </h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="contactMessagesTable"
                data-module="contact messages">

                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($messages)) {
                        $message = $row['message'] ?? '';
                        $shortMessage = mb_strlen($message) > 55
                            ? mb_substr($message, 0, 55) . '...'
                            : $message;

                        $messageJson = htmlspecialchars(
                            json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="message-name">
                                    <?= htmlspecialchars($row['name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <span class="message-email">
                                    <?= htmlspecialchars($row['email'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['phone'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <span class="subject-badge" title="<?= htmlspecialchars($row['subject'] ?? '') ?>">
                                    <?= htmlspecialchars($row['subject'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <td>
                                <div class="message-preview">
                                    <?= htmlspecialchars($shortMessage) ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="date-badge">
                                    <?= !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : 'N/A' ?>
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
                                                onclick='openViewModal(<?= $messageJson ?>)'>
                                                <i class="bx bx-show"></i> View
                                            </button>
                                        </li>

                                        <li>
                                            <a href="?delete=<?= (int)$row['id'] ?>"
                                                onclick="return confirm('Delete this message?')"
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

<!-- ===== VIEW MODAL ===== -->
<div class="modal fade contact-modal" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-envelope-open"></i> Contact Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="contact-view-box">
                            <div class="contact-label">Name</div>
                            <div class="contact-value" id="v_name"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="contact-view-box">
                            <div class="contact-label">Phone</div>
                            <div class="contact-value" id="v_phone"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="contact-view-box">
                            <div class="contact-label">Email</div>
                            <div class="contact-value" id="v_email"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="contact-view-box">
                            <div class="contact-label">Subject</div>
                            <div class="contact-value" id="v_subject"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="contact-view-box">
                            <div class="contact-label">Message</div>
                            <div class="contact-message-full" id="v_message"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function openViewModal(m) {
        document.getElementById('v_name').innerText = m.name || 'N/A';
        document.getElementById('v_email').innerText = m.email || 'N/A';
        document.getElementById('v_phone').innerText = m.phone || 'N/A';
        document.getElementById('v_subject').innerText = m.subject || 'N/A';
        document.getElementById('v_message').innerText = m.message || 'N/A';

        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }
</script>

<?php include "includes/footer.php"; ?>

<?php
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    if ($msg === 'deleted') {
        echo "<script>
            if (typeof showToast === 'function') {
                showToast('warning', 'Message deleted successfully!');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Message deleted successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        </script>";
    }
}

ob_end_flush();
?>