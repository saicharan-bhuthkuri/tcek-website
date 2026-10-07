<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Business Administration (MBA) - TCEK</title>
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="css/department.css">
</head>
<body>
    <?php $page = 'departments'; include 'header.php'; ?>

    <!-- Department Hero Header -->
    <header class="dept-portal-hero">
        <div class="container">
            <div class="dept-hero-breadcrumbs">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <a href="departments.php">Departments</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <span>Master of Business Administration</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-briefcase"></i> Postgraduate Management Program &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of Business Administration (MBA)</h1>
            <p class="dept-hero-tagline">Shaping future corporate leaders, entrepreneurs, financial analysts, and marketing visionaries</p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">MBA</span>
                        <span class="stat-lbl">2-Year PG Degree</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">Dual Specialization</span>
                        <span class="stat-lbl">Finance, HR &amp; Mktg</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-handshake"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">100%</span>
                        <span class="stat-lbl">Placement Support</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-award"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">TCEK</span>
                        <span class="stat-lbl">ICET Code</span>
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
                <a href="#overview" class="dept-mobile-nav-pill active"><i class="fas fa-info-circle"></i> About</a>
                <a href="#vision" class="dept-mobile-nav-pill"><i class="fas fa-bullseye"></i> Vision</a>
                <a href="#hod" class="dept-mobile-nav-pill"><i class="fas fa-user-tie"></i> HOD</a>
                <a href="#faculty" class="dept-mobile-nav-pill"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a href="#curriculum" class="dept-mobile-nav-pill"><i class="fas fa-file-pdf"></i> Syllabus</a>
                <a href="#peos" class="dept-mobile-nav-pill"><i class="fas fa-award"></i> POs</a>
                <a href="#gallery" class="dept-mobile-nav-pill"><i class="fas fa-images"></i> Gallery</a>
            </nav>

            <div class="dept-portal-grid">
                
                <!-- Left Sticky Sidebar -->
                <?php 
                $active_dept = 'mba';
                $dept_syllabus_link = 'assets/Dept/MBA_Syllabus.pdf';
                $dept_syllabus_name = 'MBA Curriculum Syllabus';
                $dept_peos_link = 'assets/Dept/mba_pos.docx';
                include 'dept-sidebar.php'; 
                ?>

                <!-- Right Main Content -->
                <div class="dept-main-content">

                    <!-- Section 1: Overview / About -->
                    <section class="dept-section-card" id="overview">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-info-circle"></i></div>
                            <h2>About the Department</h2>
                        </div>
                        <p class="dept-section-p">
                            The Department of Business Administration at Trinity College of Engineering &amp; Technology offers a prestigious 2-year full-time MBA program affiliated to JNTUH and recognized under UGC Autonomous guidelines. The program is designed to develop strategic management acumen, entrepreneurial spirit, and ethical decision-making abilities in aspiring business leaders.
                        </p>
                        <p class="dept-section-p">
                            Offering dual specializations across Financial Management, Human Resource Management, Marketing, and Business Analytics, our curriculum blends case-study methodology, industry live projects, corporate guest lectures, and executive simulation workshops.
                        </p>
                        <p class="dept-section-p">
                            With dedicated soft-skills training, business communication clinics, and intensive pre-placement grooming, TCEK MBA graduates consistently secure managerial and executive positions in leading banking, financial services, IT, and retail multinationals.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-coins"></i>
                                <div>
                                    <h5>Dual Specializations</h5>
                                    <p>Flexible combinations in Finance, Human Resources, Marketing, and Systems.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-chart-pie"></i>
                                <div>
                                    <h5>Case Study Pedagogy</h5>
                                    <p>Harvard-style real-world corporate case analyses and management games.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <h5>Corporate Internships</h5>
                                    <p>Mandatory summer internships with leading banks, financial institutions, and FMCG brands.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-lightbulb"></i>
                                <div>
                                    <h5>Entrepreneurship Cell</h5>
                                    <p>Active incubation support for startup ventures and business plan competitions.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Section 2: Vision & Mission -->
                    <section class="dept-section-card" id="vision">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-bullseye"></i></div>
                            <h2>Vision &amp; Mission</h2>
                        </div>
                        <div class="dept-vision-mission-grid">
                            <div class="dept-vm-card">
                                <div class="dept-vm-title">
                                    <i class="fas fa-eye"></i>
                                    <span>Department Vision</span>
                                </div>
                                <p>
                                    To emerge as a center of excellence meeting global standards in corporate business practices, entrepreneurship, and management research through a values-based educational ecosystem.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Transform student careers through values-based and industry-aligned management education.</li>
                                    <li>Enhance corporate employability and entrepreneurial competencies through experiential learning.</li>
                                    <li>Promote innovation, research acumen, and analytical problem-solving for enterprise leadership.</li>
                                    <li>Inculcate ethical business governance, social responsibility, and sustainable leadership values.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Section 3: Head of the Department (HOD) -->
                    <section class="dept-section-card" id="hod">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-user-tie"></i></div>
                            <h2>Head of the Department</h2>
                        </div>
                        <div class="dept-hod-showcase">
                            <div class="dept-hod-photo-wrap">
                                <img src="assets/Dept/hod-mba.jpg" alt="Dr. Arif Arafat - HOD MBA" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Dr. Arif Arafat</h3>
                                <span class="dept-hod-desig">Head of Department &amp; Associate Professor</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> MBA, M.Com, Ph.D</span>
                                    <span class="dept-cred-chip"><i class="fas fa-award"></i> UGC NET &amp; SET Qualified</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 17+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Dr. Arif Arafat, Head of the Department of MBA, possesses over 17 years of rich professional experience spanning corporate sales management, academic instruction, and executive development. He completed his MBA in 2006 and was awarded his Doctorate (Ph.D) in 2019.
                                </p>
                                <p class="dept-hod-bio">
                                    A qualified UGC NET and SET scholar, Dr. Arafat specializes in organizational behavior, marketing strategy, and student training. He has published numerous research papers in Scopus-indexed and UGC-CARE journals and actively serves as the Training &amp; Placement coordinator for corporate drives.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Section 4: Faculty Directory -->
                    <section class="dept-section-card" id="faculty">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-chalkboard-teacher"></i></div>
                            <h2>Faculty Directory</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 20px;">
                            Distinguished management faculty combining corporate experience, doctorate qualifications, and dedicated mentorship.
                        </p>
                        <div class="dept-table-container">
                            <table class="dept-faculty-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">S.No</th>
                                        <th>Name of the Faculty</th>
                                        <th>Designation</th>
                                        <th>Qualification</th>
                                        <th>JNTUH Reg. ID</th>
                                        <th>Experience</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="S.No">1</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. ARIF ARFAT</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">HOD &amp; Assoc. Prof</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA, M.Com, NET, SET, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0506-150408-132239</span></td>
                                        <td data-label="Experience">17 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. AMBIKA</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0506-150408-132239</span></td>
                                        <td data-label="Experience">14 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. RAJIDI RAMMOHAN REDDY</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA, NET, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3696-150422-172652</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">PARSHA RAMESH</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">9898-170915-153011</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">VENKATESWARLU NAGULA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">2590-150415-181625</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">VAMSI KRISHNA BARIBADDALA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA, M.Com, SET</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">52150405-120550</span></td>
                                        <td data-label="Experience">11 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">NASPURI SAI SHRAVANI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5545-220124-130408</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SRI LATHA THANGELLAPALLI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MBA</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0755-150408-1404212</span></td>
                                        <td data-label="Experience">5 Years</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Section 5: Syllabus & Curriculum -->
                    <section class="dept-section-card" id="curriculum">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-file-pdf"></i></div>
                            <h2>Curriculum &amp; Syllabus</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 22px;">
                            JNTUH and UGC Autonomous MBA curriculum covering core managerial modules, functional electives, seminar presentations, and corporate projects.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>MBA Program Syllabus</h4>
                                        <p>Complete course structure, credits, internal assessment scheme, and elective subjects.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/MBA_Syllabus.pdf" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>Program Outcomes (POs)</h4>
                                        <p>Managerial competency outcomes and learning matrix for MBA graduates.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/mba_pos.docx" target="_blank" class="dept-btn-download theme-blue">
                                    <i class="fas fa-eye"></i> View Program Outcomes
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Section 6: Program Outcomes (POs) -->
                    <section class="dept-section-card" id="peos">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-award"></i></div>
                            <h2>Program Outcomes (POs)</h2>
                        </div>
                        <div class="dept-vision-mission-grid">
                            <div class="dept-vm-card">
                                <div class="dept-vm-title">
                                    <i class="fas fa-crosshairs"></i>
                                    <span>Management Program Objectives</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PO 1:</strong> Apply knowledge of management theories and practices to solve business problems.</li>
                                    <li><strong>PO 2:</strong> Foster analytical and critical thinking abilities for data-driven decision making.</li>
                                    <li><strong>PO 3:</strong> Develop value-based leadership ability and entrepreneurial spirit.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Competencies</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PO 4:</strong> Understand, analyze, and communicate global economic, legal, and ethical business contexts.</li>
                                    <li><strong>PO 5:</strong> Lead and work effectively in diverse, multidisciplinary business teams.</li>
                                    <li><strong>PO 6:</strong> Demonstrate proficiency in digital business analytics tools and modern enterprise systems.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Section 7: Laboratories & Gallery -->
                    <section class="dept-section-card" id="gallery">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-images"></i></div>
                            <h2>Department Labs &amp; Gallery</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 20px;">
                            Management symposiums, business plan presentations, guest lectures, and campus recruitment activities.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="MBA Department Events &amp; Sessions" loading="lazy">
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
