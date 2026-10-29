<?php
include '../config/db.php';

$q = $_GET['q'] ?? '';
$q = trim($q);
$q = mysqli_real_escape_string($conn, $q);

if (strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

$query = "
    SELECT p.id, p.title, p.main_image, c.name as category_name, sc.name as subcategory_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
    WHERE (p.title LIKE '%$q%' OR c.name LIKE '%$q%' OR sc.name LIKE '%$q%')
    AND p.status = 1
    ORDER BY 
        CASE WHEN p.title LIKE '$q%' THEN 1 ELSE 2 END,
        p.title ASC
    LIMIT 10
";

$res = mysqli_query($conn, $query);
$results = [];

function get_product_image($mainImage) {
    $mainImage = trim((string)$mainImage);
    $mainImage = str_replace('\\', '/', $mainImage);
    $mainImage = preg_replace('#^\./#', '', ltrim($mainImage, '/'));

    $fallbacks = [
        'assets/images/no-image.svg',
        'assets/images/no-image.png',
        'assets/images/logo/1.png'
    ];

    $candidates = [];
    if ($mainImage !== '') {
        $base = basename($mainImage);
        $candidates = [
            $mainImage,
            'uploads/products/' . $base,
            'uploads/product/' . $base,
            'uploads/' . $base,
            'assets/images/products/' . $base,
            'assets/images/' . $base
        ];
    }

    foreach (array_merge($candidates, $fallbacks) as $path) {
        if (file_exists(__DIR__ . '/../' . $path)) {
            return $path;
        }
    }
    return 'assets/images/no-image.svg';
}

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $img = get_product_image($row['main_image']);

        $catLabel = [];
        if (!empty($row['category_name'])) $catLabel[] = $row['category_name'];
        if (!empty($row['subcategory_name'])) $catLabel[] = $row['subcategory_name'];
        
        $results[] = [
            'id' => $row['id'],
            'title' => htmlspecialchars($row['title']),
            'image' => htmlspecialchars($img),
            'category' => htmlspecialchars(implode(' > ', $catLabel)),
            'link' => 'product.php?id=' . $row['id']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>
