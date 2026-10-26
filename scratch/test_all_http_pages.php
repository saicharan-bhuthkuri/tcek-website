<?php
$pages = [
    '/' => 'Homepage',
    '/index.php' => 'Homepage Index',
    '/events.php' => 'Events',
    '/news.php' => 'News',
    '/circulars.php' => 'Circulars',
    '/department.php?code=CSE' => 'CSE Department',
    '/rnd-rankings.php' => 'R&D Rankings',
    '/gallery.php' => 'Gallery',
    '/admin/login.php' => 'Admin Login'
];

$allPassed = true;
foreach ($pages as $uri => $label) {
    $url = "http://127.0.0.1:8080" . $uri;
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 5
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    $statusLine = $http_response_header[0] ?? 'NO RESPONSE';
    
    if (strpos($statusLine, '200') !== false) {
        $len = strlen($content);
        echo "[PASS] $label ($url) -> $statusLine ($len bytes)\n";
    } else {
        echo "[FAIL] $label ($url) -> $statusLine\n";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "\nALL PAGES SERVED HTTP 200 OK VIA WEB SERVER!\n";
    exit(0);
} else {
    echo "\nSOME PAGES FAILED!\n";
    exit(1);
}
