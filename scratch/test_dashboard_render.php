<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['tcek_admin_logged_in'] = true;
$_SESSION['tcek_admin_role'] = 'admin';
$_GET['tab'] = 'faculty';

chdir(__DIR__ . '/../public/admin');
ob_start();
include 'dashboard.php';
$html = ob_get_clean();

echo "Dashboard HTML Length: " . strlen($html) . " bytes\n";
echo (strpos($html, 'faculty-filter-grid-primary') !== false ? " [OK] Primary grid found\n" : " [FAIL] Primary grid missing\n");
echo (strpos($html, 'faculty-filter-grid-secondary') !== false ? " [OK] Secondary grid found\n" : " [FAIL] Secondary grid missing\n");
echo (strpos($html, 'Select Department') !== false ? " [OK] Department selector found\n" : " [FAIL] Department selector missing\n");
