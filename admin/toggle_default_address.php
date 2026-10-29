<?php
require_once __DIR__ . '/check_admin.php';

$id = $_POST['id'];
$customer_id = $_POST['customer_id'];

/* Sab addresses ko non-default karo */
mysqli_query($conn, "
    UPDATE customer_addresses 
    SET is_default = 0 
    WHERE customer_id = '$customer_id'
");

/* Selected address ko default banao */
mysqli_query($conn, "
    UPDATE customer_addresses 
    SET is_default = 1 
    WHERE id = '$id'
");

echo 1;
