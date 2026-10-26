<?php
/**
 * TCEK Live Synchronization Engine
 * Handles ultra-fast, zero-blocking real-time synchronization between
 * admin dashboard and public website without page reloads.
 */

require_once __DIR__ . '/config/database.php';

if (!function_exists('get_sync_versions_file')) {
    function get_sync_versions_file(): string {
        return __DIR__ . '/config/content_versions.json';
    }
}

/**
 * Load all module version timestamps directly from MySQL
 */
function load_content_versions(): array {
    global $pdo;
    $versions = [];
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->query("SELECT module, version_timestamp, updated_at FROM content_versions");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $latest = 0;
            $latestFormatted = date('d M Y, h:i A');
            foreach ($rows as $r) {
                $versions[$r['module']] = (int)$r['version_timestamp'];
                $ts = strtotime($r['updated_at'] ?? '');
                if ($ts > $latest) {
                    $latest = $ts;
                    $latestFormatted = date('d M Y, h:i A', $ts);
                }
            }
            if (!empty($versions)) {
                $versions['last_updated'] = date('Y-m-d H:i:s', $latest ?: time());
                $versions['last_updated_formatted'] = $latestFormatted;
                return $versions;
            }
        } catch (Throwable $t) {
            error_log("Error loading content_versions from DB: " . $t->getMessage());
        }
    }

    return [
        'announcements' => time(),
        'hero'          => time(),
        'news'          => time(),
        'notifications' => time(),
        'events'        => time(),
        'event_media'   => time(),
        'gallery'       => time(),
        'rnd'           => time(),
        'departments'   => time(),
        'faculty'       => time(),
        'courses'       => time(),
        'workshops'     => time(),
        'users'         => time(),
        'global'        => time(),
        'last_updated'  => date('Y-m-d H:i:s'),
        'last_updated_formatted' => date('d M Y, h:i A')
    ];
}

/**
 * Atomically record a content change for a module in MySQL
 */
function record_content_update(string $module): bool {
    global $pdo;
    $now = time();
    $modulesToUpdate = [$module, 'global'];

    // Cross-module linkages
    if (in_array($module, ['departments', 'faculty'], true)) {
        $modulesToUpdate[] = 'courses';
    }
    if (in_array($module, ['events', 'event_media'], true)) {
        $modulesToUpdate[] = 'gallery';
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO content_versions (module, version_timestamp, updated_at)
                VALUES (:m, :ts, NOW())
                ON DUPLICATE KEY UPDATE version_timestamp = VALUES(version_timestamp), updated_at = NOW()
            ");
            foreach (array_unique($modulesToUpdate) as $mod) {
                $stmt->execute([':m' => $mod, ':ts' => $now]);
            }
            return true;
        } catch (Throwable $t) {
            error_log("Failed to update content_versions in DB: " . $t->getMessage());
        }
    }
    return false;
}

// -------------------------------------------------------------
// API REQUEST DISPATCHER
// -------------------------------------------------------------

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'check') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $since = (int)($_GET['since'] ?? 0);
    $versions = load_content_versions();
    $globalTs = (int)($versions['global'] ?? time());

    if ($since >= $globalTs) {
        echo json_encode([
            'has_updates'  => false,
            'timestamp'    => $globalTs,
            'last_updated' => $versions['last_updated_formatted'] ?? date('d M Y, h:i A')
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Determine which modules have updated
    $updatedModules = [];
    foreach ($versions as $mod => $ts) {
        if (in_array($mod, ['global', 'last_updated', 'last_updated_formatted'], true)) {
            continue;
        }
        if ((int)$ts > $since) {
            $updatedModules[] = $mod;
        }
    }

    echo json_encode([
        'has_updates'     => true,
        'timestamp'       => $globalTs,
        'updated_modules' => $updatedModules,
        'last_updated'    => $versions['last_updated_formatted'] ?? date('d M Y, h:i A')
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

if ($action === 'get_section') {
    header('Content-Type: application/json; charset=utf-8');
    $section = trim($_GET['section'] ?? '');
    $html = '';

    switch ($section) {
        case 'announcements':
            @include_once __DIR__ . '/announcements_crud.php';
            if (function_exists('get_announcement_data')) {
                $ann_data = get_announcement_data();
                $tickerHtml = function_exists('get_announcement_ticker_html') ? get_announcement_ticker_html($ann_data) : '';
                $settings = $ann_data['settings'] ?? [];
                echo json_encode([
                    'success'       => true,
                    'is_enabled'    => !empty($settings['is_enabled']),
                    'speed'         => (int)($settings['scrolling_speed'] ?? 60),
                    'last_updated'  => $settings['last_updated'] ?? date('d F Y'),
                    'ticker_html'   => $tickerHtml
                ], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'notifications':
        case 'circulars':
            @include_once __DIR__ . '/crud.php';
            if (function_exists('get_notifications')) {
                $live_notices = get_notifications(8, false);
                ob_start();
                include __DIR__ . '/snippets/bulletin_list.php';
                $html = ob_get_clean();
                echo json_encode(['success' => true, 'html' => $html, 'count' => count($live_notices)], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'circulars_table':
            @include_once __DIR__ . '/crud.php';
            if (function_exists('get_notifications')) {
                $circulars_list = get_notifications(100, false);
                ob_start();
                include __DIR__ . '/snippets/circulars_table_rows.php';
                $html = ob_get_clean();
                echo json_encode(['success' => true, 'html' => $html, 'count' => count($circulars_list)], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;


        case 'news':
            @include_once __DIR__ . '/crud.php';
            if (function_exists('get_news')) {
                $live_news = get_news(6);
                ob_start();
                include __DIR__ . '/snippets/news_press_grid.php';
                $html = ob_get_clean();
                echo json_encode(['success' => true, 'html' => $html, 'count' => count($live_news)], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'rnd':
            @include_once __DIR__ . '/rnd_crud.php';
            if (function_exists('get_rnd_rankings_by_period')) {
                $year = $_GET['year'] ?? null;
                $quarter = $_GET['quarter'] ?? null;
                $data = get_rnd_rankings_by_period($year, $quarter, 'published');
                $periods = get_distinct_rnd_periods(true);
                echo json_encode([
                    'success' => true,
                    'data'    => $data,
                    'periods' => $periods
                ], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'hero':
            @include_once __DIR__ . '/hero_crud.php';
            if (function_exists('load_hero_data')) {
                $heroData = load_hero_data();
                echo json_encode(['success' => true, 'data' => $heroData], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'courses':
            @include_once __DIR__ . '/departments_crud.php';
            if (function_exists('get_departments')) {
                $depts = get_departments(true);
                ob_start();
                include __DIR__ . '/snippets/courses_grid.php';
                $html = ob_get_clean();
                echo json_encode(['success' => true, 'html' => $html], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;

        case 'gallery':
            @include_once __DIR__ . '/crud.php';
            if (function_exists('get_gallery_items')) {
                $dyn_photos = get_gallery_items(null, null, 100);
                $dyn_news   = function_exists('get_news') ? get_news(50) : [];
                ob_start();
                include __DIR__ . '/snippets/gallery_grid.php';
                $html = ob_get_clean();
                echo json_encode(['success' => true, 'html' => $html, 'count' => count($dyn_photos)], JSON_UNESCAPED_SLASHES);
                exit;
            }
            break;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown or unsupported section snippet.'], JSON_UNESCAPED_SLASHES);
    exit;
}
