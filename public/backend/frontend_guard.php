<?php
/**
 * Global Frontend 404 Mode Guard & Middleware
 * Trinity College of Engineering & Technology
 * 
 * Central Server-Side Policy:
 * - When Global 404 Mode is ON (1):
 *     * Blocks access to every public frontend page (Homepage, About, Departments, Events, etc.)
 *     * Returns genuine HTTP 404 Not Found status code
 *     * Renders the custom Trinity College 404 Error page
 *     * Halts execution immediately (exit) so no page content can leak
 * - When Global 404 Mode is OFF (0):
 *     * Normal access to all valid frontend pages is restored immediately
 * - Strictly preserves access for:
 *     * /admin/* (Admin Console, Login, Logout)
 *     * /backend/* (APIs, Authentication, Settings, CRUD)
 *     * Static assets (CSS, JS, Images, Uploads) needed by 404 page & admin
 *     * /404.php (Direct error document execution)
 */

if (!function_exists('is_frontend_guard_excluded_request')) {
    /**
     * Check if the current request should be exempt from 404 mode
     */
    function is_frontend_guard_excluded_request(): bool {
        // CLI execution is exempt (unit tests, cron, etc.)
        if (php_sapi_name() === 'cli') {
            return true;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH) ?? $uri;
        $path = str_replace('\\', '/', $path);
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

        // Prevent recursion if 404 page itself is rendering
        if (defined('FRONTEND_GUARD_ACTIVE') || basename($path) === '404.php' || basename($script) === '404.php') {
            return true;
        }

        // Exempt Admin Console (Login, Dashboard, Admin views)
        if (str_contains($path, '/admin/') || str_contains($script, '/admin/')) {
            return true;
        }

        // Exempt Backend endpoints (APIs, CRUD, Auth, Sync, Settings)
        if (str_contains($path, '/backend/') || str_contains($script, '/backend/')) {
            return true;
        }

        // Exempt static asset files needed by 404 page & browser
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $assetExtensions = [
            'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 
            'ico', 'woff', 'woff2', 'ttf', 'eot', 'mp4', 'webm', 'pdf', 'docx', 'map'
        ];
        if (in_array($ext, $assetExtensions, true)) {
            return true;
        }

        if (
            str_contains($path, '/assets/') ||
            str_contains($path, '/css/') ||
            str_contains($path, '/js/') ||
            str_contains($path, '/uploads/')
        ) {
            return true;
        }

        return false;
    }
}

if (!function_exists('check_and_enforce_frontend_404_mode')) {
    /**
     * Central enforcement function
     * Discards any buffered output, sets HTTP 404, renders 404.php, and terminates.
     */
    function check_and_enforce_frontend_404_mode(): void {
        if (is_frontend_guard_excluded_request()) {
            return;
        }

        require_once __DIR__ . '/settings_crud.php';

        if (get_global_404_status()) {
            // Discard any output buffered before this middleware
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            if (!headers_sent()) {
                http_response_code(404);
            }

            define('FRONTEND_GUARD_ACTIVE', true);

            $errorPage = dirname(__DIR__) . '/404.php';
            if (file_exists($errorPage)) {
                include $errorPage;
            } else {
                header('Content-Type: text/html; charset=utf-8');
                echo '<!DOCTYPE html><html lang="en"><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>';
            }
            exit;
        }
    }
}
