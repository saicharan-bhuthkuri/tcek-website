<?php
// Enforce public endpoint rate limiting
if (file_exists(__DIR__ . '/backend/security/RateLimiter.php')) {
    require_once __DIR__ . '/backend/security/RateLimiter.php';
    $publicRate = RateLimiter::checkPublicLimit();
    if (!$publicRate['allowed']) {
        http_response_code(429);
        header('Retry-After: ' . (int)$publicRate['retry_after']);
        echo '<!DOCTYPE html><html><head><title>Too Many Requests</title><meta name="viewport" content="width=device-width,initial-scale=1"></head><body style="font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#1e293b;"><div style="max-width:500px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);border:1px solid #e2e8f0;"><h2 style="color:#e11d48;margin-top:0;">Too Many Requests</h2><p>You have exceeded the request rate limit for this website.</p><p style="font-weight:600;">Please wait ' . (int)$publicRate['retry_after'] . ' seconds before refreshing.</p></div></body></html>';
        exit;
    }
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<meta name="google-site-verification" content="fyPYBMa7M8fenxfEgxl8JdZHFZmQbqEBGeL7pagy2Ew" />
