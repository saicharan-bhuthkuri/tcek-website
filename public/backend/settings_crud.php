<?php
/**
 * System Settings & Global Configuration Controller
 * TCEK Dynamic Platform
 * 
 * Strict Access Controls:
 * - Only the authorized Developer account (saivortex.dev@gmail.com) can view or modify Global 404 Error Page Settings
 * - Persistent MySQL `system_settings` table with automatic sync to `public/backend/config/system_settings.json`
 * - Unauthorized requests (including Admin, Superadmin, and Staff) are strictly rejected with HTTP 403 Forbidden
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

if (!function_exists('get_system_settings_file')) {
    function get_system_settings_file(): string {
        return __DIR__ . '/config/system_settings.json';
    }
}

/**
 * Ensure the MySQL system_settings table exists
 */
function ensure_system_settings_table(): void {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `system_settings` (
                    `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    `setting_value` TEXT,
                    `updated_by` VARCHAR(100) DEFAULT 'Developer',
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {
            if (class_exists('ErrorHandler')) {
                ErrorHandler::log('WARNING', 'Failed to verify system_settings table', $e);
            }
        }
    }
}

/**
 * Load system settings from MySQL database with JSON storage fallback
 */
function load_system_settings(): array {
    $defaults = [
        'global_404_enabled' => 1,
        'global_404_custom_message' => 'The page you are looking for does not exist or has been moved.',
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => 'saivortex.dev@gmail.com'
    ];

    $file = get_system_settings_file();
    $fileSettings = [];
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $parsed = json_decode($json, true);
            if (is_array($parsed)) {
                $fileSettings = $parsed;
            }
        }
    }

    global $pdo;
    $dbSettings = [];
    if ($pdo instanceof PDO) {
        ensure_system_settings_table();
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value, updated_by, updated_at FROM system_settings");
            if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $key = $row['setting_key'] ?? '';
                    if ($key !== '') {
                        $val = $row['setting_value'] ?? '';
                        if ($key === 'global_404_enabled') {
                            $dbSettings[$key] = (int)$val;
                        } else {
                            $dbSettings[$key] = $val;
                        }
                        if (!empty($row['updated_at'])) {
                            $dbSettings['updated_at'] = $row['updated_at'];
                        }
                        if (!empty($row['updated_by'])) {
                            $dbSettings['updated_by'] = $row['updated_by'];
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            if (class_exists('ErrorHandler')) {
                ErrorHandler::log('WARNING', 'Could not query system_settings table', $e);
            }
        }
    }

    $merged = array_merge($defaults, $fileSettings, $dbSettings);
    $merged['global_404_enabled'] = isset($merged['global_404_enabled']) ? (int)$merged['global_404_enabled'] : 1;

    return $merged;
}

/**
 * Check if the Global 404 Error Page is currently enabled
 * @return bool
 */
function get_global_404_status(): bool {
    $settings = load_system_settings();
    return !empty($settings['global_404_enabled']);
}

/**
 * Update the Global 404 Error Page Setting
 * STRICTLY restricted to the authorized Developer account (saivortex.dev@gmail.com)
 * Rejects unauthorized users with HTTP 403 Forbidden.
 * 
 * @param int $enabled 1 for ON, 0 for OFF
 * @param string $updatedBy Identity string
 * @return array
 */
function update_global_404_setting(int $enabled, string $updatedBy = ''): array {
    // 1. Strict Server-Side Access Enforcement: Only Developer can control this feature
    if (!is_authorized_developer()) {
        if (!headers_sent()) {
            http_response_code(403);
        }
        return [
            'success' => false,
            'status'  => 403,
            'error'   => 'Forbidden',
            'message' => 'HTTP 403 Forbidden: Only the authorized Developer account (saivortex.dev@gmail.com) can access or modify Global 404 Error Page Settings.'
        ];
    }

    $enabled = $enabled ? 1 : 0;
    if (empty($updatedBy)) {
        $admin = function_exists('get_current_admin') ? get_current_admin() : [];
        $updatedBy = $admin['email'] ?? 'saivortex.dev@gmail.com';
    }

    $now = date('Y-m-d H:i:s');
    $file = get_system_settings_file();
    $current = load_system_settings();
    $current['global_404_enabled'] = $enabled;
    $current['updated_at'] = $now;
    $current['updated_by'] = $updatedBy;

    // 2. Persist to JSON File
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents($file, json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

    // 3. Persist to MySQL Database if available
    global $pdo;
    if ($pdo instanceof PDO) {
        ensure_system_settings_table();
        try {
            $stmt = $pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value, updated_by, updated_at)
                VALUES ('global_404_enabled', :val, :by, NOW())
                ON DUPLICATE KEY UPDATE setting_value = :val, updated_by = :by, updated_at = NOW()
            ");
            $stmt->execute([
                ':val' => (string)$enabled,
                ':by'  => $updatedBy
            ]);
        } catch (Throwable $e) {
            if (class_exists('ErrorHandler')) {
                ErrorHandler::log('ERROR', 'Failed to update system_settings in MySQL', $e);
            }
        }
    }

    // 4. Audit Log Entry
    if (function_exists('log_activity')) {
        $statusLabel = $enabled ? 'ENABLED (ON)' : 'DISABLED (OFF)';
        log_activity('Updated', 'System Settings', 'Global 404 Error Page', "Changed Global 404 Error Page status to {$statusLabel}", null, $updatedBy);
    }

    // 5. Trigger live synchronization ping
    if (function_exists('record_content_update')) {
        record_content_update('global');
    }

    return [
        'success'    => true,
        'status'     => 200,
        'is_enabled' => $enabled,
        'message'    => 'Global 404 Error Page setting successfully updated to ' . ($enabled ? 'ENABLED (ON)' : 'DISABLED (OFF)') . '.'
    ];
}
