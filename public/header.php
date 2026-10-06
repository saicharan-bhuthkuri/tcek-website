<!-- Top Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo-section">
            <!-- Assuming header_banner.png is the main college logo/title image -->
            <img src="assets/Top Header/header_banner.png" alt="Trinity College Logo" class="main-logo">
        </div>
        <div class="accreditation-logos">
            <img src="assets/Top Header/naac_logo.png" alt="NAAC">
            <img src="assets/Top Header/jntuh_logo.png" alt="JNTUH">
            <img src="assets/Top Header/nptel_logo.png" alt="NPTEL">
            <img src="assets/Top Header/ISO-LOGO.png" alt="ISO">
            <img src="assets/Top Header/nss_logo.png" alt="NSS">
            <img src="assets/Top Header/aicte_logo.png" alt="AICTE">
        </div>
    </div>
</header>

<!-- Navigation -->
<nav>
    <div class="nav-container">
        <!-- Logo removed as per request -->
        <div class="mobile-brand">
            <img src="assets/Top Header/logo.jpg" alt="Logo" class="mobile-logo">
            <span>TCEK</span>
        </div>
        <?php
        $curr_page = isset($page) ? $page : '';
        $is_about = in_array($curr_page, ['about', 'affiliation', 'policies', 'committees']);
        $is_academics = in_array($curr_page, ['academics', 'departments', 'examinations', 'e-content']);
        $is_research = in_array($curr_page, ['research', 'rnd-rankings']);
        $is_accreditations = in_array($curr_page, ['naac', 'nba', 'nirf', 'iqac', 'ugc', 'aicte']);
        $is_campus = in_array($curr_page, ['facilities', 'events', 'gallery']);
        ?>
        <ul class="nav-links">
            <li>
                <a href="index.php" class="<?php echo ($curr_page == 'home') ? 'active' : ''; ?>">Home</a>
            </li>

            <li class="nav-item-dropdown">
                <a href="about-us.php" class="nav-dropdown-toggle <?php echo $is_about ? 'active' : ''; ?>">
                    About Us <i class="fas fa-chevron-down nav-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="about-us.php" class="<?php echo ($curr_page == 'about') ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="affiliation.php" class="<?php echo ($curr_page == 'affiliation') ? 'active' : ''; ?>">Affiliation</a></li>
                    <li><a href="policies.php" class="<?php echo ($curr_page == 'policies') ? 'active' : ''; ?>">Policies</a></li>
                    <li><a href="committees.php" class="<?php echo ($curr_page == 'committees') ? 'active' : ''; ?>">Committees</a></li>
                </ul>
            </li>

            <li class="nav-item-dropdown">
                <a href="academics.php" class="nav-dropdown-toggle <?php echo $is_academics ? 'active' : ''; ?>">
                    Academics <i class="fas fa-chevron-down nav-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="academics.php" class="<?php echo ($curr_page == 'academics') ? 'active' : ''; ?>">Academics</a></li>
                    <li><a href="departments.php" class="<?php echo ($curr_page == 'departments') ? 'active' : ''; ?>">Departments</a></li>
                    <li><a href="examinations.php" class="<?php echo ($curr_page == 'examinations') ? 'active' : ''; ?>">Examinations</a></li>
                    <li><a href="e-content.php" class="<?php echo ($curr_page == 'e-content') ? 'active' : ''; ?>">E-CONTENT</a></li>
                </ul>
            </li>

            <li>
                <a href="admission.php" class="<?php echo ($curr_page == 'admission') ? 'active' : ''; ?>">Admissions</a>
            </li>

            <li>
                <a href="placement-cell.php" class="<?php echo ($curr_page == 'placement') ? 'active' : ''; ?>">Placement Cell</a>
            </li>

            <li class="nav-item-dropdown">
                <a href="rnd-rankings.php" class="nav-dropdown-toggle <?php echo $is_research ? 'active' : ''; ?>">
                    Research &amp; R&amp;D <i class="fas fa-chevron-down nav-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="research-publications.php" class="<?php echo ($curr_page == 'research') ? 'active' : ''; ?>">Research Publications</a></li>
                    <li><a href="rnd-rankings.php" class="<?php echo ($curr_page == 'rnd-rankings') ? 'active' : ''; ?>">R&amp;D Rankings <span class="nav-badge-pill">Q4 2026</span></a></li>
                </ul>
            </li>

            <li class="nav-item-dropdown">
                <a href="naac.php" class="nav-dropdown-toggle <?php echo $is_accreditations ? 'active' : ''; ?>">
                    Accreditations <i class="fas fa-chevron-down nav-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="naac.php" class="<?php echo ($curr_page == 'naac') ? 'active' : ''; ?>">NAAC</a></li>
                    <li><a href="nba.php" class="<?php echo ($curr_page == 'nba') ? 'active' : ''; ?>">NBA</a></li>
                    <li><a href="nirf.php" class="<?php echo ($curr_page == 'nirf') ? 'active' : ''; ?>">NIRF</a></li>
                    <li><a href="iqac.php" class="<?php echo ($curr_page == 'iqac') ? 'active' : ''; ?>">IQAC</a></li>
                    <li><a href="ugc.php" class="<?php echo ($curr_page == 'ugc') ? 'active' : ''; ?>">UGC</a></li>
                    <li><a href="aicte-documents.php" class="<?php echo ($curr_page == 'aicte') ? 'active' : ''; ?>">AICTE DOCUMENTS</a></li>
                </ul>
            </li>

            <li class="nav-item-dropdown">
                <a href="facilities.php" class="nav-dropdown-toggle <?php echo $is_campus ? 'active' : ''; ?>">
                    Campus Life <i class="fas fa-chevron-down nav-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="facilities.php" class="<?php echo ($curr_page == 'facilities') ? 'active' : ''; ?>">Facilities</a></li>
                    <li><a href="events.php" class="<?php echo ($curr_page == 'events') ? 'active' : ''; ?>">Events</a></li>
                    <li><a href="gallery.php" class="<?php echo ($curr_page == 'gallery') ? 'active' : ''; ?>">Gallery</a></li>
                </ul>
            </li>

            <li>
                <a href="contact.php" class="<?php echo ($curr_page == 'contact') ? 'active' : ''; ?>">Contact</a>
            </li>
        </ul>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<!-- Notification Bar -->
<div class="news-ticker">
    <div class="ticker-content">
        We Proudly Announce That We Got JNTUH & UGC AUTONOMOUS Status for Five Years From The Academic Year
        2025-2026 to 2029-2030 &nbsp;&nbsp;|&nbsp;&nbsp; We are proud to be the first and only NAAC Accredited
        Engineering College in Peddapalli District &nbsp;&nbsp;|&nbsp;&nbsp; Diploma , BTech and MBA Admissions are
        in progress for 2024-25 &nbsp;&nbsp;|&nbsp;&nbsp; For Admissions Contact: 7396903383, 8522954369
    </div>
</div>

<!-- Admission Codes Banner -->
<div class="admission-banner">
    <div class="container admission-container">
        <div class="admission-code-group">
            <i class="fas fa-university admission-label-icon"></i>
            <span>POLYCET / ECET / EAPCET / ICET CODE:</span>
            <span class="code-badge">TCEK</span>
        </div>

        <div class="admission-contact-group">
            <div class="contact-icon-box"><i class="fas fa-phone-alt"></i></div>
            <span>For Admissions Contact:</span>
            <div>
                <a href="tel:7396903383" class="phone-link">7396903383</a>
                <span class="contact-divider">|</span>
                <a href="tel:8522954369" class="phone-link">8522954369</a>
            </div>
        </div>
    </div>
</div>