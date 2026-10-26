<?php
/**
 * Database Configuration for Trinity College of Engineering & Technology (TCEK)
 * Compatible with GoDaddy cPanel MySQL Database & Local Development
 * Loaded dynamically from Environment Variables (.env)
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../security/ErrorHandler.php';

// Database credentials loaded securely from environment
$host     = env('DB_HOST', 'localhost');
$port     = env('DB_PORT', '3306');
$dbname   = env('DB_NAME', 'tcek');
$username = env('DB_USER', 'tcek');
$password = env('DB_PASS', 'tcek@developer');
$charset  = env('DB_CHARSET', 'utf8mb4');

global $pdo, $db_error;
$pdo = null;
$db_error = null;

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => true
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    $GLOBALS['pdo'] = $pdo;
} catch (PDOException $e) {
    // Log failure securely without exposing credentials or internal paths
    ErrorHandler::log('CRITICAL', 'TCEK Database Connection Failed', $e);
    $db_error = 'Database connection could not be established.';
} catch (Throwable $e) {
    ErrorHandler::log('ERROR', 'TCEK Database Driver/Config Notice', $e);
    $db_error = 'Database configuration error.';
}

/**
 * Helper to get the PDO instance safely
 * @return PDO|null
 */
function getDB() {
    global $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
        $pdo = $GLOBALS['pdo'];
        return $pdo;
    }
    return null;
}

/**
 * Check if the database is currently connected
 * @return bool
 */
function isDbConnected() {
    global $pdo;
    return (getDB() instanceof PDO);
}
