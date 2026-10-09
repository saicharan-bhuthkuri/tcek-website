<?php
session_start();
$_SESSION['tcek_admin_logged_in'] = true;
$_SESSION['tcek_admin_role'] = 'admin';
$_SESSION['tcek_admin_id'] = 1;
$_SESSION['tcek_admin_username'] = 'Charan';
$_SESSION['tcek_admin_name'] = 'Charan';

require __DIR__ . '/../public/admin/dashboard.php';
