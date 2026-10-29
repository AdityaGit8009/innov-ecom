<?php
ob_start();
include "column-right.php";
$output = ob_get_clean();
file_put_contents('output_test.html', $output);
