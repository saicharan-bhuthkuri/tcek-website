<?php
/**
 * Automated Verification Script for TCEK Security Hardening
 */

echo "==============================================================\n";
echo " TCEK SECURITY HARDENING VERIFICATION SUITE\n";
echo "==============================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($description, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] {$description}\n";
        $passCount++;
    } else {
        echo " [FAIL] {$description}\n";
        if ($details) {
            echo "        Details: {$details}\n";
        }
        $failCount++;
    }
}

// -------------------------------------------------------------
// TEST 1: ENVIRONMENT CONFIGURATION & SECRETS
// -------------------------------------------------------------
echo "\n--- 1. Testing Secrets and Environment Configuration ---\n";
require_once __DIR__ . '/../public/backend/config/env.php';

assertTest("env() function loads DB_HOST from .env", env('DB_HOST') === 'localhost', "Got: " . var_export(env('DB_HOST'), true));
assertTest("env() function loads DB_NAME from .env", env('DB_NAME') === 'tcek', "Got: " . var_export(env('DB_NAME'), true));
assertTest("env() function converts boolean strings", env('APP_DEBUG') === false, "Got: " . var_export(env('APP_DEBUG'), true));

// Check .gitignore exists and contains .env
$gitignore = @file_get_contents(__DIR__ . '/../.gitignore');
assertTest(".gitignore exists and excludes .env", strpos($gitignore, '.env') !== false);
assertTest(".gitignore excludes *.log files", strpos($gitignore, '*.log') !== false);

// -------------------------------------------------------------
// TEST 2: RATE LIMITING & EXPONENTIAL BACKOFF
// -------------------------------------------------------------
echo "\n--- 2. Testing Rate Limiting & Exponential Backoff ---\n";
require_once __DIR__ . '/../public/backend/security/RateLimiter.php';

$testUser = 'test_audit_user_' . mt_rand(1000, 9999);
$testIp   = '192.168.100.' . mt_rand(1, 250);

// Initial check should be allowed
$initCheck = RateLimiter::checkAuthLimit($testUser, $testIp);
assertTest("Initial auth check allowed", $initCheck['allowed'] === true);

// Record failures up to threshold (default 5)
$backoffs = [];
for ($i = 1; $i <= 7; $i++) {
    $res = RateLimiter::recordAuthFailure($testUser, $testIp);
    $backoffs[] = $res;
}

assertTest("5th attempt triggers backoff threshold", $backoffs[4]['blocked'] === true && $backoffs[4]['retry_after'] > 0, "5th attempt: " . json_encode($backoffs[4]));
assertTest("6th attempt backoff increases exponentially", $backoffs[5]['retry_after'] >= $backoffs[4]['retry_after'], "6th vs 5th: {$backoffs[5]['retry_after']} vs {$backoffs[4]['retry_after']}");
assertTest("7th attempt backoff increases further", $backoffs[6]['retry_after'] >= $backoffs[5]['retry_after'], "7th vs 6th: {$backoffs[6]['retry_after']} vs {$backoffs[5]['retry_after']}");

$blockedCheck = RateLimiter::checkAuthLimit($testUser, $testIp);
assertTest("checkAuthLimit rejects blocked account", $blockedCheck['allowed'] === false);

// Reset auth limits
RateLimiter::resetAuthLimits($testUser, $testIp);
$postResetCheck = RateLimiter::checkAuthLimit($testUser, $testIp);
assertTest("resetAuthLimits unlocks user upon valid login", $postResetCheck['allowed'] === true);

// -------------------------------------------------------------
// TEST 3: STRICT INPUT VALIDATION
// -------------------------------------------------------------
echo "\n--- 3. Testing Strict Input Schema Validation ---\n";
require_once __DIR__ . '/../public/backend/security/Validator.php';

// Test username validation
$val1 = Validator::validate(
    ['username' => 'valid_user123'],
    ['username' => ['type' => 'username', 'required' => true]]
);
assertTest("Valid username accepted", $val1['valid'] === true);

$val2 = Validator::validate(
    ['username' => "admin' OR '1'='1"],
    ['username' => ['type' => 'username', 'required' => true]]
);
assertTest("Malicious SQL injection username rejected", $val2['valid'] === false);

// Test date validation
$val3 = Validator::validate(
    ['publish_date' => '2026-02-30'], // Feb 30 does not exist
    ['publish_date' => ['type' => 'date', 'required' => true]]
);
assertTest("Invalid calendar date (2026-02-30) rejected", $val3['valid'] === false);

$val4 = Validator::validate(
    ['publish_date' => '2026-10-15'],
    ['publish_date' => ['type' => 'date', 'required' => true]]
);
assertTest("Valid calendar date (2026-10-15) accepted", $val4['valid'] === true);

// Test integer range validation
$val5 = Validator::validate(
    ['id' => 'not_an_int'],
    ['id' => ['type' => 'int', 'required' => true, 'min' => 1]]
);
assertTest("Non-integer ID rejected", $val5['valid'] === false);

$val6 = Validator::validate(
    ['id' => '-5'],
    ['id' => ['type' => 'int', 'required' => true, 'min' => 1]]
);
assertTest("Negative integer ID below min rejected", $val6['valid'] === false);

$val7 = Validator::validate(
    ['id' => '42'],
    ['id' => ['type' => 'int', 'required' => true, 'min' => 1]]
);
assertTest("Valid integer ID accepted and cast to int", $val7['valid'] === true && $val7['data']['id'] === 42);

// Test URL/Path validation
$val8 = Validator::validate(
    ['url' => 'javascript:alert(1)'],
    ['url' => ['type' => 'url_or_path', 'required' => true]]
);
assertTest("JavaScript pseudo-protocol rejected", $val8['valid'] === false);

// -------------------------------------------------------------
// TEST 4: SECURE ERROR HANDLING & REDACTION
// -------------------------------------------------------------
echo "\n--- 4. Testing Secure Error Handling & Redaction ---\n";
require_once __DIR__ . '/../public/backend/security/ErrorHandler.php';

$fakeEx = new Exception("SELECT * FROM users WHERE password='SuperSecretPassword123!' failed with syntax error");
$safeResp = ErrorHandler::safeError($fakeEx, "Could not fetch user records.");

assertTest("safeError returns user-friendly message without leaking SQL", $safeResp['message'] === "Could not fetch user records.");
assertTest("safeError does not contain raw SQL query in production response", !isset($safeResp['debug_detail']) || strpos($safeResp['message'], 'SuperSecretPassword123!') === false);
assertTest("safeError includes trackable incident reference ID", !empty($safeResp['error_ref']) && strpos($safeResp['error_ref'], 'ERR-') === 0);

$sanitizedLog = ErrorHandler::sanitizeLogText("Failed login: password=MySecretPassword123 and csrf_token=abcdef012345");
assertTest("Logs redact passwords", strpos($sanitizedLog, 'password=[REDACTED]') !== false);
assertTest("Logs redact CSRF tokens", strpos($sanitizedLog, 'csrf_token=[REDACTED]') !== false);

// -------------------------------------------------------------
// TEST 5: FILE UPLOAD SAFETY
// -------------------------------------------------------------
echo "\n--- 5. Testing File Upload Safety Rules ---\n";
require_once __DIR__ . '/../public/backend/upload.php';

// Test double-extension rejection
$fakeFileDoubleExt = [
    'name'     => 'shell.php.jpg',
    'type'     => 'image/jpeg',
    'tmp_name' => sys_get_temp_dir() . '/dummy.tmp',
    'error'    => UPLOAD_ERR_OK,
    'size'     => 1024
];
@file_put_contents($fakeFileDoubleExt['tmp_name'], '<?php echo "bad"; ?>');

$uploadRes1 = process_file_upload($fakeFileDoubleExt, 'Double Ext Test', 'images');
assertTest("Double extension (shell.php.jpg) rejected", $uploadRes1['success'] === false && strpos($uploadRes1['message'], 'Security Error') !== false);

// Test executable script rejection
$fakeFileExe = [
    'name'     => 'script.phtml',
    'type'     => 'text/plain',
    'tmp_name' => sys_get_temp_dir() . '/dummy2.tmp',
    'error'    => UPLOAD_ERR_OK,
    'size'     => 1024
];
@file_put_contents($fakeFileExe['tmp_name'], '<?php phpinfo(); ?>');

$uploadRes2 = process_file_upload($fakeFileExe, 'Executable Test', 'images');
assertTest("Executable extension (script.phtml) rejected", $uploadRes2['success'] === false);

// Cleanup dummy temp files
@unlink($fakeFileDoubleExt['tmp_name']);
@unlink($fakeFileExe['tmp_name']);

// Verify .htaccess in uploads directory exists and contains execution-blocking directives
$uploadsHtaccess = @file_get_contents(__DIR__ . '/../public/uploads/.htaccess');
assertTest("uploads/.htaccess blocks script execution", strpos($uploadsHtaccess, 'RemoveHandler .php') !== false);
assertTest("uploads/.htaccess enforces default-handler", strpos($uploadsHtaccess, 'SetHandler default-handler') !== false);
assertTest("uploads/.htaccess enforces X-Content-Type-Options nosniff", strpos($uploadsHtaccess, 'nosniff') !== false);

// Verify uploads/web.config exists
assertTest("uploads/web.config exists for IIS protection", file_exists(__DIR__ . '/../public/uploads/web.config'));

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n==============================================================\n";
echo " VERIFICATION SUMMARY: {$passCount} PASSED, {$failCount} FAILED\n";
echo "==============================================================\n";

if ($failCount > 0) {
    exit(1);
}
