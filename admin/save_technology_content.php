<?php
require_once __DIR__ . '/check_admin.php';

$technology_id = (int)($_POST['technology_id'] ?? 0);

if ($technology_id <= 0) {
  header("Location: manage_training_technologies.php");
  exit;
}

function clean($conn, $value)
{
  return mysqli_real_escape_string($conn, trim((string)$value));
}

function uploadCompanyLogo($fileArray, $index)
{
  $folder = "../uploads/companies/";

  if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
  }

  $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
  $name = $fileArray['name'][$index] ?? '';
  $error = $fileArray['error'][$index] ?? 1;
  $tmp = $fileArray['tmp_name'][$index] ?? '';

  if ($error !== UPLOAD_ERR_OK || $name == '') {
    return '';
  }

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed)) {
    return '';
  }

  $fileName = time() . "_" . rand(1000, 9999) . "." . $ext;
  $target = $folder . $fileName;

  if (move_uploaded_file($tmp, $target)) {
    return "uploads/companies/" . $fileName;
  }

  return '';
}

/* ================= FEATURE CARDS ================= */

mysqli_query($conn, "DELETE FROM technology_features WHERE technology_id='$technology_id'");

if (!empty($_POST['feature_title']) && is_array($_POST['feature_title'])) {
  foreach ($_POST['feature_title'] as $index => $title) {
    $title = trim($title);

    if ($title == '') {
      continue;
    }

    $icon = clean($conn, $_POST['feature_icon'][$index] ?? '');
    $title = clean($conn, $title);
    $desc = clean($conn, $_POST['feature_desc'][$index] ?? '');
    $sort = $index + 1;

    mysqli_query($conn, "INSERT INTO technology_features
      (technology_id, icon, title, description, sort_order, status)
      VALUES
      ('$technology_id', '$icon', '$title', '$desc', '$sort', 1)
    ");
  }
}

/* ================= COMPANY LOGOS ================= */

mysqli_query($conn, "DELETE FROM technology_companies WHERE technology_id='$technology_id'");

$companySort = 1;

if (!empty($_POST['old_company_logos']) && is_array($_POST['old_company_logos'])) {
  foreach ($_POST['old_company_logos'] as $logo) {
    $logo = clean($conn, $logo);

    if ($logo == '') {
      continue;
    }

    mysqli_query($conn, "INSERT INTO technology_companies
      (technology_id, logo, sort_order, status)
      VALUES
      ('$technology_id', '$logo', '$companySort', 1)
    ");

    $companySort++;
  }
}

if (!empty($_FILES['company_logos']['name'][0])) {
  foreach ($_FILES['company_logos']['name'] as $index => $name) {
    $path = uploadCompanyLogo($_FILES['company_logos'], $index);

    if ($path == '') {
      continue;
    }

    $path = clean($conn, $path);

    mysqli_query($conn, "INSERT INTO technology_companies
      (technology_id, logo, sort_order, status)
      VALUES
      ('$technology_id', '$path', '$companySort', 1)
    ");

    $companySort++;
  }
}

/* ================= COURSE CONTENT ================= */

function clearOldCourseContent($conn, $technology_id)
{
  $phaseIds = [];

  $phaseQ = mysqli_query($conn, "SELECT id FROM technology_phases WHERE technology_id='$technology_id'");

  if ($phaseQ && mysqli_num_rows($phaseQ) > 0) {
    while ($phase = mysqli_fetch_assoc($phaseQ)) {
      $phaseIds[] = (int)$phase['id'];
    }
  }

  if (!empty($phaseIds)) {
    $phaseIdStr = implode(',', $phaseIds);

    $moduleIds = [];
    $moduleQ = mysqli_query($conn, "SELECT id FROM technology_modules WHERE phase_id IN ($phaseIdStr)");

    if ($moduleQ && mysqli_num_rows($moduleQ) > 0) {
      while ($module = mysqli_fetch_assoc($moduleQ)) {
        $moduleIds[] = (int)$module['id'];
      }
    }

    if (!empty($moduleIds)) {
      $moduleIdStr = implode(',', $moduleIds);
      mysqli_query($conn, "DELETE FROM technology_topics WHERE module_id IN ($moduleIdStr)");
    }

    mysqli_query($conn, "DELETE FROM technology_modules WHERE phase_id IN ($phaseIdStr)");
  }

  mysqli_query($conn, "DELETE FROM technology_phases WHERE technology_id='$technology_id'");
}

function saveCourseContent($conn, $technology_id, $course)
{
  clearOldCourseContent($conn, $technology_id);

  if (!is_array($course) || empty($course)) {
    return;
  }

  foreach ($course as $phaseIndex => $phase) {
    $phaseTitle = trim($phase['phase_title'] ?? '');

    if ($phaseTitle == '') {
      continue;
    }

    $phaseTitle = mysqli_real_escape_string($conn, $phaseTitle);
    $phaseSort = $phaseIndex + 1;

    mysqli_query($conn, "INSERT INTO technology_phases
      (technology_id, phase_title, sort_order, status)
      VALUES
      ('$technology_id', '$phaseTitle', '$phaseSort', 1)
    ");

    $phase_id = mysqli_insert_id($conn);

    if (!empty($phase['modules']) && is_array($phase['modules'])) {
      foreach ($phase['modules'] as $moduleIndex => $module) {
        $moduleTitle = trim($module['module_title'] ?? '');

        if ($moduleTitle == '') {
          continue;
        }

        $moduleTitle = mysqli_real_escape_string($conn, $moduleTitle);
        $moduleSort = $moduleIndex + 1;

        mysqli_query($conn, "INSERT INTO technology_modules
          (phase_id, module_title, sort_order, status)
          VALUES
          ('$phase_id', '$moduleTitle', '$moduleSort', 1)
        ");

        $module_id = mysqli_insert_id($conn);

        if (!empty($module['topics']) && is_array($module['topics'])) {
          foreach ($module['topics'] as $topicIndex => $topicText) {
            $topicText = trim($topicText);

            if ($topicText == '') {
              continue;
            }

            $topicText = mysqli_real_escape_string($conn, $topicText);
            $topicSort = $topicIndex + 1;

            mysqli_query($conn, "INSERT INTO technology_topics
              (module_id, topic_text, sort_order, status)
              VALUES
              ('$module_id', '$topicText', '$topicSort', 1)
            ");
          }
        }
      }
    }
  }
}

function parseExcelCourseContent($filePath, $excelExt)
{
  $course = [];
  $phaseMap = [];
  $moduleMap = [];

  if ($excelExt !== 'csv') {
    return [];
  }

  if (($handle = fopen($filePath, "r")) === false) {
    return [];
  }

  $rowIndex = 0;

  while (($row = fgetcsv($handle, 10000, ",")) !== false) {
    $rowIndex++;

    if ($rowIndex == 1) {
      continue;
    }

    $phaseTitle = trim((string)($row[0] ?? ''));
    $moduleTitle = trim((string)($row[1] ?? ''));
    $topicText = trim((string)($row[2] ?? ''));

    $phaseTitle = preg_replace('/^\xEF\xBB\xBF/', '', $phaseTitle);

    if ($phaseTitle == '' || $moduleTitle == '' || $topicText == '') {
      continue;
    }

    $phaseKey = strtolower($phaseTitle);
    $moduleKey = $phaseKey . '|' . strtolower($moduleTitle);

    if (!isset($phaseMap[$phaseKey])) {
      $course[] = [
        'phase_title' => $phaseTitle,
        'modules' => []
      ];

      $phaseMap[$phaseKey] = count($course) - 1;
    }

    $phaseIndex = $phaseMap[$phaseKey];

    if (!isset($moduleMap[$moduleKey])) {
      $course[$phaseIndex]['modules'][] = [
        'module_title' => $moduleTitle,
        'topics' => []
      ];

      $moduleMap[$moduleKey] = count($course[$phaseIndex]['modules']) - 1;
    }

    $moduleIndex = $moduleMap[$moduleKey];

    $course[$phaseIndex]['modules'][$moduleIndex]['topics'][] = $topicText;
  }

  fclose($handle);

  return $course;
}
$excelUploaded = isset($_FILES['bulk_course_excel'])
  && $_FILES['bulk_course_excel']['error'] === UPLOAD_ERR_OK
  && $_FILES['bulk_course_excel']['name'] != '';

if ($excelUploaded) {
  $allowedExcel = ['csv'];
  $excelExt = strtolower(pathinfo($_FILES['bulk_course_excel']['name'], PATHINFO_EXTENSION));

  if (!in_array($excelExt, $allowedExcel)) {
    header("Location: manage_technology_content.php?id=" . $technology_id . "&error=invalid_excel");
    exit;
  }

  try {
    $course = parseExcelCourseContent($_FILES['bulk_course_excel']['tmp_name'], $excelExt);

    if (!is_array($course) || empty($course)) {
      header("Location: manage_technology_content.php?id=" . $technology_id . "&error=empty_course");
      exit;
    }

    saveCourseContent($conn, $technology_id, $course);
  } catch (Exception $e) {
    header("Location: manage_technology_content.php?id=" . $technology_id . "&error=invalid_excel");
    exit;
  }
} else {
  $course_json = $_POST['course_json'] ?? '[]';
  $course = json_decode($course_json, true);

  if (!is_array($course) || empty($course)) {
    header("Location: manage_technology_content.php?id=" . $technology_id . "&error=empty_course");
    exit;
  }

  saveCourseContent($conn, $technology_id, $course);
}

header("Location: manage_technology_content.php?id=" . $technology_id . "&success=1");
exit;
