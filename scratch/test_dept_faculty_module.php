<?php
/**
 * Comprehensive Test Suite for Department & Faculty Management Module
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/public/backend/crud.php';

$passed = 0;
$failed = 0;

function assert_test($condition, $name) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $name\n";
        $passed++;
    } else {
        echo "  [FAIL] $name\n";
        $failed++;
    }
}

echo "=======================================================\n";
echo "1. TESTING DEPARTMENT OPERATIONS\n";
echo "=======================================================\n";

$allDepts = get_departments(false);
assert_test(count($allDepts) >= 10, "Default departments loaded (count: " . count($allDepts) . ")");

$cse = get_department_by_code('CSE');
assert_test($cse !== null && $cse['name'] === 'Computer Science & Engineering', "Retrieved CSE department correctly");

// Test add new department
$newDeptCode = 'TEST_ROBO';
$addRes = add_department([
    'dept_code' => $newDeptCode,
    'name' => 'Robotics & Automation',
    'degree_level' => 'B.Tech',
    'intake' => '60 Seats',
    'duration' => '4 Years',
    'established_year' => 2026,
    'icon_class' => 'fas fa-robot',
    'theme_class' => 'theme-cse',
    'description' => 'Advanced Robotics and AI engineering',
    'vision' => 'Pioneering intelligent automation',
    'mission' => 'Empowering next-gen roboticists',
    'display_order' => 99,
    'is_active' => 1
]);
assert_test($addRes['success'] === true, "Successfully added new department: $newDeptCode");

// Test duplicate code prevention
$dupRes = add_department([
    'dept_code' => $newDeptCode,
    'name' => 'Duplicate Robotics',
    'degree_level' => 'B.Tech'
]);
assert_test($dupRes['success'] === false, "Prevented duplicate department code registration");

// Test update department
$roboDept = get_department_by_code($newDeptCode);
$updRes = update_department($roboDept['id'], [
    'dept_code' => $newDeptCode,
    'name' => 'Robotics & Industrial Automation',
    'degree_level' => 'B.Tech',
    'intake' => '120 Seats',
    'duration' => '4 Years',
    'established_year' => 2026,
    'is_active' => 1
]);
assert_test($updRes['success'] === true, "Updated department details successfully");
$roboDeptUpdated = get_department_by_code($newDeptCode);
assert_test($roboDeptUpdated['name'] === 'Robotics & Industrial Automation', "Verified updated name in store");

echo "\n=======================================================\n";
echo "2. TESTING FACULTY ROLES & HOD EXCLUSIVITY\n";
echo "=======================================================\n";

$validRoles = get_valid_faculty_roles();
assert_test(count($validRoles) === 6, "Supported exactly 6 required designation categories");
assert_test(isset($validRoles['HOD']), "Role: HOD supported");
assert_test(isset($validRoles['Faculty – Junior (JR)']), "Role: Faculty – Junior (JR) supported");
assert_test(isset($validRoles['Faculty – Senior (SR)']), "Role: Faculty – Senior (SR) supported");
assert_test(isset($validRoles['R&D']), "Role: R&D supported");
assert_test(isset($validRoles['R&D + Senior Faculty (SR)']), "Role: R&D + Senior Faculty (SR) supported");
assert_test(isset($validRoles['R&D + Junior Faculty (JR)']), "Role: R&D + Junior Faculty (JR) supported");

// Add Faculty 1 (Initial HOD)
$fac1Res = add_faculty_member([
    'full_name' => 'Dr. Alan Turing',
    'dept_code' => $newDeptCode,
    'role_category' => 'HOD',
    'designation' => 'Professor & Head of Department',
    'qualification' => 'Ph.D (Cambridge)',
    'experience' => '20 Years',
    'jntuh_reg_id' => '1234-567890-001',
    'email' => 'alan.turing@tcek.in',
    'is_hod' => 1,
    'is_rnd' => 1,
    'is_active' => 1
]);
assert_test($fac1Res['success'] === true, "Added initial HOD Dr. Alan Turing");
$fac1Id = $fac1Res['id'];

// Verify HOD of department is Fac1
$hod1 = get_department_hod($newDeptCode);
assert_test($hod1 !== null && $hod1['id'] == $fac1Id, "Dr. Alan Turing verified as active HOD");

// Add Faculty 2 as R&D + Senior Faculty
$fac2Res = add_faculty_member([
    'full_name' => 'Dr. Ada Lovelace',
    'dept_code' => $newDeptCode,
    'role_category' => 'R&D + Senior Faculty (SR)',
    'designation' => 'Associate Professor',
    'qualification' => 'M.Tech, Ph.D',
    'experience' => '12 Years',
    'jntuh_reg_id' => '1234-567890-002',
    'email' => 'ada.lovelace@tcek.in',
    'is_hod' => 0,
    'is_rnd' => 1,
    'is_active' => 1
]);
assert_test($fac2Res['success'] === true, "Added Faculty 2: Dr. Ada Lovelace (R&D + SR)");
$fac2Id = $fac2Res['id'];

// Test Single-Active HOD enforcement: Promote Faculty 2 to HOD
$assignHodRes = assign_department_hod($newDeptCode, $fac2Id, 'Professor & Head of Department');
assert_test($assignHodRes['success'] === true, "Assigned Dr. Ada Lovelace as new HOD");

// Verify Dr. Ada Lovelace is now HOD, and Dr. Alan Turing was demoted to Senior Faculty
$newHod = get_department_hod($newDeptCode);
assert_test($newHod !== null && $newHod['id'] == $fac2Id, "Dr. Ada Lovelace is now active HOD");

$demotedFac1 = get_faculty_by_id($fac1Id);
assert_test(empty($demotedFac1['is_hod']) && $demotedFac1['role_category'] === 'Faculty – Senior (SR)', 
    "Prior HOD Dr. Alan Turing was automatically demoted to Senior Faculty (is_hod=0)");

// Add Faculty 3 as Junior Faculty with formula injection probe in name
$fac3Res = add_faculty_member([
    'full_name' => '=CMD()|Malicious Formula',
    'dept_code' => $newDeptCode,
    'role_category' => 'Faculty – Junior (JR)',
    'designation' => 'Assistant Professor',
    'qualification' => 'M.Tech',
    'experience' => '3 Years',
    'is_active' => 1
]);
assert_test($fac3Res['success'] === true, "Added Faculty 3 with formula injection probe");
$fac3Id = $fac3Res['id'];

echo "\n=======================================================\n";
echo "3. TESTING SEARCH & MULTI-FACTOR FILTERS\n";
echo "=======================================================\n";

// Department filter
$deptFiltered = get_faculty_members(['dept_code' => $newDeptCode]);
assert_test(count($deptFiltered) === 3, "Filtered by department $newDeptCode (found: " . count($deptFiltered) . ")");

// Search by name
$searchFiltered = get_faculty_members(['dept_code' => $newDeptCode, 'search' => 'Lovelace']);
assert_test(count($searchFiltered) === 1 && $searchFiltered[0]['id'] == $fac2Id, "Search by name returned Lovelace");

// Filter by HOD status
$hodOnly = get_faculty_members(['dept_code' => $newDeptCode, 'is_hod' => '1']);
assert_test(count($hodOnly) === 1 && $hodOnly[0]['id'] == $fac2Id, "Filter by is_hod='1' returned only current HOD");

// Filter by R&D affiliation
$rndOnly = get_faculty_members(['dept_code' => $newDeptCode, 'is_rnd' => '1']);
assert_test(count($rndOnly) === 2, "Filter by is_rnd='1' returned 2 R&D affiliated faculty");

echo "\n=======================================================\n";
echo "4. TESTING DEPARTMENT DELETION GUARD\n";
echo "=======================================================\n";

// Attempt to delete department while faculty exist
$delDeptFail = delete_department($roboDept['id']);
assert_test($delDeptFail['success'] === false && strpos($delDeptFail['message'], 'Cannot delete department') !== false,
    "Blocked department deletion due to associated faculty records");

// Clean up faculty members
delete_faculty_member($fac1Id);
delete_faculty_member($fac2Id);
delete_faculty_member($fac3Id);
$countAfterDel = count_department_faculty($newDeptCode);
assert_test($countAfterDel === 0, "All test faculty deleted, count is now 0");

// Now department deletion must succeed
$delDeptSuccess = delete_department($roboDept['id']);
assert_test($delDeptSuccess['success'] === true, "Department deletion succeeded after faculty cleared");

echo "\n=======================================================\n";
echo "5. TESTING EXCEL EXPORT & FORMULA INJECTION NEUTRALIZATION\n";
echo "=======================================================\n";

// Test export generation for CSE
$excelContent = generate_faculty_excel_xml(['dept_code' => 'CSE']);
assert_test(!empty($excelContent), "Generated Excel SpreadsheetML XML");
assert_test(strpos($excelContent, 'Computer Science &amp; Engineering') !== false || strpos($excelContent, 'Computer Science') !== false, "Excel contains CSE faculty data");

// Test formula neutralization helper
$escapedFormula = sanitize_excel_cell('=SUM(1,2)');
assert_test($escapedFormula === "'=SUM(1,2)", "Escaped leading '=' formula: $escapedFormula");

$escapedPlus = sanitize_excel_cell('+12345');
assert_test($escapedPlus === "'+12345", "Escaped leading '+' formula: $escapedPlus");

$escapedAt = sanitize_excel_cell('@secret');
assert_test($escapedAt === "'@secret", "Escaped leading '@' formula: $escapedAt");

echo "\n=======================================================\n";
echo "6. TESTING PUBLIC SITE LIVE SYNCHRONIZATION\n";
echo "=======================================================\n";

// Render departments.php output buffer check
ob_start();
include $rootDir . '/public/departments.php';
$deptPageHtml = ob_get_clean();
assert_test(strpos($deptPageHtml, 'Computer Science &amp; Engineering') !== false, "public/departments.php renders active departments");
assert_test(strpos($deptPageHtml, 'Polytechnic Diploma') !== false, "public/departments.php renders diploma tab");
assert_test(strpos($deptPageHtml, 'MBA (Postgraduate)') !== false, "public/departments.php renders MBA tab");

// Render dept-cse.php output buffer check
ob_start();
include $rootDir . '/public/dept-cse.php';
$csePageHtml = ob_get_clean();
assert_test(strpos($csePageHtml, 'Head of the Department') !== false, "dept-cse.php renders HOD section");
assert_test(strpos($csePageHtml, 'Faculty Directory') !== false, "dept-cse.php renders Faculty Directory");
assert_test(strpos($csePageHtml, 'SWATHI JILLA') !== false, "dept-cse.php renders live faculty SWATHI JILLA");

// Render dept-ece.php output buffer check
ob_start();
include $rootDir . '/public/dept-ece.php';
$ecePageHtml = ob_get_clean();
assert_test(strpos($ecePageHtml, 'PRABHAKAR PARLAPALLI') !== false, "dept-ece.php renders live HOD PRABHAKAR PARLAPALLI");

// Render dept-eee.php output buffer check
ob_start();
include $rootDir . '/public/dept-eee.php';
$eeePageHtml = ob_get_clean();
assert_test(strpos($eeePageHtml, 'Dr. K. NATARAJAN') !== false, "dept-eee.php renders live HOD Dr. K. NATARAJAN");

// Render dept-aiml.php output buffer check
ob_start();
include $rootDir . '/public/dept-aiml.php';
$aimlPageHtml = ob_get_clean();
assert_test(strpos($aimlPageHtml, 'GADDAM LAKSHMI') !== false, "dept-aiml.php renders live HOD GADDAM LAKSHMI");

// Render dept-mba.php output buffer check
ob_start();
include $rootDir . '/public/dept-mba.php';
$mbaPageHtml = ob_get_clean();
assert_test(strpos($mbaPageHtml, 'Dr. ARIF ARFAT') !== false, "dept-mba.php renders live HOD Dr. ARIF ARFAT");

echo "\n=======================================================\n";
echo "TEST RESULTS SUMMARY: $passed PASSED, $failed FAILED\n";
echo "=======================================================\n";

if ($failed === 0) {
    echo "ALL TESTS PASSED WITH 100% SUCCESS!\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
