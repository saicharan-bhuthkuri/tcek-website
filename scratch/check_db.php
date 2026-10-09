<?php
require_once __DIR__ . '/../public/backend/config/database.php';
$db = getDB();
if ($db instanceof PDO) {
    echo "DB Connected Successfully!\n";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing tables: " . implode(', ', $tables) . "\n";
} else {
    echo "DB Offline / Not connected.\n";
}
