<?php
/**
 * Strict Input Schema Validator for TCEK Portal
 * Validates inputs against strict type, length, and format schemas.
 * Rejects any non-conforming input immediately instead of just sanitizing.
 */

class Validator {
    /**
     * Safe multibyte/ascii string length helper
     */
    private static function strLen(string $str): int {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'UTF-8');
        }
        return strlen($str);
    }

    /**
     * Validate an associative input array against a strict schema definition.
     *
     * @param array $input Raw input data (e.g., $_POST)
     * @param array $schema Schema definition with rules
     * @return array ['valid' => bool, 'errors' => array, 'data' => array]
     */
    public static function validate(array $input, array $schema): array {
        $errors = [];
        $validatedData = [];

        foreach ($schema as $field => $rules) {
            $exists = array_key_exists($field, $input);
            $rawVal = $exists ? $input[$field] : null;

            // Trim strings by default
            $val = is_string($rawVal) ? trim($rawVal) : $rawVal;

            $isRequired = !empty($rules['required']);
            $label = $rules['label'] ?? ucfirst(str_replace('_', ' ', $field));

            // Check required
            if ($isRequired && ($val === null || $val === '')) {
                $errors[$field] = "{$label} is required and cannot be empty.";
                continue;
            }

            // If field is optional and not provided or empty, set default if provided
            if (!$isRequired && ($val === null || $val === '')) {
                if (array_key_exists('default', $rules)) {
                    $validatedData[$field] = $rules['default'];
                } else {
                    $validatedData[$field] = null;
                }
                continue;
            }

            $type = $rules['type'] ?? 'string';

            // 1. Type & Format validation
            switch ($type) {
                case 'int':
                case 'integer':
                    if (!is_numeric($val) || (string)(int)$val !== (string)$val) {
                        $errors[$field] = "{$label} must be a valid integer.";
                        continue 2;
                    }
                    $intVal = (int)$val;
                    if (isset($rules['min']) && $intVal < $rules['min']) {
                        $errors[$field] = "{$label} must be at least {$rules['min']}.";
                        continue 2;
                    }
                    if (isset($rules['max']) && $intVal > $rules['max']) {
                        $errors[$field] = "{$label} must be no greater than {$rules['max']}.";
                        continue 2;
                    }
                    $validatedData[$field] = $intVal;
                    break;

                case 'string':
                    if (!is_string($val)) {
                        $errors[$field] = "{$label} must be a string.";
                        continue 2;
                    }
                    $len = self::strLen($val);
                    if (isset($rules['min_len']) && $len < $rules['min_len']) {
                        $errors[$field] = "{$label} must be at least {$rules['min_len']} characters long.";
                        continue 2;
                    }
                    if (isset($rules['max_len']) && $len > $rules['max_len']) {
                        $errors[$field] = "{$label} must not exceed {$rules['max_len']} characters.";
                        continue 2;
                    }
                    if (isset($rules['regex']) && !preg_match($rules['regex'], $val)) {
                        $errors[$field] = "{$label} contains an invalid format.";
                        continue 2;
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'username_or_email':
                case 'username':
                    if (!is_string($val)) {
                        $errors[$field] = "{$label} must be a valid text string.";
                        continue 2;
                    }
                    if (strpos($val, '@') !== false) {
                        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "{$label} must be a valid email address.";
                            continue 2;
                        }
                        if (self::strLen($val) > 100) {
                            $errors[$field] = "{$label} must not exceed 100 characters.";
                            continue 2;
                        }
                    } else {
                        if (!preg_match('/^[a-zA-Z0-9_.@-]{3,50}$/', $val)) {
                            $errors[$field] = "{$label} must be 3-50 characters long and can only contain letters, numbers, dots, underscores, and hyphens.";
                            continue 2;
                        }
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'email':
                    if (!is_string($val) || !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "{$label} must be a valid email address.";
                        continue 2;
                    }
                    if (self::strLen($val) > 100) {
                        $errors[$field] = "{$label} must not exceed 100 characters.";
                        continue 2;
                    }
                    $validatedData[$field] = strtolower($val);
                    break;

                case 'date':
                    if (!is_string($val) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                        $errors[$field] = "{$label} must match the date format YYYY-MM-DD.";
                        continue 2;
                    }
                    [$year, $month, $day] = explode('-', $val);
                    if (!checkdate((int)$month, (int)$day, (int)$year)) {
                        $errors[$field] = "{$label} is an invalid calendar date.";
                        continue 2;
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'time':
                    if (!is_string($val) || !preg_match('/^(0?[1-9]|1[0-2]):[0-5][0-9]\s?(AM|PM|am|pm)$|^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $val)) {
                        $errors[$field] = "{$label} must be a valid time (e.g., 10:00 AM or 14:30).";
                        continue 2;
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'url':
                case 'url_or_path':
                    if (!is_string($val)) {
                        $errors[$field] = "{$label} must be a valid link.";
                        continue 2;
                    }
                    if (self::strLen($val) > 255) {
                        $errors[$field] = "{$label} must not exceed 255 characters.";
                        continue 2;
                    }
                    // Reject dangerous pseudo-protocols like javascript: or data:
                    if (preg_match('/^\s*(javascript|data|vbscript):/i', $val)) {
                        $errors[$field] = "{$label} contains an unauthorized protocol.";
                        continue 2;
                    }
                    // Allow valid http(s) URL or safe internal relative file path
                    $isUrl = (bool)filter_var($val, FILTER_VALIDATE_URL);
                    $isRelPath = (bool)preg_match('/^[a-zA-Z0-9_\-\.\/?&=#]+$/', $val);
                    if (!$isUrl && !$isRelPath) {
                        $errors[$field] = "{$label} contains an invalid URL or relative path.";
                        continue 2;
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'enum':
                    $allowed = $rules['allowed'] ?? [];
                    if (!in_array($val, $allowed, true)) {
                        $allowedList = implode(', ', $allowed);
                        $errors[$field] = "{$label} must be one of: {$allowedList}.";
                        continue 2;
                    }
                    $validatedData[$field] = $val;
                    break;

                case 'boolean':
                case 'bool':
                    $boolVal = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($boolVal === null && $val !== 0 && $val !== 1 && $val !== '0' && $val !== '1') {
                        $errors[$field] = "{$label} must be a boolean value (0 or 1).";
                        continue 2;
                    }
                    $validatedData[$field] = $boolVal ? 1 : 0;
                    break;

                default:
                    $validatedData[$field] = $val;
                    break;
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
            'data'   => $validatedData
        ];
    }
}
