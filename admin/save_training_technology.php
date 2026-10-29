<?php
require_once __DIR__ . '/check_admin.php';

function redirectBack($type)
{
  header("Location: manage_training_technologies.php?$type=1");
  exit;
}

function clean($conn, $value)
{
  return mysqli_real_escape_string($conn, trim((string)$value));
}

function makeSlug($text)
{
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  $text = trim($text, '-');
  return $text;
}

function uploadTechnologyImage($field)
{
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    return '';
  }

  $folder = "../uploads/technologies/";

  if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
  }

  $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
  $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed)) {
    return '';
  }

  $fileName = time() . "_" . rand(1000, 9999) . "." . $ext;
  $target = $folder . $fileName;

  if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
    return "uploads/technologies/" . $fileName;
  }

  return '';
}

$id = (int)($_POST['id'] ?? 0);

$title = clean($conn, $_POST['title'] ?? '');
$slugRaw = trim($_POST['slug'] ?? '');

if ($slugRaw == '') {
  $slugRaw = $title;
}

$slug = clean($conn, makeSlug($slugRaw));

$short_text = clean($conn, $_POST['short_text'] ?? 'Certification');
$rating = (int)($_POST['rating'] ?? 5);
$sort_order = (int)($_POST['sort_order'] ?? 0);
$detail_title = clean($conn, $_POST['detail_title'] ?? '');
$detail_description = clean($conn, $_POST['detail_description'] ?? '');
$batch_text = clean($conn, $_POST['batch_text'] ?? 'New Batch Starting Soon – Enroll Today!');
$status = (int)($_POST['status'] ?? 1);

if ($rating < 1) {
  $rating = 1;
}

if ($rating > 5) {
  $rating = 5;
}

if ($title == '' || $slug == '') {
  redirectBack('error');
}

$image = uploadTechnologyImage('image');
$detail_image = uploadTechnologyImage('detail_image');

$checkSlugSql = "SELECT id FROM training_technologies WHERE slug='$slug'";
if ($id > 0) {
  $checkSlugSql .= " AND id!='$id'";
}
$checkSlugSql .= " LIMIT 1";

$checkSlugQ = mysqli_query($conn, $checkSlugSql);

if ($checkSlugQ && mysqli_num_rows($checkSlugQ) > 0) {
  header("Location: manage_training_technologies.php?error=slug_exists");
  exit;
}

if ($id > 0) {
  $imageSql = "";

  if ($image != '') {
    $image = clean($conn, $image);
    $imageSql .= ", image='$image'";
  }

  if ($detail_image != '') {
    $detail_image = clean($conn, $detail_image);
    $imageSql .= ", detail_image='$detail_image'";
  }

  $sql = "UPDATE training_technologies SET
            title='$title',
            slug='$slug',
            short_text='$short_text',
            rating='$rating',
            sort_order='$sort_order',
            detail_title='$detail_title',
            detail_description='$detail_description',
            batch_text='$batch_text',
            status='$status'
            $imageSql
          WHERE id='$id'";

  mysqli_query($conn, $sql);
} else {
  $link = clean($conn, "technology.php?slug=" . $slug);
  $image = clean($conn, $image);
  $detail_image = clean($conn, $detail_image);

  $sql = "INSERT INTO training_technologies
          (
            title,
            slug,
            image,
            link,
            short_text,
            rating,
            sort_order,
            status,
            detail_title,
            detail_image,
            detail_description,
            batch_text
          )
          VALUES
          (
            '$title',
            '$slug',
            '$image',
            '$link',
            '$short_text',
            '$rating',
            '$sort_order',
            '$status',
            '$detail_title',
            '$detail_image',
            '$detail_description',
            '$batch_text'
          )";

  mysqli_query($conn, $sql);
}

header("Location: manage_training_technologies.php?success=1");
exit;
