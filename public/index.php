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

        <!-- Why Us Section -->
        <section style="background: #fdfdfd;">
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

                <!-- Mission Section -->
                <!-- Mission Section Redesign -->
                <!-- Mission Section Redesign (Split Layout) -->
                <div class="mission-wrapper">
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
            </div>
            </div>
        </section>

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

        <!-- Achievements Section -->
        <section id="achievements">
            <div class="container">
                <div class="section-header">
                    <h2>Our Achievements</h2>
                    <p>Celebrating the success stories of our brilliant students</p>
                </div>

                <div class="achievements-container">
                    <div class="achievement-slider">
                        <!-- Slide 1: Zainab Khatoon -->
                        <div class="achievement-slide active">
                            <div class="achievement-content">
                                <div class="ach-image">
                                    <img src="assets/Achievements/zainab_tcs.png" alt="Zainab Khatoon - TCS">
                                </div>
                                <div class="ach-text">
                                    <div class="ach-quote">
                                        "As a CSE student at our institution, I, Zainab Khatoon, am thrilled to share my
                                        positive experience with our campus placement services. The dedicated training
                                        and comprehensive support provided by our institution have been instrumental in
                                        shaping my career. The focus on practical skills, mock interviews, and
                                        industry-specific knowledge has thoroughly prepared me for the job market.
                                        Thanks to these efforts, I secured a placement with TCS. The campus environment
                                        fosters continuous learning and professional growth, making it an ideal place
                                        for aspiring engineers. I am proud to be part of an institution that prioritizes
                                        student success and career readiness."
                                    </div>
                                    <div class="ach-author">
                                        <h4>Zainab Khatoon</h4>
                                        <p>CSE - Placed in TCS (3.6 LPA)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: G. Bhavitha -->
                        <div class="achievement-slide">
                            <div class="achievement-content">
                                <div class="ach-image">
                                    <img src="assets/Achievements/bhavitha_capgemini.png" alt="G. Bhavitha - Capgemini">
                                </div>
                                <div class="ach-text">
                                    <div class="ach-quote">
                                        "As a CSE student at our institution, I, Bhavitha, can confidently say that our
                                        campus placement services are exceptional. The training and support we receive
                                        are tailored to ensure we are well-prepared for the job market. From enhancing
                                        our technical skills to providing interview preparation, the focus on our future
                                        careers is evident. Thanks to these efforts, I was successfully placed in
                                        Capgemini. The campus environment is conducive to learning and growth, with
                                        resources readily available to help us succeed. I am proud to be a part of this
                                        institution, where the emphasis on placements truly sets us apart."
                                    </div>
                                    <div class="ach-author">
                                        <h4>G. Bhavitha</h4>
                                        <p>CSE - Placed in Capgemini (4 LPA)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button class="ach-nav-btn ach-prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="ach-nav-btn ach-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </section>

    </main>


    <script src="js/tabs.js"></script>
    <script src="js/achievements.js"></script>
    <script src="js/slideshow.js"></script>
    <?php include 'footer.php'; ?>