<?php
/**
 * Rate Limiter Engine for TCEK Portal
 * Implements:
 * 1. Auth routes: Combined per-IP & per-account rate limiting with Exponential Backoff
 * 2. Public endpoints: Moderate IP-based rate limiting
 * 3. Authenticated actions: Looser per-user/session rate limiting
 * 4. Configurable thresholds loaded from security.php / .env
 */

class RateLimiter {
    private static ?array $config = null;
    private static string $storageDir = '';

    /**
     * Initialize configuration and storage directory
     */
    private static function init(): void {
        if (self::$config === null) {
            $configFile = dirname(__DIR__) . '/config/security.php';
            self::$config = file_exists($configFile) ? require $configFile : [];
            self::$storageDir = dirname(__DIR__) . '/storage/rate_limits';

            if (!is_dir(self::$storageDir)) {
                @mkdir(self::$storageDir, 0750, true);
            }

            // Periodic garbage collection (1 in 100 chance)
            if (mt_rand(1, 100) === 1) {
                self::pruneStaleRecords();
            }
        }
    }

    /**
     * Get real client IP address safely
     */
    public static function getClientIp(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        // Respect proxy only if valid IP format
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            $forwarded = trim($forwarded);
            if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
                $ip = $forwarded;
            }
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = trim($_SERVER['HTTP_CLIENT_IP']);
            if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                $ip = $clientIp;
            }
        }
        return $ip;
    }

    /**
     * Internal atomic file read/write helper
     */
    private static function getStorageData(string $key): array {
        self::init();
        $filePath = self::$storageDir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
        if (!file_exists($filePath)) {
            return [];
        }

        $content = @file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private static function saveStorageData(string $key, array $data): bool {
        self::init();
        $filePath = self::$storageDir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);
        return (bool)@file_put_contents($filePath, $json, LOCK_EX);
    }

    private static function clearStorageData(string $key): bool {
        self::init();
        $filePath = self::$storageDir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }

    /**
     * Check if an authentication attempt is permitted.
     * Evaluates BOTH per-account and per-IP backoff.
     *
     * @param string $username Account username being targeted
     * @param string|null $ip Client IP (defaults to auto-detected)
     * @return array ['allowed' => bool, 'retry_after' => int, 'reason' => string|null]
     */
    public static function checkAuthLimit(string $username, ?string $ip = null): array {
        self::init();
        $ip = $ip ?? self::getClientIp();
        $username = strtolower(trim($username));
        $now = time();

        $authConfig = self::$config['rate_limiting']['auth'] ?? [];
        $window = (int)($authConfig['window_seconds'] ?? 900);

        // Check account backoff
        $accountKey = 'auth_acc_' . $username;
        $accData = self::getStorageData($accountKey);

        if (!empty($accData['blocked_until']) && $accData['blocked_until'] > $now) {
            $retryAfter = (int)($accData['blocked_until'] - $now);
            return [
                'allowed'     => false,
                'retry_after' => $retryAfter,
                'reason'      => 'account',
                'message'     => "Too many failed attempts for this account. Please wait {$retryAfter} seconds before trying again."
            ];
        }

        // Check IP backoff
        $ipKey = 'auth_ip_' . $ip;
        $ipData = self::getStorageData($ipKey);

        if (!empty($ipData['blocked_until']) && $ipData['blocked_until'] > $now) {
            $retryAfter = (int)($ipData['blocked_until'] - $now);
            return [
                'allowed'     => false,
                'retry_after' => $retryAfter,
                'reason'      => 'ip',
                'message'     => "Too many failed login attempts from your IP address. Please wait {$retryAfter} seconds before trying again."
            ];
        }

        return ['allowed' => true, 'retry_after' => 0, 'reason' => null];
    }

    /**
     * Record a failed authentication attempt with Exponential Backoff
     *
     * @param string $username
     * @param string|null $ip
     */
    public static function recordAuthFailure(string $username, ?string $ip = null): array {
        self::init();
        $ip = $ip ?? self::getClientIp();
        $username = strtolower(trim($username));
        $now = time();

        $authConfig     = self::$config['rate_limiting']['auth'] ?? [];
        $maxAttempts    = (int)($authConfig['max_attempts'] ?? 5);
        $window         = (int)($authConfig['window_seconds'] ?? 900);
        $baseDelay      = (int)($authConfig['backoff_base_sec'] ?? 2);
        $factor         = (int)($authConfig['backoff_factor'] ?? 2);
        $maxBackoff     = (int)($authConfig['max_backoff_sec'] ?? 900);
        $ipMaxAttempts  = (int)($authConfig['ip_max_attempts'] ?? 15);

        // Update Account records
        $accountKey = 'auth_acc_' . $username;
        $accData = self::getStorageData($accountKey);

        if (empty($accData) || ($now - ($accData['first_attempt'] ?? $now)) > $window) {
            $accData = [
                'attempts'      => 1,
                'first_attempt' => $now,
                'last_attempt'  => $now,
                'blocked_until' => 0
            ];
        } else {
            $accData['attempts'] = (int)($accData['attempts'] ?? 0) + 1;
            $accData['last_attempt'] = $now;
        }

        // Calculate exponential backoff if threshold exceeded
        if ($accData['attempts'] >= $maxAttempts) {
            $exponent = max(0, $accData['attempts'] - $maxAttempts);
            $delay = min($maxBackoff, (int)($baseDelay * pow($factor, $exponent)));
            $accData['blocked_until'] = $now + $delay;
        }
        self::saveStorageData($accountKey, $accData);

        // Update IP records
        $ipKey = 'auth_ip_' . $ip;
        $ipData = self::getStorageData($ipKey);

        if (empty($ipData) || ($now - ($ipData['first_attempt'] ?? $now)) > $window) {
            $ipData = [
                'attempts'      => 1,
                'first_attempt' => $now,
                'last_attempt'  => $now,
                'blocked_until' => 0
            ];
        } else {
            $ipData['attempts'] = (int)($ipData['attempts'] ?? 0) + 1;
            $ipData['last_attempt'] = $now;
        }

        if ($ipData['attempts'] >= $ipMaxAttempts) {
            $exponent = max(0, $ipData['attempts'] - $ipMaxAttempts);
            $delay = min($maxBackoff, (int)($baseDelay * pow($factor, $exponent)));
            $ipData['blocked_until'] = $now + $delay;
        }
        self::saveStorageData($ipKey, $ipData);

        $accBlocked = ($accData['blocked_until'] ?? 0) > $now;
        $retryAfter = $accBlocked ? ($accData['blocked_until'] - $now) : 0;

        return [
            'attempts'    => $accData['attempts'],
            'blocked'     => $accBlocked,
            'retry_after' => $retryAfter
        ];
    }

    /**
     * Reset rate limit records on successful authentication
     *
     * @param string $username
     * @param string|null $ip
     */
    public static function resetAuthLimits(string $username, ?string $ip = null): void {
        self::init();
        $ip = $ip ?? self::getClientIp();
        $username = strtolower(trim($username));

        self::clearStorageData('auth_acc_' . $username);
        self::clearStorageData('auth_ip_' . $ip);
    }

    /**
     * Check rate limit for moderate public endpoints
     *
     * @param string|null $ip
     * @return array ['allowed' => bool, 'retry_after' => int, 'remaining' => int]
     */
    public static function checkPublicLimit(?string $ip = null): array {
        self::init();
        $ip = $ip ?? self::getClientIp();
        $now = time();

        $pubConfig = self::$config['rate_limiting']['public'] ?? [];
        $maxRequests = (int)($pubConfig['max_requests'] ?? 100);
        $window = (int)($pubConfig['window_seconds'] ?? 60);

        $key = 'pub_ip_' . $ip;
        $data = self::getStorageData($key);

        if (empty($data) || ($now - ($data['start_time'] ?? $now)) > $window) {
            $data = [
                'count'      => 1,
                'start_time' => $now
            ];
            self::saveStorageData($key, $data);
            return ['allowed' => true, 'retry_after' => 0, 'remaining' => $maxRequests - 1];
        }

        $data['count'] = (int)($data['count'] ?? 0) + 1;
        self::saveStorageData($key, $data);

        if ($data['count'] > $maxRequests) {
            $retryAfter = max(1, $window - ($now - $data['start_time']));
            return [
                'allowed'     => false,
                'retry_after' => $retryAfter,
                'remaining'   => 0
            ];
        }

        return [
            'allowed'     => true,
            'retry_after' => 0,
            'remaining'   => max(0, $maxRequests - $data['count'])
        ];
    }

    /**
     * Check looser rate limit for authenticated user operations
     *
     * @param string|int|null $userId User ID or Session identifier
     * @return array ['allowed' => bool, 'retry_after' => int, 'remaining' => int]
     */
    public static function checkAuthenticatedLimit($userId = null): array {
        self::init();
        $now = time();

        if ($userId === null) {
            $userId = $_SESSION['tcek_admin_id'] ?? session_id() ?? self::getClientIp();
        }

        $authConfig = self::$config['rate_limiting']['authenticated'] ?? [];
        $maxRequests = (int)($authConfig['max_requests'] ?? 300);
        $window = (int)($authConfig['window_seconds'] ?? 60);

        $key = 'auth_user_' . $userId;
        $data = self::getStorageData($key);

        if (empty($data) || ($now - ($data['start_time'] ?? $now)) > $window) {
            $data = [
                'count'      => 1,
                'start_time' => $now
            ];
            self::saveStorageData($key, $data);
            return ['allowed' => true, 'retry_after' => 0, 'remaining' => $maxRequests - 1];
        }

        $data['count'] = (int)($data['count'] ?? 0) + 1;
        self::saveStorageData($key, $data);

        if ($data['count'] > $maxRequests) {
            $retryAfter = max(1, $window - ($now - $data['start_time']));
            return [
                'allowed'     => false,
                'retry_after' => $retryAfter,
                'remaining'   => 0
            ];
        }

        return [
            'allowed'     => true,
            'retry_after' => 0,
            'remaining'   => max(0, $maxRequests - $data['count'])
        ];
    }

    /**
     * Prune files older than 24 hours
     */
    private static function pruneStaleRecords(): void {
        if (!is_dir(self::$storageDir)) return;
        $files = @glob(self::$storageDir . DIRECTORY_SEPARATOR . '*.json');
        if (!$files) return;

        $threshold = time() - 86400; // 24 hours
        foreach ($files as $file) {
            if (@filemtime($file) < $threshold) {
                @unlink($file);
            }
        }
    }
}
