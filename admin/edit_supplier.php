<?php
require_once __DIR__ . '/check_admin.php';

$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM suppliers WHERE id=$id");
$supplier = mysqli_fetch_assoc($res);

if (isset($_POST['update_supplier'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query($conn, "UPDATE suppliers SET name='$name', email='$email', phone='$phone', address='$address' WHERE id=$id");
    header("Location: suppliers.php?msg=updated");
    exit;
}
?>

<?php include "includes/header.php"; ?>

<h4>Edit Supplier / Vendor</h4>

<form method="post">
    <div class="mb-3">
        <label>Supplier Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($supplier['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($supplier['email']) ?>">
    </div>
    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($supplier['phone']) ?>">
    </div>
    <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control"><?= htmlspecialchars($supplier['address']) ?></textarea>
    </div>
    <button type="submit" name="update_supplier" class="btn btn-success">Update Supplier</button>
    <a href="suppliers.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include "includes/footer.php"; ?>