<?php
require_once __DIR__ . '/check_admin.php';

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $subcategories = [];
    $res = mysqli_query($conn, "SELECT * FROM sub_categories WHERE category_id=$category_id ORDER BY name");
    while ($row = mysqli_fetch_assoc($res)) {
        $subcategories[] = $row;
    }
    echo json_encode($subcategories);
}
