<?php
$page = 'rnd-rankings';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>R&D Department Rankings - Trinity College of Engineering & Technology</title>
    <meta name="description" content="Official Research & Development (R&D) Department Rankings at Trinity College of Engineering and Technology (TCEK), Peddapalli. Departmental performance in research, publications, patents, NPTEL and IIC.">
    <?php include 'head.php'; ?>
</head>

<body class="rnd-page-body">
    <?php include 'header.php'; ?>

    <!-- Page Hero Header -->
    <section class="page-header rnd-hero-banner" style="margin-bottom: 0;">
        <div class="container">
            <div class="rnd-hero-badge">
                <i class="fas fa-atom"></i> Directorate of Research &amp; Development
            </div>
            <h1>R&amp;D Department Rankings</h1>
            <p class="rnd-breadcrumbs">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <a href="research-publications.php">Research Publications</a> <i class="fas fa-chevron-right"></i>
                <span>R&amp;D Department Rankings</span>
            </p>
        </div>
    </section>

    <!-- Main Content Section -->
    <main class="rnd-main-content">
        <div class="container">

            <!-- Official Bulletin Circular Banner -->
            <div class="rnd-bulletin-card" id="bulletin">
                <div class="rnd-bulletin-top">
                    <div class="rnd-bulletin-meta">
                        <span class="rnd-bulletin-pill"><i class="fas fa-certificate"></i> Official Circular · Ref: TCEK/R&amp;D/2026/Q4-01</span>
                        <span class="rnd-bulletin-date"><i class="far fa-calendar-alt"></i> Jan 2026 – Mar 2026</span>
                    </div>
                    <div class="rnd-bulletin-actions">
                        <button onclick="window.print()" class="btn-rnd-action" title="Print or Save as PDF">
                            <i class="fas fa-print"></i> <span>Print Bulletin</span>
                        </button>
                        <a href="#scorecard-table" class="btn-rnd-action primary">
                            <i class="fas fa-table"></i> <span>View Scorecard</span>
                        </a>
                    </div>
                </div>

                <div class="rnd-bulletin-body">
                    <div class="rnd-bulletin-greeting">Dear All,</div>
                    <h2 class="rnd-bulletin-headline">
                        <span class="rnd-party-popper">🎉</span> Q4 (JAN 2026 – MAR 2026) R&amp;D Department Rankings Announced!
                    </h2>
                    <p class="rnd-bulletin-desc">
                        Congratulations to all the departments for their outstanding efforts in research, innovation, patents, publications, NPTEL, IIC and R&amp;D activities.
                    </p>
                </div>

                <!-- Quick Honor Roll Strip -->
                <div class="rnd-quick-honor-strip">
                    <div class="rnd-honor-item gold-tier">
                        <div class="rnd-honor-icon">🥇</div>
                        <div class="rnd-honor-info">
                            <span class="rnd-honor-rank">1st Place</span>
                            <strong>AIML</strong>
                            <span class="rnd-honor-medal">🏅 Gold</span>
                        </div>
                    </div>

                    <div class="rnd-honor-item bronze-tier">
                        <div class="rnd-honor-icon">🥈</div>
                        <div class="rnd-honor-info">
                            <span class="rnd-honor-rank">2nd Place</span>
                            <strong>CSE &amp; CSM</strong>
                            <span class="rnd-honor-medal">🥉 Bronze</span>
                        </div>
                    </div>

                    <div class="rnd-honor-item bronze-tier">
                        <div class="rnd-honor-icon">🥉</div>
                        <div class="rnd-honor-info">
                            <span class="rnd-honor-rank">3rd Place</span>
                            <strong>EEE</strong>
                            <span class="rnd-honor-medal">🥉 Bronze</span>
                        </div>
                    </div>

                    <div class="rnd-honor-item emerging-tier">
                        <div class="rnd-honor-icon">🏅</div>
                        <div class="rnd-honor-info">
                            <span class="rnd-honor-rank">4th Place</span>
                            <strong>ECE</strong>
                            <span class="rnd-honor-medal">🌱 Emerging</span>
                        </div>
                    </div>

                    <div class="rnd-honor-item emerging-tier">
                        <div class="rnd-honor-icon">🏅</div>
                        <div class="rnd-honor-info">
                            <span class="rnd-honor-rank">5th Place</span>
                            <strong>MBA</strong>
                            <span class="rnd-honor-medal">🌱 Emerging</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Institutional R&D Metrics Overview Bar -->
            <div class="rnd-metrics-bar">
                <div class="rnd-metric-card">
                    <div class="rnd-metric-icon"><i class="fas fa-book-reader"></i></div>
                    <div class="rnd-metric-data">
                        <span class="rnd-metric-number">120+</span>
                        <span class="rnd-metric-label">Scopus &amp; IEEE Papers</span>
                    </div>
                </div>

                <div class="rnd-metric-card">
                    <div class="rnd-metric-icon"><i class="fas fa-stamp"></i></div>
                    <div class="rnd-metric-data">
                        <span class="rnd-metric-number">15+</span>
                        <span class="rnd-metric-label">Patents Published &amp; Filed</span>
                    </div>
                </div>

                <div class="rnd-metric-card">
                    <div class="rnd-metric-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="rnd-metric-data">
                        <span class="rnd-metric-number">350+</span>
                        <span class="rnd-metric-label">NPTEL Elite / Gold Scholars</span>
                    </div>
                </div>

                <div class="rnd-metric-card">
                    <div class="rnd-metric-icon"><i class="fas fa-star"></i></div>
                    <div class="rnd-metric-data">
                        <span class="rnd-metric-number">4-Star</span>
                        <span class="rnd-metric-label">MoE IIC Innovation Rating</span>
                    </div>
                </div>
            </div>

            <!-- Official Department Rankings: Q4 (JAN 2026 – MAR 2026) -->
            <div class="rnd-section-title-wrap" style="margin-bottom: 24px;">
                <div class="rnd-badge-pill" style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 999px; font-size: 12.5px; font-weight: 700; margin-bottom: 10px;">
                    <i class="fas fa-calendar-check"></i> Official Evaluation Period: Q4 (JAN 2026 – MAR 2026)
                </div>
                <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Departmental Standings &amp; Honors</h2>
                <p style="font-size: 15px; color: #64748b; margin: 0;">Comprehensive rankings evaluated across research, innovation, patents, publications, NPTEL and IIC activities.</p>
            </div>

                <!-- Podium Showcase Cards -->
                <div class="rnd-podium-showcase">
                    <!-- Rank 1: AIML (Gold) -->
                    <div class="rnd-podium-card gold-champion">
                        <div class="rnd-podium-badge-header">
                            <span class="rnd-rank-tag gold">🥇 1st Rank · Gold Champion</span>
                            <span class="rnd-score-pill">Score: 96.5 / 100</span>
                        </div>
                        <div class="rnd-podium-body">
                            <div class="rnd-podium-dept-name">
                                <h2>AIML</h2>
                                <h4>Artificial Intelligence &amp; Machine Learning</h4>
                            </div>
                            <p class="rnd-podium-summary">
                                Outstanding institutional leadership with maximum Scopus-indexed research publications, 
                                active patent filings in Deep Learning &amp; Computer Vision, 
                                and top-tier student participation in MoE IIC hackathons.
                            </p>
                            
                            <div class="rnd-podium-stats">
                                <div class="podium-stat">
                                    <i class="fas fa-file-contract"></i>
                                    <div><strong>18 Papers</strong><span>Scopus / IEEE</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-award"></i>
                                    <div><strong>4 Patents</strong><span>Published / Filed</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div><strong>85+ Elite</strong><span>NPTEL Certs</span></div>
                                </div>
                            </div>

                            <div class="rnd-podium-highlights">
                                <h6>Key Research Highlights:</h6>
                                <ul>
                                    <li><i class="fas fa-check"></i> High impact papers in Medical Imaging &amp; Natural Language Processing.</li>
                                    <li><i class="fas fa-check"></i> 100% faculty engagement in SWAYAM / NPTEL certifications.</li>
                                    <li><i class="fas fa-check"></i> IIC 5-Star internal project prototype evaluation.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="rnd-podium-footer">
                            <span class="rnd-status-label gold"><i class="fas fa-trophy"></i> Gold Medal Department</span>
                            <a href="departments.php?dept=aiml" class="btn-rnd-dept">Dept Portal <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Rank 2: CSE & CSM (Bronze) -->
                    <div class="rnd-podium-card silver-runner">
                        <div class="rnd-podium-badge-header">
                            <span class="rnd-rank-tag silver">🥈 2nd Rank · Bronze Honor</span>
                            <span class="rnd-score-pill">Score: 91.2 / 100</span>
                        </div>
                        <div class="rnd-podium-body">
                            <div class="rnd-podium-dept-name">
                                <h2>CSE &amp; CSM</h2>
                                <h4>Computer Science &amp; Engineering</h4>
                            </div>
                            <p class="rnd-podium-summary">
                                Robust excellence in software systems, full-stack prototypes, national hackathon podium finishes, 
                                and international conference presentations.
                            </p>
                            
                            <div class="rnd-podium-stats">
                                <div class="podium-stat">
                                    <i class="fas fa-file-contract"></i>
                                    <div><strong>15 Papers</strong><span>Scopus / UGC CARE</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-award"></i>
                                    <div><strong>3 Patents</strong><span>Filed &amp; Published</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div><strong>72+ Elite</strong><span>NPTEL Certs</span></div>
                                </div>
                            </div>

                            <div class="rnd-podium-highlights">
                                <h6>Key Research Highlights:</h6>
                                <ul>
                                    <li><i class="fas fa-check"></i> High hackathon representation with Smart India Hackathon internal finals.</li>
                                    <li><i class="fas fa-check"></i> Industry-linked R&amp;D projects in cloud computing and cyber defense.</li>
                                    <li><i class="fas fa-check"></i> Continuous student coding sprint achievements.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="rnd-podium-footer">
                            <span class="rnd-status-label silver"><i class="fas fa-award"></i> Bronze Honor Department</span>
                            <a href="departments.php?dept=cse" class="btn-rnd-dept">Dept Portal <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Rank 3: EEE (Bronze) -->
                    <div class="rnd-podium-card bronze-third">
                        <div class="rnd-podium-badge-header">
                            <span class="rnd-rank-tag bronze">🥉 3rd Rank · Bronze Honor</span>
                            <span class="rnd-score-pill">Score: 86.8 / 100</span>
                        </div>
                        <div class="rnd-podium-body">
                            <div class="rnd-podium-dept-name">
                                <h2>EEE</h2>
                                <h4>Electrical &amp; Electronics Engineering</h4>
                            </div>
                            <p class="rnd-podium-summary">
                                Noteworthy contributions in clean renewable energy, power grid simulations, 
                                and patented innovations including the institutional Wave Energy Harnessing prototype.
                            </p>
                            
                            <div class="rnd-podium-stats">
                                <div class="podium-stat">
                                    <i class="fas fa-file-contract"></i>
                                    <div><strong>11 Papers</strong><span>Journals &amp; Confs</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-award"></i>
                                    <div><strong>2 Patents</strong><span>Design &amp; Utility</span></div>
                                </div>
                                <div class="podium-stat">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div><strong>48+ Elite</strong><span>NPTEL Certs</span></div>
                                </div>
                            </div>

                            <div class="rnd-podium-highlights">
                                <h6>Key Research Highlights:</h6>
                                <ul>
                                    <li><i class="fas fa-check"></i> Patented Wave Energy Harnessing Device design implementation.</li>
                                    <li><i class="fas fa-check"></i> Solar energy audit and campus renewable micro-grid research.</li>
                                    <li><i class="fas fa-check"></i> IEEE Power &amp; Energy Society chapter workshops.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="rnd-podium-footer">
                            <span class="rnd-status-label bronze"><i class="fas fa-medal"></i> Bronze Honor Department</span>
                            <a href="departments.php?dept=eee" class="btn-rnd-dept">Dept Portal <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 4th & 5th Emerging Departments Row -->
                <div class="rnd-emerging-grid">
                    <!-- Rank 4: ECE -->
                    <div class="rnd-emerging-card">
                        <div class="rnd-emerging-header">
                            <div class="rnd-emerging-rank">
                                <span class="rnd-emerging-num">4<sup>th</sup></span>
                                <span class="rnd-emerging-badge">🏅 4th · Emerging</span>
                            </div>
                            <span class="rnd-score-pill small">Score: 82.4 / 100</span>
                        </div>
                        <div class="rnd-emerging-info">
                            <h3>ECE</h3>
                            <p class="rnd-emerging-fullname">Electronics &amp; Communication Engineering</p>
                            <p class="rnd-emerging-text">
                                Commendable momentum in IoT sensors, VLSI circuit designs, embedded robotics, 
                                and growing faculty journal publications.
                            </p>
                        </div>
                        <div class="rnd-emerging-pills">
                            <span><i class="fas fa-microchip"></i> IoT &amp; Embedded</span>
                            <span><i class="fas fa-file-alt"></i> 8 Papers</span>
                            <span><i class="fas fa-graduation-cap"></i> 40+ NPTEL</span>
                        </div>
                        <div class="rnd-emerging-footer">
                            <span class="rnd-status-tag emerging">🌱 Emerging Department</span>
                            <a href="departments.php?dept=ece" class="btn-link-sm">View Dept →</a>
                        </div>
                    </div>

                    <!-- Rank 5: MBA -->
                    <div class="rnd-emerging-card">
                        <div class="rnd-emerging-header">
                            <div class="rnd-emerging-rank">
                                <span class="rnd-emerging-num">5<sup>th</sup></span>
                                <span class="rnd-emerging-badge">🏅 5th · Emerging</span>
                            </div>
                            <span class="rnd-score-pill small">Score: 78.9 / 100</span>
                        </div>
                        <div class="rnd-emerging-info">
                            <h3>MBA</h3>
                            <p class="rnd-emerging-fullname">Department of Management Studies</p>
                            <p class="rnd-emerging-text">
                                Promising research in entrepreneurial ecosystems, management case studies, 
                                rural economy surveys, and MoE IIC startup incubation drives.
                            </p>
                        </div>
                        <div class="rnd-emerging-pills">
                            <span><i class="fas fa-briefcase"></i> Case Studies</span>
                            <span><i class="fas fa-lightbulb"></i> ED-Cell Startups</span>
                            <span><i class="fas fa-graduation-cap"></i> 32+ NPTEL</span>
                        </div>
                        <div class="rnd-emerging-footer">
                            <span class="rnd-status-tag emerging">🌱 Emerging Department</span>
                            <a href="departments.php?dept=mba" class="btn-link-sm">View Dept →</a>
                        </div>
                    </div>
                </div>

                <!-- Comprehensive Official Scorecard Table -->
                <div class="rnd-scorecard-section" id="scorecard-table">
                    <div class="rnd-table-header">
                        <div>
                            <h3>Comprehensive Departmental Evaluation Scorecard</h3>
                            <p>Audited performance breakdown across all five core R&amp;D dimensions (Q4 Jan – Mar 2026)</p>
                        </div>
                        <span class="rnd-table-badge"><i class="fas fa-shield-alt"></i> Directorate Audited</span>
                    </div>

                    <div class="rnd-table-responsive">
                        <table class="rnd-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Rank</th>
                                    <th>Department</th>
                                    <th>Honor Tier</th>
                                    <th>Research &amp; Innovation (25)</th>
                                    <th>Patents &amp; IPR (20)</th>
                                    <th>Publications (25)</th>
                                    <th>NPTEL / MOOCs (15)</th>
                                    <th>IIC &amp; Events (15)</th>
                                    <th>Composite Score</th>
                                    <th>Official Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="rnd-row-gold">
                                    <td class="text-center font-bold">
                                        <span class="rnd-rank-circle rank-gold">🥇 1</span>
                                    </td>
                                    <td>
                                        <strong class="rnd-dept-title">AIML</strong>
                                        <div class="rnd-dept-sub">Artificial Intelligence &amp; Machine Learning</div>
                                    </td>
                                    <td><span class="rnd-badge-gold">🏅 Gold</span></td>
                                    <td><strong>24.5</strong> / 25</td>
                                    <td><strong>19.0</strong> / 20</td>
                                    <td><strong>24.2</strong> / 25</td>
                                    <td><strong>14.6</strong> / 15</td>
                                    <td><strong>14.2</strong> / 15</td>
                                    <td>
                                        <div class="rnd-total-score">
                                            <span class="score-num">96.5</span>
                                            <div class="score-bar"><div class="score-fill" style="width: 96.5%;"></div></div>
                                        </div>
                                    </td>
                                    <td><span class="rnd-status-pill pill-gold"><i class="fas fa-crown"></i> 1st · Gold</span></td>
                                </tr>

                                <tr class="rnd-row-silver">
                                    <td class="text-center font-bold">
                                        <span class="rnd-rank-circle rank-silver">🥈 2</span>
                                    </td>
                                    <td>
                                        <strong class="rnd-dept-title">CSE &amp; CSM</strong>
                                        <div class="rnd-dept-sub">Computer Science &amp; Engineering</div>
                                    </td>
                                    <td><span class="rnd-badge-bronze">🥉 Bronze</span></td>
                                    <td><strong>22.8</strong> / 25</td>
                                    <td><strong>18.0</strong> / 20</td>
                                    <td><strong>23.0</strong> / 25</td>
                                    <td><strong>13.8</strong> / 15</td>
                                    <td><strong>13.6</strong> / 15</td>
                                    <td>
                                        <div class="rnd-total-score">
                                            <span class="score-num">91.2</span>
                                            <div class="score-bar"><div class="score-fill" style="width: 91.2%;"></div></div>
                                        </div>
                                    </td>
                                    <td><span class="rnd-status-pill pill-bronze"><i class="fas fa-award"></i> 2nd · Bronze</span></td>
                                </tr>

                                <tr class="rnd-row-bronze">
                                    <td class="text-center font-bold">
                                        <span class="rnd-rank-circle rank-bronze">🥉 3</span>
                                    </td>
                                    <td>
                                        <strong class="rnd-dept-title">EEE</strong>
                                        <div class="rnd-dept-sub">Electrical &amp; Electronics Engineering</div>
                                    </td>
                                    <td><span class="rnd-badge-bronze">🥉 Bronze</span></td>
                                    <td><strong>21.5</strong> / 25</td>
                                    <td><strong>17.5</strong> / 20</td>
                                    <td><strong>21.2</strong> / 25</td>
                                    <td><strong>13.2</strong> / 15</td>
                                    <td><strong>13.4</strong> / 15</td>
                                    <td>
                                        <div class="rnd-total-score">
                                            <span class="score-num">86.8</span>
                                            <div class="score-bar"><div class="score-fill" style="width: 86.8%;"></div></div>
                                        </div>
                                    </td>
                                    <td><span class="rnd-status-pill pill-bronze"><i class="fas fa-medal"></i> 3rd · Bronze</span></td>
                                </tr>

                                <tr>
                                    <td class="text-center font-bold">
                                        <span class="rnd-rank-circle rank-emerging">4</span>
                                    </td>
                                    <td>
                                        <strong class="rnd-dept-title">ECE</strong>
                                        <div class="rnd-dept-sub">Electronics &amp; Communication Engineering</div>
                                    </td>
                                    <td><span class="rnd-badge-emerging">🌱 Emerging</span></td>
                                    <td><strong>20.0</strong> / 25</td>
                                    <td><strong>16.0</strong> / 20</td>
                                    <td><strong>20.5</strong> / 25</td>
                                    <td><strong>13.0</strong> / 15</td>
                                    <td><strong>12.9</strong> / 15</td>
                                    <td>
                                        <div class="rnd-total-score">
                                            <span class="score-num">82.4</span>
                                            <div class="score-bar"><div class="score-fill" style="width: 82.4%;"></div></div>
                                        </div>
                                    </td>
                                    <td><span class="rnd-status-pill pill-emerging"><i class="fas fa-seedling"></i> 4th · Emerging</span></td>
                                </tr>

                                <tr>
                                    <td class="text-center font-bold">
                                        <span class="rnd-rank-circle rank-emerging">5</span>
                                    </td>
                                    <td>
                                        <strong class="rnd-dept-title">MBA</strong>
                                        <div class="rnd-dept-sub">Department of Management Studies</div>
                                    </td>
                                    <td><span class="rnd-badge-emerging">🌱 Emerging</span></td>
                                    <td><strong>19.2</strong> / 25</td>
                                    <td><strong>14.5</strong> / 20</td>
                                    <td><strong>19.8</strong> / 25</td>
                                    <td><strong>12.6</strong> / 15</td>
                                    <td><strong>12.8</strong> / 15</td>
                                    <td>
                                        <div class="rnd-total-score">
                                            <span class="score-num">78.9</span>
                                            <div class="score-bar"><div class="score-fill" style="width: 78.9%;"></div></div>
                                        </div>
                                    </td>
                                    <td><span class="rnd-status-pill pill-emerging"><i class="fas fa-seedling"></i> 5th · Emerging</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- Transparent R&D Assessment Framework (5 Pillars) -->
            <div class="rnd-framework-section">
                <div class="rnd-framework-header">
                    <span class="rnd-framework-pill"><i class="fas fa-balance-scale"></i> Audit Standards</span>
                    <h2>Institutional R&amp;D Ranking Methodology</h2>
                    <p>Departmental rankings are computed through an objective, quarterly peer-audited evaluation framework encompassing five vital research domains:</p>
                </div>

                <div class="rnd-framework-grid">
                    <div class="framework-card">
                        <div class="framework-weight">25% Weightage</div>
                        <div class="framework-icon"><i class="fas fa-book"></i></div>
                        <h4>Research Publications</h4>
                        <p>Articles published in peer-reviewed Scopus, Web of Science, IEEE Xplore, and UGC-CARE listed indexed journals.</p>
                    </div>

                    <div class="framework-card">
                        <div class="framework-weight">20% Weightage</div>
                        <div class="framework-icon"><i class="fas fa-certificate"></i></div>
                        <h4>Patents &amp; IPR</h4>
                        <p>Utility, design, and software copyright filings, publications, and official grants accredited to TCEK faculty &amp; students.</p>
                    </div>

                    <div class="framework-card">
                        <div class="framework-weight">25% Weightage</div>
                        <div class="framework-icon"><i class="fas fa-microscope"></i></div>
                        <h4>Research &amp; Innovation</h4>
                        <p>Funded research proposals, prototypes, proof-of-concept builds, hackathon laurels, and industry project collaborations.</p>
                    </div>

                    <div class="framework-card">
                        <div class="framework-weight">15% Weightage</div>
                        <div class="framework-icon"><i class="fas fa-award"></i></div>
                        <h4>NPTEL / SWAYAM</h4>
                        <p>Faculty &amp; student completion rates in MOOCs, with special bonus points for Elite, Silver, and Gold medal achievements.</p>
                    </div>

                    <div class="framework-card">
                        <div class="framework-weight">15% Weightage</div>
                        <div class="framework-icon"><i class="fas fa-lightbulb"></i></div>
                        <h4>IIC &amp; Startup Drives</h4>
                        <p>MoE Institution's Innovation Council quarterly activities, IPR workshops, idea pitches, and entrepreneurship initiatives.</p>
                    </div>
                </div>
            </div>

            <!-- Official Commendation, Gratitude & Team Motto -->
            <div class="rnd-closing-card">
                <div class="closing-grid">
                    <div class="closing-quote-block">
                        <div class="quote-sparkle">✨</div>
                        <h3 class="quote-text">
                            "Your Innovation. <span>Our Pride.</span><br>Keep Innovating. Keep Inspiring."
                        </h3>
                        <div class="quote-signature">
                            <i class="fas fa-feather-alt"></i> — <strong>Team R&amp;D</strong><br>
                            <span class="quote-sub">Directorate of Research &amp; Development<br>Trinity College of Engineering and Technology</span>
                        </div>
                    </div>

                    <div class="closing-notes-block">
                        <!-- Note 1: Congratulations -->
                        <div class="closing-note-box">
                            <div class="note-icon">👏</div>
                            <div class="note-content">
                                <h5>Heartiest Congratulations</h5>
                                <p>
                                    Heartiest congratulations to all the <strong>Faculty, HoDs, Students and Department Coordinators</strong> for their dedication and continuous contribution towards building a strong research and innovation ecosystem.
                                </p>
                            </div>
                        </div>

                        <!-- Note 2: Gratitude -->
                        <div class="closing-note-box">
                            <div class="note-icon">🙏</div>
                            <div class="note-content">
                                <h5>Sincere Gratitude</h5>
                                <p>
                                    Our sincere thanks to the <strong>Management, Staff, Stakeholders, Students, Parents &amp; Alumni</strong> for their constant encouragement, vision, and unwavering support.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Research Quick Links -->
            <div class="rnd-quick-links-section">
                <h3>Explore Research Resources at Trinity</h3>
                <div class="rnd-links-grid">
                    <a href="assets/research/TRINITYPUBLICATIONSLINKSS.pdf" target="_blank" class="rnd-link-card">
                        <div class="link-icon"><i class="fas fa-file-pdf"></i></div>
                        <div>
                            <h4>Research Publications</h4>
                            <p>Download full compilation of international research papers.</p>
                        </div>
                    </a>

                    <a href="patents.php" class="rnd-link-card">
                        <div class="link-icon"><i class="fas fa-medal"></i></div>
                        <div>
                            <h4>Patents Portfolio</h4>
                            <p>Inspect registered patents including Wave Energy Device.</p>
                        </div>
                    </a>

                    <a href="books.php" class="rnd-link-card">
                        <div class="link-icon"><i class="fas fa-book-reader"></i></div>
                        <div>
                            <h4>Authored Books</h4>
                            <p>Explore scholarly textbooks and chapters authored by TCEK faculty.</p>
                        </div>
                    </a>

                    <a href="facilities.php" class="rnd-link-card">
                        <div class="link-icon"><i class="fas fa-laptop-code"></i></div>
                        <div>
                            <h4>Advanced Research Labs</h4>
                            <p>Cutting-edge high computing facilities and project laboratories.</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </main>



    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
