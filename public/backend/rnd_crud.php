<?php
/**
 * TCEK R&D Department Rankings Controller
 * Handles dynamic R&D Department Rankings management:
 * - Add, Edit, Delete, Archive, and Publish ranking results
 * - Supports historical periods (Academic Year + Quarter)
 * - Validates duplicate entries for same department & period
 * - Automatically sorts cards by ranking position
 * - Atomic persistence to JSON store & MySQL database synchronization
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/sync.php';

if (!function_exists('get_rnd_store_file')) {
    function get_rnd_store_file(): string {
        return __DIR__ . '/config/rnd_rankings_data.json';
    }
}

/**
 * Ensure MySQL table exists if database is connected
 */
function ensure_rnd_table_exists(): void {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `rnd_rankings` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `academic_year` VARCHAR(20) NOT NULL,
                    `quarter` VARCHAR(10) NOT NULL,
                    `quarter_label` VARCHAR(100) DEFAULT NULL,
                    `department` VARCHAR(50) NOT NULL,
                    `department_name` VARCHAR(150) NOT NULL,
                    `rank` INT NOT NULL DEFAULT 1,
                    `award_category` VARCHAR(100) DEFAULT NULL,
                    `award_badge` VARCHAR(100) DEFAULT NULL,
                    `description` TEXT DEFAULT NULL,
                    `achievements` TEXT DEFAULT NULL,
                    `criteria_tags` TEXT DEFAULT NULL,
                    `status` ENUM('published', 'archived', 'draft') NOT NULL DEFAULT 'published',
                    `publication_date` DATE DEFAULT NULL,
                    `score` DECIMAL(5,2) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX `idx_period` (`academic_year`, `quarter`),
                    INDEX `idx_dept` (`department`),
                    INDEX `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {
            // Silently fall back to JSON store
        }
    }
}

/**
 * Load all R&D rankings data from store
 */
function load_rnd_rankings_store(): array {
    $file = get_rnd_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && isset($data['rankings']) && is_array($data['rankings'])) {
                return $data;
            }
        }
    }

    return [
        'settings' => [
            'latest_academic_year' => '2025-26',
            'latest_quarter' => 'Q4',
            'auto_publish' => true,
            'last_updated' => date('Y-m-d')
        ],
        'rankings' => []
    ];
}

/**
 * Save R&D rankings data atomically to JSON store
 */
function save_rnd_rankings_store(array $data): bool {
    $file = get_rnd_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $data['settings']['last_updated'] = date('Y-m-d H:i:s');
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $tmp = $file . '.tmp.' . bin2hex(random_bytes(6));
    $saved = false;
    if (@file_put_contents($tmp, $json, LOCK_EX) !== false) {
        if (@rename($tmp, $file)) {
            @chmod($file, 0644);
            $saved = true;
        } else {
            @unlink($tmp);
        }
    }

    if (!$saved) {
        $saved = (@file_put_contents($file, $json, LOCK_EX) !== false);
    }

    if ($saved && function_exists('record_content_update')) {
        record_content_update('rnd');
    }

    return $saved;
}

/**
 * Compare two quarters/years to sort latest first
 * E.g., 2025-26 Q4 > 2025-26 Q3 > 2024-25 Q4
 */
function compare_rnd_periods(string $yearA, string $quarterA, string $yearB, string $quarterB): int {
    if ($yearA !== $yearB) {
        return strcmp($yearB, $yearA); // Descending year
    }
    // Extract quarter number
    $qNumA = (int) filter_var($quarterA, FILTER_SANITIZE_NUMBER_INT);
    $qNumB = (int) filter_var($quarterB, FILTER_SANITIZE_NUMBER_INT);
    return $qNumB <=> $qNumA; // Descending quarter
}

/**
 * Get list of all distinct periods (academic year + quarter) sorted latest first
 */
function get_distinct_rnd_periods(bool $onlyPublished = false): array {
    $store = load_rnd_rankings_store();
    $rankings = $store['rankings'] ?? [];

    $periodsMap = [];
    foreach ($rankings as $r) {
        if ($onlyPublished && ($r['status'] ?? 'published') !== 'published') {
            continue;
        }

        $year = trim($r['academic_year'] ?? '');
        $quarter = strtoupper(trim($r['quarter'] ?? ''));
        if ($year === '' || $quarter === '') {
            continue;
        }

        $key = $year . '|' . $quarter;
        if (!isset($periodsMap[$key])) {
            $periodsMap[$key] = [
                'academic_year' => $year,
                'quarter'       => $quarter,
                'quarter_label' => trim($r['quarter_label'] ?? ''),
                'count'         => 0,
                'latest_date'   => $r['publication_date'] ?? ''
            ];
        }
        $periodsMap[$key]['count']++;
        if (!empty($r['publication_date']) && strcmp($r['publication_date'], $periodsMap[$key]['latest_date']) > 0) {
            $periodsMap[$key]['latest_date'] = $r['publication_date'];
        }
    }

    $periods = array_values($periodsMap);
    usort($periods, function($a, $b) {
        return compare_rnd_periods($a['academic_year'], $a['quarter'], $b['academic_year'], $b['quarter']);
    });

    // Mark the first one as latest
    if (!empty($periods)) {
        $periods[0]['is_latest'] = true;
    }

    return $periods;
}

/**
 * Get rankings for a specific period or default to the latest period
 * Automatically sorted by rank ASC
 */
function get_rnd_rankings_by_period(?string $academic_year = null, ?string $quarter = null, string $status = 'published'): array {
    $store = load_rnd_rankings_store();
    $rankings = $store['rankings'] ?? [];

    // Determine period to fetch
    if (empty($academic_year) || empty($quarter)) {
        $periods = get_distinct_rnd_periods($status === 'published');
        if (!empty($periods)) {
            $academic_year = $periods[0]['academic_year'];
            $quarter = $periods[0]['quarter'];
        } else {
            $academic_year = $store['settings']['latest_academic_year'] ?? '2025-26';
            $quarter = $store['settings']['latest_quarter'] ?? 'Q4';
        }
    }

    $filtered = [];
    foreach ($rankings as $r) {
        if (strcasecmp($r['academic_year'] ?? '', $academic_year) !== 0) {
            continue;
        }
        if (strcasecmp($r['quarter'] ?? '', $quarter) !== 0) {
            continue;
        }
        if ($status !== 'all' && ($r['status'] ?? 'published') !== $status) {
            continue;
        }
        $filtered[] = $r;
    }

    // Sort by rank ASC
    usort($filtered, function($a, $b) {
        return ((int)($a['rank'] ?? 99)) <=> ((int)($b['rank'] ?? 99));
    });

    return [
        'academic_year' => $academic_year,
        'quarter'       => $quarter,
        'quarter_label' => !empty($filtered) ? ($filtered[0]['quarter_label'] ?? '') : '',
        'rankings'      => $filtered
    ];
}

/**
 * Check if a department already has a ranking in the given year & quarter
 */
function check_rnd_duplicate(string $department, string $academic_year, string $quarter, ?int $excludeId = null): bool {
    $store = load_rnd_rankings_store();
    $rankings = $store['rankings'] ?? [];

    $deptClean = strtolower(trim($department));
    $yearClean = strtolower(trim($academic_year));
    $quarterClean = strtolower(trim($quarter));

    foreach ($rankings as $r) {
        if ($excludeId !== null && (int)($r['id'] ?? 0) === $excludeId) {
            continue;
        }
        if (
            strtolower(trim($r['department'] ?? '')) === $deptClean &&
            strtolower(trim($r['academic_year'] ?? '')) === $yearClean &&
            strtolower(trim($r['quarter'] ?? '')) === $quarterClean
        ) {
            return true;
        }
    }
    return false;
}

/**
 * Add a new ranking result
 */
function add_rnd_ranking(array $data): array {
    $department     = trim((string)($data['department'] ?? ''));
    $departmentName = trim((string)($data['department_name'] ?? ''));
    $academicYear   = trim((string)($data['academic_year'] ?? ''));
    $quarter        = strtoupper(trim((string)($data['quarter'] ?? '')));
    $quarterLabel   = trim((string)($data['quarter_label'] ?? ''));
    $rank           = max(1, (int)($data['rank'] ?? 1));
    $awardCategory  = trim((string)($data['award_category'] ?? ''));
    $awardBadge     = trim((string)($data['award_badge'] ?? ''));
    $description    = trim((string)($data['description'] ?? ''));
    $status         = in_array($data['status'] ?? '', ['published', 'archived', 'draft'], true) ? $data['status'] : 'published';
    $publicationDate= !empty($data['publication_date']) ? trim($data['publication_date']) : date('Y-m-d');
    $score          = isset($data['score']) && is_numeric($data['score']) ? (float)$data['score'] : null;

    if ($department === '' || $academicYear === '' || $quarter === '') {
        return ['success' => false, 'message' => 'Department code, Academic Year, and Quarter are required fields.'];
    }

    if (empty($departmentName)) {
        $departmentName = $department;
    }

    // Duplicate check
    if (check_rnd_duplicate($department, $academicYear, $quarter)) {
        return [
            'success' => false,
            'message' => "A ranking result for Department '{$department}' in Academic Year '{$academicYear}' ({$quarter}) already exists. Edit the existing result instead."
        ];
    }

    // Achievements parsing (array or newline-separated string)
    $achievements = [];
    if (isset($data['achievements'])) {
        if (is_array($data['achievements'])) {
            $achievements = array_values(array_filter(array_map('trim', $data['achievements'])));
        } else {
            $lines = explode("\n", (string)$data['achievements']);
            foreach ($lines as $line) {
                $line = trim($line, " \t\n\r\0\x0B•-*");
                if ($line !== '') {
                    $achievements[] = $line;
                }
            }
        }
    }

    // Default achievements if blank
    if (empty($achievements)) {
        $achievements = ['Faculty Research Papers', 'Student Innovation Leads', 'NPTEL Enrolment Benchmark'];
    }

    // Default award badge / category based on rank
    if (empty($awardCategory)) {
        $suffixes = [1 => 'st · Gold', 2 => 'nd · Bronze', 3 => 'rd · Bronze', 4 => 'th · Emerging', 5 => 'th · Emerging'];
        $awardCategory = ($rank) . ($suffixes[$rank] ?? 'th · Emerging');
    }
    if (empty($awardBadge)) {
        $badges = [1 => '🏆 Gold Champion', 2 => '🎖️ Bronze Honor', 3 => '🎖️ Bronze Honor', 4 => '🌱 Emerging', 5 => '🌱 Emerging'];
        $awardBadge = $badges[$rank] ?? '🌱 Emerging';
    }

    $store = load_rnd_rankings_store();
    $maxId = 0;
    foreach ($store['rankings'] as $r) {
        if ((int)($r['id'] ?? 0) > $maxId) {
            $maxId = (int)$r['id'];
        }
    }
    $newId = $maxId + 1;

    $newRecord = [
        'id'               => $newId,
        'academic_year'    => $academicYear,
        'quarter'          => $quarter,
        'quarter_label'    => $quarterLabel,
        'department'       => $department,
        'department_name'  => $departmentName,
        'rank'             => $rank,
        'award_category'   => $awardCategory,
        'award_badge'      => $awardBadge,
        'description'      => $description,
        'achievements'     => $achievements,
        'criteria_tags'    => [
            'Research & Innovation',
            'Patents & IPR',
            'Scopus & IEEE Publications',
            'NPTEL Honors',
            'IIC Initiatives'
        ],
        'status'           => $status,
        'publication_date' => $publicationDate,
        'score'            => $score,
        'created_at'       => date('Y-m-d H:i:s'),
        'updated_at'       => date('Y-m-d H:i:s')
    ];

    $store['rankings'][] = $newRecord;

    // Check if this new period is latest
    $latestPeriod = get_distinct_rnd_periods()[0] ?? null;
    if ($latestPeriod && compare_rnd_periods($academicYear, $quarter, $store['settings']['latest_academic_year'], $store['settings']['latest_quarter']) < 0) {
        $store['settings']['latest_academic_year'] = $academicYear;
        $store['settings']['latest_quarter'] = $quarter;
    }

    if (save_rnd_rankings_store($store)) {
        if (function_exists('log_activity')) {
            log_activity('CREATE', 'R&D Rankings', "Rank {$rank}: {$department} ({$quarter} {$academicYear})", "Added new R&D ranking result for {$department}", $newId);
        }
        return ['success' => true, 'message' => "Successfully added R&D Ranking for {$department} (Rank {$rank}, {$quarter} {$academicYear})!", 'id' => $newId];
    }

    return ['success' => false, 'message' => 'Failed to write ranking record to storage.'];
}

/**
 * Update an existing ranking result
 */
function update_rnd_ranking(int $id, array $data): array {
    $store = load_rnd_rankings_store();
    $foundIndex = -1;

    foreach ($store['rankings'] as $idx => $r) {
        if ((int)($r['id'] ?? 0) === $id) {
            $foundIndex = $idx;
            break;
        }
    }

    if ($foundIndex === -1) {
        return ['success' => false, 'message' => 'Ranking record not found.'];
    }

    $existing = $store['rankings'][$foundIndex];

    $department     = trim((string)($data['department'] ?? $existing['department']));
    $departmentName = trim((string)($data['department_name'] ?? $existing['department_name']));
    $academicYear   = trim((string)($data['academic_year'] ?? $existing['academic_year']));
    $quarter        = strtoupper(trim((string)($data['quarter'] ?? $existing['quarter'])));
    $quarterLabel   = isset($data['quarter_label']) ? trim((string)$data['quarter_label']) : ($existing['quarter_label'] ?? '');
    $rank           = isset($data['rank']) ? max(1, (int)$data['rank']) : (int)($existing['rank'] ?? 1);
    $awardCategory  = trim((string)($data['award_category'] ?? ($existing['award_category'] ?? '')));
    $awardBadge     = trim((string)($data['award_badge'] ?? ($existing['award_badge'] ?? '')));
    $description    = isset($data['description']) ? trim((string)$data['description']) : ($existing['description'] ?? '');
    $status         = in_array($data['status'] ?? '', ['published', 'archived', 'draft'], true) ? $data['status'] : ($existing['status'] ?? 'published');
    $publicationDate= !empty($data['publication_date']) ? trim($data['publication_date']) : ($existing['publication_date'] ?? date('Y-m-d'));
    $score          = isset($data['score']) && is_numeric($data['score']) ? (float)$data['score'] : ($existing['score'] ?? null);

    // Duplicate check excluding self
    if (check_rnd_duplicate($department, $academicYear, $quarter, $id)) {
        return [
            'success' => false,
            'message' => "Another ranking result for Department '{$department}' in Academic Year '{$academicYear}' ({$quarter}) already exists."
        ];
    }

    // Achievements parsing
    $achievements = $existing['achievements'] ?? [];
    if (isset($data['achievements'])) {
        if (is_array($data['achievements'])) {
            $achievements = array_values(array_filter(array_map('trim', $data['achievements'])));
        } else {
            $lines = explode("\n", (string)$data['achievements']);
            $achievements = [];
            foreach ($lines as $line) {
                $line = trim($line, " \t\n\r\0\x0B•-*");
                if ($line !== '') {
                    $achievements[] = $line;
                }
            }
        }
    }

    $store['rankings'][$foundIndex] = array_merge($existing, [
        'academic_year'    => $academicYear,
        'quarter'          => $quarter,
        'quarter_label'    => $quarterLabel,
        'department'       => $department,
        'department_name'  => $departmentName,
        'rank'             => $rank,
        'award_category'   => $awardCategory,
        'award_badge'      => $awardBadge,
        'description'      => $description,
        'achievements'     => $achievements,
        'status'           => $status,
        'publication_date' => $publicationDate,
        'score'            => $score,
        'updated_at'       => date('Y-m-d H:i:s')
    ]);

    if (save_rnd_rankings_store($store)) {
        if (function_exists('log_activity')) {
            log_activity('UPDATE', 'R&D Rankings', "Rank {$rank}: {$department} ({$quarter} {$academicYear})", "Updated R&D ranking details for {$department}", $id);
        }
        return ['success' => true, 'message' => "Successfully updated R&D ranking for {$department}!"];
    }

    return ['success' => false, 'message' => 'Failed to save updated ranking record.'];
}

/**
 * Delete a ranking result
 */
function delete_rnd_ranking(int $id): array {
    $store = load_rnd_rankings_store();
    $foundIndex = -1;
    $dept = '';

    foreach ($store['rankings'] as $idx => $r) {
        if ((int)($r['id'] ?? 0) === $id) {
            $foundIndex = $idx;
            $dept = $r['department'] ?? "ID {$id}";
            break;
        }
    }

    if ($foundIndex === -1) {
        return ['success' => false, 'message' => 'Ranking record not found.'];
    }

    array_splice($store['rankings'], $foundIndex, 1);

    if (save_rnd_rankings_store($store)) {
        if (function_exists('log_activity')) {
            log_activity('DELETE', 'R&D Rankings', "Deleted Ranking #{$id}", "Removed R&D ranking record for {$dept}", $id);
        }
        return ['success' => true, 'message' => "Ranking result for {$dept} deleted successfully."];
    }

    return ['success' => false, 'message' => 'Failed to delete ranking record from store.'];
}

/**
 * Toggle status between published and archived
 */
function toggle_rnd_ranking_status(int $id): array {
    $store = load_rnd_rankings_store();
    foreach ($store['rankings'] as &$r) {
        if ((int)($r['id'] ?? 0) === $id) {
            $newStatus = ($r['status'] === 'published') ? 'archived' : 'published';
            $r['status'] = $newStatus;
            $r['updated_at'] = date('Y-m-d H:i:s');
            if (save_rnd_rankings_store($store)) {
                if (function_exists('log_activity')) {
                    log_activity('STATUS_CHANGE', 'R&D Rankings', "{$r['department']} status to {$newStatus}", "Toggled status to {$newStatus}", $id);
                }
                return ['success' => true, 'message' => "Ranking status changed to {$newStatus}.", 'new_status' => $newStatus];
            }
            return ['success' => false, 'message' => 'Failed to update status in store.'];
        }
    }
    return ['success' => false, 'message' => 'Ranking record not found.'];
}

/**
 * Handle HTTP POST actions for R&D Rankings
 */
function handle_rnd_request(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['rnd_action'] ?? $_POST['action'] ?? '';
    if (!in_array($action, ['add_rnd_ranking', 'update_rnd_ranking', 'delete_rnd_ranking', 'toggle_rnd_status'], true)) {
        return;
    }

    // CSRF check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (function_exists('verify_csrf_token') && !verify_csrf_token($csrfToken)) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_msg'] = 'Security validation failed (CSRF token mismatch). Please reload and try again.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php?tab=rnd_rankings'));
        exit;
    }

    $res = ['success' => false, 'message' => 'Unknown action'];

    switch ($action) {
        case 'add_rnd_ranking':
            $res = add_rnd_ranking($_POST);
            break;

        case 'update_rnd_ranking':
            $id = (int)($_POST['id'] ?? 0);
            $res = update_rnd_ranking($id, $_POST);
            break;

        case 'delete_rnd_ranking':
            $id = (int)($_POST['id'] ?? 0);
            $res = delete_rnd_ranking($id);
            break;

        case 'toggle_rnd_status':
            $id = (int)($_POST['id'] ?? 0);
            $res = toggle_rnd_ranking_status($id);
            break;
    }

    $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
    $_SESSION['flash_msg']  = $res['message'];

    $redirect = 'dashboard.php?tab=rnd_rankings';
    if (!empty($_POST['academic_year']) && !empty($_POST['quarter'])) {
        $redirect .= '&filter_year=' . urlencode($_POST['academic_year']) . '&filter_quarter=' . urlencode($_POST['quarter']);
    }
    header("Location: {$redirect}");
    exit;
}

// Auto-listen if accessed directly with action
if (isset($_POST['rnd_action']) || (isset($_POST['action']) && strpos($_POST['action'], 'rnd_') !== false)) {
    handle_rnd_request();
}

// API endpoint for dynamic frontend switching without page reload
if (isset($_GET['action']) && $_GET['action'] === 'get_rnd_rankings_json') {
    header('Content-Type: application/json');
    $year = !empty($_GET['academic_year']) ? trim($_GET['academic_year']) : null;
    $quarter = !empty($_GET['quarter']) ? trim($_GET['quarter']) : null;
    $status = $_GET['status'] ?? 'published';
    $data = get_rnd_rankings_by_period($year, $quarter, $status);
    $periods = get_distinct_rnd_periods($status === 'published');
    echo json_encode([
        'success' => true,
        'data' => $data,
        'periods' => $periods
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
