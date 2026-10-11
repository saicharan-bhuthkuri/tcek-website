<?php
/**
 * Dedicated System Settings API Endpoint
 * Provides JSON API access for Global Settings
 * 
 * STRICT ACCESS RESTRICTION:
 * - Only the authorized Developer account (saivortex.dev@gmail.com) can access or invoke this API
 * - Rejects any other role (Superadmin, Admin, Staff, or Guests) with HTTP 403 Forbidden
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security/RateLimiter.php';
require_once __DIR__ . '/settings_crud.php';

// 1. Enforce strict developer access
if (!is_admin_logged_in() || !is_authorized_developer()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'status'  => 403,
        'error'   => 'Forbidden',
        'message' => 'HTTP 403 Forbidden: Only the authorized Developer account (saivortex.dev@gmail.com) can access or modify Global 404 Error Page Settings.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$action = trim($_POST['action'] ?? ($_GET['action'] ?? ''));

// 2. CSRF Token verification for POST mutations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'status'  => 403,
            'error'   => 'Invalid CSRF Token',
            'message' => 'Security token mismatch. Please reload the page.'
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }
}

// 3. API Actions
switch ($action) {
    case 'get_404_status':
        $settings = load_system_settings();
        echo json_encode([
            'success'     => true,
            'is_enabled'  => !empty($settings['global_404_enabled']),
            'updated_at'  => $settings['updated_at'] ?? '',
            'updated_by'  => $settings['updated_by'] ?? '',
            'custom_msg'  => $settings['global_404_custom_message'] ?? ''
        ], JSON_UNESCAPED_SLASHES);
        exit;

    case 'toggle_404_status':
    case 'toggle_global_404':
        $current = get_global_404_status();
        $new = $current ? 0 : 1;
        $admin = get_current_admin();
        $res = update_global_404_setting($new, $admin['email'] ?? 'saivortex.dev@gmail.com');
        http_response_code($res['status'] ?? 200);
        echo json_encode($res, JSON_UNESCAPED_SLASHES);
        exit;

    case 'update_404_setting':
    case 'save_global_404':
        $enabled = (!empty($_POST['is_enabled']) && $_POST['is_enabled'] !== '0') ? 1 : 0;
        $admin = get_current_admin();
        $res = update_global_404_setting($enabled, $admin['email'] ?? 'saivortex.dev@gmail.com');
        http_response_code($res['status'] ?? 200);
        echo json_encode($res, JSON_UNESCAPED_SLASHES);
        exit;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'status'  => 400,
            'message' => 'Unknown action requested.'
        ], JSON_UNESCAPED_SLASHES);
        exit;
}
