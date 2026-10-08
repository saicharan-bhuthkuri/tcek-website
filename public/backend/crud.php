<?php
/**
 * Master CRUD Controller for Trinity College of Engineering & Technology
 * Handles: Users, Gallery, Events, Notifications, Staff, Activity Logs, and Uploads
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/upload.php';

if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth($str, $start = 0, $width = 100, $trimmarker = '...') {
        if ($str === null) return '';
        $s = (string)$str;
        if (strlen($s) <= $width) return $s;
        return substr($s, $start, max(0, $width - strlen($trimmarker))) . $trimmarker;
    }
}

// ==============================================================
// 1. ACTIVITY LOGS API
// ==============================================================

function get_activity_logs_store_file() {
    return __DIR__ . '/config/activity_logs_data.json';
}

function get_default_seed_activity_logs() {
    return [
        [
            'id' => 1,
            'admin_name' => 'Charan',
            'action' => 'Updated',
            'module' => 'Events',
            'record_name' => 'Aarambh-2K26',
            'record_id' => 1,
            'description' => 'Updated venue details, chief guest schedule and celebration timings',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ],
        [
            'id' => 2,
            'admin_name' => 'tcek',
            'action' => 'Added',
            'module' => 'Notifications',
            'record_name' => 'Urgent Fee Payment Circular',
            'record_id' => 1,
            'description' => 'Published Autonomous semester end fee circular with attached document',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
        ],
        [
            'id' => 3,
            'admin_name' => 'Admin',
            'action' => 'Added',
            'module' => 'News',
            'record_name' => 'MSME Hackathon 6.0 News Release',
            'record_id' => 2,
            'description' => 'Uploaded newspaper press clipping to public News & Media gallery',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
        ],
        [
            'id' => 4,
            'admin_name' => 'Charan',
            'action' => 'Login',
            'module' => 'Users',
            'record_name' => 'Master Admin Portal',
            'record_id' => 1,
            'description' => 'Successful authentication into TCEK Administration Control Console',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
        ],
        [
            'id' => 5,
            'admin_name' => 'Admin2',
            'action' => 'Deleted',
            'module' => 'Events',
            'record_name' => 'Old Sports Day Media Item',
            'record_id' => 8,
            'description' => 'Deleted duplicate photo from annual festival media queue',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 6,
            'admin_name' => 'tcek',
            'action' => 'Added',
            'module' => 'Workshops',
            'record_name' => 'Full Stack AI Bootcamp',
            'record_id' => 3,
            'description' => 'Configured task schedule and student participation roster',
            'ip_address' => '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ]
    ];
}

function load_activity_logs_from_store() {
    $file = get_activity_logs_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
    }
    $defaults = get_default_seed_activity_logs();
    save_activity_logs_to_store($defaults);
    return $defaults;
}

function save_activity_logs_to_store($logs) {
    $file = get_activity_logs_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return @file_put_contents($file, json_encode(array_values($logs), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function add_json_activity_log($action, $module, $record_name, $description = '', $record_id = null, $admin_name = null) {
    $logs = load_activity_logs_from_store();
    if (!$admin_name) {
        $admin_name = $_SESSION['tcek_admin_name'] ?? ($_SESSION['tcek_admin_username'] ?? 'System');
    }
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $newId = !empty($logs) ? (max(array_column($logs, 'id')) + 1) : 1;
    $newLog = [
        'id' => $newId,
        'admin_name' => trim($admin_name),
        'action' => trim($action),
        'module' => trim($module),
        'record_name' => trim($record_name),
        'record_id' => $record_id,
        'description' => trim($description),
        'ip_address' => $ip_address,
        'created_at' => date('Y-m-d H:i:s')
    ];
    array_unshift($logs, $newLog);
    $logs = array_slice($logs, 0, 200);
    save_activity_logs_to_store($logs);
    return true;
}

/**
 * Fetch activity logs with filters and limits
 * 
 * @param string|null $module
 * @param string|null $action
 * @param int $limit
 * @return array
 */
function get_activity_logs($module = null, $action = null, $limit = 100) {
    global $pdo;

    if ($pdo instanceof PDO) {
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
            $db_logs = $stmt->fetchAll();
            if (!empty($db_logs)) {
                return $db_logs;
            }
        } catch (PDOException $e) {
            error_log("Failed to fetch activity logs from DB: " . $e->getMessage());
        }
    }

    // Robust JSON Store Fallback
    $all = load_activity_logs_from_store();
    $filtered = [];
    foreach ($all as $item) {
        if ($module && $module !== 'all' && strtolower($item['module'] ?? '') !== strtolower($module)) {
            continue;
        }
        if ($action && $action !== 'all' && strtolower($item['action'] ?? '') !== strtolower($action)) {
            continue;
        }
        $filtered[] = $item;
    }
    return array_slice($filtered, 0, (int)$limit);
}

// ==============================================================
// 2. USERS MANAGEMENT
// ==============================================================

function get_users($limit = 50) {
    global $pdo;
    $default_users = [
        [
            'id' => 1,
            'username' => 'tcek',
            'full_name' => 'Charan (Lead Developer)',
            'email' => 'tcekrdcell@gmail.com',
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => '2026-10-01 09:00:00'
        ],
        [
            'id' => 2,
            'username' => 'admin_academic',
            'full_name' => 'Dr. A. K. Vootla (Academic Dean)',
            'email' => 'academic@tcek.ac.in',
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => '2026-10-02 10:15:00'
        ],
        [
            'id' => 3,
            'username' => 'exam_incharge',
            'full_name' => 'Controller of Examinations (CoE)',
            'email' => 'exams@tcek.ac.in',
            'role' => 'staff',
            'is_active' => 1,
            'created_at' => '2026-10-03 11:30:00'
        ],
        [
            'id' => 4,
            'username' => 'tpo_officer',
            'full_name' => 'Training & Placement Cell',
            'email' => 'placements@tcek.ac.in',
            'role' => 'staff',
            'is_active' => 1,
            'created_at' => '2026-10-04 14:00:00'
        ],
        [
            'id' => 5,
            'username' => 'admissions_coord',
            'full_name' => 'Admissions & Counseling Desk',
            'email' => 'admissions@tcek.ac.in',
            'role' => 'staff',
            'is_active' => 1,
            'created_at' => '2026-10-05 16:45:00'
        ]
    ];

    if (!($pdo instanceof PDO)) {
        return $default_users;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, is_active, created_at, updated_at FROM users ORDER BY id ASC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if (empty($res)) {
            // Seed default admin in database
            $hash = password_hash('tcek@developer', PASSWORD_BCRYPT);
            try {
                $ins = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, is_active, created_at) VALUES ('tcek', :p, 'Charan (Lead Developer)', 'tcekrdcell@gmail.com', 'admin', 1, NOW())");
                $ins->execute([':p' => $hash]);
                $stmt->execute();
                $recheck = $stmt->fetchAll();
                if (!empty($recheck)) return $recheck;
            } catch (Exception $e2) {}
            return $default_users;
        }
        return $res;
    } catch (PDOException $e) {
        return $default_users;
    }
}

function add_user($username, $password, $full_name, $email, $role = 'staff') {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $hash = password_hash(trim($password), PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, full_name, email, role, is_active, created_at)
            VALUES (:u, :p, :f, :e, :r, 1, NOW())
        ");
        $stmt->execute([
            ':u' => trim($username),
            ':p' => $hash,
            ':f' => trim($full_name),
            ':e' => trim($email),
            ':r' => $role
        ]);
        $id = $pdo->lastInsertId();
        log_activity('Added', 'Users', $username, "Created user with role '{$role}'", $id);
        return ['success' => true, 'message' => 'User created successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function delete_user($id) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $st = $pdo->prepare("SELECT username FROM users WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        $uName = $row ? $row['username'] : 'User #' . $id;

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_activity('Deleted', 'Users', $uName, "Deleted user record", $id);
        return ['success' => true, 'message' => 'User removed successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// ==============================================================
// 3. GALLERY (IMAGES & VIDEOS)
// ==============================================================

function get_gallery_items($media_type = null, $category = null, $limit = 60) {
    global $pdo;
    if (!($pdo instanceof PDO)) return [];

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
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function add_gallery_item($title, $media_type, $category, $file_path = null, $video_url = null, $description = null) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO gallery (title, media_type, category, file_path, video_url, description, created_at)
            VALUES (:title, :media_type, :category, :file_path, :video_url, :description, NOW())
        ");
        $stmt->execute([
            ':title'       => trim($title),
            ':media_type'  => $media_type,
            ':category'    => $category,
            ':file_path'   => $file_path,
            ':video_url'   => $video_url,
            ':description' => trim($description)
        ]);
        $id = $pdo->lastInsertId();
        log_activity('Added', 'Gallery', $title, "Added {$media_type} to '{$category}' category", $id);
        return ['success' => true, 'message' => 'Gallery media added successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function delete_gallery_item($id) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];

    try {
        $st = $pdo->prepare("SELECT title, media_type FROM gallery WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        $title = $row ? $row['title'] : 'Gallery Item #' . $id;

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_activity('Deleted', 'Gallery', $title, "Deleted gallery record", $id);
        return ['success' => true, 'message' => 'Gallery item deleted.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// ==============================================================
// 4. EVENTS (IMAGE, VIDEO, EVENT DETAILS) & EVENT MEDIA
// ==============================================================

function get_events_store_file() {
    return __DIR__ . '/config/events_data.json';
}

function get_default_seed_events() {
    return [
        [
            'id' => 1,
            'title' => 'Freshers Aarambh 2K26',
            'event_date' => '2026-10-12',
            'event_time' => '10:00 AM – 05:00 PM',
            'venue' => 'Trinity Campus Auditorium',
            'description' => 'Welcoming the incoming batch of engineers and technocrats to the Trinity family with electrifying music, dazzling dance performances, interactive fun games, and unforgettable memories!',
            'image_path' => 'assets/events/tcek-fresher.jpg',
            'video_path' => 'assets/events/freshers.mp4',
            'is_featured' => 1,
            'is_active' => 1,
            'created_at' => '2026-10-01 10:00:00',
            'media_items' => [
                [
                    'id' => 1,
                    'event_id' => 1,
                    'media_type' => 'video',
                    'file_path' => 'assets/events/freshers.mp4',
                    'media_title' => 'Freshers Aarambh 2K26 Celebration',
                    'media_description' => 'Official highlight video of Freshers Aarambh 2K26 at Trinity College of Engineering & Technology.',
                    'created_at' => '2026-10-01 10:00:00'
                ],
                [
                    'id' => 2,
                    'event_id' => 1,
                    'media_type' => 'image',
                    'file_path' => 'assets/events/tcek-fresher.jpg',
                    'media_title' => 'Freshers Aarambh 2K26 Official Event Poster',
                    'media_description' => 'Official creative poster announcing Freshers Day on 12th October 2026.',
                    'created_at' => '2026-10-01 10:05:00'
                ]
            ]
        ],
        [
            'id' => 2,
            'title' => 'College Sports Week',
            'event_date' => '2026-10-01',
            'event_time' => '09:00 AM – 05:00 PM',
            'venue' => 'Trinity Sports Ground',
            'description' => 'A week of energy, talent & togetherness — cricket championship, kabaddi tournaments, badminton and athletic competitions.',
            'image_path' => 'assets/events/tcek-poster.jpg',
            'video_path' => 'assets/events/cricket-campaigns.mp4',
            'is_featured' => 0,
            'is_active' => 1,
            'created_at' => '2026-09-28 09:00:00',
            'media_items' => [
                [
                    'id' => 3,
                    'event_id' => 2,
                    'media_type' => 'video',
                    'file_path' => 'assets/events/cricket-campaigns.mp4',
                    'media_title' => 'AIML & CSE Cricket Campaigns',
                    'media_description' => 'College Sports Week 2026 cricket championship clashes between Department of AIML and Department of CSE.',
                    'created_at' => '2026-10-02 11:00:00'
                ],
                [
                    'id' => 4,
                    'event_id' => 2,
                    'media_type' => 'video',
                    'file_path' => 'assets/events/kabaddi-wins.mp4',
                    'media_title' => 'Kabaddi Championship Wins',
                    'media_description' => 'Sensational raid points, tackles, and trophy celebration in the annual college Kabaddi tournament.',
                    'created_at' => '2026-10-03 16:30:00'
                ],
                [
                    'id' => 5,
                    'event_id' => 2,
                    'media_type' => 'image',
                    'file_path' => 'assets/events/tcek-poster.jpg',
                    'media_title' => 'Sports & Cultural Week Schedule',
                    'media_description' => 'Complete 2-week schedule across sports, flash mob and traditional days.',
                    'created_at' => '2026-09-29 10:00:00'
                ]
            ]
        ],
        [
            'id' => 3,
            'title' => 'Flash Mob Dance Showcase',
            'event_date' => '2026-10-09',
            'event_time' => '04:00 PM – 05:30 PM',
            'venue' => 'Main Campus Plaza',
            'description' => 'High-voltage dance showcase featuring Trinity students, electrifying music, and creative choreography.',
            'image_path' => null,
            'video_path' => null,
            'is_featured' => 0,
            'is_active' => 1,
            'created_at' => '2026-10-02 14:00:00',
            'media_items' => []
        ],
        [
            'id' => 4,
            'title' => 'Traditional & Bathukamma Fest',
            'event_date' => '2026-10-13',
            'event_time' => '10:00 AM – 04:00 PM',
            'venue' => 'Central Quadrangle',
            'description' => 'Heritage, flowers, authentic cultural fest and celebrations honoring regional traditions.',
            'image_path' => null,
            'video_path' => null,
            'is_featured' => 0,
            'is_active' => 1,
            'created_at' => '2026-10-03 10:00:00',
            'media_items' => []
        ],
        [
            'id' => 5,
            'title' => 'Autonomous Status Felicitation',
            'event_date' => '2026-09-25',
            'event_time' => '11:00 AM – 02:00 PM',
            'venue' => 'Main Auditorium',
            'description' => 'Grand celebration on UGC granting Autonomous Status to Trinity College of Engineering and Technology.',
            'image_path' => null,
            'video_path' => 'assets/College Event/autonomus.mp4',
            'is_featured' => 0,
            'is_active' => 1,
            'created_at' => '2026-09-24 11:00:00',
            'media_items' => [
                [
                    'id' => 6,
                    'event_id' => 5,
                    'media_type' => 'video',
                    'file_path' => 'assets/College Event/autonomus.mp4',
                    'media_title' => 'Autonomous Status Felicitation',
                    'media_description' => 'Special institutional felicitation ceremonies celebrating UGC Autonomous conferment.',
                    'created_at' => '2026-09-25 15:00:00'
                ]
            ]
        ],
        [
            'id' => 6,
            'title' => 'Annual Convocation & Graduation Day',
            'event_date' => '2026-09-18',
            'event_time' => '10:00 AM – 01:30 PM',
            'venue' => 'Open Air Amphitheatre',
            'description' => 'Graduating engineers celebrating academic degrees, medals, and milestone achievements with parents and mentors.',
            'image_path' => 'assets/College Event/caps.jpg',
            'video_path' => null,
            'is_featured' => 0,
            'is_active' => 1,
            'created_at' => '2026-09-17 10:00:00',
            'media_items' => [
                [
                    'id' => 7,
                    'event_id' => 6,
                    'media_type' => 'image',
                    'file_path' => 'assets/College Event/caps.jpg',
                    'media_title' => 'Graduation Day Ceremony',
                    'media_description' => 'Graduating engineers tossing convocation caps in celebration.',
                    'created_at' => '2026-09-18 14:00:00'
                ],
                [
                    'id' => 8,
                    'event_id' => 6,
                    'media_type' => 'image',
                    'file_path' => 'assets/College Event/feli1.jpg',
                    'media_title' => 'Merit Felicitation Ceremony',
                    'media_description' => 'Recognizing outstanding student achievers, rank holders and sports stars.',
                    'created_at' => '2026-09-18 14:30:00'
                ]
            ]
        ]
    ];
}

function load_events_from_store() {
    $file = get_events_store_file();
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if (is_array($data) && !empty($data)) {
            return $data;
        }
    }
    $defaults = get_default_seed_events();
    @file_put_contents($file, json_encode($defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return $defaults;
}

function save_events_to_store($events) {
    $file = get_events_store_file();
    return @file_put_contents($file, json_encode(array_values($events), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function get_events($limit = 50, $featured_only = false) {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            // Ensure event_media table and event_time column exist in MySQL
            $pdo->exec("CREATE TABLE IF NOT EXISTS event_media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                media_type ENUM('image', 'video') NOT NULL DEFAULT 'image',
                file_path VARCHAR(255) NOT NULL,
                media_title VARCHAR(255) NOT NULL,
                media_description TEXT DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (event_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            try {
                $pdo->exec("ALTER TABLE events ADD COLUMN event_time VARCHAR(50) DEFAULT NULL AFTER event_date");
            } catch (Throwable $t) {}

            $where = $featured_only ? "WHERE is_featured = 1 AND is_active = 1" : "WHERE is_active = 1";
            $stmt = $pdo->prepare("SELECT * FROM events {$where} ORDER BY event_date DESC, id DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll();

            if (empty($events)) {
                return load_events_from_store();
            }

            // Attach multi-media items for each event
            foreach ($events as &$ev) {
                try {
                    $mStmt = $pdo->prepare("SELECT * FROM event_media WHERE event_id = :eid ORDER BY id DESC");
                    $mStmt->execute([':eid' => $ev['id']]);
                    $ev['media_items'] = $mStmt->fetchAll();
                } catch (Throwable $e2) {
                    $ev['media_items'] = [];
                }
            }
            return $events;
        } catch (PDOException $e) {
            return load_events_from_store();
        }
    }

    $events = load_events_from_store();
    if ($featured_only) {
        $events = array_filter($events, function($e) {
            return !empty($e['is_featured']) && !empty($e['is_active']);
        });
    } else {
        $events = array_filter($events, function($e) {
            return !empty($e['is_active']);
        });
    }
    usort($events, function($a, $b) {
        return strcmp($b['event_date'], $a['event_date']);
    });
    return array_slice($events, 0, $limit);
}

function get_event_by_id($id) {
    global $pdo;
    $id = (int)$id;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $ev = $stmt->fetch();
            if ($ev) {
                $mStmt = $pdo->prepare("SELECT * FROM event_media WHERE event_id = :eid ORDER BY id DESC");
                $mStmt->execute([':eid' => $id]);
                $ev['media_items'] = $mStmt->fetchAll();
                return $ev;
            }
        } catch (Throwable $e) {}
    }

    $events = load_events_from_store();
    foreach ($events as $ev) {
        if ((int)$ev['id'] === $id) return $ev;
    }
    return null;
}

function add_event($title, $event_date, $event_time = '10:00 AM', $description = '', $venue = 'Trinity Campus Auditorium', $image_path = null, $video_path = null, $is_featured = 0) {
    global $pdo;

    $title       = trim($title);
    $venue       = trim($venue ?: 'Trinity Campus Auditorium');
    $event_time  = trim($event_time ?: '10:00 AM');
    $description = trim($description);

    if ($pdo instanceof PDO) {
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
            $id = $pdo->lastInsertId();
            log_activity('Added', 'Events', $title, "Created college event scheduled for {$event_date} ({$event_time})", $id);
            return ['success' => true, 'message' => 'Event created successfully!', 'id' => $id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    $events = load_events_from_store();
    $max_id = 0;
    foreach ($events as $ev) {
        if ((int)$ev['id'] > $max_id) $max_id = (int)$ev['id'];
    }
    $new_id = $max_id + 1;

    $new_event = [
        'id'          => $new_id,
        'title'       => $title,
        'event_date'  => $event_date,
        'event_time'  => $event_time,
        'venue'       => $venue,
        'description' => $description,
        'image_path'  => $image_path,
        'video_path'  => $video_path,
        'is_featured' => (int)$is_featured,
        'is_active'   => 1,
        'created_at'  => date('Y-m-d H:i:s'),
        'media_items' => []
    ];
    array_unshift($events, $new_event);
    save_events_to_store($events);
    log_activity('Added', 'Events', $title, "Created college event scheduled for {$event_date} ({$event_time})", $new_id);

    return ['success' => true, 'message' => 'Event created successfully!', 'id' => $new_id];
}

function update_event($id, $title, $event_date, $event_time = '10:00 AM', $venue = 'Trinity Campus Auditorium', $description = '', $image_path = null, $video_path = null, $is_featured = 0) {
    global $pdo;
    $id = (int)$id;

    if ($pdo instanceof PDO) {
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
            return ['success' => true, 'message' => 'Event updated successfully!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    $events = load_events_from_store();
    $found = false;
    foreach ($events as &$ev) {
        if ((int)$ev['id'] === $id) {
            $found = true;
            $ev['title']       = trim($title);
            $ev['event_date']  = $event_date;
            $ev['event_time']  = trim($event_time ?: '10:00 AM');
            $ev['venue']       = trim($venue ?: 'Trinity Campus Auditorium');
            $ev['description'] = trim($description);
            $ev['is_featured'] = (int)$is_featured;
            if ($image_path) $ev['image_path'] = $image_path;
            if ($video_path) $ev['video_path'] = $video_path;
            break;
        }
    }
    if ($found) {
        save_events_to_store($events);
        log_activity('Updated', 'Events', $title, "Updated event details", $id);
        return ['success' => true, 'message' => 'Event updated successfully!'];
    }
    return ['success' => false, 'message' => 'Event not found.'];
}

function add_event_media($event_id, $file, $media_title, $media_description = '') {
    global $pdo;

    $event_id          = (int)$event_id;
    $media_title       = trim($media_title);
    $media_description = trim($media_description);

    if ($event_id <= 0) {
        return ['success' => false, 'message' => 'Invalid Event selected.'];
    }

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file was uploaded or file error occurred.'];
    }

    // Process file storage into uploads/images or uploads/videos
    $up = process_file_upload($file, $media_title, 'event_media', $media_description);
    if (!$up['success']) {
        return ['success' => false, 'message' => $up['message']];
    }

    $file_path = $up['data']['file_path'];
    $file_type = ($up['data']['file_type'] === 'video') ? 'video' : 'image';

    if ($pdo instanceof PDO) {
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
            $media_id = $pdo->lastInsertId();

            // Also update parent event's primary image/video if empty
            if ($file_type === 'image') {
                $chk = $pdo->prepare("SELECT image_path FROM events WHERE id = :id");
                $chk->execute([':id' => $event_id]);
                $curr = $chk->fetch();
                if ($curr && empty($curr['image_path'])) {
                    $pdo->prepare("UPDATE events SET image_path = :img WHERE id = :id")->execute([':img' => $file_path, ':id' => $event_id]);
                }
            } elseif ($file_type === 'video') {
                $chk = $pdo->prepare("SELECT video_path FROM events WHERE id = :id");
                $chk->execute([':id' => $event_id]);
                $curr = $chk->fetch();
                if ($curr && empty($curr['video_path'])) {
                    $pdo->prepare("UPDATE events SET video_path = :vid WHERE id = :id")->execute([':vid' => $file_path, ':id' => $event_id]);
                }
            }

            log_activity('Uploaded', 'Events', $media_title, "Uploaded {$file_type} for Event #{$event_id}", $media_id);
            return ['success' => true, 'message' => 'Event details and media uploaded successfully!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // JSON fallback store
    $events = load_events_from_store();
    $found = false;
    $media_id = time() . mt_rand(10, 99);

    foreach ($events as &$ev) {
        if ((int)$ev['id'] === $event_id) {
            $found = true;
            if (!isset($ev['media_items']) || !is_array($ev['media_items'])) {
                $ev['media_items'] = [];
            }
            $media_item = [
                'id'                => (int)$media_id,
                'event_id'          => $event_id,
                'media_type'        => $file_type,
                'file_path'         => $file_path,
                'media_title'       => $media_title,
                'media_description' => $media_description,
                'created_at'        => date('Y-m-d H:i:s')
            ];
            array_unshift($ev['media_items'], $media_item);

            if ($file_type === 'image' && empty($ev['image_path'])) {
                $ev['image_path'] = $file_path;
            }
            if ($file_type === 'video' && empty($ev['video_path'])) {
                $ev['video_path'] = $file_path;
            }
            break;
        }
    }

    if (!$found) {
        return ['success' => false, 'message' => 'Selected event not found.'];
    }

    save_events_to_store($events);
    log_activity('Uploaded', 'Events', $media_title, "Uploaded {$file_type} for Event #{$event_id}", $media_id);
    return ['success' => true, 'message' => 'Event details and media uploaded successfully!'];
}

function get_all_event_media($limit = 100) {
    global $pdo;
    $limit = (int)$limit;

    if ($pdo instanceof PDO) {
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
            return $stmt->fetchAll();
        } catch (Throwable $e) {}
    }

    $events = load_events_from_store();
    $all_media = [];
    foreach ($events as $ev) {
        if (!empty($ev['media_items']) && is_array($ev['media_items'])) {
            foreach ($ev['media_items'] as $m) {
                $m['event_title'] = $ev['title'] ?? ('Event #' . ($m['event_id'] ?? ''));
                $m['event_date']  = $ev['event_date'] ?? '';
                $all_media[] = $m;
            }
        }
    }
    usort($all_media, function($a, $b) {
        return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
    });
    return array_slice($all_media, 0, $limit);
}

function update_event_media($media_id, $media_title, $media_description = '', $event_id = null) {
    global $pdo;
    $media_id          = (int)$media_id;
    $media_title       = trim($media_title);
    $media_description = trim($media_description);
    $event_id          = $event_id ? (int)$event_id : null;

    if ($media_id <= 0 || empty($media_title)) {
        return ['success' => false, 'message' => 'Invalid media ID or title.'];
    }

    if ($pdo instanceof PDO) {
        try {
            if ($event_id) {
                $stmt = $pdo->prepare("UPDATE event_media SET media_title = :t, media_description = :d, event_id = :eid WHERE id = :id");
                $stmt->execute([':t' => $media_title, ':d' => $media_description, ':eid' => $event_id, ':id' => $media_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE event_media SET media_title = :t, media_description = :d WHERE id = :id");
                $stmt->execute([':t' => $media_title, ':d' => $media_description, ':id' => $media_id]);
            }
            log_activity('Updated', 'Events', $media_title, "Updated event media details", $media_id);
            return ['success' => true, 'message' => 'Media details updated successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    $events = load_events_from_store();
    $found_item = null;

    foreach ($events as $idx => &$ev) {
        if (!empty($ev['media_items'])) {
            foreach ($ev['media_items'] as $mIdx => &$m) {
                if ((int)$m['id'] === $media_id) {
                    $m['media_title'] = $media_title;
                    $m['media_description'] = $media_description;
                    if ($event_id && $event_id !== (int)$ev['id']) {
                        $found_item = $m;
                        $found_item['event_id'] = $event_id;
                        array_splice($ev['media_items'], $mIdx, 1);
                    } else {
                        $found_item = $m;
                    }
                    break 2;
                }
            }
        }
    }

    if ($found_item && $event_id && (int)$found_item['event_id'] === $event_id) {
        foreach ($events as &$ev) {
            if ((int)$ev['id'] === $event_id) {
                if (!isset($ev['media_items']) || !is_array($ev['media_items'])) {
                    $ev['media_items'] = [];
                }
                $exists = false;
                foreach ($ev['media_items'] as $chk) {
                    if ((int)$chk['id'] === $media_id) { $exists = true; break; }
                }
                if (!$exists) {
                    array_unshift($ev['media_items'], $found_item);
                }
                break;
            }
        }
    }

    if ($found_item) {
        save_events_to_store($events);
        log_activity('Updated', 'Events', $media_title, "Updated event media details", $media_id);
        return ['success' => true, 'message' => 'Media details updated successfully.'];
    }

    return ['success' => false, 'message' => 'Media item not found.'];
}

function delete_event_media($media_id) {
    global $pdo;
    $media_id = (int)$media_id;

    if ($pdo instanceof PDO) {
        try {
            $st = $pdo->prepare("SELECT * FROM event_media WHERE id = :id");
            $st->execute([':id' => $media_id]);
            $row = $st->fetch();
            if ($row) {
                if (!empty($row['file_path']) && strpos($row['file_path'], 'uploads/') === 0) {
                    $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $row['file_path'];
                    if (file_exists($abs)) @unlink($abs);
                }
                $pdo->prepare("DELETE FROM event_media WHERE id = :id")->execute([':id' => $media_id]);
                log_activity('Deleted', 'Events', $row['media_title'], "Deleted event media file", $media_id);
            }
            return ['success' => true, 'message' => 'Media file removed successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    $events = load_events_from_store();
    $found = false;
    $deleted_title = 'Media #' . $media_id;

    foreach ($events as &$ev) {
        if (!empty($ev['media_items'])) {
            $filtered = [];
            foreach ($ev['media_items'] as $m) {
                if ((int)$m['id'] === $media_id) {
                    $found = true;
                    $deleted_title = $m['media_title'];
                    if (!empty($m['file_path']) && strpos($m['file_path'], 'uploads/') === 0) {
                        $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $m['file_path'];
                        if (file_exists($abs)) @unlink($abs);
                    }
                } else {
                    $filtered[] = $m;
                }
            }
            $ev['media_items'] = $filtered;
        }
    }

    if ($found) {
        save_events_to_store($events);
        log_activity('Deleted', 'Events', $deleted_title, "Deleted event media file", $media_id);
        return ['success' => true, 'message' => 'Media file removed successfully.'];
    }

    return ['success' => false, 'message' => 'Media item not found.'];
}

function delete_event($id) {
    global $pdo;
    $id = (int)$id;

    if ($pdo instanceof PDO) {
        try {
            $st = $pdo->prepare("SELECT title FROM events WHERE id = :id");
            $st->execute([':id' => $id]);
            $row = $st->fetch();
            $title = $row ? $row['title'] : 'Event #' . $id;

            // Delete attached media files
            $mStmt = $pdo->prepare("SELECT file_path FROM event_media WHERE event_id = :id");
            $mStmt->execute([':id' => $id]);
            while ($mRow = $mStmt->fetch()) {
                if (!empty($mRow['file_path']) && strpos($mRow['file_path'], 'uploads/') === 0) {
                    $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $mRow['file_path'];
                    if (file_exists($abs)) @unlink($abs);
                }
            }
            $pdo->prepare("DELETE FROM event_media WHERE event_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM events WHERE id = :id")->execute([':id' => $id]);

            log_activity('Deleted', 'Events', $title, "Deleted event from schedule", $id);
            return ['success' => true, 'message' => 'Event and its media deleted successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    $events = load_events_from_store();
    $newList = [];
    $title = 'Event #' . $id;
    foreach ($events as $ev) {
        if ((int)$ev['id'] === $id) {
            $title = $ev['title'];
            if (!empty($ev['media_items'])) {
                foreach ($ev['media_items'] as $m) {
                    if (!empty($m['file_path']) && strpos($m['file_path'], 'uploads/') === 0) {
                        $abs = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . $m['file_path'];
                        if (file_exists($abs)) @unlink($abs);
                    }
                }
            }
        } else {
            $newList[] = $ev;
        }
    }
    save_events_to_store($newList);
    log_activity('Deleted', 'Events', $title, "Deleted event from schedule", $id);
    return ['success' => true, 'message' => 'Event and its media deleted successfully.'];
}

// ==============================================================
// 5. NOTIFICATIONS & CIRCULARS (PDF, IMAGE, DOCX, MARQUEE TICKER)
// ==============================================================

function get_notifications_store_file() {
    return __DIR__ . '/config/notifications_data.json';
}

function get_default_seed_notifications() {
    return [
        [
            'id' => 1,
            'title' => 'B.Tech Autonomous End Semester Examination Schedule AY 2026-27',
            'category' => 'Circular',
            'description' => 'Official circular regarding semester end regular & supplementary examination timetables, fee payment schedule, and hall ticket issuance.',
            'attachment_type' => 'pdf',
            'attachment_path' => 'uploads/pdfs/exam_schedule_2026.pdf',
            'file_path' => 'uploads/pdfs/exam_schedule_2026.pdf',
            'link_url' => null,
            'is_marquee' => 1,
            'publish_date' => '2026-10-08',
            'is_active' => 1,
            'created_at' => '2026-10-08 09:30:00'
        ],
        [
            'id' => 2,
            'title' => 'Admissions Guidelines & Merit Fee Concession Policy 2026-27',
            'category' => 'Circular',
            'description' => 'Guidelines for B.Tech, Polytechnic Diploma, and MBA admissions counseling under EAPCET, POLYCET and ICET. Code: TCEK.',
            'attachment_type' => 'docx',
            'attachment_path' => 'uploads/documents/admission_guidelines_2026.docx',
            'file_path' => 'uploads/documents/admission_guidelines_2026.docx',
            'link_url' => 'admission.php',
            'is_marquee' => 1,
            'publish_date' => '2026-10-05',
            'is_active' => 1,
            'created_at' => '2026-10-05 11:15:00'
        ],
        [
            'id' => 3,
            'title' => 'Campus Placement Drive by Top Tier-1 Tech MNCs',
            'category' => 'Circular',
            'description' => 'Special training sessions, mock interviews, and drive timetable by visiting tier-1 technology multinationals for final year students.',
            'attachment_type' => 'image',
            'attachment_path' => 'assets/College Event/caps.jpg',
            'file_path' => 'assets/College Event/caps.jpg',
            'link_url' => 'placement-cell.php',
            'is_marquee' => 0,
            'publish_date' => '2026-10-02',
            'is_active' => 1,
            'created_at' => '2026-10-02 14:00:00'
        ],
        [
            'id' => 4,
            'title' => 'Autonomous Academic Council Regulations & Syllabi Notification',
            'category' => 'Circular',
            'description' => 'Detailed curriculum regulations approved by the Academic Council for B.Tech CSE, AIML, ECE, and Allied branches under Autonomous status.',
            'attachment_type' => 'pdf',
            'attachment_path' => 'uploads/pdfs/autonomous_notification_ay2026.pdf',
            'file_path' => 'uploads/pdfs/autonomous_notification_ay2026.pdf',
            'link_url' => 'academics.php',
            'is_marquee' => 0,
            'publish_date' => '2026-09-28',
            'is_active' => 1,
            'created_at' => '2026-09-28 10:00:00'
        ]
    ];
}

function load_notifications_from_store() {
    $file = get_notifications_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
    }
    $defaults = get_default_seed_notifications();
    save_notifications_to_store($defaults);
    return $defaults;
}

function save_notifications_to_store($notifications) {
    $file = get_notifications_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return @file_put_contents($file, json_encode(array_values($notifications), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function get_notifications($limit = 50, $marquee_only = false) {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $where = $marquee_only ? "WHERE is_marquee = 1 AND is_active = 1" : "WHERE is_active = 1";
            $sql = "SELECT * FROM notifications {$where} ORDER BY is_marquee DESC, publish_date DESC, id DESC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll();
            if (!empty($res)) {
                foreach ($res as &$r) {
                    if (empty($r['file_path']) && !empty($r['attachment_path'])) {
                        $r['file_path'] = $r['attachment_path'];
                    }
                }
                return $res;
            }
        } catch (PDOException $e) {
            error_log("Notifications DB query failed: " . $e->getMessage());
        }
    }

    $all = load_notifications_from_store();
    if ($marquee_only) {
        $all = array_filter($all, function($item) {
            return !empty($item['is_marquee']) && !empty($item['is_active']);
        });
    } else {
        $all = array_filter($all, function($item) {
            return !empty($item['is_active']);
        });
    }

    usort($all, function($a, $b) {
        $cmp = strcmp($b['publish_date'] ?? '', $a['publish_date'] ?? '');
        return ($cmp !== 0) ? $cmp : ((int)($b['id'] ?? 0) - (int)($a['id'] ?? 0));
    });

    foreach ($all as &$r) {
        if (empty($r['file_path']) && !empty($r['attachment_path'])) {
            $r['file_path'] = $r['attachment_path'];
        }
    }

    return array_slice($all, 0, (int)$limit);
}

function get_notification_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch();
            if ($item) {
                if (empty($item['file_path']) && !empty($item['attachment_path'])) {
                    $item['file_path'] = $item['attachment_path'];
                }
                return $item;
            }
        } catch (PDOException $e) {}
    }

    $all = load_notifications_from_store();
    foreach ($all as $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            if (empty($item['file_path']) && !empty($item['attachment_path'])) {
                $item['file_path'] = $item['attachment_path'];
            }
            return $item;
        }
    }
    return null;
}

// Alias for backwards compatibility
function get_scrollbar_items($limit = 30) {
    $items = get_notifications($limit, true);
    if (empty($items)) {
        return get_notifications($limit, false);
    }
    return $items;
}

function get_notices($limit = 20, $active_only = true) {
    return get_notifications($limit, false);
}

function add_notification($title, $category, $description, $attachment_type = 'none', $attachment_path = null, $link_url = null, $is_marquee = 0, $publish_date = null) {
    global $pdo;
    $title        = trim($title);
    $category     = trim($category ?: 'Circular');
    $description  = trim($description);
    $publish_date = !empty($publish_date) ? trim($publish_date) : date('Y-m-d');
    $is_marquee   = (int)$is_marquee;

    $new_id = null;
    if ($pdo instanceof PDO) {
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
        } catch (PDOException $e) {
            error_log("Failed to insert notification into MySQL: " . $e->getMessage());
        }
    }

    $all = load_notifications_from_store();
    if (!$new_id) {
        $max_id = 0;
        foreach ($all as $item) {
            if ((int)($item['id'] ?? 0) > $max_id) $max_id = (int)$item['id'];
        }
        $new_id = $max_id + 1;
    }

    $newItem = [
        'id'              => $new_id,
        'title'           => $title,
        'category'        => $category,
        'description'     => $description,
        'attachment_type' => $attachment_type,
        'attachment_path' => $attachment_path,
        'file_path'       => $attachment_path,
        'link_url'        => !empty($link_url) ? trim($link_url) : null,
        'is_marquee'      => $is_marquee,
        'publish_date'    => $publish_date,
        'is_active'       => 1,
        'created_at'      => date('Y-m-d H:i:s')
    ];

    array_unshift($all, $newItem);
    save_notifications_to_store($all);

    log_activity('Added', 'Circulars', $title, "Published official circular (Attachment: {$attachment_type})", $new_id);
    return ['success' => true, 'message' => 'Circular published successfully!', 'id' => $new_id];
}

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

    if ($pdo instanceof PDO) {
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
        } catch (PDOException $e) {
            error_log("Failed to update notification in MySQL: " . $e->getMessage());
        }
    }

    $all = load_notifications_from_store();
    foreach ($all as &$item) {
        if ((int)($item['id'] ?? 0) === $id) {
            $item['title']           = $title;
            $item['category']        = $category;
            $item['description']     = $description;
            $item['attachment_type'] = $attachment_type;
            $item['attachment_path'] = $attachment_path;
            $item['file_path']       = $attachment_path;
            $item['link_url']        = $link_url;
            $item['is_marquee']      = $is_marquee;
            $item['publish_date']    = $publish_date;
            $item['updated_at']      = date('Y-m-d H:i:s');
            break;
        }
    }
    save_notifications_to_store($all);

    log_activity('Updated', 'Circulars', $title, "Updated circular details and file attachment", $id);
    return ['success' => true, 'message' => 'Circular updated successfully!'];
}

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

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id");
            $stmt->execute([':id' => $id]);
            try {
                $pdo->prepare("DELETE FROM notices WHERE id = :id")->execute([':id' => $id]);
            } catch (Exception $e2) {}
        } catch (PDOException $e) {
            error_log("Failed to delete notification from MySQL: " . $e->getMessage());
        }
    }

    $all = load_notifications_from_store();
    $filtered = array_filter($all, function($item) use ($id) {
        return (int)($item['id'] ?? 0) !== $id;
    });
    save_notifications_to_store($filtered);

    log_activity('Deleted', 'Circulars', $title, "Removed circular and associated document", $id);
    return ['success' => true, 'message' => 'Circular deleted successfully.'];
}

// ==============================================================
// 6. STAFF DIRECTORY (PROFILES & PROFILE IMAGES)
// ==============================================================

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
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

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
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

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
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// ==============================================================
// 7. UPLOADS STORAGE REGISTRY
// ==============================================================

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
function get_workshops($limit = 50) {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return [
            ['id' => 1, 'title' => 'Generative AI & LLM Deployment Workshop', 'instructor' => 'Dr. A. K. Vootla', 'category' => 'AI / ML', 'event_date' => '2026-10-15', 'venue' => 'CSE Lab 3', 'status' => 'ACTIVE', 'description' => 'Hands-on development of full-stack AI applications with Python & PyTorch.'],
            ['id' => 2, 'title' => 'Full-Stack Web Dev Sprint (HTML, PHP, MySQL)', 'instructor' => 'Charan (Lead Developer)', 'category' => 'Web Dev', 'event_date' => '2026-10-22', 'venue' => 'Seminar Hall A', 'status' => 'UPCOMING', 'description' => 'Live deployment to GoDaddy cPanel hosting, database triggers, and auth.'],
            ['id' => 3, 'title' => 'IoT Smart Embedded Robotics Task', 'instructor' => 'Prof. S. Rao (ECE HoD)', 'category' => 'Embedded / IoT', 'event_date' => '2026-11-05', 'venue' => 'Robotics Studio', 'status' => 'UPCOMING', 'description' => 'Microcontroller sensors, Arduino, and ESP32 wireless telemetry.']
        ];
    }
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS workshops (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            instructor VARCHAR(150) DEFAULT NULL,
            category VARCHAR(100) DEFAULT 'Technical',
            event_date DATE NOT NULL,
            venue VARCHAR(255) DEFAULT 'TCEK Seminar Hall',
            description TEXT DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'UPCOMING',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $stmt = $pdo->prepare("SELECT * FROM workshops ORDER BY event_date ASC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if (empty($res)) {
            add_workshop('Generative AI & LLM Deployment Workshop', 'Dr. A. K. Vootla', 'AI / ML', '2026-10-15', 'CSE Lab 3', 'Hands-on development of full-stack AI applications.', 'ACTIVE');
            add_workshop('Full-Stack Web Dev Sprint (PHP, MySQL)', 'Charan (Lead Developer)', 'Web Dev', '2026-10-22', 'Seminar Hall A', 'Live deployment to GoDaddy cPanel hosting.', 'UPCOMING');
            $stmt->execute();
            return $stmt->fetchAll();
        }
        return $res;
    } catch (PDOException $e) {
        return [];
    }
}

function add_workshop($title, $instructor, $category, $event_date, $venue, $description, $status = 'UPCOMING') {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];
    try {
        $stmt = $pdo->prepare("INSERT INTO workshops (title, instructor, category, event_date, venue, description, status, created_at) VALUES (:t, :i, :c, :d, :v, :desc, :st, NOW())");
        $stmt->execute([
            ':t'    => trim($title),
            ':i'    => trim($instructor),
            ':c'    => trim($category),
            ':d'    => $event_date,
            ':v'    => trim($venue),
            ':desc' => trim($description),
            ':st'   => trim($status)
        ]);
        $id = $pdo->lastInsertId();
        log_activity('Added', 'Workshops', $title, "Created workshop/task in category '{$category}'", $id);
        return ['success' => true, 'message' => 'Workshop / Task created successfully!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function delete_workshop($id) {
    global $pdo;
    if (!($pdo instanceof PDO)) return ['success' => false, 'message' => 'Database not connected.'];
    try {
        $st = $pdo->prepare("SELECT title FROM workshops WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        $title = $row ? $row['title'] : 'Workshop #' . $id;

        $stmt = $pdo->prepare("DELETE FROM workshops WHERE id = :id");
        $stmt->execute([':id' => $id]);
        log_activity('Deleted', 'Workshops', $title, "Deleted workshop record", $id);
        return ['success' => true, 'message' => 'Workshop removed.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// ==============================================================
// 9. NEWS & PRESS CLIPPINGS MANAGEMENT
// ==============================================================

function get_news_store_file() {
    return __DIR__ . '/config/news_data.json';
}

function get_default_seed_news() {
    return [
        [
            'id' => 1,
            'title' => 'Yuva Sangam: Trinity Student Selected for National Tour to IIT Guwahati',
            'image_path' => 'assets/Gallery/paper1.jpg',
            'publish_date' => '2026-10-01',
            'description' => 'Mana Telangana: Trinity College student Saniya selected for the prestigious Yuva Sangam national youth exposure tour to IIT Guwahati under the Ek Bharat Shreshtha Bharat initiative.',
            'source' => 'Mana Telangana',
            'status' => 'PUBLISHED',
            'created_at' => '2026-10-01 10:00:00'
        ],
        [
            'id' => 2,
            'title' => 'Chairman Manohar Reddy Felicitates Saniya on National Yuva Sangam Selection',
            'image_path' => 'assets/Gallery/paper2.jpg',
            'publish_date' => '2026-09-28',
            'description' => 'Prajakranthi: Founder Chairman Sri Manohar Reddy and administrative management felicitate B.Tech student Saniya for selection in the national delegation.',
            'source' => 'Prajakranthi',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-28 11:30:00'
        ],
        [
            'id' => 3,
            'title' => 'Engineering Colleges Under Vigilance Radar: Quality & Compliance Inspections Begin',
            'image_path' => 'assets/Gallery/paper3.jpg',
            'publish_date' => '2026-09-22',
            'description' => 'Andhra Prabha: Vigilance and accreditation inspection teams evaluate engineering colleges across the state on faculty standards, research facilities, and lab infrastructure.',
            'source' => 'Andhra Prabha',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-22 09:15:00'
        ],
        [
            'id' => 4,
            'title' => 'Quality Benchmark & Lab Infrastructure Vigilance Inspection Team Visit',
            'image_path' => 'assets/Gallery/paper4.jpg',
            'publish_date' => '2026-09-18',
            'description' => 'State Daily: Inspection team praises Trinity College for state-of-the-art laboratory infrastructure, AI & Robotics center of excellence, and qualified faculty.',
            'source' => 'State Daily',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-18 14:00:00'
        ],
        [
            'id' => 5,
            'title' => 'Smart India Hackathon 2026 Conducted at Trinity Engineering College',
            'image_path' => 'assets/Gallery/paper5.jpg',
            'publish_date' => '2026-09-10',
            'description' => 'Andhra Prabha: Grand launch of Smart India Hackathon internal round at Trinity campus with over 200 enthusiastic engineering students competing across hardware and software domains.',
            'source' => 'Andhra Prabha',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-10 10:30:00'
        ],
        [
            'id' => 6,
            'title' => 'Smart India Hackathon 2026: Students Showcase Real-World Technical Prototypes',
            'image_path' => 'assets/Gallery/paper6.jpg',
            'publish_date' => '2026-09-08',
            'description' => 'Mana Telangana: Student innovators exhibited functional IoT and machine learning prototypes solving real-world agricultural and municipal problems.',
            'source' => 'Mana Telangana',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-08 12:00:00'
        ],
        [
            'id' => 7,
            'title' => 'Smart India Hackathon 2026: Innovative Problem Solving at Trinity Autonomous',
            'image_path' => 'assets/Gallery/paper7.jpg',
            'publish_date' => '2026-09-05',
            'description' => 'Namasthe Telangana: Jury members appreciate the high technical caliber of projects developed during 36-hour hackathon coding sprint.',
            'source' => 'Namasthe Telangana',
            'status' => 'PUBLISHED',
            'created_at' => '2026-09-05 16:45:00'
        ],
        [
            'id' => 8,
            'title' => 'MSME Hackathon 6.0: 168 Student Project Submissions with ₹15L Funding Support',
            'image_path' => 'assets/Gallery/paper8.jpg',
            'publish_date' => '2026-08-25',
            'description' => 'Mana Telangana: Under the Ministry of MSME Idea Hackathon 6.0, Trinity Host Institute shortlisted outstanding startup ideas eligible for up to ₹15 Lakhs central government grant funding.',
            'source' => 'Mana Telangana',
            'status' => 'PUBLISHED',
            'created_at' => '2026-08-25 11:00:00'
        ],
        [
            'id' => 9,
            'title' => 'MSME Hackathon 6.0: Nurturing Youth Innovation Across 6 Thematic Sectors',
            'image_path' => 'assets/Gallery/paper9.jpg',
            'publish_date' => '2026-08-22',
            'description' => 'Namasthe Telangana: Engineering innovators pitched scalable business and technical models in healthcare, defense, and green technology.',
            'source' => 'Namasthe Telangana',
            'status' => 'PUBLISHED',
            'created_at' => '2026-08-22 15:30:00'
        ],
        [
            'id' => 10,
            'title' => 'Tremendous Response to MSME Hackathon 6.0 with Up to ₹15 Lakhs Grant',
            'image_path' => 'assets/Gallery/paper10.jpg',
            'publish_date' => '2026-08-18',
            'description' => 'Andhra Prabha: Record number of participants submitted technological business solutions for government incubation funding.',
            'source' => 'Andhra Prabha',
            'status' => 'PUBLISHED',
            'created_at' => '2026-08-18 10:15:00'
        ],
        [
            'id' => 11,
            'title' => 'MSME Idea Hackathon 6.0 Successfully Conducted at Trinity Campus',
            'image_path' => 'assets/Gallery/paper11.jpg',
            'publish_date' => '2026-08-15',
            'description' => 'Eenadu Daily: College leadership and industry experts evaluated student pitches during the nationwide entrepreneurship program.',
            'source' => 'Eenadu Daily',
            'status' => 'PUBLISHED',
            'created_at' => '2026-08-15 17:00:00'
        ]
    ];
}

function load_news_from_store() {
    $file = get_news_store_file();
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        if ($json) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
    }
    $defaults = get_default_seed_news();
    save_news_to_store($defaults);
    return $defaults;
}

function save_news_to_store($news) {
    $file = get_news_store_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return @file_put_contents($file, json_encode(array_values($news), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function get_news($limit = 50) {
    global $pdo;
    if ($pdo instanceof PDO) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS news (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                publish_date DATE NOT NULL,
                description TEXT DEFAULT NULL,
                source VARCHAR(100) DEFAULT 'Press & Media',
                link_url VARCHAR(255) DEFAULT NULL,
                status VARCHAR(50) DEFAULT 'PUBLISHED',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            try {
                $cols = $pdo->query("SHOW COLUMNS FROM news LIKE 'image_path'")->fetchAll();
                if (empty($cols)) {
                    $pdo->exec("ALTER TABLE news ADD COLUMN image_path VARCHAR(255) DEFAULT NULL AFTER title");
                }
                $descCols = $pdo->query("SHOW COLUMNS FROM news LIKE 'description'")->fetchAll();
                if (empty($descCols)) {
                    $pdo->exec("ALTER TABLE news ADD COLUMN description TEXT DEFAULT NULL AFTER publish_date");
                }
            } catch (Exception $e) {}

            $stmt = $pdo->prepare("SELECT * FROM news ORDER BY publish_date DESC, id DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll();
            if (!empty($res)) {
                return $res;
            }

            // Seed if empty
            $seeds = get_default_seed_news();
            $ins = $pdo->prepare("INSERT INTO news (id, title, image_path, publish_date, description, source, status, created_at) VALUES (:id, :t, :img, :d, :dsc, :src, 'PUBLISHED', :ca) ON DUPLICATE KEY UPDATE title=VALUES(title)");
            foreach ($seeds as $s) {
                $ins->execute([
                    ':id'  => $s['id'],
                    ':t'   => $s['title'],
                    ':img' => $s['image_path'],
                    ':d'   => $s['publish_date'],
                    ':dsc' => $s['description'],
                    ':src' => $s['source'] ?? 'Press & Media',
                    ':ca'  => $s['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            $stmt->execute();
            $seeded_res = $stmt->fetchAll();
            if (!empty($seeded_res)) {
                return $seeded_res;
            }
        } catch (PDOException $e) {
            error_log("News DB query failed: " . $e->getMessage());
        }
    }

    $all = load_news_from_store();
    usort($all, function($a, $b) {
        $c = strcmp($b['publish_date'] ?? '', $a['publish_date'] ?? '');
        return ($c !== 0) ? $c : ((int)($b['id'] ?? 0) - (int)($a['id'] ?? 0));
    });
    return array_slice($all, 0, (int)$limit);
}

function get_news_by_id($id) {
    global $pdo;
    $id = (int)$id;
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch();
            if ($item) return $item;
        } catch (PDOException $e) {}
    }
    $all = load_news_from_store();
    foreach ($all as $item) {
        if ((int)($item['id'] ?? 0) === $id) {
            return $item;
        }
    }
    return null;
}

function add_news($title, $publish_date, $description, $image_file = null, $source = 'Press & Media') {
    global $pdo;
    $title = trim($title);
    $publish_date = !empty($publish_date) ? trim($publish_date) : date('Y-m-d');
    $description = trim($description);
    $image_path = null;

    if ($image_file && isset($image_file['error']) && $image_file['error'] === UPLOAD_ERR_OK) {
        $up = process_file_upload($image_file, $title, 'news', $description);
        if ($up['success']) {
            $image_path = $up['data']['file_path'];
        } else {
            return ['success' => false, 'message' => 'Image upload failed: ' . $up['message']];
        }
    }

    $new_id = null;
    if ($pdo instanceof PDO) {
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
        } catch (PDOException $e) {
            error_log("Failed to insert news to MySQL: " . $e->getMessage());
        }
    }

    $all = load_news_from_store();
    if (!$new_id) {
        $max_id = 0;
        foreach ($all as $item) {
            if ((int)($item['id'] ?? 0) > $max_id) $max_id = (int)$item['id'];
        }
        $new_id = $max_id + 1;
    }

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
    array_unshift($all, $new_item);
    save_news_to_store($all);

    log_activity('Added', 'News', $title, "Uploaded newspaper news clipping", $new_id);
    return ['success' => true, 'message' => 'Newspaper news uploaded and published successfully!', 'id' => $new_id, 'data' => $new_item];
}

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
            $image_path = $up['data']['file_path'];
        } else {
            return ['success' => false, 'message' => 'Image upload failed: ' . $up['message']];
        }
    }

    if ($pdo instanceof PDO) {
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
        } catch (PDOException $e) {
            error_log("Failed to update news in MySQL: " . $e->getMessage());
        }
    }

    $all = load_news_from_store();
    $found = false;
    foreach ($all as &$item) {
        if ((int)($item['id'] ?? 0) === $id) {
            $item['title']        = $title;
            if ($image_path !== null) {
                $item['image_path'] = $image_path;
            }
            $item['publish_date'] = $publish_date;
            $item['description']  = $description;
            $item['source']       = $source;
            $item['updated_at']   = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $all[] = [
            'id'           => $id,
            'title'        => $title,
            'image_path'   => $image_path,
            'publish_date' => $publish_date,
            'description'  => $description,
            'source'       => $source,
            'status'       => 'PUBLISHED',
            'created_at'   => date('Y-m-d H:i:s')
        ];
    }
    save_news_to_store($all);

    log_activity('Updated', 'News', $title, "Updated newspaper news clipping details", $id);
    return ['success' => true, 'message' => 'Newspaper news updated successfully!'];
}

function delete_news($id) {
    global $pdo;
    $id = (int)$id;
    $existing = get_news_by_id($id);
    $title = $existing ? ($existing['title'] ?? 'News #' . $id) : 'News #' . $id;

    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Failed to delete news from MySQL: " . $e->getMessage());
        }
    }

    $all = load_news_from_store();
    $filtered = array_filter($all, function($item) use ($id) {
        return (int)($item['id'] ?? 0) !== $id;
    });
    save_news_to_store($filtered);

    log_activity('Deleted', 'News', $title, "Deleted newspaper news clipping", $id);
    return ['success' => true, 'message' => 'Newspaper news clipping deleted successfully.'];
}

// ==============================================================
// 10. DISPATCHER FOR ADMIN FORM SUBMISSIONS
// ==============================================================

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    require_admin_login();

    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_msg']  = 'Security validation failed (CSRF token invalid). Please try again.';
        header('Location: ../admin/dashboard.php');
        exit;
    }

    $action = $_POST['action'];

    // --- NOTIFICATION & CIRCULAR ACTIONS ---
    if ($action === 'add_notification' || $action === 'add_notice' || $action === 'add_circular') {
        $title        = trim($_POST['title'] ?? '');
        $category     = trim($_POST['category'] ?? 'Circular');
        $description  = trim($_POST['description'] ?? '');
        $link_url     = trim($_POST['link_url'] ?? '');
        $publish_date = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $is_marquee   = isset($_POST['is_marquee']) ? 1 : (isset($_POST['is_pinned']) ? 1 : 0);
        $file_path    = null;
        $file_type    = 'none';

        if (empty($title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Circular Title is required.';
            header('Location: ../admin/dashboard.php?tab=notifications');
            exit;
        }

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['attachment'], $title, 'circular', $description);
            if ($up['success']) {
                $file_path = $up['data']['file_path'];
                $file_type = $up['data']['file_type']; // 'pdf', 'image', 'docx'
            } else {
                $_SESSION['flash_type'] = 'danger';
                $_SESSION['flash_msg']  = 'Attachment upload error: ' . $up['message'];
                header('Location: ../admin/dashboard.php?tab=notifications');
                exit;
            }
        } elseif (!empty($_POST['existing_file_path'])) {
            $file_path = trim($_POST['existing_file_path']);
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
        $id           = (int)($_POST['id'] ?? 0);
        $title        = trim($_POST['title'] ?? '');
        $category     = trim($_POST['category'] ?? 'Circular');
        $description  = trim($_POST['description'] ?? '');
        $link_url     = trim($_POST['link_url'] ?? '');
        $publish_date = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $is_marquee   = isset($_POST['is_marquee']) ? 1 : 0;
        $attachment   = (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) ? $_FILES['attachment'] : null;

        if ($id <= 0 || empty($title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Circular ID and Title are required.';
            header('Location: ../admin/dashboard.php?tab=notifications');
            exit;
        }

        $res = update_notification($id, $title, $publish_date, $description, $attachment, $category, $link_url, $is_marquee);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=notifications');
        exit;
    }

    if ($action === 'delete_notification' || $action === 'delete_notice' || $action === 'delete_circular') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_notification($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=notifications');
        exit;
    }

    // --- EVENTS ACTIONS ---
    if ($action === 'add_event') {
        $title       = trim($_POST['title'] ?? '');
        $event_date  = $_POST['event_date'] ?? date('Y-m-d');
        $event_time  = trim($_POST['event_time'] ?? '10:00 AM');
        $venue       = trim($_POST['venue'] ?? 'Trinity Campus Auditorium');
        $description = trim($_POST['description'] ?? '');

        if (empty($title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Event Name / Title is required.';
            header('Location: ../admin/dashboard.php?tab=events');
            exit;
        }

        $res = add_event($title, $event_date, $event_time, $description, $venue);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    if ($action === 'upload_event_media' || $action === 'upload_event_details') {
        $event_id          = (int)($_POST['event_id'] ?? 0);
        $media_title       = trim($_POST['media_title'] ?? '');
        $media_description = trim($_POST['media_description'] ?? '');

        if ($event_id <= 0) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Please select a valid event.';
            header('Location: ../admin/dashboard.php?tab=events');
            exit;
        }

        if (empty($media_title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Media Name / Title is required.';
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Please select an image or video file to upload.';
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        $res = add_event_media($event_id, $_FILES['media_file'], $media_title, $media_description);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'delete_event_media') {
        $media_id = (int)($_POST['media_id'] ?? 0);
        $res = delete_event_media($media_id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'update_event_media' || $action === 'edit_event_media') {
        $media_id          = (int)($_POST['media_id'] ?? 0);
        $event_id          = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $media_title       = trim($_POST['media_title'] ?? '');
        $media_description = trim($_POST['media_description'] ?? '');

        if ($media_id <= 0 || empty($media_title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'File Name / Title is required.';
            header('Location: ../admin/dashboard.php?tab=event_files');
            exit;
        }

        $res = update_event_media($media_id, $media_title, $media_description, $event_id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=event_files');
        exit;
    }

    if ($action === 'update_event') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $event_date  = $_POST['event_date'] ?? date('Y-m-d');
        $event_time  = trim($_POST['event_time'] ?? '10:00 AM');
        $venue       = trim($_POST['venue'] ?? 'Trinity Campus Auditorium');
        $description = trim($_POST['description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image_path  = null;
        $video_path  = null;

        $res = update_event($id, $title, $event_date, $event_time, $venue, $description, $image_path, $video_path, $is_featured);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    if ($action === 'delete_event') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_event($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=events');
        exit;
    }

    // --- GALLERY ACTIONS (IMAGES & VIDEOS) ---
    if ($action === 'add_gallery') {
        $title       = $_POST['title'] ?? '';
        $media_type  = $_POST['media_type'] ?? 'image';
        $category    = $_POST['category'] ?? 'events';
        $description = $_POST['description'] ?? '';
        $video_url   = !empty($_POST['video_url']) ? trim($_POST['video_url']) : null;
        $file_path   = null;

        if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['gallery_file'], $title, 'gallery_' . $category, $description);
            if ($up['success']) {
                $file_path = $up['data']['file_path'];
                if ($up['data']['file_type'] === 'video') $media_type = 'video';
            }
        }

        $res = add_gallery_item($title, $media_type, $category, $file_path, $video_url, $description);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=gallery');
        exit;
    }

    if ($action === 'delete_gallery') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_gallery_item($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=gallery');
        exit;
    }

    // --- STAFF ACTIONS ---
    if ($action === 'add_staff') {
        $full_name     = $_POST['full_name'] ?? '';
        $designation   = $_POST['designation'] ?? '';
        $department    = $_POST['department'] ?? 'CSE';
        $qualification = $_POST['qualification'] ?? '';
        $email         = $_POST['email'] ?? '';
        $phone         = $_POST['phone'] ?? '';
        $bio           = $_POST['bio'] ?? '';
        $display_order = (int)($_POST['display_order'] ?? 0);
        $profile_image = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $up = process_file_upload($_FILES['profile_image'], $full_name . ' Profile', 'staff_photo', 'Staff profile photo');
            if ($up['success']) $profile_image = $up['data']['file_path'];
        }

        $res = add_staff($full_name, $designation, $department, $qualification, $email, $phone, $profile_image, $bio, $display_order);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=staff');
        exit;
    }

    if ($action === 'delete_staff') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_staff($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=staff');
        exit;
    }

    // --- USER MANAGEMENT ACTIONS ---
    if ($action === 'add_user') {
        require_admin_role();
        $username  = $_POST['username'] ?? '';
        $password  = $_POST['password'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $email     = $_POST['email'] ?? '';
        $role      = $_POST['role'] ?? 'staff';

        $res = add_user($username, $password, $full_name, $email, $role);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=users');
        exit;
    }

    if ($action === 'delete_user') {
        require_admin_role();
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)($_SESSION['tcek_admin_id'] ?? 0)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'You cannot delete your own account.';
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
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_uploaded_file($id);
        log_activity('Deleted', 'Uploads', 'File #' . $id, 'Deleted file from GoDaddy disk and MySQL registry');
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=explorer');
        exit;
    }

    // --- WORKSHOPS & TASKS ACTIONS ---
    if ($action === 'add_workshop') {
        $title       = $_POST['title'] ?? '';
        $instructor  = $_POST['instructor'] ?? '';
        $category    = $_POST['category'] ?? 'Technical';
        $event_date  = $_POST['event_date'] ?? date('Y-m-d');
        $venue       = $_POST['venue'] ?? 'TCEK Seminar Hall';
        $description = $_POST['description'] ?? '';
        $status      = $_POST['status'] ?? 'UPCOMING';

        $res = add_workshop($title, $instructor, $category, $event_date, $venue, $description, $status);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=workshops');
        exit;
    }

    if ($action === 'delete_workshop') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_workshop($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=workshops');
        exit;
    }

    // --- NEWS ACTIONS ---
    if ($action === 'add_news') {
        $title        = trim($_POST['title'] ?? '');
        $publish_date = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $description  = trim($_POST['description'] ?? ($_POST['summary'] ?? ''));
        $source       = trim($_POST['source'] ?? 'Press & Media');
        $image_file   = (isset($_FILES['newspaper_image']) && $_FILES['newspaper_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['newspaper_image'] : null;

        if (empty($title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'News Title is required.';
            header('Location: ../admin/dashboard.php?tab=news');
            exit;
        }

        $res = add_news($title, $publish_date, $description, $image_file, $source);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    if ($action === 'update_news') {
        $id           = (int)($_POST['id'] ?? 0);
        $title        = trim($_POST['title'] ?? '');
        $publish_date = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $description  = trim($_POST['description'] ?? ($_POST['summary'] ?? ''));
        $source       = trim($_POST['source'] ?? 'Press & Media');
        $image_file   = (isset($_FILES['newspaper_image']) && $_FILES['newspaper_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['newspaper_image'] : null;

        if (empty($id) || empty($title)) {
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['flash_msg']  = 'Invalid news item or title is empty.';
            header('Location: ../admin/dashboard.php?tab=news');
            exit;
        }

        $res = update_news($id, $title, $publish_date, $description, $image_file, $source);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    if ($action === 'delete_news') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_news($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $res['message'];
        header('Location: ../admin/dashboard.php?tab=news');
        exit;
    }

    // --- SCROLLBAR TICKER ACTIONS ---
    if ($action === 'add_scrollbar') {
        $title       = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $link_url    = $_POST['link_url'] ?? '';
        $publish_date= date('Y-m-d');
        
        $res = add_notification($title, 'Marquee Ticker', $description, 'none', null, $link_url, 1, $publish_date);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = 'Scrollbar marquee announcement updated successfully!';
        header('Location: ../admin/dashboard.php?tab=scrollbar');
        exit;
    }

    if ($action === 'delete_scrollbar') {
        $id = (int)($_POST['id'] ?? 0);
        $res = delete_notification($id);
        $_SESSION['flash_type'] = $res['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = 'Scrollbar marquee item removed.';
        header('Location: ../admin/dashboard.php?tab=scrollbar');
        exit;
    }
}

