<?php
/**
 * TCEK Department & Faculty Management Controller
 * Handles: Department CRUD, Faculty Directory CRUD, Role & Designation Assignments,
 * HOD Single-Active Enforcement, Search/Filters, Excel SpreadsheetML Export with Formula Sanitization,
 * and Public Website Synchronization.
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

/**
 * Return array of valid faculty role designations
 */
function get_valid_faculty_roles(): array
{
    return TCEK_FACULTY_ROLES;
}

// ==============================================================
// 1. DATA STORES (FILE FALLBACK HELPERS)
// ==============================================================

function get_departments_store_file()
{
    return __DIR__ . '/config/departments_data.json';
}

function get_staff_store_file()
{
    return __DIR__ . '/config/staff_data.json';
}

function load_departments_from_store()
{
    $file = get_departments_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
    }
    return [];
}

function save_departments_to_store($departments)
{
    $file = get_departments_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return @file_put_contents($file, json_encode(array_values($departments), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function load_staff_from_store()
{
    $file = get_staff_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
    }
    return [];
}

function save_staff_to_store($staff)
{
    $file = get_staff_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return @file_put_contents($file, json_encode(array_values($staff), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

// ==============================================================
// 2. DEPARTMENT MANAGEMENT CORE FUNCTIONS
// ==============================================================

/**
 * Retrieve all departments, with attached faculty counts and active HOD name
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
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $departments = [];
        }
    }

    if (empty($departments)) {
        $all = load_departments_from_store();
        $staff = load_staff_from_store();

        $departments = [];
        foreach ($all as $d) {
            if ($active_only && empty($d['is_active'])) {
                continue;
            }
            $code = strtoupper(trim($d['dept_code'] ?? ''));

            $count = 0;
            $hod_name = null;
            foreach ($staff as $s) {
                if (strtoupper(trim($s['dept_code'] ?? '')) === $code && !empty($s['is_active'])) {
                    $count++;
                    if (!empty($s['is_hod']) && !$hod_name) {
                        $hod_name = $s['full_name'] ?? '';
                    }
                }
            }
            $d['faculty_count'] = $count;
            $d['hod_name'] = $hod_name;
            $departments[] = $d;
        }

        usort($departments, function ($a, $b) {
            $ordA = (int) ($a['display_order'] ?? 0);
            $ordB = (int) ($b['display_order'] ?? 0);
            if ($ordA === $ordB) {
                return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
            }
            return $ordA <=> $ordB;
        });
    }

    return $departments;
}

/**
 * Retrieve paginated departments list with search filter
 */
function get_departments_paginated($page = 1, $per_page = 10, $search = '')
{
    $all = get_departments(false);
    $search = trim((string) $search);

    if ($search !== '') {
        $all = array_filter($all, function ($d) use ($search) {
            return stripos($d['name'] ?? '', $search) !== false ||
                stripos($d['dept_code'] ?? '', $search) !== false ||
                stripos($d['degree_level'] ?? '', $search) !== false ||
                stripos($d['hod_name'] ?? '', $search) !== false;
        });
    }

    $total = count($all);
    $pagination = build_pagination_meta($total, $page, $per_page);
    $pagination['items'] = array_values(array_slice($all, $pagination['offset'], $pagination['per_page']));
    return $pagination;
}

/**
 * Retrieve a department by its ID
 */
function get_department_by_id($id)
{
    global $pdo;
    $id = (int) $id;
    if ($id <= 0) return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) return $row;
        } catch (PDOException $e) {}
    }

    $all = get_departments(false);
    foreach ($all as $d) {
        if ((int) ($d['id'] ?? 0) === $id) {
            return $d;
        }
    }
    return null;
}

/**
 * Retrieve a department by its code (e.g. 'CSE', 'ECE')
 */
function get_department_by_code($code)
{
    global $pdo;
    $code = strtoupper(trim((string) $code));
    if ($code === '') return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE UPPER(dept_code) = :code OR UPPER(slug) = :code LIMIT 1");
            $stmt->execute([':code' => $code]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) return $row;
        } catch (PDOException $e) {}
    }

    $all = get_departments(false);
    foreach ($all as $d) {
        if (strtoupper(trim($d['dept_code'] ?? '')) === $code || strtoupper(trim($d['slug'] ?? '')) === $code) {
            return $d;
        }
    }
    return null;
}

/**
 * Retrieve a department by slug
 */
function get_department_by_slug($slug)
{
    global $pdo;
    $slug = strtolower(trim((string) $slug));
    if ($slug === '') return null;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM departments WHERE LOWER(slug) = :slug OR LOWER(dept_code) = :slug LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) return $row;
        } catch (PDOException $e) {}
    }

    $all = get_departments(false);
    foreach ($all as $d) {
        if (strtolower(trim($d['slug'] ?? '')) === $slug || strtolower(trim($d['dept_code'] ?? '')) === $slug) {
            return $d;
        }
    }
    return null;
}

/**
 * Count total faculty assigned to a department
 */
function count_department_faculty($dept_code)
{
    global $pdo;
    $dept_code = strtoupper(trim((string) $dept_code));

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE UPPER(dept_code) = :code");
            $stmt->execute([':code' => $dept_code]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            // fallback below
        }
    }

    $staff = load_staff_from_store();
    $count = 0;
    foreach ($staff as $s) {
        if (strtoupper(trim($s['dept_code'] ?? '')) === $dept_code) {
            $count++;
        }
    }
    return $count;
}

/**
 * Create a new department with uniqueness checks and activity logging
 */
function add_department($dept_code, $name = '', $slug = '', $degree_level = 'B.Tech', $intake = '60 Seats', $duration = '4 Years', $established_year = 2008, $icon_class = 'fas fa-graduation-cap', $theme_class = 'theme-cse', $description = '', $vision = '', $mission = '', $page_url = '', $display_order = 0)
{
    global $pdo;

    $banner_image = 'assets/courses/cse.png';
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
        $banner_image = $data['banner_image'] ?? 'assets/courses/cse.png';
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
    }

    $dept_code = strtoupper(trim((string) $dept_code));
    $name = trim((string) $name);

    if (empty($dept_code) || empty($name)) {
        return ['success' => false, 'message' => 'Department Code and Department Name are required.'];
    }

    // Uniqueness validation (check both code and name)
    $all = load_departments_from_store();
    foreach ($all as $d) {
        if (strtoupper(trim($d['dept_code'] ?? '')) === $dept_code) {
            return ['success' => false, 'message' => "Department code '{$dept_code}' is already registered."];
        }
        if (strcasecmp(trim($d['name'] ?? ''), $name) === 0) {
            return ['success' => false, 'message' => "Department name '{$name}' is already registered."];
        }
    }

    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $dept_code));
    } else {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $slug));
    }

    if (empty($page_url)) {
        $page_url = 'department.php?slug=' . $slug;
    }

    $max_id = 0;
    foreach ($all as $d) {
        if ((int) ($d['id'] ?? 0) > $max_id) {
            $max_id = (int) $d['id'];
        }
    }
    $new_id = $max_id + 1;

    $record = [
        'id' => $new_id,
        'dept_code' => $dept_code,
        'name' => $name,
        'slug' => $slug,
        'degree_level' => $degree_level ?: 'B.Tech',
        'intake' => $intake ?: '60 Seats',
        'duration' => $duration ?: '4 Years',
        'established_year' => (int) ($established_year ?: 2008),
        'icon_class' => $icon_class ?: 'fas fa-graduation-cap',
        'theme_class' => $theme_class ?: 'theme-cse',
        'banner_image' => $banner_image ?: 'assets/courses/cse.png',
        'tags' => is_array($tags) ? $tags : (empty($tags) ? [] : array_map('trim', explode(',', $tags))),
        'syllabus_url' => $syllabus_url ?: 'assets/Dept/peos_psos.docx',
        'peos_url' => $peos_url ?: 'assets/Dept/peos_psos.docx',
        'gallery_images' => is_array($gallery_images) ? $gallery_images : ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'],
        'description' => $description,
        'vision' => $vision,
        'mission' => $mission,
        'page_url' => $page_url,
        'display_order' => (int) $display_order,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("INSERT INTO departments 
                (dept_code, name, slug, degree_level, intake, duration, established_year, icon_class, theme_class, description, vision, mission, page_url, display_order, is_active, created_at)
                VALUES 
                (:dept_code, :name, :slug, :degree_level, :intake, :duration, :established_year, :icon_class, :theme_class, :description, :vision, :mission, :page_url, :display_order, 1, NOW())");
            $stmt->execute([
                ':dept_code' => $dept_code,
                ':name' => $name,
                ':slug' => $slug,
                ':degree_level' => $record['degree_level'],
                ':intake' => $record['intake'],
                ':duration' => $record['duration'],
                ':established_year' => $record['established_year'],
                ':icon_class' => $record['icon_class'],
                ':theme_class' => $record['theme_class'],
                ':description' => $description,
                ':vision' => $vision,
                ':mission' => $mission,
                ':page_url' => $page_url,
                ':display_order' => (int) $display_order
            ]);
            $record['id'] = (int) $pdo->lastInsertId();
            $new_id = $record['id'];
        } catch (PDOException $e) {
            error_log("Failed to insert department into MySQL: " . $e->getMessage());
        }
    }

    $all[] = $record;
    save_departments_to_store($all);

    log_activity('Added', 'Departments', $name, "Created new department ({$dept_code})", $new_id);
    return ['success' => true, 'message' => "Department '{$name}' ({$dept_code}) created successfully.", 'id' => $new_id];
}

/**
 * Update department details with uniqueness check
 */
function update_department($id, $dept_code, $name = '', $slug = '', $degree_level = 'B.Tech', $intake = '60 Seats', $duration = '4 Years', $established_year = 2008, $icon_class = 'fas fa-graduation-cap', $theme_class = 'theme-cse', $description = '', $vision = '', $mission = '', $page_url = '', $display_order = 0, $is_active = 1)
{
    global $pdo;
    $id = (int) $id;

    $banner_image = null;
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
        $banner_image = $data['banner_image'] ?? null;
        $tags = isset($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : array_map('trim', explode(',', $data['tags']))) : null;
        $syllabus_url = $data['syllabus_url'] ?? null;
        $peos_url = $data['peos_url'] ?? null;
        if (isset($data['gallery_images'])) {
            $gallery_images = is_array($data['gallery_images']) ? $data['gallery_images'] : array_map('trim', explode(',', $data['gallery_images']));
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

    $all = load_departments_from_store();
    $found_key = null;
    $old_code = null;
    foreach ($all as $k => $d) {
        if ((int) ($d['id'] ?? 0) === $id) {
            $found_key = $k;
            $old_code = strtoupper(trim($d['dept_code'] ?? ''));
            break;
        }
    }

    if ($found_key === null) {
        return ['success' => false, 'message' => 'Department not found.'];
    }

    // Check duplicate code or name against other records
    foreach ($all as $k => $d) {
        if ($k !== $found_key) {
            if (strtoupper(trim($d['dept_code'] ?? '')) === $dept_code) {
                return ['success' => false, 'message' => "Department code '{$dept_code}' is already used by another department."];
            }
            if (strcasecmp(trim($d['name'] ?? ''), $name) === 0) {
                return ['success' => false, 'message' => "Department name '{$name}' is already used by another department."];
            }
        }
    }

    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $dept_code));
    }
    if (empty($page_url)) {
        $page_url = 'department.php?slug=' . $slug;
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("UPDATE departments SET
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
                is_active = :is_active,
                updated_at = NOW()
                WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':dept_code' => $dept_code,
                ':name' => $name,
                ':slug' => $slug,
                ':degree_level' => $degree_level,
                ':intake' => $intake,
                ':duration' => $duration,
                ':established_year' => (int) $established_year,
                ':icon_class' => $icon_class,
                ':theme_class' => $theme_class,
                ':description' => $description,
                ':vision' => $vision,
                ':mission' => $mission,
                ':page_url' => $page_url,
                ':display_order' => (int) $display_order,
                ':is_active' => (int) $is_active
            ]);

            // Sync faculty dept_code if department code changed
            if ($old_code && $old_code !== $dept_code) {
                $upd_staff = $pdo->prepare("UPDATE staff SET dept_code = :new_code WHERE dept_code = :old_code");
                $upd_staff->execute([':new_code' => $dept_code, ':old_code' => $old_code]);
            }
        } catch (PDOException $e) {
            error_log("Failed to update department in MySQL: " . $e->getMessage());
        }
    }

    $all[$found_key]['dept_code'] = $dept_code;
    $all[$found_key]['name'] = $name;
    $all[$found_key]['slug'] = $slug;
    $all[$found_key]['degree_level'] = $degree_level;
    $all[$found_key]['intake'] = $intake;
    $all[$found_key]['duration'] = $duration;
    $all[$found_key]['established_year'] = (int) $established_year;
    $all[$found_key]['icon_class'] = $icon_class;
    $all[$found_key]['theme_class'] = $theme_class;
    $all[$found_key]['description'] = $description;
    $all[$found_key]['vision'] = $vision;
    $all[$found_key]['mission'] = $mission;
    $all[$found_key]['page_url'] = $page_url;
    $all[$found_key]['display_order'] = (int) $display_order;
    $all[$found_key]['is_active'] = (int) $is_active;
    if ($banner_image !== null) {
        $all[$found_key]['banner_image'] = $banner_image;
    }
    if ($tags !== null) {
        $all[$found_key]['tags'] = $tags;
    }
    if ($syllabus_url !== null) {
        $all[$found_key]['syllabus_url'] = $syllabus_url;
    }
    if ($peos_url !== null) {
        $all[$found_key]['peos_url'] = $peos_url;
    }
    if ($gallery_images !== null) {
        $all[$found_key]['gallery_images'] = $gallery_images;
    }
    $all[$found_key]['updated_at'] = date('Y-m-d H:i:s');
    save_departments_to_store($all);

    // Sync faculty dept_code in JSON store if code changed
    if ($old_code && $old_code !== $dept_code) {
        $staff = load_staff_from_store();
        $staff_modified = false;
        foreach ($staff as &$s) {
            if (strtoupper(trim($s['dept_code'] ?? '')) === $old_code) {
                $s['dept_code'] = $dept_code;
                $staff_modified = true;
            }
        }
        unset($s);
        if ($staff_modified) {
            save_staff_to_store($staff);
        }
    }

    log_activity('Updated', 'Departments', $name, "Updated department {$dept_code}", $id);
    return ['success' => true, 'message' => "Department '{$name}' updated successfully."];
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

    // Guard: Prevent deletion if faculty are still attached
    if ($faculty_count > 0) {
        return [
            'success' => false,
            'message' => "Cannot delete department '{$dept['name']}' ({$dept_code}): It has {$faculty_count} associated faculty member(s). Please reassign or delete the faculty members first."
        ];
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Failed to delete department from MySQL: " . $e->getMessage());
        }
    }

    $all = load_departments_from_store();
    $filtered = array_filter($all, function ($d) use ($id) {
        return (int) ($d['id'] ?? 0) !== $id;
    });
    save_departments_to_store($filtered);

    log_activity('Deleted', 'Departments', $dept['name'], "Deleted department {$dept_code}", $id);
    return ['success' => true, 'message' => "Department '{$dept['name']}' deleted successfully."];
}

// ==============================================================
// 3. FACULTY & STAFF MANAGEMENT CORE FUNCTIONS
// ==============================================================

/**
 * Internal helper: Demote any existing active HOD for a department
 * Ensures at most one active HOD exists per department
 */
function demote_existing_department_hod($dept_code, $exclude_id = null)
{
    global $pdo;
    $dept_code = strtoupper(trim((string) $dept_code));

    if ($pdo instanceof PDO) {
        try {
            $sql = "UPDATE staff SET is_hod = 0, 
                    role_category = CASE WHEN role_category = 'HOD' THEN 'Faculty – Senior (SR)' ELSE role_category END
                    WHERE UPPER(dept_code) = :code AND is_hod = 1";
            $params = [':code' => $dept_code];
            if ($exclude_id !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = (int) $exclude_id;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Failed to demote HOD in MySQL: " . $e->getMessage());
        }
    }

    $staff = load_staff_from_store();
    $modified = false;
    foreach ($staff as &$s) {
        if (strtoupper(trim($s['dept_code'] ?? '')) === $dept_code) {
            if ($exclude_id !== null && (int) ($s['id'] ?? 0) === (int) $exclude_id) {
                continue;
            }
            if (!empty($s['is_hod'])) {
                $s['is_hod'] = 0;
                if (($s['role_category'] ?? '') === 'HOD') {
                    $s['role_category'] = 'Faculty – Senior (SR)';
                }
                $modified = true;
            }
        }
    }
    unset($s);
    if ($modified) {
        save_staff_to_store($staff);
    }
}

/**
 * Retrieve faculty members with comprehensive filter options
 *
 * @param array $filters [
 *   'department'    => 'CSE' | 'all',
 *   'dept_code'     => 'CSE' | 'all',
 *   'search'        => string,
 *   'role_category' => 'HOD' | 'Faculty – Junior (JR)' | ... | 'all',
 *   'is_hod'        => '1' | '0' | 'all',
 *   'is_rnd'        => '1' | '0' | 'all',
 *   'is_active'     => 1 | 0 | null,
 *   'sort_by'       => 'name' | 'experience' | 'order' | 'id',
 *   'sort_dir'      => 'asc' | 'desc'
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

            $sql = "SELECT s.*, d.name AS department_name FROM staff s 
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
        } catch (PDOException $e) {
            $faculty = [];
        }
    }

    if (empty($faculty)) {
        $all = load_staff_from_store();
        $departments = get_departments(false);
        $dept_map = [];
        foreach ($departments as $d) {
            $dept_map[strtoupper(trim($d['dept_code'] ?? ''))] = $d['name'] ?? '';
        }

        $filtered = [];
        foreach ($all as $item) {
            $item_code = strtoupper(trim($item['dept_code'] ?? ''));
            $item['department_name'] = $dept_map[$item_code] ?? ($item['department'] ?? $item_code);

            // Dept filter
            if ($dept_filter !== '' && strtolower($dept_filter) !== 'all') {
                if ($item_code !== strtoupper($dept_filter)) {
                    continue;
                }
            }

            // Search filter
            if ($search !== '') {
                $haystack = ($item['full_name'] ?? '') . ' ' .
                    ($item['email'] ?? '') . ' ' .
                    ($item['jntuh_reg_id'] ?? '') . ' ' .
                    ($item['qualification'] ?? '') . ' ' .
                    ($item['designation'] ?? '');
                if (stripos($haystack, $search) === false) {
                    continue;
                }
            }

            // Role Category filter
            if ($role_filter !== '' && strtolower($role_filter) !== 'all') {
                if (($item['role_category'] ?? '') !== $role_filter) {
                    continue;
                }
            }

            // HOD filter
            if ($hod_filter === '1' && empty($item['is_hod'])) {
                continue;
            }
            if ($hod_filter === '0' && !empty($item['is_hod'])) {
                continue;
            }

            // R&D filter
            $is_rnd = !empty($item['is_rnd']) || (stripos($item['role_category'] ?? '', 'R&D') !== false);
            if ($rnd_filter === '1' && !$is_rnd) {
                continue;
            }
            if ($rnd_filter === '0' && $is_rnd) {
                continue;
            }

            // Active filter
            if ($active_filter !== null && (int) ($item['is_active'] ?? 1) !== $active_filter) {
                continue;
            }

            $filtered[] = $item;
        }

        // Sorting
        usort($filtered, function ($a, $b) use ($sort_by, $sort_dir) {
            if ($sort_by === 'name') {
                $res = strcasecmp($a['full_name'] ?? '', $b['full_name'] ?? '');
                return $sort_dir === 'desc' ? -$res : $res;
            }
            if ($sort_by === 'experience') {
                $res = strcasecmp($a['experience'] ?? '', $b['experience'] ?? '');
                return $sort_dir === 'desc' ? -$res : $res;
            }
            if ($sort_by === 'id') {
                $res = ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0));
                return $sort_dir === 'desc' ? -$res : $res;
            }

            // Default: HOD first, then display_order ASC, then name ASC
            $hodA = !empty($a['is_hod']) ? 1 : 0;
            $hodB = !empty($b['is_hod']) ? 1 : 0;
            if ($hodA !== $hodB) {
                return $hodB <=> $hodA; // HOD first
            }
            $ordA = (int) ($a['display_order'] ?? 0);
            $ordB = (int) ($b['display_order'] ?? 0);
            if ($ordA !== $ordB) {
                return $ordA <=> $ordB;
            }
            return strcasecmp($a['full_name'] ?? '', $b['full_name'] ?? '');
        });

        $faculty = $filtered;
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
 * Retrieve faculty member by ID
 */
function get_faculty_by_id($id)
{
    $id = (int) $id;
    $all = get_faculty_members([]);
    foreach ($all as $f) {
        if ((int) ($f['id'] ?? 0) === $id) {
            return $f;
        }
    }
    return null;
}

/**
 * Public website integration: Retrieve all active faculty for a specific department
 * Guaranteed single source of truth for dept-*.php pages
 */
function get_department_faculty($dept_code, $active_only = true)
{
    return get_faculty_members([
        'dept_code' => $dept_code,
        'is_active' => $active_only ? 1 : null,
        'sort_by' => 'order'
    ]);
}

/**
 * Public website integration: Retrieve active HOD profile for a specific department
 */
function get_department_hod($dept_code)
{
    $faculty = get_department_faculty($dept_code, true);
    foreach ($faculty as $f) {
        if (!empty($f['is_hod'])) {
            return $f;
        }
    }
    // Fallback: return first faculty if no explicit HOD flag
    return !empty($faculty) ? $faculty[0] : null;
}

/**
 * Add a new faculty member with HOD exclusivity and R&D role assignment
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

    // Role category mapping & HOD exclusivity enforcement
    if ($role_category === 'HOD' || !empty($is_hod)) {
        $is_hod = 1;
        $role_category = 'HOD';
        // Enforce at most ONE active HOD in this department
        demote_existing_department_hod($dept_code);
    } else {
        $is_hod = 0;
    }

    if (stripos($role_category, 'R&D') !== false || !empty($is_rnd)) {
        $is_rnd = 1;
    } else {
        $is_rnd = 0;
    }

    // Default image if none provided
    if (empty($profile_image)) {
        $profile_image = 'assets/Dept/faculty-avatar.png';
    }

    $dept_obj = get_department_by_code($dept_code);
    $full_dept_name = $dept_obj['name'] ?? $dept_code;

    $all = load_staff_from_store();
    $max_id = 0;
    foreach ($all as $s) {
        if ((int) ($s['id'] ?? 0) > $max_id) {
            $max_id = (int) $s['id'];
        }
    }
    $new_id = $max_id + 1;

    $record = [
        'id' => $new_id,
        'full_name' => $full_name,
        'dept_code' => $dept_code,
        'department' => $full_dept_name,
        'designation' => $designation,
        'role_category' => $role_category,
        'is_hod' => $is_hod,
        'is_rnd' => $is_rnd,
        'qualification' => $qualification ?: 'M.Tech',
        'jntuh_reg_id' => $jntuh_reg_id ?: 'N/A',
        'experience' => $experience ?: '5+ Years',
        'email' => $email ?: (strtolower(preg_replace('/[^a-z0-9]/', '', $full_name)) . '@tcek.in'),
        'phone' => $phone ?: '98480' . sprintf('%05d', $new_id),
        'profile_image' => $profile_image,
        'bio' => $bio ?: "Faculty member in the Department of {$full_dept_name} at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.",
        'display_order' => (int) $display_order,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("INSERT INTO staff 
                (full_name, dept_code, department, designation, role_category, is_hod, is_rnd, qualification, jntuh_reg_id, experience, email, phone, profile_image, bio, display_order, is_active, created_at)
                VALUES 
                (:full_name, :dept_code, :department, :designation, :role_category, :is_hod, :is_rnd, :qualification, :jntuh_reg_id, :experience, :email, :phone, :profile_image, :bio, :display_order, 1, NOW())");
            $stmt->execute([
                ':full_name' => $full_name,
                ':dept_code' => $dept_code,
                ':department' => $full_dept_name,
                ':designation' => $designation,
                ':role_category' => $role_category,
                ':is_hod' => $is_hod,
                ':is_rnd' => $is_rnd,
                ':qualification' => $record['qualification'],
                ':jntuh_reg_id' => $record['jntuh_reg_id'],
                ':experience' => $record['experience'],
                ':email' => $record['email'],
                ':phone' => $record['phone'],
                ':profile_image' => $record['profile_image'],
                ':bio' => $record['bio'],
                ':display_order' => (int) $display_order
            ]);
            $record['id'] = (int) $pdo->lastInsertId();
            $new_id = $record['id'];
        } catch (PDOException $e) {
            error_log("Failed to insert staff into MySQL: " . $e->getMessage());
        }
    }

    $all[] = $record;
    save_staff_to_store($all);

    log_activity('Added', 'Faculty', $full_name, "Added faculty member to {$dept_code} ({$role_category})", $new_id);
    return ['success' => true, 'message' => "Faculty member '{$full_name}' added to {$dept_code} successfully.", 'id' => $new_id];
}

/**
 * Update faculty details with HOD single-active check and activity log
 */
function update_faculty_member($id, $full_name, $dept_code = '', $designation = '', $role_category = 'Faculty – Junior (JR)', $is_hod = 0, $is_rnd = 0, $qualification = '', $jntuh_reg_id = '', $experience = '', $email = '', $phone = '', $profile_image = null, $bio = '', $display_order = 0, $is_active = 1)
{
    global $pdo;
    $id = (int) $id;

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

    $all = load_staff_from_store();
    $found_key = null;
    foreach ($all as $k => $s) {
        if ((int) ($s['id'] ?? 0) === $id) {
            $found_key = $k;
            break;
        }
    }

    if ($found_key === null) {
        return ['success' => false, 'message' => 'Faculty member not found.'];
    }

    // Role category mapping & HOD exclusivity enforcement
    if ($role_category === 'HOD' || !empty($is_hod)) {
        $is_hod = 1;
        $role_category = 'HOD';
        // Demote other HOD in this department
        demote_existing_department_hod($dept_code, $id);
    } else {
        $is_hod = 0;
    }

    if (stripos($role_category, 'R&D') !== false || !empty($is_rnd)) {
        $is_rnd = 1;
    } else {
        $is_rnd = 0;
    }

    $existing_img = $all[$found_key]['profile_image'] ?? 'assets/Dept/faculty-avatar.png';
    $final_img = (!empty($profile_image)) ? $profile_image : $existing_img;

    $dept_obj = get_department_by_code($dept_code);
    $full_dept_name = $dept_obj['name'] ?? $dept_code;

    if ($pdo instanceof PDO) {
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
                ':id' => $id,
                ':full_name' => $full_name,
                ':dept_code' => $dept_code,
                ':department' => $full_dept_name,
                ':designation' => $designation,
                ':role_category' => $role_category,
                ':is_hod' => $is_hod,
                ':is_rnd' => $is_rnd,
                ':qualification' => $qualification,
                ':jntuh_reg_id' => $jntuh_reg_id,
                ':experience' => $experience,
                ':email' => $email,
                ':phone' => $phone,
                ':profile_image' => $final_img,
                ':bio' => $bio,
                ':display_order' => (int) $display_order,
                ':is_active' => (int) $is_active
            ]);
        } catch (PDOException $e) {
            error_log("Failed to update staff in MySQL: " . $e->getMessage());
        }
    }

    $all[$found_key]['full_name'] = $full_name;
    $all[$found_key]['dept_code'] = $dept_code;
    $all[$found_key]['department'] = $full_dept_name;
    $all[$found_key]['designation'] = $designation;
    $all[$found_key]['role_category'] = $role_category;
    $all[$found_key]['is_hod'] = $is_hod;
    $all[$found_key]['is_rnd'] = $is_rnd;
    $all[$found_key]['qualification'] = $qualification;
    $all[$found_key]['jntuh_reg_id'] = $jntuh_reg_id;
    $all[$found_key]['experience'] = $experience;
    $all[$found_key]['email'] = $email;
    $all[$found_key]['phone'] = $phone;
    $all[$found_key]['profile_image'] = $final_img;
    $all[$found_key]['bio'] = $bio;
    $all[$found_key]['display_order'] = (int) $display_order;
    $all[$found_key]['is_active'] = (int) $is_active;
    $all[$found_key]['updated_at'] = date('Y-m-d H:i:s');
    save_staff_to_store($all);

    log_activity('Updated', 'Faculty', $full_name, "Updated faculty profile in {$dept_code} ({$role_category})", $id);
    return ['success' => true, 'message' => "Faculty member '{$full_name}' updated successfully."];
}

/**
 * Delete a faculty member with activity log
 */
function delete_faculty_member($id)
{
    global $pdo;
    $id = (int) $id;
    $f = get_faculty_by_id($id);
    if (!$f) {
        return ['success' => false, 'message' => 'Faculty member not found.'];
    }

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM staff WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Failed to delete staff from MySQL: " . $e->getMessage());
        }
    }

    $all = load_staff_from_store();
    $filtered = array_filter($all, function ($item) use ($id) {
        return (int) ($item['id'] ?? 0) !== $id;
    });
    save_staff_to_store($filtered);

    log_activity('Deleted', 'Faculty', $f['full_name'], "Deleted faculty member from department {$f['dept_code']}", $id);
    return ['success' => true, 'message' => "Faculty member '{$f['full_name']}' deleted successfully."];
}

/**
 * Dedicated operation: Set a specific faculty member as active HOD of their department
 * Demotes the previous HOD and sets the new HOD
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

    // Demote any existing HOD for this department
    demote_existing_department_hod($dept_code, $faculty_id);

    // Update target member as HOD
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("UPDATE staff SET dept_code = :code, is_hod = 1, role_category = 'HOD' WHERE id = :id");
            $stmt->execute([':code' => $dept_code, ':id' => $faculty_id]);
        } catch (PDOException $e) {
            error_log("Failed to assign HOD in MySQL: " . $e->getMessage());
        }
    }

    $staff = load_staff_from_store();
    foreach ($staff as &$s) {
        if ((int) ($s['id'] ?? 0) === $faculty_id) {
            $s['dept_code'] = $dept_code;
            $s['is_hod'] = 1;
            $s['role_category'] = 'HOD';
            break;
        }
    }
    unset($s);
    save_staff_to_store($staff);

    log_activity('Updated', 'Faculty', $target['full_name'], "Assigned as Head of Department for {$dept_code}", $faculty_id);
    return ['success' => true, 'message' => "Successfully appointed '{$target['full_name']}' as active HOD for department {$dept_code}."];
}

// ==============================================================
// 4. DEPARTMENT-WISE EXCEL EXPORT (SPREADSHEETML WITH FORMULA ESCAPING)
// ==============================================================

/**
 * Clean cell value to prevent CSV/Spreadsheet formula injection
 *
 * @param mixed $val
 * @return string
 */
function sanitize_excel_cell($val): string
{
    if ($val === null) {
        return '';
    }
    $s = (string) $val;
    // Prefix single quote if starts with =, +, -, @ to neutralize formulas
    if (isset($s[0]) && in_array($s[0], ['=', '+', '-', '@'], true)) {
        $s = "'" . $s;
    }
    return htmlspecialchars($s, ENT_XML1, 'UTF-8');
}

/**
 * Generate formatted Excel XML (SpreadsheetML) spreadsheet string
 * Includes formula injection sanitization on all output cells
 *
 * @param array $filters Query filters matching active UI state
 * @return string
 */
function generate_faculty_excel_xml($filters = []): string
{
    $items = get_faculty_members($filters);

    ob_start();
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    ?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:html="http://www.w3.org/TR/REC-html40">
        <Styles>
            <Style ss:ID="Header">
                <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1" /><Interior ss:Color="#00B894" ss:Pattern="Solid" /><Alignment ss:Horizontal="Center" ss:Vertical="Center" /><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1" /><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1" /><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1" /><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1" /></Borders>
            </Style>
            <Style ss:ID="Data">
                <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#1E293B" /><Alignment ss:Vertical="Center" /><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0" /><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0" /><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0" /><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0" /></Borders>
            </Style>
            <Style ss:ID="DataHOD">
                <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0F766E" ss:Bold="1" /><Interior ss:Color="#F0FDFA" ss:Pattern="Solid" /><Alignment ss:Vertical="Center" /><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#99F6E4" /><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#99F6E4" /><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#99F6E4" /><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#99F6E4" /></Borders>
            </Style>
        </Styles>
        <Worksheet ss:Name="Faculty Directory">
            <Table>
                <Row ss:StyleID="Header" ss:Height="24">
                    <Cell><Data ss:Type="String">S.No</Data></Cell>
                    <Cell><Data ss:Type="String">Department Code</Data></Cell>
                    <Cell><Data ss:Type="String">Department Name</Data></Cell>
                    <Cell><Data ss:Type="String">Faculty Full Name</Data></Cell>
                    <Cell><Data ss:Type="String">Designation</Data></Cell>
                    <Cell><Data ss:Type="String">Role Category</Data></Cell>
                    <Cell><Data ss:Type="String">HOD Status</Data></Cell>
                    <Cell><Data ss:Type="String">R&amp;D Affiliation</Data></Cell>
                    <Cell><Data ss:Type="String">Qualification</Data></Cell>
                    <Cell><Data ss:Type="String">JNTUH Reg. ID</Data></Cell>
                    <Cell><Data ss:Type="String">Experience</Data></Cell>
                    <Cell><Data ss:Type="String">Email</Data></Cell>
                    <Cell><Data ss:Type="String">Phone</Data></Cell>
                    <Cell><Data ss:Type="String">Status</Data></Cell>
                </Row>
                <?php
                $sno = 1;
                foreach ($items as $f):
                    $is_hod_str = !empty($f['is_hod']) ? 'Yes (Active HOD)' : 'No';
                    $is_rnd_str = (!empty($f['is_rnd']) || stripos($f['role_category'] ?? '', 'R&D') !== false) ? 'Yes' : 'No';
                    $status_str = !empty($f['is_active']) ? 'Active' : 'Inactive';
                    $style = !empty($f['is_hod']) ? 'DataHOD' : 'Data';
                    ?>
                    <Row ss:StyleID="<?php echo $style; ?>" ss:Height="20">
                        <Cell><Data ss:Type="Number"><?php echo $sno++; ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['dept_code'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['department_name'] ?? ($f['department'] ?? ($f['dept_code'] ?? ''))); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['full_name'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['designation'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['role_category'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($is_hod_str); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($is_rnd_str); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['qualification'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['jntuh_reg_id'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['experience'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['email'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($f['phone'] ?? ''); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?php echo sanitize_excel_cell($status_str); ?></Data></Cell>
                    </Row>
                <?php endforeach; ?>
            </Table>
        </Worksheet>
    </Workbook>
    <?php
    return (string) ob_get_clean();
}

/**
 * Stream clean, formatted Excel XML (.xls) spreadsheet to browser
 *
 * @param array $filters Query filters matching active UI state
 */
function export_faculty_excel($filters = []): void
{
    while (ob_get_level()) {
        ob_end_clean();
    }

    $dept_filter = trim((string) ($filters['department'] ?? ($filters['dept_code'] ?? 'all')));
    $dept_label = ($dept_filter !== '' && strtolower($dept_filter) !== 'all') ? strtoupper($dept_filter) : 'All_Departments';
    $filename = "TCEK_Faculty_{$dept_label}_" . date('Ymd_His') . ".xls";

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
    header('Pragma: public');

    echo generate_faculty_excel_xml($filters);
    exit;
}
