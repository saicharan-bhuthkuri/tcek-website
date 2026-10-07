<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Computer Science and Engineering (AIML) - TCEK</title>
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
                <span>Computer Science &amp; Engineering (AIML)</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-robot"></i> B.Tech Under Graduate Program &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of Computer Science &amp; Engineering (AIML)</h1>
            <p class="dept-hero-tagline">Pioneering intelligent algorithms, neural architectures, data intelligence, and autonomous computing for Industry 4.0</p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">2021</span>
                        <span class="stat-lbl">Established</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">60 Seats</span>
                        <span class="stat-lbl">Annual Intake</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-laptop-code"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">AI Lab Suite</span>
                        <span class="stat-lbl">High-Speed Computing</span>
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
                $active_dept = 'aiml';
                $dept_syllabus_link = 'assets/Dept/R22B.Tech.AIMLIandIIYearSyllabus.pdf';
                $dept_syllabus_name = 'B.Tech AIML R22 Syllabus';
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
                            The Department of Artificial Intelligence and Machine Learning at Trinity College of Engineering &amp; Technology (TCEK) was established in 2021 with an approved intake of 60 students. Offered as a premier 4-year Under Graduate B.Tech program, the department caters to the explosive global demand for AI engineers, machine learning scientists, and data architects.
                        </p>
                        <p class="dept-section-p">
                            The department is equipped with state-of-the-art computing laboratories powered by multi-core workstations, high-speed fiber internet, and specialized AI development environments. We emphasize deep mathematical foundations, Python/PyTorch/TensorFlow engineering, cloud computing, generative AI, and computer vision systems.
                        </p>
                        <p class="dept-section-p">
                            Our faculty members are deeply committed to blending research-driven pedagogy with real-world application building. The department regularly collaborates with industry leaders, sponsoring student hackathons, open-source AI projects, and technology internships.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-brain"></i>
                                <div>
                                    <h5>Deep Learning &amp; Neural Nets</h5>
                                    <p>Comprehensive curriculum covering CNNs, RNNs, Transformers, and LLMs.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-microchip"></i>
                                <div>
                                    <h5>High-Compute Workstations</h5>
                                    <p>Dedicated laboratory infrastructure for training intensive machine learning models.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-handshake"></i>
                                <div>
                                    <h5>Industry Collaborations</h5>
                                    <p>Active tie-ups with tech enterprises, TASK, and AI hackathons for practical immersion.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-rocket"></i>
                                <div>
                                    <h5>Capstone Project Mentorship</h5>
                                    <p>Guidance from faculty and corporate mentors on real-world AI applications.</p>
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
                                    To become a regional leader in providing high-quality education in the field of Artificial Intelligence and Machine Learning, nurturing students to compete globally through curricula that impart sound theoretical foundations, hands-on experiential learning, and the socio-ethical values required to make transformative contributions to humanity.
                                </p>
                            </div>
                            <div class="dept-vm-card">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Provide state-of-the-art computational infrastructure and expert instruction in advanced AI algorithms and emerging data technologies.</li>
                                    <li>Foster a culture of interdisciplinary research, industrial problem-solving, and continuous entrepreneurial innovation.</li>
                                    <li>Instill high ethical standards, social empathy, collaborative teamwork, and lifelong learning competencies among aspiring technocrats.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Section 3: Head of the Department -->
                    <section class="dept-section-card" id="hod">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-user-tie"></i></div>
                            <h2>Head of the Department</h2>
                        </div>
                        <div class="dept-hod-showcase">
                            <div class="dept-hod-avatar-wrap">
                                <img src="assets/Dept/hod-cse.jpeg" alt="Mrs. J. Swathi, HOD CSE &amp; CSE(AIML)" class="dept-hod-photo">
                                <div class="dept-hod-badge">
                                    <i class="fas fa-check-circle"></i> Department Head
                                </div>
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
                                    Mrs. J. Swathi is a distinguished academician, mentor, and researcher with over 15 years of rich experience in the field of Computer Science and Engineering. Serving as the Head of the Department at Trinity College of Engineering &amp; Technology, she has been instrumental in shaping academic curricula, implementing innovative teaching methods, and fostering advanced technical learning.
                                </p>
                                <p class="dept-hod-bio">
                                    Her primary research domains include Machine Learning, Deep Neural Architectures, Cloud Computing, and Intelligent Computing Systems. She actively guides students in capstone software projects, AI implementations, and research publications, bridging academic theory with industry requirements.
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
                            Our faculty members bring diverse academic specializations, research acumen, and a passion for student success.
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
                                        <td data-label="Experience">15+ Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">G ANJANEYULU</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MCA, M.Tech, MA ENG, B.Ed</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">2439-200306-160524</span></td>
                                        <td data-label="Experience">16 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MARUPAKA AMULYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0352-170126-131206</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MOHD ASEEM FEROZE</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3136-160107-104243</span></td>
                                        <td data-label="Experience">9 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">POODARI LAVANYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3591-170205-154548</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">NAJIRIN</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7575-200210-131406</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SANDHYARANI ADAPA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7461-200227-134103</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Section 5: Curriculum & Syllabus -->
                    <section class="dept-section-card" id="curriculum">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-file-pdf"></i></div>
                            <h2>Curriculum &amp; Syllabus</h2>
                        </div>
                        <p class="dept-section-p">
                            The curriculum is structured following JNTUH and AICTE model regulations, integrating core computing principles, applied data science, neural networks, natural language processing, and advanced machine learning laboratories.
                        </p>
                        
                        <div class="dept-doc-download-card">
                            <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                            <div class="dept-doc-meta">
                                <h4>B.Tech AI &amp; ML (R22 Regulations)</h4>
                                <p>Comprehensive I &amp; II Year syllabus, course structure, credits, and laboratory scheme approved by JNTUH.</p>
                            </div>
                            <a href="assets/Dept/R22B.Tech.AIMLIandIIYearSyllabus.pdf" target="_blank" class="dept-download-action-btn">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </section>

                    <!-- Section 6: PEOs, POs & PSOs -->
                    <section class="dept-section-card" id="peos">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-award"></i></div>
                            <h2>Program Educational Objectives (PEOs) &amp; PSOs</h2>
                        </div>
                        <p class="dept-section-p">
                            Our educational objectives define what graduates are expected to attain within a few years of graduation, aligning academic rigor with industry standards.
                        </p>
                        <div class="dept-doc-download-card">
                            <div class="dept-doc-icon" style="background: rgba(9, 132, 227, 0.1); color: #0984e3;"><i class="fas fa-file-alt"></i></div>
                            <div class="dept-doc-meta">
                                <h4>PEOs, POs &amp; PSOs Document</h4>
                                <p>Program Outcomes, Educational Objectives, and Specific Outcomes document for the B.Tech Under Graduate program.</p>
                            </div>
                            <a href="assets/Dept/peos_psos.docx" target="_blank" class="dept-download-action-btn" style="background: #0984e3;">
                                <i class="fas fa-download"></i> View Document
                            </a>
                        </div>
                    </section>

                    <!-- Section 7: Department Gallery -->
                    <section class="dept-section-card" id="gallery">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-images"></i></div>
                            <h2>Department Photo Gallery</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 20px;">
                            Glimpses of lab sessions, technical hackathons, guest lectures, and student celebrations in the department.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $aiml_images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($aiml_images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="AIML Department Activity" loading="lazy">
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
