<?php
/**
 * Academic Department Portal - Sticky Sidebar Navigation Component
 * Expected Variables (with fallbacks):
 * $active_dept: 'aiml' | 'cse' | 'ece' | 'eee' | 'hs' | 'mba'
 * $dept_syllabus_link: path to syllabus file
 * $dept_syllabus_name: name of syllabus
 * $dept_peos_link: path to peos file
 */
$active_dept = isset($active_dept) ? $active_dept : '';
$dept_syllabus_link = isset($dept_syllabus_link) ? $dept_syllabus_link : '#curriculum';
$dept_syllabus_name = isset($dept_syllabus_name) ? $dept_syllabus_name : 'Download Syllabus';
$dept_peos_link = isset($dept_peos_link) ? $dept_peos_link : '#peos';
?>

<aside class="dept-sidebar">
    <!-- Card 1: On-Page Quick Jump Navigation -->
    <div class="dept-sidebar-card">
        <div class="dept-sidebar-header">
            <i class="fas fa-compass"></i>
            <h4>Page Navigation</h4>
        </div>
        <ul class="dept-nav-menu" id="dept-spy-menu">
            <li>
                <a href="#overview" class="dept-nav-link active">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-info-circle"></i>
                        <span>About Department</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#vision" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-bullseye"></i>
                        <span>Vision &amp; Mission</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#hod" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-user-tie"></i>
                        <span>Head of Department</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#faculty" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Faculty Directory</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#curriculum" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-file-pdf"></i>
                        <span>Curriculum &amp; Syllabus</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#peos" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-award"></i>
                        <span>PEOs &amp; PSOs</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#gallery" class="dept-nav-link">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-images"></i>
                        <span>Department Gallery</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
        </ul>
    </div>


    <!-- Card 3: Quick Resources & Downloads -->
    <div class="dept-sidebar-card">
        <div class="dept-sidebar-header">
            <i class="fas fa-download"></i>
            <h4>Quick Downloads</h4>
        </div>
        <a href="<?php echo htmlspecialchars($dept_syllabus_link); ?>" target="_blank" class="dept-resource-download-btn">
            <div class="res-icon"><i class="fas fa-file-pdf"></i></div>
            <div class="res-text">
                <span class="res-title">Academic Syllabus</span>
                <span class="res-sub">Official JNTUH R22 PDF</span>
            </div>
            <i class="fas fa-arrow-down" style="font-size: 0.8rem; color: #94a3b8;"></i>
        </a>
        <a href="<?php echo htmlspecialchars($dept_peos_link); ?>" target="_blank" class="dept-resource-download-btn theme-word">
            <div class="res-icon"><i class="fas fa-file-alt"></i></div>
            <div class="res-text">
                <span class="res-title">PEOs &amp; PSOs Document</span>
                <span class="res-sub">Program Objectives</span>
            </div>
            <i class="fas fa-arrow-down" style="font-size: 0.8rem; color: #94a3b8;"></i>
        </a>
    </div>

    <!-- Card 4: Department Helpline & Enquiries -->
    <div class="dept-quick-enquiry">
        <h5>Admissions &amp; Enquiries</h5>
        <p>Have questions about courses, curriculum, or laboratory facilities? Contact our department desk.</p>
        <div style="font-size: 0.85rem; margin-bottom: 12px; display: flex; flex-direction: column; gap: 6px;">
            <div><i class="fas fa-id-card-alt" style="color: #55efc4; margin-right: 6px;"></i> EAPCET Code: <strong>TCEK</strong></div>
            <div><i class="fas fa-phone-alt" style="color: #55efc4; margin-right: 6px;"></i> +91 7396903383, 8522954369</div>
        </div>
        <a href="admission.php" class="dept-quick-call-btn">
            <i class="fas fa-paper-plane"></i> Apply / Enquire Now
        </a>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.dept-nav-link, .dept-mobile-nav-pill');
    const sections = document.querySelectorAll('.dept-section-card');

    function onScroll() {
        let scrollPos = window.scrollY + 140;
        sections.forEach(sec => {
            const top = sec.offsetTop;
            const height = sec.offsetHeight;
            if (scrollPos >= top && scrollPos < top + height) {
                const id = sec.getAttribute('id');
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href === '#' + id) {
                        link.classList.add('active');
                    } else if (href && href.startsWith('#')) {
                        link.classList.remove('active');
                    }
                });
            }
        });
    }
    window.addEventListener('scroll', onScroll);
});
</script>
