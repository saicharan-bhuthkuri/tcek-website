<?php
/**
 * Secure Error Handler & Logger for TCEK Portal
 * Enforces:
 * - Hiding internal stack traces, database errors, and filesystem paths from end-users
 * - Centralized logging of full diagnostic details with unique reference IDs
 * - Redaction of sensitive fields (passwords, tokens, database credentials)
 * - Safe generic error responses for both HTML and JSON requests
 */

require_once __DIR__ . '/../config/env.php';

class ErrorHandler {
    private static string $logDir = '';
    private static bool $isRegistered = false;

    /**
     * Initialize error reporting policies and custom handlers
     */
    public static function init(): void {
        if (self::$isRegistered) {
            return;
        }

        self::$logDir = dirname(__DIR__) . '/storage/logs';
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0750, true);
        }

        $isDebug = (bool)env('APP_DEBUG', false);

        // Turn off public error display in production
        if (!$isDebug) {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        } else {
            ini_set('display_errors', '1');
        }

        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        // Register custom exception & shutdown handlers
        set_exception_handler([self::class, 'handleUncaughtException']);
        set_error_handler([self::class, 'handlePhpError']);

        self::$isRegistered = true;
    }

    /**
     * Generate a unique error tracking reference ID (e.g., ERR-A1B2C3D4)
     */
    public static function generateRefId(): string {
        return 'ERR-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }

    /**
     * Redact sensitive values from log strings and traces
     */
    public static function sanitizeLogText(string $text): string {
        $patterns = [
            '/password=([^&\s]+)/i'              => 'password=[REDACTED]',
            '/"password"\s*:\s*"[^"]+"/i'        => '"password":"[REDACTED]"',
            '/\'password\'\s*=>\s*\'[^\']+\'/i'  => '\'password\' => \'[REDACTED]\'',
            '/DB_PASS=([^\r\n]+)/i'              => 'DB_PASS=[REDACTED]',
            '/csrf_token=([^&\s]+)/i'            => 'csrf_token=[REDACTED]',
        ];
        return preg_replace(array_keys($patterns), array_values($patterns), $text);
    }

    /**
     * Log an error securely to server log file
     */
    public static function log(string $level, string $message, ?Throwable $exception = null, string $refId = ''): void {
        self::init();
        $refId = $refId ?: self::generateRefId();
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $uri = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $entry = "[{$timestamp}] [{$level}] [{$refId}] [IP: {$ip}] [URI: {$uri}]\n";
        $entry .= "Message: {$message}\n";

        if ($exception !== null) {
            $entry .= "Exception: " . get_class($exception) . " in " . $exception->getFile() . ":" . $exception->getLine() . "\n";
            $entry .= "Detail: " . $exception->getMessage() . "\n";
            $entry .= "Stack Trace:\n" . $exception->getTraceAsString() . "\n";
        }
        $entry .= str_repeat('-', 80) . "\n";

        $cleanEntry = self::sanitizeLogText($entry);

        // Write to local protected log file
        $logFile = self::$logDir . '/app_' . date('Y-m-d') . '.log';
        @file_put_contents($logFile, $cleanEntry, FILE_APPEND | LOCK_EX);

        // Also push to standard PHP error log
        error_log("[{$refId}] {$message}");
    }

    /**
     * Generate a safe, non-leaking user-facing error response array
     *
     * @param Throwable|null $e Caught exception
     * @param string $userMessage Friendly user-facing message
     * @return array
     */
    public static function safeError(?Throwable $e, string $userMessage = 'An unexpected error occurred. Please try again later.'): array {
        $refId = self::generateRefId();
        $detail = $e ? $e->getMessage() : $userMessage;

        self::log('ERROR', "Handled Application Error: {$detail}", $e, $refId);

        $response = [
            'success'   => false,
            'message'   => $userMessage,
            'error_ref' => $refId
        ];

        // In debug mode only, provide internal detail for developers
        if (env('APP_DEBUG', false) && $e) {
            $response['debug_detail'] = $e->getMessage();
            $response['debug_file']   = $e->getFile() . ':' . $e->getLine();
        }

        return $response;
    }

    /**
     * Uncaught exception handler
     */
    public static function handleUncaughtException(Throwable $e): void {
        $refId = self::generateRefId();
        self::log('CRITICAL', "Uncaught Exception: " . $e->getMessage(), $e, $refId);

        if (!headers_sent()) {
            http_response_code(500);
        }

        // Check if JSON request
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                  (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'   => false,
                'message'   => 'An unexpected internal server error occurred. Our team has been notified.',
                'error_ref' => $refId
            ]);
            exit;
        }

        // Clean user-friendly HTML error page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Service Unavailable - Trinity College</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
                .error-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 40px; max-width: 520px; width: 100%; text-align: center; }
                h1 { font-size: 22px; color: #dc2626; margin-bottom: 12px; }
                p { color: #64748b; font-size: 14.5px; line-height: 1.6; margin-bottom: 20px; }
                .ref-badge { display: inline-block; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; font-family: monospace; font-size: 13px; color: #334155; margin-bottom: 24px; }
                .btn { display: inline-block; background: #00b894; color: #ffffff; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; }
                .btn:hover { background: #00a884; }
            </style>
        </head>
        <body>
            <div class="error-card">
                <h1>Service Notice</h1>
                <p>We are temporarily unable to complete your request. Our technical staff has logged the incident for review.</p>
                <div class="ref-badge">Incident Ref: <?php echo htmlspecialchars($refId); ?></div>
                <div><a href="/index.php" class="btn">Return to Homepage</a></div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * PHP Error to ErrorException converter
     */
    public static function handlePhpError(int $errno, string $errstr, string $errfile, int $errline): bool {
        // Respect error_reporting settings
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $message = "PHP Notice/Warning [{$errno}]: {$errstr} in {$errfile}:{$errline}";
        self::log('WARNING', $message);

        // Convert fatal-level errors into exceptions
        if ($errno === E_USER_ERROR || $errno === E_RECOVERABLE_ERROR) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        return true;
    }
}

// Automatically initialize ErrorHandler
ErrorHandler::init();
