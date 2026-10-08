<?php
/**
 * Admin Logout Handler
 */
require_once __DIR__ . '/../backend/auth.php';
admin_logout();
header('Location: login.php?msg=logged_out');
exit;
