<?php
$html = file_get_contents('http://127.0.0.1:8080/index.php');
if ($html === false) {
    echo "FAILED to connect to http://127.0.0.1:8080/index.php\n";
    exit(1);
}

echo "Total HTML length: " . strlen($html) . " bytes\n";
echo "Bulletins count: " . substr_count($html, 'bulletin-item-card') . "\n";
echo "Press cards count: " . substr_count($html, 'press-card') . "\n";
echo "Has 'No active notifications': " . (strpos($html, 'No active notifications') !== false ? 'YES (BAD)' : 'NO (GOOD)') . "\n";
echo "Has 'No newspaper clippings': " . (strpos($html, 'No newspaper clippings') !== false ? 'YES (BAD)' : 'NO (GOOD)') . "\n";

// Check titles of notifications rendered
preg_match_all('/<div class="bulletin-content">.*?<h5>(.*?)<\/h5>/is', $html, $notices);
echo "\n--- Rendered notice titles (" . count($notices[1]) . ") --- \n";
foreach ($notices[1] as $title) {
    echo " - " . trim(strip_tags($title)) . "\n";
}

// Check titles of news clippings rendered
preg_match_all('/<h6 class="press-headline">(.*?)<\/h6>/is', $html, $news);
echo "\n--- Rendered press news titles (" . count($news[1]) . ") --- \n";
foreach ($news[1] as $title) {
    echo " - " . trim(strip_tags($title)) . "\n";
}
