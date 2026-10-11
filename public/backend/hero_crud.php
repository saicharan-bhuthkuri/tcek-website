<?php
/**
 * TCEK Homepage Hero Slider Controller
 * Handles homepage hero section content & slides management:
 * - Update Content: Title, Subtitle, Left Badge, Right Badge, Admissions CTA text & link
 * - Upload & Manage Images: Add new slides, replace media, edit captions
 * - Slide Management: Reorder slides, toggle visibility, remove slides
 * - Atomic persistence to config/hero_slider_data.json with fallback defaults
 */

if (!function_exists('get_hero_store_file')) {
    function get_hero_store_file(): string {
        return __DIR__ . '/config/hero_slider_data.json';
    }
}

require_once __DIR__ . '/sync.php';


/**
 * Load hero slider data from JSON store or return robust defaults
 */
function load_hero_data(): array {
    $file = get_hero_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                // Ensure default content keys exist
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

                $data['content'] = array_merge($defaultContent, $data['content'] ?? []);
                $data['settings'] = array_merge([
                    'auto_play'      => 1,
                    'slide_interval' => 3600
                ], $data['settings'] ?? []);

                if (!isset($data['slides']) || !is_array($data['slides'])) {
                    $data['slides'] = [];
                }

                // Sort slides by display_order ASC
                usort($data['slides'], function($a, $b) {
                    return ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0);
                });

                return $data;
            }
        }
    }

    // Default configuration if store is missing
    return [
        'content' => [
            'title'            => 'Excellence in Education',
            'subtitle'         => 'Inspiring Future Innovators Since 2008',
            'subtitle_icon'    => 'fas fa-shield-halved',
            'badge_left_text'  => 'Campus Highlights',
            'badge_left_pulse' => 1,
            'badge_right_text' => "NAAC 'B++' Grade & Autonomous",
            'badge_right_icon' => 'fas fa-certificate',
            'cta_text'         => 'Admissions Open',
            'cta_link'         => 'admission.php'
        ],
        'settings' => [
            'auto_play'      => 1,
            'slide_interval' => 3600
        ],
        'slides' => [
            [
                'id'            => 1,
                'media_type'    => 'image',
                'file_path'     => 'assets/College Event/all1.jpg',
                'title'         => 'College Event',
                'is_active'     => 1,
                'display_order' => 1
            ],
            [
                'id'            => 2,
                'media_type'    => 'image',
                'file_path'     => 'assets/College Event/caps.jpg',
                'title'         => 'Graduation',
                'is_active'     => 1,
                'display_order' => 2
            ],
            [
                'id'            => 3,
                'media_type'    => 'image',
                'file_path'     => 'assets/College Event/feli1.jpg',
                'title'         => 'Felicitation 1',
                'is_active'     => 1,
                'display_order' => 3
            ],
            [
                'id'            => 4,
                'media_type'    => 'image',
                'file_path'     => 'assets/College Event/feli2.jpg',
                'title'         => 'Felicitation 2',
                'is_active'     => 1,
                'display_order' => 4
            ],
            [
                'id'            => 5,
                'media_type'    => 'video',
                'file_path'     => 'assets/College Event/autonomus.mp4',
                'title'         => 'Autonomous Status Video',
                'is_active'     => 1,
                'display_order' => 5
            ]
        ],
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

/**
 * Save hero data atomically
 */
function save_hero_data(array $data): bool {
    $file = get_hero_store_file();
    $dir  = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $data['updated_at'] = date('Y-m-d H:i:s');
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $saved = @file_put_contents($file, $json, LOCK_EX) !== false;
    if ($saved && function_exists('record_content_update')) {
        record_content_update('hero');
    }
    return $saved;
}

/**
 * Update text content & badges
 */
function update_hero_content(array $fields): array {
    $data = load_hero_data();

    $data['content']['title']            = trim($fields['title'] ?? $data['content']['title']);
    $data['content']['subtitle']         = trim($fields['subtitle'] ?? $data['content']['subtitle']);
    $data['content']['badge_left_text']  = trim($fields['badge_left_text'] ?? $data['content']['badge_left_text']);
    $data['content']['badge_left_pulse'] = !empty($fields['badge_left_pulse']) ? 1 : 0;
    $data['content']['badge_right_text'] = trim($fields['badge_right_text'] ?? $data['content']['badge_right_text']);
    $data['content']['badge_right_icon'] = trim($fields['badge_right_icon'] ?? 'fas fa-certificate');
    $data['content']['cta_text']         = trim($fields['cta_text'] ?? $data['content']['cta_text']);
    $data['content']['cta_link']         = trim($fields['cta_link'] ?? $data['content']['cta_link']);

    if (isset($fields['slide_interval'])) {
        $data['settings']['slide_interval'] = max(1000, min(20000, (int)$fields['slide_interval']));
    }

    $saved = save_hero_data($data);
    return [
        'success' => $saved,
        'message' => $saved ? 'Hero content updated successfully.' : 'Failed to save hero content.'
    ];
}

/**
 * Add a new slide to the hero slider
 */
function add_hero_slide(string $filePath, string $title = '', string $mediaType = 'image', int $isActive = 1): array {
    $data = load_hero_data();

    // Auto-detect media type if not explicit
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
        $mediaType = 'video';
    }

    // Find highest existing ID and display_order
    $maxId    = 0;
    $maxOrder = 0;
    foreach ($data['slides'] as $s) {
        if (($s['id'] ?? 0) > $maxId) {
            $maxId = (int)$s['id'];
        }
        if (($s['display_order'] ?? 0) > $maxOrder) {
            $maxOrder = (int)$s['display_order'];
        }
    }

    $newSlide = [
        'id'            => $maxId + 1,
        'media_type'    => in_array($mediaType, ['image', 'video']) ? $mediaType : 'image',
        'file_path'     => trim($filePath),
        'title'         => trim($title) ?: 'Hero Slide ' . ($maxId + 1),
        'is_active'     => $isActive ? 1 : 0,
        'display_order' => $maxOrder + 1
    ];

    $data['slides'][] = $newSlide;
    $saved = save_hero_data($data);

    return [
        'success' => $saved,
        'message' => $saved ? 'New slide added to hero slider.' : 'Failed to add slide.',
        'slide'   => $newSlide
    ];
}

/**
 * Update an existing slide
 */
function update_hero_slide(int $id, array $updates): array {
    $data = load_hero_data();
    $found = false;

    foreach ($data['slides'] as &$slide) {
        if ((int)$slide['id'] === $id) {
            if (isset($updates['title'])) {
                $slide['title'] = trim($updates['title']);
            }
            if (!empty($updates['file_path'])) {
                $slide['file_path'] = trim($updates['file_path']);
                $ext = strtolower(pathinfo($slide['file_path'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                    $slide['media_type'] = 'video';
                } else {
                    $slide['media_type'] = 'image';
                }
            }
            if (isset($updates['media_type']) && in_array($updates['media_type'], ['image', 'video'])) {
                $slide['media_type'] = $updates['media_type'];
            }
            if (isset($updates['is_active'])) {
                $slide['is_active'] = !empty($updates['is_active']) ? 1 : 0;
            }
            if (isset($updates['display_order'])) {
                $slide['display_order'] = (int)$updates['display_order'];
            }
            $found = true;
            break;
        }
    }
    unset($slide);

    if (!$found) {
        return ['success' => false, 'message' => 'Slide not found.'];
    }

    $saved = save_hero_data($data);
    return [
        'success' => $saved,
        'message' => $saved ? 'Slide updated successfully.' : 'Failed to update slide.'
    ];
}

/**
 * Delete a slide
 */
function delete_hero_slide(int $id): array {
    $data = load_hero_data();
    $initialCount = count($data['slides']);

    $data['slides'] = array_values(array_filter($data['slides'], function($s) use ($id) {
        return (int)$s['id'] !== $id;
    }));

    if (count($data['slides']) === $initialCount) {
        return ['success' => false, 'message' => 'Slide not found.'];
    }

    // Re-index display orders
    foreach ($data['slides'] as $idx => &$s) {
        $s['display_order'] = $idx + 1;
    }
    unset($s);

    $saved = save_hero_data($data);
    return [
        'success' => $saved,
        'message' => $saved ? 'Slide removed successfully.' : 'Failed to delete slide.'
    ];
}

/**
 * Toggle active status of a slide
 */
function toggle_hero_slide(int $id): array {
    $data = load_hero_data();
    $newState = 0;
    $found = false;

    foreach ($data['slides'] as &$slide) {
        if ((int)$slide['id'] === $id) {
            $slide['is_active'] = empty($slide['is_active']) ? 1 : 0;
            $newState = $slide['is_active'];
            $found = true;
            break;
        }
    }
    unset($slide);

    if (!$found) {
        return ['success' => false, 'message' => 'Slide not found.'];
    }

    $saved = save_hero_data($data);
    return [
        'success'   => $saved,
        'message'   => $saved ? ($newState ? 'Slide enabled.' : 'Slide hidden.') : 'Failed to toggle slide.',
        'is_active' => $newState
    ];
}

/**
 * Move slide up or down in order
 */
function move_hero_slide(int $id, string $direction): array {
    $data = load_hero_data();
    $slides = $data['slides'];

    // Sort by display_order
    usort($slides, function($a, $b) {
        return ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0);
    });

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
        // Swap display_order
        $temp = $slides[$targetIdx];
        $slides[$targetIdx] = $slides[$swapIdx];
        $slides[$swapIdx] = $temp;

        // Reassign 1-based display_order
        foreach ($slides as $i => &$s) {
            $s['display_order'] = $i + 1;
        }
        unset($s);

        $data['slides'] = $slides;
        $saved = save_hero_data($data);
        return [
            'success' => $saved,
            'message' => $saved ? 'Slide order updated.' : 'Failed to reorder slide.'
        ];
    }

    return ['success' => true, 'message' => 'Slide is already in requested position.'];
}
