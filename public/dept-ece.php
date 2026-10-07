<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Electronics &amp; Communication Engineering - TCEK</title>
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
                <span>Electronics &amp; Communication Engineering</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-microchip"></i> B.Tech Under Graduate Program &bull; UGC Autonomous &bull; NBA Applied
            </span>
            <h1 class="dept-hero-title">Department of Electronics &amp; Communication Engineering</h1>
            <p class="dept-hero-tagline">Empowering hardware technocrats in VLSI, Embedded Systems, IoT, Microwave, and Wireless Communications since 2008</p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">2008</span>
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
                    <div class="dept-hero-stat-icon"><i class="fas fa-satellite-dish"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">VLSI &amp; DSP</span>
                        <span class="stat-lbl">Advanced Labs</span>
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
                $active_dept = 'ece';
                $dept_syllabus_link = 'assets/Dept/R22B.Tech.ECEIIIYearSyllabus.pdf';
                $dept_syllabus_name = 'B.Tech ECE R22 Syllabus';
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
                            The Department of Electronics and Communication Engineering (ECE) at Trinity College of Engineering &amp; Technology was established right from the inception of the college in 2008 with an approved intake of 60 students. The primary objective of the department is to impart quality education, robust practical training, and interdisciplinary research in electronic circuits, signal processing, and communication networks.
                        </p>
                        <p class="dept-section-p">
                            Our laboratories are fully outfitted with modern test and measurement equipment including digital storage oscilloscopes (DSOs), spectrum analyzers, FPGA/CPLD development kits, ARM microcontroller trainers, and industry-standard EDA tools for VLSI simulation.
                        </p>
                        <p class="dept-section-p">
                            With expanding applications in autonomous electric vehicles, 5G/6G telecommunications, Internet of Things (IoT), and defense electronics, the ECE curriculum prepares students for both premier core engineering giants and top software enterprises.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-microchip"></i>
                                <div>
                                    <h5>VLSI &amp; Embedded Systems</h5>
                                    <p>Comprehensive hands-on training with Verilog, VHDL, FPGA kits, and microcontrollers.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-broadcast-tower"></i>
                                <div>
                                    <h5>Wireless &amp; 5G Communications</h5>
                                    <p>Digital communication trainers, optical fiber test benches, and microwave benches.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-industry"></i>
                                <div>
                                    <h5>Core Industry Placements</h5>
                                    <p>Pathways to core electronic majors, semiconductor companies, and telecom providers.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-robot"></i>
                                <div>
                                    <h5>IoT &amp; Robotics Prototyping</h5>
                                    <p>Active student innovation lab for IoT sensor networks and autonomous hardware.</p>
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
                                    To be recognized as a pioneer in the field of Electronics and Communication Engineering by providing high-quality education, fostering innovation, and concentrating research efforts on emerging technologies for the betterment of society.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Impart quality education with strong fundamentals in the field of Electronics and Communication Engineering.</li>
                                    <li>Inculcate teamwork, lifelong learning attitudes, and ethical values to meet dynamic industry and societal expectations.</li>
                                    <li>Enable students with advanced problem-solving skills through collaborative and multidisciplinary research projects.</li>
                                    <li>Bridge academia and industry through guest lectures, industrial visits, and certified internships.</li>
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
                                <img src="assets/Dept/hod-ece.jpeg" alt="Mr. P. Prabhakar - HOD ECE" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Mr. P. Prabhakar</h3>
                                <span class="dept-hod-desig">Head of Department &amp; Associate Professor</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> M.Tech (ECE)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-id-badge"></i> JNTUH Reg: 0117-150505-114948</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 20+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Welcome to the exciting world of Electronics and Communication Engineering at TCEK! As you embark on this transformative journey, you join a vibrant community of passionate learners, innovative thinkers, and dedicated educators.
                                </p>
                                <p class="dept-hod-bio">
                                    With over 20 years of teaching and mentoring experience, Mr. P. Prabhakar leads the department with a sharp focus on hands-on hardware laboratory training, VLSI chip design, embedded IoT prototyping, and signal processing. Under his direction, students actively take part in national symposiums, hackathons, and industrial field visits.
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
                            Meet our distinguished faculty team comprising doctorates, seasoned post-graduates, and industry veterans.
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
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. M GANESH</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Professor &amp; Principal</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">6533-170912-154901</span></td>
                                        <td data-label="Experience">12 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">PRABHAKAR PARLAPALLI</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">HOD &amp; Assoc. Prof</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0117-150505-114948</span></td>
                                        <td data-label="Experience">20 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">GANTLA LAVANYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">1663-170203-183207</span></td>
                                        <td data-label="Experience">6 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">VODNALA MAMATHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">1235-170122-184453</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">MADHU SHEKAR PITTALA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0762-150425-103028</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SHAHJAHAN</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">6533-170912-154901</span></td>
                                        <td data-label="Experience">10 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">BURRA SRUJANA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">3645-170918-134720</span></td>
                                        <td data-label="Experience">4 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">DAMA SAMPATH</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0534-160311-182640</span></td>
                                        <td data-label="Experience">6 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">9</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">B RAJENDAR</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Associate Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">6533-170912-154901</span></td>
                                        <td data-label="Experience">12 Years</td>
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
                            Official academic syllabus structure under JNTUH R22 and UGC Autonomous guidelines, tailored for core hardware competence and electronics innovation.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>B.Tech ECE R22 Syllabus</h4>
                                        <p>Detailed semester-wise breakdown of courses, labs, electives, and credit distribution.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/R22B.Tech.ECEIIIYearSyllabus.pdf" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>PEOs &amp; PSOs Document</h4>
                                        <p>Comprehensive statement of educational objectives and program outcomes.</p>
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
                                    <li><strong>PEO 1:</strong> Graduates will excel in electronics design, telecommunications, VLSI engineering, and embedded firmware roles.</li>
                                    <li><strong>PEO 2:</strong> Graduates will adapt to emerging communication technologies through lifelong learning and advanced studies.</li>
                                    <li><strong>PEO 3:</strong> Graduates will exhibit professional ethics, interdisciplinary collaboration, and effective technical leadership.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Ability to analyze, design, and simulate analog and digital electronic circuits and VLSI subsystems.</li>
                                    <li><strong>PSO 2:</strong> Competence in developing micro-controller based embedded systems, IoT modules, and sensor nodes.</li>
                                    <li><strong>PSO 3:</strong> Proficiency in evaluating signal processing algorithms and RF/wireless transmission systems.</li>
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
                            Glimpses of our electronic test facilities, microwave labs, embedded microcontroller workstations, and student workshops.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="ECE Department Laboratories &amp; Events" loading="lazy">
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
