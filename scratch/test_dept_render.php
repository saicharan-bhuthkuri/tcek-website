<?php
chdir(__DIR__ . '/../public');

// Test 1: CSE dynamic detail page
$_GET['slug'] = 'cse';
ob_start();
include 'department.php';
$cse_html = ob_get_clean();
echo "CSE Page Output: " . strlen($cse_html) . " bytes\n";
echo (strpos($cse_html, 'Computer Science') !== false ? " [OK] CSE Title found\n" : " [FAIL] CSE Title missing\n");
echo (strpos($cse_html, 'R22B.Tech.CSEIandIIYearSyllabus.pdf') !== false ? " [OK] Dynamic syllabus found\n" : " [FAIL] Syllabus link missing\n");

// Test 2: AIML dynamic detail page
$_GET['slug'] = 'aiml';
ob_start();
include 'department.php';
$aiml_html = ob_get_clean();
echo "AIML Page Output: " . strlen($aiml_html) . " bytes\n";
echo (strpos($aiml_html, 'Artificial Intelligence') !== false ? " [OK] AIML Title found\n" : " [FAIL] AIML Title missing\n");

// Test 3: Departments directory page
unset($_GET['slug']);
ob_start();
include 'departments.php';
$depts_html = ob_get_clean();
echo "Departments Directory Output: " . strlen($depts_html) . " bytes\n";
echo (strpos($depts_html, "openCourseTab(event, 'btech')") !== false ? " [OK] Dynamic btech tab found\n" : " [FAIL] btech tab missing\n");
echo (strpos($depts_html, "openCourseTab(event, 'diploma')") !== false ? " [OK] Dynamic diploma tab found\n" : " [FAIL] diploma tab missing\n");
echo (strpos($depts_html, "openCourseTab(event, 'mba')") !== false ? " [OK] Dynamic mba tab found\n" : " [FAIL] mba tab missing\n");
echo (strpos($depts_html, "department.php?slug=cse") !== false ? " [OK] Dynamic CSE link found\n" : " [FAIL] CSE link missing\n");
echo (strpos($depts_html, "department.php?slug=aiml") !== false ? " [OK] Dynamic AIML link found\n" : " [FAIL] AIML link missing\n");
