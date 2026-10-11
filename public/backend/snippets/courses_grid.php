<?php
/**
 * Dynamic Courses Tabs & Grid Snippet for Live Sync & index.php
 * Renders all departments dynamically from database / departments store grouped by degree_level
 */
if (empty($depts)) {
    @include_once __DIR__ . '/../departments_crud.php';
    if (function_exists('get_departments')) {
        $depts = get_departments(true);
    }
}

$btech_depts   = [];
$diploma_depts = [];
$mba_depts     = [];

foreach ($depts as $d) {
    $level = strtoupper(trim($d['degree_level'] ?? 'B.TECH'));
    if (strpos($level, 'DIP') !== false || strpos($level, 'POLY') !== false) {
        $diploma_depts[] = $d;
    } elseif (strpos($level, 'MBA') !== false || strpos($level, 'MANAGEMENT') !== false) {
        $mba_depts[] = $d;
    } else {
        $btech_depts[] = $d;
    }
}

// Helper for department tags
function get_dept_tags($code): array {
    $c = strtoupper(trim($code));
    switch ($c) {
        case 'CSE':
            return ['Cloud & DevOps', 'Full Stack', 'Cyber Security'];
        case 'AIML':
            return ['Deep Learning', 'Neural Nets', 'Computer Vision'];
        case 'CSE & CSM':
        case 'CSM':
        case 'CSE-AIML':
            return ['GenAI & LLMs', 'Data Science', 'Smart Systems'];
        case 'ECE':
            return ['VLSI Design', 'Embedded Systems', '5G & IoT'];
        case 'EEE':
            return ['Smart Grids', 'Power Systems', 'EV Tech'];
        case 'MBA':
            return ['Financial Analytics', 'Digital Marketing', 'Strategic HR', 'Corporate Leadership'];
        case 'MECH':
            return ['Robotics & CAD', 'Thermal Engineering', 'Automotive Systems'];
        case 'CIVIL':
            return ['Structural Analysis', 'Smart Cities', 'Geotechnical'];
        default:
            return ['Core Fundamentals', 'Advanced Labs', 'Industry Projects'];
    }
}

// Helper for banner image
function get_dept_banner($code, $degree, $custom_banner = null): string {
    if (!empty($custom_banner)) {
        return $custom_banner;
    }
    $c = strtolower(trim(str_replace(['&', ' '], '', $code)));
    if (strpos($c, 'cseaiml') !== false || strpos($c, 'csm') !== false) {
        return 'assets/courses/cse-aiml.jpg';
    }
    if (strpos($c, 'aiml') !== false) return 'assets/courses/aiml.png';
    if (strpos($c, 'cse') !== false)  return 'assets/courses/cse.png';
    if (strpos($c, 'ece') !== false)  return 'assets/courses/ece.png';
    if (strpos($c, 'eee') !== false)  return 'assets/courses/eee.png';
    if (strpos($c, 'mba') !== false)  return 'assets/courses/mba.jpg';
    return 'assets/courses/all.jpg';
}
?>

<!-- Tab Buttons -->
<div class="tabs-container">
    <button class="tab-btn active" onclick="openCourseTab(event, 'btech')">
        B.Tech (UG) <span style="font-size:11px; opacity:0.85; margin-left:4px;">(<?php echo count($btech_depts); ?>)</span>
    </button>
    <button class="tab-btn" onclick="openCourseTab(event, 'diploma')">
        Diploma (Polytechnic) <span style="font-size:11px; opacity:0.85; margin-left:4px;">(<?php echo count($diploma_depts); ?>)</span>
    </button>
    <button class="tab-btn" onclick="openCourseTab(event, 'mba')">
        MBA (Post Graduate) <span style="font-size:11px; opacity:0.85; margin-left:4px;">(<?php echo count($mba_depts); ?>)</span>
    </button>
</div>

<!-- B.Tech Content -->
<div id="btech" class="tab-content" style="display: block;">
    <div class="courses-modern-grid">
        <?php foreach ($btech_depts as $d): 
            $code  = htmlspecialchars($d['dept_code']);
            $name  = htmlspecialchars($d['name']);
            $theme = htmlspecialchars($d['theme_class'] ?? 'theme-cse');
            $icon  = htmlspecialchars($d['icon_class'] ?? 'fas fa-graduation-cap');
            $slug  = htmlspecialchars($d['slug'] ?? strtolower($code));
            $intake= htmlspecialchars($d['intake'] ?? '60');
            $dur   = htmlspecialchars($d['duration'] ?? '4 Years');
            $est   = htmlspecialchars((string)($d['established_year'] ?? '2008'));
            $banner= get_dept_banner($d['dept_code'], 'btech', $d['banner_image'] ?? $d['dept_logo'] ?? null);
            $tags  = get_dept_tags($d['dept_code']);
            $target_url = !empty($d['page_url']) ? $d['page_url'] : ('department.php?slug=' . urlencode($slug));
        ?>
            <div class="course-card <?php echo $theme; ?>" onclick="window.location.href='<?php echo htmlspecialchars($target_url); ?>';" style="cursor: pointer;" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); window.location.href='<?php echo htmlspecialchars($target_url); ?>';}">
                <div class="course-card-banner">
                    <span class="course-card-category-icon"><i class="<?php echo $icon; ?>"></i></span>
                    <span class="course-card-badge-top">B.Tech · <?php echo $dur; ?></span>
                    <img src="<?php echo $banner; ?>" alt="<?php echo $name; ?>" class="course-banner-img" onerror="this.src='assets/courses/all.jpg'">
                </div>
                <div class="course-card-body">
                    <h3><a href="<?php echo htmlspecialchars($target_url); ?>" style="text-decoration:none; color:inherit;"><?php echo $name; ?> (<?php echo $code; ?>)</a></h3>
                    <div class="course-tags">
                        <?php foreach ($tags as $tIdx => $t): ?>
                            <span class="course-tag">
                                <?php if ($tIdx === 0): ?><i class="fas fa-check-circle"></i> <?php endif; ?>
                                <?php echo htmlspecialchars($t); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="course-metrics-row">
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $intake; ?></span>
                            <span class="metric-name">Intake</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $dur; ?></span>
                            <span class="metric-name">Duration</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $est; ?></span>
                            <span class="metric-name">Established</span>
                        </div>
                    </div>
                    <div class="course-card-footer">
                        <a href="<?php echo htmlspecialchars($target_url); ?>" class="btn-course-explore">
                            <span>Explore Department</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Diploma Content -->
<div id="diploma" class="tab-content" style="display: none;">
    <div class="courses-modern-grid">
        <?php foreach ($diploma_depts as $d): 
            $code  = htmlspecialchars($d['dept_code']);
            $name  = htmlspecialchars($d['name']);
            $theme = htmlspecialchars($d['theme_class'] ?? 'theme-cse');
            $icon  = htmlspecialchars($d['icon_class'] ?? 'fas fa-graduation-cap');
            $slug  = htmlspecialchars($d['slug'] ?? strtolower($code));
            $intake= htmlspecialchars($d['intake'] ?? '60');
            $dur   = htmlspecialchars($d['duration'] ?? '3 Years');
            $est   = htmlspecialchars((string)($d['established_year'] ?? '2013'));
            $banner= get_dept_banner($d['dept_code'], 'diploma', $d['banner_image'] ?? $d['dept_logo'] ?? null);
            $tags  = get_dept_tags($d['dept_code']);
            $target_url = !empty($d['page_url']) ? $d['page_url'] : ('department.php?slug=' . urlencode($slug));
        ?>
            <div class="course-card <?php echo $theme; ?>" onclick="window.location.href='<?php echo htmlspecialchars($target_url); ?>';" style="cursor: pointer;" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); window.location.href='<?php echo htmlspecialchars($target_url); ?>';}">
                <div class="course-card-banner">
                    <span class="course-card-category-icon"><i class="<?php echo $icon; ?>"></i></span>
                    <span class="course-card-badge-top">Polytechnic · <?php echo $dur; ?></span>
                    <img src="<?php echo $banner; ?>" alt="<?php echo $name; ?>" class="course-banner-img" onerror="this.src='assets/courses/all.jpg'">
                </div>
                <div class="course-card-body">
                    <h3><a href="<?php echo htmlspecialchars($target_url); ?>" style="text-decoration:none; color:inherit;"><?php echo $name; ?> (D<?php echo $code; ?>)</a></h3>
                    <div class="course-tags">
                        <?php foreach ($tags as $tIdx => $t): ?>
                            <span class="course-tag">
                                <?php if ($tIdx === 0): ?><i class="fas fa-check-circle"></i> <?php endif; ?>
                                <?php echo htmlspecialchars($t); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="course-metrics-row">
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $intake; ?></span>
                            <span class="metric-name">Intake</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $dur; ?></span>
                            <span class="metric-name">Duration</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $est; ?></span>
                            <span class="metric-name">Established</span>
                        </div>
                    </div>
                    <div class="course-card-footer">
                        <a href="<?php echo htmlspecialchars($target_url); ?>" class="btn-course-explore">
                            <span>Explore Department</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MBA Content -->
<div id="mba" class="tab-content" style="display: none;">
    <div class="courses-modern-grid mba-single-grid">
        <?php foreach ($mba_depts as $d): 
            $code  = htmlspecialchars($d['dept_code']);
            $name  = htmlspecialchars($d['name']);
            $theme = htmlspecialchars($d['theme_class'] ?? 'theme-mba');
            $icon  = htmlspecialchars($d['icon_class'] ?? 'fas fa-briefcase');
            $slug  = htmlspecialchars($d['slug'] ?? 'mba');
            $intake= htmlspecialchars($d['intake'] ?? '120');
            $dur   = htmlspecialchars($d['duration'] ?? '2 Years');
            $est   = htmlspecialchars((string)($d['established_year'] ?? '2009'));
            $banner= get_dept_banner($d['dept_code'], 'mba', $d['banner_image'] ?? $d['dept_logo'] ?? null);
            $tags  = get_dept_tags('MBA');
            $target_url = !empty($d['page_url']) ? $d['page_url'] : ('department.php?slug=' . urlencode($slug));
        ?>
            <div class="course-card <?php echo $theme; ?> flagship-card" onclick="window.location.href='<?php echo htmlspecialchars($target_url); ?>';" style="cursor: pointer;" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); window.location.href='<?php echo htmlspecialchars($target_url); ?>';}">
                <div class="course-card-banner" style="height: 220px;">
                    <span class="course-card-category-icon"><i class="<?php echo $icon; ?>"></i></span>
                    <span class="course-card-badge-top">Post Graduate · <?php echo $dur; ?></span>
                    <img src="<?php echo $banner; ?>" alt="<?php echo $name; ?>" class="course-banner-img" style="max-height: 180px;">
                </div>
                <div class="course-card-body">
                    <h3><a href="<?php echo htmlspecialchars($target_url); ?>" style="text-decoration:none; color:inherit;"><?php echo $name; ?></a></h3>
                    <div class="course-tags">
                        <?php foreach ($tags as $tIdx => $t): ?>
                            <span class="course-tag">
                                <?php if ($tIdx === 0): ?><i class="fas fa-check-circle"></i> <?php endif; ?>
                                <?php echo htmlspecialchars($t); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="course-metrics-row">
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $intake; ?></span>
                            <span class="metric-name">Intake</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $dur; ?></span>
                            <span class="metric-name">Duration</span>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-block">
                            <span class="metric-num"><?php echo $est; ?></span>
                            <span class="metric-name">Established</span>
                        </div>
                    </div>
                    <div class="course-card-footer">
                        <a href="<?php echo htmlspecialchars($target_url); ?>" class="btn-course-explore">
                            <span>Explore MBA Department</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
