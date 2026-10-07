<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Computer Science &amp; Engineering - TCEK</title>
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
                <span>Computer Science &amp; Engineering</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-laptop-code"></i> B.Tech Under Graduate Program &bull; UGC Autonomous &bull; NBA Applied
            </span>
            <h1 class="dept-hero-title">Department of Computer Science &amp; Engineering</h1>
            <p class="dept-hero-tagline">Nurturing next-generation software architects, algorithms innovators, and full-stack technocrats since 2008</p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">2008</span>
                        <span class="stat-lbl">Established</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-users"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">120 Seats</span>
                        <span class="stat-lbl">Annual Intake</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-server"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">6 Labs</span>
                        <span class="stat-lbl">Computing Centers</span>
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
                <a href="#overview" class="dept-mobile-nav-pill active"><i class="fas fa-info-circle"></i> About</a>
                <a href="#vision" class="dept-mobile-nav-pill"><i class="fas fa-bullseye"></i> Vision</a>
                <a href="#hod" class="dept-mobile-nav-pill"><i class="fas fa-user-tie"></i> HOD</a>
                <a href="#faculty" class="dept-mobile-nav-pill"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a href="#curriculum" class="dept-mobile-nav-pill"><i class="fas fa-file-pdf"></i> Syllabus</a>
                <a href="#peos" class="dept-mobile-nav-pill"><i class="fas fa-award"></i> PEOs</a>
                <a href="#gallery" class="dept-mobile-nav-pill"><i class="fas fa-images"></i> Gallery</a>
            </nav>

            <div class="dept-portal-grid">
                
                <!-- Left Sticky Sidebar -->
                <?php 
                $active_dept = 'cse';
                $dept_syllabus_link = 'assets/Dept/R22B.Tech.CSEIandIIYearSyllabus.pdf';
                $dept_syllabus_name = 'B.Tech CSE R22 Syllabus';
                $dept_peos_link = 'assets/Dept/peos_psos.docx';
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
                            The Department of Computer Science and Engineering at Trinity College of Engineering &amp; Technology (TCEK) was established in 2008 with an initial intake of 60 students, subsequently enhanced to 120 seats in 2010. Offering a comprehensive 4-year Under Graduate B.Tech degree, the department stands as one of the most prominent academic pillars of the institution.
                        </p>
                        <p class="dept-section-p">
                            The department boasts world-class infrastructure and high-speed network laboratories equipped with modern computing systems, licensed software, and gigabit fiber connectivity. We place special focus on programming fundamentals, data structures, cloud architectures, cybersecurity, distributed computing, and artificial intelligence.
                        </p>
                        <p class="dept-section-p">
                            Active industry-institute partnerships with leading tech corporations and the Telangana Academy for Skill and Knowledge (TASK) provide our students with industry-relevant internships, hands-on coding bootcamps, and top-tier campus placement placements.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-code"></i>
                                <div>
                                    <h5>Full-Stack Development</h5>
                                    <p>Comprehensive training in Java, Python, C++, Web Technologies, and Cloud Microservices.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-network-wired"></i>
                                <div>
                                    <h5>Advanced Computing Centers</h5>
                                    <p>Over 300+ networked systems with gigabit LAN and continuous power backup.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-briefcase"></i>
                                <div>
                                    <h5>Top Multinational Placements</h5>
                                    <p>Students placed regularly in TCS, Capgemini, Infosys, Genpact, and Tech Mahindra.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-certificate"></i>
                                <div>
                                    <h5>Industry Certifications</h5>
                                    <p>Curriculum integrated with NPTEL, Coursera, AWS Academy, and RedHat certifications.</p>
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
                                    To produce professional Computer Science Engineers who can meet the dynamic expectations of the global technology sector and contribute to the advancement of computing through creativity, innovation, and an outstanding learner-centric environment.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Provide practical, qualitative technical education in modern laboratory environments to solve algorithmic and real-world software problems.</li>
                                    <li>Inculcate strong foundations in computer science core principles and interdisciplinary computing domains.</li>
                                    <li>Develop domain expertise and research skills that enable graduates to pursue rewarding careers and higher education.</li>
                                    <li>Instill ethical values, leadership qualities, and professional communication among students.</li>
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
                                <img src="assets/Dept/hod-cse.jpeg" alt="Mrs. J. Swathi - HOD CSE" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Mrs. J. Swathi</h3>
                                <span class="dept-hod-desig">Head of Department &amp; Associate Professor</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> M.Tech (CSE)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-id-badge"></i> JNTUH Reg: 2717-150427-180153</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 15+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Mrs. J. Swathi is a distinguished academician, mentor, and researcher with over 15 years of rich experience in the field of Computer Science and Engineering. Serving as the Head of the Department at Trinity College of Engineering &amp; Technology, she has been instrumental in shaping academic curricula, implementing innovative teaching methods, and fostering a strong coding culture.
                                </p>
                                <p class="dept-hod-bio">
                                    Her primary areas of interest include Machine Learning, Deep Learning, Cloud Computing, and Blockchain Technology. She has actively guided numerous student capstone projects and research papers, bridging the gap between textbook concepts and industry practice.
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
                            The department is powered by experienced and devoted faculty members committed to student academic excellence and research mentorship.
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
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">SWATHI JILLA</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">HOD &amp; Assoc. Prof</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">2717-150427-180153</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">GADDAM LAKSHMI</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech, UGC NET, (Ph.D)</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7259-150409-113641</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SYED KHAJAPASHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0802-170126-125051</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">ANJALI KOMUROJU</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">4513-190506-102113</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MOHAMMAD ZIAUDDIN</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5682-170203-164956</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SYED MEER SUBHAN ALI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5682-170203-164956</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">KOTTE ANUSHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5682-170203-164956</span></td>
                                        <td data-label="Experience">5 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">PALLA SWATHI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5682-170203-164956</span></td>
                                        <td data-label="Experience">5 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">9</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">JITTAVENI SRINIVAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5682-170203-164956</span></td>
                                        <td data-label="Experience">3 Years</td>
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
                            The curriculum follows the JNTUH R22 regulation with continuous upgrades in accordance with UGC Autonomous guidelines, incorporating modern Industry 4.0 computing courses.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>B.Tech CSE R22 Syllabus</h4>
                                        <p>Comprehensive year-wise and semester-wise subject codes, credits, and evaluation scheme.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/R22B.Tech.CSEIandIIYearSyllabus.pdf" target="_blank" class="dept-btn-download">
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
                                <a href="assets/Dept/peos_psos.docx" target="_blank" class="dept-btn-download theme-blue">
                                    <i class="fas fa-eye"></i> View PEOs Document
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Section 6: PEOs & PSOs -->
                    <section class="dept-section-card" id="peos">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-award"></i></div>
                            <h2>Program Educational Objectives (PEOs &amp; PSOs)</h2>
                        </div>
                        <div class="dept-vision-mission-grid">
                            <div class="dept-vm-card">
                                <div class="dept-vm-title">
                                    <i class="fas fa-crosshairs"></i>
                                    <span>Program Educational Objectives</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PEO 1:</strong> Graduates will establish successful careers in software engineering, cloud computing, and IT leadership roles globally.</li>
                                    <li><strong>PEO 2:</strong> Graduates will demonstrate expertise in developing robust algorithmic solutions to complex multidisciplinary challenges.</li>
                                    <li><strong>PEO 3:</strong> Graduates will practice professional and ethical responsibility, fostering lifelong learning through research and certifications.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Ability to apply design and development principles in constructing software systems of varying complexity.</li>
                                    <li><strong>PSO 2:</strong> Proficiency in utilizing modern computing environments, cloud platforms, and cybersecurity protocols.</li>
                                    <li><strong>PSO 3:</strong> Competence to analyze, design, and model computer hardware and software interfaces for enterprise systems.</li>
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
                            State-of-the-art computer centers, student hackathons, code clinics, and academic workshops.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="CSE Department Laboratories &amp; Events" loading="lazy">
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
