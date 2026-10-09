<?php
/**
 * Verification of User's Exact Department Management & Navigation Requirements
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/public/backend/crud.php';

$passed = 0;
$failed = 0;

function check($cond, $title) {
    global $passed, $failed;
    if ($cond) {
        echo "  [PASS] $title\n";
        $passed++;
    } else {
        echo "  [FAIL] $title\n";
        $failed++;
    }
}

echo "=======================================================\n";
echo "TEST 1: Dynamic Linking on departments.php\n";
echo "=======================================================\n";

ob_start();
include $rootDir . '/public/departments.php';
$htmlDepartments = ob_get_clean();

check(strpos($htmlDepartments, 'department.php?slug=cse') !== false, "departments.php links CSE to department.php?slug=cse");
check(strpos($htmlDepartments, 'department.php?slug=ece') !== false, "departments.php links ECE to department.php?slug=ece");
check(strpos($htmlDepartments, 'department.php?slug=aiml') !== false, "departments.php links AIML to department.php?slug=aiml");
check(strpos($htmlDepartments, 'department.php?slug=mba') !== false, "departments.php links MBA to department.php?slug=mba");

echo "\n=======================================================\n";
echo "TEST 2: Dynamic Department Detail Page (department.php)\n";
echo "=======================================================\n";

// Test rendering AIML via department.php?slug=aiml
$_GET = ['slug' => 'aiml'];
ob_start();
include $rootDir . '/public/department.php';
$htmlAiml = ob_get_clean();

check(strpos($htmlAiml, 'Artificial Intelligence &amp; Machine Learning') !== false || strpos($htmlAiml, 'Artificial Intelligence & Machine Learning') !== false, "department.php?slug=aiml renders AIML title");
check(strpos($htmlAiml, 'GADDAM LAKSHMI') !== false, "department.php?slug=aiml renders HOD GADDAM LAKSHMI");
check(strpos($htmlAiml, 'Faculty Directory') !== false, "department.php?slug=aiml renders Faculty Directory");

// Test rendering CSE via department.php?slug=cse
$_GET = ['slug' => 'cse'];
ob_start();
include $rootDir . '/public/department.php';
$htmlCse = ob_get_clean();

check(strpos($htmlCse, 'Computer Science &amp; Engineering') !== false || strpos($htmlCse, 'Computer Science & Engineering') !== false, "department.php?slug=cse renders CSE title");
check(strpos($htmlCse, 'SWATHI JILLA') !== false, "department.php?slug=cse renders faculty member SWATHI JILLA");

echo "\n=======================================================\n";
echo "TEST 3: Creating a NEW Department without any new PHP file\n";
echo "=======================================================\n";

$newCode = 'CSB';
$newSlug = 'csb';
$newName = 'Cyber Security & Blockchain';

// Ensure clean slate
$existing = get_department_by_code($newCode);
if ($existing) {
    delete_department($existing['id']);
}

$createRes = add_department([
    'dept_code' => $newCode,
    'name' => $newName,
    'slug' => $newSlug,
    'degree_level' => 'B.Tech',
    'intake' => '60 Seats',
    'duration' => '4 Years',
    'established_year' => 2026,
    'icon_class' => 'fas fa-shield-alt',
    'theme_class' => 'theme-cse',
    'description' => 'Cutting-edge program in offensive cyber security and blockchain infrastructure.',
    'vision' => 'To be a premier global center in cyber threat intelligence and decentralized cryptography.',
    'mission' => "1. Train ethical hackers and security researchers.\n2. Develop secure blockchain distributed applications.\n3. Build cyber resilience through hands-on red team labs.",
    'display_order' => 15,
    'is_active' => 1
]);

check($createRes['success'] === true, "Successfully created department $newCode via backend");
$deptRecord = get_department_by_slug($newSlug);
check($deptRecord !== null && $deptRecord['dept_code'] === $newCode, "Retrieved new department by slug '$newSlug'");

// Check if new department automatically appears on public departments.php
$_GET = [];
ob_start();
include $rootDir . '/public/departments.php';
$htmlWithNew = ob_get_clean();

check(strpos($htmlWithNew, 'Cyber Security &amp; Blockchain') !== false || strpos($htmlWithNew, 'Cyber Security & Blockchain') !== false, "departments.php automatically displays new department $newCode");
check(strpos($htmlWithNew, 'department.php?slug=csb') !== false, "departments.php has link department.php?slug=csb for the new department");

// Check if opening the new department detail page works dynamically without creating any new file
$_GET = ['slug' => 'csb'];
ob_start();
include $rootDir . '/public/department.php';
$htmlNewDeptPage = ob_get_clean();

check(strpos($htmlNewDeptPage, 'Cyber Security &amp; Blockchain') !== false || strpos($htmlNewDeptPage, 'Cyber Security & Blockchain') !== false, "department.php?slug=csb renders title");
check(strpos($htmlNewDeptPage, 'Cutting-edge program in offensive cyber security') !== false, "department.php?slug=csb renders description");
check(strpos($htmlNewDeptPage, 'Train ethical hackers') !== false, "department.php?slug=csb renders mission points");

echo "\n=======================================================\n";
echo "TEST 4: Adding HOD & Faculty to NEW Department\n";
echo "=======================================================\n";

$hodRes = add_faculty_member([
    'full_name' => 'Dr. Linus Torvalds',
    'dept_code' => $newCode,
    'role_category' => 'HOD',
    'designation' => 'Professor & Head of Department',
    'qualification' => 'M.S., Ph.D (Helsinki)',
    'experience' => '25 Years',
    'jntuh_reg_id' => '9988-776655-001',
    'email' => 'linus.torvalds@tcek.in',
    'is_hod' => 1,
    'is_rnd' => 1,
    'is_active' => 1
]);
check($hodRes['success'] === true, "Added HOD Dr. Linus Torvalds to new department");

$facRes = add_faculty_member([
    'full_name' => 'Margaret Hamilton',
    'dept_code' => $newCode,
    'role_category' => 'Faculty – Senior (SR)',
    'designation' => 'Associate Professor',
    'qualification' => 'M.Tech (MIT)',
    'experience' => '15 Years',
    'jntuh_reg_id' => '9988-776655-002',
    'email' => 'margaret.hamilton@tcek.in',
    'is_hod' => 0,
    'is_rnd' => 1,
    'is_active' => 1
]);
check($facRes['success'] === true, "Added Faculty Margaret Hamilton to new department");

// Verify that the new department page now renders the HOD and Faculty automatically
$_GET = ['slug' => 'csb'];
ob_start();
include $rootDir . '/public/department.php';
$htmlWithStaff = ob_get_clean();

check(strpos($htmlWithStaff, 'Dr. Linus Torvalds') !== false, "department.php?slug=csb renders new HOD Dr. Linus Torvalds");
check(strpos($htmlWithStaff, 'Margaret Hamilton') !== false, "department.php?slug=csb renders new faculty Margaret Hamilton in directory");

echo "\n=======================================================\n";
echo "TEST 5: Updating Department in Dashboard Reflects Live\n";
echo "=======================================================\n";

$updatedName = 'Cyber Security & Digital Forensics';
$updRes = update_department($deptRecord['id'], [
    'dept_code' => $newCode,
    'name' => $updatedName,
    'slug' => $newSlug,
    'degree_level' => 'B.Tech',
    'intake' => '120 Seats',
    'duration' => '4 Years',
    'is_active' => 1
]);
check($updRes['success'] === true, "Updated department name and intake");

$_GET = ['slug' => 'csb'];
ob_start();
include $rootDir . '/public/department.php';
$htmlUpdated = ob_get_clean();
check(strpos($htmlUpdated, 'Cyber Security &amp; Digital Forensics') !== false || strpos($htmlUpdated, 'Cyber Security & Digital Forensics') !== false, "department.php?slug=csb immediately reflects updated name");

echo "\n=======================================================\n";
echo "TEST 6: Deletion Guard & Cleanup\n";
echo "=======================================================\n";

// Deleting department with active faculty must be blocked
$delBlocked = delete_department($deptRecord['id']);
check($delBlocked['success'] === false, "Prevented deletion of department while faculty exist");

// Delete faculty members first
delete_faculty_member($hodRes['id']);
delete_faculty_member($facRes['id']);

// Now delete department
$delAllowed = delete_department($deptRecord['id']);
check($delAllowed['success'] === true, "Successfully deleted department after clearing faculty");

// Verify department no longer appears on departments.php
$_GET = [];
ob_start();
include $rootDir . '/public/departments.php';
$htmlAfterDel = ob_get_clean();
check(strpos($htmlAfterDel, 'Cyber Security &amp; Digital Forensics') === false && strpos($htmlAfterDel, 'Cyber Security & Digital Forensics') === false, "Deleted department no longer appears on departments.php");

echo "\n=======================================================\n";
echo "TEST 7: Backward Compatibility for Legacy URLs\n";
echo "=======================================================\n";

// Test legacy dept-aiml.php
$_GET = [];
ob_start();
include $rootDir . '/public/dept-aiml.php';
$htmlLegacyAiml = ob_get_clean();
check(strpos($htmlLegacyAiml, 'GADDAM LAKSHMI') !== false, "Legacy dept-aiml.php delegates and renders live HOD");

// Test legacy dept-cse.php
$_GET = [];
ob_start();
include $rootDir . '/public/dept-cse.php';
$htmlLegacyCse = ob_get_clean();
check(strpos($htmlLegacyCse, 'SWATHI JILLA') !== false, "Legacy dept-cse.php delegates and renders live faculty");

echo "\n=======================================================\n";
echo "SUMMARY: $passed PASSED, $failed FAILED\n";
echo "=======================================================\n";

if ($failed === 0) {
    echo "ALL USER REQUIREMENTS VERIFIED SUCCESSFULLY WITH 100% PASS!\n";
    exit(0);
} else {
    echo "SOME CHECKS FAILED!\n";
    exit(1);
}
