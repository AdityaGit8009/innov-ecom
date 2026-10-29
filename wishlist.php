<?php include 'header.php';
include "config/db.php";
require_once "secure_helpers.php";

$user_id = ecom_require_user();
$q = mysqli_query(
    $conn,
    "SELECT w.id AS wid, p.*
  FROM wishlist w
  JOIN products p ON p.id = w.product_id
  WHERE w.user_id = '$user_id'
  ORDER BY w.id DESC"
);
?>

<!-- ================= BREADCRUMB ================= -->
<section class="breadcrumb-section">
    <h1><?= "Wishlist" ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> <span>| Wishlist</span>
    </p>
</section>

<div class="container my-5">
    <div class="row g-4">

        <?php if (mysqli_num_rows($q)): ?>
            <?php while ($p = mysqli_fetch_assoc($q)): ?>
                <div class="col-lg-3">
                    <div class="card p-3 h-100">

                        <img src="uploads/products/<?= $p['main_image']; ?>" class="mb-2">

                        <h6><?= $p['title']; ?></h6>
                        <p>₹<?= $p['price']; ?></p>

                        <div class="d-flex justify-content-between mt-auto">
                            <a href="cart.php?add=<?= $p['id']; ?>" class="btn btn-sm btn-primary">Move to Cart</a>
                            <button type="button" class="btn btn-link text-danger p-0 remove-wishlist-btn" data-wid="<?= $p['wid']; ?>">Remove</button>

                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Wishlist Empty</p>
        <?php endif; ?>

    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        $(".remove-wishlist-btn").click(function() {

            let wid = $(this).data("wid"); // wishlist id
            let card = $(this).closest(".col-lg-3");

            $.ajax({
                url: "remove_wishlist.php",
                method: "POST",
                data: {
                    wid: wid
                },

                success: function(response) {

                    if (response.trim() === "success") {

                        // remove card from UI
                        card.fadeOut(300, function() {
                            $(this).remove();
                        });

                        // 🔥 REAL-TIME HEADER UPDATE
                        updateHeaderCounts();

                        // wishlist empty message
                        if ($(".remove-wishlist-btn").length === 1) {
                            $(".container .row").html(
                                '<p class="text-center">Wishlist Empty</p>'
                            );
                        }

                    }

                },
                error: function() {
                    alert("Something went wrong. Please try again!");
                }
            });

        });

    });
</script>


<script>
    $(".add-wishlist-btn").click(function() {

        let pid = $(this).data("pid");

        $.ajax({
            url: "ajax/add_to_wishlist.php",
            type: "POST",
            data: {
                product_id: pid
            },
            success: function(res) {

                if (res.trim() === "success") {

                    updateHeaderCounts();

                    alert("Added to wishlist"); // optional
                }

            }
        });
    });
</script>