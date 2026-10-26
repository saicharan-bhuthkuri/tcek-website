<?php
/**
 * TCEK Top Announcement Bar Controller
 * Handles the single main scrolling announcement for the college website:
 * - Update announcement message & optional destination link
 * - Enable / Disable announcement bar visibility
 * - Scrolling speed configuration
 * - "Last Updated" date timestamp tracking
 * - 100% MySQL Database Powered Storage & Retrieval
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/sync.php';

/**
 * Ensure announcements table exists in MySQL
 */
function ensure_announcements_table(?PDO $pdo = null): void {
    if (!$pdo) {
        $pdo = getDB();
    }
    if ($pdo instanceof PDO) {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `announcements` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `announcement_text` TEXT NOT NULL,
                  `link_url` VARCHAR(255) DEFAULT NULL,
                  `is_enabled` TINYINT(1) DEFAULT 1,
                  `scrolling_speed` INT DEFAULT 60,
                  `last_updated` VARCHAR(50) DEFAULT NULL,
                  `show_last_updated` TINYINT(1) DEFAULT 1,
                  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {}
    }
}

/**
 * Load announcement configuration directly from MySQL database
 */
function load_announcements_data(): array {
    global $pdo;
    $def_text = 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)';

    if ($pdo instanceof PDO) {
        try {
            ensure_announcements_table($pdo);
            $stmt = $pdo->query("SELECT * FROM announcements ORDER BY id ASC LIMIT 1");
            $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

            if ($row) {
                $is_enabled = isset($row['is_enabled']) ? (int)$row['is_enabled'] : 1;
                $speed      = isset($row['scrolling_speed']) ? (int)$row['scrolling_speed'] : 60;
                $speed      = max(10, min(300, $speed));
                $last_up    = !empty($row['last_updated']) ? $row['last_updated'] : date('d F Y');
                $show_up    = isset($row['show_last_updated']) ? (int)$row['show_last_updated'] : 1;
                $text       = trim($row['announcement_text'] ?? '') ?: $def_text;
                $link       = trim($row['link_url'] ?? '');

                return [
                    'id'                => (int)($row['id'] ?? 1),
                    'is_enabled'        => $is_enabled,
                    'announcement_text' => $text,
                    'link_url'          => $link,
                    'scrolling_speed'   => $speed,
                    'last_updated'      => $last_up,
                    'show_last_updated' => $show_up,
                    'updated_at'        => $row['updated_at'] ?? date('Y-m-d H:i:s'),
                    'settings'          => [
                        'is_enabled'        => $is_enabled,
                        'scrolling_speed'   => $speed,
                        'last_updated'      => $last_up,
                        'show_last_updated' => $show_up,
                        'announcement_text' => $text,
                        'link_url'          => $link
                    ]
                ];
            } else {
                // Seed default row into MySQL table
                $ins = $pdo->prepare("
                    INSERT INTO announcements (id, announcement_text, link_url, is_enabled, scrolling_speed, last_updated, show_last_updated, created_at)
                    VALUES (1, :t, 'admission.php', 1, 60, :lu, 1, NOW())
                ");
                $nowDate = date('d F Y');
                $ins->execute([':t' => $def_text, ':lu' => $nowDate]);
            }
        } catch (Throwable $e) {
            error_log("Failed to load announcements from MySQL: " . $e->getMessage());
        }
    }

    // Fallback in-memory defaults if DB connection is initializing
    return [
        'id'                => 1,
        'is_enabled'        => 1,
        'announcement_text' => $def_text,
        'link_url'          => 'admission.php',
        'scrolling_speed'   => 60,
        'last_updated'      => date('d F Y'),
        'show_last_updated' => 1,
        'updated_at'        => date('Y-m-d H:i:s'),
        'settings'          => [
            'is_enabled'        => 1,
            'scrolling_speed'   => 60,
            'last_updated'      => date('d F Y'),
            'show_last_updated' => 1,
            'announcement_text' => $def_text,
            'link_url'          => 'admission.php'
        ]
    ];
}

/**
 * Public accessor for website headers and dashboard
 */
function get_announcement_data(): array {
    return load_announcements_data();
}

/**
 * Save / Update the single main scrolling announcement in MySQL database
 */
function save_main_announcement(
    string $announcement_text,
    string $link_url = '',
    int $is_enabled = 1,
    int $scrolling_speed = 60,
    string $last_updated = '',
    int $show_last_updated = 1
): array {
    global $pdo;

    $announcement_text = trim($announcement_text);
    if ($announcement_text === '') {
        return ['success' => false, 'message' => 'Announcement message cannot be empty.'];
    }

    $scrolling_speed = max(10, min(300, (int)$scrolling_speed));
    $last_updated    = trim($last_updated);
    if ($last_updated === '') {
        $last_updated = date('d F Y');
    }
    $is_enabled = $is_enabled ? 1 : 0;
    $show_last_updated = $show_last_updated ? 1 : 0;
    $link_url = trim($link_url);

    if ($pdo instanceof PDO) {
        try {
            ensure_announcements_table($pdo);
            $stmt = $pdo->prepare("
                INSERT INTO announcements (id, announcement_text, link_url, is_enabled, scrolling_speed, last_updated, show_last_updated, updated_at)
                VALUES (1, :t, :l, :e, :s, :lu, :su, NOW())
                ON DUPLICATE KEY UPDATE
                    announcement_text = VALUES(announcement_text),
                    link_url = VALUES(link_url),
                    is_enabled = VALUES(is_enabled),
                    scrolling_speed = VALUES(scrolling_speed),
                    last_updated = VALUES(last_updated),
                    show_last_updated = VALUES(show_last_updated),
                    updated_at = NOW()
            ");
            $saved = $stmt->execute([
                ':t'  => $announcement_text,
                ':l'  => $link_url,
                ':e'  => $is_enabled,
                ':s'  => $scrolling_speed,
                ':lu' => $last_updated,
                ':su' => $show_last_updated
            ]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('announcements');
                }
                if (function_exists('log_activity')) {
                    $status_str = $is_enabled ? 'Enabled' : 'Disabled';
                    log_activity('Updated', 'Announcements', 'Main Announcement', "Status: {$status_str}, Speed: {$scrolling_speed}s, Date: {$last_updated}");
                }
                return ['success' => true, 'message' => 'Main announcement updated and live on website!'];
            }
        } catch (Throwable $e) {
            error_log("Failed to save announcement to MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error while saving announcement: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Toggle announcement bar visibility on/off in MySQL
 */
function toggle_announcement_bar_visibility(): array {
    global $pdo;
    $data = load_announcements_data();
    $current = !empty($data['is_enabled']) ? 1 : 0;
    $new = $current ? 0 : 1;

    if ($pdo instanceof PDO) {
        try {
            ensure_announcements_table($pdo);
            $stmt = $pdo->prepare("UPDATE announcements SET is_enabled = :e, updated_at = NOW() WHERE id = :id");
            $saved = $stmt->execute([':e' => $new, ':id' => (int)($data['id'] ?? 1)]);
            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('announcements');
                }
                if (function_exists('log_activity')) {
                    log_activity('Updated', 'Announcements', 'Announcement Bar', "Visibility set to " . ($new ? 'Enabled' : 'Disabled'));
                }
                return ['success' => true, 'message' => 'Announcement bar ' . ($new ? 'enabled (live on website)' : 'disabled (hidden from website)') . '!'];
            }
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Generate formatted HTML for the single main scrolling announcement
 */
function get_announcement_ticker_html(?array $data = null, bool $ignore_disabled = false): string {
    if ($data === null) {
        $data = load_announcements_data();
    }

    $is_enabled = !empty($data['is_enabled']) || !empty($data['settings']['is_enabled']);
    if (!$ignore_disabled && !$is_enabled) {
        return '';
    }

    $text = trim($data['announcement_text'] ?? ($data['settings']['announcement_text'] ?? ''));
    if ($text === '') {
        $text = 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years • NAAC Accredited • Admissions Open';
    }

    $link = trim($data['link_url'] ?? ($data['settings']['link_url'] ?? ''));
    $escaped_text = htmlspecialchars($text);

    if ($link !== '') {
        $escaped_link = htmlspecialchars($link);
        return '<a href="' . $escaped_link . '" class="ticker-scroll-link">' . $escaped_text . '</a>';
    }

    return '<span class="ticker-scroll-text">' . $escaped_text . '</span>';
}

/**
 * Legacy compatibility bridge for update_announcement_settings
 */
function update_announcement_settings(int $is_enabled, int $scrolling_speed, string $last_updated = '', int $show_last_updated = 1, string $custom_text = ''): array {
    $data = load_announcements_data();
    $text = trim($custom_text) ?: ($data['announcement_text'] ?? 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status');
    $link = $data['link_url'] ?? '';
    return save_main_announcement($text, $link, $is_enabled, $scrolling_speed, $last_updated, $show_last_updated);
}

/**
 * Legacy bridge: add_announcement_item updates the main announcement
 */
function add_announcement_item(string $title, string $link_url = '', int $is_active = 1): array {
    $data = load_announcements_data();
    return save_main_announcement($title, $link_url, $is_active, (int)($data['scrolling_speed'] ?? 60), $data['last_updated'] ?? date('d F Y'), (int)($data['show_last_updated'] ?? 1));
}

/**
 * Legacy bridge: update_announcement_item updates the main announcement
 */
function update_announcement_item(int $id, string $title, string $link_url = '', int $is_active = 1): array {
    $data = load_announcements_data();
    return save_main_announcement($title, $link_url, $is_active, (int)($data['scrolling_speed'] ?? 60), $data['last_updated'] ?? date('d F Y'), (int)($data['show_last_updated'] ?? 1));
}

/**
 * Legacy bridge: delete_announcement_item
 */
function delete_announcement_item(int $id): array {
    return ['success' => true, 'message' => 'Announcement item reset.'];
}

/**
 * Legacy bridge: toggle_announcement_item_status
 */
function toggle_announcement_item_status(int $id): array {
    return toggle_announcement_bar_visibility();
}
