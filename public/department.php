<?php
/**
 * TCEK Master Dynamic Department Portal
 * Single dynamic detail page that renders ANY department created in Admin Dashboard
 * Live database synchronization for department profile, HOD, and faculty directory
 */
require_once __DIR__ . '/backend/crud.php';

$slug = trim($_GET['slug'] ?? '');
$code = trim($_GET['code'] ?? $_GET['dept'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

$dept = null;

if ($slug !== '') {
    $dept = get_department_by_slug($slug);
    if (!$dept) {
        $dept = get_department_by_code($slug);
    }
} elseif ($code !== '') {
    $dept = get_department_by_code($code);
    if (!$dept) {
        $dept = get_department_by_slug($code);
    }
} elseif ($id > 0) {
    $dept = get_department_by_id($id);
}

// If no matching active department, redirect to departments directory
if (!$dept || empty($dept['is_active'])) {
    header('Location: departments.php');
    exit;
}

$dept_code    = strtoupper(trim($dept['dept_code'] ?? ''));
$dept_name    = htmlspecialchars($dept['name'] ?? 'Department');
$dept_slug    = strtolower(trim($dept['slug'] ?? ''));
if (empty($dept_slug)) {
    $dept_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $dept_code));
}
$degree_level = htmlspecialchars($dept['degree_level'] ?? 'B.Tech');
$intake       = htmlspecialchars($dept['intake'] ?? '60 Seats');
$duration     = htmlspecialchars($dept['duration'] ?? '4 Years');
$established  = (int)($dept['established_year'] ?? 2008);
$icon_class   = htmlspecialchars($dept['icon_class'] ?? 'fas fa-graduation-cap');
$theme_class  = htmlspecialchars($dept['theme_class'] ?? 'theme-cse');
$description  = $dept['description'] ?? '';
$vision       = $dept['vision'] ?? '';
$mission      = $dept['mission'] ?? '';
$active_dept  = $dept_slug;

// Dynamic syllabus and PEOs documents from department record
$dept_syllabus_link = !empty($dept['syllabus_url']) ? htmlspecialchars($dept['syllabus_url']) : 'assets/Dept/peos_psos.docx';
$dept_syllabus_name = "Download {$dept_code} Syllabus";
$dept_peos_link     = !empty($dept['peos_url']) ? htmlspecialchars($dept['peos_url']) : 'assets/Dept/peos_psos.docx';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of <?php echo $dept_name; ?> - Trinity College of Engineering &amp; Technology</title>
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="css/department.css">
</head>
<body class="<?php echo $theme_class; ?>">
    <?php $page = 'departments'; include 'header.php'; ?>

    <!-- Department Hero Header -->
    <header class="dept-portal-hero">
        <div class="container">
            <div class="dept-hero-breadcrumbs">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <a href="departments.php">Departments</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <span><?php echo $dept_name; ?></span>
            </div>
            <span class="dept-hero-badge">
                <i class="<?php echo $icon_class; ?>"></i> <?php echo $degree_level; ?> Program &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of <?php echo $dept_name; ?></h1>
            <p class="dept-hero-tagline">
                <?php echo htmlspecialchars($description ?: 'Excellence in education, technical innovation, high-end laboratories, and student mentorship.'); ?>
            </p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num"><?php echo $established; ?></span>
                        <span class="stat-lbl">Established</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-users"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num"><?php echo $intake; ?></span>
                        <span class="stat-lbl">Annual Intake</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num"><?php echo $duration; ?></span>
                        <span class="stat-lbl">Program Duration</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-award"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">TCEK</span>
                        <span class="stat-lbl">Counselling Code</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Portal Area with Sticky Sidebar -->
    <main class="dept-portal-wrapper">
        <div class="container">

            <!-- Mobile Quick Navigation Bar -->
            <nav class="dept-mobile-nav-bar" aria-label="Department Mobile Navigation">
                <a href="#overview" class="dept-mobile-nav-pill active" data-target="overview"><i class="fas fa-info-circle"></i> About</a>
                <a href="#vision" class="dept-mobile-nav-pill" data-target="vision"><i class="fas fa-bullseye"></i> Vision</a>
                <a href="#hod" class="dept-mobile-nav-pill" data-target="hod"><i class="fas fa-user-tie"></i> HOD</a>
                <a href="#faculty" class="dept-mobile-nav-pill" data-target="faculty"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a href="#curriculum" class="dept-mobile-nav-pill" data-target="curriculum"><i class="fas fa-file-pdf"></i> Syllabus</a>
                <a href="#peos" class="dept-mobile-nav-pill" data-target="peos"><i class="fas fa-award"></i> PEOs</a>
                <a href="#gallery" class="dept-mobile-nav-pill" data-target="gallery"><i class="fas fa-images"></i> Labs</a>
            </nav>

            <div class="dept-portal-grid">
                
                <!-- Left Sticky Sidebar -->
                <?php 
                include 'dept-sidebar.php'; 
                ?>

                <!-- Right Main Content -->
                <div class="dept-main-content">

                    <!-- Section 1: Overview / About -->
                    <section class="dept-section-card active-section" id="overview">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-info-circle"></i></div>
                            <div>
                                <h2>About the Department</h2>
                                <div class="dept-section-sub">Academic overview, specializations, and departmental advantages</div>
                            </div>
                        </div>
                        <p class="dept-section-p">
                            <?php 
                            echo nl2br(htmlspecialchars($description ?: 'The Department of ' . $dept['name'] . ' provides industry-relevant education, high-standard laboratories, and experienced faculty to prepare students for rewarding global engineering careers.')); 
                            ?>
                        </p>
                        <p class="dept-section-p">
                            Active industry-institute partnerships with leading tech corporations and the Telangana Academy for Skill and Knowledge (TASK) provide our students with industry-relevant internships, hands-on technical bootcamps, and top-tier campus recruitment drives.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-microchip"></i>
                                <div>
                                    <h5>Advanced Technical Labs</h5>
                                    <p>Fully networked computing clusters, specialized equipment, and licensed industry toolkits.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-handshake"></i>
                                <div>
                                    <h5>TASK &amp; Corporate Tie-Ups</h5>
                                    <p>Comprehensive vocational training, semester internships, and industrial hackathons.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-briefcase"></i>
                                <div>
                                    <h5>Top Campus Placements</h5>
                                    <p>Graduates placed regularly across Fortune 500 tech companies and premier multinational enterprises.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-certificate"></i>
                                <div>
                                    <h5>Global Certifications</h5>
                                    <p>Curriculum integrated with NPTEL, Coursera, AWS Academy, and RedHat professional certifications.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Section 2: Vision & Mission -->
                    <section class="dept-section-card" id="vision">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-bullseye"></i></div>
                            <div>
                                <h2>Vision &amp; Mission</h2>
                                <div class="dept-section-sub">Institutional commitment to academic excellence and moral leadership</div>
                            </div>
                        </div>
                        <div class="dept-vision-mission-grid">
                            <div class="dept-vm-card theme-vision">
                                <div class="dept-vm-title">
                                    <i class="fas fa-eye"></i>
                                    <span>Department Vision</span>
                                </div>
                                <p>
                                    <?php echo nl2br(htmlspecialchars($vision ?: "To evolve into a center of excellence in {$dept['name']} education and research, developing socially conscious, technically sound engineering professionals.")); ?>
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <?php if (!empty($mission)): ?>
                                    <ul class="dept-mission-list">
                                        <?php 
                                        $mission_items = preg_split('/(?:\r\n|\r|\n|[0-9]+\.\s*)/', $mission, -1, PREG_SPLIT_NO_EMPTY);
                                        foreach ($mission_items as $item):
                                            $item = trim($item);
                                            if ($item === '') continue;
                                        ?>
                                            <li><?php echo htmlspecialchars($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <ul class="dept-mission-list">
                                        <li>Provide practical, qualitative technical education in modern laboratory environments to solve real-world problems.</li>
                                        <li>Inculcate strong foundations in core principles and interdisciplinary technology domains.</li>
                                        <li>Develop domain expertise and research skills that enable graduates to pursue rewarding careers and higher education.</li>
                                        <li>Instill ethical values, leadership qualities, and professional communication among students.</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Section 3 & 4: Head of the Department (HOD) & Faculty Directory (Real-time Database Synchronized) -->
                    <?php 
                    include 'dept-faculty-inc.php'; 
                    ?>

                    <!-- Section 5: Syllabus & Curriculum -->
                    <section class="dept-section-card" id="curriculum">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-file-pdf"></i></div>
                            <div>
                                <h2>Curriculum &amp; Syllabus</h2>
                                <div class="dept-section-sub">Official academic regulations, autonomous syllabus, and course scheme</div>
                            </div>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 22px;">
                            The curriculum follows the JNTUH R22 regulation with continuous upgrades in accordance with UGC Autonomous guidelines, incorporating modern Industry 4.0 courses and experiential laboratories.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4><?php echo $degree_level; ?> <?php echo $dept_code; ?> Official Syllabus</h4>
                                        <p>Comprehensive year-wise and semester-wise subject codes, credits, and evaluation scheme.</p>
                                    </div>
                                </div>
                                <a href="<?php echo htmlspecialchars($dept_syllabus_link); ?>" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>PEOs &amp; PSOs Document</h4>
                                        <p>Program Educational Objectives and Course Outcomes documentation for accreditation.</p>
                                    </div>
                                </div>
                                <a href="<?php echo htmlspecialchars($dept_peos_link); ?>" target="_blank" class="dept-btn-download theme-blue">
                                    <i class="fas fa-eye"></i> View PEOs Document
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Section 6: PEOs & PSOs -->
                    <section class="dept-section-card" id="peos">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-award"></i></div>
                            <div>
                                <h2>Program Educational Objectives (PEOs &amp; PSOs)</h2>
                                <div class="dept-section-sub">Core academic objectives, learning milestones, and program-specific outcomes</div>
                            </div>
                        </div>
                        <div class="dept-vision-mission-grid">
                            <div class="dept-vm-card">
                                <div class="dept-vm-title">
                                    <i class="fas fa-crosshairs"></i>
                                    <span>Program Educational Objectives (PEOs)</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PEO 1:</strong> Graduates will establish successful professional careers in core <?php echo $dept_name; ?> and multidisciplinary technology domains globally.</li>
                                    <li><strong>PEO 2:</strong> Graduates will demonstrate technical competence in designing, testing, and optimizing robust engineering solutions.</li>
                                    <li><strong>PEO 3:</strong> Graduates will practice professional and ethical responsibility, fostering lifelong learning through research, certifications, and entrepreneurship.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes (PSOs)</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Ability to apply design and development principles in constructing systems of varying complexity.</li>
                                    <li><strong>PSO 2:</strong> Proficiency in utilizing modern laboratory instruments, simulation software, and industry computing platforms.</li>
                                    <li><strong>PSO 3:</strong> Competence to analyze, synthesize, and troubleshoot real-world engineering problems for sustainable socio-economic impact.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Section 7: Laboratories & Gallery -->
                    <section class="dept-section-card" id="gallery">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-images"></i></div>
                            <div>
                                <h2>Department Labs &amp; Gallery</h2>
                                <div class="dept-section-sub">State-of-the-art laboratory facilities, technical equipment, and student activities</div>
                            </div>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 20px;">
                            State-of-the-art laboratory centers, student technical symposiums, research clinics, and academic workshops.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            if (!empty($dept['gallery_images'])) {
                                $images = is_array($dept['gallery_images']) ? $dept['gallery_images'] : array_map('trim', explode(',', $dept['gallery_images']));
                            } else {
                                $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            }
                            foreach($images as $img): 
                                $img_src = (strpos($img, '/') !== false) ? $img : "assets/Dept/{$img}";
                            ?>
                            <div class="dept-gallery-card">
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo $dept_code; ?> Department Laboratories &amp; Facilities" loading="lazy">
                                <div class="dept-gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
