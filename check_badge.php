<?php
ob_start();
include "column-right.php";
$output = ob_get_clean();
echo strpos($output, "Out Of Stock") !== false ? "Found Out Of Stock" : "Not Found";
