<?php
$files = [
    'CSE' => __DIR__ . '/../public/dept-cse.php',
    'ECE' => __DIR__ . '/../public/dept-ece.php',
    'EEE' => __DIR__ . '/../public/dept-eee.php',
    'AIML' => __DIR__ . '/../public/dept-aiml.php',
    'CSE-AIML' => __DIR__ . '/../public/dept-cse-aiml.php',
    'H&S' => __DIR__ . '/../public/dept-hs.php',
    'MBA' => __DIR__ . '/../public/dept-mba.php'
];

$dept_names = [
    'CSE' => 'Computer Science & Engineering',
    'ECE' => 'Electronics & Communication Engineering',
    'EEE' => 'Electrical & Electronics Engineering',
    'AIML' => 'Artificial Intelligence & Machine Learning',
    'CSE-AIML' => 'Computer Science & Engineering (AI & ML)',
    'H&S' => 'Humanities & Sciences',
    'MBA' => 'Masters in Business Administration'
];

$all_faculty = [];
$id = 1;

foreach ($files as $dept_code => $path) {
    if (!file_exists($path)) continue;
    $html = file_get_contents($path);
    
    // Parse HOD section details
    $hod_img = '';
    $hod_name = '';
    $hod_desig = '';
    $hod_bio = '';
    $hod_qual = '';
    $hod_reg = '';
    $hod_exp = '';
    
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]+class=["\']dept-hod-photo["\']/', $html, $m)) {
        $hod_img = $m[1];
    }
    if (preg_match('/<div class="dept-hod-details">\s*<h3>([^<]+)<\/h3>\s*<span class="dept-hod-desig">([^<]+)<\/span>/', $html, $m)) {
        $hod_name = html_entity_decode(trim($m[1]));
        $hod_desig = html_entity_decode(trim($m[2]));
    }
    if (preg_match('/fa-graduation-cap<\/i>\s*([^<]+)<\/span>/', $html, $m)) {
        $hod_qual = html_entity_decode(trim($m[1]));
    }
    if (preg_match('/fa-id-badge<\/i>\s*([^<]+)<\/span>/', $html, $m)) {
        $hod_reg = html_entity_decode(trim(str_replace('JNTUH Reg:', '', $m[1])));
    }
    if (preg_match('/fa-clock<\/i>\s*([^<]+)<\/span>/', $html, $m)) {
        $hod_exp = html_entity_decode(trim($m[1]));
    }
    if (preg_match_all('/<p class="dept-hod-bio">(.*?)<\/p>/s', $html, $m)) {
        $hod_bio = implode("\n\n", array_map('strip_tags', $m[1]));
    }

    // Parse faculty table rows
    if (preg_match('/<table class="dept-faculty-table">(.*?)<\/table>/s', $html, $tm)) {
        if (preg_match_all('/<tr>(.*?)<\/tr>/s', $tm[1], $rows)) {
            $order = 1;
            foreach ($rows[1] as $row) {
                if (strpos($row, '<th') !== false) continue;
                
                $name = '';
                $desig = '';
                $qual = '';
                $reg = '';
                $exp = '';
                
                if (preg_match('/class="dept-faculty-name"[^>]*>([^<]+)</', $row, $m)) {
                    $name = html_entity_decode(trim($m[1]));
                }
                if (preg_match('/class="dept-role-pill[^"]*"[^>]*>([^<]+)</', $row, $m)) {
                    $desig = html_entity_decode(trim($m[1]));
                }
                if (preg_match('/class="dept-qual-pill"[^>]*>([^<]+)</', $row, $m)) {
                    $qual = html_entity_decode(trim($m[1]));
                }
                if (preg_match('/class="dept-reg-id"[^>]*>([^<]+)</', $row, $m)) {
                    $reg = html_entity_decode(trim($m[1]));
                }
                if (preg_match('/<td[^>]*data-label="Experience"[^>]*>([^<]+)</', $row, $m)) {
                    $exp = html_entity_decode(trim($m[1]));
                }
                
                if (!empty($name)) {
                    $is_hod = (stripos($desig, 'hod') !== false || ($order === 1 && stripos($name, 'hod') !== false)) ? 1 : 0;
                    
                    // Categorize role
                    $role_cat = 'Faculty – Junior (JR)';
                    if ($is_hod) {
                        $role_cat = 'HOD';
                    } elseif (stripos($desig, 'professor') !== false && stripos($desig, 'assistant') === false) {
                        $role_cat = 'Faculty – Senior (SR)';
                    } elseif (stripos($desig, 'assoc') !== false) {
                        $role_cat = 'Faculty – Senior (SR)';
                    }
                    
                    // Set R&D flag for select members to showcase R&D features
                    $is_rnd = 0;
                    if (in_array($name, ['GADDAM LAKSHMI', 'Dr. M GANESH', 'Dr. K. NATARAJAN', 'Dr. Ashok Kumar Vootla', 'Dr. ARIF ARFAT', 'MOHD ASEEM FEROZE'])) {
                        $is_rnd = 1;
                        if (!$is_hod) {
                            $role_cat = ($role_cat === 'Faculty – Senior (SR)') ? 'R&D + Senior Faculty (SR)' : 'R&D + Junior Faculty (JR)';
                        }
                    }
                    
                    $full_dept_name = $dept_names[$dept_code] ?? $dept_code;
                    
                    $all_faculty[] = [
                        'id' => $id++,
                        'full_name' => $name,
                        'dept_code' => $dept_code,
                        'department' => $full_dept_name,
                        'designation' => $desig ?: ($is_hod ? 'Head of Department' : 'Assistant Professor'),
                        'role_category' => $role_cat,
                        'is_hod' => $is_hod,
                        'is_rnd' => $is_rnd,
                        'qualification' => $qual ?: 'M.Tech',
                        'jntuh_reg_id' => $reg ?: 'N/A',
                        'experience' => $exp ?: '5+ Years',
                        'email' => strtolower(preg_replace('/[^a-z0-9]/', '', $name)) . '@tcek.in',
                        'phone' => '98480' . sprintf('%05d', $id),
                        'profile_image' => ($is_hod && $hod_img) ? $hod_img : 'assets/Dept/faculty-avatar.png',
                        'bio' => $is_hod ? $hod_bio : "Faculty member in the Department of {$full_dept_name} at TCEK, actively engaging in classroom teaching, student mentorship, and lab practical guidance.",
                        'display_order' => $order++,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }
    }
}

echo "Total Parsed Faculty: " . count($all_faculty) . "\n";
file_put_contents(__DIR__ . '/parsed_faculty.json', json_encode($all_faculty, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
