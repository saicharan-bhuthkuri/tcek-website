<?php
/**
 * Security Configuration for TCEK Portal
 * Centralizes all configurable thresholds for Rate Limiting, Input Validation,
 * Error Logging, and File Upload Rules.
 */

require_once __DIR__ . '/env.php';

return [
    'rate_limiting' => [
        // Authentication routes (login, password verification)
        'auth' => [
            'max_attempts'        => (int)env('RATE_LIMIT_AUTH_MAX_ATTEMPTS', 5),
            'window_seconds'      => (int)env('RATE_LIMIT_AUTH_WINDOW_SECONDS', 900), // 15 mins window
            'backoff_base_sec'    => (int)env('RATE_LIMIT_AUTH_BACKOFF_BASE_SECONDS', 2),
            'backoff_factor'      => (int)env('RATE_LIMIT_AUTH_BACKOFF_FACTOR', 2),
            'max_backoff_sec'     => (int)env('RATE_LIMIT_AUTH_MAX_BACKOFF_SECONDS', 900), // Max 15 mins delay
            'ip_max_attempts'     => (int)env('RATE_LIMIT_AUTH_IP_MAX_ATTEMPTS', 15), // IP-wide threshold
        ],

        // Public website endpoints (e.g. index, circulars, news)
        'public' => [
            'max_requests'   => (int)env('RATE_LIMIT_PUBLIC_MAX_REQUESTS', 100),
            'window_seconds' => (int)env('RATE_LIMIT_PUBLIC_WINDOW_SECONDS', 60),
        ],

        // Authenticated admin & staff dashboard operations
        'authenticated' => [
            'max_requests'   => (int)env('RATE_LIMIT_AUTH_USER_MAX_REQUESTS', 300),
            'window_seconds' => (int)env('RATE_LIMIT_AUTH_USER_WINDOW_SECONDS', 60),
        ],
    ],

    'uploads' => [
        'max_sizes' => [
            'image'    => (int)env('UPLOAD_MAX_IMAGE_SIZE', 10 * 1024 * 1024),      // 10MB
            'pdf'      => (int)env('UPLOAD_MAX_DOCUMENT_SIZE', 25 * 1024 * 1024),   // 25MB
            'docx'     => (int)env('UPLOAD_MAX_DOCUMENT_SIZE', 25 * 1024 * 1024),   // 25MB
            'video'    => (int)env('UPLOAD_MAX_VIDEO_SIZE', 100 * 1024 * 1024),     // 100MB
        ],
        'allowed_mimes' => [
            'image' => [
                'image/jpeg' => ['jpg', 'jpeg'],
                'image/png'  => ['png'],
                'image/webp' => ['webp'],
                'image/gif'  => ['gif'],
                'image/svg+xml' => ['svg'],
            ],
            'pdf' => [
                'application/pdf' => ['pdf'],
            ],
            'docx' => [
                'application/msword' => ['doc'],
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            ],
            'video' => [
                'video/mp4'        => ['mp4'],
                'video/webm'       => ['webm'],
                'video/ogg'        => ['ogg'],
                'video/quicktime'  => ['mov'],
                'video/x-matroska' => ['mkv'],
            ],
        ],
        'disallowed_extensions' => [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'pht', 'phar',
            'inc', 'exe', 'bat', 'cmd', 'sh', 'bash', 'py', 'pl', 'cgi', 'js', 'html',
            'htm', 'shtml', 'asp', 'aspx', 'jsp', 'htaccess', 'htpasswd', 'config'
        ],
    ],

    'app' => [
        'debug' => (bool)env('APP_DEBUG', false),
        'env'   => (string)env('APP_ENV', 'development'),
    ]
];
