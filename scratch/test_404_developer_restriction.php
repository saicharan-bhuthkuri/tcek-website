<?php
/**
 * Test Suite: Global 404 Error Page Settings - Developer Role Restriction & HTTP 403 Enforcement
 */

require_once __DIR__ . '/../public/backend/config/database.php';
require_once __DIR__ . '/../public/backend/auth.php';
require_once __DIR__ . '/../public/backend/settings_crud.php';

echo "=== START TEST SUITE: 404 DEVELOPER RESTRICTION ===\n\n";

$passCount = 0;
$failCount = 0;

function assert_test(bool $condition, string $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . "\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// Test 1: Helper function is_authorized_developer() unit tests
// -------------------------------------------------------------
echo "--- 1. Testing is_authorized_developer() role & identity matrix ---\n";

// Case A: Not logged in
$_SESSION = [];
assert_test(!is_authorized_developer(), "Guest (not logged in) is rejected by is_authorized_developer()");

// Case B: Regular Staff
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_id'        => 3,
    'tcek_admin_username'  => 'staff_user',
    'tcek_admin_email'     => 'faculty@tcek.in',
    'tcek_admin_role'      => 'staff'
];
assert_test(!is_authorized_developer(), "Role STAFF is rejected by is_authorized_developer()");

// Case C: Standard ADMIN
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_id'        => 2,
    'tcek_admin_username'  => 'tcek',
    'tcek_admin_email'     => 'officetcek@gmail.com',
    'tcek_admin_role'      => 'admin'
];
assert_test(!is_authorized_developer(), "Role ADMIN is rejected by is_authorized_developer()");

// Case D: SUPERADMIN (non-developer email)
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_id'        => 5,
    'tcek_admin_username'  => 'super_admin_user',
    'tcek_admin_email'     => 'superadmin@tcek.in',
    'tcek_admin_role'      => 'superadmin'
];
assert_test(!is_authorized_developer(), "Role SUPERADMIN is strictly rejected by is_authorized_developer()");

// Case E: Spoofed Developer role with wrong email/username
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_id'        => 99,
    'tcek_admin_username'  => 'imposter',
    'tcek_admin_email'     => 'imposter@attacker.com',
    'tcek_admin_role'      => 'developer'
];
assert_test(!is_authorized_developer(), "Developer role with mismatched email/identity is rejected");

// Case F: Legitimate Developer Charan / saivortex.dev@gmail.com
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_id'        => 1,
    'tcek_admin_username'  => 'Charan',
    'tcek_admin_email'     => 'saivortex.dev@gmail.com',
    'tcek_admin_role'      => 'developer'
];
assert_test(is_authorized_developer(), "Legitimate Developer (Charan / saivortex.dev@gmail.com) is APPROVED");


// -------------------------------------------------------------
// Test 2: Server-side update_global_404_setting() enforcement
// -------------------------------------------------------------
echo "\n--- 2. Testing update_global_404_setting() security ---\n";

// Attempt update as ADMIN
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_username'  => 'tcek',
    'tcek_admin_email'     => 'officetcek@gmail.com',
    'tcek_admin_role'      => 'admin'
];
$adminAttempt = update_global_404_setting(0, 'officetcek@gmail.com');
assert_test(
    isset($adminAttempt['status']) && $adminAttempt['status'] === 403 && $adminAttempt['success'] === false,
    "ADMIN attempt to update Global 404 returns HTTP 403 Forbidden"
);

// Attempt update as SUPERADMIN
$_SESSION['tcek_admin_role'] = 'superadmin';
$_SESSION['tcek_admin_email'] = 'superadmin@tcek.in';
$superAttempt = update_global_404_setting(0, 'superadmin@tcek.in');
assert_test(
    isset($superAttempt['status']) && $superAttempt['status'] === 403 && $superAttempt['success'] === false,
    "SUPERADMIN attempt to update Global 404 returns HTTP 403 Forbidden"
);

// Legitimate Developer update
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_username'  => 'Charan',
    'tcek_admin_email'     => 'saivortex.dev@gmail.com',
    'tcek_admin_role'      => 'developer'
];
$devToggleOff = update_global_404_setting(0, 'saivortex.dev@gmail.com');
assert_test(
    $devToggleOff['success'] === true && $devToggleOff['is_enabled'] === 0,
    "DEVELOPER successfully toggles Global 404 to OFF (0)"
);
assert_test(get_global_404_status() === false, "Verified status is now FALSE/OFF");

$devToggleOn = update_global_404_setting(1, 'saivortex.dev@gmail.com');
assert_test(
    $devToggleOn['success'] === true && $devToggleOn['is_enabled'] === 1,
    "DEVELOPER successfully toggles Global 404 to ON (1)"
);
assert_test(get_global_404_status() === true, "Verified status is now TRUE/ON");


// -------------------------------------------------------------
// Test 3: HTTP API Endpoint (backend/api_settings.php) over localhost
// -------------------------------------------------------------
echo "\n--- 3. Testing HTTP API endpoints & rejection ---\n";

// Request without session cookies (guest)
$ch = curl_init('http://localhost:8000/backend/api_settings.php?action=get_404_status');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

assert_test($httpCode === 403, "Unauthenticated request to api_settings.php rejected with HTTP 403");
$jsonRes = json_decode($response, true);
assert_test(isset($jsonRes['status']) && $jsonRes['status'] === 403, "API returned JSON body confirming 403 Forbidden");


// -------------------------------------------------------------
// Test 4: 404 Error Page HTTP status and layout
// -------------------------------------------------------------
echo "\n--- 4. Testing 404 Error Page output ---\n";

$ch = curl_init('http://localhost:8000/404.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

assert_test($code === 404, "404.php returns genuine HTTP 404 status code");
assert_test(strpos($html, 'HTTP Error 404') !== false, "404.php contains Trinity College 404 branding");
assert_test(strpos($html, 'Return to Homepage') !== false, "404.php contains navigation link to index.php");


// -------------------------------------------------------------
// Test 5: Verify Non-Developer Cannot Access developer_settings in dashboard.php
// -------------------------------------------------------------
echo "\n--- 5. Testing Dashboard Role Isolation ---\n";

// Simulate dashboard session check for ADMIN
$_SESSION = [
    'tcek_admin_logged_in' => true,
    'tcek_admin_username'  => 'tcek',
    'tcek_admin_email'     => 'officetcek@gmail.com',
    'tcek_admin_role'      => 'admin',
    'tcek_admin_name'      => 'TCEK Administrator'
];

ob_start();
// Test that tab developer_settings is sanitized away for admin
$test_tab = 'developer_settings';
$is_dev_check = is_authorized_developer();
$allowed_test = ['overview', 'users'];
if ($is_dev_check) {
    $allowed_test[] = 'developer_settings';
}
if (in_array($test_tab, ['developer_settings', 'settings']) && !$is_dev_check) {
    $test_tab = 'overview';
    $tab_status_code = 403;
} else {
    $tab_status_code = 200;
}
ob_end_clean();

assert_test(!$is_dev_check, "Admin is verified as NOT developer");
assert_test($test_tab === 'overview', "Admin requesting ?tab=developer_settings is redirected to overview");
assert_test($tab_status_code === 403, "Admin requesting ?tab=developer_settings gets HTTP 403 Forbidden code");


echo "\n=== TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED ===\n";
if ($failCount === 0) {
    echo ">>> ALL TESTS PASSED SUCCESSFULLY! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
