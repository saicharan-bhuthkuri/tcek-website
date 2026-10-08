<?php
/**
 * File Upload Handler for Trinity College of Engineering & Technology
 * Handles storage of actual files in GoDaddy storage (uploads/images, uploads/pdfs, uploads/videos)
 * and metadata recording in MySQL database.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';

// Web root directory for uploads (public/ or public_html/)
define('UPLOAD_BASE_DIR', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads');

/**
 * Handle file upload, save to disk, and save record in MySQL
 * 
 * @param array $file $_FILES['input_name']
 * @param string $title Friendly title/label
 * @param string $category Category (e.g. 'notice', 'circular', 'gallery', 'event')
 * @param string $description Optional description
 * @return array ['success' => bool, 'message' => string, 'data' => array|null]
 */
function process_file_upload($file, $title = '', $category = 'general', $description = '') {
    global $pdo;

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_codes = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE directive in HTML form.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        ];
        $msg = $error_codes[$file['error']] ?? 'Upload error occurred.';
        return ['success' => false, 'message' => $msg, 'data' => null];
    }

    $orig_name = basename($file['name']);
    $file_size = (int)$file['size'];
    $file_tmp  = $file['tmp_name'];
    $extension = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

    // Security check: Block dangerous extensions
    $forbidden = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'pht', 'phar', 'inc', 'exe', 'bat', 'sh', 'py', 'pl', 'cgi', 'js', 'html', 'htm'];
    if (in_array($extension, $forbidden)) {
        return ['success' => false, 'message' => 'Security Error: Executable or script files are not allowed.', 'data' => null];
    }

    // Determine category folder and file type
    $allowed_images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $allowed_pdfs   = ['pdf'];
    $allowed_docs   = ['docx', 'doc'];
    $allowed_videos = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

    $sub_folder = 'others';
    $file_type  = 'other';

    if (in_array($extension, $allowed_images)) {
        $sub_folder = 'images';
        $file_type  = 'image';
    } elseif (in_array($extension, $allowed_pdfs)) {
        $sub_folder = 'pdfs';
        $file_type  = 'pdf';
    } elseif (in_array($extension, $allowed_docs)) {
        $sub_folder = 'documents';
        $file_type  = 'docx';
    } elseif (in_array($extension, $allowed_videos)) {
        $sub_folder = 'videos';
        $file_type  = 'video';
    } else {
        return ['success' => false, 'message' => "Invalid file extension '.$extension'. Allowed: PDF, DOCX/DOC, Images (JPG, PNG, WEBP, GIF), Videos (MP4, WEBM).", 'data' => null];
    }

    // Target folder on GoDaddy storage
    $target_dir = UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . $sub_folder;
    if (!is_dir($target_dir)) {
        if (!@mkdir($target_dir, 0755, true)) {
            return ['success' => false, 'message' => "Unable to create upload directory '$sub_folder'. Check folder permissions.", 'data' => null];
        }
    }

    // Generate safe, unique file name
    $clean_basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig_name, PATHINFO_FILENAME));
    $clean_basename = substr($clean_basename, 0, 40);
    $unique_name    = time() . '_' . mt_rand(1000, 9999) . '_' . $clean_basename . '.' . $extension;
    $target_file    = $target_dir . DIRECTORY_SEPARATOR . $unique_name;

    // Move file to GoDaddy storage folder
    if (!move_uploaded_file($file_tmp, $target_file)) {
        return ['success' => false, 'message' => 'Failed to save file to server storage.', 'data' => null];
    }

    // Path relative to web root (e.g., uploads/pdfs/123456_notice.pdf)
    $relative_path = 'uploads/' . $sub_folder . '/' . $unique_name;
    $title = !empty(trim($title)) ? trim($title) : pathinfo($orig_name, PATHINFO_FILENAME);
    $uploaded_by = $_SESSION['tcek_admin_username'] ?? 'tcek';

    $record_id = null;

    // Store file metadata in MySQL
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO uploads (title, file_name, file_path, file_type, file_size, category, description, uploaded_by, created_at)
                VALUES (:title, :file_name, :file_path, :file_type, :file_size, :category, :description, :uploaded_by, NOW())
            ");
            $stmt->execute([
                ':title'       => $title,
                ':file_name'   => $unique_name,
                ':file_path'   => $relative_path,
                ':file_type'   => $file_type,
                ':file_size'   => $file_size,
                ':category'    => $category,
                ':description' => $description,
                ':uploaded_by' => $uploaded_by
            ]);
            $record_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("DB insert failed for uploaded file: " . $e->getMessage());
        }
    }

    return [
        'success'   => true,
        'message'   => 'File uploaded successfully!',
        'data'      => [
            'id'           => $record_id,
            'title'        => $title,
            'file_name'    => $unique_name,
            'file_path'    => $relative_path,
            'file_type'    => $file_type,
            'file_size'    => $file_size,
            'category'     => $category
        ]
    ];
}

/**
 * Delete a file from disk and remove its MySQL registry record
 * 
 * @param int $id Upload ID in MySQL
 * @return array ['success' => bool, 'message' => string]
 */
function delete_uploaded_file($id) {
    global $pdo;

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database not connected.'];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'message' => 'File record not found.'];
        }

        // Remove from disk
        $full_path = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row['file_path']);
        if (file_exists($full_path) && is_file($full_path)) {
            @unlink($full_path);
        }

        // Delete from DB
        $del = $pdo->prepare("DELETE FROM uploads WHERE id = :id");
        $del->execute([':id' => $id]);

        return ['success' => true, 'message' => 'File and record deleted successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// Handle direct POST request from Admin forms or AJAX
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_file') {
    require_admin_login();

    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $resp = ['success' => false, 'message' => 'Invalid security token (CSRF). Please reload the page.'];
    } else {
        $file = $_FILES['file'] ?? null;
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? 'general';
        $description = $_POST['description'] ?? '';

        $resp = process_file_upload($file, $title, $category, $description);
    }

    // Check if client expects JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' || isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($resp);
        exit;
    } else {
        $_SESSION['flash_type'] = $resp['success'] ? 'success' : 'danger';
        $_SESSION['flash_msg']  = $resp['message'];
        header('Location: ../admin/dashboard.php?tab=uploads');
        exit;
    }
}
