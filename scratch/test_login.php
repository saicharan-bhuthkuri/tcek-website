<?php
require_once __DIR__ . '/../public/backend/auth.php';
$res = verify_admin_login('Charan', 'tcek@developer');
print_r($res);
echo "\nSession after login:\n";
print_r($_SESSION);
