<!DOCTYPE html>
<html lang="en">
<head>
    <title>Department of Humanities &amp; Sciences - TCEK</title>
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
                <span>Humanities &amp; Sciences</span>
            </div>
            <span class="dept-hero-badge">
                <i class="fas fa-atom"></i> First Year Foundation &bull; UGC Autonomous
            </span>
            <h1 class="dept-hero-title">Department of Humanities &amp; Sciences</h1>
            <p class="dept-hero-tagline">Building the core scientific, mathematical, and communicative foundation for engineering excellence since 2008</p>
            
            <div class="dept-hero-stats-grid">
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">2008</span>
                        <span class="stat-lbl">Established</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-users-cog"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">21+ Faculty</span>
                        <span class="stat-lbl">Expert Mentors</span>
                    </div>
                </div>
                <div class="dept-hero-stat-card">
                    <div class="dept-hero-stat-icon"><i class="fas fa-flask"></i></div>
                    <div class="dept-hero-stat-text">
                        <span class="stat-num">4 Science Labs</span>
                        <span class="stat-lbl">Physics &amp; Chemistry</span>
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
                $active_dept = 'hs';
                $dept_syllabus_link = 'assets/Dept/HS_Syllabus.pdf';
                $dept_syllabus_name = 'H&S Foundation Syllabus';
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
                            The Department of Humanities and Sciences (H&amp;S) comprises the foundational disciplines of English, Mathematics, Physics, Chemistry, and Environmental Studies. Serving as the academic launchpad for all engineering branches, the department plays an indispensable role in molding first-year students into confident, scientifically rigorous technocrats.
                        </p>
                        <p class="dept-section-p">
                            Our primary objective is to equip students with strong analytical thinking, mathematical modeling capabilities, physical intuition, and fluent communication skills. The department operates specialized English Language Communication Skills (ELCS) &amp; Advanced Communication Skills (AECS) multimedia labs, along with state-of-the-art Engineering Physics and Engineering Chemistry laboratories.
                        </p>
                        <p class="dept-section-p">
                            In addition to academic curricula, H&amp;S faculty drive intensive placement preparation, training undergraduates early in verbal ability, quantitative aptitude, group discussions, and personal interview etiquette.
                        </p>

                        <!-- Highlights 4-Grid -->
                        <div class="dept-highlights-grid">
                            <div class="dept-highlight-item">
                                <i class="fas fa-language"></i>
                                <div>
                                    <h5>Language &amp; Communication Labs</h5>
                                    <p>Multimedia software for phonetics, interactive group discussions, and presentation skills.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-square-root-alt"></i>
                                <div>
                                    <h5>Applied Engineering Mathematics</h5>
                                    <p>Matrices, calculus, differential equations, and numerical analysis for engineers.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-flask"></i>
                                <div>
                                    <h5>Physics &amp; Chemistry Labs</h5>
                                    <p>Optics, laser spectrometers, semiconductor bandgap kits, and water analysis benches.</p>
                                </div>
                            </div>
                            <div class="dept-highlight-item">
                                <i class="fas fa-user-check"></i>
                                <div>
                                    <h5>Personality &amp; Aptitude Grooming</h5>
                                    <p>Foundational aptitude and soft-skills bootcamps preparing students for campus hiring.</p>
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
                                    To establish a strong scientific, analytical, and communicative foundation for engineering students, fostering critical thinking, ethical integrity, and lifelong learning attitudes essential for global leadership.
                                </p>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Department Mission</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li>Impart in-depth knowledge in basic sciences, mathematics, and humanities to build solid engineering foundations.</li>
                                    <li>Develop students' communication prowess, soft skills, and professional conduct for global workplaces.</li>
                                    <li>Provide experiential laboratory learning in physics, chemistry, and language computer modules.</li>
                                    <li>Cultivate curiosity, moral ethics, and environmental stewardship across the college community.</li>
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
                                <img src="assets/Dept/hod-h&s.jpeg" alt="Mrs. Padmini Pachwa - HOD H&amp;S" class="dept-hod-photo">
                                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
                            </div>
                            <div class="dept-hod-details">
                                <h3>Mrs. Padmini Pachwa</h3>
                                <span class="dept-hod-desig">Head of Department &amp; Assistant Professor</span>
                                
                                <div class="dept-hod-credentials-row">
                                    <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> M.Sc (Mathematics)</span>
                                    <span class="dept-cred-chip"><i class="fas fa-briefcase"></i> Industry &amp; Academic Background</span>
                                    <span class="dept-cred-chip"><i class="fas fa-clock"></i> 15+ Years Experience</span>
                                </div>

                                <p class="dept-hod-bio">
                                    Mrs. Padmini Pachwa, Head of the Department of Humanities &amp; Sciences, brings over 15 years of multifaceted academic, training, and institutional experience. She completed her M.Sc in Mathematics in 2001 and has guided thousands of first-year engineering students through their crucial academic transition.
                                </p>
                                <p class="dept-hod-bio">
                                    With specialized expertise in student mentoring, personality enhancement, and mathematical pedagogy, she has published several papers in reputed national and international journals. Under her guidance, the department focuses on disciplined study habits, communicative self-confidence, and foundational scientific competence.
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
                            Our diverse faculty team includes subject matter specialists in Mathematics, Physics, Chemistry, English, and Management.
                        </p>
                        <div class="dept-table-container">
                            <table class="dept-faculty-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">S.No</th>
                                        <th>Name of the Faculty</th>
                                        <th>Designation</th>
                                        <th>Discipline / Dept</th>
                                        <th>Qualification</th>
                                        <th>Experience</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="S.No">1</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">Dr. Ashok Kumar Vootla</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Professor &amp; Director</span></td>
                                        <td data-label="Discipline">H&amp;S</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">Ph.D</span></td>
                                        <td data-label="Experience">17 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">2</td>
                                        <td data-label="Faculty Name"><strong class="dept-faculty-name">P. PADMINI</strong></td>
                                        <td data-label="Designation"><span class="dept-role-pill hod">HOD &amp; Asst. Prof</span></td>
                                        <td data-label="Discipline">Mathematics</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Mathematics)</span></td>
                                        <td data-label="Experience">15 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">3</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">N. MAHENDAR</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">English</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.A (English)</span></td>
                                        <td data-label="Experience">7 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">4</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">G. SRINIVAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">English</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.A (English)</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">5</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">ASIA BEGUM</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">English</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.A (English)</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">6</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">V. SRINIVAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Chemistry</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Chemistry)</span></td>
                                        <td data-label="Experience">6 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">7</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">HUMERA AMREEN</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Chemistry</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Chemistry)</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">8</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">M. SUSHMA RANI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Chemistry</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Chemistry)</span></td>
                                        <td data-label="Experience">1 Year</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">9</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">B. JHANSI RANI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Physics</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Physics)</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">10</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">T. NANDITHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Physics</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Physics)</span></td>
                                        <td data-label="Experience">2 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">11</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">SD. KALIMUNISSA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Physics</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Sc (Physics)</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">12</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">B. RAMAMURTHY</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Mechanical</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">5 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">13</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">S. VINAY KUMAR</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Mechanical</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">4 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">14</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">A. VIKAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Mechanical</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">1 Year</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">15</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">V. MAMATHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">ECE / H&amp;S</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">16</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">T. SAMPATH KUMAR</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">ECE / H&amp;S</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">2 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">17</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">J. SURESH</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">EEE / H&amp;S</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.Tech</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">18</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">A. SRILATHA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Management</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.B.A</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">19</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">N. ARUNA JYOTHI</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Management</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.B.A</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">20</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">K. SRINIVAS</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Assistant Professor</span></td>
                                        <td data-label="Discipline">Management</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.B.A</span></td>
                                        <td data-label="Experience">3 Years</td>
                                    </tr>
                                    <tr>
                                        <td data-label="S.No">21</td>
                                        <td data-label="Faculty Name"><span class="dept-faculty-name">A. VIJAYA</span></td>
                                        <td data-label="Designation"><span class="dept-role-pill">Librarian</span></td>
                                        <td data-label="Discipline">Library Science</td>
                                        <td data-label="Qualification"><span class="dept-qual-pill">M.L.I.Sc</span></td>
                                        <td data-label="Experience">11 Years</td>
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
                            B.Tech 1st Year common foundational syllabus including Engineering Physics, Chemistry, Matrices, Calculus, and English Communication.
                        </p>
                        <div class="dept-docs-grid">
                            <div class="dept-doc-card">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-pdf"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>H&amp;S Foundation Syllabus</h4>
                                        <p>Comprehensive subject breakdown and lab experiments for B.Tech First Year.</p>
                                    </div>
                                </div>
                                <a href="assets/Dept/HS_Syllabus.pdf" target="_blank" class="dept-btn-download">
                                    <i class="fas fa-download"></i> Download Official PDF
                                </a>
                            </div>

                            <div class="dept-doc-card theme-word">
                                <div class="dept-doc-card-top">
                                    <div class="dept-doc-icon"><i class="fas fa-file-word"></i></div>
                                    <div class="dept-doc-meta">
                                        <h4>PEOs &amp; PSOs Document</h4>
                                        <p>Program outcomes and educational objectives document.</p>
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
                                    <li><strong>PEO 1:</strong> Impart fundamental scientific concepts enabling students to analyze real engineering challenges.</li>
                                    <li><strong>PEO 2:</strong> Foster exceptional verbal and written communication skills for international technical discourse.</li>
                                    <li><strong>PEO 3:</strong> Inculcate ethical principles, environmental values, and human values in every student.</li>
                                </ul>
                            </div>
                            <div class="dept-vm-card theme-mission">
                                <div class="dept-vm-title">
                                    <i class="fas fa-check-double"></i>
                                    <span>Program Specific Outcomes</span>
                                </div>
                                <ul class="dept-mission-list">
                                    <li><strong>PSO 1:</strong> Proficiency in applied calculus, differential equations, and scientific computing methods.</li>
                                    <li><strong>PSO 2:</strong> Practical competence in conducting physics experiments, chemical analysis, and lab reporting.</li>
                                    <li><strong>PSO 3:</strong> Fluency in English presentations, interviews, group discussions, and technical documentation.</li>
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
                            Language communication laboratories, physics optics benches, chemistry labs, and student orientation sessions.
                        </p>
                        <div class="dept-gallery-grid">
                            <?php 
                            $images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg', '7.jpeg', '8.jpeg', '9.jpeg', '10.jpeg'];
                            foreach($images as $img): 
                            ?>
                            <div class="dept-gallery-card">
                                <img src="assets/Dept/<?php echo $img; ?>" alt="Humanities &amp; Sciences Laboratories &amp; Events" loading="lazy">
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
