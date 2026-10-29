<?php
ob_start();
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

/* ===== FETCH ADDRESSES ===== */
$addresses = mysqli_query($conn, "
    SELECT *
    FROM customer_addresses
    ORDER BY id DESC
");
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

    #customerAddressesTable {
        width: 100% !important;
        font-size: 14px;
    }

    #customerAddressesTable thead th {
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

    #customerAddressesTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px;
        color: #64748b;
        border-color: #e5e7eb;
    }

    #customerAddressesTable tbody tr:hover {
        background: #f8fafc;
    }

    .customer-name {
        font-weight: 700;
        color: #334155;
    }

    .badge-soft {
        padding: 7px 10px;
        border-radius: 30px;
        font-weight: 700;
        display: inline-block;
        font-size: 12px;
    }

    .badge-soft-primary {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-default-toggle {
        border: 0;
        border-radius: 30px;
        padding: 7px 14px;
        font-weight: 700;
        font-size: 12px;
        min-width: 65px;
    }

    .btn-default-yes {
        background: #dcfce7;
        color: #166534;
    }

    .btn-default-no {
        background: #e2e8f0;
        color: #475569;
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
        <i class="bx bx-map"></i> Customer Addresses
    </h4>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle datatable w-100"
                id="customerAddressesTable"
                data-module="customer addresses">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No.</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Address Type</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Pincode</th>
                        <th>Default</th>
                        <th>Added On</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($addresses)) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>

                            <td>
                                <span class="customer-name">
                                    <?= htmlspecialchars($row['name'] ?? '') ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['phone'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-soft badge-soft-primary">
                                    <?= htmlspecialchars($row['type'] ?? 'Home') ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['city'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['state'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['pincode'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btn-default-toggle toggle-default <?= !empty($row['is_default']) ? 'btn-default-yes' : 'btn-default-no' ?>"
                                    data-id="<?= (int)$row['id'] ?>"
                                    data-customer="<?= (int)$row['customer_id'] ?>">
                                    <?= !empty($row['is_default']) ? 'Yes' : 'No' ?>
                                </button>
                            </td>

                            <td class="text-center">
                                <?= !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : '' ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
    $(document).on("click", ".toggle-default", function() {
        let btn = $(this);
        let id = btn.data("id");
        let customer_id = btn.data("customer");

        $.ajax({
            url: "toggle_default_address.php",
            type: "POST",
            data: {
                id: id,
                customer_id: customer_id
            },
            success: function(res) {
                if ($.trim(res) === "1") {
                    $(".toggle-default").each(function() {
                        if ($(this).data("customer") == customer_id) {
                            $(this)
                                .removeClass("btn-default-yes")
                                .addClass("btn-default-no")
                                .text("No");
                        }
                    });

                    btn
                        .removeClass("btn-default-no")
                        .addClass("btn-default-yes")
                        .text("Yes");

                    if (typeof showToast === "function") {
                        showToast("success", "Default address updated successfully!");
                    }
                } else {
                    if (typeof showToast === "function") {
                        showToast("error", "Default address update failed!");
                    } else {
                        alert("Default address update failed!");
                    }
                }
            },
            error: function() {
                if (typeof showToast === "function") {
                    showToast("error", "Something went wrong!");
                } else {
                    alert("Something went wrong!");
                }
            }
        });
    });
</script>

<?php ob_end_flush(); ?>