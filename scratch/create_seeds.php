<?php
/**
 * Initial Data Seeder for Departments and Faculty
 * Generates public/backend/config/departments_data.json and public/backend/config/staff_data.json
 */

$departments = [
    [
        'id' => 1,
        'dept_code' => 'CSE',
        'name' => 'Computer Science & Engineering',
        'slug' => 'cse',
        'degree_level' => 'B.Tech',
        'intake' => '120 Seats',
        'duration' => '4 Years',
        'established_year' => 2008,
        'icon_class' => 'fas fa-laptop-code',
        'theme_class' => 'theme-cse',
        'description' => 'Nurturing next-generation software architects, algorithms innovators, and full-stack technocrats since 2008.',
        'vision' => 'To produce professional Computer Science Engineers who can meet the dynamic expectations of the global technology sector and contribute to the advancement of computing through creativity, innovation, and an outstanding learner-centric environment.',
        'mission' => "1. Provide practical, qualitative technical education in modern laboratory environments to solve algorithmic and real-world software problems.\n2. Inculcate strong foundations in computer science core principles and interdisciplinary computing domains.\n3. Develop domain expertise and research skills that enable graduates to pursue rewarding careers and higher education.\n4. Instill ethical values, leadership qualities, and professional communication among students.",
        'page_url' => 'dept-cse.php',
        'display_order' => 1,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 2,
        'dept_code' => 'ECE',
        'name' => 'Electronics & Communication Engineering',
        'slug' => 'ece',
        'degree_level' => 'B.Tech',
        'intake' => '60 Seats',
        'duration' => '4 Years',
        'established_year' => 2008,
        'icon_class' => 'fas fa-microchip',
        'theme_class' => 'theme-ece',
        'description' => 'Empowering future communication engineers, VLSI circuit designers, and embedded IoT innovators.',
        'vision' => 'To evolve into a center of excellence in Electronics and Communication Engineering education and research, developing socially conscious, technically sound engineering professionals.',
        'mission' => "1. Foster excellence in core electronics, wireless communication, and embedded systems.\n2. Provide modern laboratory facilities aligned with industry standards.\n3. Encourage interdisciplinary research and product development.",
        'page_url' => 'dept-ece.php',
        'display_order' => 2,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 3,
        'dept_code' => 'EEE',
        'name' => 'Electrical & Electronics Engineering',
        'slug' => 'eee',
        'degree_level' => 'B.Tech',
        'intake' => '60 Seats',
        'duration' => '4 Years',
        'established_year' => 2008,
        'icon_class' => 'fas fa-bolt',
        'theme_class' => 'theme-eee',
        'description' => 'Driving innovation in clean energy, electric mobility, power electronics, and autonomous grid systems.',
        'vision' => 'To be a premier department offering state-of-the-art education in electrical engineering, smart grids, and renewable energy technologies.',
        'mission' => "1. Equip students with fundamental engineering knowledge and advanced hands-on expertise.\n2. Partner with electrical utility and EV industries for real-world projects.\n3. Impart high ethical standards and green engineering responsibility.",
        'page_url' => 'dept-eee.php',
        'display_order' => 3,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 4,
        'dept_code' => 'AIML',
        'name' => 'Artificial Intelligence & Machine Learning',
        'slug' => 'aiml',
        'degree_level' => 'B.Tech',
        'intake' => '60 Seats',
        'duration' => '4 Years',
        'established_year' => 2021,
        'icon_class' => 'fas fa-brain',
        'theme_class' => 'theme-aiml',
        'description' => 'Pioneering cutting-edge research in deep learning, neural networks, computer vision, and cognitive computing.',
        'vision' => 'To develop ethical AI innovators and data scientists who engineer impactful artificial intelligence solutions for global challenges.',
        'mission' => "1. Provide in-depth training in algorithms, mathematics, and high-performance computing.\n2. Engage in real-world AI applications across healthcare, finance, and robotics.\n3. Instill responsible AI ethics and intellectual curiosity.",
        'page_url' => 'dept-aiml.php',
        'display_order' => 4,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 5,
        'dept_code' => 'CSE-AIML',
        'name' => 'Computer Science & Engineering (AI & ML)',
        'slug' => 'cse-aiml',
        'degree_level' => 'B.Tech',
        'intake' => '60 Seats',
        'duration' => '4 Years',
        'established_year' => 2024,
        'icon_class' => 'fas fa-robot',
        'theme_class' => 'theme-cse-aiml',
        'description' => 'Integrating core computer systems engineering with generative AI, large language models, and autonomous intelligent systems.',
        'vision' => 'To build world-class engineers specializing in generative intelligence, large models, and resilient scalable cloud infrastructure.',
        'mission' => "1. Merge core software engineering with cutting-edge LLMs and machine intelligence.\n2. Foster industry-backed incubator projects.\n3. Champion research and lifelong technical agility.",
        'page_url' => 'dept-cse-aiml.php',
        'display_order' => 5,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 6,
        'dept_code' => 'H&S',
        'name' => 'Humanities & Sciences',
        'slug' => 'hs',
        'degree_level' => 'B.Tech',
        'intake' => 'All Intake',
        'duration' => 'Foundation',
        'established_year' => 2008,
        'icon_class' => 'fas fa-flask',
        'theme_class' => 'theme-hs',
        'description' => 'Building strong scientific, mathematical, and communicative foundations for first-year engineering scholars.',
        'vision' => 'To establish strong scientific inquiry, mathematical rigor, and linguistic competence as the bedrock of engineering education.',
        'mission' => "1. Deliver rigorous foundational courses in Mathematics, Physics, Chemistry, and English.\n2. Foster interdisciplinary thinking and experimental curiosity.\n3. Build professional soft skills, critical thinking, and character.",
        'page_url' => 'dept-hs.php',
        'display_order' => 6,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 7,
        'dept_code' => 'MBA',
        'name' => 'Masters in Business Administration',
        'slug' => 'mba',
        'degree_level' => 'MBA',
        'intake' => '120 Seats',
        'duration' => '2 Years',
        'established_year' => 2009,
        'icon_class' => 'fas fa-briefcase',
        'theme_class' => 'theme-mba',
        'description' => 'Developing strategic business leaders, financial analysts, human resource strategists, and innovative entrepreneurs.',
        'vision' => 'To be a premier business school inspiring transformative business leadership and ethical entrepreneurship.',
        'mission' => "1. Impart holistic management education blending analytics, finance, marketing, and HR.\n2. Facilitate executive mentorship and corporate internships.\n3. Cultivate entrepreneurial mindset and sustainable management practices.",
        'page_url' => 'dept-mba.php',
        'display_order' => 7,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 8,
        'dept_code' => 'DEEE',
        'name' => 'Electrical & Electronics Engineering (Diploma)',
        'slug' => 'deee',
        'degree_level' => 'Diploma',
        'intake' => '60 Seats',
        'duration' => '3 Years',
        'established_year' => 2013,
        'icon_class' => 'fas fa-plug',
        'theme_class' => 'theme-eee',
        'description' => 'Hands-on polytechnic diploma program focusing on circuit design, electrical maintenance, power wiring, and industrial automation.',
        'vision' => 'To prepare industry-ready diploma technicians equipped with practical electrical skills.',
        'mission' => 'Deliver comprehensive workshop training and laboratory skills in electrical installations and machinery.',
        'page_url' => 'dept-eee.php',
        'display_order' => 8,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 9,
        'dept_code' => 'DECE',
        'name' => 'Electronics & Communication Engineering (Diploma)',
        'slug' => 'dece',
        'degree_level' => 'Diploma',
        'intake' => '60 Seats',
        'duration' => '3 Years',
        'established_year' => 2013,
        'icon_class' => 'fas fa-satellite-dish',
        'theme_class' => 'theme-ece',
        'description' => 'Practical polytechnic diploma program in digital electronics, communication equipment testing, microcontrollers, and PCB design.',
        'vision' => 'To cultivate skilled technical manpower in electronics fabrication and communication equipment.',
        'mission' => 'Impart practical knowledge of electronics troubleshooting, microcontroller programming, and telecom gear.',
        'page_url' => 'dept-ece.php',
        'display_order' => 9,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 10,
        'dept_code' => 'DCSE',
        'name' => 'Computer Science Engineering (Diploma)',
        'slug' => 'dcse',
        'degree_level' => 'Diploma',
        'intake' => '60 Seats',
        'duration' => '3 Years',
        'established_year' => 2023,
        'icon_class' => 'fas fa-desktop',
        'theme_class' => 'theme-cse',
        'description' => 'Polytechnic diploma focusing on application programming, computer maintenance, database administration, and web technologies.',
        'vision' => 'To empower young learners with vocational proficiency in software coding and IT administration.',
        'mission' => 'Deliver experiential training in software programming, database management, and computer hardware.',
        'page_url' => 'dept-cse.php',
        'display_order' => 10,
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ]
];

// Now load and refine the 71 parsed faculty records
$parsed_file = __DIR__ . '/parsed_faculty.json';
$raw_faculty = file_exists($parsed_file) ? json_decode(file_get_contents($parsed_file), true) : [];

$faculty = [];
foreach ($raw_faculty as $idx => $f) {
    $clean_name = trim($f['full_name']);
    $email_prefix = preg_replace('/[^a-z0-9]/', '', strtolower($clean_name));
    if (empty($email_prefix)) {
        $email_prefix = 'faculty' . ($idx + 1);
    }
    
    // Ensure clean bio without extra indentation
    $bio = trim(preg_replace('/\s+/', ' ', $f['bio']));
    
    $f['email'] = $email_prefix . '@tcek.in';
    $f['bio'] = $bio;
    $faculty[] = $f;
}

// Write to public/backend/config/
$target_dept = __DIR__ . '/../public/backend/config/departments_data.json';
$target_staff = __DIR__ . '/../public/backend/config/staff_data.json';

file_put_contents($target_dept, json_encode($departments, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
file_put_contents($target_staff, json_encode($faculty, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Seeded " . count($departments) . " departments into $target_dept\n";
echo "Seeded " . count($faculty) . " faculty members into $target_staff\n";
