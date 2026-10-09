<?php
/**
 * Environment Configuration Loader for TCEK
 * Safely loads key-value pairs from .env file outside or inside web root,
 * falls back to getenv() and system $_ENV/$_SERVER variables, with default fallbacks.
 */

if (!function_exists('load_env_file')) {
    function load_env_file($path = null) {
        static $loaded = false;
        if ($loaded && $path === null) {
            return;
        }

        // Search candidate paths if none provided
        if ($path === null) {
            $candidates = [
                dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.env',        // Workspace root
                dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env',        // public/
                dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env',           // backend/
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . '.env'
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_readable($cand)) {
                    $path = $cand;
                    break;
                }
            }
        }

        if (!$path || !file_exists($path) || !is_readable($path)) {
            $loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            $loaded = true;
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments and empty lines
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                continue;
            }

            // Split on the first '='
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $val = trim($parts[1]);

            // Strip surrounding quotes
            if (
                (str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))
            ) {
                $val = substr($val, 1, -1);
            }

            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }

        $loaded = true;
    }
}

// Auto-load on include
load_env_file();

if (!function_exists('env')) {
    /**
     * Retrieve environment variable with default fallback
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null) {
        $val = getenv($key);
        if ($val === false) {
            $val = $_ENV[$key] ?? ($_SERVER[$key] ?? null);
        }

        if ($val === null || $val === false) {
            return $default;
        }

        // Type conversions
        $lower = strtolower(trim((string)$val));
        if ($lower === 'true' || $lower === '(true)') return true;
        if ($lower === 'false' || $lower === '(false)') return false;
        if ($lower === 'empty' || $lower === '(empty)' || $lower === 'null') return null;

        return $val;
    }
}
