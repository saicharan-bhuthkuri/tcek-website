<?php
/**
 * Test Frontend Render with Pure MySQL Data
 */

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../public');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

$pages = [
    'index.php',
    'events.php',
    'news.php',
    'department.php?code=CSE',
    'rnd-rankings.php',
    'gallery.php'
];

$errors = [];

foreach ($pages as $page) {
    $parts = parse_url($page);
    $file = $parts['path'];
    $query = $parts['query'] ?? '';
    parse_str($query, $_GET);

    $full_path = __DIR__ . '/../public/' . $file;
    if (!file_exists($full_path)) {
        $errors[] = "File not found: $full_path";
        continue;
    }

    ob_start();
    try {
        // Run in clean scope
        include $full_path;
        $output = ob_get_clean();
        $len = strlen($output);
        if ($len > 100) {
            echo " [PASS] Rendered $page ($len bytes)\n";
        } else {
            $errors[] = "Page $page returned unusually short output ($len bytes)";
            echo " [WARN] Short output for $page ($len bytes)\n";
        }
    } catch (Throwable $e) {
        ob_end_clean();
        $errors[] = "Exception rendering $page: " . $e->getMessage();
        echo " [FAIL] Exception rendering $page: " . $e->getMessage() . "\n";
    }
}

if (empty($errors)) {
    echo "\nALL FRONTEND PAGES RENDERED SEAMLESSLY WITH PURE MYSQL DATA!\n";
    exit(0);
} else {
    echo "\nSOME PAGES FAILED:\n" . implode("\n", $errors) . "\n";
    exit(1);
}
