<?php
/**
 * Master CRUD Controller for Trinity College of Engineering & Technology
 * Handles: Users, Gallery, Events, Notifications, Staff, Activity Logs, and Uploads
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/security/RateLimiter.php';
require_once __DIR__ . '/security/Validator.php';
require_once __DIR__ . '/security/ErrorHandler.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/departments_crud.php';
require_once __DIR__ . '/announcements_crud.php';
require_once __DIR__ . '/hero_crud.php';
require_once __DIR__ . '/sync.php';
require_once __DIR__ . '/settings_crud.php';

if (!function_exists('mb_strimwidth')) {
    /**
     * @param string|null $str
     * @param int $start
     * @param int $width
     * @param string $trimmarker
     * @return string
     */
    function mb_strimwidth($str, $start = 0, $width = 100, $trimmarker = '...') {
        if ($str === null) return '';
        $s = (string)$str;
        if (strlen($s) <= $width) return $s;
        return substr($s, $start, max(0, $width - strlen($trimmarker))) . $trimmarker;
    }
}

// ==============================================================
// 0. UNIVERSAL PAGINATION ENGINE
// ==============================================================

/**
 * Universal Pagination Meta Calculator
 *
 * @param int $total Total records
 * @param int $page Current page (1-based)
 * @param int $per_page Records per page
 * @return array
 */
function build_pagination_meta($total, $page = 1, $per_page = 10) {
    $total = max(0, (int)$total);
    $per_page = max(1, (int)$per_page);
    $total_pages = max(1, (int)ceil($total / $per_page));
    $page = max(1, min((int)$page, $total_pages));
    $offset = ($page - 1) * $per_page;
    $start_index = $total > 0 ? $offset + 1 : 0;
    $end_index = min($offset + $per_page, $total);

    return [
        'total'       => $total,
        'page'        => $page,
        'per_page'    => $per_page,
        'total_pages' => $total_pages,
        'offset'      => $offset,
        'start_index' => $start_index,
        'end_index'   => $end_index,
        'items'       => []
    ];
}

/**
 * Universal Pagination Bar Component HTML Renderer
 *
 * @param array $pagination Result from build_pagination_meta
 * @param array $extra_params Query overrides/defaults
 * @return string
 */
function render_pagination_bar($pagination, $extra_params = []) {
    if (empty($pagination)) {
        return '';
    }

    $current_page = (int)($pagination['page'] ?? 1);
    $total_pages  = (int)($pagination['total_pages'] ?? 1);
    $total_items  = (int)($pagination['total'] ?? 0);
    $start_index  = (int)($pagination['start_index'] ?? 0);
    $end_index    = (int)($pagination['end_index'] ?? 0);

    if ($total_items === 0) {
        return '';
    }

    // Build URL function preserving current GET params
    $base_params = $_GET ?? [];
    foreach ($extra_params as $k => $v) {
        if ($v === null || $v === '') {
            unset($base_params[$k]);
        } else {
            $base_params[$k] = $v;
        }
    }

    $build_url = function($pageNum) use ($base_params) {
        $p = $base_params;
        $p['page'] = $pageNum;
        return '?' . http_build_query($p);
    };

    ob_start();
    ?>
    <div class="pagination-footer-bar">
        <div class="pagination-info-text">
            Showing <strong><?php echo $start_index; ?>–<?php echo $end_index; ?></strong> of <strong><?php echo $total_items; ?></strong> records
        </div>

        <nav class="pagination-controls" aria-label="Table Navigation">
            <!-- Prev Button -->
            <?php if ($current_page <= 1): ?>
                <span class="pagination-btn disabled" aria-disabled="true">
                    <i class="fas fa-arrow-left"></i> Prev
                </span>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($build_url($current_page - 1)); ?>" class="pagination-btn" title="Go to previous page">
                    <i class="fas fa-arrow-left"></i> Prev
                </a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <div class="pagination-pages-group">
                <?php
                $range = 2;
                $page_numbers = [];
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i === 1 || $i === $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                        $page_numbers[] = $i;
                    }
                }

                $prev_num = null;
                foreach ($page_numbers as $num):
                    if ($prev_num !== null && $num - $prev_num > 1): ?>
                        <span class="pagination-ellipsis">&hellip;</span>
                    <?php endif;

                    if ($num === $current_page): ?>
                        <span class="pagination-page-btn active" aria-current="page"><?php echo $num; ?></span>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($build_url($num)); ?>" class="pagination-page-btn" title="Go to page <?php echo $num; ?>">
                            <?php echo $num; ?>
                        </a>
                    <?php endif;
                    $prev_num = $num;
                endforeach; ?>
            </div>

            <!-- Next Button -->
            <?php if ($current_page >= $total_pages): ?>
                <span class="pagination-btn disabled" aria-disabled="true">
                    Next <i class="fas fa-arrow-right"></i>
                </span>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($build_url($current_page + 1)); ?>" class="pagination-btn" title="Go to next page">
                    Next <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php
    return ob_get_clean();
}

// ==============================================================
// 1. ACTIVITY LOGS API
// ==============================================================

function get_activity_logs_store_file() {
    return __DIR__ . '/config/activity_logs_data.json';
}

function load_activity_logs_from_store() {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC, id DESC LIMIT 200");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {}
    }
    return [];
}

/**
 * @param array $logs
 * @return bool
 */
function save_activity_logs_to_store($logs) {
    // MySQL table activity_logs is the source of truth
    return true;
}

/**
 * @param string $action
 * @param string $module
 * @param string $record_name
 * @param string $description
 * @param int|null $record_id
 * @param string|null $admin_name
 * @return bool
 */
function add_json_activity_log($action, $module, $record_name, $description = '', $record_id = null, $admin_name = null) {
    if (function_exists('log_activity')) {
        return log_activity($action, $module, $record_name, $description, $record_id, $admin_name);
    }
    return true;
}

/**
 * Fetch activity logs directly from MySQL with filters and limits
 * 
 * @param string|null $module
 * @param string|null $action
 * @param int $limit
 * @return array
 */
function get_activity_logs($module = null, $action = null, $limit = 100) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $conditions = [];
        $params = [];

        if ($module && $module !== 'all') {
            $conditions[] = "module = :module";
            $params[':module'] = $module;
        }

        if ($action && $action !== 'all') {
            $conditions[] = "action = :action";
            $params[':action'] = $action;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $sql = "SELECT * FROM activity_logs {$whereClause} ORDER BY created_at DESC, id DESC LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to fetch activity logs from DB: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch paginated activity logs directly from MySQL with filters, search, and limits
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $module
 * @param string|null $action
 * @param string|null $search
 * @return array
 */
function get_activity_logs_paginated($page = 1, $per_page = 10, $module = null, $action = null, $search = null) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = [];
        $params = [];

        if ($module && $module !== 'all') {
            $conditions[] = "module = :module";
            $params[':module'] = $module;
        }
        if ($action && $action !== 'all') {
            $conditions[] = "action = :action";
            $params[':action'] = $action;
        }
        if (!empty($search)) {
            $conditions[] = "(record_name LIKE :s OR admin_name LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs {$whereClause}");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);

        $sql = "SELECT * FROM activity_logs {$whereClause} ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $meta['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $meta;
    } catch (PDOException $e) {
        error_log("Failed to fetch paginated activity logs from DB: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total activity logs matching optional filters
 *
 * @param string|null $module
 * @param string|null $action
 * @return int
 */
function get_activity_logs_count($module = null, $action = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = [];
        $params = [];
        if ($module && $module !== 'all') {
            $conditions[] = "module = :m";
            $params[':m'] = $module;
        }
        if ($action && $action !== 'all') {
            $conditions[] = "action = :a";
            $params[':a'] = $action;
        }
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

// ==============================================================
// 2. USERS MANAGEMENT
// ==============================================================

function get_users_store_file() {
    return __DIR__ . '/config/users_data.json';
}

function load_users_from_store() {
    return get_users(100);
}

/**
 * Save users to data store
 *
 * @param array $users
 * @return bool
 */
function save_users_to_store($users) {
    // MySQL table users is the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('users');
    }
    return true;
}

/**
 * Get users list from database
 *
 * @param int $limit
 * @return array
 */
function get_users($limit = 50) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, is_active, created_at, updated_at FROM users ORDER BY id ASC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            foreach ($res as &$u) {
                if (function_exists('is_protected_account') && is_protected_account($u)) {
                    $u['role'] = 'developer';
                    $u['is_protected'] = 1;
                    $u['is_active'] = 1;
                }
            }
            unset($u);
            return $res;
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch users from DB: " . $e->getMessage());
    }
    return [];
}

/**
 * @param string $username
 * @param string $password
 * @param string $full_name
 * @param string $email
 * @param string $role
 * @return array
 */
function add_user($username, $password, $full_name, $email, $role = 'staff') {
    global $pdo;
    $username  = trim($username);
    $full_name = trim($full_name);
    $email     = trim($email);
    $role      = strtolower(trim($role));
    $hash      = password_hash(trim($password), PASSWORD_BCRYPT);

    if (function_exists('is_protected_account') && is_protected_account(['username' => $username, 'email' => $email])) {
        return ['success' => false, 'message' => 'The system developer account identifier is permanently reserved.'];
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, full_name, email, role, is_active, created_at)
            VALUES (:u, :p, :f, :e, :r, 1, NOW())
        ");
        $stmt->execute([
            ':u' => $username,
            ':p' => $hash,
            ':f' => $full_name,
            ':e' => $email,
            ':r' => $role
        ]);
        $new_id = (int)$pdo->lastInsertId();

        log_activity('Added', 'Users', $username, "Created user with role '{$role}'", $new_id);
        if (function_exists('record_content_update')) {
            record_content_update('users');
        }
        return ['success' => true, 'message' => 'User created successfully!', 'id' => $new_id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * @param int $id
 * @return array|null
 */
function get_user_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO)) {
        return null;
    }
    try {
        $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, is_active, created_at, updated_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (function_exists('is_protected_account') && is_protected_account($user)) {
                $user['role'] = 'developer';
                $user['is_protected'] = 1;
                $user['is_active'] = 1;
            }
            return $user;
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch user by ID: " . $e->getMessage());
    }
    return null;
}

/**
 * @param int $id
 * @param array $data
 * @return array
 */
function update_user($id, $data) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    $existing = get_user_by_id($id);
    if (!$existing) {
        return ['success' => false, 'message' => 'User not found.'];
    }

    $fields = [];
    $params = [':id' => $id];

    if (isset($data['full_name'])) {
        $fields[] = "`full_name` = :full_name";
        $params[':full_name'] = trim($data['full_name']);
    }
    if (isset($data['email'])) {
        $email = trim($data['email']);
        if (function_exists('is_protected_account') && is_protected_account($existing) && strtolower($email) !== strtolower($existing['email'])) {
            return ['success' => false, 'message' => 'Developer account email cannot be modified.'];
        }
        $fields[] = "`email` = :email";
        $params[':email'] = $email;
    }
    if (isset($data['role'])) {
        $role = strtolower(trim($data['role']));
        if (function_exists('is_protected_account') && is_protected_account($existing) && $role !== 'developer') {
            return ['success' => false, 'message' => 'Developer account role cannot be demoted.'];
        }
        $fields[] = "`role` = :role";
        $params[':role'] = $role;
    }
    if (isset($data['password']) && !empty($data['password'])) {
        $fields[] = "`password` = :pwd";
        $params[':pwd'] = password_hash(trim($data['password']), PASSWORD_BCRYPT);
    }
    if (isset($data['is_active'])) {
        $isActive = (int)$data['is_active'];
        if (function_exists('is_protected_account') && is_protected_account($existing)) {
            $isActive = 1;
        }
        $fields[] = "`is_active` = :is_active";
        $params[':is_active'] = $isActive;
    }

    if (empty($fields)) {
        return ['success' => true, 'message' => 'No changes made.'];
    }

    try {
        $sql = "UPDATE `users` SET " . implode(', ', $fields) . ", `updated_at` = NOW() WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity('Updated', 'Users', $existing['username'], 'Updated user profile fields', $id);
        if (function_exists('record_content_update')) {
            record_content_update('users');
        }
        return ['success' => true, 'message' => 'User updated successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while updating the user.');
    }
}

/**
 * @param int $id
 * @return array
 */
function delete_user($id) {
    global $pdo;
    $id = (int)$id;
    $uName = 'User #' . $id;

    if (function_exists('is_protected_account') && is_protected_account($id)) {
        if (function_exists('log_activity')) {
            log_activity('Security Alert', 'Users', 'User #' . $id, 'Blocked attempt to delete protected system developer account', $id);
        }
        return ['success' => false, 'message' => 'The system developer account (Charan / saivortex.dev@gmail.com) is permanently protected and cannot be deleted.'];
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $st = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (function_exists('is_protected_account') && is_protected_account($row)) {
                if (function_exists('log_activity')) {
                    log_activity('Security Alert', 'Users', $row['username'], 'Blocked attempt to delete protected system developer account from database', $id);
                }
                return ['success' => false, 'message' => 'The system developer account (Charan / saivortex.dev@gmail.com) is permanently protected and cannot be deleted.'];
            }
            $uName = $row['username'];
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);

        log_activity('Deleted', 'Users', $uName, "Deleted user record", $id);
        if (function_exists('record_content_update')) {
            record_content_update('users');
        }
        return ['success' => true, 'message' => 'User removed successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 3. GALLERY (IMAGES & VIDEOS)
// ==============================================================

function get_gallery_store_file() {
    return __DIR__ . '/config/gallery_data.json';
}

function load_gallery_from_store() {
    return get_gallery_items(null, null, 100);
}

/**
 * Save gallery items to data store
 *
 * @param array $items
 * @return bool
 */
function save_gallery_to_store($items) {
    // MySQL table gallery is the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('gallery');
    }
    return true;
}

/**
 * Get gallery media items with optional type and category filters
 *
 * @param string|null $media_type
 * @param string|null $category
 * @param int $limit
 * @return array
 */
function get_gallery_items($media_type = null, $category = null, $limit = 60) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $conditions = [];
        $params = [];

        if ($media_type && in_array($media_type, ['image', 'video'])) {
            $conditions[] = "media_type = :media_type";
            $params[':media_type'] = $media_type;
        }

        if ($category && in_array($category, ['events', 'campus', 'milestones', 'press'])) {
            $conditions[] = "category = :category";
            $params[':category'] = $category;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $stmt = $pdo->prepare("SELECT * FROM gallery {$whereClause} ORDER BY id DESC LIMIT :limit");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to fetch gallery items from DB: " . $e->getMessage());
        return [];
    }
}

/**
 * @param string $title
 * @param string $media_type
 * @param string $category
 * @param string|null $file_path
 * @param string|null $video_url
 * @param string|null $description
 * @return array
 */
function add_gallery_item($title, $media_type, $category, $file_path = null, $video_url = null, $description = null) {
    global $pdo;
    $title       = trim($title);
    $media_type  = in_array($media_type, ['image', 'video']) ? $media_type : 'image';
    $category    = in_array($category, ['events', 'campus', 'milestones', 'press']) ? $category : 'events';
    $description = $description !== null ? trim($description) : null;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO gallery (title, media_type, category, file_path, video_url, description, created_at)
            VALUES (:title, :media_type, :category, :file_path, :video_url, :description, NOW())
        ");
        $stmt->execute([
            ':title'       => $title,
            ':media_type'  => $media_type,
            ':category'    => $category,
            ':file_path'   => $file_path,
            ':video_url'   => $video_url,
            ':description' => $description
        ]);
        $new_id = (int)$pdo->lastInsertId();

        log_activity('Added', 'Gallery', $title, "Added {$media_type} to '{$category}' category", $new_id);
        if (function_exists('record_content_update')) {
            record_content_update('gallery');
        }
        return ['success' => true, 'message' => 'Gallery media added successfully!', 'id' => $new_id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * @param int $id
 * @return array
 */
function delete_gallery_item($id) {
    global $pdo;
    $id = (int)$id;
    $title = 'Gallery Item #' . $id;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $st = $pdo->prepare("SELECT title, media_type, file_path FROM gallery WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $title = $row['title'];
            if (!empty($row['file_path']) && strpos($row['file_path'], 'uploads/') === 0) {
                $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $row['file_path'];
                if (file_exists($abs)) @unlink($abs);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
        $stmt->execute([':id' => $id]);

        log_activity('Deleted', 'Gallery', $title, "Deleted gallery record", $id);
        if (function_exists('record_content_update')) {
            record_content_update('gallery');
        }
        return ['success' => true, 'message' => 'Gallery item deleted.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 4. EVENTS (IMAGE, VIDEO, EVENT DETAILS) & EVENT MEDIA
// ==============================================================

function get_events_store_file() {
    return __DIR__ . '/config/events_data.json';
}

function load_events_from_store() {
    return get_events(100, false);
}

/**
 * Save events to data store
 *
 * @param array $events
 * @return bool
 */
function save_events_to_store($events) {
    // MySQL tables events and event_media are the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('events');
    }
    return true;
}

/**
 * Fetch events list from database with attached media
 *
 * @param int $limit
 * @param bool $featured_only
 * @return array
 */
function get_events($limit = 50, $featured_only = false) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $where = $featured_only ? "WHERE is_featured = 1 AND is_active = 1" : "WHERE is_active = 1";
        $stmt = $pdo->prepare("SELECT * FROM events {$where} ORDER BY event_date DESC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($events)) {
            return [];
        }

        $eventIds = array_column($events, 'id');
        $mediaByEvent = [];
        if (!empty($eventIds)) {
            $inClause = implode(',', array_map('intval', $eventIds));
            $mStmt = $pdo->query("SELECT * FROM event_media WHERE event_id IN ({$inClause}) ORDER BY id DESC");
            $allMedia = $mStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allMedia as $m) {
                $mediaByEvent[$m['event_id']][] = $m;
            }
        }

        foreach ($events as &$ev) {
            $ev['media_items'] = $mediaByEvent[$ev['id']] ?? [];
        }
        unset($ev);

        return $events;
    } catch (PDOException $e) {
        error_log("Failed to fetch events from DB: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch paginated events with search and featured filters directly from MySQL
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $search
 * @param bool $featured_only
 * @return array
 */
function get_events_paginated($page = 1, $per_page = 10, $search = null, $featured_only = false) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = ["is_active = 1"];
        $params = [];

        if ($featured_only) {
            $conditions[] = "is_featured = 1";
        }
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR venue LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }

        $where = "WHERE " . implode(" AND ", $conditions);

        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM events {$where}");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);

        $stmt = $pdo->prepare("SELECT * FROM events {$where} ORDER BY event_date DESC, id DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $eventIds = array_column($events, 'id');
        $mediaByEvent = [];
        if (!empty($eventIds)) {
            $inClause = implode(',', array_map('intval', $eventIds));
            $mStmt = $pdo->query("SELECT * FROM event_media WHERE event_id IN ({$inClause}) ORDER BY id DESC");
            $allMedia = $mStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allMedia as $m) {
                $mediaByEvent[$m['event_id']][] = $m;
            }
        }

        foreach ($events as &$ev) {
            $ev['media_items'] = $mediaByEvent[$ev['id']] ?? [];
        }
        unset($ev);

        $meta['items'] = $events;
        return $meta;
    } catch (PDOException $e) {
        error_log("Events DB pagination failed: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total events matching optional search and featured filters
 *
 * @param string|null $search
 * @param bool $featured_only
 * @return int
 */
function get_events_count($search = null, $featured_only = false) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = ["is_active = 1"];
        $params = [];
        if ($featured_only) $conditions[] = "is_featured = 1";
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR venue LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        $where = "WHERE " . implode(" AND ", $conditions);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM events {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/**
 * Retrieve a single event record by ID with attached media
 *
 * @param int $id
 * @return array|null
 */
function get_event_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO) || $id <= 0) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $ev = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ev) {
            $mStmt = $pdo->prepare("SELECT * FROM event_media WHERE event_id = :eid ORDER BY id DESC");
            $mStmt->execute([':eid' => $id]);
            $ev['media_items'] = $mStmt->fetchAll(PDO::FETCH_ASSOC);
            return $ev;
        }
    } catch (Throwable $e) {
        error_log("Failed to get event by id: " . $e->getMessage());
    }
    return null;
}

/**
 * Add a new event to the database
 *
 * @param string $title
 * @param string $event_date
 * @param string $event_time
 * @param string $description
 * @param string $venue
 * @param string|null $image_path
 * @param string|null $video_path
 * @param int $is_featured
 * @return array
 */
function add_event($title, $event_date, $event_time = '10:00 AM', $description = '', $venue = 'Trinity Campus Auditorium', $image_path = null, $video_path = null, $is_featured = 0) {
    global $pdo;
    $title       = trim($title);
    $venue       = trim($venue ?: 'Trinity Campus Auditorium');
    $event_time  = trim($event_time ?: '10:00 AM');
    $description = trim($description);

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO events (title, event_date, event_time, venue, description, image_path, video_path, is_featured, is_active, created_at)
            VALUES (:t, :d, :tm, :v, :desc, :img, :vid, :f, 1, NOW())
        ");
        $stmt->execute([
            ':t'    => $title,
            ':d'    => $event_date,
            ':tm'   => $event_time,
            ':v'    => $venue,
            ':desc' => $description,
            ':img'  => $image_path,
            ':vid'  => $video_path,
            ':f'    => (int)$is_featured
        ]);
        $id = (int)$pdo->lastInsertId();
        log_activity('Added', 'Events', $title, "Created college event scheduled for {$event_date} ({$event_time})", $id);
        if (function_exists('record_content_update')) {
            record_content_update('events');
        }
        return ['success' => true, 'message' => 'Event created successfully!', 'id' => $id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Update an existing event in the database
 *
 * @param int $id
 * @param string $title
 * @param string $event_date
 * @param string $event_time
 * @param string $venue
 * @param string $description
 * @param string|null $image_path
 * @param string|null $video_path
 * @param int $is_featured
 * @return array
 */
function update_event($id, $title, $event_date, $event_time = '10:00 AM', $venue = 'Trinity Campus Auditorium', $description = '', $image_path = null, $video_path = null, $is_featured = 0) {
    global $pdo;
    $id = (int)$id;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $sql = "UPDATE events SET title = :t, event_date = :d, event_time = :tm, venue = :v, description = :desc, is_featured = :f";
        $params = [
            ':id'   => $id,
            ':t'    => trim($title),
            ':d'    => $event_date,
            ':tm'   => trim($event_time ?: '10:00 AM'),
            ':v'    => trim($venue ?: 'Trinity Campus Auditorium'),
            ':desc' => trim($description),
            ':f'    => (int)$is_featured
        ];
        if ($image_path) {
            $sql .= ", image_path = :img";
            $params[':img'] = $image_path;
        }
        if ($video_path) {
            $sql .= ", video_path = :vid";
            $params[':vid'] = $video_path;
        }
        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity('Updated', 'Events', $title, "Updated event details", $id);
        if (function_exists('record_content_update')) {
            record_content_update('events');
        }
        return ['success' => true, 'message' => 'Event updated successfully!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Upload and associate media with an event
 *
 * @param int $event_id
 * @param array|string $file
 * @param string $media_title
 * @param string $media_description
 * @return array
 */
function add_event_media($event_id, $file, $media_title, $media_description = '') {
    global $pdo;

    $event_id          = (int)$event_id;
    $media_title       = trim($media_title);
    $media_description = trim($media_description);

    if ($event_id <= 0) {
        return ['success' => false, 'message' => 'Invalid Event selected.'];
    }

    if (is_string($file) && !empty($file)) {
        $file_path = $file;
        $file_type = preg_match('/\.(mp4|webm|avi|mov)$/i', $file) ? 'video' : 'image';
    } else {
        if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file was uploaded or file error occurred.'];
        }

        // Process file storage into uploads/images or uploads/videos
        $up = process_file_upload($file, $media_title, 'event_media', $media_description);
        if (!$up['success']) {
            return ['success' => false, 'message' => $up['message']];
        }

        $file_path = $up['data']['file_path'];
        $file_type = ($up['data']['file_type'] === 'video') ? 'video' : 'image';
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO event_media (event_id, media_type, file_path, media_title, media_description, created_at)
            VALUES (:eid, :mtype, :fpath, :mtitle, :mdesc, NOW())
        ");
        $stmt->execute([
            ':eid'   => $event_id,
            ':mtype' => $file_type,
            ':fpath' => $file_path,
            ':mtitle'=> $media_title,
            ':mdesc' => $media_description
        ]);
        $media_id = (int)$pdo->lastInsertId();

        // Also update parent event's primary image/video if empty
        if ($file_type === 'image') {
            $chk = $pdo->prepare("SELECT image_path FROM events WHERE id = :id");
            $chk->execute([':id' => $event_id]);
            $curr = $chk->fetch(PDO::FETCH_ASSOC);
            if ($curr && empty($curr['image_path'])) {
                $pdo->prepare("UPDATE events SET image_path = :img WHERE id = :id")->execute([':img' => $file_path, ':id' => $event_id]);
            }
        } elseif ($file_type === 'video') {
            $chk = $pdo->prepare("SELECT video_path FROM events WHERE id = :id");
            $chk->execute([':id' => $event_id]);
            $curr = $chk->fetch(PDO::FETCH_ASSOC);
            if ($curr && empty($curr['video_path'])) {
                $pdo->prepare("UPDATE events SET video_path = :vid WHERE id = :id")->execute([':vid' => $file_path, ':id' => $event_id]);
            }
        }

        log_activity('Uploaded', 'Events', $media_title, "Uploaded {$file_type} for Event #{$event_id}", $media_id);
        if (function_exists('record_content_update')) {
            record_content_update('event_media');
            record_content_update('events');
        }
        return ['success' => true, 'message' => 'Event details and media uploaded successfully!', 'id' => $media_id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Retrieve recent event media records
 *
 * @param int $limit
 * @return array
 */
function get_all_event_media($limit = 100) {
    global $pdo;
    $limit = (int)$limit;

    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT m.*, e.title AS event_title, e.event_date
            FROM event_media m
            LEFT JOIN events e ON m.event_id = e.id
            ORDER BY m.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Fetch paginated event media with event_id and search filters directly from MySQL
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $search
 * @param int|null $event_id
 * @return array
 */
function get_event_media_paginated($page = 1, $per_page = 10, $search = null, $event_id = null) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = [];
        $params = [];

        if (!empty($event_id)) {
            $conditions[] = "m.event_id = :eid";
            $params[':eid'] = (int)$event_id;
        }
        if (!empty($search)) {
            $conditions[] = "(m.media_title LIKE :s OR m.media_description LIKE :s OR e.title LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $cStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM event_media m
            LEFT JOIN events e ON m.event_id = e.id
            {$where}
        ");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);

        $stmt = $pdo->prepare("
            SELECT m.*, e.title AS event_title, e.event_date
            FROM event_media m
            LEFT JOIN events e ON m.event_id = e.id
            {$where}
            ORDER BY m.id DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $meta['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $meta;
    } catch (Throwable $e) {
        error_log("Event media DB pagination failed: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total event media items matching optional filters
 *
 * @param int|null $event_id
 * @param string|null $search
 * @return int
 */
function get_event_media_count($event_id = null, $search = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = [];
        $params = [];
        if (!empty($event_id)) {
            $conditions[] = "m.event_id = :eid";
            $params[':eid'] = (int)$event_id;
        }
        if (!empty($search)) {
            $conditions[] = "(m.media_title LIKE :s OR m.media_description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_media m {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/**
 * Update event media metadata
 *
 * @param int $media_id
 * @param string $media_title
 * @param string $media_description
 * @param int|null $event_id
 * @return array
 */
function update_event_media($media_id, $media_title, $media_description = '', $event_id = null) {
    global $pdo;
    $media_id          = (int)$media_id;
    $media_title       = trim($media_title);
    $media_description = trim($media_description);
    $event_id          = $event_id ? (int)$event_id : null;

    if ($media_id <= 0 || empty($media_title)) {
        return ['success' => false, 'message' => 'Invalid media ID or title.'];
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        if ($event_id) {
            $stmt = $pdo->prepare("UPDATE event_media SET media_title = :t, media_description = :d, event_id = :eid WHERE id = :id");
            $stmt->execute([':t' => $media_title, ':d' => $media_description, ':eid' => $event_id, ':id' => $media_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE event_media SET media_title = :t, media_description = :d WHERE id = :id");
            $stmt->execute([':t' => $media_title, ':d' => $media_description, ':id' => $media_id]);
        }
        log_activity('Updated', 'Events', $media_title, "Updated event media details", $media_id);
        if (function_exists('record_content_update')) {
            record_content_update('event_media');
        }
        return ['success' => true, 'message' => 'Media details updated successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete event media record and associated disk file
 *
 * @param int $media_id
 * @return array
 */
function delete_event_media($media_id) {
    global $pdo;
    $media_id = (int)$media_id;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $st = $pdo->prepare("SELECT * FROM event_media WHERE id = :id");
        $st->execute([':id' => $media_id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (!empty($row['file_path']) && strpos($row['file_path'], 'uploads/') === 0) {
                $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $row['file_path'];
                if (file_exists($abs)) @unlink($abs);
            }
            $pdo->prepare("DELETE FROM event_media WHERE id = :id")->execute([':id' => $media_id]);
            log_activity('Deleted', 'Events', $row['media_title'], "Deleted event media file", $media_id);
        }
        if (function_exists('record_content_update')) {
            record_content_update('event_media');
            record_content_update('events');
        }
        return ['success' => true, 'message' => 'Media file removed successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete event record and all associated media from database and disk
 *
 * @param int $id
 * @return array
 */
function delete_event($id) {
    global $pdo;
    $id = (int)$id;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $st = $pdo->prepare("SELECT title FROM events WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $title = $row ? $row['title'] : 'Event #' . $id;

        // Delete attached media files from disk
        $mStmt = $pdo->prepare("SELECT file_path FROM event_media WHERE event_id = :id");
        $mStmt->execute([':id' => $id]);
        while ($mRow = $mStmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($mRow['file_path']) && strpos($mRow['file_path'], 'uploads/') === 0) {
                $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $mRow['file_path'];
                if (file_exists($abs)) @unlink($abs);
            }
        }
        $pdo->prepare("DELETE FROM event_media WHERE event_id = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM events WHERE id = :id")->execute([':id' => $id]);

        log_activity('Deleted', 'Events', $title, "Deleted event from schedule", $id);
        if (function_exists('record_content_update')) {
            record_content_update('events');
        }
        return ['success' => true, 'message' => 'Event and its media deleted successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 5. NOTIFICATIONS & CIRCULARS (PDF, IMAGE, DOCX, MARQUEE TICKER)
// ==============================================================

function get_notifications_store_file() {
    return __DIR__ . '/config/notifications_data.json';
}

function load_notifications_from_store() {
    return get_notifications(100, false);
}

/**
 * Save notifications to data store
 *
 * @param array $notifications
 * @return bool
 */
function save_notifications_to_store($notifications) {
    // MySQL table notifications is the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('notifications');
        record_content_update('circulars');
    }
    return true;
}

/**
 * Fetch official notifications and circulars from database
 *
 * @param int $limit
 * @param bool $marquee_only
 * @return array
 */
function get_notifications($limit = 50, $marquee_only = false) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $where = $marquee_only ? "WHERE is_marquee = 1 AND is_active = 1" : "WHERE is_active = 1";
        $sql = "SELECT * FROM notifications {$where} ORDER BY is_marquee DESC, publish_date DESC, id DESC LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            foreach ($res as &$r) {
                if (empty($r['file_path']) && !empty($r['attachment_path'])) {
                    $r['file_path'] = $r['attachment_path'];
                }
            }
            unset($r);
            return $res;
        }
    } catch (PDOException $e) {
        error_log("Notifications DB query failed: " . $e->getMessage());
    }
    return [];
}

/**
 * Fetch paginated notifications/circulars directly from MySQL
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $search
 * @param bool $marquee_only
 * @return array
 */
function get_notifications_paginated($page = 1, $per_page = 10, $search = null, $marquee_only = false) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = ["is_active = 1"];
        $params = [];

        if ($marquee_only) {
            $conditions[] = "is_marquee = 1";
        }
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }

        $where = "WHERE " . implode(" AND ", $conditions);

        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications {$where}");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);

        $sql = "SELECT * FROM notifications {$where} ORDER BY is_marquee DESC, publish_date DESC, id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($res as &$r) {
            if (empty($r['file_path']) && !empty($r['attachment_path'])) {
                $r['file_path'] = $r['attachment_path'];
            }
        }
        unset($r);

        $meta['items'] = $res;
        return $meta;
    } catch (PDOException $e) {
        error_log("Notifications DB pagination failed: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total notifications matching search and marquee filters
 *
 * @param string|null $search
 * @param bool $marquee_only
 * @return int
 */
function get_notifications_count($search = null, $marquee_only = false) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = ["is_active = 1"];
        $params = [];
        if ($marquee_only) $conditions[] = "is_marquee = 1";
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        $where = "WHERE " . implode(" AND ", $conditions);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/**
 * Retrieve a single notification / circular record by ID
 *
 * @param int $id
 * @return array|null
 */
function get_notification_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO) || $id <= 0) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($item) {
            if (empty($item['file_path']) && !empty($item['attachment_path'])) {
                $item['file_path'] = $item['attachment_path'];
            }
            return $item;
        }
    } catch (PDOException $e) {
        error_log("Failed to get notification by id: " . $e->getMessage());
    }
    return null;
}

/**
 * Alias for backwards compatibility - fetch marquee items
 *
 * @param int $limit
 * @return array
 */
function get_scrollbar_items($limit = 30) {
    $items = get_notifications($limit, true);
    if (empty($items)) {
        return get_notifications($limit, false);
    }
    return $items;
}

/**
 * Alias for backwards compatibility - fetch active circulars
 *
 * @param int $limit
 * @param bool $active_only
 * @return array
 */
function get_notices($limit = 20, $active_only = true) {
    return get_notifications($limit, false);
}

/**
 * Add a new official circular / notification to database
 *
 * @param string $title
 * @param string $category
 * @param string $description
 * @param string $attachment_type
 * @param string|null $attachment_path
 * @param string|null $link_url
 * @param int $is_marquee
 * @param string|null $publish_date
 * @return array
 */
function add_notification($title, $category, $description, $attachment_type = 'none', $attachment_path = null, $link_url = null, $is_marquee = 0, $publish_date = null) {
    global $pdo;
    $title        = trim($title);
    $category     = trim($category ?: 'Circular');
    $description  = trim($description);
    $publish_date = !empty($publish_date) ? trim($publish_date) : date('Y-m-d');
    $is_marquee   = (int)$is_marquee;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (title, category, description, attachment_type, attachment_path, link_url, is_marquee, publish_date, is_active, created_at)
            VALUES (:t, :cat, :desc, :atype, :apath, :url, :marq, :pdate, 1, NOW())
        ");
        $stmt->execute([
            ':t'     => $title,
            ':cat'   => $category,
            ':desc'  => $description,
            ':atype' => $attachment_type,
            ':apath' => $attachment_path,
            ':url'   => !empty($link_url) ? trim($link_url) : null,
            ':marq'  => $is_marquee,
            ':pdate' => $publish_date
        ]);
        $new_id = (int)$pdo->lastInsertId();

        // Also insert into notices table for backward compatibility
        try {
            $nStmt = $pdo->prepare("
                INSERT INTO notices (id, title, category, description, attachment_type, attachment_path, is_marquee, publish_date, is_active, created_at)
                VALUES (:id, :t, :cat, :desc, :atype, :apath, :marq, :pdate, 1, NOW())
                ON DUPLICATE KEY UPDATE title=VALUES(title)
            ");
            $nStmt->execute([
                ':id'    => $new_id,
                ':t'     => $title,
                ':cat'   => $category,
                ':desc'  => $description,
                ':atype' => $attachment_type,
                ':apath' => $attachment_path,
                ':marq'  => $is_marquee,
                ':pdate' => $publish_date
            ]);
        } catch (Throwable $t) {}

        log_activity('Added', 'Circulars', $title, "Published official circular (Attachment: {$attachment_type})", $new_id);
        if (function_exists('record_content_update')) {
            record_content_update('notifications');
            record_content_update('circulars');
        }
        return ['success' => true, 'message' => 'Circular published successfully!', 'id' => $new_id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Update an existing circular / notification record
 *
 * @param int $id
 * @param string $title
 * @param string $publish_date
 * @param string $description
 * @param array|null $file
 * @param string $category
 * @param string|null $link_url
 * @param int $is_marquee
 * @return array
 */
function update_notification($id, $title, $publish_date, $description, $file = null, $category = 'Circular', $link_url = null, $is_marquee = 0) {
    global $pdo;
    $id = (int)$id;
    if ($id <= 0) return ['success' => false, 'message' => 'Invalid circular ID.'];

    $existing = get_notification_by_id($id);
    if (!$existing) return ['success' => false, 'message' => 'Circular not found.'];

    $title        = trim($title);
    $publish_date = !empty($publish_date) ? trim($publish_date) : ($existing['publish_date'] ?? date('Y-m-d'));
    $description  = trim($description);
    $category     = trim($category ?: ($existing['category'] ?? 'Circular'));
    $link_url     = !empty($link_url) ? trim($link_url) : ($existing['link_url'] ?? null);
    $is_marquee   = (int)$is_marquee;

    $attachment_path = $existing['attachment_path'] ?? ($existing['file_path'] ?? null);
    $attachment_type = $existing['attachment_type'] ?? 'none';

    // Check if new file was uploaded
    if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $up = process_file_upload($file, $title, 'circular', $description);
        if ($up['success']) {
            if (!empty($attachment_path) && strpos($attachment_path, 'uploads/') === 0) {
                $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $attachment_path;
                if (file_exists($abs)) @unlink($abs);
            }
            $attachment_path = $up['data']['file_path'];
            $attachment_type = $up['data']['file_type']; // 'pdf', 'image', 'docx'
        } else {
            return ['success' => false, 'message' => 'File replacement failed: ' . $up['message']];
        }
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE notifications 
            SET title = :t, category = :cat, description = :desc, attachment_type = :atype, 
                attachment_path = :apath, link_url = :url, is_marquee = :marq, publish_date = :pdate, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':id'    => $id,
            ':t'     => $title,
            ':cat'   => $category,
            ':desc'  => $description,
            ':atype' => $attachment_type,
            ':apath' => $attachment_path,
            ':url'   => $link_url,
            ':marq'  => $is_marquee,
            ':pdate' => $publish_date
        ]);

        try {
            $pdo->prepare("
                UPDATE notices 
                SET title = :t, category = :cat, description = :desc, attachment_type = :atype, 
                    attachment_path = :apath, is_marquee = :marq, publish_date = :pdate, updated_at = NOW()
                WHERE id = :id
            ")->execute([
                ':id'    => $id,
                ':t'     => $title,
                ':cat'   => $category,
                ':desc'  => $description,
                ':atype' => $attachment_type,
                ':apath' => $attachment_path,
                ':marq'  => $is_marquee,
                ':pdate' => $publish_date
            ]);
        } catch (Throwable $t) {}

        log_activity('Updated', 'Circulars', $title, "Updated circular details and file attachment", $id);
        if (function_exists('record_content_update')) {
            record_content_update('notifications');
            record_content_update('circulars');
        }
        return ['success' => true, 'message' => 'Circular updated successfully!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete a notification / circular and its file attachment
 *
 * @param int $id
 * @return array
 */
function delete_notification($id) {
    global $pdo;
    $id = (int)$id;

    $existing = get_notification_by_id($id);
    $title = $existing ? ($existing['title'] ?? 'Circular #' . $id) : 'Circular #' . $id;

    if ($existing) {
        $fPath = $existing['attachment_path'] ?? ($existing['file_path'] ?? null);
        if (!empty($fPath) && strpos($fPath, 'uploads/') === 0) {
            $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $fPath;
            if (file_exists($abs)) @unlink($abs);
        }
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        try {
            $pdo->prepare("DELETE FROM notices WHERE id = :id")->execute([':id' => $id]);
        } catch (Exception $e2) {}

        log_activity('Deleted', 'Circulars', $title, "Removed circular and associated document", $id);
        if (function_exists('record_content_update')) {
            record_content_update('notifications');
            record_content_update('circulars');
        }
        return ['success' => true, 'message' => 'Circular deleted successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 6. STAFF DIRECTORY (PROFILES & PROFILE IMAGES)
// ==============================================================

/**
 * Get staff members directory with optional department filter
 *
 * @param string|null $department
 * @param int $limit
 * @return array
 */
function get_staff($department = null, $limit = 50) {
    global $pdo;
    if (!($pdo instanceof PDO)) return [];

    try {
        $where = ($department && $department !== 'all') ? "WHERE department = :dept AND is_active = 1" : "WHERE is_active = 1";
        $stmt = $pdo->prepare("SELECT * FROM staff {$where} ORDER BY display_order ASC, id ASC LIMIT :limit");
        if ($department && $department !== 'all') {
            $stmt->bindValue(':dept', $department);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Add staff profile to directory
 *
 * @param string $full_name
 * @param string $designation
 * @param string $department
 * @param string $qualification
 * @param string|null $email
 * @param string|null $phone
 * @param string|null $profile_image
 * @param string|null $bio
 * @param int $display_order
 * @return array
 */
function add_staff($full_name, $designation, $department, $qualification, $email = null, $phone = null, $profile_image = null, $bio = null, $display_order = 0) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO staff (full_name, designation, department, qualification, email, phone, profile_image, bio, display_order, is_active, created_at)
            VALUES (:fn, :des, :dept, :qual, :em, :ph, :img, :bio, :ord, 1, NOW())
        ");
        $stmt->execute([
            ':fn'   => trim($full_name),
            ':des'  => trim($designation),
            ':dept' => trim($department),
            ':qual' => trim($qualification),
            ':em'   => trim($email),
            ':ph'   => trim($phone),
            ':img'  => $profile_image,
            ':bio'  => trim($bio),
            ':ord'  => (int)$display_order
        ]);
        $id = $pdo->lastInsertId();
        log_activity('Added', 'Staff', $full_name, "Added {$designation} in {$department} department", $id);
        return ['success' => true, 'message' => 'Staff profile created successfully!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Update staff profile details
 *
 * @param int $id
 * @param string $full_name
 * @param string $designation
 * @param string $department
 * @param string $qualification
 * @param string|null $email
 * @param string|null $phone
 * @param string|null $profile_image
 * @param string|null $bio
 * @return array
 */
function update_staff($id, $full_name, $designation, $department, $qualification, $email, $phone, $profile_image = null, $bio = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $sql = "UPDATE staff SET full_name = :fn, designation = :des, department = :dept, qualification = :qual, email = :em, phone = :ph, bio = :bio";
        $params = [
            ':id'   => $id,
            ':fn'   => trim($full_name),
            ':des'  => trim($designation),
            ':dept' => trim($department),
            ':qual' => trim($qualification),
            ':em'   => trim($email),
            ':ph'   => trim($phone),
            ':bio'  => trim($bio)
        ];
        if ($profile_image) {
            $sql .= ", profile_image = :img";
            $params[':img'] = $profile_image;
        }
        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity('Updated', 'Staff', $full_name, "Updated faculty profile information", $id);
        return ['success' => true, 'message' => 'Staff profile updated!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete staff profile from directory
 *
 * @param int $id
 * @return array
 */
function delete_staff($id) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $st = $pdo->prepare("SELECT full_name FROM staff WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        $name = $row ? $row['full_name'] : 'Staff #' . $id;

        $stmt = $pdo->prepare("DELETE FROM staff WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_activity('Deleted', 'Staff', $name, "Removed staff directory record", $id);
        return ['success' => true, 'message' => 'Staff profile deleted.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 7. UPLOADS STORAGE REGISTRY
// ==============================================================

/**
 * Get all uploads from registry with optional type filter
 *
 * @param string|null $type
 * @param int $limit
 * @return array
 */
function get_all_uploads($type = null, $limit = 100) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [
            ['id' => 1, 'file_name' => 'autonomous_notification_ay2026.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/autonomous_notification_ay2026.pdf', 'file_size' => 1425600, 'original_name' => 'autonomous_notification_ay2026.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-01 10:30:00'],
            ['id' => 2, 'file_name' => 'jntuh_exam_timetable_sem1.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/jntuh_exam_timetable_sem1.pdf', 'file_size' => 845200, 'original_name' => 'jntuh_exam_timetable_sem1.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-02 11:15:00'],
            ['id' => 3, 'file_name' => 'college_campus_aerial.jpg', 'file_type' => 'image', 'file_path' => 'uploads/images/college_campus_aerial.jpg', 'file_size' => 2548000, 'original_name' => 'college_campus_aerial.jpg', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-03 14:20:00'],
            ['id' => 4, 'file_name' => 'naac_accreditation_certificate.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/naac_accreditation_certificate.pdf', 'file_size' => 3120000, 'original_name' => 'naac_accreditation_certificate.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-04 09:40:00'],
            ['id' => 5, 'file_name' => 'sports_day_celebrations_2026.mp4', 'file_type' => 'video', 'file_path' => 'uploads/videos/sports_day_celebrations_2026.mp4', 'file_size' => 18450000, 'original_name' => 'sports_day_celebrations_2026.mp4', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-05 16:00:00'],
            ['id' => 6, 'file_name' => 'placements_brochure_2026.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/placements_brochure_2026.pdf', 'file_size' => 4210000, 'original_name' => 'placements_brochure_2026.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-06 12:10:00']
        ];
    }

    try {
        if ($type && in_array($type, ['pdf', 'image', 'video', 'docx'])) {
            $stmt = $pdo->prepare("SELECT * FROM uploads WHERE file_type = :type ORDER BY id DESC LIMIT :limit");
            $stmt->bindValue(':type', $type);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM uploads ORDER BY id DESC LIMIT :limit");
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if (empty($res)) {
            return [
                ['id' => 1, 'file_name' => 'autonomous_notification_ay2026.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/autonomous_notification_ay2026.pdf', 'file_size' => 1425600, 'original_name' => 'autonomous_notification_ay2026.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-01 10:30:00'],
                ['id' => 2, 'file_name' => 'jntuh_exam_timetable_sem1.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/jntuh_exam_timetable_sem1.pdf', 'file_size' => 845200, 'original_name' => 'jntuh_exam_timetable_sem1.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-02 11:15:00'],
                ['id' => 3, 'file_name' => 'college_campus_aerial.jpg', 'file_type' => 'image', 'file_path' => 'uploads/images/college_campus_aerial.jpg', 'file_size' => 2548000, 'original_name' => 'college_campus_aerial.jpg', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-03 14:20:00'],
                ['id' => 4, 'file_name' => 'naac_accreditation_certificate.pdf', 'file_type' => 'pdf', 'file_path' => 'uploads/pdfs/naac_accreditation_certificate.pdf', 'file_size' => 3120000, 'original_name' => 'naac_accreditation_certificate.pdf', 'uploaded_by' => 'Charan', 'created_at' => '2026-10-04 09:40:00']
            ];
        }
        return $res;
    } catch (PDOException $e) {
        return [];
    }
}

// ==============================================================
// 8. WORKSHOPS & TASKS
// ==============================================================

function get_workshops_store_file() {
    return __DIR__ . '/config/workshops_data.json';
}

function load_workshops_from_store() {
    return get_workshops(100);
}

/**
 * Save workshops to data store
 *
 * @param array $workshops
 * @return bool
 */
function save_workshops_to_store($workshops) {
    // MySQL table workshops is the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('workshops');
    }
    return true;
}

/**
 * Fetch upcoming workshops and tasks from database
 *
 * @param int $limit
 * @return array
 */
function get_workshops($limit = 50) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM workshops ORDER BY event_date ASC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to fetch workshops from DB: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch paginated workshops directly from MySQL
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $search
 * @param string|null $category
 * @return array
 */
function get_workshops_paginated($page = 1, $per_page = 10, $search = null, $category = null) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR instructor LIKE :s OR venue LIKE :s OR category LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        if (!empty($category) && $category !== 'all') {
            $conditions[] = "category = :cat";
            $params[':cat'] = trim($category);
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM workshops {$where}");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);
        $stmt = $pdo->prepare("SELECT * FROM workshops {$where} ORDER BY event_date ASC, id DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $meta['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $meta;
    } catch (PDOException $e) {
        error_log("Workshops pagination DB failed: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total workshops matching optional search and category filters
 *
 * @param string|null $search
 * @param string|null $category
 * @return int
 */
function get_workshops_count($search = null, $category = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = [];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR instructor LIKE :s OR venue LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        if (!empty($category) && $category !== 'all') {
            $conditions[] = "category = :cat";
            $params[':cat'] = trim($category);
        }
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM workshops {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/**
 * Retrieve workshop details by ID
 *
 * @param int $id
 * @return array|null
 */
function get_workshop_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO) || $id <= 0) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM workshops WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Add a new workshop or seminar
 *
 * @param string $title
 * @param string $instructor
 * @param string $category
 * @param string $event_date
 * @param string $venue
 * @param string $description
 * @param string $status
 * @return array
 */
function add_workshop($title, $instructor, $category, $event_date, $venue, $description, $status = 'UPCOMING') {
    global $pdo;
    $title       = trim($title);
    $instructor  = trim($instructor);
    $category    = trim($category ?: 'Technical');
    $venue       = trim($venue ?: 'TCEK Seminar Hall');
    $description = trim($description);
    $status      = trim($status ?: 'UPCOMING');

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO workshops (title, instructor, category, event_date, venue, description, status, created_at) VALUES (:t, :i, :c, :d, :v, :desc, :st, NOW())");
        $stmt->execute([
            ':t'    => $title,
            ':i'    => $instructor,
            ':c'    => $category,
            ':d'    => $event_date,
            ':v'    => $venue,
            ':desc' => $description,
            ':st'   => $status
        ]);
        $new_id = (int)$pdo->lastInsertId();

        log_activity('Added', 'Workshops', $title, "Created workshop/task in category '{$category}'", $new_id);
        if (function_exists('record_content_update')) {
            record_content_update('workshops');
        }
        return ['success' => true, 'message' => 'Workshop / Task created successfully!', 'id' => $new_id];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Update workshop details
 *
 * @param int $id
 * @param string $title
 * @param string $instructor
 * @param string $category
 * @param string $event_date
 * @param string $venue
 * @param string $description
 * @param string $status
 * @return array
 */
function update_workshop($id, $title, $instructor, $category, $event_date, $venue, $description, $status = 'UPCOMING') {
    global $pdo;
    $id          = (int)$id;
    $title       = trim($title);
    $instructor  = trim($instructor);
    $category    = trim($category ?: 'Technical');
    $venue       = trim($venue ?: 'TCEK Seminar Hall');
    $description = trim($description);
    $status      = trim($status ?: 'UPCOMING');

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE workshops 
            SET title = :t, instructor = :i, category = :c, event_date = :d, venue = :v, description = :desc, status = :st, updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute([
            ':id'   => $id,
            ':t'    => $title,
            ':i'    => $instructor,
            ':c'    => $category,
            ':d'    => $event_date,
            ':v'    => $venue,
            ':desc' => $description,
            ':st'   => $status
        ]);

        log_activity('Updated', 'Workshops', $title, "Updated workshop record", $id);
        if (function_exists('record_content_update')) {
            record_content_update('workshops');
        }
        return ['success' => true, 'message' => 'Workshop updated successfully!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete workshop record by ID
 *
 * @param int $id
 * @return array
 */
function delete_workshop($id) {
    global $pdo;
    $id = (int)$id;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $st = $pdo->prepare("SELECT title FROM workshops WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $title = $row ? $row['title'] : 'Workshop #' . $id;

        $stmt = $pdo->prepare("DELETE FROM workshops WHERE id = :id");
        $stmt->execute([':id' => $id]);

        log_activity('Deleted', 'Workshops', $title, "Deleted workshop record", $id);
        if (function_exists('record_content_update')) {
            record_content_update('workshops');
        }
        return ['success' => true, 'message' => 'Workshop removed.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 9. NEWS & PRESS CLIPPINGS MANAGEMENT
// ==============================================================

function get_news_store_file() {
    return __DIR__ . '/config/news_data.json';
}

function load_news_from_store() {
    return get_news(100);
}

/**
 * Save news clippings to data store
 *
 * @param array $news
 * @return bool
 */
function save_news_to_store($news) {
    // MySQL table news is the source of truth
    if (function_exists('record_content_update')) {
        record_content_update('news');
    }
    return true;
}

/**
 * Fetch published newspaper news clippings from database
 *
 * @param int $limit
 * @return array
 */
function get_news($limit = 50) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM news ORDER BY publish_date DESC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("News DB query failed: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch paginated news with search filter directly from MySQL
 *
 * @param int $page
 * @param int $per_page
 * @param string|null $search
 * @return array
 */
function get_news_paginated($page = 1, $per_page = 10, $search = null) {
    global $pdo;
    $per_page = max(1, (int)$per_page);
    $page = max(1, (int)$page);

    if (!($pdo instanceof PDO)) {
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }

    try {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR description LIKE :s OR source LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }

        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM news {$where}");
        foreach ($params as $k => $v) {
            $cStmt->bindValue($k, $v);
        }
        $cStmt->execute();
        $total = (int)$cStmt->fetchColumn();

        $meta = build_pagination_meta($total, $page, $per_page);

        $stmt = $pdo->prepare("SELECT * FROM news {$where} ORDER BY publish_date DESC, id DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $meta['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $meta['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $meta['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $meta;
    } catch (PDOException $e) {
        error_log("News DB pagination failed: " . $e->getMessage());
        $meta = build_pagination_meta(0, $page, $per_page);
        $meta['items'] = [];
        return $meta;
    }
}

/**
 * Count total news clippings matching optional search filter
 *
 * @param string|null $search
 * @return int
 */
function get_news_count($search = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return 0;
    }

    try {
        $conditions = [];
        $params = [];
        if (!empty($search)) {
            $conditions[] = "(title LIKE :s OR description LIKE :s)";
            $params[':s'] = '%' . trim($search) . '%';
        }
        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM news {$where}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

/**
 * Retrieve news clipping by ID
 *
 * @param int $id
 * @return array|null
 */
function get_news_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if (!($pdo instanceof PDO) || $id <= 0) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Add a news clipping or press release
 *
 * @param string $title
 * @param string $publish_date
 * @param string $description
 * @param array|string|null $image_file
 * @param string $source
 * @return array
 */
function add_news($title, $publish_date, $description, $image_file = null, $source = 'Press & Media') {
    global $pdo;
    $title = trim($title);
    $publish_date = !empty($publish_date) ? trim($publish_date) : date('Y-m-d');
    $description = trim($description);
    $image_path = null;

    if (is_string($image_file) && !empty($image_file)) {
        $image_path = $image_file;
    } elseif ($image_file && isset($image_file['error']) && $image_file['error'] === UPLOAD_ERR_OK) {
        $up = process_file_upload($image_file, $title, 'news', $description);
        if ($up['success']) {
            $image_path = $up['data']['file_path'];
        } else {
            return ['success' => false, 'message' => 'Image upload failed: ' . $up['message']];
        }
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO news (title, image_path, publish_date, description, source, status, created_at) VALUES (:t, :img, :d, :desc, :src, 'PUBLISHED', NOW())");
        $stmt->execute([
            ':t'    => $title,
            ':img'  => $image_path,
            ':d'    => $publish_date,
            ':desc' => $description,
            ':src'  => $source
        ]);
        $new_id = (int)$pdo->lastInsertId();

        $new_item = [
            'id'           => $new_id,
            'title'        => $title,
            'image_path'   => $image_path,
            'publish_date' => $publish_date,
            'description'  => $description,
            'source'       => $source,
            'status'       => 'PUBLISHED',
            'created_at'   => date('Y-m-d H:i:s')
        ];

        log_activity('Added', 'News', $title, "Uploaded newspaper news clipping", $new_id);
        if (function_exists('record_content_update')) {
            record_content_update('news');
        }
        return ['success' => true, 'message' => 'Newspaper news uploaded and published successfully!', 'id' => $new_id, 'data' => $new_item];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Update news clipping record
 *
 * @param int $id
 * @param string $title
 * @param string $publish_date
 * @param string $description
 * @param array|string|null $image_file
 * @param string $source
 * @return array
 */
function update_news($id, $title, $publish_date, $description, $image_file = null, $source = 'Press & Media') {
    global $pdo;
    $id = (int)$id;
    $title = trim($title);
    $publish_date = !empty($publish_date) ? trim($publish_date) : date('Y-m-d');
    $description = trim($description);

    $existing = get_news_by_id($id);
    $image_path = $existing ? ($existing['image_path'] ?? null) : null;

    if ($image_file && isset($image_file['error']) && $image_file['error'] === UPLOAD_ERR_OK) {
        $up = process_file_upload($image_file, $title, 'news', $description);
        if ($up['success']) {
            if (!empty($image_path) && strpos($image_path, 'uploads/') === 0) {
                $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $image_path;
                if (file_exists($abs)) @unlink($abs);
            }
            $image_path = $up['data']['file_path'];
        } else {
            return ['success' => false, 'message' => 'Image upload failed: ' . $up['message']];
        }
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("UPDATE news SET title = :t, image_path = :img, publish_date = :d, description = :desc, source = :src, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':t'    => $title,
            ':img'  => $image_path,
            ':d'    => $publish_date,
            ':desc' => $description,
            ':src'  => $source,
            ':id'   => $id
        ]);

        log_activity('Updated', 'News', $title, "Updated newspaper news clipping details", $id);
        if (function_exists('record_content_update')) {
            record_content_update('news');
        }
        return ['success' => true, 'message' => 'Newspaper news updated successfully!'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

/**
 * Delete news clipping and associated image file
 *
 * @param int $id
 * @return array
 */
function delete_news($id) {
    global $pdo;
    $id = (int)$id;
    $existing = get_news_by_id($id);
    $title = $existing ? ($existing['title'] ?? 'News #' . $id) : 'News #' . $id;

    if ($existing && !empty($existing['image_path']) && strpos($existing['image_path'], 'uploads/') === 0) {
        $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $existing['image_path'];
        if (file_exists($abs)) @unlink($abs);
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
        $stmt->execute([':id' => $id]);

        log_activity('Deleted', 'News', $title, "Deleted newspaper news clipping", $id);
        if (function_exists('record_content_update')) {
            record_content_update('news');
        }
        return ['success' => true, 'message' => 'Newspaper news clipping deleted successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'A database error occurred while processing your request.');
    }
}

// ==============================================================
// 10. DISPATCHER FOR ADMIN FORM SUBMISSIONS
// ==============================================================

// Direct GET handler for Excel Export (e.g. from export link/button in dashboard)
if (($_GET['action'] ?? '') === 'export_faculty_excel') {
    require_admin_login();
    export_faculty_excel($_GET);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    require_admin_login();

    // 1. Rate Limiting on Authenticated User Operations
    $rateCheck = RateLimiter::checkAuthenticatedLimit();
    if (!$rateCheck['allowed']) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_msg']  = "Too many requests. Please wait {$rateCheck['retry_after']} seconds before submitting again.";
        http_response_code(429);
        header('Retry-After: ' . (int)$rateCheck['retry_after']);
        header('Location: ../admin/dashboard.php');
        exit;
    }

    // 2. CSRF Token Verification
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_msg']  = 'Security validation failed (CSRF token mismatch). Please reload and try again.';
        http_response_code(403);
        header('Location: ../admin/dashboard.php');
        exit;
    }

    $action = (string)$_POST['action'];

    // --- NOTIFICATION & CIRCULAR ACTIONS ---
    if ($action === 'add_notification' || $action === 'add_notice' || $action === 'add_circular') {
        $validation = Validator::validate($_POST, [
            'title'        => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Circular Title'],
            'category'     => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'Circular', 'label' => 'Category'],
            'description'  => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'link_url'     => ['type' => 'url_or_path', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Link URL'],
            'publish_date' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d'), 'label' => 'Publish Date'],
            'is_marquee'   => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'Marquee Status'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=notifications');
            exit;
        }

        $v = $validation['data'];
        $title        = $v['title'];
        $category     = $v['category'];
        $description  = $v['description'];
        $link_url     = $v['link_url'];
        $publish_date = $v['publish_date'];
        $is_marquee   = $v['is_marquee'];
        $file_path    = null;
        $file_type    = 'none';

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['attachment'], $title, 'circular', $description);
            if ($up['success']) {
                $file_path = $up['data']['file_path'];
                $file_type = $up['data']['file_type'];
            } else {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'Attachment upload error: ' . $up['message'];
                header('Location: ../admin/dashboard.php?tab=notifications');
                exit;
            }
        } elseif (!empty($_POST['existing_file_path'])) {
            $existingPath = trim((string)$_POST['existing_file_path']);
            // Verify path safe against traversal
            if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $existingPath) || str_contains($existingPath, '..')) {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'Invalid existing file path.';
                header('Location: ../admin/dashboard.php?tab=notifications');
                exit;
            }
            $file_path = $existingPath;
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $file_type = in_array($ext, ['doc', 'docx']) ? 'docx' : ($ext === 'pdf' ? 'pdf' : (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'none'));
        }

        $res = add_notification($title, $category, $description, $file_type, $file_path, $link_url, $is_marquee, $publish_date);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=notifications');
        exit;
    }

    if ($action === 'update_circular' || $action === 'update_notification' || $action === 'edit_circular') {
        $validation = Validator::validate($_POST, [
            'id'           => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Circular ID'],
            'title'        => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Circular Title'],
            'category'     => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'Circular', 'label' => 'Category'],
            'description'  => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'link_url'     => ['type' => 'url_or_path', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Link URL'],
            'publish_date' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d'), 'label' => 'Publish Date'],
            'is_marquee'   => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'Marquee Status'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=notifications');
            exit;
        }

        $v = $validation['data'];
        $attachment = (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) ? $_FILES['attachment'] : null;

        $res = update_notification($v['id'], $v['title'], $v['publish_date'], $v['description'], $attachment, $v['category'], $v['link_url'], $v['is_marquee']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=notifications');
        exit;
    }

    if ($action === 'delete_notification' || $action === 'delete_notice' || $action === 'delete_circular') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Notification ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=notifications');
            exit;
        }

        $res = delete_notification($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=notifications');
        exit;
    }

    // --- EVENTS ACTIONS ---
    if ($action === 'add_event') {
        $validation = Validator::validate($_POST, [
            'title'       => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Event Title'],
            'event_date'  => ['type' => 'date', 'required' => true, 'label' => 'Event Date'],
            'event_time'  => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '10:00 AM', 'label' => 'Event Time'],
            'venue'       => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => 'Trinity Campus Auditorium', 'label' => 'Venue'],
            'description' => ['type' => 'string', 'required' => false, 'max_len' => 10000, 'default' => '', 'label' => 'Description'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=events');
            exit;
        }

        $v = $validation['data'];
        $res = add_event($v['title'], $v['event_date'], $v['event_time'], $v['description'], $v['venue']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    if ($action === 'upload_event_media' || $action === 'upload_event_details') {
        $validation = Validator::validate($_POST, [
            'event_id'          => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Event ID'],
            'media_title'       => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 255, 'label' => 'Media Title'],
            'media_description' => ['type' => 'string', 'required' => false, 'max_len' => 2000, 'default' => '', 'label' => 'Media Description'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Please select an image or video file to upload.';
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        $v = $validation['data'];
        $res = add_event_media($v['event_id'], $_FILES['media_file'], $v['media_title'], $v['media_description']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'delete_event_media') {
        $validation = Validator::validate($_POST, [
            'media_id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Media ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        $res = delete_event_media($validation['data']['media_id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'update_event_media' || $action === 'edit_event_media') {
        $validation = Validator::validate($_POST, [
            'media_id'          => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Media ID'],
            'event_id'          => ['type' => 'int', 'required' => false, 'min' => 1, 'default' => null, 'label' => 'Event ID'],
            'media_title'       => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 255, 'label' => 'Media Title'],
            'media_description' => ['type' => 'string', 'required' => false, 'max_len' => 2000, 'default' => '', 'label' => 'Media Description'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        $v = $validation['data'];
        $res = update_event_media($v['media_id'], $v['media_title'], $v['media_description'], $v['event_id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'update_event') {
        $validation = Validator::validate($_POST, [
            'id'          => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Event ID'],
            'title'       => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Event Title'],
            'event_date'  => ['type' => 'date', 'required' => true, 'label' => 'Event Date'],
            'event_time'  => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '10:00 AM', 'label' => 'Event Time'],
            'venue'       => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => 'Trinity Campus Auditorium', 'label' => 'Venue'],
            'description' => ['type' => 'string', 'required' => false, 'max_len' => 10000, 'default' => '', 'label' => 'Description'],
            'is_featured' => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'Featured Status'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=events');
            exit;
        }

        $v = $validation['data'];
        $res = update_event($v['id'], $v['title'], $v['event_date'], $v['event_time'], $v['venue'], $v['description'], null, null, $v['is_featured']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    if ($action === 'delete_event') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Event ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=events');
            exit;
        }

        $res = delete_event($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    // --- GALLERY ACTIONS (IMAGES & VIDEOS) ---
    if ($action === 'add_gallery') {
        $validation = Validator::validate($_POST, [
            'title'       => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 255, 'label' => 'Title'],
            'media_type'  => ['type' => 'enum', 'required' => false, 'allowed' => ['image', 'video'], 'default' => 'image', 'label' => 'Media Type'],
            'category'    => ['type' => 'enum', 'required' => false, 'allowed' => ['events', 'campus', 'milestones', 'press'], 'default' => 'events', 'label' => 'Category'],
            'description' => ['type' => 'string', 'required' => false, 'max_len' => 3000, 'default' => '', 'label' => 'Description'],
            'video_url'   => ['type' => 'url', 'required' => false, 'max_len' => 255, 'default' => null, 'label' => 'Video URL'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=gallery');
            exit;
        }

        $v = $validation['data'];
        $title       = $v['title'];
        $media_type  = $v['media_type'];
        $category    = $v['category'];
        $description = $v['description'];
        $video_url   = $v['video_url'];
        $file_path   = null;

        if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['gallery_file'], $title, 'gallery_' . $category, $description);
            if ($up['success']) {
                $file_path = $up['data']['file_path'];
                if ($up['data']['file_type'] === 'video') $media_type = 'video';
            } else {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'File upload error: ' . $up['message'];
                header('Location: ../admin/dashboard.php?tab=gallery');
                exit;
            }
        }

        $res = add_gallery_item($title, $media_type, $category, $file_path, $video_url, $description);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=gallery');
        exit;
    }

    if ($action === 'delete_gallery') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Gallery ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=gallery');
            exit;
        }

        $res = delete_gallery_item($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=gallery');
        exit;
    }

    // --- STAFF ACTIONS ---
    if ($action === 'add_staff') {
        $validation = Validator::validate($_POST, [
            'full_name'     => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 150, 'label' => 'Full Name'],
            'designation'   => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 100, 'label' => 'Designation'],
            'department'    => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 100, 'label' => 'Department'],
            'qualification' => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => '', 'label' => 'Qualification'],
            'email'         => ['type' => 'email', 'required' => false, 'default' => '', 'label' => 'Email'],
            'phone'         => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '', 'label' => 'Phone'],
            'bio'           => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Biography'],
            'display_order' => ['type' => 'int', 'required' => false, 'min' => 0, 'max' => 9999, 'default' => 0, 'label' => 'Display Order'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=staff');
            exit;
        }

        $v = $validation['data'];
        $profile_image = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['profile_image'], $v['full_name'] . ' Profile', 'staff_photo', 'Staff profile photo');
            if ($up['success']) {
                $profile_image = $up['data']['file_path'];
            } else {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'Profile image upload error: ' . $up['message'];
                header('Location: ../admin/dashboard.php?tab=staff');
                exit;
            }
        }

        $res = add_staff($v['full_name'], $v['designation'], $v['department'], $v['qualification'], $v['email'], $v['phone'], $profile_image, $v['bio'], $v['display_order']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=staff');
        exit;
    }

    if ($action === 'delete_staff') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Staff ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=staff');
            exit;
        }

        $res = delete_staff($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=staff');
        exit;
    }

    // --- USER MANAGEMENT ACTIONS ---
    if ($action === 'add_user') {
        require_admin_role();
        $validation = Validator::validate($_POST, [
            'username'  => ['type' => 'username', 'required' => true, 'label' => 'Username'],
            'password'  => ['type' => 'string', 'required' => true, 'min_len' => 8, 'max_len' => 100, 'label' => 'Password'],
            'full_name' => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 100, 'label' => 'Full Name'],
            'email'     => ['type' => 'email', 'required' => false, 'default' => '', 'label' => 'Email'],
            'role'      => ['type' => 'enum', 'required' => false, 'allowed' => ['developer', 'superadmin', 'super_admin', 'admin', 'editor', 'staff'], 'default' => 'staff', 'label' => 'Role'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=users');
            exit;
        }

        $v = $validation['data'];
        $requestedRole = strtolower($v['role']);
        // Only existing Developer / Superadmin can create another Developer / Superadmin
        if (in_array($requestedRole, ['developer', 'superadmin', 'super_admin'], true) && !is_developer_role()) {
            $requestedRole = 'admin';
        }

        $res = add_user($v['username'], $v['password'], $v['full_name'], $v['email'], $requestedRole);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=users');
        exit;
    }

    if ($action === 'delete_user') {
        require_admin_role();
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'User ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=users');
            exit;
        }

        $id = $validation['data']['id'];
        if ($id === (int)($_SESSION['tcek_admin_id'] ?? 0)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'You cannot delete your own account.';
        } elseif (function_exists('is_protected_account') && is_protected_account($id)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'The system developer account (Charan / saivortex.dev@gmail.com) is permanently protected and cannot be deleted.';
        } else {
            $res = delete_user($id);
            $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
            $_SESSION['flash_msg']  = $res['message'];
        }
        header('Location: ../admin/dashboard.php?tab=users');
        exit;
    }

    // --- DELETE UPLOADED FILE ---
    if ($action === 'delete_upload') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Upload ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=explorer');
            exit;
        }

        $id = $validation['data']['id'];
        $res = delete_uploaded_file($id);
        log_activity('Deleted', 'Uploads', 'File #' . $id, 'Deleted file registry and storage record');
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=explorer');
        exit;
    }

    // --- WORKSHOPS & TASKS ACTIONS ---
    if ($action === 'add_workshop') {
        $validation = Validator::validate($_POST, [
            'title'       => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Workshop Title'],
            'instructor'  => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => '', 'label' => 'Instructor'],
            'category'    => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'Technical', 'label' => 'Category'],
            'event_date'  => ['type' => 'date', 'required' => true, 'label' => 'Event Date'],
            'venue'       => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => 'TCEK Seminar Hall', 'label' => 'Venue'],
            'description' => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'status'      => ['type' => 'enum', 'required' => false, 'allowed' => ['UPCOMING', 'ACTIVE', 'COMPLETED'], 'default' => 'UPCOMING', 'label' => 'Status'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=workshops');
            exit;
        }

        $v = $validation['data'];
        $res = add_workshop($v['title'], $v['instructor'], $v['category'], $v['event_date'], $v['venue'], $v['description'], $v['status']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=workshops');
        exit;
    }

    if ($action === 'delete_workshop') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Workshop ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=workshops');
            exit;
        }

        $res = delete_workshop($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=workshops');
        exit;
    }

    // --- NEWS ACTIONS ---
    if ($action === 'add_news') {
        $validation = Validator::validate($_POST, [
            'title'        => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'News Title'],
            'publish_date' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d'), 'label' => 'Publish Date'],
            'description'  => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'source'       => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'Press & Media', 'label' => 'Source'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=news');
            exit;
        }

        $v = $validation['data'];
        $image_file = (isset($_FILES['newspaper_image']) && $_FILES['newspaper_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['newspaper_image'] : null;

        $res = add_news($v['title'], $v['publish_date'], $v['description'], $image_file, $v['source']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    if ($action === 'update_news') {
        $validation = Validator::validate($_POST, [
            'id'           => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'News ID'],
            'title'        => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'News Title'],
            'publish_date' => ['type' => 'date', 'required' => false, 'default' => date('Y-m-d'), 'label' => 'Publish Date'],
            'description'  => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'source'       => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'Press & Media', 'label' => 'Source'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=news');
            exit;
        }

        $v = $validation['data'];
        $image_file = (isset($_FILES['newspaper_image']) && $_FILES['newspaper_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['newspaper_image'] : null;

        $res = update_news($v['id'], $v['title'], $v['publish_date'], $v['description'], $image_file, $v['source']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    if ($action === 'delete_news') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'News ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=news');
            exit;
        }

        $res = delete_news($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    // --- MAIN SINGLE ANNOUNCEMENT TICKER ACTIONS ---
    if ($action === 'save_main_announcement' || $action === 'save_announcement_settings') {
        $announcement_text = trim($_POST['announcement_text'] ?? ($_POST['title'] ?? ($_POST['custom_text'] ?? '')));
        $link_url          = trim($_POST['link_url'] ?? '');
        $is_enabled        = (!empty($_POST['is_enabled']) && $_POST['is_enabled'] != '0') ? 1 : 0;
        $scrolling_speed   = isset($_POST['scrolling_speed']) ? max(10, min(300, (int)$_POST['scrolling_speed'])) : 60;
        $last_updated      = !empty($_POST['last_updated']) ? trim($_POST['last_updated']) : date('d F Y');
        $show_last_updated = (!empty($_POST['show_last_updated']) && $_POST['show_last_updated'] != '0') ? 1 : 0;

        if (empty($announcement_text)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Announcement message cannot be empty.';
            header('Location: ../admin/dashboard.php?tab=announcements');
            exit;
        }

        $res = save_main_announcement($announcement_text, $link_url, $is_enabled, $scrolling_speed, $last_updated, $show_last_updated);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    if ($action === 'add_announcement' || $action === 'add_scrollbar') {
        $validation = Validator::validate($_POST, [
            'title'     => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 1000, 'label' => 'Announcement Text'],
            'link_url'  => ['type' => 'url_or_path', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Link URL'],
            'is_active' => ['type' => 'int', 'required' => false, 'min' => 0, 'max' => 1, 'default' => 1, 'label' => 'Status']
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=announcements');
            exit;
        }

        $v = $validation['data'];
        $res = add_announcement_item($v['title'], $v['link_url'], $v['is_active']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    if ($action === 'update_announcement') {
        $validation = Validator::validate($_POST, [
            'id'        => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Announcement ID'],
            'title'     => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 1000, 'label' => 'Announcement Text'],
            'link_url'  => ['type' => 'url_or_path', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Link URL'],
            'is_active' => ['type' => 'int', 'required' => false, 'min' => 0, 'max' => 1, 'default' => 1, 'label' => 'Status']
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=announcements');
            exit;
        }

        $v = $validation['data'];
        $res = update_announcement_item($v['id'], $v['title'], $v['link_url'], $v['is_active']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    if ($action === 'delete_announcement' || $action === 'delete_scrollbar') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Announcement ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=announcements');
            exit;
        }

        $res = delete_announcement_item($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    if ($action === 'toggle_announcement_status') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Announcement ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=announcements');
            exit;
        }

        $res = toggle_announcement_item_status($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    if ($action === 'toggle_announcement_bar') {
        $res = toggle_announcement_bar_visibility();
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=announcements');
        exit;
    }

    // --- HOMEPAGE HERO SLIDER ACTIONS ---
    if ($action === 'save_hero_content') {
        require_admin_login();
        $fields = [
            'title'            => trim($_POST['title'] ?? ''),
            'subtitle'         => trim($_POST['subtitle'] ?? ''),
            'badge_left_text'  => trim($_POST['badge_left_text'] ?? ''),
            'badge_left_pulse' => (!empty($_POST['badge_left_pulse']) && $_POST['badge_left_pulse'] != '0') ? 1 : 0,
            'badge_right_text' => trim($_POST['badge_right_text'] ?? ''),
            'badge_right_icon' => trim($_POST['badge_right_icon'] ?? 'fas fa-certificate'),
            'cta_text'         => trim($_POST['cta_text'] ?? ''),
            'cta_link'         => trim($_POST['cta_link'] ?? ''),
            'slide_interval'   => isset($_POST['slide_interval']) ? (int)$_POST['slide_interval'] : 3600
        ];

        if (empty($fields['title'])) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Hero title cannot be empty.';
            header('Location: ../admin/dashboard.php?tab=hero');
            exit;
        }

        $res = update_hero_content($fields);
        if (function_exists('logActivity')) {
            logActivity('Updated', 'Hero Slider', 'Homepage Hero Content', 'Updated hero titles, badges, and CTA button');
        }
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    if ($action === 'add_hero_slide') {
        require_admin_login();
        $title     = trim($_POST['title'] ?? '');
        $is_active = (!empty($_POST['is_active']) && $_POST['is_active'] != '0') ? 1 : 0;
        $file_path = trim($_POST['file_path'] ?? '');

        // Handle uploaded file if present
        if (!empty($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = process_file_upload($_FILES['slide_file'], $title ?: 'Hero Slide', 'hero', 'Homepage Hero Slide');
            if ($uploadRes['success'] && !empty($uploadRes['data']['file_path'])) {
                $file_path = $uploadRes['data']['file_path'];
            } else {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'File upload error: ' . ($uploadRes['message'] ?? 'Could not upload file.');
                header('Location: ../admin/dashboard.php?tab=hero');
                exit;
            }
        }

        if (empty($file_path)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Please provide an image/video file or enter a valid file path.';
            header('Location: ../admin/dashboard.php?tab=hero');
            exit;
        }

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $media_type = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']) ? 'video' : 'image';

        $res = add_hero_slide($file_path, $title, $media_type, $is_active);
        if (function_exists('logActivity')) {
            logActivity('Added', 'Hero Slider', $title ?: 'New Slide', 'Added new hero slide: ' . $file_path);
        }
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    if ($action === 'edit_hero_slide') {
        require_admin_login();
        $id        = (int)($_POST['id'] ?? 0);
        $title     = trim($_POST['title'] ?? '');
        $is_active = (!empty($_POST['is_active']) && $_POST['is_active'] != '0') ? 1 : 0;
        $file_path = trim($_POST['file_path'] ?? '');

        if ($id <= 0) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Invalid slide identifier.';
            header('Location: ../admin/dashboard.php?tab=hero');
            exit;
        }

        $updates = [
            'title'     => $title,
            'is_active' => $is_active
        ];

        // Handle replacement upload if user selected a new file
        if (!empty($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = process_file_upload($_FILES['slide_file'], $title ?: 'Hero Slide', 'hero', 'Homepage Hero Slide');
            if ($uploadRes['success'] && !empty($uploadRes['data']['file_path'])) {
                $updates['file_path'] = $uploadRes['data']['file_path'];
            }
        } elseif (!empty($file_path)) {
            $updates['file_path'] = $file_path;
        }

        $res = update_hero_slide($id, $updates);
        if (function_exists('logActivity')) {
            logActivity('Updated', 'Hero Slider', 'Slide #' . $id, 'Modified hero slide details');
        }
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    if ($action === 'delete_hero_slide') {
        require_admin_login();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Invalid slide ID.';
            header('Location: ../admin/dashboard.php?tab=hero');
            exit;
        }

        $res = delete_hero_slide($id);
        if (function_exists('logActivity')) {
            logActivity('Deleted', 'Hero Slider', 'Slide #' . $id, 'Removed slide from hero carousel');
        }
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    if ($action === 'toggle_hero_slide') {
        require_admin_login();
        $id = (int)($_POST['id'] ?? 0);
        $res = toggle_hero_slide($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    if ($action === 'move_hero_slide') {
        require_admin_login();
        $id        = (int)($_POST['id'] ?? 0);
        $direction = trim($_POST['direction'] ?? 'up');
        $res = move_hero_slide($id, $direction);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=hero');
        exit;
    }

    // --- DEPARTMENT MANAGEMENT ACTIONS ---
    if ($action === 'add_department') {
        $validation = Validator::validate($_POST, [
            'dept_code'        => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 50, 'regex' => '/^[A-Za-z0-9\-\s&]+$/', 'label' => 'Department Code'],
            'name'             => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Department Name'],
            'degree_level'     => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => 'B.Tech', 'label' => 'Degree Level'],
            'intake'           => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '60 Seats', 'label' => 'Intake'],
            'duration'         => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '4 Years', 'label' => 'Duration'],
            'established_year' => ['type' => 'int', 'required' => false, 'min' => 1950, 'max' => 2030, 'default' => 2008, 'label' => 'Established Year'],
            'icon_class'       => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'fas fa-graduation-cap', 'label' => 'Icon Class'],
            'theme_class'      => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => 'theme-cse', 'label' => 'Theme Class'],
            'description'      => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'vision'           => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Vision'],
            'mission'          => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Mission'],
            'page_url'         => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Page URL'],
            'display_order'    => ['type' => 'int', 'required' => false, 'default' => 0, 'label' => 'Display Order'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=departments');
            exit;
        }

        $v = $validation['data'];
        $logo_path = !empty($_POST['banner_image']) ? trim($_POST['banner_image']) : null;
        if (isset($_FILES['dept_logo']) && $_FILES['dept_logo']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['dept_logo'], $v['name'] . ' Logo', 'department', 'Department logo image');
            if ($up['success']) {
                $logo_path = $up['data']['file_path'];
            }
        }
        $res = add_department($v['dept_code'], $v['name'], '', $v['degree_level'], $v['intake'], $v['duration'], $v['established_year'], $v['icon_class'], $v['theme_class'], $v['description'], $v['vision'], $v['mission'], $v['page_url'], $v['display_order'], $logo_path);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=departments');
        exit;
    }

    if ($action === 'update_department') {
        $validation = Validator::validate($_POST, [
            'id'               => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Department ID'],
            'dept_code'        => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 50, 'regex' => '/^[A-Za-z0-9\-\s&]+$/', 'label' => 'Department Code'],
            'name'             => ['type' => 'string', 'required' => true, 'min_len' => 3, 'max_len' => 255, 'label' => 'Department Name'],
            'degree_level'     => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => 'B.Tech', 'label' => 'Degree Level'],
            'intake'           => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '60 Seats', 'label' => 'Intake'],
            'duration'         => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '4 Years', 'label' => 'Duration'],
            'established_year' => ['type' => 'int', 'required' => false, 'min' => 1950, 'max' => 2030, 'default' => 2008, 'label' => 'Established Year'],
            'icon_class'       => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'fas fa-graduation-cap', 'label' => 'Icon Class'],
            'theme_class'      => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => 'theme-cse', 'label' => 'Theme Class'],
            'description'      => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Description'],
            'vision'           => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Vision'],
            'mission'          => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Mission'],
            'page_url'         => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => '', 'label' => 'Page URL'],
            'display_order'    => ['type' => 'int', 'required' => false, 'default' => 0, 'label' => 'Display Order'],
            'is_active'        => ['type' => 'boolean', 'required' => false, 'default' => 1, 'label' => 'Active Status'],
        ]);

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=departments');
            exit;
        }

        $v = $validation['data'];
        $logo_path = isset($_POST['banner_image']) && trim($_POST['banner_image']) !== '' ? trim($_POST['banner_image']) : null;
        if (isset($_FILES['dept_logo']) && $_FILES['dept_logo']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['dept_logo'], $v['name'] . ' Logo', 'department', 'Department logo image');
            if ($up['success']) {
                $logo_path = $up['data']['file_path'];
            }
        }
        $res = update_department($v['id'], $v['dept_code'], $v['name'], '', $v['degree_level'], $v['intake'], $v['duration'], $v['established_year'], $v['icon_class'], $v['theme_class'], $v['description'], $v['vision'], $v['mission'], $v['page_url'], $v['display_order'], $v['is_active'], $logo_path);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=departments');
        exit;
    }

    if ($action === 'delete_department') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Department ID']
        ]);
        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ../admin/dashboard.php?tab=departments');
            exit;
        }

        $res = delete_department($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=departments');
        exit;
    }

    // --- FACULTY MANAGEMENT ACTIONS ---
    if ($action === 'add_faculty') {
        $validation = Validator::validate($_POST, [
            'full_name'     => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 150, 'label' => 'Faculty Name'],
            'dept_code'     => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 50, 'label' => 'Department Code'],
            'designation'   => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => 'Assistant Professor', 'label' => 'Designation Title'],
            'role_category' => ['type' => 'string', 'required' => true, 'enum' => array_keys(TCEK_FACULTY_ROLES), 'label' => 'Role Category'],
            'is_hod'        => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'HOD Status'],
            'is_rnd'        => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'R&D Status'],
            'qualification' => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => 'M.Tech', 'label' => 'Qualification'],
            'jntuh_reg_id'  => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'N/A', 'label' => 'JNTUH Reg. ID'],
            'experience'    => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '5+ Years', 'label' => 'Experience'],
            'email'         => ['type' => 'email', 'required' => false, 'default' => '', 'label' => 'Email Address'],
            'phone'         => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '', 'label' => 'Phone Number'],
            'bio'           => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Biography'],
            'display_order' => ['type' => 'int', 'required' => false, 'default' => 0, 'label' => 'Display Order'],
        ]);

        $redirect_dept = trim($_POST['dept_code'] ?? '');
        $redirect_url = '../admin/dashboard.php?tab=faculty' . ($redirect_dept ? '&dept=' . urlencode($redirect_dept) : '');

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ' . $redirect_url);
            exit;
        }

        $v = $validation['data'];
        $photo_path = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['profile_image'], $v['full_name'] . ' Profile Photo', 'staff', 'Faculty profile image');
            if ($up['success']) {
                $photo_path = $up['data']['file_path'];
            }
        }

        $res = add_faculty_member($v['full_name'], $v['dept_code'], $v['designation'], $v['role_category'], $v['is_hod'], $v['is_rnd'], $v['qualification'], $v['jntuh_reg_id'], $v['experience'], $v['email'], $v['phone'], $photo_path, $v['bio'], $v['display_order']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ' . $redirect_url);
        exit;
    }

    if ($action === 'update_faculty') {
        $validation = Validator::validate($_POST, [
            'id'            => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Faculty ID'],
            'full_name'     => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 150, 'label' => 'Faculty Name'],
            'dept_code'     => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 50, 'label' => 'Department Code'],
            'designation'   => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => 'Assistant Professor', 'label' => 'Designation Title'],
            'role_category' => ['type' => 'string', 'required' => true, 'enum' => array_keys(TCEK_FACULTY_ROLES), 'label' => 'Role Category'],
            'is_hod'        => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'HOD Status'],
            'is_rnd'        => ['type' => 'boolean', 'required' => false, 'default' => 0, 'label' => 'R&D Status'],
            'qualification' => ['type' => 'string', 'required' => false, 'max_len' => 150, 'default' => 'M.Tech', 'label' => 'Qualification'],
            'jntuh_reg_id'  => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'N/A', 'label' => 'JNTUH Reg. ID'],
            'experience'    => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '5+ Years', 'label' => 'Experience'],
            'email'         => ['type' => 'email', 'required' => false, 'default' => '', 'label' => 'Email Address'],
            'phone'         => ['type' => 'string', 'required' => false, 'max_len' => 50, 'default' => '', 'label' => 'Phone Number'],
            'bio'           => ['type' => 'string', 'required' => false, 'max_len' => 5000, 'default' => '', 'label' => 'Biography'],
            'display_order' => ['type' => 'int', 'required' => false, 'default' => 0, 'label' => 'Display Order'],
            'is_active'     => ['type' => 'boolean', 'required' => false, 'default' => 1, 'label' => 'Active Status'],
        ]);

        $redirect_dept = trim($_POST['dept_code'] ?? '');
        $redirect_url = '../admin/dashboard.php?tab=faculty' . ($redirect_dept ? '&dept=' . urlencode($redirect_dept) : '');

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ' . $redirect_url);
            exit;
        }

        $v = $validation['data'];
        $photo_path = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['profile_image'], $v['full_name'] . ' Profile Photo', 'staff', 'Faculty profile image');
            if ($up['success']) {
                $photo_path = $up['data']['file_path'];
            }
        }

        $res = update_faculty_member($v['id'], $v['full_name'], $v['dept_code'], $v['designation'], $v['role_category'], $v['is_hod'], $v['is_rnd'], $v['qualification'], $v['jntuh_reg_id'], $v['experience'], $v['email'], $v['phone'], $photo_path, $v['bio'], $v['display_order'], $v['is_active']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ' . $redirect_url);
        exit;
    }

    if ($action === 'delete_faculty') {
        $validation = Validator::validate($_POST, [
            'id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Faculty ID']
        ]);
        $redirect_dept = trim($_POST['dept_code'] ?? '');
        $redirect_url = '../admin/dashboard.php?tab=faculty' . ($redirect_dept ? '&dept=' . urlencode($redirect_dept) : '');

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ' . $redirect_url);
            exit;
        }

        $res = delete_faculty_member($validation['data']['id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ' . $redirect_url);
        exit;
    }

    if ($action === 'assign_hod') {
        $validation = Validator::validate($_POST, [
            'faculty_id' => ['type' => 'int', 'required' => true, 'min' => 1, 'label' => 'Faculty Member'],
            'dept_code'  => ['type' => 'string', 'required' => true, 'min_len' => 2, 'max_len' => 50, 'label' => 'Department Code'],
        ]);
        $redirect_dept = trim($_POST['dept_code'] ?? '');
        $redirect_url = '../admin/dashboard.php?tab=faculty' . ($redirect_dept ? '&dept=' . urlencode($redirect_dept) : '');

        if (!$validation['valid']) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = reset($validation['errors']);
            header('Location: ' . $redirect_url);
            exit;
        }

        $v = $validation['data'];
        $res = assign_department_hod($v['dept_code'], $v['faculty_id']);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ' . $redirect_url);
        exit;
    }

    if ($action === 'export_faculty_excel') {
        export_faculty_excel($_POST);
        exit;
    }

    // --- GLOBAL 404 ERROR PAGE SETTINGS ACTIONS (DEVELOPER ONLY) ---
    if ($action === 'toggle_global_404' || $action === 'save_global_404_setting' || $action === 'update_global_404') {
        // Enforce strict Developer account access: Rejects unauthorized requests with HTTP 403 Forbidden
        require_developer_access();

        $admin = get_current_admin();
        if ($action === 'toggle_global_404') {
            $current = get_global_404_status();
            $new = $current ? 0 : 1;
            $res = update_global_404_setting($new, $admin['email'] ?? 'saivortex.dev@gmail.com');
        } else {
            $is_enabled = (!empty($_POST['is_enabled']) && $_POST['is_enabled'] !== '0') ? 1 : 0;
            $res = update_global_404_setting($is_enabled, $admin['email'] ?? 'saivortex.dev@gmail.com');
        }

        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];

        $redirectTab = !empty($_POST['redirect_tab']) ? trim($_POST['redirect_tab']) : 'developer_settings';
        header("Location: ../admin/dashboard.php?tab=" . urlencode($redirectTab));
        exit;
    }
}

