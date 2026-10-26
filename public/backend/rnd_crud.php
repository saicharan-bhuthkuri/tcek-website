<?php
/**
 * TCEK R&D Department Rankings Controller
 * Handles dynamic R&D Department Rankings management:
 * - Add, Edit, Delete, Archive, and Publish ranking results
 * - Supports historical periods (Academic Year + Quarter)
 * - Validates duplicate entries for same department & period
 * - Automatically sorts cards by ranking position
 * - 100% MySQL Database Powered Storage & Retrieval
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/sync.php';

/**
 * Ensure MySQL tables exist if database is connected
 */
function ensure_rnd_table_exists(?PDO $pdo = null): void {
    if (!$pdo) {
        $pdo = getDB();
    }
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

                CREATE TABLE IF NOT EXISTS `rnd_settings` (
                    `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    `setting_value` TEXT DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {}
    }
}

/**
 * Helper to parse achievements / tags (handles JSON or array)
 */
function parse_rnd_json_field($val): array {
    if (empty($val)) return [];
    if (is_array($val)) return $val;
    $decoded = json_decode((string)$val, true);
    if (is_array($decoded)) return $decoded;
    $lines = explode("\n", (string)$val);
    $arr = [];
    foreach ($lines as $l) {
        $l = trim($l, " \t\n\r\0\x0B•-*");
        if ($l !== '') $arr[] = $l;
    }
    return $arr;
}

/**
 * Compare two quarters/years to sort latest first
 * E.g., 2025-26 Q4 > 2025-26 Q3 > 2024-25 Q4
 */
function compare_rnd_periods(string $yearA, string $quarterA, string $yearB, string $quarterB): int {
    if ($yearA !== $yearB) {
        return strcmp($yearB, $yearA); // Descending year
    }
    $qNumA = (int) filter_var($quarterA, FILTER_SANITIZE_NUMBER_INT);
    $qNumB = (int) filter_var($quarterB, FILTER_SANITIZE_NUMBER_INT);
    return $qNumB <=> $qNumA; // Descending quarter
}

/**
 * Load all R&D rankings data and settings directly from MySQL
 */
function load_rnd_rankings_store(): array {
    global $pdo;

    $settings = [
        'latest_academic_year' => '2025-26',
        'latest_quarter'       => 'Q4',
        'auto_publish'         => true,
        'last_updated'         => date('Y-m-d')
    ];
    $rankings = [];

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);

            // Load settings
            $sStmt = $pdo->query("SELECT setting_key, setting_value FROM rnd_settings");
            if ($sStmt) {
                while ($row = $sStmt->fetch(PDO::FETCH_ASSOC)) {
                    $k = $row['setting_key'];
                    $v = $row['setting_value'];
                    if ($k === 'auto_publish') {
                        $settings[$k] = (bool)$v;
                    } else {
                        $settings[$k] = $v;
                    }
                }
            }

            // Load rankings
            $rStmt = $pdo->query("SELECT * FROM rnd_rankings ORDER BY rank ASC, id ASC");
            if ($rStmt) {
                while ($row = $rStmt->fetch(PDO::FETCH_ASSOC)) {
                    $row['id']            = (int)$row['id'];
                    $row['rank']          = (int)$row['rank'];
                    $row['score']         = isset($row['score']) ? (float)$row['score'] : null;
                    $row['achievements']  = parse_rnd_json_field($row['achievements']);
                    $row['criteria_tags'] = parse_rnd_json_field($row['criteria_tags']);
                    $rankings[] = $row;
                }
            }

            return ['settings' => $settings, 'rankings' => $rankings];
        } catch (Throwable $e) {
            error_log("Failed to load R&D rankings from MySQL: " . $e->getMessage());
        }
    }

    return ['settings' => $settings, 'rankings' => $rankings];
}

/**
 * Update R&D system settings in MySQL
 */
function update_rnd_settings(array $newSettings): bool {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);
            $stmt = $pdo->prepare("INSERT INTO rnd_settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
            foreach ($newSettings as $k => $v) {
                $stmt->execute([':k' => $k, ':v' => is_bool($v) ? ($v ? '1' : '0') : (string)$v]);
            }
            if (function_exists('record_content_update')) {
                record_content_update('rnd');
            }
            return true;
        } catch (Throwable $e) {
            error_log("Failed to update R&D settings in MySQL: " . $e->getMessage());
        }
    }
    return false;
}

/**
 * Get list of all distinct periods (academic year + quarter) sorted latest first
 */
function get_distinct_rnd_periods(bool $onlyPublished = false): array {
    global $pdo;

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);
            $sql = "
                SELECT academic_year, quarter, quarter_label, COUNT(*) AS `count`, MAX(publication_date) AS latest_date
                FROM rnd_rankings
            ";
            if ($onlyPublished) {
                $sql .= " WHERE status = 'published' ";
            }
            $sql .= " GROUP BY academic_year, quarter, quarter_label ";

            $stmt = $pdo->query($sql);
            $periods = [];
            if ($stmt) {
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $year = trim($r['academic_year'] ?? '');
                    $quarter = strtoupper(trim($r['quarter'] ?? ''));
                    if ($year === '' || $quarter === '') continue;

                    $periods[] = [
                        'academic_year' => $year,
                        'quarter'       => $quarter,
                        'quarter_label' => trim($r['quarter_label'] ?? ''),
                        'count'         => (int)$r['count'],
                        'latest_date'   => $r['latest_date'] ?? ''
                    ];
                }
            }

            usort($periods, function($a, $b) {
                return compare_rnd_periods($a['academic_year'], $a['quarter'], $b['academic_year'], $b['quarter']);
            });

            if (!empty($periods)) {
                $periods[0]['is_latest'] = true;
            }

            return $periods;
        } catch (Throwable $e) {
            error_log("Failed to query distinct R&D periods: " . $e->getMessage());
        }
    }

    return [];
}

/**
 * Get rankings for a specific period or default to the latest period
 * Automatically sorted by rank ASC
 */
function get_rnd_rankings_by_period(?string $academic_year = null, ?string $quarter = null, string $status = 'published'): array {
    global $pdo;

    if (empty($academic_year) || empty($quarter)) {
        $periods = get_distinct_rnd_periods($status === 'published');
        if (!empty($periods)) {
            $academic_year = $periods[0]['academic_year'];
            $quarter = $periods[0]['quarter'];
        } else {
            $academic_year = '2025-26';
            $quarter = 'Q4';
        }
    }

    $filtered = [];
    $quarter_label = '';

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);
            $sql = "SELECT * FROM rnd_rankings WHERE academic_year = :y AND quarter = :q";
            $params = [':y' => $academic_year, ':q' => $quarter];

            if ($status !== 'all') {
                $sql .= " AND status = :st";
                $params[':st'] = $status;
            }
            $sql .= " ORDER BY rank ASC, id ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {
                $r['id']            = (int)$r['id'];
                $r['rank']          = (int)$r['rank'];
                $r['score']         = isset($r['score']) ? (float)$r['score'] : null;
                $r['achievements']  = parse_rnd_json_field($r['achievements']);
                $r['criteria_tags'] = parse_rnd_json_field($r['criteria_tags']);
                if (empty($quarter_label) && !empty($r['quarter_label'])) {
                    $quarter_label = $r['quarter_label'];
                }
                $filtered[] = $r;
            }
        } catch (Throwable $e) {
            error_log("Failed to query R&D rankings by period: " . $e->getMessage());
        }
    }

    return [
        'academic_year' => $academic_year,
        'quarter'       => $quarter,
        'quarter_label' => $quarter_label,
        'rankings'      => $filtered
    ];
}

/**
 * Check if a department already has a ranking in the given year & quarter
 */
function check_rnd_duplicate(string $department, string $academic_year, string $quarter, ?int $excludeId = null): bool {
    global $pdo;

    $deptClean = trim($department);
    $yearClean = trim($academic_year);
    $quarterClean = trim($quarter);

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);
            $sql = "SELECT COUNT(*) FROM rnd_rankings WHERE LOWER(department) = LOWER(:d) AND LOWER(academic_year) = LOWER(:y) AND LOWER(quarter) = LOWER(:q)";
            $params = [':d' => $deptClean, ':y' => $yearClean, ':q' => $quarterClean];

            if ($excludeId !== null && $excludeId > 0) {
                $sql .= " AND id != :exId";
                $params[':exId'] = $excludeId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return ((int)$stmt->fetchColumn()) > 0;
        } catch (Throwable $e) {
            error_log("Failed to check duplicate R&D ranking: " . $e->getMessage());
        }
    }

    return false;
}

/**
 * Add a new ranking result directly to MySQL
 */
function add_rnd_ranking(array $data): array {
    global $pdo;

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

    // Achievements parsing
    $achievements = [];
    if (isset($data['achievements'])) {
        if (is_array($data['achievements'])) {
            $achievements = array_values(array_filter(array_map('trim', $data['achievements'])));
        } else {
            $lines = explode("\n", (string)$data['achievements']);
            foreach ($lines as $line) {
                $line = trim($line, " \t\n\r\0\x0B•-*");
                if ($line !== '') $achievements[] = $line;
            }
        }
    }
    if (empty($achievements)) {
        $achievements = ['Faculty Research Papers', 'Student Innovation Leads', 'NPTEL Enrolment Benchmark'];
    }

    $criteria_tags = [
        'Research & Innovation',
        'Patents & IPR',
        'Scopus & IEEE Publications',
        'NPTEL Honors',
        'IIC Initiatives'
    ];

    if (empty($awardCategory)) {
        $suffixes = [1 => 'st · Gold', 2 => 'nd · Bronze', 3 => 'rd · Bronze', 4 => 'th · Emerging', 5 => 'th · Emerging'];
        $awardCategory = ($rank) . ($suffixes[$rank] ?? 'th · Emerging');
    }
    if (empty($awardBadge)) {
        $badges = [1 => '🏆 Gold Champion', 2 => '🎖️ Bronze Honor', 3 => '🎖️ Bronze Honor', 4 => '🌱 Emerging', 5 => '🌱 Emerging'];
        $awardBadge = $badges[$rank] ?? '🌱 Emerging';
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);

            $stmt = $pdo->prepare("
                INSERT INTO rnd_rankings (
                    academic_year, quarter, quarter_label, department, department_name,
                    rank, award_category, award_badge, description, achievements, criteria_tags,
                    status, publication_date, score, created_at, updated_at
                ) VALUES (
                    :y, :q, :ql, :dept, :dept_name,
                    :rk, :ac, :ab, :desc, :ach, :ct,
                    :st, :pd, :sc, NOW(), NOW()
                )
            ");
            $saved = $stmt->execute([
                ':y'         => $academicYear,
                ':q'         => $quarter,
                ':ql'        => $quarterLabel,
                ':dept'      => $department,
                ':dept_name' => $departmentName,
                ':rk'        => $rank,
                ':ac'        => $awardCategory,
                ':ab'        => $awardBadge,
                ':desc'      => $description,
                ':ach'       => json_encode($achievements),
                ':ct'        => json_encode($criteria_tags),
                ':st'        => $status,
                ':pd'        => $publicationDate,
                ':sc'        => $score
            ]);

            if ($saved) {
                $newId = (int)$pdo->lastInsertId();
                if (function_exists('record_content_update')) {
                    record_content_update('rnd');
                }
                if (function_exists('log_activity')) {
                    log_activity('CREATE', 'R&D Rankings', "Rank {$rank}: {$department} ({$quarter} {$academicYear})", "Added new R&D ranking result for {$department}", $newId);
                }
                return ['success' => true, 'message' => "Successfully added R&D Ranking for {$department} (Rank {$rank}, {$quarter} {$academicYear})!", 'id' => $newId];
            }
        } catch (Throwable $e) {
            error_log("Failed to insert R&D ranking into MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Update an existing ranking result in MySQL
 */
function update_rnd_ranking(int $id, array $data): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid ranking ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);

            $stmt = $pdo->prepare("SELECT * FROM rnd_rankings WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                return ['success' => false, 'message' => 'Ranking record not found.'];
            }

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

            if (check_rnd_duplicate($department, $academicYear, $quarter, $id)) {
                return [
                    'success' => false,
                    'message' => "Another ranking result for Department '{$department}' in Academic Year '{$academicYear}' ({$quarter}) already exists."
                ];
            }

            // Achievements parsing
            $achievements = parse_rnd_json_field($existing['achievements']);
            if (isset($data['achievements'])) {
                if (is_array($data['achievements'])) {
                    $achievements = array_values(array_filter(array_map('trim', $data['achievements'])));
                } else {
                    $lines = explode("\n", (string)$data['achievements']);
                    $achievements = [];
                    foreach ($lines as $line) {
                        $line = trim($line, " \t\n\r\0\x0B•-*");
                        if ($line !== '') $achievements[] = $line;
                    }
                }
            }

            $upStmt = $pdo->prepare("
                UPDATE rnd_rankings SET
                    academic_year = :y, quarter = :q, quarter_label = :ql,
                    department = :dept, department_name = :dept_name,
                    rank = :rk, award_category = :ac, award_badge = :ab,
                    description = :desc, achievements = :ach,
                    status = :st, publication_date = :pd, score = :sc,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $saved = $upStmt->execute([
                ':y'         => $academicYear,
                ':q'         => $quarter,
                ':ql'        => $quarterLabel,
                ':dept'      => $department,
                ':dept_name' => $departmentName,
                ':rk'        => $rank,
                ':ac'        => $awardCategory,
                ':ab'        => $awardBadge,
                ':desc'      => $description,
                ':ach'       => json_encode($achievements),
                ':st'        => $status,
                ':pd'        => $publicationDate,
                ':sc'        => $score,
                ':id'        => $id
            ]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('rnd');
                }
                if (function_exists('log_activity')) {
                    log_activity('UPDATE', 'R&D Rankings', "Rank {$rank}: {$department} ({$quarter} {$academicYear})", "Updated R&D ranking details for {$department}", $id);
                }
                return ['success' => true, 'message' => "Successfully updated R&D ranking for {$department}!"];
            }
        } catch (Throwable $e) {
            error_log("Failed to update R&D ranking in MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Delete a ranking result in MySQL
 */
function delete_rnd_ranking(int $id): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid ranking ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);

            $stmt = $pdo->prepare("SELECT department FROM rnd_rankings WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $dept = $stmt->fetchColumn() ?: "ID #{$id}";

            $delStmt = $pdo->prepare("DELETE FROM rnd_rankings WHERE id = :id");
            $saved = $delStmt->execute([':id' => $id]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('rnd');
                }
                if (function_exists('log_activity')) {
                    log_activity('DELETE', 'R&D Rankings', "Deleted Ranking #{$id}", "Removed R&D ranking record for {$dept}", $id);
                }
                return ['success' => true, 'message' => "Ranking result for {$dept} deleted successfully."];
            }
        } catch (Throwable $e) {
            error_log("Failed to delete R&D ranking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Toggle status between published and archived in MySQL
 */
function toggle_rnd_ranking_status(int $id): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid ranking ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_rnd_table_exists($pdo);

            $stmt = $pdo->prepare("SELECT department, status FROM rnd_rankings WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return ['success' => false, 'message' => 'Ranking record not found.'];
            }

            $newStatus = ($row['status'] === 'published') ? 'archived' : 'published';
            $up = $pdo->prepare("UPDATE rnd_rankings SET status = :s, updated_at = NOW() WHERE id = :id");
            $saved = $up->execute([':s' => $newStatus, ':id' => $id]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('rnd');
                }
                if (function_exists('log_activity')) {
                    log_activity('STATUS_CHANGE', 'R&D Rankings', "{$row['department']} status to {$newStatus}", "Toggled status to {$newStatus}", $id);
                }
                return ['success' => true, 'message' => "Ranking status changed to {$newStatus}.", 'new_status' => $newStatus];
            }
        } catch (Throwable $e) {
            error_log("Failed to toggle R&D ranking status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
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
