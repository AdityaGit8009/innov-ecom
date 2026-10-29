<?php
require_once __DIR__ . '/check_admin.php';

$fileName = "course_accordion_bulk_upload_template.csv";

/*
| CSV Template Download
| Columns: Phase, Module, Topic
*/

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Pragma: no-cache");
header("Expires: 0");

/* UTF-8 BOM for Excel support */
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");

/* Header Row */
fputcsv($output, ["Phase", "Module", "Topic"]);

/* Sample Rows */
$rows = [
  ["Phase 1", "Module 1: Basics", "Introduction"],
  ["Phase 1", "Module 1: Basics", "Environment Setup"],
  ["Phase 1", "Module 2: Core Concepts", "Variables"],
  ["Phase 1", "Module 2: Core Concepts", "Functions"],
  ["Phase 2", "Module 1: Advanced Concepts", "Project Structure"],
  ["Phase 2", "Module 1: Advanced Concepts", "Final Project"],
];

foreach ($rows as $row) {
  fputcsv($output, $row);
}

fclose($output);
exit;
