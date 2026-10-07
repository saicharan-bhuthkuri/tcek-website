<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Artificial Intelligence &amp; Machine Learning - TCEK</title>
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
                <span>Artificial Intelligence &amp; Machine Learning</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-robot"></i> B.Tech Under Graduate Program &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of Artificial Intelligence &amp; Machine Learning</h1>
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
                                    To emerge as a regional center of academic and technological excellence in Artificial Intelligence and Machine Learning, nurturing socially conscious, technically competent, and innovative engineers capable of solving complex global problems and driving Industry 4.0 innovations.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Provide comprehensive education blending theoretical foundations with hands-on implementation of AI algorithms.</li>
                                    <li>Establish state-of-the-art laboratory infrastructure and research ecosystems for advanced technological development.</li>
                                    <li>Foster industry-institute collaborations, entrepreneurship, and interdisciplinary problem solving.</li>
                                    <li>Instill strong ethical values, social responsibility, and lifelong learning attitudes in every graduate.</li>
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
                                <img src="assets/Dept/Hod-aiml.jpg" alt="Prof. Gaddam Lakshmi - HOD AIML" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Prof. Gaddam Lakshmi</h3>
                                <span class="dept-hod-desig">Head of Department &amp; Associate Professor</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> M.Tech (CSE)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-award"></i> UGC NET Qualified</span>
                                    <span class="dept-cred-chip"><i class="fas fa-book-reader"></i> (Ph.D) Pursuing</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 15+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Prof. Gaddam Lakshmi, Head of the Department of Artificial Intelligence &amp; Machine Learning (AIML), is a seasoned academician and researcher with over 15 years of distinguished service in higher technical education. She holds an M.Tech in Computer Science &amp; Engineering, has qualified the prestigious UGC NET, and is actively pursuing her doctoral research.
                                </p>
                                <p class="dept-hod-bio">
                                    Her research domains include Artificial Intelligence, Machine Learning Algorithms, Data Analytics, and Intelligent Computing Systems. Under her leadership, the department emphasizes practical problem-solving, laboratory excellence, and tailored mentorship to groom students into competitive technocrats ready for top global tech careers.
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
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">GADDAM LAKSHMI</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">HOD &amp; Assoc. Prof</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech, UGC NET, (Ph.D)</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7259-150409-113641</span></td>
                                        <td data-label="Experience">15+ Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">G ANJANEYULU</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">MCA, M.Tech, MA ENG, B.Ed</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">2439-200306-160524</span></td>
                                        <td data-label="Experience">16 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MARUPAKA AMULYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0352-170126-131206</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MOHD ASEEM FEROZE</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3136-160107-104243</span></td>
                                        <td data-label="Experience">9 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">POODARI LAVANYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3591-170205-154548</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">NAJIRIN</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7575-200210-131406</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
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

                    <!-- Section 5: Syllabus & Curriculum -->
                    <section class="dept-section-card" id="curriculum">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-file-pdf"></i></div>
                            <h2>Curriculum &amp; Syllabus</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 22px;">
                            The curriculum is structured around outcome-based education (OBE) benchmarks, aligning with UGC Autonomous guidelines and the JNTUH R22 academic regulation.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>B.Tech AIML R22 Syllabus</h4>
                                        <p>Complete course structure, subject codes, weekly credits, and semester-wise evaluation scheme.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/R22B.Tech.AIMLIandIIYearSyllabus.pdf" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>Course Structure &amp; Scheme</h4>
                                        <p>Core electives, professional open electives, and mandatory lab practice guidelines.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/R22B.Tech.CSE(AIML)CourseStructureSyllabus2.pdf" target="_blank" class="dept-btn-download theme-blue">
                                    <i class="fas fa-eye"></i> View Course Scheme
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
                                    <li><strong>PEO 1:</strong> Graduates will excel in software development, data science, and intelligent automation careers in multinational corporations.</li>
                                    <li><strong>PEO 2:</strong> Graduates will pursue higher studies, certifications, and research in emerging machine learning frontiers.</li>
                                    <li><strong>PEO 3:</strong> Graduates will practice professional ethics, teamwork, and effective communication in cross-functional engineering teams.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Ability to formulate, design, and train mathematical and computational AI models for real-world datasets.</li>
                                    <li><strong>PSO 2:</strong> Competence in utilizing standard ML/AI libraries, frameworks, cloud infrastructures, and edge AI deployment pipelines.</li>
                                    <li><strong>PSO 3:</strong> Proficiency in developing intelligent autonomous systems addressing societal and industrial engineering problems.</li>
                                </ul>
                            </div>
                        </div>
                        <div style="margin-top: 24px; text-align: center;">
                            <a href="assets/Dept/peos_psos.docx" target="_blank" class="dept-btn-download theme-blue">
                                <i class="fas fa-file-download"></i> Download Complete PEOs &amp; PSOs Document (.docx)
                            </a>
                        </div>
                    </section>

                    <!-- Section 7: Laboratories & Gallery -->
                    <section class="dept-section-card" id="gallery">
                        <div class="dept-section-head">
                            <div class="dept-section-icon-badge"><i class="fas fa-images"></i></div>
                            <h2>Department Labs &amp; Gallery</h2>
                        </div>
                        <p class="dept-section-p" style="margin-bottom: 20px;">
                            Snapshots from our advanced computing laboratories, technical project workshops, and departmental events.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="AIML Department Laboratory &amp; Activities" loading="lazy">
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
