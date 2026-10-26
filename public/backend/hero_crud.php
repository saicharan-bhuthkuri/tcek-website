<?php
/**
 * TCEK Homepage Hero Slider Controller
 * Handles homepage hero section content & slides management:
 * - Update Content: Title, Subtitle, Left Badge, Right Badge, Admissions CTA text & link
 * - Upload & Manage Images: Add new slides, replace media, edit captions
 * - Slide Management: Reorder slides, toggle visibility, remove slides
 * - 100% MySQL Database Powered Storage & Retrieval
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/sync.php';

/**
 * Ensure hero tables exist in MySQL
 */
function ensure_hero_tables(?PDO $pdo = null): void {
    if (!$pdo) {
        $pdo = getDB();
    }
    if ($pdo instanceof PDO) {
        try {
            $pdo->exec("
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
            ");
        } catch (Throwable $e) {}
    }
}

/**
 * Load hero slider data directly from MySQL database
 */
function load_hero_data(): array {
    global $pdo;

    $defaultContent = [
        'title'            => 'Excellence in Education',
        'subtitle'         => 'Inspiring Future Innovators Since 2008',
        'subtitle_icon'    => 'fas fa-shield-halved',
        'badge_left_text'  => 'Campus Highlights',
        'badge_left_pulse' => 1,
        'badge_right_text' => "NAAC 'B++' Grade & Autonomous",
        'badge_right_icon' => 'fas fa-certificate',
        'cta_text'         => 'Admissions Open',
        'cta_link'         => 'admission.php'
    ];

    $defaultSettings = [
        'auto_play'      => 1,
        'slide_interval' => 3600
    ];

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            // Fetch content & settings
            $stmt = $pdo->query("SELECT * FROM hero_content ORDER BY id ASC LIMIT 1");
            $contentRow = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

            $content = $defaultContent;
            $settings = $defaultSettings;
            $updated_at = date('Y-m-d H:i:s');

            if ($contentRow) {
                $content = [
                    'title'            => $contentRow['title'] ?? $defaultContent['title'],
                    'subtitle'         => $contentRow['subtitle'] ?? $defaultContent['subtitle'],
                    'subtitle_icon'    => $contentRow['subtitle_icon'] ?? $defaultContent['subtitle_icon'],
                    'badge_left_text'  => $contentRow['badge_left_text'] ?? $defaultContent['badge_left_text'],
                    'badge_left_pulse' => isset($contentRow['badge_left_pulse']) ? (int)$contentRow['badge_left_pulse'] : 1,
                    'badge_right_text' => $contentRow['badge_right_text'] ?? $defaultContent['badge_right_text'],
                    'badge_right_icon' => $contentRow['badge_right_icon'] ?? $defaultContent['badge_right_icon'],
                    'cta_text'         => $contentRow['cta_text'] ?? $defaultContent['cta_text'],
                    'cta_link'         => $contentRow['cta_link'] ?? $defaultContent['cta_link']
                ];
                $settings = [
                    'auto_play'      => isset($contentRow['auto_play']) ? (int)$contentRow['auto_play'] : 1,
                    'slide_interval' => isset($contentRow['slide_interval']) ? (int)$contentRow['slide_interval'] : 3600
                ];
                $updated_at = $contentRow['updated_at'] ?? $updated_at;
            }

            // Fetch slides
            $sStmt = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC");
            $slides = $sStmt ? $sStmt->fetchAll(PDO::FETCH_ASSOC) : [];

            // Cast types
            foreach ($slides as &$sl) {
                $sl['id']            = (int)$sl['id'];
                $sl['is_active']     = (int)($sl['is_active'] ?? 1);
                $sl['display_order'] = (int)($sl['display_order'] ?? 0);
            }
            unset($sl);

            return [
                'content'    => $content,
                'settings'   => $settings,
                'slides'     => $slides,
                'updated_at' => $updated_at
            ];
        } catch (Throwable $e) {
            error_log("Failed to load hero data from MySQL: " . $e->getMessage());
        }
    }

    return [
        'content'    => $defaultContent,
        'settings'   => $defaultSettings,
        'slides'     => [],
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

/**
 * Update text content & badges in MySQL
 */
function update_hero_content(array $fields): array {
    global $pdo;

    $current = load_hero_data();
    $title            = trim($fields['title'] ?? $current['content']['title']);
    $subtitle         = trim($fields['subtitle'] ?? $current['content']['subtitle']);
    $subtitle_icon    = trim($fields['subtitle_icon'] ?? ($current['content']['subtitle_icon'] ?? 'fas fa-shield-halved'));
    $badge_left_text  = trim($fields['badge_left_text'] ?? $current['content']['badge_left_text']);
    $badge_left_pulse = !empty($fields['badge_left_pulse']) ? 1 : 0;
    $badge_right_text = trim($fields['badge_right_text'] ?? $current['content']['badge_right_text']);
    $badge_right_icon = trim($fields['badge_right_icon'] ?? ($current['content']['badge_right_icon'] ?? 'fas fa-certificate'));
    $cta_text         = trim($fields['cta_text'] ?? $current['content']['cta_text']);
    $cta_link         = trim($fields['cta_link'] ?? $current['content']['cta_link']);

    $auto_play      = isset($fields['auto_play']) ? (int)$fields['auto_play'] : (int)($current['settings']['auto_play'] ?? 1);
    $slide_interval = isset($fields['slide_interval']) ? max(1000, min(20000, (int)$fields['slide_interval'])) : (int)($current['settings']['slide_interval'] ?? 3600);

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);
            $stmt = $pdo->prepare("
                INSERT INTO hero_content (
                    id, title, subtitle, subtitle_icon, badge_left_text, badge_left_pulse,
                    badge_right_text, badge_right_icon, cta_text, cta_link, auto_play, slide_interval, updated_at
                ) VALUES (
                    1, :t, :sub, :sub_ico, :bl_t, :bl_p,
                    :br_t, :br_ico, :cta_t, :cta_l, :ap, :si, NOW()
                )
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    subtitle = VALUES(subtitle),
                    subtitle_icon = VALUES(subtitle_icon),
                    badge_left_text = VALUES(badge_left_text),
                    badge_left_pulse = VALUES(badge_left_pulse),
                    badge_right_text = VALUES(badge_right_text),
                    badge_right_icon = VALUES(badge_right_icon),
                    cta_text = VALUES(cta_text),
                    cta_link = VALUES(cta_link),
                    auto_play = VALUES(auto_play),
                    slide_interval = VALUES(slide_interval),
                    updated_at = NOW()
            ");
            $saved = $stmt->execute([
                ':t'       => $title,
                ':sub'     => $subtitle,
                ':sub_ico' => $subtitle_icon,
                ':bl_t'    => $badge_left_text,
                ':bl_p'    => $badge_left_pulse,
                ':br_t'    => $badge_right_text,
                ':br_ico'  => $badge_right_icon,
                ':cta_t'   => $cta_text,
                ':cta_l'   => $cta_link,
                ':ap'      => $auto_play,
                ':si'      => $slide_interval
            ]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                if (function_exists('log_activity')) {
                    log_activity('Updated', 'Hero Slider', 'Homepage Hero', "Updated hero title & badges: {$title}");
                }
                return ['success' => true, 'message' => 'Hero content updated successfully.'];
            }
        } catch (Throwable $e) {
            error_log("Failed to update hero content in MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Add a new slide to the hero slider in MySQL
 */
function add_hero_slide(string $filePath, string $title = '', string $mediaType = 'image', int $isActive = 1): array {
    global $pdo;

    $filePath = trim($filePath);
    if (empty($filePath)) {
        return ['success' => false, 'message' => 'File path cannot be empty.'];
    }

    // Auto-detect media type if not explicit
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
        $mediaType = 'video';
    } else {
        $mediaType = 'image';
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            // Get max display order
            $orderStmt = $pdo->query("SELECT COALESCE(MAX(display_order), 0) FROM hero_slides");
            $maxOrder = (int)$orderStmt->fetchColumn();

            $stmt = $pdo->prepare("
                INSERT INTO hero_slides (media_type, file_path, title, is_active, display_order, created_at)
                VALUES (:m, :f, :t, :a, :o, NOW())
            ");
            $newTitle = trim($title) ?: 'Hero Slide ' . ($maxOrder + 1);
            $saved = $stmt->execute([
                ':m' => $mediaType,
                ':f' => $filePath,
                ':t' => $newTitle,
                ':a' => $isActive ? 1 : 0,
                ':o' => $maxOrder + 1
            ]);

            if ($saved) {
                $newId = (int)$pdo->lastInsertId();
                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                if (function_exists('log_activity')) {
                    log_activity('Added', 'Hero Slider', $newTitle, "Added slide: {$filePath} ({$mediaType})", $newId);
                }
                return [
                    'success' => true,
                    'message' => 'New slide added to hero slider.',
                    'slide'   => [
                        'id'            => $newId,
                        'media_type'    => $mediaType,
                        'file_path'     => $filePath,
                        'title'         => $newTitle,
                        'is_active'     => $isActive ? 1 : 0,
                        'display_order' => $maxOrder + 1
                    ]
                ];
            }
        } catch (Throwable $e) {
            error_log("Failed to add hero slide to MySQL: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Update an existing slide in MySQL
 */
function update_hero_slide(int $id, array $updates): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid slide ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $slide = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$slide) {
                return ['success' => false, 'message' => 'Slide not found.'];
            }

            $title = isset($updates['title']) ? trim($updates['title']) : $slide['title'];
            $filePath = !empty($updates['file_path']) ? trim($updates['file_path']) : $slide['file_path'];
            $mediaType = $slide['media_type'];

            if (!empty($updates['file_path'])) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                    $mediaType = 'video';
                } else {
                    $mediaType = 'image';
                }
            } elseif (isset($updates['media_type']) && in_array($updates['media_type'], ['image', 'video'])) {
                $mediaType = $updates['media_type'];
            }

            $isActive = isset($updates['is_active']) ? (!empty($updates['is_active']) ? 1 : 0) : (int)$slide['is_active'];
            $displayOrder = isset($updates['display_order']) ? (int)$updates['display_order'] : (int)$slide['display_order'];

            $upStmt = $pdo->prepare("
                UPDATE hero_slides
                SET title = :t, file_path = :f, media_type = :m, is_active = :a, display_order = :o, updated_at = NOW()
                WHERE id = :id
            ");
            $saved = $upStmt->execute([
                ':t'  => $title,
                ':f'  => $filePath,
                ':m'  => $mediaType,
                ':a'  => $isActive,
                ':o'  => $displayOrder,
                ':id' => $id
            ]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                if (function_exists('log_activity')) {
                    log_activity('Updated', 'Hero Slider', $title, "Updated hero slide ID #{$id}", $id);
                }
                return ['success' => true, 'message' => 'Slide updated successfully.'];
            }
        } catch (Throwable $e) {
            error_log("Failed to update hero slide: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Delete a slide in MySQL
 */
function delete_hero_slide(int $id): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid slide ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $slide = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$slide) {
                return ['success' => false, 'message' => 'Slide not found.'];
            }

            $delStmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = :id");
            $saved = $delStmt->execute([':id' => $id]);

            if ($saved) {
                // Re-index display orders
                $all = $pdo->query("SELECT id FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
                $up = $pdo->prepare("UPDATE hero_slides SET display_order = :o WHERE id = :id");
                foreach ($all as $idx => $r) {
                    $up->execute([':o' => $idx + 1, ':id' => (int)$r['id']]);
                }

                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                if (function_exists('log_activity')) {
                    log_activity('Deleted', 'Hero Slider', $slide['title'] ?? "Slide #{$id}", "Deleted hero slide ID #{$id}", $id);
                }
                return ['success' => true, 'message' => 'Slide removed successfully.'];
            }
        } catch (Throwable $e) {
            error_log("Failed to delete hero slide: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Toggle active status of a slide in MySQL
 */
function toggle_hero_slide(int $id): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid slide ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            $stmt = $pdo->prepare("SELECT id, title, is_active FROM hero_slides WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $slide = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$slide) {
                return ['success' => false, 'message' => 'Slide not found.'];
            }

            $newState = !empty($slide['is_active']) ? 0 : 1;
            $upStmt = $pdo->prepare("UPDATE hero_slides SET is_active = :a, updated_at = NOW() WHERE id = :id");
            $saved = $upStmt->execute([':a' => $newState, ':id' => $id]);

            if ($saved) {
                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                if (function_exists('log_activity')) {
                    $status_str = $newState ? 'enabled' : 'hidden';
                    log_activity('Updated', 'Hero Slider', $slide['title'], "Slide ID #{$id} {$status_str}", $id);
                }
                return [
                    'success'   => true,
                    'message'   => $newState ? 'Slide enabled.' : 'Slide hidden.',
                    'is_active' => $newState
                ];
            }
        } catch (Throwable $e) {
            error_log("Failed to toggle hero slide: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}

/**
 * Move slide up or down in order in MySQL
 */
function move_hero_slide(int $id, string $direction): array {
    global $pdo;
    $id = (int)$id;

    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid slide ID.'];
    }

    if ($pdo instanceof PDO) {
        try {
            ensure_hero_tables($pdo);

            $slides = $pdo->query("SELECT id, display_order FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
            $targetIdx = -1;

            foreach ($slides as $i => $s) {
                if ((int)$s['id'] === $id) {
                    $targetIdx = $i;
                    break;
                }
            }

            if ($targetIdx === -1) {
                return ['success' => false, 'message' => 'Slide not found.'];
            }

            $swapIdx = -1;
            if ($direction === 'up' && $targetIdx > 0) {
                $swapIdx = $targetIdx - 1;
            } elseif ($direction === 'down' && $targetIdx < count($slides) - 1) {
                $swapIdx = $targetIdx + 1;
            }

            if ($swapIdx !== -1) {
                $targetSlide = $slides[$targetIdx];
                $swapSlide   = $slides[$swapIdx];

                $upStmt = $pdo->prepare("UPDATE hero_slides SET display_order = :o, updated_at = NOW() WHERE id = :id");
                // Swap orders
                $upStmt->execute([':o' => (int)$swapSlide['display_order'], ':id' => (int)$targetSlide['id']]);
                $upStmt->execute([':o' => (int)$targetSlide['display_order'], ':id' => (int)$swapSlide['id']]);

                if (function_exists('record_content_update')) {
                    record_content_update('hero');
                }
                return ['success' => true, 'message' => 'Slide order updated.'];
            }

            return ['success' => true, 'message' => 'Slide is already in requested position.'];
        } catch (Throwable $e) {
            error_log("Failed to move hero slide: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    return ['success' => false, 'message' => 'Database connection unavailable.'];
}
