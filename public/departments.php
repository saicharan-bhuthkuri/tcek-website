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
            
            <!-- Tab Buttons -->
            <div class="tabs-container">
                <button type="button" class="tab-btn active" onclick="openCourseTab(event, 'btech')">
                    <i class="fas fa-laptop-code" style="margin-right: 6px;"></i> B.Tech (Undergraduate)
                </button>
                <button type="button" class="tab-btn" onclick="openCourseTab(event, 'diploma')">
                    <i class="fas fa-tools" style="margin-right: 6px;"></i> Polytechnic Diploma
                </button>
                <button type="button" class="tab-btn" onclick="openCourseTab(event, 'mba')">
                    <i class="fas fa-briefcase" style="margin-right: 6px;"></i> MBA (Postgraduate)
                </button>
            </div>

            <!-- B.Tech Content -->
            <div id="btech" class="tab-content active" style="display: block;">
                <div class="courses-modern-grid">
                    
                    <!-- EEE -->
                    <div class="course-card theme-eee">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-bolt"></i></span>
                            <span class="course-card-badge-top">B.Tech &bull; 4 Yrs</span>
                            <img src="assets/courses/eee.png" alt="Electrical &amp; Electronics Engineering (EEE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Electrical &amp; Electronics Engineering (EEE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Smart Grids</span>
                                <span class="course-tag">Power Systems</span>
                                <span class="course-tag">EV Tech</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">4 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2008</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-eee.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ECE -->
                    <div class="course-card theme-ece">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-microchip"></i></span>
                            <span class="course-card-badge-top">B.Tech &bull; 4 Yrs</span>
                            <img src="assets/courses/ece.png" alt="Electronics &amp; Communication Engineering (ECE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Electronics &amp; Communication Engineering (ECE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> VLSI Design</span>
                                <span class="course-tag">Embedded Systems</span>
                                <span class="course-tag">5G &amp; IoT</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">4 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2008</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-ece.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- CSE -->
                    <div class="course-card theme-cse">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-laptop-code"></i></span>
                            <span class="course-card-badge-top">B.Tech &bull; 4 Yrs</span>
                            <img src="assets/courses/cse.png" alt="Computer Science &amp; Engineering (CSE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Computer Science &amp; Engineering (CSE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Cloud &amp; DevOps</span>
                                <span class="course-tag">Full Stack</span>
                                <span class="course-tag">Cyber Security</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">4 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2008</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-cse.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- AIML -->
                    <div class="course-card theme-aiml">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-brain"></i></span>
                            <span class="course-card-badge-top">B.Tech &bull; 4 Yrs</span>
                            <img src="assets/courses/aiml.png" alt="Artificial Intelligence &amp; Machine Learning (AIML)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Artificial Intelligence &amp; Machine Learning (AIML)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Deep Learning</span>
                                <span class="course-tag">Neural Nets</span>
                                <span class="course-tag">Computer Vision</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">4 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2021</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-aiml.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- CSE (AI & ML) -->
                    <div class="course-card theme-cse-aiml">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-robot"></i></span>
                            <span class="course-card-badge-top">B.Tech &bull; 4 Yrs</span>
                            <img src="assets/courses/cse-aiml.jpg" alt="Computer Science and Engineering (AI &amp; ML)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Computer Science &amp; Engineering (AI &amp; ML)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> GenAI &amp; LLMs</span>
                                <span class="course-tag">Data Science</span>
                                <span class="course-tag">Smart Systems</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">4 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2024</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-cse-aiml.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- H & S -->
                    <div class="course-card theme-hs">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-flask"></i></span>
                            <span class="course-card-badge-top">Foundational &bull; 4 Yrs</span>
                            <img src="assets/courses/hs.jpg" alt="Humanities &amp; Sciences (H &amp; S)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Humanities &amp; Sciences (H &amp; S)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Applied Physics</span>
                                <span class="course-tag">Engineering Chemistry</span>
                                <span class="course-tag">Mathematics</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">All</span>
                                    <span class="metric-name">B.Tech Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">Foundation</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2008</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-hs.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Diploma Content -->
            <div id="diploma" class="tab-content">
                <div class="courses-modern-grid">
                    
                    <!-- DEEE -->
                    <div class="course-card theme-eee">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-plug"></i></span>
                            <span class="course-card-badge-top">Polytechnic &bull; 3 Yrs</span>
                            <img src="assets/courses/eee.png" alt="Electrical &amp; Electronics Engineering (DEEE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Electrical &amp; Electronics Engineering (DEEE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Circuit Design</span>
                                <span class="course-tag">Power Wiring</span>
                                <span class="course-tag">Automation</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">3 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2013</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-eee.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- DECE -->
                    <div class="course-card theme-ece">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-satellite-dish"></i></span>
                            <span class="course-card-badge-top">Polytechnic &bull; 3 Yrs</span>
                            <img src="assets/courses/ece.png" alt="Electronics &amp; Communication Engineering (DECE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Electronics &amp; Communication Engineering (DECE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Digital Circuits</span>
                                <span class="course-tag">Microcontrollers</span>
                                <span class="course-tag">Comms</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">3 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2013</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-ece.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- DCSE -->
                    <div class="course-card theme-cse">
                        <div class="course-card-banner">
                            <span class="course-card-category-icon"><i class="fas fa-desktop"></i></span>
                            <span class="course-card-badge-top">Polytechnic &bull; 3 Yrs</span>
                            <img src="assets/courses/cse.png" alt="Computer Science Engineering (DCSE)" class="course-banner-img">
                        </div>
                        <div class="course-card-body">
                            <h3>Computer Science Engineering (DCSE)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Programming</span>
                                <span class="course-tag">Web Technologies</span>
                                <span class="course-tag">Databases</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">60</span>
                                    <span class="metric-name">Intake</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">3 Years</span>
                                    <span class="metric-name">Duration</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2023</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-cse.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- MBA Content -->
            <div id="mba" class="tab-content">
                <div class="courses-modern-grid mba-single-grid">
                    <div class="course-card theme-mba flagship-card">
                        <div class="course-card-banner" style="height: 220px;">
                            <span class="course-card-category-icon"><i class="fas fa-briefcase"></i></span>
                            <span class="course-card-badge-top">Post Graduate &bull; 2 Yrs</span>
                            <img src="assets/courses/mba.jpg" alt="Masters in Business Administration (MBA)" class="course-banner-img" style="max-height: 180px;">
                        </div>
                        <div class="course-card-body">
                            <h3>Masters in Business Administration (MBA)</h3>
                            <div class="course-tags">
                                <span class="course-tag"><i class="fas fa-check-circle"></i> Financial Analytics</span>
                                <span class="course-tag">Digital Marketing</span>
                                <span class="course-tag">Strategic HR</span>
                                <span class="course-tag">Corporate Leadership</span>
                            </div>
                            <div class="course-metrics-row">
                                <div class="metric-block">
                                    <span class="metric-num">120</span>
                                    <span class="metric-name">Total Seats</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2 Years</span>
                                    <span class="metric-name">Full Time</span>
                                </div>
                                <div class="metric-divider"></div>
                                <div class="metric-block">
                                    <span class="metric-num">2009</span>
                                    <span class="metric-name">Established</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="dept-mba.php" class="btn-course-explore">
                                    <span>Explore Department</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="js/tabs.js"></script>

</body>
</html>
