-- ==============================================================
-- TCEK (Trinity College of Engineering & Technology) Database
-- Database Name: tcek
-- Compatible with MySQL 5.6+ / 5.7+ / MySQL 8.0 / MariaDB on GoDaddy cPanel
-- Full Direct MySQL Storage - JSON Files Replaced
-- ==============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+05:30";

-- ==============================================================
-- 1. TABLE STRUCTURES (20 TABLES)
-- ==============================================================

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `username_2` (`username`),
  KEY `email` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'TCEK Administrator',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'officetcek@gmail.com',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Developer',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: content_versions
CREATE TABLE IF NOT EXISTS `content_versions` (
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version_timestamp` bigint(20) NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: uploads
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` enum('pdf','image','video','docx','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `file_size` bigint(20) DEFAULT '0',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `description` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'tcek',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `file_type` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: gallery
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` enum('image','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `category` enum('events','campus','milestones','press') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'events',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `media_type` (`media_type`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `event_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '10:00 AM',
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Trinity Campus Auditorium',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_date` (`event_date`),
  KEY `is_featured` (`is_featured`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: event_media
CREATE TABLE IF NOT EXISTS `event_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `media_type` enum('image','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `description` text COLLATE utf8mb4_unicode_ci,
  `attachment_type` enum('pdf','image','docx','none') COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_marquee` tinyint(1) DEFAULT '0',
  `publish_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `publish_date` (`publish_date`),
  KEY `is_marquee` (`is_marquee`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notices
CREATE TABLE IF NOT EXISTS `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'NEW',
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_date` date NOT NULL,
  `is_pinned` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B.Tech',
  `intake` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '60 Seats',
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '4 Years',
  `established_year` int(11) DEFAULT '2008',
  `icon_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-graduation-cap',
  `theme_class` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'theme-cse',
  `banner_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/courses/cse.png',
  `tags` text COLLATE utf8mb4_unicode_ci,
  `syllabus_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peos_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `vision` text COLLATE utf8mb4_unicode_ci,
  `mission` text COLLATE utf8mb4_unicode_ci,
  `page_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dept_code` (`dept_code`),
  UNIQUE KEY `slug` (`slug`),
  KEY `dept_code_2` (`dept_code`),
  KEY `slug_2` (`slug`),
  KEY `degree_level` (`degree_level`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: staff
CREATE TABLE IF NOT EXISTS `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dept_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Faculty – Junior (JR)',
  `is_hod` tinyint(1) DEFAULT '0',
  `is_rnd` tinyint(1) DEFAULT '0',
  `qualification` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jntuh_reg_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `display_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dept_code` (`dept_code`),
  KEY `role_category` (`role_category`),
  KEY `is_hod` (`is_hod`),
  KEY `is_rnd` (`is_rnd`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: news
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Press & Media',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'PUBLISHED',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `publish_date` (`publish_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workshops
CREATE TABLE IF NOT EXISTS `workshops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Technical',
  `event_date` date NOT NULL,
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Seminar Hall',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UPCOMING',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: rnd_rankings
CREATE TABLE IF NOT EXISTS `rnd_rankings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '1',
  `award_category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `award_badge` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `achievements` text COLLATE utf8mb4_unicode_ci,
  `criteria_tags` text COLLATE utf8mb4_unicode_ci,
  `status` enum('published','archived','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `publication_date` date DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_period` (`academic_year`,`quarter`),
  KEY `idx_dept` (`department`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: rnd_settings
CREATE TABLE IF NOT EXISTS `rnd_settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '1',
  `scrolling_speed` int(11) DEFAULT '60',
  `last_updated` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_last_updated` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hero_content
CREATE TABLE IF NOT EXISTS `hero_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Excellence in Education',
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inspiring Future Innovators Since 2008',
  `subtitle_icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-shield-halved',
  `badge_left_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Campus Highlights',
  `badge_left_pulse` tinyint(1) DEFAULT '1',
  `badge_right_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'NAAC ''B++'' Grade & Autonomous',
  `badge_right_icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-certificate',
  `cta_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Admissions Open',
  `cta_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'admission.php',
  `auto_play` tinyint(1) DEFAULT '1',
  `slide_interval` int(11) DEFAULT '3600',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hero_slides
CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_type` enum('image','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==============================================================
-- 2. SEED DATA & INITIAL RECORDS
-- ==============================================================

-- Data for table `system_settings`
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_by`, `updated_at`) VALUES
  ('global_404_custom_message', 'The page you are looking for does not exist or has been moved.', 'saivortex.dev@gmail.com', '2026-10-26 19:16:52'),
  ('global_404_enabled', '0', 'saivortex.dev@gmail.com', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- Data for table `content_versions`
INSERT INTO `content_versions` (`module`, `version_timestamp`, `updated_at`) VALUES
  ('announcements', 1728645000, '2026-10-26 19:16:52'),
  ('courses', 1791721339, '2026-10-26 19:16:52'),
  ('departments', 1791721339, '2026-10-26 19:16:52'),
  ('event_media', 1728645000, '2026-10-26 19:16:52'),
  ('events', 1728645000, '2026-10-26 19:16:52'),
  ('faculty', 1728645000, '2026-10-26 19:16:52'),
  ('gallery', 1728645000, '2026-10-26 19:16:52'),
  ('global', 1791727751, '2026-10-26 19:16:52'),
  ('hero', 1728645000, '2026-10-26 19:16:52'),
  ('news', 1728645000, '2026-10-26 19:16:52'),
  ('notifications', 1728645000, '2026-10-26 19:16:52'),
  ('rnd', 1728645000, '2026-10-26 19:16:52'),
  ('users', 1791724033, '2026-10-26 19:16:52'),
  ('workshops', 1728645000, '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `version_timestamp` = VALUES(`version_timestamp`), `updated_at` = VALUES(`updated_at`);

-- Data for table `users`
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', 'Charan', '$2y$12$4UEnf0l1e/dEsA8PtrujXOjWsH3movWizobG5vHq.INjW/t65lg/i', 'Charan', 'saivortex.dev@gmail.com', 'developer', '1', '2026-10-01 09:00:00', '2026-10-26 19:16:52'),
  ('2', 'tcek', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'TCEK Administrator', 'officetcek@gmail.com', 'admin', '1', '2026-10-26 13:46:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `admins`
INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `created_at`) VALUES
  ('1', 'Charan', '$2y$12$4UEnf0l1e/dEsA8PtrujXOjWsH3movWizobG5vHq.INjW/t65lg/i', 'Charan', 'saivortex.dev@gmail.com', '2026-10-26 19:16:52'),
  ('2', 'tcek', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'TCEK Administrator', 'officetcek@gmail.com', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `announcements`
INSERT INTO `announcements` (`id`, `announcement_text`, `link_url`, `is_enabled`, `scrolling_speed`, `last_updated`, `show_last_updated`, `created_at`, `updated_at`) VALUES
  ('1', 'We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year 2025-2026 to 2029-2030 • NAAC Accredited • Admissions Open for Diploma, BTech and MBA (For Admissions Contact: 7396903383, 8522954369)', '', '1', '60', '09 October 2026', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `hero_content`
INSERT INTO `hero_content` (`id`, `title`, `subtitle`, `subtitle_icon`, `badge_left_text`, `badge_left_pulse`, `badge_right_text`, `badge_right_icon`, `cta_text`, `cta_link`, `auto_play`, `slide_interval`, `created_at`, `updated_at`) VALUES
  ('1', 'Excellence in Education', 'Inspiring Future Innovators Since 2008', 'fas fa-shield-halved', 'Campus Highlights', '0', 'NAAC \'B++\' Grade & Autonomous', 'fas fa-certificate', 'Admissions Open', 'admission.php', '1', '3600', '2026-10-26 19:16:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `hero_slides`
INSERT INTO `hero_slides` (`id`, `media_type`, `file_path`, `title`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
  ('1', 'image', 'assets/College Event/all1.jpg', 'College Event', '1', '2', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('2', 'image', 'assets/College Event/caps.jpg', 'Graduation', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('3', 'image', 'assets/College Event/feli1.jpg', 'Felicitation 1', '1', '3', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('4', 'image', 'assets/College Event/feli2.jpg', 'Felicitation 2', '1', '4', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('5', 'video', 'assets/College Event/autonomus.mp4', 'Autonomous Status Video', '1', '5', '2026-10-26 19:16:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `departments`
INSERT INTO `departments` (`id`, `dept_code`, `name`, `slug`, `degree_level`, `intake`, `duration`, `established_year`, `icon_class`, `theme_class`, `banner_image`, `tags`, `syllabus_url`, `peos_url`, `gallery_images`, `description`, `vision`, `mission`, `page_url`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', 'CSE', 'Computer Science & Engineering', 'cse', 'B.Tech', '120 Seats', '4 Years', '2008', 'fas fa-laptop-code', 'theme-cse', 'assets/courses/cse.png', 'Cloud & DevOps, Full Stack, Cyber Security', 'assets/Dept/R22B.Tech.CSEIandIIYearSyllabus.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Nurturing next-generation software architects, algorithms innovators, and full-stack technocrats since 2008.', 'To produce professional Computer Science Engineers who can meet the dynamic expectations of the global technology sector and contribute to the advancement of computing through creativity, innovation, and an outstanding learner-centric environment.', '1. Provide practical, qualitative technical education in modern laboratory environments to solve algorithmic and real-world software problems.\n2. Inculcate strong foundations in computer science core principles and interdisciplinary computing domains.\n3. Develop domain expertise and research skills that enable graduates to pursue rewarding careers and higher education.\n4. Instill ethical values, leadership qualities, and professional communication among students.', 'department.php?slug=cse', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('2', 'ECE', 'Electronics & Communication Engineering', 'ece', 'B.Tech', '60 Seats', '4 Years', '2008', 'fas fa-microchip', 'theme-ece', 'assets/courses/ece.png', 'VLSI Design, Embedded Systems, 5G & IoT', 'assets/Dept/R22B.Tech.ECEIIIYearSyllabus.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Empowering future communication engineers, VLSI circuit designers, and embedded IoT innovators.', 'To evolve into a center of excellence in Electronics and Communication Engineering education and research, developing socially conscious, technically sound engineering professionals.', '1. Foster excellence in core electronics, wireless communication, and embedded systems.\n2. Provide modern laboratory facilities aligned with industry standards.\n3. Encourage interdisciplinary research and product development.', 'department.php?slug=ece', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('3', 'EEE', 'Electrical & Electronics Engineering', 'eee', 'B.Tech', '60 Seats', '4 Years', '2008', 'fas fa-bolt', 'theme-eee', 'assets/courses/eee.png', 'Smart Grids, Power Systems, EV Tech', 'assets/Dept/R22B.Tech.EEEIandIIYearSyllabus2.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Driving innovation in clean energy, electric mobility, power electronics, and autonomous grid systems.', 'To be a premier department offering state-of-the-art education in electrical engineering, smart grids, and renewable energy technologies.', '1. Equip students with fundamental engineering knowledge and advanced hands-on expertise.\n2. Partner with electrical utility and EV industries for real-world projects.\n3. Impart high ethical standards and green engineering responsibility.', 'department.php?slug=eee', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('4', 'AIML', 'Artificial Intelligence & Machine Learning', 'aiml', 'B.Tech', '60 Seats', '4 Years', '2021', 'fas fa-brain', 'theme-aiml', 'assets/courses/aiml.png', 'Deep Learning, Neural Nets, Computer Vision', 'assets/Dept/R22B.Tech.CSE(AIML)CourseStructureSyllabus2.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Pioneering cutting-edge research in deep learning, neural networks, computer vision, and cognitive computing.', 'To develop ethical AI innovators and data scientists who engineer impactful artificial intelligence solutions for global challenges.', '1. Provide in-depth training in algorithms, mathematics, and high-performance computing.\n2. Engage in real-world AI applications across healthcare, finance, and robotics.\n3. Instill responsible AI ethics and intellectual curiosity.', 'department.php?slug=aiml', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('5', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'cse-aiml', 'B.Tech', '60 Seats', '4 Years', '2024', 'fas fa-robot', 'theme-cse-aiml', 'assets/courses/cse-aiml.jpg', 'GenAI & LLMs, Data Science, Smart Systems', 'assets/Dept/R22B.Tech.CSE(AIML)CourseStructureSyllabus2.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Integrating core computer systems engineering with generative AI, large language models, and autonomous intelligent systems.', 'To build world-class engineers specializing in generative intelligence, large models, and resilient scalable cloud infrastructure.', '1. Merge core software engineering with cutting-edge LLMs and machine intelligence.\n2. Foster industry-backed incubator projects.\n3. Champion research and lifelong technical agility.', 'department.php?slug=cse-aiml', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('6', 'H&S', 'Humanities & Sciences', 'h-s', 'B.Tech', 'All Intake', 'Foundation', '2008', 'fas fa-flask', 'theme-hs', 'assets/courses/hs.jpg', 'Applied Physics, Engineering Chemistry, Mathematics', 'assets/Dept/R22B.Tech.CSEIandIIYearSyllabus.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Building strong scientific, mathematical, and communicative foundations for first-year engineering scholars.', 'To establish strong scientific inquiry, mathematical rigor, and linguistic competence as the bedrock of engineering education.', '1. Deliver rigorous foundational courses in Mathematics, Physics, Chemistry, and English.\r\n2. Foster interdisciplinary thinking and experimental curiosity.\r\n3. Build professional soft skills, critical thinking, and character.', 'department.php?slug=hs', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('7', 'MBA', 'Masters in Business Administration', 'mba', 'MBA', '120 Seats', '2 Years', '2009', 'fas fa-briefcase', 'theme-mba', 'assets/courses/mba.jpg', 'Financial Analytics, Digital Marketing, Strategic HR, Corporate Leadership', 'assets/Dept/peos_psos.docx', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Developing strategic business leaders, financial analysts, human resource strategists, and innovative entrepreneurs.', 'To be a premier business school inspiring transformative business leadership and ethical entrepreneurship.', '1. Impart holistic management education blending analytics, finance, marketing, and HR.\n2. Facilitate executive mentorship and corporate internships.\n3. Cultivate entrepreneurial mindset and sustainable management practices.', 'department.php?slug=mba', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('8', 'DEEE', 'Electrical & Electronics Engineering (Diploma)', 'deee', 'Diploma', '60 Seats', '3 Years', '2013', 'fas fa-plug', 'theme-eee', 'assets/courses/eee.png', 'Circuit Design, Power Wiring, Automation', 'assets/Dept/R22B.Tech.EEEIandIIYearSyllabus2.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Hands-on polytechnic diploma program focusing on circuit design, electrical maintenance, power wiring, and industrial automation.', 'To prepare industry-ready diploma technicians equipped with practical electrical skills.', 'Deliver comprehensive workshop training and laboratory skills in electrical installations and machinery.', 'department.php?slug=deee', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('9', 'DECE', 'Electronics & Communication Engineering (Diploma)', 'dece', 'Diploma', '60 Seats', '3 Years', '2013', 'fas fa-satellite-dish', 'theme-ece', 'assets/courses/ece.png', 'Digital Circuits, Microcontrollers, Comms', 'assets/Dept/R22B.Tech.ECEIIIYearSyllabus.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Practical polytechnic diploma program in digital electronics, communication equipment testing, microcontrollers, and PCB design.', 'To cultivate skilled technical manpower in electronics fabrication and communication equipment.', 'Impart practical knowledge of electronics troubleshooting, microcontroller programming, and telecom gear.', 'department.php?slug=dece', '9', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('10', 'DCSE', 'Computer Science Engineering (Diploma)', 'dcse', 'Diploma', '60 Seats', '3 Years', '2023', 'fas fa-desktop', 'theme-cse', 'assets/courses/cse.png', 'Programming, Web Technologies, Databases', 'assets/Dept/R22B.Tech.CSEIandIIYearSyllabus.pdf', 'assets/Dept/peos_psos.docx', '[\"1.jpeg\",\"2.jpeg\",\"3.jpeg\",\"4.jpeg\",\"5.jpeg\",\"6.jpeg\",\"7.jpeg\",\"8.jpeg\",\"9.jpeg\",\"10.jpeg\"]', 'Polytechnic diploma focusing on application programming, computer maintenance, database administration, and web technologies.', 'To empower young learners with vocational proficiency in software coding and IT administration.', 'Deliver experiential training in software programming, database management, and computer hardware.', 'department.php?slug=dcse', '10', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `staff`
INSERT INTO `staff` (`id`, `full_name`, `dept_code`, `department`, `designation`, `role_category`, `is_hod`, `is_rnd`, `qualification`, `jntuh_reg_id`, `experience`, `email`, `phone`, `profile_image`, `bio`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', 'SWATHI JILLA', 'CSE', 'Computer Science & Engineering', 'HOD & Assoc. Prof', 'HOD', '1', '0', 'M.Tech', '2717-150427-180153', '15 Years', 'swathijilla@tcek.in', '9848000002', 'assets/Dept/hod-cse.jpeg', 'Mrs. J. Swathi is a distinguished academician, mentor, and researcher with over 15 years of rich experience in the field of Computer Science and Engineering. Serving as the Head of the Department at Trinity College of Engineering &amp; Technology, she has been instrumental in shaping academic curricula, implementing innovative teaching methods, and fostering a strong coding culture. Her primary areas of interest include Machine Learning, Deep Learning, Cloud Computing, and Blockchain Technology. She has actively guided numerous student capstone projects and research papers, bridging the gap between textbook concepts and industry practice.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('2', 'GADDAM LAKSHMI', 'CSE', 'Computer Science & Engineering', 'Associate Professor', 'R&D + Senior Faculty (SR)', '0', '1', 'M.Tech, UGC NET, (Ph.D)', '7259-150409-113641', '15 Years', 'gaddamlakshmi@tcek.in', '9848000003', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('3', 'SYED KHAJAPASHA', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0802-170126-125051', '10 Years', 'syedkhajapasha@tcek.in', '9848000004', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('4', 'ANJALI KOMUROJU', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '4513-190506-102113', '8 Years', 'anjalikomuroju@tcek.in', '9848000005', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('5', 'MOHAMMAD ZIAUDDIN', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '5682-170203-164956', '10 Years', 'mohammadziauddin@tcek.in', '9848000006', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('6', 'SYED MEER SUBHAN ALI', 'CSE', 'Computer Science & Engineering', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'M.Tech', '5682-170203-164956', '15 Years', 'syedmeersubhanali@tcek.in', '9848000007', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('7', 'KOTTE ANUSHA', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '5682-170203-164956', '5 Years', 'kotteanusha@tcek.in', '9848000008', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('8', 'PALLA SWATHI', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '5682-170203-164956', '5 Years', 'pallaswathi@tcek.in', '9848000009', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('9', 'JITTAVENI SRINIVAS', 'CSE', 'Computer Science & Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '5682-170203-164956', '3 Years', 'jittavenisrinivas@tcek.in', '9848000010', NULL, 'Faculty member in the Department of Computer Science & Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '9', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('10', 'Dr. M GANESH', 'ECE', 'Electronics & Communication Engineering', 'Professor & Principal', 'R&D + Senior Faculty (SR)', '0', '1', 'M.Tech, Ph.D', '6533-170912-154901', '12 Years', 'drmganesh@tcek.in', '9848000011', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('11', 'PRABHAKAR PARLAPALLI', 'ECE', 'Electronics & Communication Engineering', 'HOD & Assoc. Prof', 'HOD', '1', '0', 'M.Tech', '0117-150505-114948', '20 Years', 'prabhakarparlapalli@tcek.in', '9848000012', 'assets/Dept/hod-ece.jpeg', 'Welcome to the exciting world of Electronics and Communication Engineering at TCEK! As you embark on this transformative journey, you join a vibrant community of passionate learners, innovative thinkers, and dedicated educators. With over 20 years of teaching and mentoring experience, Mr. P. Prabhakar leads the department with a sharp focus on hands-on hardware laboratory training, VLSI chip design, embedded IoT prototyping, and signal processing. Under his direction, students actively take part in national symposiums, hackathons, and industrial field visits.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('12', 'GANTLA LAVANYA', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '1663-170203-183207', '6 Years', 'gantlalavanya@tcek.in', '9848000013', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('13', 'VODNALA MAMATHA', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '1235-170122-184453', '8 Years', 'vodnalamamatha@tcek.in', '9848000014', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('14', 'MADHU SHEKAR PITTALA', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0762-150425-103028', '10 Years', 'madhushekarpittala@tcek.in', '9848000015', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('15', 'SHAHJAHAN', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '6533-170912-154901', '10 Years', 'shahjahan@tcek.in', '9848000016', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('16', 'BURRA SRUJANA', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '3645-170918-134720', '4 Years', 'burrasrujana@tcek.in', '9848000017', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('17', 'DAMA SAMPATH', 'ECE', 'Electronics & Communication Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0534-160311-182640', '6 Years', 'damasampath@tcek.in', '9848000018', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('18', 'B RAJENDAR', 'ECE', 'Electronics & Communication Engineering', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'M.Tech, Ph.D', '6533-170912-154901', '12 Years', 'brajendar@tcek.in', '9848000019', NULL, 'Faculty member in the Department of Electronics & Communication Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '9', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('19', 'Dr. K. NATARAJAN', 'EEE', 'Electrical & Electronics Engineering', 'Professor & HOD', 'HOD', '1', '1', 'M.E, Ph.D', '7515-170420-141201', '14+ Years', 'drknatarajan@tcek.in', '9848000020', 'assets/Dept/hod-eee.jpeg', 'Dr. K. Natarajan obtained his B.E in Electrical &amp; Electronics Engineering from Sri Ramakrishna Institute of Technology, Coimbatore and M.E in Electrical Machines from the renowned PSG College of Technology, Coimbatore. He completed his Ph.D degree from Anna University, Chennai in 2017. With over 14 years of teaching and research experience at undergraduate and postgraduate levels, Dr. Natarajan has guided numerous student engineering projects, published articles in prestigious international journals, and spearheaded laboratory modernization in renewable energy and power electronics.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('20', 'VAMSHI KRISHNA KOMURAVELLI', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '75150405-174823', '15 Years', 'vamshikrishnakomuravelli@tcek.in', '9848000021', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('21', 'SHIVA KUMAR MUNJAM', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7734-150412-151225', '20 Years', 'shivakumarmunjam@tcek.in', '9848000022', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('22', 'SANTHOSH BANDI', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '4224-150416-153422', '16 Years', 'santhoshbandi@tcek.in', '9848000023', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('23', 'ARPULA KRISHNAIAH', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '1777-150419-12802', '15 Years', 'arpulakrishnaiah@tcek.in', '9848000024', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('24', 'KOPPULA SRINIVAS', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0016-170119-101553', '15 Years', 'koppulasrinivas@tcek.in', '9848000025', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('25', 'CHUNDURI SUPRIYA', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7565-180130-151342', '12 Years', 'chundurisupriya@tcek.in', '9848000026', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('26', 'VELPULA SWARUPA', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '8793-170126-091552', '8 Years', 'velpulaswarupa@tcek.in', '9848000027', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('27', 'ASHOK KUMAR GUDA', 'EEE', 'Electrical & Electronics Engineering', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '5286-150506-163956', '15 Years', 'ashokkumarguda@tcek.in', '9848000028', NULL, 'Faculty member in the Department of Electrical & Electronics Engineering at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '9', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('28', 'GADDAM LAKSHMI', 'AIML', 'Artificial Intelligence & Machine Learning', 'HOD & Assoc. Prof', 'HOD', '1', '1', 'M.Tech, UGC NET, (Ph.D)', '7259-150409-113641', '15+ Years', 'gaddamlakshmi@tcek.in', '9848000029', 'assets/Dept/Hod-aiml.jpg', 'Prof. Gaddam Lakshmi, Head of the Department of Artificial Intelligence &amp; Machine Learning (AIML), is a seasoned academician and researcher with over 15 years of distinguished service in higher technical education. She holds an M.Tech in Computer Science &amp; Engineering, has qualified the prestigious UGC NET, and is actively pursuing her doctoral research. Her research domains include Artificial Intelligence, Machine Learning Algorithms, Data Analytics, and Intelligent Computing Systems. Under her leadership, the department emphasizes practical problem-solving, laboratory excellence, and tailored mentorship to groom students into competitive technocrats ready for top global tech careers.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('29', 'G ANJANEYULU', 'AIML', 'Artificial Intelligence & Machine Learning', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'MCA, M.Tech, MA ENG, B.Ed', '2439-200306-160524', '16 Years', 'ganjaneyulu@tcek.in', '9848000030', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('30', 'MARUPAKA AMULYA', 'AIML', 'Artificial Intelligence & Machine Learning', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0352-170126-131206', '10 Years', 'marupakaamulya@tcek.in', '9848000031', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('31', 'MOHD ASEEM FEROZE', 'AIML', 'Artificial Intelligence & Machine Learning', 'Assistant Professor', 'R&D + Junior Faculty (JR)', '0', '1', 'M.Tech', '3136-160107-104243', '9 Years', 'mohdaseemferoze@tcek.in', '9848000032', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('32', 'POODARI LAVANYA', 'AIML', 'Artificial Intelligence & Machine Learning', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '3591-170205-154548', '8 Years', 'poodarilavanya@tcek.in', '9848000033', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('33', 'NAJIRIN', 'AIML', 'Artificial Intelligence & Machine Learning', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7575-200210-131406', '8 Years', 'najirin@tcek.in', '9848000034', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('34', 'SANDHYARANI ADAPA', 'AIML', 'Artificial Intelligence & Machine Learning', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7461-200227-134103', '8 Years', 'sandhyaraniadapa@tcek.in', '9848000035', NULL, 'Faculty member in the Department of Artificial Intelligence & Machine Learning at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('35', 'SWATHI JILLA', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'HOD & Assoc. Prof', 'HOD', '1', '0', 'M.Tech', '2717-150427-180153', '15 Years', 'swathijilla@tcek.in', '9848000036', 'assets/Dept/hod-cse.jpeg', 'Mrs. J. Swathi is a distinguished academician, mentor, and researcher with over 15 years of rich experience in the field of Computer Science and Engineering. Serving as the Head of the Department at Trinity College of Engineering &amp; Technology, she has been instrumental in shaping academic curricula, implementing innovative teaching methods, and fostering advanced technical learning. Her primary research domains include Machine Learning, Deep Neural Architectures, Cloud Computing, and Intelligent Computing Systems. She actively guides students in capstone software projects, AI implementations, and research publications, bridging academic theory with industry requirements.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('36', 'GADDAM LAKSHMI', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Associate Professor', 'R&D + Senior Faculty (SR)', '0', '1', 'M.Tech, UGC NET, (Ph.D)', '7259-150409-113641', '15+ Years', 'gaddamlakshmi@tcek.in', '9848000037', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('37', 'G ANJANEYULU', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'MCA, M.Tech, MA ENG, B.Ed', '2439-200306-160524', '16 Years', 'ganjaneyulu@tcek.in', '9848000038', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('38', 'MARUPAKA AMULYA', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '0352-170126-131206', '10 Years', 'marupakaamulya@tcek.in', '9848000039', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('39', 'MOHD ASEEM FEROZE', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Assistant Professor', 'R&D + Junior Faculty (JR)', '0', '1', 'M.Tech', '3136-160107-104243', '9 Years', 'mohdaseemferoze@tcek.in', '9848000040', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('40', 'POODARI LAVANYA', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '3591-170205-154548', '8 Years', 'poodarilavanya@tcek.in', '9848000041', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('41', 'NAJIRIN', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7575-200210-131406', '8 Years', 'najirin@tcek.in', '9848000042', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('42', 'SANDHYARANI ADAPA', 'CSE-AIML', 'Computer Science & Engineering (AI & ML)', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', '7461-200227-134103', '8 Years', 'sandhyaraniadapa@tcek.in', '9848000043', NULL, 'Faculty member in the Department of Computer Science & Engineering (AI & ML) at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('43', 'Dr. Ashok Kumar Vootla', 'H&S', 'Humanities & Sciences', 'Professor & Director', 'R&D + Senior Faculty (SR)', '0', '1', 'Ph.D', 'N/A', '17 Years', 'drashokkumarvootla@tcek.in', '9848000044', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('44', 'P. PADMINI', 'H&S', 'Humanities & Sciences', 'HOD & Asst. Prof', 'HOD', '1', '0', 'M.Sc (Mathematics)', 'N/A', '15 Years', 'ppadmini@tcek.in', '9848000045', 'assets/Dept/hod-h&s.jpeg', 'Mrs. Padmini Pachwa, Head of the Department of Humanities &amp; Sciences, brings over 15 years of multifaceted academic, training, and institutional experience. She completed her M.Sc in Mathematics in 2001 and has guided thousands of first-year engineering students through their crucial academic transition. With specialized expertise in student mentoring, personality enhancement, and mathematical pedagogy, she has published several papers in reputed national and international journals. Under her guidance, the department focuses on disciplined study habits, communicative self-confidence, and foundational scientific competence.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('45', 'N. MAHENDAR', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.A (English)', 'N/A', '7 Years', 'nmahendar@tcek.in', '9848000046', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('46', 'G. SRINIVAS', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.A (English)', 'N/A', '3 Years', 'gsrinivas@tcek.in', '9848000047', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('47', 'ASIA BEGUM', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.A (English)', 'N/A', '3 Years', 'asiabegum@tcek.in', '9848000048', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('48', 'V. SRINIVAS', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Chemistry)', 'N/A', '6 Years', 'vsrinivas@tcek.in', '9848000049', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('49', 'HUMERA AMREEN', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Chemistry)', 'N/A', '3 Years', 'humeraamreen@tcek.in', '9848000050', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('50', 'M. SUSHMA RANI', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Chemistry)', 'N/A', '1 Year', 'msushmarani@tcek.in', '9848000051', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('51', 'B. JHANSI RANI', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Physics)', 'N/A', '3 Years', 'bjhansirani@tcek.in', '9848000052', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '9', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('52', 'T. NANDITHA', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Physics)', 'N/A', '2 Years', 'tnanditha@tcek.in', '9848000053', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '10', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('53', 'SD. KALIMUNISSA', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Sc (Physics)', 'N/A', '3 Years', 'sdkalimunissa@tcek.in', '9848000054', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '11', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('54', 'B. RAMAMURTHY', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '5 Years', 'bramamurthy@tcek.in', '9848000055', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '12', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('55', 'S. VINAY KUMAR', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '4 Years', 'svinaykumar@tcek.in', '9848000056', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '13', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('56', 'A. VIKAS', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '1 Year', 'avikas@tcek.in', '9848000057', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '14', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('57', 'V. MAMATHA', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '3 Years', 'vmamatha@tcek.in', '9848000058', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '15', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('58', 'T. SAMPATH KUMAR', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '2 Years', 'tsampathkumar@tcek.in', '9848000059', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '16', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('59', 'J. SURESH', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.Tech', 'N/A', '3 Years', 'jsuresh@tcek.in', '9848000060', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '17', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('60', 'A. SRILATHA', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.B.A', 'N/A', '3 Years', 'asrilatha@tcek.in', '9848000061', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '18', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('61', 'N. ARUNA JYOTHI', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.B.A', 'N/A', '3 Years', 'narunajyothi@tcek.in', '9848000062', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '19', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('62', 'K. SRINIVAS', 'H&S', 'Humanities & Sciences', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'M.B.A', 'N/A', '3 Years', 'ksrinivas@tcek.in', '9848000063', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '20', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('63', 'A. VIJAYA', 'H&S', 'Humanities & Sciences', 'Librarian', 'Faculty – Junior (JR)', '0', '0', 'M.L.I.Sc', 'N/A', '11 Years', 'avijaya@tcek.in', '9848000064', NULL, 'Faculty member in the Department of Humanities & Sciences at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '21', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('64', 'Dr. ARIF ARFAT', 'MBA', 'Masters in Business Administration', 'HOD & Assoc. Prof', 'HOD', '1', '1', 'MBA, M.Com, NET, SET, Ph.D', '0506-150408-132239', '17 Years', 'drarifarfat@tcek.in', '9848000065', 'assets/Dept/hod-mba.jpg', 'Dr. Arif Arafat, Head of the Department of MBA, possesses over 17 years of rich professional experience spanning corporate sales management, academic instruction, and executive development. He completed his MBA in 2006 and was awarded his Doctorate (Ph.D) in 2019. A qualified UGC NET and SET scholar, Dr. Arafat specializes in organizational behavior, marketing strategy, and student training. He has published numerous research papers in Scopus-indexed and UGC-CARE journals and actively serves as the Training &amp; Placement coordinator for corporate drives.', '1', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('65', 'Dr. AMBIKA', 'MBA', 'Masters in Business Administration', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'MBA, Ph.D', '0506-150408-132239', '14 Years', 'drambika@tcek.in', '9848000066', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '2', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('66', 'Dr. RAJIDI RAMMOHAN REDDY', 'MBA', 'Masters in Business Administration', 'Associate Professor', 'Faculty – Senior (SR)', '0', '0', 'MBA, NET, Ph.D', '3696-150422-172652', '15 Years', 'drrajidirammohanreddy@tcek.in', '9848000067', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '3', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('67', 'PARSHA RAMESH', 'MBA', 'Masters in Business Administration', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'MBA', '9898-170915-153011', '10 Years', 'parsharamesh@tcek.in', '9848000068', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '4', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('68', 'VENKATESWARLU NAGULA', 'MBA', 'Masters in Business Administration', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'MBA', '2590-150415-181625', '10 Years', 'venkateswarlunagula@tcek.in', '9848000069', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '5', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('69', 'VAMSI KRISHNA BARIBADDALA', 'MBA', 'Masters in Business Administration', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'MBA, M.Com, SET', '52150405-120550', '11 Years', 'vamsikrishnabaribaddala@tcek.in', '9848000070', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '6', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('70', 'NASPURI SAI SHRAVANI', 'MBA', 'Masters in Business Administration', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'MBA', '5545-220124-130408', '3 Years', 'naspurisaishravani@tcek.in', '9848000071', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '7', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52'),
  ('71', 'SRI LATHA THANGELLAPALLI', 'MBA', 'Masters in Business Administration', 'Assistant Professor', 'Faculty – Junior (JR)', '0', '0', 'MBA', '0755-150408-1404212', '5 Years', 'srilathathangellapalli@tcek.in', '9848000072', NULL, 'Faculty member in the Department of Masters in Business Administration at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.', '8', '1', '2026-10-26 19:16:52', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `events`
INSERT INTO `events` (`id`, `title`, `event_date`, `event_time`, `venue`, `description`, `image_path`, `video_path`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', 'Freshers Aarambh 2K26', '2026-10-12', '10:00 AM – 05:00 PM', 'Trinity Campus Auditorium', 'Welcoming the incoming batch of engineers and technocrats to the Trinity family with electrifying music, dazzling dance performances, interactive fun games, and unforgettable memories!', 'assets/events/tcek-fresher.jpg', 'assets/events/freshers.mp4', '1', '1', '2026-10-01 10:00:00', '2026-10-26 19:16:52'),
  ('2', 'College Sports Week', '2026-10-01', '09:00 AM – 05:00 PM', 'Trinity Sports Ground', 'A week of energy, talent & togetherness — cricket championship, kabaddi tournaments, badminton and athletic competitions.', 'assets/events/tcek-poster.jpg', 'assets/events/cricket-campaigns.mp4', '0', '1', '2026-09-28 09:00:00', '2026-10-26 19:16:52'),
  ('3', 'Flash Mob Dance Showcase', '2026-10-09', '04:00 PM – 05:30 PM', 'Main Campus Plaza', 'High-voltage dance showcase featuring Trinity students, electrifying music, and creative choreography.', NULL, NULL, '0', '1', '2026-10-02 14:00:00', '2026-10-26 19:16:52'),
  ('4', 'Traditional & Bathukamma Fest', '2026-10-13', '10:00 AM – 04:00 PM', 'Central Quadrangle', 'Heritage, flowers, authentic cultural fest and celebrations honoring regional traditions.', NULL, NULL, '0', '1', '2026-10-03 10:00:00', '2026-10-26 19:16:52'),
  ('5', 'Autonomous Status Felicitation', '2026-09-25', '11:00 AM – 02:00 PM', 'Main Auditorium', 'Grand celebration on UGC granting Autonomous Status to Trinity College of Engineering and Technology.', NULL, 'assets/College Event/autonomus.mp4', '0', '1', '2026-09-24 11:00:00', '2026-10-26 19:16:52'),
  ('6', 'Annual Convocation & Graduation Day', '2026-09-18', '10:00 AM – 01:30 PM', 'Open Air Amphitheatre', 'Graduating engineers celebrating academic degrees, medals, and milestone achievements with parents and mentors.', 'assets/College Event/caps.jpg', NULL, '0', '1', '2026-09-17 10:00:00', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `event_media`
INSERT INTO `event_media` (`id`, `event_id`, `media_type`, `file_path`, `media_title`, `media_description`, `created_at`) VALUES
  ('1', '1', 'video', 'assets/events/freshers.mp4', 'Freshers Aarambh 2K26 Celebration', 'Official highlight video of Freshers Aarambh 2K26 at Trinity College of Engineering & Technology.', '2026-10-01 10:00:00'),
  ('2', '1', 'image', 'assets/events/tcek-fresher.jpg', 'Freshers Aarambh 2K26 Official Event Poster', 'Official creative poster announcing Freshers Day on 12th October 2026.', '2026-10-01 10:05:00'),
  ('3', '2', 'video', 'assets/events/cricket-campaigns.mp4', 'AIML & CSE Cricket Campaigns', 'College Sports Week 2026 cricket championship clashes between Department of AIML and Department of CSE.', '2026-10-02 11:00:00'),
  ('4', '2', 'video', 'assets/events/kabaddi-wins.mp4', 'Kabaddi Championship Wins', 'Sensational raid points, tackles, and trophy celebration in the annual college Kabaddi tournament.', '2026-10-03 16:30:00'),
  ('5', '2', 'image', 'assets/events/tcek-poster.jpg', 'Sports & Cultural Week Schedule', 'Complete 2-week schedule across sports, flash mob and traditional days.', '2026-09-29 10:00:00'),
  ('6', '5', 'video', 'assets/College Event/autonomus.mp4', 'Autonomous Status Felicitation', 'Special institutional felicitation ceremonies celebrating UGC Autonomous conferment.', '2026-09-25 15:00:00'),
  ('7', '6', 'image', 'assets/College Event/caps.jpg', 'Graduation Day Ceremony', 'Graduating engineers tossing convocation caps in celebration.', '2026-09-18 14:00:00'),
  ('8', '6', 'image', 'assets/College Event/feli1.jpg', 'Merit Felicitation Ceremony', 'Recognizing outstanding student achievers, rank holders and sports stars.', '2026-09-18 14:30:00')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `notifications`
INSERT INTO `notifications` (`id`, `title`, `category`, `description`, `attachment_type`, `attachment_path`, `link_url`, `is_marquee`, `publish_date`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', 'B.Tech Autonomous End Semester Examination Schedule AY 2026-27', 'Circular', 'Official circular regarding semester end regular & supplementary examination timetables, fee payment schedule, and hall ticket issuance.', 'pdf', 'uploads/pdfs/exam_schedule_2026.pdf', NULL, '1', '2026-10-08', '1', '2026-10-08 09:30:00', '2026-10-26 19:16:52'),
  ('2', 'Admissions Guidelines & Merit Fee Concession Policy 2026-27', 'Circular', 'Guidelines for B.Tech, Polytechnic Diploma, and MBA admissions counseling under EAPCET, POLYCET and ICET. Code: TCEK.', 'docx', 'uploads/documents/admission_guidelines_2026.docx', 'admission.php', '1', '2026-10-05', '1', '2026-10-05 11:15:00', '2026-10-26 19:16:52'),
  ('4', 'Autonomous Academic Council Regulations & Syllabi Notification', 'Circular', 'Detailed curriculum regulations approved by the Academic Council for B.Tech CSE, AIML, ECE, and Allied branches under Autonomous status.', 'pdf', 'uploads/pdfs/autonomous_notification_ay2026.pdf', 'academics.php', '0', '2026-09-28', '1', '2026-09-28 10:00:00', '2026-10-26 19:16:52'),
  ('5', 'Urgent Fee Payment Circular for Autonomous Batches', 'Circular', 'Last date to clear semester examination fee is 25th October 2026 without fine.', 'pdf', 'uploads/pdfs/autonomous_notification_ay2026.pdf', NULL, '1', '2026-10-10', '1', '2026-10-08 17:09:41', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `notices`
INSERT INTO `notices` (`id`, `title`, `category`, `badge`, `description`, `file_path`, `link_url`, `publish_date`, `is_pinned`, `is_active`, `created_at`) VALUES
  ('1', 'B.Tech Autonomous End Semester Examination Schedule AY 2026-27', 'Circular', 'NEW', 'Official circular regarding semester end regular & supplementary examination timetables, fee payment schedule, and hall ticket issuance.', 'uploads/pdfs/exam_schedule_2026.pdf', NULL, '2026-10-08', '1', '1', '2026-10-08 09:30:00'),
  ('2', 'Admissions Guidelines & Merit Fee Concession Policy 2026-27', 'Circular', 'NEW', 'Guidelines for B.Tech, Polytechnic Diploma, and MBA admissions counseling under EAPCET, POLYCET and ICET. Code: TCEK.', 'uploads/documents/admission_guidelines_2026.docx', 'admission.php', '2026-10-05', '1', '1', '2026-10-05 11:15:00'),
  ('4', 'Autonomous Academic Council Regulations & Syllabi Notification', 'Circular', 'NEW', 'Detailed curriculum regulations approved by the Academic Council for B.Tech CSE, AIML, ECE, and Allied branches under Autonomous status.', 'uploads/pdfs/autonomous_notification_ay2026.pdf', 'academics.php', '2026-09-28', '0', '1', '2026-09-28 10:00:00'),
  ('5', 'Urgent Fee Payment Circular for Autonomous Batches', 'Circular', 'NEW', 'Last date to clear semester examination fee is 25th October 2026 without fine.', 'uploads/pdfs/autonomous_notification_ay2026.pdf', NULL, '2026-10-10', '1', '1', '2026-10-08 17:09:41')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `news`
INSERT INTO `news` (`id`, `title`, `image_path`, `publish_date`, `description`, `source`, `link_url`, `status`, `created_at`, `updated_at`) VALUES
  ('1', 'Yuva Sangam: Trinity Student Selected for National Tour to IIT Guwahati', 'assets/Gallery/paper1.jpg', '2026-10-01', 'Mana Telangana: Trinity College student Saniya selected for the prestigious Yuva Sangam national youth exposure tour to IIT Guwahati under the Ek Bharat Shreshtha Bharat initiative.', 'Mana Telangana', NULL, 'PUBLISHED', '2026-10-01 10:00:00', '2026-10-26 19:16:52'),
  ('2', 'Chairman Manohar Reddy Felicitates Saniya on National Yuva Sangam Selection', 'assets/Gallery/paper2.jpg', '2026-09-28', 'Prajakranthi: Founder Chairman Sri Manohar Reddy and administrative management felicitate B.Tech student Saniya for selection in the national delegation.', 'Prajakranthi', NULL, 'PUBLISHED', '2026-09-28 11:30:00', '2026-10-26 19:16:52'),
  ('3', 'Engineering Colleges Under Vigilance Radar: Quality & Compliance Inspections Begin', 'assets/Gallery/paper3.jpg', '2026-09-22', 'Andhra Prabha: Vigilance and accreditation inspection teams evaluate engineering colleges across the state on faculty standards, research facilities, and lab infrastructure.', 'Andhra Prabha', NULL, 'PUBLISHED', '2026-09-22 09:15:00', '2026-10-26 19:16:52'),
  ('4', 'Quality Benchmark & Lab Infrastructure Vigilance Inspection Team Visit', 'assets/Gallery/paper4.jpg', '2026-09-18', 'State Daily: Inspection team praises Trinity College for state-of-the-art laboratory infrastructure, AI & Robotics center of excellence, and qualified faculty.', 'State Daily', NULL, 'PUBLISHED', '2026-09-18 14:00:00', '2026-10-26 19:16:52'),
  ('5', 'Smart India Hackathon 2026 Conducted at Trinity Engineering College', 'assets/Gallery/paper5.jpg', '2026-09-10', 'Andhra Prabha: Grand launch of Smart India Hackathon internal round at Trinity campus with over 200 enthusiastic engineering students competing across hardware and software domains.', 'Andhra Prabha', NULL, 'PUBLISHED', '2026-09-10 10:30:00', '2026-10-26 19:16:52'),
  ('6', 'Smart India Hackathon 2026: Students Showcase Real-World Technical Prototypes', 'assets/Gallery/paper6.jpg', '2026-09-08', 'Mana Telangana: Student innovators exhibited functional IoT and machine learning prototypes solving real-world agricultural and municipal problems.', 'Mana Telangana', NULL, 'PUBLISHED', '2026-09-08 12:00:00', '2026-10-26 19:16:52'),
  ('7', 'Smart India Hackathon 2026: Innovative Problem Solving at Trinity Autonomous', 'assets/Gallery/paper7.jpg', '2026-09-05', 'Namasthe Telangana: Jury members appreciate the high technical caliber of projects developed during 36-hour hackathon coding sprint.', 'Namasthe Telangana', NULL, 'PUBLISHED', '2026-09-05 16:45:00', '2026-10-26 19:16:52'),
  ('8', 'MSME Hackathon 6.0: 168 Student Project Submissions with ₹15L Funding Support', 'assets/Gallery/paper8.jpg', '2026-08-25', 'Mana Telangana: Under the Ministry of MSME Idea Hackathon 6.0, Trinity Host Institute shortlisted outstanding startup ideas eligible for up to ₹15 Lakhs central government grant funding.', 'Mana Telangana', NULL, 'PUBLISHED', '2026-08-25 11:00:00', '2026-10-26 19:16:52'),
  ('9', 'MSME Hackathon 6.0: Nurturing Youth Innovation Across 6 Thematic Sectors', 'assets/Gallery/paper9.jpg', '2026-08-22', 'Namasthe Telangana: Engineering innovators pitched scalable business and technical models in healthcare, defense, and green technology.', 'Namasthe Telangana', NULL, 'PUBLISHED', '2026-08-22 15:30:00', '2026-10-26 19:16:52'),
  ('10', 'Tremendous Response to MSME Hackathon 6.0 with Up to ₹15 Lakhs Grant', 'assets/Gallery/paper10.jpg', '2026-08-18', 'Andhra Prabha: Record number of participants submitted technological business solutions for government incubation funding.', 'Andhra Prabha', NULL, 'PUBLISHED', '2026-08-18 10:15:00', '2026-10-26 19:16:52'),
  ('11', 'MSME Idea Hackathon 6.0 Successfully Conducted at Trinity Campus', 'assets/Gallery/paper11.jpg', '2026-08-15', 'Eenadu Daily: College leadership and industry experts evaluated student pitches during the nationwide entrepreneurship program.', 'Eenadu Daily', NULL, 'PUBLISHED', '2026-08-15 17:00:00', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `workshops`
INSERT INTO `workshops` (`id`, `title`, `instructor`, `category`, `event_date`, `venue`, `status`, `description`, `created_at`, `updated_at`) VALUES
  ('1', 'Generative AI & LLM Deployment Workshop', 'Dr. A. K. Vootla', 'AI / ML', '2026-10-15', 'CSE Lab 3', 'ACTIVE', 'Hands-on development of full-stack AI applications with Python & PyTorch.', '2026-10-01 09:00:00', '2026-10-26 19:16:52'),
  ('2', 'Full-Stack Web Dev Sprint (PHP, MySQL)', 'Charan (Lead Developer)', 'Web Dev', '2026-10-22', 'Seminar Hall A', 'UPCOMING', 'Live deployment to GoDaddy cPanel hosting, database triggers, and auth.', '2026-10-02 10:15:00', '2026-10-26 19:16:52'),
  ('3', 'IoT Smart Embedded Robotics Task', 'Prof. S. Rao (ECE HoD)', 'Embedded / IoT', '2026-11-05', 'Robotics Studio', 'UPCOMING', 'Microcontroller sensors, Arduino, and ESP32 wireless telemetry.', '2026-10-03 11:30:00', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `rnd_settings`
INSERT INTO `rnd_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
  ('auto_publish', '1', '2026-10-26 19:16:52'),
  ('last_updated', '2026-10-11 11:03:01', '2026-10-26 19:16:52'),
  ('latest_academic_year', '2025-26', '2026-10-26 19:16:52'),
  ('latest_quarter', 'Q4', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `rnd_rankings`
INSERT INTO `rnd_rankings` (`id`, `academic_year`, `quarter`, `quarter_label`, `department`, `department_name`, `rank`, `award_category`, `award_badge`, `description`, `achievements`, `criteria_tags`, `status`, `publication_date`, `score`, `created_at`, `updated_at`) VALUES
  ('1', '2025-26', 'Q4', 'JAN 2026 – MAR 2026', 'AIML', 'Artificial Intelligence & Machine Learning', '1', '🥇 1st · Gold', '🏆 Gold Champion', 'Outstanding achievements in high-impact research, patent leads and NPTEL excellence.', '[\"High-Impact Research Papers\",\"Patents & Innovation Leads\",\"NPTEL & IIC Star Rating\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\",\"NPTEL Honors\",\"IIC Initiatives\"]', 'published', '2026-03-31', '96.50', '2026-03-31 09:00:00', '2026-10-26 19:16:52'),
  ('2', '2025-26', 'Q4', 'JAN 2026 – MAR 2026', 'CSE & CSM', 'Computer Science & Engineering / CSM', '2', '🥈 2nd · Bronze', '🎖️ Bronze Honor', 'Superb coding hackathon initiatives and international technical journal publications.', '[\"Coding & Hackathon Projects\",\"Technical Publications\",\"Active IIC Engagement\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\",\"NPTEL Honors\",\"IIC Initiatives\"]', 'published', '2026-03-31', '92.00', '2026-03-31 09:00:00', '2026-10-26 19:16:52'),
  ('3', '2025-26', 'Q4', 'JAN 2026 – MAR 2026', 'EEE', 'Electrical & Electronics Engineering', '3', '🥉 3rd · Bronze', '🎖️ Bronze Honor', 'Green energy innovations and faculty research publications in renewable technologies.', '[\"Mini Hydel & Power Labs\",\"Green Energy Innovations\",\"Faculty Research Papers\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\",\"NPTEL Honors\",\"IIC Initiatives\"]', 'published', '2026-03-31', '88.50', '2026-03-31 09:00:00', '2026-10-26 19:16:52'),
  ('4', '2025-26', 'Q4', 'JAN 2026 – MAR 2026', 'ECE', 'Electronics & Communication Engineering', '4', '🏅 4th · Emerging', '🌱 Emerging', 'Strong growth in IoT embedded systems and rising student NPTEL enrollments.', '[\"Embedded & IoT Systems\",\"Signal Processing Projects\",\"Rising NPTEL Enrolments\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\",\"NPTEL Honors\",\"IIC Initiatives\"]', 'published', '2026-03-31', '84.00', '2026-03-31 09:00:00', '2026-10-26 19:16:52'),
  ('5', '2025-26', 'Q4', 'JAN 2026 – MAR 2026', 'MBA', 'Department of Management Studies', '5', '🏅 5th · Emerging', '🌱 Emerging', 'Dynamic business incubation meets and entrepreneurial case study research.', '[\"Business Case Studies\",\"Entrepreneurship Cell\",\"Startup Incubation Meets\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\",\"NPTEL Honors\",\"IIC Initiatives\"]', 'published', '2026-03-31', '81.50', '2026-03-31 09:00:00', '2026-10-26 19:16:52'),
  ('6', '2025-26', 'Q3', 'OCT 2025 – DEC 2025', 'CSE & CSM', 'Computer Science & Engineering / CSM', '1', '🥇 1st · Gold', '🏆 Gold Champion', 'National hackathon champions and 5 Scopus journal indexed publications.', '[\"National Hackathon Winners\",\"5 Scopus Journal Publications\",\"Top NPTEL Domain Certification\"]', '[\"Research & Innovation\",\"Patents & IPR\",\"Scopus & IEEE Publications\"]', 'published', '2025-12-31', '95.00', '2025-12-31 09:00:00', '2026-10-26 19:16:52'),
  ('7', '2025-26', 'Q3', 'OCT 2025 – DEC 2025', 'AIML', 'Artificial Intelligence & Machine Learning', '2', '🥈 2nd · Silver', '🥈 Silver Star', 'Smart India Hackathon finalists and two published generative AI patents.', '[\"AI\\/ML Innovation Hub Leads\",\"2 Published Patents\",\"Smart India Hackathon Finalists\"]', '[\"Research & Innovation\",\"Patents & IPR\"]', 'published', '2025-12-31', '91.50', '2025-12-31 09:00:00', '2026-10-26 19:16:52'),
  ('8', '2025-26', 'Q3', 'OCT 2025 – DEC 2025', 'ECE', 'Electronics & Communication Engineering', '3', '🥉 3rd · Bronze', '🎖️ Bronze Honor', 'IoT prototype demonstrations and IEEE VLSI workshop publications.', '[\"IoT Prototype Demos\",\"Faculty Research Grants\",\"VLSI Workshop Publications\"]', '[\"Research & Innovation\",\"Scopus & IEEE Publications\"]', 'published', '2025-12-31', '87.00', '2025-12-31 09:00:00', '2026-10-26 19:16:52'),
  ('9', '2025-26', 'Q3', 'OCT 2025 – DEC 2025', 'EEE', 'Electrical & Electronics Engineering', '4', '🏅 4th · Emerging', '🌱 Emerging', 'Solar microgrid design publications and IEEE student branch drives.', '[\"Smart Grid Energy Papers\",\"IEEE Student Chapter Drives\",\"Solar Lab Installations\"]', '[\"Research & Innovation\",\"NPTEL Honors\"]', 'published', '2025-12-31', '83.50', '2025-12-31 09:00:00', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `gallery`
INSERT INTO `gallery` (`id`, `title`, `media_type`, `category`, `file_path`, `video_url`, `description`, `created_at`) VALUES
  ('1', 'Grand Academic Felicitation Ceremony', 'image', 'events', 'assets/College Event/feli1.jpg', NULL, 'Felicitation of top rankers and university gold medalists.', '2026-10-26 19:16:52'),
  ('2', 'Faculty & Student Honors Presentation', 'image', 'events', 'assets/College Event/feli2.jpg', NULL, 'Annual academic accolades and faculty research citations.', '2026-10-26 19:16:52'),
  ('3', 'College Assembly & Annual Gathering', 'image', 'events', 'assets/College Event/all1.jpg', NULL, 'Campus-wide gathering at the main college auditorium.', '2026-10-26 19:16:52'),
  ('4', 'Cultural Fest & Student Celebrations', 'image', 'events', 'assets/Gallery/im.jpg', NULL, 'Vibrant stage performances and student dance competitions.', '2026-10-26 19:16:52'),
  ('5', 'Traditional Day Celebrations', 'image', 'events', 'assets/Gallery/im1.jpg', NULL, 'Celebrating rich cultural heritage and student harmony.', '2026-10-26 19:16:52'),
  ('6', 'Annual Sports Meet & Athletics', 'image', 'events', 'assets/Gallery/im2.jpg', NULL, 'Inter-college cricket, volleyball, and athletics championships.', '2026-10-26 19:16:52'),
  ('7', 'Campus Day Festival Celebrations', 'image', 'events', 'assets/Gallery/im3.jpg', NULL, 'Joyous graduation and campus day celebration.', '2026-10-26 19:16:52'),
  ('8', 'UGC Autonomous Status Announcement', 'image', 'milestones', 'assets/Gallery/autonomous.jpg', NULL, 'UGC confers Autonomous Institution status to Trinity College.', '2026-10-26 19:16:52'),
  ('9', 'NAAC B++ Grade Accreditation Recognition', 'image', 'milestones', 'assets/Gallery/naac2.jpg', NULL, 'National Assessment and Accreditation Council B++ Grade accreditation.', '2026-10-26 19:16:52'),
  ('10', 'Infosys & Multi-Company Campus Drive', 'image', 'milestones', 'assets/Gallery/infosys.jpg', NULL, 'Prestigious corporate recruitment drives and top placement packages.', '2026-10-26 19:16:52'),
  ('11', 'Advanced Computer Science Lab', 'image', 'campus', 'assets/Gallery/im4.jpg', NULL, 'State-of-the-art computational lab equipped with high-end workstations.', '2026-10-26 19:16:52'),
  ('12', 'Electronics & VLSI Testing Workstation', 'image', 'campus', 'assets/Gallery/im5.jpg', NULL, 'Electronics hardware prototyping and microchip design lab.', '2026-10-26 19:16:52'),
  ('13', 'Central Library & Knowledge Resource Center', 'image', 'campus', 'assets/Gallery/im6.jpg', NULL, 'Over 50,000 volumes, international IEEE journals, and digital e-learning zone.', '2026-10-26 19:16:52'),
  ('14', 'Main College Campus & Lawns', 'image', 'campus', 'assets/Gallery/im8.jpg', NULL, 'Lush green campus environment designed for holistic learning.', '2026-10-26 19:16:52'),
  ('15', 'Engineering Innovation Workshops', 'image', 'campus', 'assets/Gallery/IMG-1.jpg', NULL, 'Hands-on machine fabrication, robotic welding, and additive manufacturing.', '2026-10-26 19:16:52'),
  ('16', 'Air-Conditioned Seminar Hall', 'image', 'campus', 'assets/Gallery/IMG-4.jpg', NULL, 'Modern seminar hall with integrated surround sound & projection systems.', '2026-10-26 19:16:52'),
  ('17', 'Smart Classrooms & Digital Projectors', 'image', 'campus', 'assets/Gallery/IMG-5.jpg', NULL, 'Interactive smart teaching boards and digital multimedia classrooms.', '2026-10-26 19:16:52'),
  ('18', 'Sports Grounds & Physical Education', 'image', 'campus', 'assets/Gallery/IMG-6.jpg', NULL, 'Full-sized cricket ground, basketball, and volleyball courts.', '2026-10-26 19:16:52'),
  ('19', 'Student Technical Project Exhibition', 'image', 'campus', 'assets/Gallery/IMG-7.jpg', NULL, 'Annual technical symposium and prototype project demonstration.', '2026-10-26 19:16:52'),
  ('20', 'Central Auditorium & Cultural Stage', 'image', 'campus', 'assets/Gallery/IMG-8.jpg', NULL, '1,500 capacity auditorium hosting international conferences.', '2026-10-26 19:16:52'),
  ('21', 'College Administrative Complex', 'image', 'campus', 'assets/Gallery/IMG2.jpg', NULL, 'Single-window student service cell and administrative headquarters.', '2026-10-26 19:16:52')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Data for table `activity_logs`
INSERT INTO `activity_logs` (`id`, `admin_name`, `action`, `module`, `record_name`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
  ('1', 'Charan', 'Updated', 'Events', 'Aarambh-2K26', '1', 'Updated venue details, chief guest schedule and celebration timings', '127.0.0.1', '2026-10-08 17:26:10'),
  ('2', 'tcek', 'Added', 'Notifications', 'Urgent Fee Payment Circular', '1', 'Published Autonomous semester end fee circular with attached document', '127.0.0.1', '2026-10-08 16:41:11'),
  ('3', 'Admin', 'Added', 'News', 'MSME Hackathon 6.0 News Release', '2', 'Uploaded newspaper press clipping to public News & Media gallery', '127.0.0.1', '2026-10-08 14:41:11'),
  ('4', 'Charan', 'Login', 'Users', 'Master Admin Portal', '1', 'Successful authentication into TCEK Administration Control Console', '127.0.0.1', '2026-10-08 12:41:11'),
  ('5', 'Admin2', 'Deleted', 'Events', 'Old Sports Day Media Item', '8', 'Deleted duplicate photo from annual festival media queue', '127.0.0.1', '2026-10-07 17:41:11'),
  ('6', 'tcek', 'Added', 'Workshops', 'Full Stack AI Bootcamp', '3', 'Configured task schedule and student participation roster', '127.0.0.1', '2026-10-06 17:41:11'),
  ('7', 'Charan', 'Deleted', 'Circulars', 'Campus Placement Drive by Top Tier-1 Tech MNCs', '3', 'Removed circular and associated document', '::1', '2026-10-08 18:07:22'),
  ('8', 'Charan', 'Login', 'Users', 'Charan', '1', 'User authenticated into admin portal (Local Store)', '127.0.0.1', '2026-10-09 05:54:26'),
  ('9', 'System', 'Added', 'Departments', 'Robotics & Automation', '11', 'Created new department (TEST_ROBO)', '127.0.0.1', '2026-10-09 05:56:28'),
  ('10', 'System', 'Updated', 'Departments', 'Robotics & Industrial Automation', '11', 'Updated department TEST_ROBO', '127.0.0.1', '2026-10-09 05:56:28'),
  ('11', 'System', 'Updated', 'Departments', 'Robotics & Industrial Automation', '11', 'Updated department TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('12', 'System', 'Added', 'Faculty', 'Dr. Alan Turing', '72', 'Added faculty member to TEST_ROBO (HOD)', '127.0.0.1', '2026-10-09 06:01:49'),
  ('13', 'System', 'Added', 'Faculty', 'Dr. Ada Lovelace', '73', 'Added faculty member to TEST_ROBO (R&D + Senior Faculty (SR))', '127.0.0.1', '2026-10-09 06:01:49'),
  ('14', 'System', 'Updated', 'Faculty', 'Dr. Ada Lovelace', '73', 'Assigned as Head of Department for TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('15', 'System', 'Added', 'Faculty', '=CMD()|Malicious Formula', '74', 'Added faculty member to TEST_ROBO (Faculty – Junior (JR))', '127.0.0.1', '2026-10-09 06:01:49'),
  ('16', 'System', 'Deleted', 'Faculty', 'Dr. Alan Turing', '72', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('17', 'System', 'Deleted', 'Faculty', 'Dr. Ada Lovelace', '73', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('18', 'System', 'Deleted', 'Faculty', '=CMD()|Malicious Formula', '74', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('19', 'System', 'Deleted', 'Departments', 'Robotics & Industrial Automation', '11', 'Deleted department TEST_ROBO', '127.0.0.1', '2026-10-09 06:01:49'),
  ('20', 'Charan', 'Login', 'Users', 'Charan', '1', 'User authenticated into admin portal (Local Store)', '::1', '2026-10-09 06:02:02'),
  ('21', 'Charan', 'Login', 'Users', 'Charan', '1', 'User authenticated into admin portal (Local Store)', '127.0.0.1', '2026-10-09 06:09:51'),
  ('22', 'System', 'Added', 'Departments', 'Robotics & Automation', '11', 'Created new department (TEST_ROBO)', '127.0.0.1', '2026-10-09 06:13:00'),
  ('23', 'System', 'Updated', 'Departments', 'Robotics & Industrial Automation', '11', 'Updated department TEST_ROBO', '127.0.0.1', '2026-10-09 06:13:00'),
  ('24', 'System', 'Added', 'Faculty', 'Dr. Alan Turing', '72', 'Added faculty member to TEST_ROBO (HOD)', '127.0.0.1', '2026-10-09 06:13:00'),
  ('25', 'System', 'Added', 'Faculty', 'Dr. Ada Lovelace', '73', 'Added faculty member to TEST_ROBO (R&D + Senior Faculty (SR))', '127.0.0.1', '2026-10-09 06:13:00'),
  ('26', 'System', 'Updated', 'Faculty', 'Dr. Ada Lovelace', '73', 'Assigned as Head of Department for TEST_ROBO', '127.0.0.1', '2026-10-09 06:13:00'),
  ('27', 'System', 'Added', 'Faculty', '=CMD()|Malicious Formula', '74', 'Added faculty member to TEST_ROBO (Faculty – Junior (JR))', '127.0.0.1', '2026-10-09 06:13:00'),
  ('28', 'System', 'Deleted', 'Faculty', 'Dr. Alan Turing', '72', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:13:00'),
  ('29', 'System', 'Deleted', 'Faculty', 'Dr. Ada Lovelace', '73', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:13:00'),
  ('30', 'System', 'Deleted', 'Faculty', '=CMD()|Malicious Formula', '74', 'Deleted faculty member from department TEST_ROBO', '127.0.0.1', '2026-10-09 06:13:00')
ON DUPLICATE KEY UPDATE `id` = `id`;


SET FOREIGN_KEY_CHECKS = 1;
