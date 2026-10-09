<?php require_once __DIR__ . '/backend/crud.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Departments - Trinity College of Engineering &amp; Technology</title>
    <?php include 'head.php'; ?>
    <style>
        .dept-page-header {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            padding: 70px 20px 45px;
            text-align: center;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .dept-page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .dept-header-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .dept-page-header h1 {
            font-size: 2.6rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .dept-page-header p {
            font-size: 1.12rem;
            max-width: 760px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
        }

        .dept-breadcrumbs {
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .dept-breadcrumbs a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
        }

        .dept-breadcrumbs a:hover {
            text-decoration: underline;
        }

        .departments-wrapper {
            padding: 60px 0 90px;
            background: #f8fafc;
            min-height: 600px;
        }
    </style>
</head>

<body>
    <?php $page = 'departments'; include 'header.php'; ?>

    <!-- Page Header -->
    <section class="dept-page-header">
        <div class="container">
            <span class="dept-header-pill">
                <i class="fas fa-graduation-cap"></i> Academic Disciplines &bull; UGC Autonomous
            </span>
            <h1>Our Academic Departments</h1>
            <p>Explore our industry-aligned undergraduate, polytechnic diploma, and postgraduate programs equipped with state-of-the-art laboratories and renowned faculty.</p>
            <div class="dept-breadcrumbs">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span><i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></span>
                <span>Departments</span>
            </div>
        </div>
    </section>

    <!-- Departments Showcase Section -->
    <section class="departments-wrapper">
        <div class="container">
            
            <?php
            $all_active_depts = get_departments(true);

            // Dynamically categorize departments by degree level
            $program_groups = [];
            foreach ($all_active_depts as $d) {
                $lvl = trim($d['degree_level'] ?? 'B.Tech');
                $lvl_upper = strtoupper($lvl);
                if (strpos($lvl_upper, 'DIPLOMA') !== false || strpos($lvl_upper, 'POLY') !== false) {
                    $tab_key = 'diploma';
                    $tab_title = 'Polytechnic Diploma';
                    $tab_icon = 'fas fa-tools';
                } elseif (strpos($lvl_upper, 'MBA') !== false || strpos($lvl_upper, 'MANAGEMENT') !== false || strpos($lvl_upper, 'POST') !== false || strpos($lvl_upper, 'PG') !== false) {
                    $tab_key = 'mba';
                    $tab_title = 'MBA (Postgraduate)';
                    $tab_icon = 'fas fa-briefcase';
                } else {
                    $tab_key = 'btech';
                    $tab_title = 'B.Tech (Undergraduate)';
                    $tab_icon = 'fas fa-laptop-code';
                }

                if (!isset($program_groups[$tab_key])) {
                    $program_groups[$tab_key] = [
                        'key' => $tab_key,
                        'title' => $tab_title,
                        'icon' => $tab_icon,
                        'depts' => []
                    ];
                }
                $program_groups[$tab_key]['depts'][] = $d;
            }

            if (!function_exists('render_public_department_card')) {
                function render_public_department_card($dept) {
                    $code = strtoupper(trim($dept['dept_code'] ?? ''));
                    $name = htmlspecialchars($dept['name'] ?? '');
                    $degree_level = htmlspecialchars($dept['degree_level'] ?? 'B.Tech');
                    $duration = htmlspecialchars($dept['duration'] ?? '4 Years');
                    $intake = htmlspecialchars($dept['intake'] ?? '60');
                    $intake_num = preg_replace('/[^0-9]/', '', $intake) ?: $intake;
                    $established = (int)($dept['established_year'] ?? 2008);
                    $theme = htmlspecialchars($dept['theme_class'] ?? 'theme-cse');
                    $icon = htmlspecialchars($dept['icon_class'] ?? 'fas fa-graduation-cap');
                    
                    // Dynamic banner image from database / JSON
                    $banner_img = !empty($dept['banner_image']) ? htmlspecialchars($dept['banner_image']) : 'assets/courses/cse.png';

                    $slug = strtolower(trim($dept['slug'] ?? ''));
                    if (empty($slug)) {
                        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $code));
                    }
                    $target_url = 'department.php?slug=' . urlencode($slug);

                    // Dynamic tags from database / JSON
                    if (!empty($dept['tags'])) {
                        $tags = is_array($dept['tags']) ? $dept['tags'] : array_map('trim', explode(',', $dept['tags']));
                    } else {
                        $tags = ['Core Curriculum', 'Modern Labs', 'Expert Mentorship'];
                    }
                    ?>
                    <div class="course-card <?php echo $theme; ?><?php echo $code === 'MBA' ? ' flagship-card' : ''; ?>">
                        <div class="course-card-banner" <?php echo $code === 'MBA' ? 'style="height: 220px;"' : ''; ?>>
                            <span class="course-card-category-icon"><i class="<?php echo $icon; ?>"></i></span>
                            <span class="course-card-badge-top"><?php echo $degree_level; ?> &bull; <?php echo $duration; ?></span>
                            <img src="<?php echo $banner_img; ?>" alt="<?php echo $name; ?>" class="course-banner-img" <?php echo $code === 'MBA' ? 'style="max-height: 180px;"' : ''; ?>>
                        </div>
                        <div class="course-card-body">
                            <h3><?php echo $name; ?> (<?php echo $code; ?>)</h3>
                            <div class="course-tags">
                                <?php foreach ($tags as $idx => $t): ?>
                                    <span class="course-tag">
                                        <?php if ($idx === 0): ?><i class="fas fa-check-circle"></i> <?php endif; ?>
                                        <?php echo htmlspecialchars($t); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num"><?php echo $intake_num; ?></span>
                                    <span class="metric-name"><?php echo $code === 'H&S' ? 'B.Tech Intake' : ($code === 'MBA' ? 'Total Seats' : 'Intake'); ?></span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num"><?php echo $duration; ?></span>
                                    <span class="metric-name"><?php echo $code === 'MBA' ? 'Full Time' : 'Duration'; ?></span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num"><?php echo $established; ?></span>
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
                    <?php
                }
            }
            ?>

            <!-- Tab Buttons (Dynamically Generated) -->
            <div class="tabs-container">
                <?php 
                $first = true;
                foreach ($program_groups as $group): 
                ?>
                <button type="button" class="tab-btn <?php echo $first ? 'active' : ''; ?>" onclick="openCourseTab(event, '<?php echo htmlspecialchars($group['key']); ?>')">
                    <i class="<?php echo htmlspecialchars($group['icon']); ?>" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($group['title']); ?>
                </button>
                <?php 
                    $first = false;
                endforeach; 
                ?>
            </div>

            <!-- Tab Contents (Dynamically Rendered) -->
            <?php 
            $first = true;
            foreach ($program_groups as $group): 
                $is_mba = ($group['key'] === 'mba');
            ?>
            <div id="<?php echo htmlspecialchars($group['key']); ?>" class="tab-content <?php echo $first ? 'active' : ''; ?>" <?php echo $first ? 'style="display: block;"' : ''; ?>>
                <div class="courses-modern-grid <?php echo $is_mba ? 'mba-single-grid' : ''; ?>">
                    <?php 
                    foreach ($group['depts'] as $dept) {
                        render_public_department_card($dept);
                    }
                    ?>
                </div>
            </div>
            <?php 
                $first = false;
            endforeach; 
            ?>

        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="js/tabs.js"></script>

</body>
</html>
