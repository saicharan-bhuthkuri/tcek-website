<?php
/**
 * TCEK Department & Faculty Management Controller
 * Handles: Department CRUD, Faculty Directory CRUD, Role & Designation Assignments,
 * HOD Single-Active Enforcement, Search/Filters, Excel SpreadsheetML Export with Formula Sanitization,
 * and Public Website Synchronization.
 * 100% MySQL Database Powered Storage & Retrieval
 */

if (!defined('TCEK_FACULTY_ROLES')) {
    define('TCEK_FACULTY_ROLES', [
        'HOD' => 'HOD – Head of Department',
        'Faculty – Junior (JR)' => 'Faculty – Junior (JR)',
        'Faculty – Senior (SR)' => 'Faculty – Senior (SR)',
        'R&D' => 'R&D',
        'R&D + Senior Faculty (SR)' => 'R&D + Senior Faculty (SR)',
        'R&D + Junior Faculty (JR)' => 'R&D + Junior Faculty (JR)'
    ]);
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/sync.php';

/**
 * Return array of valid faculty role designations
 */
function get_valid_faculty_roles(): array
{
    return TCEK_FACULTY_ROLES;
}

// ==============================================================
// 1. DEPARTMENT MANAGEMENT CORE FUNCTIONS (MYSQL POWERED)
// ==============================================================

/**
 * Retrieve all departments directly from MySQL, with attached faculty counts and active HOD name
 *
 * @param bool $active_only
 * @return array
 */
function get_departments($active_only = false)
{
    global $pdo;
    $departments = [];

    if ($pdo instanceof PDO) {
        try {
            $sql = "SELECT d.*, 
                    (SELECT COUNT(*) FROM staff s WHERE UPPER(s.dept_code) = UPPER(d.dept_code) AND s.is_active = 1) AS faculty_count,
                    (SELECT s.full_name FROM staff s WHERE UPPER(s.dept_code) = UPPER(d.dept_code) AND s.is_hod = 1 AND s.is_active = 1 LIMIT 1) AS hod_name
                    FROM departments d ";
            if ($active_only) {
                $sql .= " WHERE d.is_active = 1 ";
            }
            $sql .= " ORDER BY d.display_order ASC, d.name ASC";
            $stmt = $pdo->query($sql);
            if ($stmt) {
                $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($departments as &$d) {
                    $d['id'] = (int)$d['id'];
                    $d['is_active'] = (int)($d['is_active'] ?? 1);
                    $d['display_order'] = (int)($d['display_order'] ?? 0);
                    $d['faculty_count'] = (int)($d['faculty_count'] ?? 0);
                    if (!isset($d['tags'])) $d['tags'] = [];
                    elseif (is_string($d['tags'])) {
                        $d['tags'] = array_filter(array_map('trim', explode(',', $d['tags'])));
                    }
                    if (isset($d['gallery_images']) && is_string($d['gallery_images'])) {
                        $dec = json_decode($d['gallery_images'], true);
                        $d['gallery_images'] = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', $d['gallery_images'])));
                    }
                }
                unset($d);
            }
        } catch (Throwable $e) {
            error_log("Failed to query departments from MySQL: " . $e->getMessage());
        }
    }

    return $departments;
}

/**
 * Paginated department query for administration dashboard
 *
 * @param int $page
 * @param int $per_page
 * @param string $search
 * @return array
 */
function get_departments_paginated($page = 1, $per_page = 10, $search = '')
{
    $all = get_departments(false);
    if (!empty($search)) {
        $s = strtolower(trim($search));
        $all = array_filter($all, function ($d) use ($s) {
            return (stripos($d['name'] ?? '', $s) !== false ||
                stripos($d['dept_code'] ?? '', $s) !== false ||
                stripos($d['degree_level'] ?? '', $s) !== false);
        });
        $all = array_values($all);
    }
    $total = count($all);
    $pagination = build_pagination_meta($total, $page, $per_page);
    $pagination['items'] = array_values(array_slice($all, $pagination['offset'], $pagination['per_page']));
    return $pagination;
}

/**
 * Retrieve single department record by primary key ID
 */
function get_department_by_id($id)
{
    global $pdo;
    $id = (int)$id;
    if ($id <= 0) return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $d = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($d) {
                $d['id'] = (int)$d['id'];
                $d['is_active'] = (int)($d['is_active'] ?? 1);
                if (isset($d['tags']) && is_string($d['tags'])) {
                    $d['tags'] = array_filter(array_map('trim', explode(',', $d['tags'])));
                }
                if (isset($d['gallery_images']) && is_string($d['gallery_images'])) {
                    $dec = json_decode($d['gallery_images'], true);
                    $d['gallery_images'] = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', $d['gallery_images'])));
                }
                return $d;
            }
        } catch (Throwable $e) {
            error_log("Failed to query department by ID: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Retrieve single department by its unique dept_code
 */
function get_department_by_code($code)
{
    global $pdo;
    $code = strtoupper(trim((string)$code));
    if (empty($code)) return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE UPPER(dept_code) = :code LIMIT 1");
            $stmt->execute([':code' => $code]);
            $d = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($d) {
                $d['id'] = (int)$d['id'];
                $d['is_active'] = (int)($d['is_active'] ?? 1);
                if (isset($d['tags']) && is_string($d['tags'])) {
                    $d['tags'] = array_filter(array_map('trim', explode(',', $d['tags'])));
                }
                if (isset($d['gallery_images']) && is_string($d['gallery_images'])) {
                    $dec = json_decode($d['gallery_images'], true);
                    $d['gallery_images'] = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', $d['gallery_images'])));
                }
                return $d;
            }
        } catch (Throwable $e) {
            error_log("Failed to query department by code: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Retrieve single department by URL slug
 */
function get_department_by_slug($slug)
{
    global $pdo;
    $slug = strtolower(trim((string)$slug));
    if (empty($slug)) return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE LOWER(slug) = :slug LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $d = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($d) {
                $d['id'] = (int)$d['id'];
                $d['is_active'] = (int)($d['is_active'] ?? 1);
                if (isset($d['tags']) && is_string($d['tags'])) {
                    $d['tags'] = array_filter(array_map('trim', explode(',', $d['tags'])));
                }
                if (isset($d['gallery_images']) && is_string($d['gallery_images'])) {
                    $dec = json_decode($d['gallery_images'], true);
                    $d['gallery_images'] = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', $d['gallery_images'])));
                }
                return $d;
            }
        } catch (Throwable $e) {
            error_log("Failed to query department by slug: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Calculate total active faculty assigned to a specific department code
 */
function count_department_faculty($dept_code)
{
    global $pdo;
    $dept_code = strtoupper(trim((string)$dept_code));
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE UPPER(dept_code) = :code AND is_active = 1");
            $stmt->execute([':code' => $dept_code]);
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log("Failed to count department faculty: " . $e->getMessage());
        }
    }
    return 0;
}

/**
 * Create a new department with uniqueness checks in MySQL
 */
function add_department($dept_code, $name = '', $slug = '', $degree_level = 'B.Tech', $intake = '60 Seats', $duration = '4 Years', $established_year = 2008, $icon_class = 'fas fa-graduation-cap', $theme_class = 'theme-cse', $description = '', $vision = '', $mission = '', $page_url = '', $display_order = 0, $banner_image = null)
{
    global $pdo;

    $default_banner = 'assets/courses/cse.png';
    $tags = [];
    $syllabus_url = 'assets/Dept/peos_psos.docx';
    $peos_url = 'assets/Dept/peos_psos.docx';
    $gallery_images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];

    if (is_array($dept_code)) {
        $data = $dept_code;
        $dept_code = $data['dept_code'] ?? '';
        $name = $data['name'] ?? '';
        $slug = $data['slug'] ?? '';
        $degree_level = $data['degree_level'] ?? 'B.Tech';
        $intake = $data['intake'] ?? '60 Seats';
        $duration = $data['duration'] ?? '4 Years';
        $established_year = $data['established_year'] ?? 2008;
        $icon_class = $data['icon_class'] ?? 'fas fa-graduation-cap';
        $theme_class = $data['theme_class'] ?? 'theme-cse';
        $banner_image = $data['banner_image'] ?? $data['dept_logo'] ?? $data['logo'] ?? ($banner_image ?: $default_banner);
        $tags = $data['tags'] ?? [];
        $syllabus_url = $data['syllabus_url'] ?? 'assets/Dept/peos_psos.docx';
        $peos_url = $data['peos_url'] ?? 'assets/Dept/peos_psos.docx';
        if (isset($data['gallery_images'])) {
            $gallery_images = is_array($data['gallery_images']) ? $data['gallery_images'] : array_map('trim', explode(',', $data['gallery_images']));
        }
        $description = $data['description'] ?? '';
        $vision = $data['vision'] ?? '';
        $mission = $data['mission'] ?? '';
        $page_url = $data['page_url'] ?? '';
        $display_order = $data['display_order'] ?? 0;
    } else {
        $banner_image = $banner_image ?: $default_banner;
    }

    $dept_code = strtoupper(trim((string) $dept_code));
    $name = trim((string) $name);

    if (empty($dept_code) || empty($name)) {
        return ['success' => false, 'message' => 'Department Code and Department Name are required.'];
    }

    if ($pdo instanceof PDO) {
        // Uniqueness validation
        $check = $pdo->prepare("SELECT dept_code, name FROM departments WHERE UPPER(dept_code) = :c OR LOWER(name) = :n LIMIT 1");
        $check->execute([':c' => $dept_code, ':n' => strtolower($name)]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if (strtoupper($existing['dept_code']) === $dept_code) {
                return ['success' => false, 'message' => "Department code '{$dept_code}' is already registered."];
            }
            return ['success' => false, 'message' => "Department name '{$name}' is already registered."];
        }

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $dept_code));
        } else {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $slug));
        }

        if (empty($page_url)) {
            $page_url = 'department.php?slug=' . $slug;
        }

        $tagStr = is_array($tags) ? implode(', ', $tags) : (string)$tags;
        $galStr = is_array($gallery_images) ? json_encode($gallery_images) : (string)$gallery_images;

        try {
            $stmt = $pdo->prepare("INSERT INTO departments 
                (dept_code, name, slug, degree_level, intake, duration, established_year, icon_class, theme_class, banner_image, tags, syllabus_url, peos_url, gallery_images, description, vision, mission, page_url, display_order, is_active, created_at)
                VALUES 
                (:dept_code, :name, :slug, :degree_level, :intake, :duration, :established_year, :icon_class, :theme_class, :banner_image, :tags, :syll, :peos, :gal, :description, :vision, :mission, :page_url, :display_order, 1, NOW())");
            $stmt->execute([
                ':dept_code'        => $dept_code,
                ':name'             => $name,
                ':slug'             => $slug,
                ':degree_level'     => $degree_level ?: 'B.Tech',
                ':intake'           => $intake ?: '60 Seats',
                ':duration'         => $duration ?: '4 Years',
                ':established_year' => (int) ($established_year ?: 2008),
                ':icon_class'       => $icon_class ?: 'fas fa-graduation-cap',
                ':theme_class'      => $theme_class ?: 'theme-cse',
                ':banner_image'     => $banner_image ?: 'assets/courses/cse.png',
                ':tags'             => $tagStr,
                ':syll'             => $syllabus_url ?: 'assets/Dept/peos_psos.docx',
                ':peos'             => $peos_url ?: 'assets/Dept/peos_psos.docx',
                ':gal'              => $galStr,
                ':description'      => $description,
                ':vision'           => $vision,
                ':mission'          => $mission,
                ':page_url'         => $page_url,
                ':display_order'    => (int) $display_order
            ]);
            $new_id = (int) $pdo->lastInsertId();

            if (function_exists('record_content_update')) {
                record_content_update('departments');
                record_content_update('courses');
            }
            if (function_exists('log_activity')) {
                log_activity('Added', 'Departments', $name, "Created new department ({$dept_code})", $new_id);
            }
            return ['success' => true, 'message' => "Department '{$name}' ({$dept_code}) created successfully.", 'id' => $new_id];
        } catch (Throwable $e) {
            error_log("Failed to insert department into MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Update department details with uniqueness check in MySQL
 */
function update_department($id, $dept_code, $name = '', $slug = '', $degree_level = 'B.Tech', $intake = '60 Seats', $duration = '4 Years', $established_year = 2008, $icon_class = 'fas fa-graduation-cap', $theme_class = 'theme-cse', $description = '', $vision = '', $mission = '', $page_url = '', $display_order = 0, $is_active = 1, $banner_image = null)
{
    global $pdo;
    $id = (int) $id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid department ID.'];
    }

    $tags = null;
    $syllabus_url = null;
    $peos_url = null;
    $gallery_images = null;

    if (is_array($dept_code)) {
        $data = $dept_code;
        $dept_code = $data['dept_code'] ?? '';
        $name = $data['name'] ?? '';
        $slug = $data['slug'] ?? '';
        $degree_level = $data['degree_level'] ?? 'B.Tech';
        $intake = $data['intake'] ?? '60 Seats';
        $duration = $data['duration'] ?? '4 Years';
        $established_year = $data['established_year'] ?? 2008;
        $icon_class = $data['icon_class'] ?? 'fas fa-graduation-cap';
        $theme_class = $data['theme_class'] ?? 'theme-cse';
        $banner_image = $data['banner_image'] ?? $data['dept_logo'] ?? $data['logo'] ?? ($banner_image ?: null);
        $tags = isset($data['tags']) ? (is_array($data['tags']) ? implode(', ', $data['tags']) : (string)$data['tags']) : null;
        $syllabus_url = $data['syllabus_url'] ?? null;
        $peos_url = $data['peos_url'] ?? null;
        if (isset($data['gallery_images'])) {
            $gallery_images = is_array($data['gallery_images']) ? json_encode($data['gallery_images']) : (string)$data['gallery_images'];
        }
        $description = $data['description'] ?? '';
        $vision = $data['vision'] ?? '';
        $mission = $data['mission'] ?? '';
        $page_url = $data['page_url'] ?? '';
        $display_order = $data['display_order'] ?? 0;
        $is_active = $data['is_active'] ?? 1;
    }

    $dept_code = strtoupper(trim((string) $dept_code));
    $name = trim((string) $name);

    if ($pdo instanceof PDO) {
        $cur = $pdo->prepare("SELECT * FROM departments WHERE id = :id LIMIT 1");
        $cur->execute([':id' => $id]);
        $existing = $cur->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            return ['success' => false, 'message' => 'Department not found.'];
        }

        $old_code = strtoupper($existing['dept_code']);

        // Check duplicate code or name against other records
        $check = $pdo->prepare("SELECT id, dept_code, name FROM departments WHERE id != :id AND (UPPER(dept_code) = :c OR LOWER(name) = :n) LIMIT 1");
        $check->execute([':id' => $id, ':c' => $dept_code, ':n' => strtolower($name)]);
        $duplicate = $check->fetch(PDO::FETCH_ASSOC);

        if ($duplicate) {
            if (strtoupper($duplicate['dept_code']) === $dept_code) {
                return ['success' => false, 'message' => "Department code '{$dept_code}' is already used by another department."];
            }
            return ['success' => false, 'message' => "Department name '{$name}' is already used by another department."];
        }

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $dept_code));
        }
        if (empty($page_url)) {
            $page_url = 'department.php?slug=' . $slug;
        }

        try {
            $update_sql = "UPDATE departments SET
                dept_code = :dept_code,
                name = :name,
                slug = :slug,
                degree_level = :degree_level,
                intake = :intake,
                duration = :duration,
                established_year = :established_year,
                icon_class = :icon_class,
                theme_class = :theme_class,
                description = :description,
                vision = :vision,
                mission = :mission,
                page_url = :page_url,
                display_order = :display_order,
                is_active = :is_active";
            $update_params = [
                ':id'               => $id,
                ':dept_code'        => $dept_code,
                ':name'             => $name,
                ':slug'             => $slug,
                ':degree_level'     => $degree_level,
                ':intake'           => $intake,
                ':duration'         => $duration,
                ':established_year' => (int) $established_year,
                ':icon_class'       => $icon_class,
                ':theme_class'      => $theme_class,
                ':description'      => $description,
                ':vision'           => $vision,
                ':mission'          => $mission,
                ':page_url'         => $page_url,
                ':display_order'    => (int) $display_order,
                ':is_active'        => (int) $is_active
            ];

            if ($banner_image !== null) {
                $update_sql .= ", banner_image = :banner_image";
                $update_params[':banner_image'] = $banner_image;
            }
            if ($tags !== null) {
                $update_sql .= ", tags = :tags";
                $update_params[':tags'] = $tags;
            }
            if ($syllabus_url !== null) {
                $update_sql .= ", syllabus_url = :syllabus_url";
                $update_params[':syllabus_url'] = $syllabus_url;
            }
            if ($peos_url !== null) {
                $update_sql .= ", peos_url = :peos_url";
                $update_params[':peos_url'] = $peos_url;
            }
            if ($gallery_images !== null) {
                $update_sql .= ", gallery_images = :gallery_images";
                $update_params[':gallery_images'] = $gallery_images;
            }

            $update_sql .= ", updated_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($update_sql);
            $stmt->execute($update_params);

            // Sync faculty dept_code if department code changed
            if ($old_code && $old_code !== $dept_code) {
                $upd_staff = $pdo->prepare("UPDATE staff SET dept_code = :new_code WHERE UPPER(dept_code) = :old_code");
                $upd_staff->execute([':new_code' => $dept_code, ':old_code' => $old_code]);
            }

            if (function_exists('record_content_update')) {
                record_content_update('departments');
                record_content_update('courses');
            }
            if (function_exists('log_activity')) {
                log_activity('Updated', 'Departments', $name, "Updated department {$dept_code}", $id);
            }
            return ['success' => true, 'message' => "Department '{$name}' updated successfully."];
        } catch (Throwable $e) {
            error_log("Failed to update department in MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Delete a department - Strictly prevents accidental deletion if associated faculty exist
 */
function delete_department($id)
{
    global $pdo;
    $id = (int) $id;
    $dept = get_department_by_id($id);
    if (!$dept) {
        return ['success' => false, 'message' => 'Department not found.'];
    }

    $dept_code = strtoupper(trim($dept['dept_code'] ?? ''));
    $faculty_count = count_department_faculty($dept_code);

    if ($faculty_count > 0) {
        return [
            'success' => false,
            'message' => "Cannot delete department '{$dept['name']}' ({$dept_code}) because it has {$faculty_count} active faculty member(s) assigned. Please reassign or remove faculty first."
        ];
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if (function_exists('record_content_update')) {
                record_content_update('departments');
                record_content_update('courses');
            }
            if (function_exists('log_activity')) {
                log_activity('Deleted', 'Departments', $dept['name'], "Deleted department {$dept_code}", $id);
            }
            return ['success' => true, 'message' => "Department '{$dept['name']}' deleted successfully."];
        } catch (Throwable $e) {
            error_log("Failed to delete department from MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Demote any current active HOD of a department
 */
function demote_existing_department_hod($dept_code, $exclude_id = null)
{
    global $pdo;
    $dept_code = strtoupper(trim((string) $dept_code));
    if ($pdo instanceof PDO) {
        try {
            $sql = "UPDATE staff SET is_hod = 0, role_category = 'Faculty – Senior (SR)' WHERE UPPER(dept_code) = :code AND is_hod = 1";
            $params = [':code' => $dept_code];
            if ($exclude_id !== null && $exclude_id > 0) {
                $sql .= " AND id != :exId";
                $params[':exId'] = (int)$exclude_id;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } catch (Throwable $e) {
            error_log("Failed to demote department HOD in MySQL: " . $e->getMessage());
        }
    }
}

// ==============================================================
// 2. FACULTY DIRECTORY MANAGEMENT (MYSQL POWERED)
// ==============================================================

/**
 * Retrieve faculty members matching filter criteria directly from MySQL
 *
 * @param array $filters [
 *    'department'    => string ('all' or 'CSE', etc.)
 *    'search'        => string
 *    'role_category' => string
 *    'is_hod'        => string ('all', '1', '0')
 *    'is_rnd'        => string ('all', '1', '0')
 *    'is_active'     => int|null
 *    'sort_by'       => 'order'|'name'|'experience'|'id'
 *    'sort_dir'      => 'asc'|'desc'
 * ]
 * @return array
 */
function get_faculty_members($filters = [])
{
    global $pdo;
    $faculty = [];

    $dept_filter = trim((string) ($filters['department'] ?? ($filters['dept_code'] ?? 'all')));
    $search = trim((string) ($filters['search'] ?? ''));
    $role_filter = trim((string) ($filters['role_category'] ?? ($filters['role'] ?? 'all')));
    $hod_filter = trim((string) ($filters['is_hod'] ?? 'all'));
    $rnd_filter = trim((string) ($filters['is_rnd'] ?? 'all'));
    $active_filter = isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== 'all' ? (int) $filters['is_active'] : null;
    $sort_by = strtolower(trim((string) ($filters['sort_by'] ?? 'order')));
    $sort_dir = strtolower(trim((string) ($filters['sort_dir'] ?? 'asc')));

    if ($pdo instanceof PDO) {
        try {
            $where = [];
            $params = [];

            if ($dept_filter !== '' && strtolower($dept_filter) !== 'all') {
                $where[] = "UPPER(s.dept_code) = :dept";
                $params[':dept'] = strtoupper($dept_filter);
            }
            if ($search !== '') {
                $where[] = "(s.full_name LIKE :search OR s.email LIKE :search OR s.jntuh_reg_id LIKE :search OR s.qualification LIKE :search OR s.designation LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }
            if ($role_filter !== '' && strtolower($role_filter) !== 'all') {
                $where[] = "s.role_category = :role";
                $params[':role'] = $role_filter;
            }
            if ($hod_filter === '1') {
                $where[] = "s.is_hod = 1";
            } elseif ($hod_filter === '0') {
                $where[] = "s.is_hod = 0";
            }
            if ($rnd_filter === '1') {
                $where[] = "(s.is_rnd = 1 OR s.role_category LIKE '%R&D%')";
            } elseif ($rnd_filter === '0') {
                $where[] = "(s.is_rnd = 0 AND s.role_category NOT LIKE '%R&D%')";
            }
            if ($active_filter !== null) {
                $where[] = "s.is_active = :is_active";
                $params[':is_active'] = $active_filter;
            }

            $sql = "SELECT s.*, COALESCE(d.name, s.department) AS department_name FROM staff s 
                    LEFT JOIN departments d ON UPPER(d.dept_code) = UPPER(s.dept_code)";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $orderSql = " ORDER BY s.is_hod DESC, s.display_order ASC, s.full_name ASC";
            if ($sort_by === 'name') {
                $orderSql = " ORDER BY s.full_name " . ($sort_dir === 'desc' ? 'DESC' : 'ASC');
            } elseif ($sort_by === 'experience') {
                $orderSql = " ORDER BY s.experience " . ($sort_dir === 'desc' ? 'DESC' : 'ASC');
            } elseif ($sort_by === 'id') {
                $orderSql = " ORDER BY s.id " . ($sort_dir === 'desc' ? 'DESC' : 'ASC');
            }
            $sql .= $orderSql;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($faculty as &$f) {
                $f['id'] = (int)$f['id'];
                $f['is_hod'] = (int)($f['is_hod'] ?? 0);
                $f['is_rnd'] = (int)($f['is_rnd'] ?? 0);
                $f['is_active'] = (int)($f['is_active'] ?? 1);
                $f['display_order'] = (int)($f['display_order'] ?? 0);
            }
            unset($f);
        } catch (Throwable $e) {
            error_log("Failed to query faculty from MySQL: " . $e->getMessage());
        }
    }

    return $faculty;
}

/**
 * Retrieve paginated faculty directory results
 */
function get_faculty_paginated($page = 1, $per_page = 10, $filters = [])
{
    $all = get_faculty_members($filters);
    $total = count($all);
    $pagination = build_pagination_meta($total, $page, $per_page);
    $pagination['items'] = array_values(array_slice($all, $pagination['offset'], $pagination['per_page']));
    return $pagination;
}

/**
 * Retrieve faculty member by ID directly from MySQL
 */
function get_faculty_by_id($id)
{
    global $pdo;
    $id = (int)$id;
    if ($id <= 0) return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT s.*, COALESCE(d.name, s.department) AS department_name FROM staff s LEFT JOIN departments d ON UPPER(d.dept_code) = UPPER(s.dept_code) WHERE s.id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $f = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($f) {
                $f['id'] = (int)$f['id'];
                $f['is_hod'] = (int)($f['is_hod'] ?? 0);
                $f['is_rnd'] = (int)($f['is_rnd'] ?? 0);
                $f['is_active'] = (int)($f['is_active'] ?? 1);
                $f['display_order'] = (int)($f['display_order'] ?? 0);
                return $f;
            }
        } catch (Throwable $e) {
            error_log("Failed to query faculty by ID: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Public website integration: Retrieve all active faculty for a specific department
 */
function get_department_faculty($dept_code, $active_only = true)
{
    return get_faculty_members([
        'dept_code' => $dept_code,
        'is_active' => $active_only ? 1 : null,
        'sort_by'   => 'order'
    ]);
}

/**
 * Retrieve current active HOD of a department
 */
function get_department_hod($dept_code)
{
    $faculty = get_department_faculty($dept_code, true);
    foreach ($faculty as $f) {
        if (!empty($f['is_hod'])) {
            return $f;
        }
    }
    return !empty($faculty) ? $faculty[0] : null;
}

/**
 * Add a new faculty member directly in MySQL
 */
function add_faculty_member($full_name, $dept_code = '', $designation = '', $role_category = 'Faculty – Junior (JR)', $is_hod = 0, $is_rnd = 0, $qualification = 'M.Tech', $jntuh_reg_id = 'N/A', $experience = '5+ Years', $email = '', $phone = '', $profile_image = null, $bio = '', $display_order = 0)
{
    global $pdo;

    if (is_array($full_name)) {
        $data = $full_name;
        $full_name = $data['full_name'] ?? '';
        $dept_code = $data['dept_code'] ?? '';
        $designation = $data['designation'] ?? '';
        $role_category = $data['role_category'] ?? 'Faculty – Junior (JR)';
        $is_hod = $data['is_hod'] ?? 0;
        $is_rnd = $data['is_rnd'] ?? 0;
        $qualification = $data['qualification'] ?? 'M.Tech';
        $jntuh_reg_id = $data['jntuh_reg_id'] ?? 'N/A';
        $experience = $data['experience'] ?? '5+ Years';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $profile_image = $data['profile_image'] ?? null;
        $bio = $data['bio'] ?? '';
        $display_order = $data['display_order'] ?? 0;
    }

    $full_name = trim((string) $full_name);
    $dept_code = strtoupper(trim((string) $dept_code));
    $designation = trim((string) $designation);
    $role_category = trim((string) $role_category);
    $qualification = trim((string) $qualification);
    $jntuh_reg_id = trim((string) $jntuh_reg_id);
    $experience = trim((string) $experience);
    $email = trim((string) $email);
    $phone = trim((string) $phone);
    $bio = trim((string) $bio);

    if (empty($full_name) || empty($dept_code)) {
        return ['success' => false, 'message' => 'Faculty Name and Department are required.'];
    }

    if (empty($designation)) {
        $designation = ($role_category === 'HOD') ? 'Head of Department' : 'Assistant Professor';
    }

    if ($role_category === 'HOD' || !empty($is_hod)) {
        $is_hod = 1;
        $role_category = 'HOD';
        demote_existing_department_hod($dept_code);
    } else {
        $is_hod = 0;
    }

    if (stripos($role_category, 'R&D') !== false || !empty($is_rnd)) {
        $is_rnd = 1;
    } else {
        $is_rnd = 0;
    }

    if (empty($profile_image) || $profile_image === 'assets/Dept/faculty-avatar.png') {
        $profile_image = null;
    }

    $dept_obj = get_department_by_code($dept_code);
    $full_dept_name = $dept_obj['name'] ?? $dept_code;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("INSERT INTO staff 
                (full_name, dept_code, department, designation, role_category, is_hod, is_rnd, qualification, jntuh_reg_id, experience, email, phone, profile_image, bio, display_order, is_active, created_at)
                VALUES 
                (:full_name, :dept_code, :department, :designation, :role_category, :is_hod, :is_rnd, :qualification, :jntuh_reg_id, :experience, :email, :phone, :profile_image, :bio, :display_order, 1, NOW())");
            $stmt->execute([
                ':full_name'     => $full_name,
                ':dept_code'     => $dept_code,
                ':department'    => $full_dept_name,
                ':designation'   => $designation,
                ':role_category' => $role_category,
                ':is_hod'        => $is_hod,
                ':is_rnd'        => $is_rnd,
                ':qualification' => $qualification ?: 'M.Tech',
                ':jntuh_reg_id'  => $jntuh_reg_id ?: 'N/A',
                ':experience'    => $experience ?: '5+ Years',
                ':email'         => $email ?: (strtolower(preg_replace('/[^a-z0-9]/', '', $full_name)) . '@tcek.in'),
                ':phone'         => $phone ?: '',
                ':profile_image' => $profile_image,
                ':bio'           => $bio ?: "Faculty member in the Department of {$full_dept_name} at TCEK.",
                ':display_order' => (int) $display_order
            ]);
            $new_id = (int) $pdo->lastInsertId();

            if (function_exists('record_content_update')) {
                record_content_update('faculty');
                record_content_update('departments');
            }
            if (function_exists('log_activity')) {
                log_activity('Added', 'Faculty', $full_name, "Added faculty member to {$dept_code} ({$role_category})", $new_id);
            }
            return ['success' => true, 'message' => "Faculty member '{$full_name}' added to {$dept_code} successfully.", 'id' => $new_id];
        } catch (Throwable $e) {
            error_log("Failed to insert staff into MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Update faculty details directly in MySQL
 */
function update_faculty_member($id, $full_name, $dept_code = '', $designation = '', $role_category = 'Faculty – Junior (JR)', $is_hod = 0, $is_rnd = 0, $qualification = '', $jntuh_reg_id = '', $experience = '', $email = '', $phone = '', $profile_image = null, $bio = '', $display_order = 0, $is_active = 1)
{
    global $pdo;
    $id = (int) $id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid faculty ID.'];
    }

    if (is_array($full_name)) {
        $data = $full_name;
        $full_name = $data['full_name'] ?? '';
        $dept_code = $data['dept_code'] ?? '';
        $designation = $data['designation'] ?? '';
        $role_category = $data['role_category'] ?? 'Faculty – Junior (JR)';
        $is_hod = $data['is_hod'] ?? 0;
        $is_rnd = $data['is_rnd'] ?? 0;
        $qualification = $data['qualification'] ?? '';
        $jntuh_reg_id = $data['jntuh_reg_id'] ?? '';
        $experience = $data['experience'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $profile_image = $data['profile_image'] ?? null;
        $bio = $data['bio'] ?? '';
        $display_order = $data['display_order'] ?? 0;
        $is_active = $data['is_active'] ?? 1;
    }

    $full_name = trim((string) $full_name);
    $dept_code = strtoupper(trim((string) $dept_code));
    $designation = trim((string) $designation);
    $role_category = trim((string) $role_category);
    $qualification = trim((string) $qualification);
    $jntuh_reg_id = trim((string) $jntuh_reg_id);
    $experience = trim((string) $experience);
    $email = trim((string) $email);
    $phone = trim((string) $phone);
    $bio = trim((string) $bio);

    if ($pdo instanceof PDO) {
        $cur = $pdo->prepare("SELECT * FROM staff WHERE id = :id LIMIT 1");
        $cur->execute([':id' => $id]);
        $existing = $cur->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            return ['success' => false, 'message' => 'Faculty member not found.'];
        }

        if ($role_category === 'HOD' || !empty($is_hod)) {
            $is_hod = 1;
            $role_category = 'HOD';
            demote_existing_department_hod($dept_code, $id);
        } else {
            $is_hod = 0;
        }

        if (stripos($role_category, 'R&D') !== false || !empty($is_rnd)) {
            $is_rnd = 1;
        } else {
            $is_rnd = 0;
        }

        $existing_img = $existing['profile_image'] ?? null;
        if ($existing_img === 'assets/Dept/faculty-avatar.png') {
            $existing_img = null;
        }
        $final_img = (!empty($profile_image)) ? $profile_image : $existing_img;

        $dept_obj = get_department_by_code($dept_code);
        $full_dept_name = $dept_obj['name'] ?? $dept_code;

        try {
            $stmt = $pdo->prepare("UPDATE staff SET
                full_name = :full_name,
                dept_code = :dept_code,
                department = :department,
                designation = :designation,
                role_category = :role_category,
                is_hod = :is_hod,
                is_rnd = :is_rnd,
                qualification = :qualification,
                jntuh_reg_id = :jntuh_reg_id,
                experience = :experience,
                email = :email,
                phone = :phone,
                profile_image = :profile_image,
                bio = :bio,
                display_order = :display_order,
                is_active = :is_active,
                updated_at = NOW()
                WHERE id = :id");
            $stmt->execute([
                ':id'            => $id,
                ':full_name'     => $full_name,
                ':dept_code'     => $dept_code,
                ':department'    => $full_dept_name,
                ':designation'   => $designation,
                ':role_category' => $role_category,
                ':is_hod'        => $is_hod,
                ':is_rnd'        => $is_rnd,
                ':qualification' => $qualification,
                ':jntuh_reg_id'  => $jntuh_reg_id,
                ':experience'    => $experience,
                ':email'         => $email,
                ':phone'         => $phone,
                ':profile_image' => $final_img,
                ':bio'           => $bio,
                ':display_order' => (int) $display_order,
                ':is_active'     => (int) $is_active
            ]);

            if (function_exists('record_content_update')) {
                record_content_update('faculty');
                record_content_update('departments');
            }
            if (function_exists('log_activity')) {
                log_activity('Updated', 'Faculty', $full_name, "Updated faculty profile in {$dept_code} ({$role_category})", $id);
            }
            return ['success' => true, 'message' => "Faculty member '{$full_name}' updated successfully."];
        } catch (Throwable $e) {
            error_log("Failed to update staff in MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Delete a faculty member directly in MySQL
 */
function delete_faculty_member($id)
{
    global $pdo;
    $id = (int) $id;
    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid faculty ID.'];
    }

    $f = get_faculty_by_id($id);
    if (!$f) {
        return ['success' => false, 'message' => 'Faculty member not found.'];
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM staff WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if (function_exists('record_content_update')) {
                record_content_update('faculty');
                record_content_update('departments');
            }
            if (function_exists('log_activity')) {
                log_activity('Deleted', 'Faculty', $f['full_name'], "Deleted faculty member from department {$f['dept_code']}", $id);
            }
            return ['success' => true, 'message' => "Faculty member '{$f['full_name']}' deleted successfully."];
        } catch (Throwable $e) {
            error_log("Failed to delete staff from MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Appoint a specific faculty member as active HOD of their department in MySQL
 */
function assign_department_hod($dept_code, $faculty_id)
{
    global $pdo;
    $dept_code = strtoupper(trim((string) $dept_code));
    $faculty_id = (int) $faculty_id;

    $target = get_faculty_by_id($faculty_id);
    if (!$target) {
        return ['success' => false, 'message' => 'Faculty member not found.'];
    }

    demote_existing_department_hod($dept_code, $faculty_id);

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("UPDATE staff SET dept_code = :code, is_hod = 1, role_category = 'HOD', updated_at = NOW() WHERE id = :id");
            $stmt->execute([':code' => $dept_code, ':id' => $faculty_id]);

            if (function_exists('record_content_update')) {
                record_content_update('faculty');
                record_content_update('departments');
            }
            if (function_exists('log_activity')) {
                log_activity('Updated', 'Faculty', $target['full_name'], "Assigned as Head of Department for {$dept_code}", $faculty_id);
            }
            return ['success' => true, 'message' => "Successfully appointed '{$target['full_name']}' as active HOD for department {$dept_code}."];
        } catch (Throwable $e) {
            error_log("Failed to assign HOD in MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

// ==============================================================
// 3. DEPARTMENT-WISE EXCEL EXPORT (SPREADSHEETML WITH FORMULA ESCAPING)
// ==============================================================

/**
 * Clean cell value to prevent CSV/Spreadsheet formula injection
 */
function sanitize_excel_cell($val): string
{
    if ($val === null) {
        return '';
    }
    $str = (string) $val;
    $trimmed = ltrim($str);
    if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
        $str = "'" . $str;
    }
    return htmlspecialchars($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/**
 * Generate native Excel XML 2003 (SpreadsheetML) representation of the faculty directory
 */
function generate_faculty_excel_xml($filters = []): string
{
    $faculty = get_faculty_members($filters);
    $dept = $filters['department'] ?? ($filters['dept_code'] ?? 'All');
    $dept_label = strtoupper($dept) === 'ALL' ? 'All Departments' : strtoupper($dept);
    $gen_date = date('d-M-Y H:i:s');

    ob_start();
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
    echo '          xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
    echo '          xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
    echo '          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
    echo '          xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    echo '<Styles>' . "\n";
    echo '<Style ss:ID="Header">' . "\n";
    echo '<Font ss:Bold="1" ss:Color="#FFFFFF" ss:FontName="Segoe UI" ss:Size="11"/>' . "\n";
    echo '<Interior ss:Color="#0F3854" ss:Pattern="Solid"/>' . "\n";
    echo '<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n";
    echo '<Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/></Borders>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="Title">' . "\n";
    echo '<Font ss:Bold="1" ss:Color="#0F3854" ss:FontName="Segoe UI" ss:Size="14"/>' . "\n";
    echo '<Alignment ss:Horizontal="Left" ss:Vertical="Center"/>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="RowEven">' . "\n";
    echo '<Font ss:FontName="Segoe UI" ss:Size="10"/>' . "\n";
    echo '<Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>' . "\n";
    echo '<Alignment ss:Vertical="Center"/>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="RowOdd">' . "\n";
    echo '<Font ss:FontName="Segoe UI" ss:Size="10"/>' . "\n";
    echo '<Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>' . "\n";
    echo '<Alignment ss:Vertical="Center"/>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="BadgeHOD">' . "\n";
    echo '<Font ss:Bold="1" ss:Color="#854D0E" ss:FontName="Segoe UI" ss:Size="10"/>' . "\n";
    echo '<Interior ss:Color="#FEF08A" ss:Pattern="Solid"/>' . "\n";
    echo '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
    echo '</Style>' . "\n";
    echo '</Styles>' . "\n";
    ?>
        <Worksheet ss:Name="Faculty Directory">


            <Table ss:DefaultRowHeight="20">
                <Column ss:Width="40"/>
                <Column ss:Width="170"/>
                <Column ss:Width="60"/>
                <Column ss:Width="160"/>
                <Column ss:Width="160"/>
                <Column ss:Width="130"/>
                <Column ss:Width="110"/>
                <Column ss:Width="80"/>
                <Column ss:Width="180"/>
                <Column ss:Width="100"/>
                <Column ss:Width="60"/>

                <Row ss:Height="26">
                    <Cell ss:MergeAcross="10" ss:StyleID="Title">
                        <Data ss:Type="String">Trinity College of Engineering &amp; Technology (TCEK) — Faculty Directory (<?php echo sanitize_excel_cell($dept_label); ?>)</Data>
                    </Cell>
                </Row>
                <Row ss:Height="18">
                    <Cell ss:MergeAcross="10">
                        <Data ss:Type="String">Generated on <?php echo $gen_date; ?> • Total Records: <?php echo count($faculty); ?></Data>
                    </Cell>
                </Row>
                <Row></Row>

                <Row ss:Height="24" ss:StyleID="Header">
                    <Cell><Data ss:Type="String">S.No</Data></Cell>
                    <Cell><Data ss:Type="String">Full Name</Data></Cell>
                    <Cell><Data ss:Type="String">Dept</Data></Cell>
                    <Cell><Data ss:Type="String">Department Name</Data></Cell>
                    <Cell><Data ss:Type="String">Designation</Data></Cell>
                    <Cell><Data ss:Type="String">Role Category</Data></Cell>
                    <Cell><Data ss:Type="String">Qualification</Data></Cell>
                    <Cell><Data ss:Type="String">Experience</Data></Cell>
                    <Cell><Data ss:Type="String">Email Address</Data></Cell>
                    <Cell><Data ss:Type="String">Phone Number</Data></Cell>
                    <Cell><Data ss:Type="String">Status</Data></Cell>
                </Row>

                <?php 
                $sno = 1;
                foreach ($faculty as $f): 
                    $rowStyle = !empty($f['is_hod']) ? 'BadgeHOD' : (($sno % 2 === 0) ? 'RowOdd' : 'RowEven');
                ?>
                <Row ss:Height="20" ss:StyleID="<?php echo $rowStyle; ?>">
                    <Cell><Data ss:Type="Number"><?php echo $sno++; ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['full_name'] ?? ''); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['dept_code'] ?? ''); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['department_name'] ?? ($f['department'] ?? '')); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['designation'] ?? ''); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['role_category'] ?? ''); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['qualification'] ?? 'N/A'); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['experience'] ?? 'N/A'); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['email'] ?? 'N/A'); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['phone'] ?? 'N/A'); ?></Data></Cell>
                    <Cell><Data ss:Type="String"><?php echo !empty($f['is_active']) ? 'Active' : 'Inactive'; ?></Data></Cell>
                </Row>
                <?php endforeach; ?>
            </Table>
        </Worksheet>
    </Workbook>
    <?php
    return ob_get_clean();
}

/**
 * HTTP Stream: Export and download Excel spreadsheet for faculty directory
 */
function export_faculty_excel($filters = []): void
{
    $dept = $filters['department'] ?? ($filters['dept_code'] ?? 'all');
    $dept_code = strtoupper(trim((string)$dept));
    $filename = 'TCEK_Faculty_Directory_' . ($dept_code === 'ALL' ? 'All_Departments' : $dept_code) . '_' . date('Ymd_His') . '.xls';

    $xml = generate_faculty_excel_xml($filters);

    if (!headers_sent()) {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    echo $xml;
    exit;
}
