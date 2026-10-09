<?php
/**
 * Hardened File Upload Handler for Trinity College of Engineering & Technology
 * Enforces:
 * - Strict input validation against explicit schema
 * - MIME type and Magic-Byte content inspection (PHP fileinfo, getimagesize, header checks)
 * - Extension allowlist & blocking of double extensions / hidden scripts
 * - Configurable file size limits per category
 * - Isolated, randomized server filenames (preventing path traversal)
 * - Safe error handling (no path or DB leaks)
 * - Rate limiting on upload attempts
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security/RateLimiter.php';
require_once __DIR__ . '/security/Validator.php';
require_once __DIR__ . '/security/ErrorHandler.php';

// Web root directory for uploads
define('UPLOAD_BASE_DIR', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads');

/**
 * Handle file upload with comprehensive validation and content inspection
 * 
 * @param array $file $_FILES['input_name']
 * @param string $title Friendly title/label
 * @param string $category Category (e.g. 'notice', 'circular', 'gallery', 'event')
 * @param string $description Optional description
 * @return array ['success' => bool, 'message' => string, 'data' => array|null]
 */
function process_file_upload($file, $title = '', $category = 'general', $description = ''): array {
    global $pdo;

    // 1. Rate Limiting Check on Uploads
    $rateCheck = RateLimiter::checkAuthenticatedLimit();
    if (!$rateCheck['allowed']) {
        return [
            'success' => false,
            'message' => "Too many upload requests. Please wait {$rateCheck['retry_after']} seconds.",
            'data'    => null
        ];
    }

    // 2. Validate Metadata Inputs against Strict Schema
    $metaValidation = Validator::validate(
        ['title' => $title, 'category' => $category, 'description' => $description],
        [
            'title'       => ['type' => 'string', 'required' => false, 'max_len' => 255, 'default' => ''],
            'category'    => ['type' => 'string', 'required' => false, 'max_len' => 100, 'default' => 'general'],
            'description' => ['type' => 'string', 'required' => false, 'max_len' => 3000, 'default' => ''],
        ]
    );

    if (!$metaValidation['valid']) {
        $firstErr = reset($metaValidation['errors']);
        return ['success' => false, 'message' => "Input validation failed: {$firstErr}", 'data' => null];
    }

    $title       = $metaValidation['data']['title'];
    $category    = $metaValidation['data']['category'];
    $description = $metaValidation['data']['description'];

    // 3. Verify Basic Upload Status
    if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $error_codes = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server maximum upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form MAX_FILE_SIZE limit.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected for upload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write upload to server disk.',
            UPLOAD_ERR_EXTENSION  => 'A server extension blocked the file upload.'
        ];
        $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $msg = $error_codes[$errorCode] ?? 'An error occurred during file upload.';
        return ['success' => false, 'message' => $msg, 'data' => null];
    }

    $orig_name = basename((string)$file['name']);
    $file_size = (int)$file['size'];
    $file_tmp  = (string)$file['tmp_name'];

    // Load security settings
    $secConfig = file_exists(__DIR__ . '/config/security.php') ? require __DIR__ . '/config/security.php' : [];
    $disallowedExts = $secConfig['uploads']['disallowed_extensions'] ?? [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'pht', 'phar',
        'inc', 'exe', 'bat', 'cmd', 'sh', 'bash', 'py', 'pl', 'cgi', 'js', 'html', 'htm'
    ];

    // 4. Double-Extension & Dangerous Name Check (performed first)
    $nameParts = explode('.', strtolower($orig_name));
    foreach ($nameParts as $part) {
        if (in_array(trim($part), $disallowedExts, true)) {
            ErrorHandler::log('SECURITY', "Rejected upload containing dangerous extension token: {$orig_name}");
            return ['success' => false, 'message' => 'Security Error: Executable or script files are strictly prohibited.', 'data' => null];
        }
    }

    if (!is_uploaded_file($file_tmp) && php_sapi_name() !== 'cli') {
        ErrorHandler::log('SECURITY', "Possible file upload forgery attempt detected for file: {$orig_name}");
        return ['success' => false, 'message' => 'Invalid upload source.', 'data' => null];
    }

    $extension = end($nameParts);

    // 5. Categorize and Validate Extension Allowlist
    $allowed_images = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowed_pdfs   = ['pdf'];
    $allowed_docs   = ['docx', 'doc'];
    $allowed_videos = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

    $sub_folder = '';
    $file_type  = '';

    if (in_array($extension, $allowed_images, true)) {
        $sub_folder = 'images';
        $file_type  = 'image';
    } elseif (in_array($extension, $allowed_pdfs, true)) {
        $sub_folder = 'pdfs';
        $file_type  = 'pdf';
    } elseif (in_array($extension, $allowed_docs, true)) {
        $sub_folder = 'documents';
        $file_type  = 'docx';
    } elseif (in_array($extension, $allowed_videos, true)) {
        $sub_folder = 'videos';
        $file_type  = 'video';
    } else {
        return [
            'success' => false,
            'message' => "Unsupported file extension '.{$extension}'. Allowed formats: PDF, DOCX, JPG, PNG, WEBP, GIF, MP4, WEBM.",
            'data'    => null
        ];
    }

    // 6. Enforce Configurable Category Size Limits
    $maxSize = $secConfig['uploads']['max_sizes'][$file_type] ?? (25 * 1024 * 1024);
    if ($file_size > $maxSize) {
        $maxMB = round($maxSize / (1024 * 1024), 1);
        return [
            'success' => false,
            'message' => "File size exceeds the maximum allowed limit of {$maxMB}MB for {$file_type} files.",
            'data'    => null
        ];
    }

    // 7. Server-Side Content Inspection (MIME type verification via PHP fileinfo OOP)
    $detectedMime = false;
    if (class_exists('finfo')) {
        $finfoObj = new finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfoObj->file($file_tmp);
    } elseif (function_exists('mime_content_type')) {
        $detectedMime = mime_content_type($file_tmp);
    }

    if (!$detectedMime) {
        return ['success' => false, 'message' => 'Unable to determine the file content type.', 'data' => null];
    }

    $allowedMimes = $secConfig['uploads']['allowed_mimes'][$file_type] ?? [];
    if (!array_key_exists($detectedMime, $allowedMimes)) {
        ErrorHandler::log('SECURITY', "MIME mismatch: File {$orig_name} claimed {$extension} but inspected as {$detectedMime}");
        return [
            'success' => false,
            'message' => 'Security Error: File content does not match its claimed file type.',
            'data'    => null
        ];
    }

    // 8. Deep Magic-Byte / Structure Content Validation
    if ($file_type === 'image') {
        // Verify valid image header and dimensions using getimagesize
        $imgSize = @getimagesize($file_tmp);
        if ($imgSize === false || $imgSize[0] <= 0 || $imgSize[1] <= 0) {
            ErrorHandler::log('SECURITY', "Corrupted or spoofed image upload attempt: {$orig_name}");
            return ['success' => false, 'message' => 'Security Error: Uploaded image file is invalid or corrupted.', 'data' => null];
        }
    } elseif ($file_type === 'pdf') {
        // PDF Magic Bytes: First 5 bytes MUST be %PDF-
        $pdfHeader = @file_get_contents($file_tmp, false, null, 0, 5);
        if ($pdfHeader !== '%PDF-') {
            ErrorHandler::log('SECURITY', "Spoofed PDF upload attempt: {$orig_name}");
            return ['success' => false, 'message' => 'Security Error: Uploaded PDF is invalid or malformed.', 'data' => null];
        }
    } elseif ($file_type === 'docx') {
        // DOCX is a PK ZIP container: First 4 bytes MUST be PK\x03\x04
        $zipHeader = @file_get_contents($file_tmp, false, null, 0, 4);
        if ($zipHeader !== "\x50\x4B\x03\x04" && $extension === 'docx') {
            ErrorHandler::log('SECURITY', "Spoofed DOCX upload attempt: {$orig_name}");
            return ['success' => false, 'message' => 'Security Error: Uploaded document is invalid or malformed.', 'data' => null];
        }
    }

    // 9. Prepare Target Storage Directory
    $target_dir = UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . $sub_folder;
    if (!is_dir($target_dir)) {
        if (!@mkdir($target_dir, 0755, true)) {
            ErrorHandler::log('ERROR', "Unable to create upload directory: {$target_dir}");
            return ['success' => false, 'message' => 'Storage directory is unavailable. Please check server permissions.', 'data' => null];
        }
    }

    // 10. Generate Isolated, Cryptographically Random Filename (Prevent Path Traversal)
    $randomHex    = bin2hex(random_bytes(16));
    $unique_name  = time() . '_' . $randomHex . '.' . $extension;
    $target_file  = $target_dir . DIRECTORY_SEPARATOR . $unique_name;

    // Move file to storage
    if (!move_uploaded_file($file_tmp, $target_file)) {
        ErrorHandler::log('ERROR', "Failed to move uploaded file {$file_tmp} to {$target_file}");
        return ['success' => false, 'message' => 'Failed to store file on server.', 'data' => null];
    }

    // Set safe permissions on uploaded file (read-only for web server, not executable)
    @chmod($target_file, 0644);

    $relative_path = 'uploads/' . $sub_folder . '/' . $unique_name;
    $displayTitle = !empty($title) ? $title : pathinfo($orig_name, PATHINFO_FILENAME);
    $uploaded_by = $_SESSION['tcek_admin_username'] ?? 'tcek';

    $record_id = null;

    // 11. Store file metadata in MySQL
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO uploads (title, file_name, file_path, file_type, file_size, category, description, uploaded_by, created_at)
                VALUES (:title, :file_name, :file_path, :file_type, :file_size, :category, :description, :uploaded_by, NOW())
            ");
            $stmt->execute([
                ':title'       => $displayTitle,
                ':file_name'   => $unique_name,
                ':file_path'   => $relative_path,
                ':file_type'   => $file_type,
                ':file_size'   => $file_size,
                ':category'    => $category,
                ':description' => $description,
                ':uploaded_by' => $uploaded_by
            ]);
            $record_id = (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            ErrorHandler::log('ERROR', 'DB insert failed for uploaded file record', $e);
        }
    }

    log_activity('Uploaded', 'Uploads', $displayTitle, "Uploaded {$file_type} file", $record_id);

    return [
        'success' => true,
        'message' => 'File uploaded and verified successfully!',
        'data'    => [
            'id'        => $record_id,
            'title'     => $displayTitle,
            'file_name' => $unique_name,
            'file_path' => $relative_path,
            'file_type' => $file_type,
            'file_size' => $file_size,
            'category'  => $category
        ]
    ];
}

/**
 * Delete a file safely from disk and remove its MySQL registry record
 * 
 * @param int $id Upload ID in MySQL
 * @return array ['success' => bool, 'message' => string]
 */
function delete_uploaded_file($id): array {
    global $pdo;

    $id = (int)$id;
    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid file ID specified.'];
    }

    if (!($pdo instanceof PDO)) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'message' => 'File record not found.'];
        }

        // Prevent path traversal by strictly validating relative path
        $cleanRelPath = str_replace(['..', '\\'], ['', '/'], $row['file_path']);
        $full_path = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanRelPath);

        // Verify the resolved path is strictly within the uploads directory
        $uploadsBase = realpath(UPLOAD_BASE_DIR);
        if ($uploadsBase && str_starts_with(realpath(dirname($full_path)) ?: '', $uploadsBase)) {
            if (file_exists($full_path) && is_file($full_path)) {
                @unlink($full_path);
            }
        }

        // Delete from DB
        $del = $pdo->prepare("DELETE FROM uploads WHERE id = :id");
        $del->execute([':id' => $id]);

        return ['success' => true, 'message' => 'File record deleted successfully.'];
    } catch (PDOException $e) {
        return ErrorHandler::safeError($e, 'Unable to delete the requested file at this time.');
    }
}

// Handle direct POST request from Admin forms or AJAX
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_file') {
    require_admin_login();

    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $resp = ['success' => false, 'message' => 'Invalid security token (CSRF mismatch). Please reload the page.'];
        http_response_code(403);
    } else {
        $file        = $_FILES['file'] ?? null;
        $title       = $_POST['title'] ?? '';
        $category    = $_POST['category'] ?? 'general';
        $description = $_POST['description'] ?? '';

        $resp = process_file_upload($file, $title, $category, $description);
    }

    // Return JSON if AJAX requested
    if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_POST['ajax'])) {
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
