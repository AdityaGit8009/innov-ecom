<?php
$content = file_get_contents('training.php');
$styles = file_get_contents('training_styles.txt');
$start = strpos($content, '<style>');
$end = strpos($content, '</style>') + 8;
if ($start !== false && $end !== false) {
    $new_content = substr_replace($content, $styles, $start, $end - $start);
    file_put_contents('training.php', $new_content);
    echo "Replaced styles successfully.\n";
} else {
    echo "Could not find style block.\n";
}
