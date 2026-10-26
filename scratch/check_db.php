<?php
require_once __DIR__ . '/../public/backend/config/database.php';

echo "Database connection test:\n";
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Found " . count($tables) . " tables:\n";
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo " - {$t}: {$count} records\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
