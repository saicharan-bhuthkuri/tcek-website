<?php
/**
 * TCEK MySQL Database Migration & Seeder Engine
 * Migrates all website data from JSON storage directly into MySQL relational tables.
 * Safe, idempotent execution: executes table creation and seeds data if empty or missing.
 */

require_once __DIR__ . '/config/database.php';

function run_tcek_database_migration(?PDO $pdo = null): array {
    if (!$pdo) {
        $pdo = getDB();
    }
    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    $results = [];

    // ==========================================================
    // 1. CREATE ALL REQUIRED MYSQL TABLES
    // ==========================================================

    $tableQueries = [
        'users' => "
            CREATE TABLE IF NOT EXISTS `users` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(50) NOT NULL UNIQUE,
              `password` VARCHAR(255) NOT NULL,
              `full_name` VARCHAR(100) NOT NULL,
              `email` VARCHAR(100) DEFAULT NULL,
              `role` VARCHAR(50) NOT NULL DEFAULT 'admin',
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`username`),
              INDEX (`email`),
              INDEX (`role`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'admins' => "
            CREATE TABLE IF NOT EXISTS `admins` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(50) NOT NULL UNIQUE,
              `password` VARCHAR(255) NOT NULL,
              `full_name` VARCHAR(100) DEFAULT 'TCEK Administrator',
              `email` VARCHAR(100) DEFAULT 'officetcek@gmail.com',
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'announcements' => "
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
        ",
        'hero_content' => "
            CREATE TABLE IF NOT EXISTS `hero_content` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL DEFAULT 'Excellence in Education',
              `subtitle` VARCHAR(255) NOT NULL DEFAULT 'Inspiring Future Innovators Since 2008',
              `subtitle_icon` VARCHAR(100) DEFAULT 'fas fa-shield-halved',
              `badge_left_text` VARCHAR(100) DEFAULT 'Campus Highlights',
              `badge_left_pulse` TINYINT(1) DEFAULT 1,
              `badge_right_text` VARCHAR(100) DEFAULT 'NAAC \'B++\' Grade & Autonomous',
              `badge_right_icon` VARCHAR(100) DEFAULT 'fas fa-certificate',
              `cta_text` VARCHAR(100) DEFAULT 'Admissions Open',
              `cta_link` VARCHAR(255) DEFAULT 'admission.php',
              `auto_play` TINYINT(1) DEFAULT 1,
              `slide_interval` INT DEFAULT 3600,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'hero_slides' => "
            CREATE TABLE IF NOT EXISTS `hero_slides` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `media_type` ENUM('image', 'video') NOT NULL DEFAULT 'image',
              `file_path` VARCHAR(255) NOT NULL,
              `title` VARCHAR(255) NOT NULL DEFAULT '',
              `is_active` TINYINT(1) DEFAULT 1,
              `display_order` INT DEFAULT 0,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`is_active`),
              INDEX (`display_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'gallery' => "
            CREATE TABLE IF NOT EXISTS `gallery` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `media_type` ENUM('image', 'video') NOT NULL DEFAULT 'image',
              `category` ENUM('events', 'campus', 'milestones', 'press') NOT NULL DEFAULT 'events',
              `file_path` VARCHAR(255) DEFAULT NULL,
              `video_url` VARCHAR(255) DEFAULT NULL,
              `description` TEXT DEFAULT NULL,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX (`media_type`),
              INDEX (`category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'events' => "
            CREATE TABLE IF NOT EXISTS `events` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `event_date` DATE NOT NULL,
              `event_time` VARCHAR(50) DEFAULT '10:00 AM',
              `venue` VARCHAR(255) DEFAULT 'Trinity Campus Auditorium',
              `description` TEXT DEFAULT NULL,
              `image_path` VARCHAR(255) DEFAULT NULL,
              `video_path` VARCHAR(255) DEFAULT NULL,
              `is_featured` TINYINT(1) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`event_date`),
              INDEX (`is_featured`),
              INDEX (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'event_media' => "
            CREATE TABLE IF NOT EXISTS `event_media` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `event_id` INT NOT NULL,
              `media_type` ENUM('image', 'video') NOT NULL DEFAULT 'image',
              `file_path` VARCHAR(255) NOT NULL,
              `media_title` VARCHAR(255) NOT NULL,
              `media_description` TEXT DEFAULT NULL,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX (`event_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'notifications' => "
            CREATE TABLE IF NOT EXISTS `notifications` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `category` VARCHAR(100) NOT NULL DEFAULT 'General',
              `description` TEXT DEFAULT NULL,
              `attachment_type` ENUM('pdf', 'image', 'docx', 'none') DEFAULT 'none',
              `attachment_path` VARCHAR(255) DEFAULT NULL,
              `link_url` VARCHAR(255) DEFAULT NULL,
              `is_marquee` TINYINT(1) DEFAULT 0,
              `publish_date` DATE NOT NULL,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`publish_date`),
              INDEX (`is_marquee`),
              INDEX (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'notices' => "
            CREATE TABLE IF NOT EXISTS `notices` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `category` VARCHAR(100) NOT NULL DEFAULT 'General',
              `badge` VARCHAR(50) DEFAULT 'NEW',
              `description` TEXT DEFAULT NULL,
              `file_path` VARCHAR(255) DEFAULT NULL,
              `link_url` VARCHAR(255) DEFAULT NULL,
              `publish_date` DATE NOT NULL,
              `is_pinned` TINYINT(1) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'departments' => "
            CREATE TABLE IF NOT EXISTS `departments` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `dept_code` VARCHAR(50) NOT NULL UNIQUE,
              `name` VARCHAR(255) NOT NULL,
              `slug` VARCHAR(100) NOT NULL UNIQUE,
              `degree_level` VARCHAR(50) NOT NULL DEFAULT 'B.Tech',
              `intake` VARCHAR(50) DEFAULT '60 Seats',
              `duration` VARCHAR(50) DEFAULT '4 Years',
              `established_year` INT DEFAULT 2008,
              `icon_class` VARCHAR(100) DEFAULT 'fas fa-graduation-cap',
              `theme_class` VARCHAR(50) DEFAULT 'theme-cse',
              `banner_image` VARCHAR(255) DEFAULT 'assets/courses/cse.png',
              `tags` TEXT DEFAULT NULL,
              `syllabus_url` VARCHAR(255) DEFAULT NULL,
              `peos_url` VARCHAR(255) DEFAULT NULL,
              `gallery_images` TEXT DEFAULT NULL,
              `description` TEXT DEFAULT NULL,
              `vision` TEXT DEFAULT NULL,
              `mission` TEXT DEFAULT NULL,
              `page_url` VARCHAR(255) DEFAULT NULL,
              `display_order` INT DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`dept_code`),
              INDEX (`slug`),
              INDEX (`degree_level`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'staff' => "
            CREATE TABLE IF NOT EXISTS `staff` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `full_name` VARCHAR(150) NOT NULL,
              `dept_code` VARCHAR(50) NOT NULL,
              `department` VARCHAR(100) NOT NULL,
              `designation` VARCHAR(100) NOT NULL,
              `role_category` VARCHAR(100) NOT NULL DEFAULT 'Faculty – Junior (JR)',
              `is_hod` TINYINT(1) DEFAULT 0,
              `is_rnd` TINYINT(1) DEFAULT 0,
              `qualification` VARCHAR(150) DEFAULT NULL,
              `jntuh_reg_id` VARCHAR(100) DEFAULT NULL,
              `experience` VARCHAR(50) DEFAULT NULL,
              `email` VARCHAR(100) DEFAULT NULL,
              `phone` VARCHAR(50) DEFAULT NULL,
              `profile_image` VARCHAR(255) DEFAULT NULL,
              `bio` TEXT DEFAULT NULL,
              `display_order` INT DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`dept_code`),
              INDEX (`role_category`),
              INDEX (`is_hod`),
              INDEX (`is_rnd`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'activity_logs' => "
            CREATE TABLE IF NOT EXISTS `activity_logs` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `admin_name` VARCHAR(100) NOT NULL,
              `action` VARCHAR(50) NOT NULL,
              `module` VARCHAR(100) NOT NULL,
              `record_name` VARCHAR(255) NOT NULL,
              `record_id` INT DEFAULT NULL,
              `description` TEXT DEFAULT NULL,
              `ip_address` VARCHAR(50) DEFAULT NULL,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX (`module`),
              INDEX (`action`),
              INDEX (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'uploads' => "
            CREATE TABLE IF NOT EXISTS `uploads` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `file_name` VARCHAR(255) NOT NULL,
              `file_path` VARCHAR(255) NOT NULL,
              `file_type` ENUM('pdf', 'image', 'video', 'docx', 'other') NOT NULL DEFAULT 'other',
              `file_size` BIGINT DEFAULT 0,
              `category` VARCHAR(100) DEFAULT 'general',
              `description` TEXT DEFAULT NULL,
              `uploaded_by` VARCHAR(50) DEFAULT 'tcek',
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX (`category`),
              INDEX (`file_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'system_settings' => "
            CREATE TABLE IF NOT EXISTS `system_settings` (
              `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
              `setting_value` TEXT,
              `updated_by` VARCHAR(100) DEFAULT 'Developer',
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'news' => "
            CREATE TABLE IF NOT EXISTS `news` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `image_path` VARCHAR(255) DEFAULT NULL,
              `publish_date` DATE NOT NULL,
              `description` TEXT DEFAULT NULL,
              `source` VARCHAR(100) DEFAULT 'Press & Media',
              `link_url` VARCHAR(255) DEFAULT NULL,
              `status` VARCHAR(50) DEFAULT 'PUBLISHED',
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX (`publish_date`),
              INDEX (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'rnd_rankings' => "
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
        ",
        'rnd_settings' => "
            CREATE TABLE IF NOT EXISTS `rnd_settings` (
              `setting_key` VARCHAR(100) NOT NULL PRIMARY KEY,
              `setting_value` TEXT DEFAULT NULL,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'workshops' => "
            CREATE TABLE IF NOT EXISTS `workshops` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(255) NOT NULL,
              `instructor` VARCHAR(150) NOT NULL,
              `category` VARCHAR(100) NOT NULL DEFAULT 'Technical',
              `event_date` DATE NOT NULL,
              `venue` VARCHAR(255) DEFAULT 'Seminar Hall',
              `status` VARCHAR(50) DEFAULT 'UPCOMING',
              `description` TEXT DEFAULT NULL,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'content_versions' => "
            CREATE TABLE IF NOT EXISTS `content_versions` (
              `module` VARCHAR(50) NOT NULL PRIMARY KEY,
              `version_timestamp` BIGINT NOT NULL,
              `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        "
    ];

    foreach ($tableQueries as $table => $sql) {
        try {
            $pdo->exec($sql);
            $results['tables'][$table] = 'OK';
        } catch (Throwable $e) {
            $results['tables'][$table] = 'Error: ' . $e->getMessage();
        }
    }

    // ==========================================================
    // 2. MIGRATE / SEED DATA FROM JSON FILES DIRECTLY TO MYSQL
    // ==========================================================

    $configDir = __DIR__ . '/config';

    // 2a. Users
    $usersCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($usersCount === 0) {
        $usersFile = $configDir . '/users_data.json';
        $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
        if (empty($users)) {
            $users = [
                ['id' => 1, 'username' => 'Charan', 'password' => '$2y$12$4UEnf0l1e/dEsA8PtrujXOjWsH3movWizobG5vHq.INjW/t65lg/i', 'full_name' => 'Charan', 'email' => 'saivortex.dev@gmail.com', 'role' => 'developer', 'is_active' => 1],
                ['id' => 2, 'username' => 'tcek', 'password' => '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'full_name' => 'TCEK Administrator', 'email' => 'officetcek@gmail.com', 'role' => 'admin', 'is_active' => 1]
            ];
        }
        $stmt = $pdo->prepare("INSERT INTO users (id, username, password, full_name, email, role, is_active, created_at) VALUES (:id, :u, :p, :n, :e, :r, :a, :c) ON DUPLICATE KEY UPDATE full_name=VALUES(full_name)");
        foreach ($users as $u) {
            $stmt->execute([
                ':id' => $u['id'] ?? null,
                ':u'  => $u['username'],
                ':p'  => $u['password'] ?? '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq',
                ':n'  => $u['full_name'] ?? $u['username'],
                ':e'  => $u['email'] ?? null,
                ':r'  => $u['role'] ?? 'admin',
                ':a'  => isset($u['is_active']) ? (int)$u['is_active'] : 1,
                ':c'  => $u['created_at'] ?? date('Y-m-d H:i:s')
            ]);
        }
        // Also populate admins table
        $admStmt = $pdo->prepare("INSERT INTO admins (id, username, password, full_name, email) VALUES (:id, :u, :p, :n, :e) ON DUPLICATE KEY UPDATE username=VALUES(username)");
        foreach ($users as $u) {
            $admStmt->execute([
                ':id' => $u['id'] ?? null,
                ':u'  => $u['username'],
                ':p'  => $u['password'] ?? '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq',
                ':n'  => $u['full_name'] ?? $u['username'],
                ':e'  => $u['email'] ?? 'officetcek@gmail.com'
            ]);
        }
        $results['data']['users'] = count($users) . ' imported';
    } else {
        $results['data']['users'] = 'Already populated (' . $usersCount . ' records)';
    }

    // 2b. Announcements
    $annCount = (int)$pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
    if ($annCount === 0) {
        $annFile = $configDir . '/announcements_data.json';
        $ann = file_exists($annFile) ? json_decode(file_get_contents($annFile), true) : [];
        $defText = 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)';
        $text   = !empty($ann['announcement_text']) ? $ann['announcement_text'] : $defText;
        $link   = $ann['link_url'] ?? '';
        $active = isset($ann['is_enabled']) ? (int)$ann['is_enabled'] : 1;
        $speed  = isset($ann['scrolling_speed']) ? (int)$ann['scrolling_speed'] : 60;
        $lastUp = !empty($ann['last_updated']) ? $ann['last_updated'] : date('d F Y');
        $showUp = isset($ann['show_last_updated']) ? (int)$ann['show_last_updated'] : 1;

        $stmt = $pdo->prepare("INSERT INTO announcements (id, announcement_text, link_url, is_enabled, scrolling_speed, last_updated, show_last_updated, created_at) VALUES (1, :t, :l, :e, :s, :lu, :su, NOW())");
        $stmt->execute([
            ':t'  => $text,
            ':l'  => $link,
            ':e'  => $active,
            ':s'  => $speed,
            ':lu' => $lastUp,
            ':su' => $showUp
        ]);
        $results['data']['announcements'] = '1 imported';
    } else {
        $results['data']['announcements'] = 'Already populated';
    }

    // 2c. Hero Content & Hero Slides
    $heroContentCount = (int)$pdo->query("SELECT COUNT(*) FROM hero_content")->fetchColumn();
    if ($heroContentCount === 0) {
        $heroFile = $configDir . '/hero_slider_data.json';
        $hero = file_exists($heroFile) ? json_decode(file_get_contents($heroFile), true) : [];
        $c = $hero['content'] ?? [];
        $s = $hero['settings'] ?? [];

        $stmt = $pdo->prepare("
            INSERT INTO hero_content (
                id, title, subtitle, subtitle_icon, badge_left_text, badge_left_pulse, 
                badge_right_text, badge_right_icon, cta_text, cta_link, auto_play, slide_interval
            ) VALUES (
                1, :t, :sub, :sub_ico, :bl_t, :bl_p, :br_t, :br_ico, :cta_t, :cta_l, :ap, :si
            )
        ");
        $stmt->execute([
            ':t'       => $c['title'] ?? 'Excellence in Education',
            ':sub'     => $c['subtitle'] ?? 'Inspiring Future Innovators Since 2008',
            ':sub_ico' => $c['subtitle_icon'] ?? 'fas fa-shield-halved',
            ':bl_t'    => $c['badge_left_text'] ?? 'Campus Highlights',
            ':bl_p'    => isset($c['badge_left_pulse']) ? (int)$c['badge_left_pulse'] : 0,
            ':br_t'    => $c['badge_right_text'] ?? "NAAC 'B++' Grade & Autonomous",
            ':br_ico'  => $c['badge_right_icon'] ?? 'fas fa-certificate',
            ':cta_t'   => $c['cta_text'] ?? 'Admissions Open',
            ':cta_l'   => $c['cta_link'] ?? 'admission.php',
            ':ap'      => isset($s['auto_play']) ? (int)$s['auto_play'] : 1,
            ':si'      => isset($s['slide_interval']) ? (int)$s['slide_interval'] : 3600
        ]);
        $results['data']['hero_content'] = '1 imported';
    }

    $heroSlidesCount = (int)$pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
    if ($heroSlidesCount === 0) {
        $heroFile = $configDir . '/hero_slider_data.json';
        $hero = file_exists($heroFile) ? json_decode(file_get_contents($heroFile), true) : [];
        $slides = $hero['slides'] ?? [];
        if (!empty($slides)) {
            $stmt = $pdo->prepare("
                INSERT INTO hero_slides (id, media_type, file_path, title, is_active, display_order, created_at)
                VALUES (:id, :m, :f, :t, :a, :o, NOW())
            ");
            foreach ($slides as $sl) {
                $stmt->execute([
                    ':id' => $sl['id'] ?? null,
                    ':m'  => $sl['media_type'] ?? 'image',
                    ':f'  => $sl['file_path'],
                    ':t'  => $sl['title'] ?? '',
                    ':a'  => isset($sl['is_active']) ? (int)$sl['is_active'] : 1,
                    ':o'  => isset($sl['display_order']) ? (int)$sl['display_order'] : 0
                ]);
            }
            $results['data']['hero_slides'] = count($slides) . ' imported';
        }
    } else {
        $results['data']['hero_slides'] = 'Already populated';
    }

    // 2d. Departments
    $deptCount = (int)$pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    if ($deptCount === 0) {
        $deptFile = $configDir . '/departments_data.json';
        $depts = file_exists($deptFile) ? json_decode(file_get_contents($deptFile), true) : [];
        if (!empty($depts)) {
            $stmt = $pdo->prepare("
                INSERT INTO departments (
                    id, dept_code, name, slug, degree_level, intake, duration, established_year,
                    icon_class, theme_class, banner_image, tags, syllabus_url, peos_url,
                    gallery_images, description, vision, mission, page_url, display_order, is_active, created_at
                ) VALUES (
                    :id, :code, :name, :slug, :deg, :intake, :dur, :est,
                    :icon, :theme, :banner, :tags, :syll, :peos,
                    :gal, :desc, :vis, :mis, :page, :ord, :act, NOW()
                )
            ");
            foreach ($depts as $d) {
                $tags = is_array($d['tags'] ?? null) ? implode(', ', $d['tags']) : ($d['tags'] ?? null);
                $gal = is_array($d['gallery_images'] ?? null) ? json_encode($d['gallery_images']) : ($d['gallery_images'] ?? null);
                $stmt->execute([
                    ':id'     => $d['id'] ?? null,
                    ':code'   => $d['dept_code'],
                    ':name'   => $d['name'],
                    ':slug'   => $d['slug'],
                    ':deg'    => $d['degree_level'] ?? 'B.Tech',
                    ':intake' => $d['intake'] ?? '60 Seats',
                    ':dur'    => $d['duration'] ?? '4 Years',
                    ':est'    => isset($d['established_year']) ? (int)$d['established_year'] : 2008,
                    ':icon'   => $d['icon_class'] ?? 'fas fa-graduation-cap',
                    ':theme'  => $d['theme_class'] ?? 'theme-cse',
                    ':banner' => $d['banner_image'] ?? null,
                    ':tags'   => $tags,
                    ':syll'   => $d['syllabus_url'] ?? null,
                    ':peos'   => $d['peos_url'] ?? null,
                    ':gal'    => $gal,
                    ':desc'   => $d['description'] ?? null,
                    ':vis'    => $d['vision'] ?? null,
                    ':mis'    => $d['mission'] ?? null,
                    ':page'   => $d['page_url'] ?? null,
                    ':ord'    => isset($d['display_order']) ? (int)$d['display_order'] : 0,
                    ':act'    => isset($d['is_active']) ? (int)$d['is_active'] : 1
                ]);
            }
            $results['data']['departments'] = count($depts) . ' imported';
        }
    } else {
        $results['data']['departments'] = 'Already populated (' . $deptCount . ' records)';
    }

    // 2e. Staff Details / Faculty Directory
    $staffCount = (int)$pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
    if ($staffCount === 0) {
        $staffFile = $configDir . '/staff_data.json';
        $staff = file_exists($staffFile) ? json_decode(file_get_contents($staffFile), true) : [];
        if (!empty($staff)) {
            $stmt = $pdo->prepare("
                INSERT INTO staff (
                    id, full_name, dept_code, department, designation, role_category,
                    is_hod, is_rnd, qualification, jntuh_reg_id, experience, email, phone,
                    profile_image, bio, display_order, is_active, created_at
                ) VALUES (
                    :id, :name, :code, :dept, :desig, :role,
                    :hod, :rnd, :qual, :reg, :exp, :email, :phone,
                    :img, :bio, :ord, :act, NOW()
                )
            ");
            foreach ($staff as $s) {
                $stmt->execute([
                    ':id'    => $s['id'] ?? null,
                    ':name'  => $s['full_name'],
                    ':code'  => $s['dept_code'] ?? 'CSE',
                    ':dept'  => $s['department'] ?? 'Computer Science & Engineering',
                    ':desig' => $s['designation'] ?? 'Assistant Professor',
                    ':role'  => $s['role_category'] ?? 'Faculty – Junior (JR)',
                    ':hod'   => !empty($s['is_hod']) ? 1 : 0,
                    ':rnd'   => !empty($s['is_rnd']) ? 1 : 0,
                    ':qual'  => $s['qualification'] ?? null,
                    ':reg'   => $s['jntuh_reg_id'] ?? null,
                    ':exp'   => $s['experience'] ?? null,
                    ':email' => $s['email'] ?? null,
                    ':phone' => $s['phone'] ?? null,
                    ':img'   => $s['profile_image'] ?? null,
                    ':bio'   => $s['bio'] ?? null,
                    ':ord'   => isset($s['display_order']) ? (int)$s['display_order'] : 0,
                    ':act'   => isset($s['is_active']) ? (int)$s['is_active'] : 1
                ]);
            }
            $results['data']['staff'] = count($staff) . ' imported';
        }
    } else {
        $results['data']['staff'] = 'Already populated (' . $staffCount . ' records)';
    }

    // 2f. Events & Event Media
    $eventsCount = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    if ($eventsCount === 0) {
        $eventsFile = $configDir . '/events_data.json';
        $events = file_exists($eventsFile) ? json_decode(file_get_contents($eventsFile), true) : [];
        if (!empty($events)) {
            $evStmt = $pdo->prepare("
                INSERT INTO events (
                    id, title, event_date, event_time, venue, description, image_path,
                    video_path, is_featured, is_active, created_at
                ) VALUES (
                    :id, :t, :d, :tm, :v, :desc, :img, :vid, :f, :a, :c
                )
            ");
            $mStmt = $pdo->prepare("
                INSERT INTO event_media (
                    id, event_id, media_type, file_path, media_title, media_description, created_at
                ) VALUES (
                    :id, :eid, :m, :f, :t, :desc, :c
                )
            ");

            foreach ($events as $ev) {
                $evStmt->execute([
                    ':id'   => $ev['id'] ?? null,
                    ':t'    => $ev['title'],
                    ':d'    => $ev['event_date'],
                    ':tm'   => $ev['event_time'] ?? '10:00 AM',
                    ':v'    => $ev['venue'] ?? 'Trinity Campus Auditorium',
                    ':desc' => $ev['description'] ?? null,
                    ':img'  => $ev['image_path'] ?? null,
                    ':vid'  => $ev['video_path'] ?? null,
                    ':f'    => !empty($ev['is_featured']) ? 1 : 0,
                    ':a'    => isset($ev['is_active']) ? (int)$ev['is_active'] : 1,
                    ':c'    => $ev['created_at'] ?? date('Y-m-d H:i:s')
                ]);

                if (!empty($ev['media_items']) && is_array($ev['media_items'])) {
                    foreach ($ev['media_items'] as $m) {
                        $mStmt->execute([
                            ':id'   => $m['id'] ?? null,
                            ':eid'  => $ev['id'],
                            ':m'    => $m['media_type'] ?? 'image',
                            ':f'    => $m['file_path'],
                            ':t'    => $m['media_title'] ?? $ev['title'],
                            ':desc' => $m['media_description'] ?? null,
                            ':c'    => $m['created_at'] ?? date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            $results['data']['events'] = count($events) . ' imported';
        }
    } else {
        $results['data']['events'] = 'Already populated (' . $eventsCount . ' records)';
    }

    // 2g. News & Press Clippings
    $newsCount = (int)$pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
    if ($newsCount === 0) {
        $newsFile = $configDir . '/news_data.json';
        $news = file_exists($newsFile) ? json_decode(file_get_contents($newsFile), true) : [];
        if (!empty($news)) {
            $stmt = $pdo->prepare("
                INSERT INTO news (
                    id, title, image_path, publish_date, description, source, status, created_at
                ) VALUES (
                    :id, :t, :img, :d, :desc, :src, :st, :c
                )
            ");
            foreach ($news as $n) {
                $stmt->execute([
                    ':id'   => $n['id'] ?? null,
                    ':t'    => $n['title'],
                    ':img'  => $n['image_path'] ?? null,
                    ':d'    => $n['publish_date'] ?? date('Y-m-d'),
                    ':desc' => $n['description'] ?? null,
                    ':src'  => $n['source'] ?? 'Press & Media',
                    ':st'   => $n['status'] ?? 'PUBLISHED',
                    ':c'    => $n['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            $results['data']['news'] = count($news) . ' imported';
        }
    } else {
        $results['data']['news'] = 'Already populated (' . $newsCount . ' records)';
    }

    // 2h. Notifications & Notices
    $notifCount = (int)$pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
    if ($notifCount === 0) {
        $notifFile = $configDir . '/notifications_data.json';
        $notifs = file_exists($notifFile) ? json_decode(file_get_contents($notifFile), true) : [];
        if (!empty($notifs)) {
            $stmt = $pdo->prepare("
                INSERT INTO notifications (
                    id, title, category, description, attachment_type, attachment_path,
                    link_url, is_marquee, publish_date, is_active, created_at
                ) VALUES (
                    :id, :t, :cat, :desc, :at, :ap, :link, :m, :pd, :a, :c
                )
            ");
            $nNotice = $pdo->prepare("
                INSERT INTO notices (
                    id, title, category, badge, description, file_path, link_url, publish_date, is_pinned, is_active, created_at
                ) VALUES (
                    :id, :t, :cat, 'NEW', :desc, :fp, :link, :pd, :pin, :a, :c
                )
            ");
            foreach ($notifs as $nt) {
                $stmt->execute([
                    ':id'   => $nt['id'] ?? null,
                    ':t'    => $nt['title'],
                    ':cat'  => $nt['category'] ?? 'General',
                    ':desc' => $nt['description'] ?? null,
                    ':at'   => $nt['attachment_type'] ?? 'none',
                    ':ap'   => $nt['attachment_path'] ?? ($nt['file_path'] ?? null),
                    ':link' => $nt['link_url'] ?? null,
                    ':m'    => !empty($nt['is_marquee']) ? 1 : 0,
                    ':pd'   => $nt['publish_date'] ?? date('Y-m-d'),
                    ':a'    => isset($nt['is_active']) ? (int)$nt['is_active'] : 1,
                    ':c'    => $nt['created_at'] ?? date('Y-m-d H:i:s')
                ]);

                try {
                    $nNotice->execute([
                        ':id'   => $nt['id'] ?? null,
                        ':t'    => $nt['title'],
                        ':cat'  => $nt['category'] ?? 'General',
                        ':desc' => $nt['description'] ?? null,
                        ':fp'   => $nt['attachment_path'] ?? ($nt['file_path'] ?? null),
                        ':link' => $nt['link_url'] ?? null,
                        ':pd'   => $nt['publish_date'] ?? date('Y-m-d'),
                        ':pin'  => !empty($nt['is_marquee']) ? 1 : 0,
                        ':a'    => isset($nt['is_active']) ? (int)$nt['is_active'] : 1,
                        ':c'    => $nt['created_at'] ?? date('Y-m-d H:i:s')
                    ]);
                } catch (Throwable $tn) {}
            }
            $results['data']['notifications'] = count($notifs) . ' imported';
        }
    } else {
        $results['data']['notifications'] = 'Already populated (' . $notifCount . ' records)';
    }

    // 2i. Gallery
    $galCount = (int)$pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    if ($galCount === 0) {
        $galFile = $configDir . '/gallery_data.json';
        $gallery = file_exists($galFile) ? json_decode(file_get_contents($galFile), true) : [];
        if (!empty($gallery)) {
            $stmt = $pdo->prepare("
                INSERT INTO gallery (id, title, media_type, category, file_path, video_url, description, created_at)
                VALUES (:id, :t, :m, :c, :f, :v, :desc, NOW())
            ");
            foreach ($gallery as $g) {
                $stmt->execute([
                    ':id'   => $g['id'] ?? null,
                    ':t'    => $g['title'],
                    ':m'    => $g['media_type'] ?? 'image',
                    ':c'    => $g['category'] ?? 'events',
                    ':f'    => $g['file_path'] ?? null,
                    ':v'    => $g['video_url'] ?? null,
                    ':desc' => $g['description'] ?? null
                ]);
            }
            $results['data']['gallery'] = count($gallery) . ' imported';
        }
    } else {
        $results['data']['gallery'] = 'Already populated (' . $galCount . ' records)';
    }

    // 2j. R&D Department Rankings & Settings
    $rndCount = (int)$pdo->query("SELECT COUNT(*) FROM rnd_rankings")->fetchColumn();
    if ($rndCount === 0) {
        $rndFile = $configDir . '/rnd_rankings_data.json';
        $rndData = file_exists($rndFile) ? json_decode(file_get_contents($rndFile), true) : [];
        $rankings = $rndData['rankings'] ?? [];
        $settings = $rndData['settings'] ?? [];

        if (!empty($settings)) {
            $sStmt = $pdo->prepare("INSERT INTO rnd_settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
            foreach ($settings as $k => $v) {
                $sStmt->execute([':k' => $k, ':v' => is_bool($v) ? ($v ? '1' : '0') : (string)$v]);
            }
        }

        if (!empty($rankings)) {
            $stmt = $pdo->prepare("
                INSERT INTO rnd_rankings (
                    id, academic_year, quarter, quarter_label, department, department_name,
                    rank, award_category, award_badge, description, achievements, criteria_tags,
                    status, publication_date, score, created_at
                ) VALUES (
                    :id, :y, :q, :ql, :dept, :dept_name,
                    :rk, :ac, :ab, :desc, :ach, :ct,
                    :st, :pd, :sc, :c
                )
            ");
            foreach ($rankings as $r) {
                $ach = is_array($r['achievements'] ?? null) ? json_encode($r['achievements']) : ($r['achievements'] ?? null);
                $crit = is_array($r['criteria_tags'] ?? null) ? json_encode($r['criteria_tags']) : ($r['criteria_tags'] ?? null);
                $stmt->execute([
                    ':id'        => $r['id'] ?? null,
                    ':y'         => $r['academic_year'],
                    ':q'         => $r['quarter'],
                    ':ql'        => $r['quarter_label'] ?? null,
                    ':dept'      => $r['department'],
                    ':dept_name' => $r['department_name'] ?? $r['department'],
                    ':rk'        => isset($r['rank']) ? (int)$r['rank'] : 1,
                    ':ac'        => $r['award_category'] ?? null,
                    ':ab'        => $r['award_badge'] ?? null,
                    ':desc'      => $r['description'] ?? null,
                    ':ach'       => $ach,
                    ':ct'        => $crit,
                    ':st'        => $r['status'] ?? 'published',
                    ':pd'        => $r['publication_date'] ?? null,
                    ':sc'        => isset($r['score']) ? (float)$r['score'] : null,
                    ':c'         => $r['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            $results['data']['rnd_rankings'] = count($rankings) . ' imported';
        }
    } else {
        $results['data']['rnd_rankings'] = 'Already populated (' . $rndCount . ' records)';
    }

    // 2k. Workshops
    $workshopsCount = (int)$pdo->query("SELECT COUNT(*) FROM workshops")->fetchColumn();
    if ($workshopsCount === 0) {
        $wkFile = $configDir . '/workshops_data.json';
        $workshops = file_exists($wkFile) ? json_decode(file_get_contents($wkFile), true) : [];
        if (!empty($workshops)) {
            $stmt = $pdo->prepare("
                INSERT INTO workshops (id, title, instructor, category, event_date, venue, status, description, created_at)
                VALUES (:id, :t, :ins, :cat, :d, :v, :st, :desc, :c)
            ");
            foreach ($workshops as $w) {
                $stmt->execute([
                    ':id'   => $w['id'] ?? null,
                    ':t'    => $w['title'],
                    ':ins'  => $w['instructor'] ?? 'TCEK Faculty',
                    ':cat'  => $w['category'] ?? 'Technical',
                    ':d'    => $w['event_date'] ?? date('Y-m-d'),
                    ':v'    => $w['venue'] ?? 'Seminar Hall',
                    ':st'   => $w['status'] ?? 'UPCOMING',
                    ':desc' => $w['description'] ?? null,
                    ':c'    => $w['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            $results['data']['workshops'] = count($workshops) . ' imported';
        }
    } else {
        $results['data']['workshops'] = 'Already populated (' . $workshopsCount . ' records)';
    }

    // 2l. Activity Logs
    $logsCount = (int)$pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
    if ($logsCount === 0) {
        $logsFile = $configDir . '/activity_logs_data.json';
        $logs = file_exists($logsFile) ? json_decode(file_get_contents($logsFile), true) : [];
        if (!empty($logs)) {
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (id, admin_name, action, module, record_name, record_id, description, ip_address, created_at)
                VALUES (:id, :adm, :act, :mod, :rn, :rid, :desc, :ip, :c)
            ");
            foreach ($logs as $l) {
                $stmt->execute([
                    ':id'   => $l['id'] ?? null,
                    ':adm'  => $l['admin_name'] ?? 'System',
                    ':act'  => $l['action'] ?? 'Updated',
                    ':mod'  => $l['module'] ?? 'System',
                    ':rn'   => $l['record_name'] ?? 'General',
                    ':rid'  => $l['record_id'] ?? null,
                    ':desc' => $l['description'] ?? null,
                    ':ip'   => $l['ip_address'] ?? '127.0.0.1',
                    ':c'    => $l['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            $results['data']['activity_logs'] = count($logs) . ' imported';
        }
    } else {
        $results['data']['activity_logs'] = 'Already populated (' . $logsCount . ' records)';
    }

    // 2m. System Settings
    $sysCount = (int)$pdo->query("SELECT COUNT(*) FROM system_settings")->fetchColumn();
    if ($sysCount === 0) {
        $sysFile = $configDir . '/system_settings.json';
        $sys = file_exists($sysFile) ? json_decode(file_get_contents($sysFile), true) : [];
        $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, updated_by, updated_at) VALUES (:k, :v, :by, NOW()) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
        $stmt->execute([
            ':k'  => 'global_404_enabled',
            ':v'  => isset($sys['global_404_enabled']) ? (string)$sys['global_404_enabled'] : '0',
            ':by' => $sys['updated_by'] ?? 'saivortex.dev@gmail.com'
        ]);
        $stmt->execute([
            ':k'  => 'global_404_custom_message',
            ':v'  => $sys['global_404_custom_message'] ?? 'The page you are looking for does not exist or has been moved.',
            ':by' => $sys['updated_by'] ?? 'saivortex.dev@gmail.com'
        ]);
        $results['data']['system_settings'] = 'Imported';
    } else {
        $results['data']['system_settings'] = 'Already populated';
    }

    // 2n. Content Versions
    $vCount = (int)$pdo->query("SELECT COUNT(*) FROM content_versions")->fetchColumn();
    if ($vCount === 0) {
        $vFile = $configDir . '/content_versions.json';
        $vData = file_exists($vFile) ? json_decode(file_get_contents($vFile), true) : [];
        $modules = ['announcements', 'hero', 'news', 'notifications', 'events', 'event_media', 'gallery', 'rnd', 'departments', 'faculty', 'courses', 'workshops', 'users', 'global'];
        $stmt = $pdo->prepare("INSERT INTO content_versions (module, version_timestamp, updated_at) VALUES (:m, :ts, NOW()) ON DUPLICATE KEY UPDATE version_timestamp=VALUES(version_timestamp)");
        $now = time();
        foreach ($modules as $m) {
            $ts = isset($vData[$m]) ? (int)$vData[$m] : $now;
            $stmt->execute([':m' => $m, ':ts' => $ts]);
        }
        $results['data']['content_versions'] = 'Imported';
    } else {
        $results['data']['content_versions'] = 'Already populated';
    }

    return ['success' => true, 'results' => $results];
}

// Auto-run if executed directly via CLI
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "Starting TCEK Database Migration...\n";
    $res = run_tcek_database_migration();
    echo json_encode($res, JSON_PRETTY_PRINT) . "\n";
}
