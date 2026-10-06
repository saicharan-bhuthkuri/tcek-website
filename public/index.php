<!DOCTYPE html>
<html lang="en">

<head>
    <title>Trinity College of Engineering & Technology - Peddapalli</title>
    <?php include 'head.php'; ?>
</head>

<body>

    <?php $page = 'home';
    include 'header.php'; ?>
    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero hero-full-landscape">
            <div class="hero-stage-container">
                <div class="hero-stage">
                    <!-- Badges (Floating on top) -->
                    <div class="hero-top-badges">
                        <div class="hero-glass-badge">
                            <span class="live-pulse-dot"></span>
                            <span>Campus Highlights</span>
                        </div>
                        <div class="hero-glass-badge badge-accent">
                            <i class="fas fa-certificate"></i>
                            <span>NAAC 'B++' Grade &amp; Autonomous</span>
                        </div>
                    </div>

                    <!-- Slide Media Items -->
                    <div class="hero-slides-track">
                        <img src="assets/College Event/all1.jpg" alt="College Event" class="hero-slide active">
                        <img src="assets/College Event/caps.jpg" alt="Graduation" class="hero-slide">
                        <img src="assets/College Event/feli1.jpg" alt="Felicitation 1" class="hero-slide">
                        <img src="assets/College Event/feli2.jpg" alt="Felicitation 2" class="hero-slide">
                        <video src="assets/College Event/autonomus.mp4" class="hero-slide" muted playsinline></video>
                    </div>

                    <!-- Cinematic Bottom Overlay Bar -->
                    <div class="hero-bottom-bar">
                        <div class="hero-bar-text">
                            <h3>Excellence in Education</h3>
                            <p><i class="fas fa-shield-halved"></i> Inspiring Future Innovators Since 2008</p>
                        </div>

                        <!-- Controls: Arrows + Dots -->
                        <div class="hero-controls-wrapper">
                            <button class="hero-nav-arrow prev-slide" aria-label="Previous Slide">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="hero-dots-container"></div>
                            <button class="hero-nav-arrow next-slide" aria-label="Next Slide">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Quick Action CTA -->
                        <div class="hero-bar-cta">
                            <a href="admission.php" class="hero-pill-btn">
                                <span>Admissions Open</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TCEK News Section (Magazine & Bulletin Redesign) -->
        <section class="tcek-news-section" id="news">
            <div class="container">
                <!-- Section Header -->
                <div class="news-header-wrap">
                    <span class="news-top-pill">
                        <span class="pulse-indicator"></span> Official Announcements
                    </span>
                    <h2>TCEK NEWS &amp; ANNOUNCEMENTS</h2>
                    <p>Stay informed with our latest university milestones, academic circulars, campus drives &amp; press coverage</p>
                </div>

                <!-- Modern Breaking Ticker Bar -->
                <div class="news-ticker-modern">
                    <div class="ticker-badge"><i class="fas fa-bullhorn"></i> LATEST NOTICE</div>
                    <div class="ticker-marquee">
                        <span>⚡ <strong>Admissions Open 2024–25:</strong> B.Tech, Diploma (Polytechnic) &amp; MBA | EAPCET / POLYCET / ICET Code: <strong>TCEK</strong> | Helpline: <strong>7396903383</strong>, <strong>8522954369</strong></span>
                    </div>
                    <a href="admission.php" class="ticker-link-pill">
                        <span>Admissions Portal</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- 2-Column Asymmetric Main News Hub -->
                <div class="news-main-hub">
                    <!-- Left: Featured Spotlight Card -->
                    <div class="news-spotlight-card">
                        <div class="spotlight-media-wrap">
                            <img src="assets/College Event/caps.jpg" alt="Trinity College Autonomous Milestone Celebration" class="spotlight-img">
                            <div class="spotlight-media-overlay">
                                <span class="spotlight-badge"><i class="fas fa-award"></i> Major Milestone</span>
                                <span class="spotlight-date-chip"><i class="far fa-calendar-alt"></i> AY 2025–2026 to 2029–2030</span>
                            </div>
                        </div>
                        <div class="spotlight-body">
                            <div class="spotlight-meta">
                                <span class="meta-tag"><i class="fas fa-university"></i> UGC &amp; JNTUH Autonomous</span>
                                <span class="meta-tag"><i class="fas fa-check-circle"></i> 5 Years Validity</span>
                            </div>
                            <h3>Trinity College Conferred UGC &amp; JNTUH Autonomous Status for 5 Academic Years</h3>
                            <p>We are immensely proud to announce that Trinity College of Engineering &amp; Technology has been officially conferred Autonomous status by UGC and JNTUH. This prestigious milestone grants academic independence to formulate advanced, industry-aligned curricula, introduce cutting-edge electives in AI, Data Science &amp; VLSI, and provide enhanced research and placement avenues for our students.</p>
                            
                            <div class="spotlight-highlights-grid">
                                <div class="highlight-pill">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div>
                                        <strong>Curriculum Autonomy</strong>
                                        <span>Industry 4.0 Syllabus</span>
                                    </div>
                                </div>
                                <div class="highlight-pill">
                                    <i class="fas fa-medal"></i>
                                    <div>
                                        <strong>Degree Prestige</strong>
                                        <span>Recognized by UGC &amp; JNTUH</span>
                                    </div>
                                </div>
                            </div>

                            <div class="spotlight-footer">
                                <a href="ugc.php" class="btn-spotlight-action">
                                    <span>Read UGC Notification</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <a href="academics.php" class="btn-spotlight-link">
                                    <span>Explore Academics</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Digital Bulletin Board -->
                    <div class="news-bulletin-board">
                        <div class="bulletin-header">
                            <div class="bulletin-heading">
                                <div class="bulletin-icon-pulse"><i class="fas fa-bell"></i></div>
                                <h4>Recent Bulletins</h4>
                            </div>
                            <span class="bulletin-badge-live"><span class="live-dot"></span> LIVE FEED</span>
                        </div>

                        <div class="bulletin-items-list">
                            <!-- Bulletin 1: NAAC -->
                            <a href="naac.php" class="bulletin-item-card">
                                <div class="bulletin-date-badge theme-naac">
                                    <span class="date-month">NAAC</span>
                                    <span class="date-day">B++</span>
                                </div>
                                <div class="bulletin-content">
                                    <span class="bulletin-category cat-naac">Accreditation</span>
                                    <h5>First &amp; Only NAAC Accredited College in Peddapalli</h5>
                                    <p>Recognized for world-class laboratory infrastructure, experienced faculty, and strong student outcomes.</p>
                                    <span class="bulletin-link-text">View Certificate <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </a>

                            <!-- Bulletin 2: Admissions -->
                            <a href="admission.php" class="bulletin-item-card">
                                <div class="bulletin-date-badge theme-adms">
                                    <span class="date-month">CODE</span>
                                    <span class="date-day">TCEK</span>
                                </div>
                                <div class="bulletin-content">
                                    <span class="bulletin-category cat-adms">Admissions 2024–25</span>
                                    <h5>B.Tech, Diploma &amp; MBA Counseling Open</h5>
                                    <p>Seat allotments through TS EAPCET, POLYCET &amp; ICET. Merit scholarship fee concessions available.</p>
                                    <span class="bulletin-link-text">Admissions Details <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </a>

                            <!-- Bulletin 3: Placements -->
                            <a href="placement-cell.php" class="bulletin-item-card">
                                <div class="bulletin-date-badge theme-jobs">
                                    <span class="date-month">DRIVE</span>
                                    <span class="date-day">100%</span>
                                </div>
                                <div class="bulletin-content">
                                    <span class="bulletin-category cat-jobs">Campus Placements</span>
                                    <h5>Recruitment Drives: TCS, Capgemini, Infosys</h5>
                                    <p>Pre-placement training, coding bootcamps, and top multinational recruitment opportunities.</p>
                                    <span class="bulletin-link-text">Placement Reports <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Press & Newspaper Clippings Section -->
                <div class="news-press-section">
                    <div class="press-strip-header">
                        <div class="press-title">
                            <i class="fas fa-newspaper"></i>
                            <span>TCEK In Regional &amp; National Press</span>
                        </div>
                        <a href="gallery.php" class="view-all-press-btn">
                            <span>View All Gallery Clippings</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="press-clippings-grid">
                        <!-- Row 1: Academic & Milestone Press Clippings -->
                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper.jpg', 'Eenadu / Sakshi: Autonomous Status Celebration')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper.jpg" alt="Press Coverage of Autonomous Status">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Press Release</span>
                                <h6 class="press-headline">Autonomous Status Conferred by UGC to TCEK</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper1.jpg', 'Andhra Jyothi: Academic Excellence & Placements')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper1.jpg" alt="Press Coverage of Academic Excellence">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Print Media</span>
                                <h6 class="press-headline">Peddapalli Technocrats Bag Top Tech Placements</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper2.jpg', 'Namasthe Telangana: NAAC B++ Accreditation Recognition')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper2.jpg" alt="Press Coverage of NAAC Accreditation">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Accreditation</span>
                                <h6 class="press-headline">Pioneer NAAC B++ Accredited Engineering College</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper3.jpg', 'Daily News: Campus Innovation & Technical Symposium')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper3.jpg" alt="Press Coverage of Innovation Symposium">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Campus News</span>
                                <h6 class="press-headline">State-Level Technical Symposium &amp; Project Expo</h6>
                            </div>
                        </div>

                        <!-- Row 2: Industrial Visits, Placements & Sports Press Clippings -->
                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper4.jpg', 'Sakshi: Campus Placement Drive - 224 Placed Across 17 MNCs')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper4.jpg" alt="Press Coverage of Campus Placement Drive">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Sakshi Daily</span>
                                <h6 class="press-headline">Campus Placement Drive: 224 Placed in 17 MNCs</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/paper5.jpg', 'Mana Vartha: Electrical & Electronics Mini Hydel Industrial Visit')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/paper5.jpg" alt="Press Coverage of Industrial Visit">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Mana Vartha</span>
                                <h6 class="press-headline">Electrical &amp; Electronics Mini Hydel Industrial Visit</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/infosys.jpg', 'Prabha News: Trinity Tech Students Visit Infosys SEZ with TASK')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/infosys.jpg" alt="Press Coverage of Infosys SEZ Visit">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Prabha News</span>
                                <h6 class="press-headline">Trinity Tech Students Visit Infosys SEZ with TASK</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/papers.jpg', 'Prabha News: National Level Martial Arts Championship Gold')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/papers.jpg" alt="Press Coverage of Sports Achievement">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Sports Honor</span>
                                <h6 class="press-headline">National Level Martial Arts Championship Gold</h6>
                            </div>
                        </div>

                        <!-- Row 3: Institutional Notifications & Graduation Press -->
                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/autonomous.jpg', 'UGC Gazette: Autonomous Status Conferred for 5 Years')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/autonomous.jpg" alt="Official UGC Autonomy Notification">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">UGC Gazette</span>
                                <h6 class="press-headline">Autonomous Status Conferred for 5 Academic Years</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/naac2.jpg', 'NAAC Council: Accredited with National B++ Grade Benchmark')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/naac2.jpg" alt="Official NAAC B++ Accreditation Release">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">NAAC Council</span>
                                <h6 class="press-headline">Accredited with Prestigious B++ Quality Benchmark</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/Gallery/pamplet1.jpg', 'Academic Bulletin: 17 Years of Engineering Academic Excellence')">
                            <div class="press-thumb-wrap">
                                <img src="assets/Gallery/pamplet1.jpg" alt="Official 17 Years Excellence Release">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Campus Bulletin</span>
                                <h6 class="press-headline">17 Years of Engineering Academic Excellence</h6>
                            </div>
                        </div>

                        <div class="press-card" onclick="openNewsLightbox('assets/College Event/feli2.jpg', 'Special Feature: Annual Convocation & Graduation Ceremony')">
                            <div class="press-thumb-wrap">
                                <img src="assets/College Event/feli2.jpg" alt="Graduation Day and Convocation Ceremony">
                                <div class="press-overlay-badge">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>
                            <div class="press-info">
                                <span class="press-source">Special Feature</span>
                                <h6 class="press-headline">Annual Convocation &amp; Graduation Honors Ceremony</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Lightbox Modal -->
        <div id="news-lightbox-modal" class="news-lightbox" onclick="closeNewsLightbox(event)">
            <div class="news-lightbox-box">
                <button type="button" class="news-lightbox-close" onclick="closeNewsLightbox(event)" aria-label="Close modal">&times;</button>
                <img id="news-lightbox-target" src="" alt="Zoomed Newspaper Clipping">
            </div>
        </div>
        <script>
            function openNewsLightbox(src, caption) {
                const modal = document.getElementById('news-lightbox-modal');
                const img = document.getElementById('news-lightbox-target');
                if (modal && img) {
                    img.src = src;
                    img.alt = caption || 'News Article';
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }
            function closeNewsLightbox(e) {
                if (e.target.id === 'news-lightbox-modal' || e.target.classList.contains('news-lightbox-close')) {
                    const modal = document.getElementById('news-lightbox-modal');
                    if (modal) {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            }
        </script>

        <!-- Features/Courses Section -->
        <section id="courses">
            <div class="container">
                <div class="section-header">
                    <h2>Our Courses</h2>
                    <p>World-class academic programmes designed for future leaders</p>
                </div>

                <!-- Tab Buttons -->
                <div class="tabs-container">
                    <button class="tab-btn active" onclick="openCourseTab(event, 'btech')">B.Tech</button>
                    <button class="tab-btn" onclick="openCourseTab(event, 'diploma')">Diploma</button>
                    <button class="tab-btn" onclick="openCourseTab(event, 'mba')">MBA</button>
                </div>

                <!-- B.Tech Content -->
                <div id="btech" class="tab-content" style="display: block;">
                    <div class="courses-modern-grid">
                        <!-- EEE -->
                        <div class="course-card theme-eee">
                            <div class="course-card-banner">
                                <span class="course-card-category-icon"><i class="fas fa-bolt"></i></span>
                                <span class="course-card-badge-top">B.Tech · 4 Yrs</span>
                                <img src="assets/courses/eee.png" alt="Electrical & Electronics Engineering (EEE)"
                                    class="course-banner-img">
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
                                <span class="course-card-badge-top">B.Tech · 4 Yrs</span>
                                <img src="assets/courses/ece.png" alt="Electronics & Communication Engineering (ECE)"
                                    class="course-banner-img">
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
                                <span class="course-card-badge-top">B.Tech · 4 Yrs</span>
                                <img src="assets/courses/cse.png" alt="Computer Science & Engineering (CSE)"
                                    class="course-banner-img">
                            </div>
                            <div class="course-card-body">
                                <h3>Computer Science &amp; Engineering (CSE)</h3>
                                <div class="course-tags">
                                    <span class="course-tag"><i class="fas fa-check-circle"></i> Cloud &amp;
                                        DevOps</span>
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
                                <span class="course-card-badge-top">B.Tech · 4 Yrs</span>
                                <img src="assets/courses/aiml.png"
                                    alt="Artificial Intelligence & Machine Learning (AIML)" class="course-banner-img">
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
                                <span class="course-card-badge-top">B.Tech · 4 Yrs</span>
                                <img src="assets/courses/cse-aiml.jpg" alt="Computer Science and Engineering (AI & ML)"
                                    class="course-banner-img">
                            </div>
                            <div class="course-card-body">
                                <h3>Computer Science and Engineering (AI &amp; ML)</h3>
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
                    </div>
                </div>

                <!-- Diploma Content -->
                <div id="diploma" class="tab-content">
                    <div class="courses-modern-grid">
                        <div class="course-card theme-eee">
                            <div class="course-card-banner">
                                <span class="course-card-category-icon"><i class="fas fa-plug"></i></span>
                                <span class="course-card-badge-top">Polytechnic · 3 Yrs</span>
                                <img src="assets/courses/eee.png" alt="Electrical & Electronics Engineering (DEEE)"
                                    class="course-banner-img">
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
                                    <a href="departments.php" class="btn-course-explore">
                                        <span>Course Details</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="course-card theme-ece">
                            <div class="course-card-banner">
                                <span class="course-card-category-icon"><i class="fas fa-satellite-dish"></i></span>
                                <span class="course-card-badge-top">Polytechnic · 3 Yrs</span>
                                <img src="assets/courses/ece.png" alt="Electronics & Communication Engineering (DECE)"
                                    class="course-banner-img">
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
                                    <a href="departments.php" class="btn-course-explore">
                                        <span>Course Details</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="course-card theme-cse">
                            <div class="course-card-banner">
                                <span class="course-card-category-icon"><i class="fas fa-desktop"></i></span>
                                <span class="course-card-badge-top">Polytechnic · 3 Yrs</span>
                                <img src="assets/courses/cse.png" alt="Computer Science Engineering (DCSE)"
                                    class="course-banner-img">
                            </div>
                            <div class="course-card-body">
                                <h3>Computer Science Engineering (DCSE)</h3>
                                <div class="course-tags">
                                    <span class="course-tag"><i class="fas fa-check-circle"></i> Programming</span>
                                    <span class="course-tag">Web Technologies</span>
                                    <span class="course-tag">Database</span>
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
                                    <a href="departments.php" class="btn-course-explore">
                                        <span>Course Details</span>
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
                                <span class="course-card-badge-top">Post Graduate · 2 Yrs</span>
                                <img src="assets/courses/mba.jpg" alt="Masters in Business Administration (MBA)"
                                    class="course-banner-img" style="max-height: 180px;">
                            </div>
                            <div class="course-card-body">
                                <h3>Masters in Business Administration (MBA)</h3>
                                <div class="course-tags">
                                    <span class="course-tag"><i class="fas fa-check-circle"></i> Financial
                                        Analytics</span>
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
                                        <span>Explore MBA Department</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Achievements & Placements Section -->
        <section id="achievements">
            <div class="container">
                <div class="ach-header-wrap">
                    <div class="ach-pill">
                        <span class="ach-pill-dot"></span>
                        <span>Campus Placement Success • Class of 2022-2024</span>
                    </div>
                    <h2>Our Achievements & Placements</h2>
                    <p>Celebrating the remarkable success stories of our students advancing into global IT leaders and multinational technology corporations</p>
                </div>

                <!-- Placement Highlights Stats Bar -->
                <div class="ach-stats-grid">
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">85%+</span>
                            <span class="ach-stat-label">Placement Record</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-trophy"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">₹12 LPA</span>
                            <span class="ach-stat-label">Highest Package</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-building"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">50+</span>
                            <span class="ach-stat-label">Corporate Recruiters</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-award"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">TASK Partner</span>
                            <span class="ach-stat-label">Govt. Skill Synergy</span>
                        </div>
                    </div>
                </div>

                <!-- Main Showcase Card Slider -->
                <div class="achievements-container">
                    <div class="achievements-showcase-card">
                        <div class="achievement-slider">
                            <!-- Slide 1: G. Bhavitha -->
                            <div class="achievement-slide active">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/Achievements/bhavitha_capgemini.png" alt="G. Bhavitha - Placed in Capgemini (4 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of 2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill capgemini">
                                                <i class="fas fa-briefcase"></i> Placed in Capgemini
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "As a CSE student at our institution, I, Bhavitha, can confidently say that our campus placement services are <span class="ach-text-bold">exceptional</span>. The training and support we receive are tailored to ensure we are <span class="ach-text-bold">well-prepared for the job market</span>. From enhancing our technical skills to providing interview preparation, the focus on our future careers is evident. Thanks to these efforts, I was successfully placed in <span class="ach-text-bold">Capgemini</span>."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>GB</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">G. Bhavitha</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 18UD1AO410</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">4.0 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2: Zainab Khatoon -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/Achievements/zainab_tcs.png" alt="Zainab Khatoon - Placed in TCS (3.6 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of 2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill tcs">
                                                <i class="fas fa-briefcase"></i> Placed in TCS
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "The dedicated training and comprehensive support provided by our institution have been instrumental in <span class="ach-text-bold">shaping my engineering career</span>. The focus on practical skills, mock interviews, and industry-specific knowledge thoroughly prepared me for the job market. Thanks to these efforts, I secured a placement with <span class="ach-text-bold">Tata Consultancy Services</span> as Assistant System Engineer."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>ZK</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">Zainab Khatoon</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 19UD1A0542</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.6 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 3: J. Pooja -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/placements/student1.jpeg" alt="J. Pooja - Placed in Infosys (3.6 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of 2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill infosys">
                                                <i class="fas fa-briefcase"></i> Placed in Infosys
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "Trinity College provided an enriching academic ecosystem with active guidance from experienced mentors and the <span class="ach-text-bold">Training & Placement Cell</span>. Continuous aptitude assessments, soft-skill workshops, and coding challenges gave me the edge required to crack the <span class="ach-text-bold">Infosys</span> national assessment and interview rounds."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>JP</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">J. Pooja</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 19UD5A0206</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.6 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 4: Ananth Kumar -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/placements/student5.jpeg" alt="Ananth Kumar - Placed in Wipro (3.5 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of 2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill wipro">
                                                <i class="fas fa-briefcase"></i> Placed in Wipro
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "The hands-on laboratory experience and guidance from our faculty at TCEK helped me build strong engineering fundamentals. The college's industry partnerships, <span class="ach-text-bold">TASK skill bootcamps</span>, and placement training gave us real-world corporate readiness, helping me secure an offer at <span class="ach-text-bold">Wipro</span>."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>AK</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">Ananth Kumar</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 18UD1AO422</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.5 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Controls Bar -->
                        <div class="ach-controls-bar">
                            <div class="ach-counter">
                                <span class="ach-current-num">01</span>
                                <span class="ach-separator">/</span>
                                <span class="ach-total-num">04</span>
                            </div>
                            <div class="ach-dots" id="achDots">
                                <button class="ach-dot active" data-index="0" aria-label="Slide 1: G. Bhavitha"></button>
                                <button class="ach-dot" data-index="1" aria-label="Slide 2: Zainab Khatoon"></button>
                                <button class="ach-dot" data-index="2" aria-label="Slide 3: J. Pooja"></button>
                                <button class="ach-dot" data-index="3" aria-label="Slide 4: Ananth Kumar"></button>
                            </div>
                            <div class="ach-nav-btns">
                                <button class="ach-ctrl-btn ach-prev" aria-label="Previous Student"><i class="fas fa-arrow-left"></i></button>
                                <button class="ach-ctrl-btn ach-next" aria-label="Next Student"><i class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placed Students Quick-Strip / Mini Wall -->
                <div class="ach-alumni-strip">
                    <div class="ach-alumni-heading">
                        <i class="fas fa-users"></i>
                        <span>More Star Placements:</span>
                    </div>
                    <div class="ach-alumni-badges">
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student2.jpeg" alt="B. Sravani - Capgemini">
                            <div class="pill-meta">
                                <strong>B. Sravani</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student4.jpeg" alt="G. Swetha - Capgemini">
                            <div class="pill-meta">
                                <strong>G. Swetha</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student6.jpeg" alt="J. Hima Bindu - Capgemini">
                            <div class="pill-meta">
                                <strong>J. Hima Bindu</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student7.jpeg" alt="M. Meghana - TCS">
                            <div class="pill-meta">
                                <strong>M. Meghana</strong>
                                <span>TCS (3.36 LPA)</span>
                            </div>
                        </div>
                    </div>
                    <div class="ach-cta-wrap">
                        <a href="placement-cell.php" class="btn-ach-explore">
                            <span>Explore Placement Cell</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Recruiter Logos Strip -->
                <div class="ach-recruiters-section">
                    <div class="ach-recruiters-title">Proud Corporate Hiring & Training Partners</div>
                    <div class="ach-recruiters-grid">
                        <div class="recruiter-chip"><span class="chip-dot"></span> Capgemini</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Tata Consultancy Services</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Infosys</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Wipro</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Tech Mahindra</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Cognizant</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> TASK Telangana</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- R&D Department Rankings Section (Q4 Announcement) -->
        <section id="rnd-rankings">
            <div class="container">
                <div class="rnd-header-wrap">
                    <div class="rnd-pill">
                        <span class="rnd-pill-icon"><i class="fas fa-atom"></i></span>
                        <span>Q4 (JAN 2026 – MAR 2026) Official Announcement</span>
                    </div>
                    <h2>R&amp;D Department Rankings</h2>
                    <p>Congratulations to all academic departments for outstanding achievements in research, innovation, patents, international publications, NPTEL benchmarks, IIC and R&amp;D activities.</p>
                </div>

                <!-- Criteria Pills Strip -->
                <div class="rnd-criteria-strip">
                    <span class="rnd-crit-tag"><i class="fas fa-microscope"></i> Research &amp; Innovation</span>
                    <span class="rnd-crit-tag"><i class="fas fa-certificate"></i> Patents &amp; IPR</span>
                    <span class="rnd-crit-tag"><i class="fas fa-book-open"></i> Scopus &amp; IEEE Publications</span>
                    <span class="rnd-crit-tag"><i class="fas fa-medal"></i> NPTEL Honors</span>
                    <span class="rnd-crit-tag"><i class="fas fa-lightbulb"></i> IIC Initiatives</span>
                </div>

                <!-- 5 Department Rankings Grid -->
                <div class="rnd-podium-grid">
                    <!-- 1st Rank: AIML (Gold) -->
                    <div class="rnd-card rank-1">
                        <div>
                            <div class="rnd-card-top">
                                <span class="rnd-rank-num">1<sup>st</sup></span>
                                <span class="rnd-medal-badge">🥇 1st · Gold</span>
                            </div>
                            <h3 class="rnd-dept-code">AIML</h3>
                            <div class="rnd-dept-full">Artificial Intelligence &amp; Machine Learning</div>
                            <ul class="rnd-dept-perks">
                                <li><i class="fas fa-check-circle"></i> High-Impact Research Papers</li>
                                <li><i class="fas fa-check-circle"></i> Patents &amp; Innovation Leads</li>
                                <li><i class="fas fa-check-circle"></i> NPTEL &amp; IIC Star Rating</li>
                            </ul>
                        </div>
                        <div class="rnd-status-tag">
                            <i class="fas fa-trophy"></i> Gold Champion
                        </div>
                    </div>

                    <!-- 2nd Rank: CSE & CSM (Bronze) -->
                    <div class="rnd-card rank-2">
                        <div>
                            <div class="rnd-card-top">
                                <span class="rnd-rank-num">2<sup>nd</sup></span>
                                <span class="rnd-medal-badge">🥈 2nd · Bronze</span>
                            </div>
                            <h3 class="rnd-dept-code">CSE &amp; CSM</h3>
                            <div class="rnd-dept-full">Computer Science &amp; Engineering / CSM</div>
                            <ul class="rnd-dept-perks">
                                <li><i class="fas fa-check-circle"></i> Coding &amp; Hackathon Projects</li>
                                <li><i class="fas fa-check-circle"></i> Technical Publications</li>
                                <li><i class="fas fa-check-circle"></i> Active IIC Engagement</li>
                            </ul>
                        </div>
                        <div class="rnd-status-tag">
                            <i class="fas fa-award"></i> Bronze Honor
                        </div>
                    </div>

                    <!-- 3rd Rank: EEE (Bronze) -->
                    <div class="rnd-card rank-3">
                        <div>
                            <div class="rnd-card-top">
                                <span class="rnd-rank-num">3<sup>rd</sup></span>
                                <span class="rnd-medal-badge">🥉 3rd · Bronze</span>
                            </div>
                            <h3 class="rnd-dept-code">EEE</h3>
                            <div class="rnd-dept-full">Electrical &amp; Electronics Engineering</div>
                            <ul class="rnd-dept-perks">
                                <li><i class="fas fa-check-circle"></i> Mini Hydel &amp; Power Labs</li>
                                <li><i class="fas fa-check-circle"></i> Green Energy Innovations</li>
                                <li><i class="fas fa-check-circle"></i> Faculty Research Papers</li>
                            </ul>
                        </div>
                        <div class="rnd-status-tag">
                            <i class="fas fa-medal"></i> Bronze Honor
                        </div>
                    </div>

                    <!-- 4th Rank: ECE (Emerging) -->
                    <div class="rnd-card rank-4">
                        <div>
                            <div class="rnd-card-top">
                                <span class="rnd-rank-num">4<sup>th</sup></span>
                                <span class="rnd-medal-badge">🏅 4th · Emerging</span>
                            </div>
                            <h3 class="rnd-dept-code">ECE</h3>
                            <div class="rnd-dept-full">Electronics &amp; Communication Engineering</div>
                            <ul class="rnd-dept-perks">
                                <li><i class="fas fa-check-circle"></i> Embedded &amp; IoT Systems</li>
                                <li><i class="fas fa-check-circle"></i> Signal Processing Projects</li>
                                <li><i class="fas fa-check-circle"></i> Rising NPTEL Enrolments</li>
                            </ul>
                        </div>
                        <div class="rnd-status-tag">
                            🌱 Emerging
                        </div>
                    </div>

                    <!-- 5th Rank: MBA (Emerging) -->
                    <div class="rnd-card rank-5">
                        <div>
                            <div class="rnd-card-top">
                                <span class="rnd-rank-num">5<sup>th</sup></span>
                                <span class="rnd-medal-badge">🏅 5th · Emerging</span>
                            </div>
                            <h3 class="rnd-dept-code">MBA</h3>
                            <div class="rnd-dept-full">Department of Management Studies</div>
                            <ul class="rnd-dept-perks">
                                <li><i class="fas fa-check-circle"></i> Business Case Studies</li>
                                <li><i class="fas fa-check-circle"></i> Entrepreneurship Cell</li>
                                <li><i class="fas fa-check-circle"></i> Startup Incubation Meets</li>
                            </ul>
                        </div>
                        <div class="rnd-status-tag">
                            🌱 Emerging
                        </div>
                    </div>
                </div>

                <!-- Motivation & Acknowledgement Card -->
                <div class="rnd-footer-card">
                    <div class="rnd-motto-side">
                        <div class="rnd-motto-quote">
                            ✨ "Your Innovation. <span>Our Pride.</span> Keep Innovating. Keep Inspiring."
                        </div>
                        <div class="rnd-signature">
                            <i class="fas fa-signature"></i> — Team Research &amp; Development (R&amp;D)
                        </div>
                        <a href="research-publications.php" class="btn-rnd-explore">
                            <span>Explore Research Publications</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="rnd-ack-side">
                        <div class="rnd-ack-box">
                            <div class="rnd-ack-icon">👏</div>
                            <div class="rnd-ack-text">
                                <h5>Heartiest Congratulations</h5>
                                <p>To all the Faculty, HoDs, Students and Department Coordinators for their dedication and continuous contribution towards building a strong research and innovation ecosystem.</p>
                            </div>
                        </div>
                        <div class="rnd-ack-box">
                            <div class="rnd-ack-icon">🙏</div>
                            <div class="rnd-ack-text">
                                <h5>Sincere Gratitude</h5>
                                <p>Our sincere thanks to the Management, Staff, Stakeholders, Students, Parents &amp; Alumni for their constant encouragement and invaluable support.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Us, Vision & Mission Section -->
        <section id="why-us" style="background: #ffffff; padding: 90px 20px; border-top: 1px solid #f1f5f9;">
            <div class="container">
                <div class="section-header">
                    <h2>Why Us?</h2>
                    <!-- Intro Text -->
                    <div
                        style="font-size: 16px; color: #636e72; line-height: 1.8; margin-bottom: 40px; text-align: left;">
                        <p style="margin-bottom: 20px;">
                            To implement this thought, we established an Education Society which aims at breaking
                            grounds for high level educational institutions. Trinity College of Engineering and
                            Technology (T.C.E.K.) is a premier initiative of this society. The institute aims to impart
                            knowledge by attracting and involving well experienced, qualified faculty and providing best
                            infrastructural facilities to the students. T.C.E.K. views interaction and collaboration
                            with industry as critical for preparing successful and trend setter technocrats for
                            tomorrow. Workshops and guest lectures with a focus on developing entrepreneur skills will
                            be our mainstay.
                        </p>
                        <p>
                            We are leading step by step to achieve our objectives to transform the Institute into one of
                            the notable technical institutes of the country. T.C.E.K. is ready with the facilities to
                            provide best services to you. It is for you to avail this opportunity.
                        </p>
                    </div>
                </div>

                <div class="features-grid">
                    <div class="card">
                        <div class="modal-icon" style="color:#00b894;"><i class="fas fa-eye"></i></div>
                        <h3>Our Vision</h3>
                        <p>Becoming a vibrant knowledge hub and a center of excellence in education. Generating cutting
                            edge technology using research and innovation to make India a developed nation. Creating
                            leaders in the field of science, technology and management by providing quality education.
                            To be the fountain head in producing highly skilled, globally competent engineers.</p>
                    </div>
                    <div class="card">
                        <div class="modal-icon" style="color:#00b894;"><i class="fas fa-heart"></i></div>
                        <h3>Our Values</h3>
                        <p>Such an esteemed institutions are Trinity Educationaly Institutions, where excellence
                            exemplifies setting new standards in the field of academics with it continuous process to
                            its consistency.</p>
                    </div>
                </div>

                <!-- Mission Section (Split Layout) -->
                <div class="mission-wrapper" style="margin-top: 50px;">
                    <div class="mission-split-container">
                        <!-- Left Side: Visual & Title -->
                        <div class="mission-content-left">
                            <div class="section-header" style="text-align: left; margin-bottom: 30px;">
                                <span
                                    style="display: block; font-size: 14px; font-weight: 700; color: #00b894; margin-bottom: 10px; letter-spacing: 1px; text-transform: uppercase;">Our
                                    Goal</span>
                                <h2 style="margin-bottom: 15px;">Our Mission</h2>
                                <p style="font-size: 16px; margin-bottom: 0;">Driving innovation and excellence in
                                    technical education to shape the future.</p>
                            </div>
                            <!-- Generated Illustration -->
                            <img src="assets/Top Header/mission_abstract.png" alt="Mission and Growth Illustration">
                        </div>

                        <!-- Right Side: Vertical List -->
                        <div class="mission-list">
                            <!-- M1 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Accessible Education</h4>
                                    <p>Committed to make higher education available to all those who are deprived of
                                        object-oriented modular education with an emphasis on practical knowledge
                                        keeping in view the emerging industrial needs.</p>
                                </div>
                            </div>

                            <!-- M2 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Skill-Based Training</h4>
                                    <p>To provide an affordable high-quality education student centered
                                        teaching-learning processes to the professional aspirants of rural areas to
                                        impart skill-based training and achieve 100% placements.</p>
                                </div>
                            </div>

                            <!-- M3 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Conducive Atmosphere</h4>
                                    <p>To create a healthy and conducive atmosphere among the faculty, students both
                                        professionally and ethically and to have an effective interaction with industry
                                        professionals and alumni.</p>
                                </div>
                            </div>

                            <!-- M4 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-microscope"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Research & Development</h4>
                                    <p>To promote research activities among the students and to generate technically
                                        sound and highly skilled Engineers to cater the needs of the nation.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>


    <script src="js/tabs.js"></script>
    <script src="js/achievements.js"></script>
    <script src="js/slideshow.js"></script>
    <?php include 'footer.php'; ?>