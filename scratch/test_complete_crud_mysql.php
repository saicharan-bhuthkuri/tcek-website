<?php
/**
 * Comprehensive Pure MySQL CRUD Verification Test Suite for TCEK
 * Validates all CRUD operations against MySQL for all 12 modules.
 */

define('ALLOW_INC', true);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SESSION['user'] = 'test_admin';

require_once __DIR__ . '/../public/backend/config/database.php';
require_once __DIR__ . '/../public/backend/sync.php';
require_once __DIR__ . '/../public/backend/crud.php';
require_once __DIR__ . '/../public/backend/announcements_crud.php';
require_once __DIR__ . '/../public/backend/hero_crud.php';
require_once __DIR__ . '/../public/backend/rnd_crud.php';
require_once __DIR__ . '/../public/backend/departments_crud.php';
require_once __DIR__ . '/../public/backend/settings_crud.php';

$pdo = getDB();
$results = [];

/**
 * @param string $label
 * @param bool $condition
 * @param array $results
 * @return void
 */
function assert_test(string $label, bool $condition, array &$results): void {
    if ($condition) {
        $results[] = ["PASS", $label];
        echo " [PASS] $label\n";
    } else {
        $results[] = ["FAIL", $label];
        echo " [FAIL] $label\n";
    }
}

echo "==============================================================\n";
echo "Starting Pure MySQL CRUD Test Suite\n";
echo "==============================================================\n\n";

// -------------------------------------------------------------
// 1. Users CRUD
// -------------------------------------------------------------
echo "1. Testing Users CRUD...\n";
$u_name = 'testuser_' . time();
$add_u_res = add_user($u_name, 'secret123', 'Test User Unit', 'testuser@example.com', 'editor');
assert_test("Users - Create User in MySQL", !empty($add_u_res['success']), $results);
$u_id = $add_u_res['id'] ?? null;

$get_u = get_user_by_id($u_id);
assert_test("Users - Read User by ID from MySQL", $get_u && $get_u['username'] === $u_name, $results);

$update_u_res = update_user($u_id, ['full_name' => 'Test User Updated', 'role' => 'staff']);
assert_test("Users - Update User in MySQL", !empty($update_u_res['success']), $results);

$get_u_updated = get_user_by_id($u_id);
assert_test("Users - Verify Updated Profile in MySQL", $get_u_updated && $get_u_updated['full_name'] === 'Test User Updated', $results);

$del_u_res = delete_user($u_id);
assert_test("Users - Delete User from MySQL", !empty($del_u_res['success']), $results);

$get_u_deleted = get_user_by_id($u_id);
assert_test("Users - Verify User Deletion in MySQL", $get_u_deleted === null, $results);

// -------------------------------------------------------------
// 2. Notifications & Circulars CRUD
// -------------------------------------------------------------
echo "\n2. Testing Notifications & Circulars CRUD...\n";
$notif_title = 'Test Circular ' . time();
$add_n_res = add_notification(
    $notif_title,
    'Examinations',
    'Autonomous timetable release',
    'pdf',
    'uploads/test.pdf',
    'https://tcek.in/exam',
    1,
    date('Y-m-d')
);
assert_test("Notifications - Create Notification in MySQL", !empty($add_n_res['success']), $results);
$n_id = $add_n_res['id'] ?? null;

$get_n = get_notification_by_id($n_id);
assert_test("Notifications - Read Notification from MySQL", $get_n && $get_n['title'] === $notif_title, $results);

$update_n_res = update_notification(
    $n_id,
    'Updated Circular ' . time(),
    date('Y-m-d'),
    'Updated examination notes',
    null,
    'Academic',
    null,
    0
);
assert_test("Notifications - Update Notification in MySQL", !empty($update_n_res['success']), $results);

$del_n_res = delete_notification($n_id);
assert_test("Notifications - Delete Notification from MySQL", !empty($del_n_res['success']), $results);

$get_n_deleted = get_notification_by_id($n_id);
assert_test("Notifications - Verify Deletion in MySQL", $get_n_deleted === null, $results);

// -------------------------------------------------------------
// 3. Events & Event Media CRUD
// -------------------------------------------------------------
echo "\n3. Testing Events & Event Media CRUD...\n";
$event_title = 'Test Tech Symposium ' . time();
$add_e_res = add_event(
    $event_title,
    date('Y-m-d', strtotime('+5 days')),
    '10:30 AM',
    'Annual symposium on computing',
    'Auditorium',
    'assets/events/tech.jpg',
    null,
    1
);
assert_test("Events - Create Event in MySQL", !empty($add_e_res['success']), $results);
$e_id = $add_e_res['id'] ?? null;

$get_e = get_event_by_id($e_id);
assert_test("Events - Read Event by ID from MySQL", $get_e && $get_e['title'] === $event_title, $results);

$update_e_res = update_event(
    $e_id,
    $event_title . ' [Updated]',
    date('Y-m-d', strtotime('+6 days')),
    '11:00 AM',
    'Seminar Hall',
    'Updated description'
);
assert_test("Events - Update Event in MySQL", !empty($update_e_res['success']), $results);

// Add event media
$add_m_res = add_event_media($e_id, 'assets/events/photo1.jpg', 'Keynote Speech', 'Keynote address by guest');
assert_test("Event Media - Create Event Media in MySQL", !empty($add_m_res['success']), $results);
$m_id = $add_m_res['id'] ?? null;

$all_media = get_all_event_media(10);
$found_m = false;
foreach ($all_media as $m) {
    if ((int)$m['id'] === (int)$m_id) { $found_m = true; break; }
}
assert_test("Event Media - Read Event Media from MySQL", $found_m, $results);

$del_m_res = delete_event_media($m_id);
assert_test("Event Media - Delete Event Media from MySQL", !empty($del_m_res['success']), $results);

$del_e_res = delete_event($e_id);
assert_test("Events - Delete Event from MySQL", !empty($del_e_res['success']), $results);

$get_e_deleted = get_event_by_id($e_id);
assert_test("Events - Verify Deletion in MySQL", $get_e_deleted === null, $results);

// -------------------------------------------------------------
// 4. News & Press Clippings CRUD
// -------------------------------------------------------------
echo "\n4. Testing News & Press Clippings CRUD...\n";
$news_title = 'Test Press Release ' . time();
$add_news_res = add_news($news_title, date('Y-m-d'), 'TCEK partners with tech giant', 'assets/news/press.jpg', 'Deccan Chronicle');
assert_test("News - Create News Item in MySQL", !empty($add_news_res['success']), $results);
$news_id = $add_news_res['id'] ?? null;

$get_news = get_news_by_id($news_id);
assert_test("News - Read News by ID from MySQL", $get_news && $get_news['title'] === $news_title, $results);

$update_news_res = update_news($news_id, 'Updated Press Release', date('Y-m-d'), 'Updated press details', null, 'Eenadu');
assert_test("News - Update News in MySQL", !empty($update_news_res['success']), $results);

$del_news_res = delete_news($news_id);
assert_test("News - Delete News from MySQL", !empty($del_news_res['success']), $results);

$get_news_deleted = get_news_by_id($news_id);
assert_test("News - Verify News Deletion in MySQL", $get_news_deleted === null, $results);

// -------------------------------------------------------------
// 5. Workshops CRUD
// -------------------------------------------------------------
echo "\n5. Testing Workshops CRUD...\n";
$ws_title = 'AI Workshop ' . time();
$add_ws_res = add_workshop($ws_title, 'Dr. Smith', 'Tech', date('Y-m-d', strtotime('+3 days')), 'Lab 3', 'Hands on deep learning', 'UPCOMING');
assert_test("Workshops - Create Workshop in MySQL", !empty($add_ws_res['success']), $results);
$ws_id = $add_ws_res['id'] ?? null;

$get_ws = get_workshop_by_id($ws_id);
assert_test("Workshops - Read Workshop by ID from MySQL", $get_ws && $get_ws['title'] === $ws_title, $results);

$update_ws_res = update_workshop($ws_id, $ws_title, 'Dr. Smith Jr.', 'Tech', date('Y-m-d', strtotime('+3 days')), 'Lab 4', 'Updated deep learning', 'COMPLETED');
assert_test("Workshops - Update Workshop in MySQL", !empty($update_ws_res['success']), $results);

$del_ws_res = delete_workshop($ws_id);
assert_test("Workshops - Delete Workshop from MySQL", !empty($del_ws_res['success']), $results);

$get_ws_deleted = get_workshop_by_id($ws_id);
assert_test("Workshops - Verify Workshop Deletion in MySQL", $get_ws_deleted === null, $results);

// -------------------------------------------------------------
// 6. Gallery CRUD
// -------------------------------------------------------------
echo "\n6. Testing Gallery CRUD...\n";
$gal_title = 'Campus View ' . time();
$add_gal_res = add_gallery_item($gal_title, 'image', 'campus', 'assets/gallery/campus.jpg', null, 'Campus panoramic photo');
assert_test("Gallery - Create Gallery Item in MySQL", !empty($add_gal_res['success']), $results);
$gal_id = $add_gal_res['id'] ?? null;

$gal_items = get_gallery_items('campus', 'image');
$found_gal = false;
foreach ($gal_items as $item) {
    if ((int)$item['id'] === (int)$gal_id) { $found_gal = true; break; }
}
assert_test("Gallery - Read Gallery Items from MySQL", $found_gal, $results);

$del_gal_res = delete_gallery_item($gal_id);
assert_test("Gallery - Delete Gallery Item from MySQL", !empty($del_gal_res['success']), $results);

// -------------------------------------------------------------
// 7. Hero Slider & Content CRUD
// -------------------------------------------------------------
echo "\n7. Testing Hero Slider & Content CRUD...\n";
$hero_data = load_hero_data();
assert_test("Hero - Load Hero Data from MySQL", isset($hero_data['content']['title']), $results);

$orig_title = $hero_data['content']['title'];
$update_h_res = update_hero_content(['title' => 'Excellence in Education [Test]']);
assert_test("Hero - Update Hero Content in MySQL", !empty($update_h_res['success']), $results);

// Revert title
update_hero_content(['title' => $orig_title]);

$add_slide_res = add_hero_slide('assets/slider/test_slide.jpg', 'Test Slide ' . time(), 'image', 1);
assert_test("Hero - Add Hero Slide in MySQL", !empty($add_slide_res['success']), $results);
$slide_id = $add_slide_res['slide']['id'] ?? null;

$update_slide_res = update_hero_slide($slide_id, ['title' => 'Test Slide Updated']);
assert_test("Hero - Update Hero Slide in MySQL", !empty($update_slide_res['success']), $results);

$del_slide_res = delete_hero_slide($slide_id);
assert_test("Hero - Delete Hero Slide from MySQL", !empty($del_slide_res['success']), $results);

// -------------------------------------------------------------
// 8. Announcements CRUD
// -------------------------------------------------------------
echo "\n8. Testing Announcements CRUD...\n";
$get_ann = get_announcement_data();
assert_test("Announcements - Read Announcement from MySQL", is_array($get_ann) && isset($get_ann['announcement_text']), $results);

$save_ann_res = save_main_announcement(
    'Test Emergency Announcement ' . time(),
    'admission.php',
    0,
    55,
    date('d F Y'),
    1
);
assert_test("Announcements - Save Announcement to MySQL", !empty($save_ann_res['success']), $results);

$toggle_res = toggle_announcement_bar_visibility();
assert_test("Announcements - Toggle Status in MySQL", !empty($toggle_res['success']), $results);

// Restore default
save_main_announcement(
    'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)',
    'admission.php',
    1,
    60,
    date('d F Y'),
    1
);

// -------------------------------------------------------------
// 9. R&D Department Rankings & Settings CRUD
// -------------------------------------------------------------
echo "\n9. Testing R&D Department Rankings & Settings CRUD...\n";
$rnd_test_dept = 'TEST_RND_' . substr(strval(time()), -4);
$add_rnd_res = add_rnd_ranking([
    'academic_year' => '2026-27',
    'quarter' => 'Q4',
    'quarter_label' => 'Q4 2026-27',
    'department' => $rnd_test_dept,
    'department_name' => 'Department of Test R&D',
    'rank' => 1,
    'score' => 97.5,
    'status' => 'published',
    'achievements' => ['Patent Filed', '3 Q1 Papers']
]);
assert_test("R&D - Create Ranking in MySQL", !empty($add_rnd_res['success']), $results);
$rnd_id = $add_rnd_res['id'] ?? null;

$rankings_period = get_rnd_rankings_by_period('2026-27', 'Q4');
$found_rnd = false;
foreach ($rankings_period['rankings'] as $r) {
    if ((int)$r['id'] === (int)$rnd_id) { $found_rnd = true; break; }
}
assert_test("R&D - Read Rankings from MySQL", $found_rnd, $results);

$update_rnd_res = update_rnd_ranking($rnd_id, ['score' => 99.0, 'rank' => 1]);
assert_test("R&D - Update Ranking in MySQL", !empty($update_rnd_res['success']), $results);

$del_rnd_res = delete_rnd_ranking($rnd_id);
assert_test("R&D - Delete Ranking from MySQL", !empty($del_rnd_res['success']), $results);

$update_rnd_sett = update_rnd_settings(['latest_academic_year' => '2025-26', 'latest_quarter' => 'Q4']);
assert_test("R&D - Update System Settings in MySQL", $update_rnd_sett === true, $results);

// -------------------------------------------------------------
// 10. Academic Departments & Faculty CRUD
// -------------------------------------------------------------
echo "\n10. Testing Academic Departments & Faculty CRUD...\n";
$dept_code = 'TDEPT' . substr(strval(time()), -4);
$dept_name = 'Department of Innovation ' . time();
$dept_slug = 'dept-innov-' . time();

$add_dept_res = add_department(
    $dept_code,
    $dept_name,
    $dept_slug,
    'B.Tech',
    '60 Seats',
    '4 Years',
    2026,
    'fas fa-lightbulb',
    'theme-cse',
    'Test Department Description'
);
assert_test("Departments - Create Department in MySQL", !empty($add_dept_res['success']), $results);
$dept_id = $add_dept_res['id'] ?? null;

$get_dept = get_department_by_code($dept_code);
assert_test("Departments - Read Department by Code from MySQL", $get_dept && $get_dept['dept_code'] === $dept_code, $results);

// Faculty
$add_fac_res = add_faculty_member(
    'Dr. Test Faculty',
    $dept_code,
    'Associate Professor',
    'Faculty – Junior (JR)',
    0,
    0,
    'Ph.D.',
    'JNTUH-999',
    '10 Years',
    'testfac@tcek.in',
    '9876543210'
);
assert_test("Faculty - Create Faculty in MySQL", !empty($add_fac_res['success']), $results);
$fac_id = $add_fac_res['id'] ?? null;

$faculty_list = get_department_faculty($dept_code, false);
assert_test("Faculty - Read Faculty by Department from MySQL", count($faculty_list) >= 1, $results);

$del_fac_res = delete_faculty_member($fac_id);
assert_test("Faculty - Delete Faculty from MySQL", !empty($del_fac_res['success']), $results);

$del_dept_res = delete_department($dept_id);
assert_test("Departments - Delete Department from MySQL", !empty($del_dept_res['success']), $results);

// -------------------------------------------------------------
// 11. System Settings CRUD
// -------------------------------------------------------------
echo "\n11. Testing System Settings CRUD...\n";
$_SESSION['tcek_admin_logged_in'] = true;
$_SESSION['tcek_admin_username'] = 'Charan';
$_SESSION['tcek_admin_email'] = 'saivortex.dev@gmail.com';
$_SESSION['tcek_admin_role'] = 'developer';

$settings_data = load_system_settings();
assert_test("Settings - Load Settings from MySQL", is_array($settings_data) && isset($settings_data['global_404_enabled']), $results);

$upd_sett_res = update_global_404_setting(1, 'saivortex.dev@gmail.com');
assert_test("Settings - Update Global 404 Setting in MySQL", !empty($upd_sett_res['success']), $results);

$status_404 = get_global_404_status();
assert_test("Settings - Read Global 404 Status from MySQL", $status_404 === true, $results);

// Restore 404 status back to 0
$restore_404_res = update_global_404_setting(0, 'saivortex.dev@gmail.com');
assert_test("Settings - Restore Global 404 Setting to 0 in MySQL", !empty($restore_404_res['success']), $results);
assert_test("Settings - Verify Global 404 Setting is 0", get_global_404_status() === false, $results);

// -------------------------------------------------------------
// 12. Content Version Tracking & Activity Logs
// -------------------------------------------------------------
echo "\n12. Testing Content Versions & Activity Logs...\n";
record_content_update('test_module');
$versions = load_content_versions();
assert_test("Sync - Update & Read Content Version in MySQL", isset($versions['test_module']), $results);
if ($pdo instanceof PDO) {
    $pdo->query("DELETE FROM `content_versions` WHERE `module` = 'test_module'");
}

$log_res = log_activity('Test Action', 'Test Suite', 'Unit Record', 'Executed automated verification suite');
assert_test("Activity Logs - Insert Audit Log in MySQL", $log_res === true, $results);

$logs = get_activity_logs(null, null, 10);
assert_test("Activity Logs - Read Logs from MySQL", !empty($logs), $results);

// -------------------------------------------------------------
// FINAL SUMMARY
// -------------------------------------------------------------
$total = count($results);
$passed = 0;
$failed = 0;
foreach ($results as $r) {
    if ($r[0] === 'PASS') $passed++;
    else $failed++;
}

echo "\n==============================================================\n";
echo "Pure MySQL CRUD Verification Suite Results:\n";
echo "Total Tests: $total | Passed: $passed | Failed: $failed\n";
echo "==============================================================\n";

if ($failed === 0) {
    echo "SUCCESS: ALL 12 MODULES OPERATE 100% THROUGH PHP & MYSQL!\n";
    exit(0);
} else {
    echo "FAILURE: SOME OPERATIONS DID NOT PASS.\n";
    exit(1);
}
