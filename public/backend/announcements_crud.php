<?php
/**
 * TCEK Top Announcement Bar Controller
 * Handles the single main scrolling announcement for the college website:
 * - Update announcement message & optional destination link
 * - Enable / Disable announcement bar visibility
 * - Scrolling speed configuration
 * - "Last Updated" date timestamp tracking
 */

if (!function_exists('get_announcements_store_file')) {
    function get_announcements_store_file(): string {
        return __DIR__ . '/config/announcements_data.json';
    }
}

/**
 * Load announcement configuration from local store
 */
function load_announcements_data(): array {
    $file = get_announcements_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                // If legacy items array exists, extract the first item as main announcement
                if (!isset($data['announcement_text'])) {
                    $first_title = '';
                    $first_link = '';
                    if (!empty($data['items']) && is_array($data['items'])) {
                        $first_title = $data['items'][0]['title'] ?? '';
                        $first_link  = $data['items'][0]['link_url'] ?? '';
                    }
                    $data['announcement_text'] = $first_title ?: 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)';
                    $data['link_url'] = $first_link;
                }

                $is_enabled = isset($data['is_enabled']) ? (int)$data['is_enabled'] : (isset($data['settings']['is_enabled']) ? (int)$data['settings']['is_enabled'] : 1);
                $speed      = isset($data['scrolling_speed']) ? (int)$data['scrolling_speed'] : (isset($data['settings']['scrolling_speed']) ? (int)$data['settings']['scrolling_speed'] : 60);
                $last_up    = !empty($data['last_updated']) ? $data['last_updated'] : (!empty($data['settings']['last_updated']) ? $data['settings']['last_updated'] : date('d F Y'));
                $show_up    = isset($data['show_last_updated']) ? (int)$data['show_last_updated'] : (isset($data['settings']['show_last_updated']) ? (int)$data['settings']['show_last_updated'] : 1);

                $data['is_enabled']        = $is_enabled ? 1 : 0;
                $data['scrolling_speed']   = max(10, min(300, $speed));
                $data['last_updated']      = $last_up;
                $data['show_last_updated'] = $show_up ? 1 : 0;
                $data['announcement_text'] = trim($data['announcement_text'] ?? '');
                $data['link_url']          = trim($data['link_url'] ?? '');

                // Keep $data['settings'] synced for any legacy templates
                $data['settings'] = [
                    'is_enabled'        => $data['is_enabled'],
                    'scrolling_speed'   => $data['scrolling_speed'],
                    'last_updated'      => $data['last_updated'],
                    'show_last_updated' => $data['show_last_updated'],
                    'announcement_text' => $data['announcement_text'],
                    'link_url'          => $data['link_url']
                ];

                return $data;
            }
        }
    }

    // Default configuration if store file is missing
    $def_text = 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)';
    return [
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
 * Save announcement data atomically
 */
function save_announcements_data(array $data): bool {
    $file = get_announcements_store_file();
    $dir  = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return @file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Public accessor for website headers and dashboard
 */
function get_announcement_data(): array {
    return load_announcements_data();
}

/**
 * Save / Update the single main scrolling announcement
 * Replaces the existing announcement message and updates settings.
 */
function save_main_announcement(
    string $announcement_text,
    string $link_url = '',
    int $is_enabled = 1,
    int $scrolling_speed = 60,
    string $last_updated = '',
    int $show_last_updated = 1
): array {
    $announcement_text = trim($announcement_text);
    if ($announcement_text === '') {
        return ['success' => false, 'message' => 'Announcement message cannot be empty.'];
    }

    $scrolling_speed = max(10, min(300, (int)$scrolling_speed));
    $last_updated    = trim($last_updated);
    if ($last_updated === '') {
        $last_updated = date('d F Y');
    }

    $data = [
        'is_enabled'        => $is_enabled ? 1 : 0,
        'announcement_text' => $announcement_text,
        'link_url'          => trim($link_url),
        'scrolling_speed'   => $scrolling_speed,
        'last_updated'      => $last_updated,
        'show_last_updated' => $show_last_updated ? 1 : 0,
        'updated_at'        => date('Y-m-d H:i:s'),
        'settings'          => [
            'is_enabled'        => $is_enabled ? 1 : 0,
            'scrolling_speed'   => $scrolling_speed,
            'last_updated'      => $last_updated,
            'show_last_updated' => $show_last_updated ? 1 : 0,
            'announcement_text' => $announcement_text,
            'link_url'          => trim($link_url)
        ]
    ];

    $saved = save_announcements_data($data);
    if ($saved) {
        if (function_exists('log_activity')) {
            $status_str = $is_enabled ? 'Enabled' : 'Disabled';
            log_activity('Updated', 'Announcements', 'Main Announcement', "Status: {$status_str}, Speed: {$scrolling_speed}s, Date: {$last_updated}");
        }
        return ['success' => true, 'message' => 'Main announcement updated and live on website!'];
    }
    return ['success' => false, 'message' => 'Failed to save announcement settings.'];
}

/**
 * Toggle announcement bar visibility on/off
 */
function toggle_announcement_bar_visibility(): array {
    $data = load_announcements_data();
    $current = !empty($data['is_enabled']) ? 1 : 0;
    $new = $current ? 0 : 1;

    $data['is_enabled'] = $new;
    $data['settings']['is_enabled'] = $new;
    $data['updated_at'] = date('Y-m-d H:i:s');

    $saved = save_announcements_data($data);
    if ($saved) {
        if (function_exists('log_activity')) {
            log_activity('Updated', 'Announcements', 'Announcement Bar', "Visibility set to " . ($new ? 'Enabled' : 'Disabled'));
        }
        return ['success' => true, 'message' => 'Announcement bar ' . ($new ? 'enabled (live on website)' : 'disabled (hidden from website)') . '!'];
    }
    return ['success' => false, 'message' => 'Failed to update visibility.'];
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
