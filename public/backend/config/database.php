<?php
/**
 * Database Configuration for Trinity College of Engineering & Technology (TCEK)
 * Compatible with GoDaddy cPanel MySQL Database
 */

// Database credentials
$host     = "localhost";
$dbname   = "tcek";
$username = "tcek";
$password = "tcek@developer";

$pdo = null;
$db_error = null;

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Save error message without breaking application
    $db_error = $e->getMessage();
    error_log("TCEK Database Connection Failed: " . $e->getMessage());
} catch (Throwable $e) {
    $db_error = $e->getMessage();
    error_log("TCEK Database Driver/Config Notice: " . $e->getMessage());
}

/**
 * Helper to get the PDO instance safely
 * @return PDO|null
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * Check if the database is currently connected
 * @return bool
 */
function isDbConnected() {
    global $pdo;
    return ($pdo instanceof PDO);
}
