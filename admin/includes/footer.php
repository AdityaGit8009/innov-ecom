</div>
<!-- / Content --> <!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div
            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="mb-2 mb-md-0">
                &#169;
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with by
                <a href="https://softproindia.in/" target="_blank" class="footer-link">Softpro India Computer Technologies Pvt. Ltd.</a>
            </div>
        </div>
    </div>
</footer>
<!-- / Footer -->
<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div> <!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<script src="./assets/vendor/libs/jquery/jquery.js"></script>
<script src="./assets/vendor/libs/popper/popper.js"></script>
<script src="./assets/vendor/js/bootstrap.js"></script>
<script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="./assets/vendor/js/menu.js"></script><!-- endbuild --><!-- Vendors JS -->
<script src="./assets/vendor/libs/apex-charts/apexcharts.js"></script><!-- Main JS -->
<script src="./assets/js/main.js"></script><!-- Page JS -->
<script src="./assets/js/dashboards-analytics.js"></script><!-- Place this tag before closing body tag for github widget button. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['msg'])) {
?>
    <script>
        function showToast(type = 'info', message = '', timer = 3000) {
            Swal.fire({
                toast: true, // Makes it a toast
                position: 'bottom-end', // Position of toast
                icon: type, // 'success', 'error', 'warning', 'info', 'question'
                title: message, // Message text
                showConfirmButton: false, // No confirm button
                timer: timer, // Auto close after X ms
                timerProgressBar: true, // Progress bar
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        }
    </script>



<?php }
?>

<style>
    .admin-table-card,
    .card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .07)
    }

    .table thead th {
        background: #fff3ed !important;
        color: #3b1d0f !important;
        border-bottom: 1px solid #ffd6c4 !important;
        font-weight: 800;
        white-space: nowrap
    }

    .table td {
        vertical-align: middle
    }

    .dt-buttons .btn {
        border-radius: 10px !important;
        margin-right: 6px !important
    }

    .dropdown.action-menu .btn {
        border-radius: 10px;
        border: 1px solid #eee;
        background: #fff;
        color: #333
    }

    .menu-item.active>.menu-link,
    .menu-item.open>.menu-link {
        background: #fff1e8 !important;
        color: #ff5a1f !important;
        font-weight: 800;
        border-radius: 10px
    }

    .menu-item.active>.menu-link i {
        color: #ff5a1f !important
    }
</style>
<!-- DataTables Core -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<!-- Common Admin DataTable JS -->
<script src="./assets/js/admin-datatable.js"></script>






</body>

</html>