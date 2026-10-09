-- ==============================================================
-- TCEK (Trinity College of Engineering & Technology) Database
-- Database Name: tcek
-- Compatible with MySQL 5.7+ / MySQL 8.0 / MariaDB on GoDaddy cPanel
-- ==============================================================

-- 1. Users Table (Admin users and Regular users)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('admin', 'editor', 'staff') NOT NULL DEFAULT 'admin',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backward compatibility alias for admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT 'TCEK Administrator',
  `email` VARCHAR(100) DEFAULT 'officetcek@gmail.com',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Gallery Table (Images and Videos)
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `media_type` ENUM('image', 'video') NOT NULL DEFAULT 'image',
  `category` ENUM('events', 'campus', 'milestones', 'press') NOT NULL DEFAULT 'events',
  `file_path` VARCHAR(255) DEFAULT NULL,
  `video_url` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Events Table (Event image, Event video, Event details / description)
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` VARCHAR(50) DEFAULT NULL,
  `venue` VARCHAR(255) DEFAULT 'Trinity Campus Auditorium',
  `description` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `video_path` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3b. Event Media Table (Multiple images and videos linked to specific events)
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

-- 4. Notifications Table (PDF, Image, DOCX, Exam-related details, Scrolling Marquee text)
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
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backward compatibility alias for notices table
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

-- 5. Departments Table (Academic Departments, intake, durations, themes, URLs)
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

-- 6. Staff Table (Staff profile information, department, profile image)
CREATE TABLE IF NOT EXISTS `staff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `designation` VARCHAR(100) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `qualification` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Activity Logs Table (Audit trail: Who changed what and when)
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_name` VARCHAR(100) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `module` VARCHAR(100) NOT NULL,
  `record_name` VARCHAR(255) NOT NULL,
  `record_id` INT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Uploads Storage Registry Table (Tracks files stored in uploads/)
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
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- Initial Seed Data
-- ==============================================================

-- Default Administrator & Staff Users (Password: tcek@developer)
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`, `is_active`)
VALUES 
('tcek', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'TCEK Administrator', 'officetcek@gmail.com', 'admin', 1),
('Charan', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'Charan (Lead Admin)', 'charan@tcek.in', 'admin', 1),
('staff_user', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'Faculty Coordinator', 'faculty@tcek.in', 'staff', 1)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

INSERT INTO `admins` (`username`, `password`, `full_name`, `email`)
VALUES ('tcek', '$2y$12$8EoDxDbWZn1SygecaSJ4uO3ffa2.wAgsSzl1GWshCe6G9qIwAQ5Hq', 'TCEK Administrator', 'officetcek@gmail.com')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- Sample Events
INSERT INTO `events` (`title`, `event_date`, `venue`, `description`, `image_path`, `video_path`, `is_featured`, `is_active`)
VALUES 
('Freshers Aarambh 2K26', '2026-10-12', 'TCEK Open Air Auditorium', 'Welcoming 1st Year B.Tech and Polytechnic students with grand cultural performances and faculty felicitation.', 'assets/events/aarambh.jpg', 'assets/events/freshers.mp4', 1, 1),
('College Sports Week 2026', '2026-10-01', 'College Sports Grounds', 'Inter-department cricket tournaments, volleyball, athletics, and chess championships.', 'assets/events/sports.jpg', NULL, 1, 1)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Sample Notifications (PDF, Image, DOCX, Exam notification & Scrolling Marquee)
INSERT INTO `notifications` (`title`, `category`, `description`, `attachment_type`, `attachment_path`, `link_url`, `is_marquee`, `publish_date`, `is_active`)
VALUES 
('B.Tech End Semester Autonomous Examinations Schedule Released', 'Examination', 'Official notification regarding autonomous examination timetables, registration dates, and hall ticket issuance for regular & supplementary candidates.', 'pdf', 'uploads/pdfs/exam_schedule_2026.pdf', NULL, 1, CURDATE(), 1),
('Admissions Open AY 2026-27: B.Tech, Diploma & MBA Helpline Active', 'Admissions', 'EAPCET / POLYCET / ICET Code: TCEK. Merit scholarship concession forms available at administrative office.', 'docx', 'uploads/documents/admission_guidelines_2026.docx', 'admission.php', 1, CURDATE(), 1),
('Campus Placement Drive by Top Tier-1 Tech MNCs', 'Placements', 'Coding bootcamps and pre-placement interviews starting next week for CSE, ECE, EEE & AIML branches.', 'image', 'assets/College Event/caps.jpg', 'placement-cell.php', 0, CURDATE(), 1)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Sample Staff
INSERT INTO `staff` (`full_name`, `designation`, `department`, `qualification`, `email`, `phone`, `bio`, `display_order`, `is_active`)
VALUES 
('Dr. Ashok Kumar Vootla', 'Professor & Principal', 'Administration', 'Ph.D., M.Tech (CSE)', 'principal@tcek.in', '7396903383', 'Distinguished academician with over 20+ years of teaching, research, and institutional administration experience.', 1, 1),
('P. Padmini', 'Associate Professor & HoD', 'H&S', 'M.Sc., (Ph.D.)', 'hod.hs@tcek.in', '8522954369', 'Specialist in Applied Sciences with significant research contributions and academic mentorship.', 2, 1),
('N. Mahendar', 'Associate Professor & HoD', 'CSE', 'M.Tech, (Ph.D.)', 'hod.cse@tcek.in', '9848012345', 'Expertise in Artificial Intelligence, Cloud Computing, and Machine Learning algorithms.', 3, 1)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Initial Activity Logs (Including User Request Examples)
INSERT INTO `activity_logs` (`admin_name`, `action`, `module`, `record_name`, `description`, `ip_address`, `created_at`)
VALUES 
('Charan', 'Updated', 'Events', 'Aarambh-2K26', 'Updated venue details, chief guest schedule and celebration timings', '127.0.0.1', '2026-10-08 19:30:00'),
('Admin2', 'Deleted', 'Gallery', 'Sports Day 2026 Image', 'Deleted duplicate sports day celebration photograph from archival gallery', '127.0.0.1', '2026-10-08 19:45:00'),
('tcek', 'Added', 'Notifications', 'B.Tech Exam Schedule', 'Published Autonomous semester end exam notification with attached PDF timetable', '127.0.0.1', NOW()),
('tcek', 'Added', 'Staff', 'Dr. Ashok Kumar Vootla', 'Created staff profile in Administrative and Academic directory', '127.0.0.1', NOW())
ON DUPLICATE KEY UPDATE `id` = `id`;
