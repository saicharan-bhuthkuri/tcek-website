<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Electrical &amp; Electronics Engineering - TCEK</title>
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
                <span>Electrical &amp; Electronics Engineering</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-bolt"></i> B.Tech Under Graduate Program &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of Electrical &amp; Electronics Engineering</h1>
            <p class="dept-hero-tagline">Empowering electrical engineers in Power Systems, Renewable Energy, Electric Vehicles, Control Systems, and Smart Grids since 2008</p>
            
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
                    <div class="dept-hero-stat-icon"><i class="fas fa-plug"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">5 Heavy Labs</span>
                        <span class="stat-lbl">Machines &amp; Power Labs</span>
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
                $active_dept = 'eee';
                $dept_syllabus_link = 'assets/Dept/R22B.Tech.EEEIandIIYearSyllabus2.pdf';
                $dept_syllabus_name = 'B.Tech EEE R22 Syllabus';
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
                            The Department of Electrical and Electronics Engineering (EEE) at Trinity College of Engineering &amp; Technology was established in 2008. The department is dedicated to providing comprehensive education, laboratory training, and industrial exposure to produce competent electrical engineers who can drive power infrastructure and technological breakthroughs.
                        </p>
                        <p class="dept-section-p">
                            The department is equipped with expansive laboratories including Electrical Machines Lab, Power Electronics &amp; Drives Lab, Power Systems Simulation Lab, Control Systems Lab, and Electrical Measurements Lab. Our curriculum covers traditional electric grid infrastructure as well as contemporary domains such as Electric Vehicle (EV) powertrains, Solar Photovoltaic Systems, and Smart Microgrids.
                        </p>
                        <p class="dept-section-p">
                            To ensure practical industry immersion, the department routinely arranges technical industrial visits to regional hydro power stations (such as TGGENCO Chandapally Mini Hydel Station), 132kV/220kV substations, and thermal generation hubs.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-charging-station"></i>
                                <div>
                                    <h5>Electric Vehicles &amp; Clean Energy</h5>
                                    <p>Practical focus on EV motor drives, battery management, and solar renewable energy.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-cogs"></i>
                                <div>
                                    <h5>Heavy Electrical Machines Labs</h5>
                                    <p>Comprehensive AC/DC motors, alternators, transformers, and load testing panels.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-industry"></i>
                                <div>
                                    <h5>Substation &amp; Plant Visits</h5>
                                    <p>Regular field visits to state electricity generation and transmission facilities.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <h5>Power Protection &amp; Control</h5>
                                    <p>Training on numerical relays, switchgear, PLC automation, and SCADA systems.</p>
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
                                    To be recognized as a premier center of excellence in Electrical and Electronics Engineering, producing socially responsible technocrats equipped with cutting-edge technical expertise, innovation, and ethical integrity for sustainable energy and automation.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Provide strong foundational education in electrical sciences, power generation, and control engineering.</li>
                                    <li>Equip laboratories with modern industrial equipment and simulation tools for comprehensive experiential learning.</li>
                                    <li>Foster industry-readiness through industrial training, plant visits, and real-time engineering projects.</li>
                                    <li>Promote research and innovation in green energy, electric mobility, and energy-efficient technologies.</li>
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
                                <img src="assets/Dept/hod-eee.jpeg" alt="Dr. K. Natarajan - HOD EEE" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Dr. K. Natarajan</h3>
                                <span class="dept-hod-desig">Professor &amp; Head of Department</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> Ph.D (Anna University)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-certificate"></i> M.E (PSG Tech)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 14+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Dr. K. Natarajan obtained his B.E in Electrical &amp; Electronics Engineering from Sri Ramakrishna Institute of Technology, Coimbatore and M.E in Electrical Machines from the renowned PSG College of Technology, Coimbatore. He completed his Ph.D degree from Anna University, Chennai in 2017.
                                </p>
                                <p class="dept-hod-bio">
                                    With over 14 years of teaching and research experience at undergraduate and postgraduate levels, Dr. Natarajan has guided numerous student engineering projects, published articles in prestigious international journals, and spearheaded laboratory modernization in renewable energy and power electronics.
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
                            Experienced academicians with deep industry and academic competencies guiding our students across all electrical specializations.
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
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. K. NATARAJAN</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">Professor &amp; HOD</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.E, Ph.D</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7515-170420-141201</span></td>
                                        <td data-label="Experience">14+ Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">VAMSHI KRISHNA KOMURAVELLI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">75150405-174823</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SHIVA KUMAR MUNJAM</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7734-150412-151225</span></td>
                                        <td data-label="Experience">20 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SANTHOSH BANDI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">4224-150416-153422</span></td>
                                        <td data-label="Experience">16 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">ARPULA KRISHNAIAH</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">1777-150419-12802</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">KOPPULA SRINIVAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">0016-170119-101553</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">CHUNDURI SUPRIYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">7565-180130-151342</span></td>
                                        <td data-label="Experience">12 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">VELPULA SWARUPA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">8793-170126-091552</span></td>
                                        <td data-label="Experience">8 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">9</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">ASHOK KUMAR GUDA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Reg ID"><span class="dept-reg-id">5286-150506-163956</span></td>
                                        <td data-label="Experience">15 Years</td>
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
                            JNTUH R22 academic regulation curriculum combined with UGC Autonomous advancements to meet current power industry benchmarks.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>B.Tech EEE R22 Syllabus</h4>
                                        <p>Comprehensive subject outlines, practical laboratory courses, and credit allocations.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/R22B.Tech.EEEIandIIYearSyllabus2.pdf" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>PEOs &amp; PSOs Document</h4>
                                        <p>Program educational objectives and student outcomes documentation.</p>
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
                                    <li><strong>PEO 1:</strong> Graduates will excel in electrical power systems, industrial drives, renewable energy, and control engineering.</li>
                                    <li><strong>PEO 2:</strong> Graduates will pursue advanced studies and technological research in smart electrical grids and automation.</li>
                                    <li><strong>PEO 3:</strong> Graduates will practice engineering with high professional ethics, environmental sensitivity, and safety consciousness.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Ability to analyze, design, and operate electrical machines, power transmission systems, and power electronics.</li>
                                    <li><strong>PSO 2:</strong> Competence in simulating electrical power circuits using MATLAB/Simulink, PSPICE, and industrial software.</li>
                                    <li><strong>PSO 3:</strong> Proficiency in developing sustainable energy systems, solar integration, and modern industrial automation modules.</li>
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
                            Electrical machines laboratory, power systems panels, industrial field trips, and hands-on workshops.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="EEE Department Laboratories &amp; Events" loading="lazy">
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
