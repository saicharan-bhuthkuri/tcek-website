<?php
/**
 * Authentication Module for TCEK Admin Portal
 * Supports Users Table (Admin, Editor, Staff) with Fallback to Admins Table
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';

/**
 * Check if admin or user is currently logged in
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['tcek_admin_logged_in']) && $_SESSION['tcek_admin_logged_in'] === true;
}

/**
 * Enforce authentication; redirect to login if unauthorized
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Check if the currently logged in user is an Admin
 * @return bool
 */
function is_admin_role() {
    $role = $_SESSION['tcek_admin_role'] ?? 'admin';
    return in_array($role, ['admin', 'super_admin']);
}

/**
 * Enforce admin role for sensitive tabs like Activity Logs and User Management
 */
function require_admin_role() {
    require_admin_login();
    if (!is_admin_role()) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_msg']  = 'Access restricted. Only authorized administrators can view this section.';
        header('Location: dashboard.php?tab=overview');
        exit;
    }
}

/**
 * Get current admin/user session info
 * @return array
 */
function get_current_admin() {
    return [
        'id'        => $_SESSION['tcek_admin_id'] ?? null,
        'username'  => $_SESSION['tcek_admin_username'] ?? 'tcek',
        'full_name' => $_SESSION['tcek_admin_name'] ?? 'TCEK Administrator',
        'email'     => $_SESSION['tcek_admin_email'] ?? 'officetcek@gmail.com',
        'role'      => $_SESSION['tcek_admin_role'] ?? 'admin'
    ];
}

/**
 * Record an entry into activity_logs
 * 
 * @param string $action 'Added', 'Updated', 'Deleted', 'Uploaded', 'Login', etc.
 * @param string $module 'Events', 'Gallery', 'Notifications', 'Staff', 'Users', 'Uploads'
 * @param string $record_name Name or identifier of affected record
 * @param string $description Optional description of change
 * @param int|null $record_id
 * @param string|null $admin_name If null, uses currently logged in user
 * @return bool
 */
function log_activity($action, $module, $record_name, $description = '', $record_id = null, $admin_name = null) {
    global $pdo;

    if (!$admin_name) {
        $admin_name = $_SESSION['tcek_admin_name'] ?? ($_SESSION['tcek_admin_username'] ?? 'System');
    }

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }

    $db_ok = false;
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (admin_name, action, module, record_name, record_id, description, ip_address, created_at)
                VALUES (:admin_name, :action, :module, :record_name, :record_id, :description, :ip_address, NOW())
            ");
            $db_ok = $stmt->execute([
                ':admin_name'  => trim($admin_name),
                ':action'      => trim($action),
                ':module'      => trim($module),
                ':record_name' => trim($record_name),
                ':record_id'   => $record_id,
                ':description' => trim($description),
                ':ip_address'  => $ip_address
            ]);
        } catch (PDOException $e) {
            error_log("Failed to insert activity log: " . $e->getMessage());
        }
    }

    // Mirror to JSON store
    @include_once __DIR__ . '/crud.php';
    if (function_exists('add_json_activity_log')) {
        add_json_activity_log($action, $module, $record_name, $description, $record_id, $admin_name);
    }

    return true;
}

/**
 * Verify login against Users or Admins table
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function verify_admin_login($username, $password) {
    global $pdo;

    $username = trim($username);
    $password = trim($password);

    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Please provide both username and password.'];
    }

    $is_default_creds = (($username === 'tcek' || $username === 'Charan') && $password === 'tcek@developer');

    if ($pdo instanceof PDO) {
        try {
            // Check users table first
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // If not found in users, check admins table
            if (!$user) {
                try {
                    $stmt2 = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
                    $stmt2->execute([':username' => $username]);
                    $user = $stmt2->fetch();
                    if ($user) {
                        $user['role'] = 'admin';
                    }
                } catch (Exception $e) {
                    // ignore
                }
            }

            if ($user) {
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    $_SESSION['tcek_admin_logged_in'] = true;
                    $_SESSION['tcek_admin_id']        = $user['id'];
                    $_SESSION['tcek_admin_username']  = $user['username'];
                    $_SESSION['tcek_admin_name']      = $user['full_name'] ?? $user['username'];
                    $_SESSION['tcek_admin_email']     = $user['email'] ?? 'officetcek@gmail.com';
                    $_SESSION['tcek_admin_role']      = $user['role'] ?? 'admin';

                    log_activity('Login', 'Users', $user['username'], 'User authenticated into admin portal', $user['id'], $_SESSION['tcek_admin_name']);

                    return ['success' => true, 'message' => 'Login successful.'];
                }
            } elseif ($is_default_creds) {
                // Auto seed user
                try {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $ins = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, is_active) VALUES (:u, :p, :n, :e, 'admin', 1)");
                    $ins->execute([
                        ':u' => $username,
                        ':p' => $hash,
                        ':n' => ($username === 'Charan') ? 'Charan' : 'TCEK Administrator',
                        ':e' => 'officetcek@gmail.com'
                    ]);
                    $newId = $pdo->lastInsertId();
                    $_SESSION['tcek_admin_logged_in'] = true;
                    $_SESSION['tcek_admin_id']        = $newId;
                    $_SESSION['tcek_admin_username']  = $username;
                    $_SESSION['tcek_admin_name']      = ($username === 'Charan') ? 'Charan' : 'TCEK Administrator';
                    $_SESSION['tcek_admin_email']     = 'officetcek@gmail.com';
                    $_SESSION['tcek_admin_role']      = 'admin';

                    log_activity('Login', 'Users', $username, 'Developer seed login verified', $newId, $_SESSION['tcek_admin_name']);

                    return ['success' => true, 'message' => 'Login successful.'];
                } catch (Exception $ex) {
                    $_SESSION['tcek_admin_logged_in'] = true;
                    $_SESSION['tcek_admin_id']        = 1;
                    $_SESSION['tcek_admin_username']  = $username;
                    $_SESSION['tcek_admin_name']      = ($username === 'Charan') ? 'Charan' : 'TCEK Administrator';
                    $_SESSION['tcek_admin_role']      = 'admin';
                    return ['success' => true, 'message' => 'Login successful.'];
                }
            }
        } catch (PDOException $e) {
            if ($is_default_creds) {
                $_SESSION['tcek_admin_logged_in'] = true;
                $_SESSION['tcek_admin_id']        = 1;
                $_SESSION['tcek_admin_username']  = $username;
                $_SESSION['tcek_admin_name']      = ($username === 'Charan') ? 'Charan' : 'TCEK Administrator';
                $_SESSION['tcek_admin_role']      = 'admin';
                return ['success' => true, 'message' => 'Logged in via credentials (Tables pending setup).'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    } else {
        if ($is_default_creds) {
            $_SESSION['tcek_admin_logged_in'] = true;
            $_SESSION['tcek_admin_id']        = 1;
            $_SESSION['tcek_admin_username']  = $username;
            $_SESSION['tcek_admin_name']      = ($username === 'Charan') ? 'Charan' : 'TCEK Administrator (Offline)';
            $_SESSION['tcek_admin_role']      = 'admin';
            return ['success' => true, 'message' => 'Logged in (Database not yet connected).'];
        }
    }

    return ['success' => false, 'message' => 'Invalid username or password.'];
}

/**
 * Logout admin
 */
function admin_logout() {
    if (isset($_SESSION['tcek_admin_name'])) {
        log_activity('Logout', 'Users', $_SESSION['tcek_admin_username'] ?? 'User', 'User logged out of admin session');
    }
    unset($_SESSION['tcek_admin_logged_in']);
    unset($_SESSION['tcek_admin_id']);
    unset($_SESSION['tcek_admin_username']);
    unset($_SESSION['tcek_admin_name']);
    unset($_SESSION['tcek_admin_email']);
    unset($_SESSION['tcek_admin_role']);
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string
 */
function get_csrf_token() {
    if (empty($_SESSION['tcek_csrf_token'])) {
        $_SESSION['tcek_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['tcek_csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    return isset($_SESSION['tcek_csrf_token']) && hash_equals($_SESSION['tcek_csrf_token'], (string)$token);
}
