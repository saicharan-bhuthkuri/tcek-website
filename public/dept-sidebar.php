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
        <ul class="dept-nav-menu" id="dept-nav-menu">
            <li>
                <a href="#overview" class="dept-nav-link active" data-target="overview">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-info-circle"></i>
                        <span>About Department</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#vision" class="dept-nav-link" data-target="vision">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-bullseye"></i>
                        <span>Vision &amp; Mission</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#hod" class="dept-nav-link" data-target="hod">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-user-tie"></i>
                        <span>Head of Department</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#faculty" class="dept-nav-link" data-target="faculty">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Faculty Directory</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#curriculum" class="dept-nav-link" data-target="curriculum">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-file-pdf"></i>
                        <span>Curriculum &amp; Syllabus</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#peos" class="dept-nav-link" data-target="peos">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-award"></i>
                        <span>PEOs &amp; PSOs</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
            <li>
                <a href="#gallery" class="dept-nav-link" data-target="gallery">
                    <span class="dept-nav-link-left">
                        <i class="fas fa-images"></i>
                        <span>Department Gallery</span>
                    </span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </li>
        </ul>
    </div>
</aside>

<script>
/**
 * TCEK Department Single-Section Tab Controller
 * Ensures only the chosen section is displayed in the main content area.
 * Keeps sidebar and mobile navigation in sync, handles deep linking & browser history.
 */
(function() {
    'use strict';

    function getTargetId(val) {
        if (!val) return 'overview';
        return val.replace(/^#/, '').trim();
    }

    function switchSection(targetId, updateHash) {
        targetId = getTargetId(targetId);
        let targetSection = document.getElementById(targetId);

        // Fallback to overview if section does not exist
        if (!targetSection) {
            targetId = 'overview';
            targetSection = document.getElementById('overview');
        }
        if (!targetSection) return;

        // 1. Hide all department sections so no sections are displayed together
        const allSections = document.querySelectorAll('.dept-section-card');
        allSections.forEach(function(sec) {
            sec.classList.remove('active-section');
            sec.style.display = 'none';
        });

        // 2. Display ONLY the selected section with smooth fade animation
        targetSection.style.display = 'block';
        void targetSection.offsetWidth; // Force reflow
        targetSection.classList.add('active-section');

        // 3. Update active states on sidebar navigation & mobile quick nav pills
        const navElements = document.querySelectorAll('.dept-nav-link, .dept-mobile-nav-pill');
        navElements.forEach(function(link) {
            const href = link.getAttribute('href') || '';
            const target = link.getAttribute('data-target') || getTargetId(href);
            if (target === targetId) {
                link.classList.add('active');
            } else if (href.startsWith('#') || link.getAttribute('data-target')) {
                link.classList.remove('active');
            }
        });

        // 4. Update URL hash without jumping page
        if (updateHash) {
            if (window.history && window.history.pushState) {
                window.history.pushState(null, '', '#' + targetId);
            } else {
                window.location.hash = '#' + targetId;
            }
        }

        // 5. On mobile/tablet screens, scroll smoothly to the main content area
        if (window.innerWidth <= 992) {
            const mainContent = document.querySelector('.dept-main-content');
            if (mainContent) {
                const headerOffset = 85;
                const elementPosition = mainContent.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }
    }

    // Expose global switcher for any inline buttons
    window.switchDeptSection = switchSection;

    document.addEventListener('DOMContentLoaded', function() {
        // Intercept clicks on sidebar navigation links and mobile pills
        const navElements = document.querySelectorAll('.dept-nav-link, .dept-mobile-nav-pill');
        navElements.forEach(function(item) {
            item.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = getTargetId(href);
                    switchSection(targetId, true);
                }
            });
        });

        // Intercept on-page anchor links pointing to any section (e.g. href="#curriculum")
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && href.length > 1) {
                const targetId = getTargetId(href);
                const section = document.getElementById(targetId);
                if (section && section.classList.contains('dept-section-card')) {
                    e.preventDefault();
                    switchSection(targetId, true);
                }
            }
        });

        // Handle browser Back / Forward buttons
        window.addEventListener('hashchange', function() {
            const hash = getTargetId(window.location.hash);
            switchSection(hash, false);
        });

        // Initialize display from URL hash or default to 'overview'
        const initialHash = getTargetId(window.location.hash) || 'overview';
        switchSection(initialHash, false);
    });
})();
</script>
