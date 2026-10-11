<?php
$page = 'rnd-rankings';
require_once __DIR__ . '/backend/rnd_crud.php';
$rnd_periods          = function_exists('get_distinct_rnd_periods') ? get_distinct_rnd_periods(true) : [];
$rnd_selected_year    = $_GET['rnd_year'] ?? ($rnd_periods[0]['academic_year'] ?? '2025-26');
$rnd_selected_quarter = $_GET['rnd_quarter'] ?? ($rnd_periods[0]['quarter'] ?? 'Q4');
$rnd_data             = function_exists('get_rnd_rankings_by_period') ? get_rnd_rankings_by_period($rnd_selected_year, $rnd_selected_quarter, 'published') : [];
$rnd_rankings         = $rnd_data['rankings'] ?? [];
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
    <main class="rnd-main-content" data-sync-module="rnd">
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
                        <span class="rnd-party-popper">🎉</span> <?php echo htmlspecialchars($rnd_data['quarter_label'] ?: "{$rnd_selected_quarter} (AY {$rnd_selected_year})"); ?> R&amp;D Department Rankings Announced!
                    </h2>
                    <p class="rnd-bulletin-desc">
                        Congratulations to all the departments for their outstanding efforts in research, innovation, patents, publications, NPTEL, IIC and R&amp;D activities.
                    </p>
                </div>

                <!-- Quick Honor Roll Strip (Dynamic) -->
                <div class="rnd-quick-honor-strip">
                    <?php 
                    $tierClasses = [1 => 'gold-tier', 2 => 'bronze-tier', 3 => 'bronze-tier', 4 => 'emerging-tier', 5 => 'emerging-tier'];
                    $icons = [1 => '🥇', 2 => '🥈', 3 => '🥉', 4 => '🏅', 5 => '🏅'];
                    foreach (array_slice($rnd_rankings, 0, 5) as $idx => $r): 
                        $rank = (int)($r['rank'] ?? ($idx + 1));
                        $tier = $tierClasses[$rank] ?? 'emerging-tier';
                        $ico = $icons[$rank] ?? '🏅';
                        $suffix = $rank === 1 ? 'st' : ($rank === 2 ? 'nd' : ($rank === 3 ? 'rd' : 'th'));
                    ?>
                        <div class="rnd-honor-item <?php echo $tier; ?>">
                            <div class="rnd-honor-icon"><?php echo $ico; ?></div>
                            <div class="rnd-honor-info">
                                <span class="rnd-honor-rank"><?php echo $rank . $suffix; ?> Place</span>
                                <strong><?php echo htmlspecialchars($r['department']); ?></strong>
                                <span class="rnd-honor-medal"><?php echo htmlspecialchars($r['award_category'] ?? ''); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

            <!-- Period Selector Bar for Archive & History -->
            <?php if (!empty($rnd_periods)): ?>
                <div class="rnd-period-selector-bar">
                    <div class="rnd-selector-inner">
                        <span class="rnd-selector-label"><i class="fas fa-history"></i> Select Evaluation Period:</span>
                        <div class="rnd-period-tabs">
                            <?php foreach ($rnd_periods as $p): 
                                $isSelected = (strcasecmp($p['academic_year'], $rnd_selected_year) === 0 && strcasecmp($p['quarter'], $rnd_selected_quarter) === 0);
                            ?>
                                <a href="?rnd_year=<?php echo urlencode($p['academic_year']); ?>&rnd_quarter=<?php echo urlencode($p['quarter']); ?>#bulletin" 
                                   class="rnd-period-btn <?php echo $isSelected ? 'active' : ''; ?>">
                                    <?php if (!empty($p['is_latest'])): ?>
                                        <span class="rnd-latest-tag">LATEST</span>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($p['quarter']); ?> · AY <?php echo htmlspecialchars($p['academic_year']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Official Department Rankings Header -->
            <div class="rnd-section-title-wrap" style="margin-bottom: 24px;">
                <div class="rnd-badge-pill" style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 999px; font-size: 12.5px; font-weight: 700; margin-bottom: 10px;">
                    <i class="fas fa-calendar-check"></i> Official Evaluation Period: <?php echo htmlspecialchars($rnd_data['quarter_label'] ?: "{$rnd_selected_quarter} (AY {$rnd_selected_year})"); ?>
                </div>
                <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Departmental Standings &amp; Honors</h2>
                <p style="font-size: 15px; color: #64748b; margin: 0;">Comprehensive rankings evaluated across research, innovation, patents, publications, NPTEL and IIC activities.</p>
            </div>

                <!-- Dynamic Podium Showcase Cards (Top 3) -->
                <div class="rnd-podium-showcase">
                    <?php 
                    $top3 = array_slice($rnd_rankings, 0, 3);
                    $podiumStyles = [
                        1 => ['card' => 'gold-champion', 'tag' => 'gold', 'label' => 'gold', 'icon' => 'fa-trophy', 'prefix' => '🥇 1st Rank · '],
                        2 => ['card' => 'silver-runner', 'tag' => 'silver', 'label' => 'silver', 'icon' => 'fa-award', 'prefix' => '🥈 2nd Rank · '],
                        3 => ['card' => 'bronze-third', 'tag' => 'bronze', 'label' => 'bronze', 'icon' => 'fa-medal', 'prefix' => '🥉 3rd Rank · ']
                    ];

                    foreach ($top3 as $idx => $r):
                        $rank = (int)($r['rank'] ?? ($idx + 1));
                        $style = $podiumStyles[$rank] ?? $podiumStyles[3];
                        $deptCode = htmlspecialchars($r['department']);
                        $deptSlug = strtolower(explode(' ', trim($deptCode))[0]);
                        $score = isset($r['score']) && $r['score'] !== null ? number_format((float)$r['score'], 1) : null;
                        $achievements = (array)($r['achievements'] ?? []);
                    ?>
                        <div class="rnd-podium-card <?php echo $style['card']; ?>">
                            <div class="rnd-podium-badge-header">
                                <span class="rnd-rank-tag <?php echo $style['tag']; ?>">
                                    <?php echo $style['prefix'] . htmlspecialchars($r['award_category'] ?? "Rank {$rank}"); ?>
                                </span>
                                <?php if ($score !== null): ?>
                                    <span class="rnd-score-pill">Score: <?php echo $score; ?> / 100</span>
                                <?php endif; ?>
                            </div>
                            <div class="rnd-podium-body">
                                <div class="rnd-podium-dept-name">
                                    <h2><?php echo $deptCode; ?></h2>
                                    <h4><?php echo htmlspecialchars($r['department_name']); ?></h4>
                                </div>
                                <p class="rnd-podium-summary">
                                    <?php echo htmlspecialchars($r['description'] ?: "Recognized for institutional excellence, high-impact research papers, patents, and active student innovation drives."); ?>
                                </p>
                                
                                <div class="rnd-podium-highlights">
                                    <h6>Key Research Highlights:</h6>
                                    <ul>
                                        <?php foreach ($achievements as $ach): ?>
                                            <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($ach); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="rnd-podium-footer">
                                <span class="rnd-status-label <?php echo $style['label']; ?>">
                                    <i class="fas <?php echo $style['icon']; ?>"></i> <?php echo htmlspecialchars($r['award_badge'] ?? 'Honor Department'); ?>
                                </span>
                                <a href="departments.php?dept=<?php echo urlencode($deptSlug); ?>" class="btn-rnd-dept">Dept Portal <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Emerging Departments Grid (Rank 4, 5, and beyond) -->
                <?php 
                $emergingList = array_slice($rnd_rankings, 3);
                if (!empty($emergingList)):
                ?>
                    <div class="rnd-emerging-grid">
                        <?php foreach ($emergingList as $idx => $r): 
                            $rank = (int)($r['rank'] ?? ($idx + 4));
                            $deptCode = htmlspecialchars($r['department']);
                            $deptSlug = strtolower(explode(' ', trim($deptCode))[0]);
                            $score = isset($r['score']) && $r['score'] !== null ? number_format((float)$r['score'], 1) : null;
                            $achievements = (array)($r['achievements'] ?? []);
                        ?>
                            <div class="rnd-emerging-card">
                                <div class="rnd-emerging-header">
                                    <div class="rnd-emerging-rank">
                                        <span class="rnd-emerging-num"><?php echo $rank; ?><sup>th</sup></span>
                                        <span class="rnd-emerging-badge">🏅 <?php echo htmlspecialchars($r['award_category'] ?? "{$rank}th · Emerging"); ?></span>
                                    </div>
                                    <?php if ($score !== null): ?>
                                        <span class="rnd-score-pill small">Score: <?php echo $score; ?> / 100</span>
                                    <?php endif; ?>
                                </div>
                                <div class="rnd-emerging-info">
                                    <h3><?php echo $deptCode; ?></h3>
                                    <p class="rnd-emerging-fullname"><?php echo htmlspecialchars($r['department_name']); ?></p>
                                    <p class="rnd-emerging-text">
                                        <?php echo htmlspecialchars($r['description'] ?: "Commendable growth in departmental research, faculty publications, and innovation projects."); ?>
                                    </p>
                                </div>
                                <div class="rnd-emerging-pills">
                                    <?php foreach (array_slice($achievements, 0, 3) as $ach): ?>
                                        <span><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($ach); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="rnd-emerging-footer">
                                    <span class="rnd-status-tag emerging"><?php echo htmlspecialchars($r['award_badge'] ?? '🌱 Emerging Department'); ?></span>
                                    <a href="departments.php?dept=<?php echo urlencode($deptSlug); ?>" class="btn-link-sm">View Dept →</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Comprehensive Official Scorecard Table -->
                <div class="rnd-scorecard-section" id="scorecard-table">
                    <div class="rnd-table-header">
                        <div>
                            <h3>Comprehensive Departmental Evaluation Scorecard</h3>
                            <p>Audited performance breakdown across all core R&amp;D dimensions (<?php echo htmlspecialchars("{$rnd_selected_quarter} AY {$rnd_selected_year}"); ?>)</p>
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
                                    <th>Key Achievements</th>
                                    <th>Publication Date</th>
                                    <th>Composite Score</th>
                                    <th>Official Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rnd_rankings)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            No published ranking results for this evaluation period.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rnd_rankings as $idx => $r): 
                                        $rank = (int)($r['rank'] ?? ($idx + 1));
                                        $dept = htmlspecialchars($r['department']);
                                        $deptName = htmlspecialchars($r['department_name']);
                                        $score = isset($r['score']) && $r['score'] !== null ? number_format((float)$r['score'], 1) : null;
                                        $achievements = (array)($r['achievements'] ?? []);
                                        $rowClass = ($rank === 1) ? 'rnd-row-gold' : (($rank === 2) ? 'rnd-row-silver' : (($rank === 3) ? 'rnd-row-bronze' : ''));
                                        $circleClass = ($rank === 1) ? 'rank-gold' : (($rank === 2) ? 'rank-silver' : (($rank === 3) ? 'rank-bronze' : 'rank-emerging'));
                                        $badgeClass = ($rank === 1) ? 'rnd-badge-gold' : (($rank <= 3) ? 'rnd-badge-bronze' : 'rnd-badge-emerging');
                                        $statusClass = ($rank === 1) ? 'pill-gold' : (($rank <= 3) ? 'pill-bronze' : 'pill-emerging');
                                    ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td class="text-center font-bold">
                                                <span class="rnd-rank-circle <?php echo $circleClass; ?>"><?php echo $rank; ?></span>
                                            </td>
                                            <td>
                                                <strong class="rnd-dept-title"><?php echo $dept; ?></strong>
                                                <div class="rnd-dept-sub"><?php echo $deptName; ?></div>
                                            </td>
                                            <td><span class="<?php echo $badgeClass; ?>"><?php echo htmlspecialchars($r['award_category'] ?? ''); ?></span></td>
                                            <td>
                                                <div style="font-size: 13px; color: #334155;">
                                                    <?php echo htmlspecialchars(implode(' · ', array_slice($achievements, 0, 2))); ?>
                                                </div>
                                            </td>
                                            <td style="font-size: 13px; color: #64748b; white-space: nowrap;">
                                                <i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($r['publication_date'] ?? 'N/A'); ?>
                                            </td>
                                            <td>
                                                <?php if ($score !== null): ?>
                                                    <div class="rnd-total-score">
                                                        <span class="score-num"><?php echo $score; ?></span>
                                                        <div class="score-bar"><div class="score-fill" style="width: <?php echo min(100, $score); ?>%;"></div></div>
                                                    </div>
                                                <?php else: ?>
                                                    <span style="color:#94a3b8;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="rnd-status-pill <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($r['award_badge'] ?? 'Ranked'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
