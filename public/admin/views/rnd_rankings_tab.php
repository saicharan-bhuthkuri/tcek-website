<?php
/**
 * R&D Department Rankings Management View (Admin Console)
 * Dedicated interface to add, edit, filter, archive, and publish R&D rankings.
 * Displays both historical and latest results with academic year and quarter filters.
 */

// Filter datasets according to query parameters
$filtered_rankings = $all_rnd_rankings;

if (!empty($rnd_filter_year)) {
    $filtered_rankings = array_filter($filtered_rankings, function($r) use ($rnd_filter_year) {
        return strcasecmp($r['academic_year'] ?? '', $rnd_filter_year) === 0;
    });
}

if (!empty($rnd_filter_quarter)) {
    $filtered_rankings = array_filter($filtered_rankings, function($r) use ($rnd_filter_quarter) {
        return strcasecmp($r['quarter'] ?? '', $rnd_filter_quarter) === 0;
    });
}

if ($rnd_filter_status !== 'all' && !empty($rnd_filter_status)) {
    $filtered_rankings = array_filter($filtered_rankings, function($r) use ($rnd_filter_status) {
        return ($r['status'] ?? 'published') === $rnd_filter_status;
    });
}

if (!empty($rnd_search)) {
    $q = mb_strtolower($rnd_search);
    $filtered_rankings = array_filter($filtered_rankings, function($r) use ($q) {
        $dept = mb_strtolower($r['department'] ?? '');
        $name = mb_strtolower($r['department_name'] ?? '');
        $cat  = mb_strtolower($r['award_category'] ?? '');
        $badge = mb_strtolower($r['award_badge'] ?? '');
        $desc = mb_strtolower($r['description'] ?? '');
        $achStr = mb_strtolower(implode(' ', (array)($r['achievements'] ?? [])));
        return (strpos($dept, $q) !== false || strpos($name, $q) !== false || 
                strpos($cat, $q) !== false || strpos($badge, $q) !== false || 
                strpos($desc, $q) !== false || strpos($achStr, $q) !== false);
    });
}

// Sort by Academic Year DESC, Quarter DESC, Rank ASC
usort($filtered_rankings, function($a, $b) {
    $periodCmp = compare_rnd_periods($a['academic_year'] ?? '', $a['quarter'] ?? '', $b['academic_year'] ?? '', $b['quarter'] ?? '');
    if ($periodCmp !== 0) {
        return $periodCmp;
    }
    return ((int)($a['rank'] ?? 99)) <=> ((int)($b['rank'] ?? 99));
});

// Calculate statistics
$published_count = count(array_filter($all_rnd_rankings, function($r) { return ($r['status'] ?? '') === 'published'; }));
$archived_count  = count(array_filter($all_rnd_rankings, function($r) { return ($r['status'] ?? '') === 'archived'; }));
$unique_depts    = count(array_unique(array_column($all_rnd_rankings, 'department')));
$latest_p        = $rnd_periods_all[0] ?? null;
?>
<div class="tab-pane active">
    <!-- Header with Action Buttons -->
    <div class="pane-header-image2">
        <div class="pane-title-group">
            <div class="pane-text">
                <h2><i class="fas fa-medal" style="color:#d97706; margin-right:8px;"></i> R&amp;D Department Rankings</h2>
                <p>Manage, evaluate, and publish quarterly research &amp; innovation rankings for all academic departments</p>
            </div>
        </div>
        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <a href="../index.php#rnd-rankings" target="_blank" class="btn-outline-pill" style="text-decoration:none; font-size:12.5px; font-weight:600;">
                <i class="fas fa-home"></i> View Homepage Section
            </a>
            <a href="../rnd-rankings.php" target="_blank" class="btn-outline-pill" style="text-decoration:none; font-size:12.5px; font-weight:600; color:#0f766e; border-color:#99f6e4;">
                <i class="fas fa-external-link-alt"></i> Public R&amp;D Portal
            </a>
            <button type="button" class="btn-emerald-pill" onclick="openAddRndModal()" style="background:#d97706; border-color:#d97706;">
                <i class="fas fa-plus"></i> Add New Result
            </button>
        </div>
    </div>

    <!-- Quick Statistics Strip -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap:14px; margin-bottom:20px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="width:42px; height:42px; border-radius:10px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Active Period</span>
                <strong style="font-size:15px; color:#0f172a;">
                    <?php echo $latest_p ? htmlspecialchars("{$latest_p['quarter']} · {$latest_p['academic_year']}") : 'No Data'; ?>
                </strong>
                <span style="font-size:10.5px; color:#10b981; font-weight:600; margin-left:4px;">● LATEST</span>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="width:42px; height:42px; border-radius:10px; background:#ecfdf5; color:#10b981; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Published Results</span>
                <strong style="font-size:16px; color:#0f172a;"><?php echo $published_count; ?> Records</strong>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="width:42px; height:42px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Departments Evaluated</span>
                <strong style="font-size:16px; color:#0f172a;"><?php echo $unique_depts; ?> Departments</strong>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
            <div style="width:42px; height:42px; border-radius:10px; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fas fa-archive"></i>
            </div>
            <div>
                <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Preserved Periods</span>
                <strong style="font-size:16px; color:#0f172a;"><?php echo count($rnd_periods_all); ?> Evaluation Quarters</strong>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="action-filter-bar-image2" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <form method="GET" action="dashboard.php" style="display:flex; align-items:center; gap:10px; margin:0; flex-wrap:wrap;">
            <input type="hidden" name="tab" value="rnd_rankings">

            <!-- Evaluation Period Selector -->
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:12px; font-weight:600; color:#475569; margin:0;">Period:</label>
                <select name="period_selector" class="form-control" style="width:auto; min-width:170px; padding:6px 12px; font-size:12.5px;" onchange="handlePeriodDropdownChange(this)">
                    <option value="">All Evaluation Periods</option>
                    <?php foreach ($rnd_periods_all as $p): 
                        $val = $p['academic_year'] . '|' . $p['quarter'];
                        $isCur = (!empty($rnd_filter_year) && !empty($rnd_filter_quarter) && 
                                  strcasecmp($p['academic_year'], $rnd_filter_year) === 0 && 
                                  strcasecmp($p['quarter'], $rnd_filter_quarter) === 0);
                    ?>
                        <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $isCur ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars("{$p['quarter']} (AY {$p['academic_year']})"); ?>
                            <?php echo !empty($p['is_latest']) ? ' - LATEST' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="filter_year" id="filterYearInput" value="<?php echo htmlspecialchars($rnd_filter_year); ?>">
                <input type="hidden" name="filter_quarter" id="filterQuarterInput" value="<?php echo htmlspecialchars($rnd_filter_quarter); ?>">
            </div>

            <!-- Status Filter -->
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:12px; font-weight:600; color:#475569; margin:0;">Status:</label>
                <select name="filter_status" class="form-control" style="width:auto; padding:6px 12px; font-size:12.5px;" onchange="this.form.submit()">
                    <option value="all" <?php echo $rnd_filter_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="published" <?php echo $rnd_filter_status === 'published' ? 'selected' : ''; ?>>Published Only</option>
                    <option value="archived" <?php echo $rnd_filter_status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    <option value="draft" <?php echo $rnd_filter_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>

            <!-- Search Box -->
            <div class="search-box-pill" style="margin:0; width:260px;">
                <i class="fas fa-search"></i>
                <input type="text" name="rnd_search" value="<?php echo htmlspecialchars($rnd_search); ?>" class="search-pill-input" placeholder="Search department, rank, perks..." onchange="this.form.submit()">
            </div>

            <button type="submit" class="btn-emerald-pill" style="padding:7px 16px; font-size:12.5px;">Filter</button>

            <?php if (!empty($rnd_filter_year) || !empty($rnd_filter_quarter) || $rnd_filter_status !== 'all' || !empty($rnd_search)): ?>
                <a href="?tab=rnd_rankings" class="btn-outline-pill" style="text-decoration:none; font-size:12px; padding:6px 12px;">
                    <i class="fas fa-times"></i> Reset Filters
                </a>
            <?php endif; ?>
        </form>

        <div style="font-size:12.5px; color:#64748b; font-weight:500;">
            Showing <strong><?php echo count($filtered_rankings); ?></strong> of <strong><?php echo count($all_rnd_rankings); ?></strong> Ranking Results
        </div>
    </div>

    <!-- Rankings Table Card -->
    <div class="table-card-image2">
        <div class="table-responsive">
            <table class="table-image2">
                <thead>
                    <tr>
                        <th style="width: 110px;">RANK</th>
                        <th style="min-width: 180px;">DEPARTMENT</th>
                        <th style="min-width: 160px;">EVALUATION PERIOD</th>
                        <th style="min-width: 160px;">AWARD CATEGORY</th>
                        <th style="min-width: 240px;">HIGHLIGHTED ACHIEVEMENTS</th>
                        <th style="width: 90px; text-align: center;">SCORE</th>
                        <th style="width: 110px; text-align: center;">STATUS</th>
                        <th style="width: 150px; text-align: right; white-space: nowrap;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_rankings)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding:45px 20px; color:#94a3b8;">
                                <i class="fas fa-medal" style="font-size:36px; margin-bottom:12px; color:#cbd5e1; display:block;"></i>
                                <?php if (!empty($rnd_search) || !empty($rnd_filter_year)): ?>
                                    No ranking results matching your filter criteria.
                                    <br><a href="?tab=rnd_rankings" style="color:#00b894; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">Reset Filters</a>
                                <?php else: ?>
                                    No ranking results added yet. Click <strong>“Add New Result”</strong> above to publish quarterly rankings.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered_rankings as $r): 
                            $id = (int)$r['id'];
                            $rank = (int)($r['rank'] ?? 1);
                            $dept = htmlspecialchars($r['department'] ?? '');
                            $deptName = htmlspecialchars($r['department_name'] ?? '');
                            $year = htmlspecialchars($r['academic_year'] ?? '');
                            $quarter = htmlspecialchars($r['quarter'] ?? '');
                            $awardCat = htmlspecialchars($r['award_category'] ?? '');
                            $awardBadge = htmlspecialchars($r['award_badge'] ?? '');
                            $achievements = (array)($r['achievements'] ?? []);
                            $pubDate = htmlspecialchars($r['publication_date'] ?? '');
                            $status = $r['status'] ?? 'published';
                            $score = isset($r['score']) && $r['score'] !== null ? number_format((float)$r['score'], 1) : null;

                            // Badge styling according to rank
                            $rankBadgeBg = '#f1f5f9';
                            $rankBadgeColor = '#475569';
                            $rankIcon = 'fa-award';

                            if ($rank === 1) {
                                $rankBadgeBg = '#fef3c7';
                                $rankBadgeColor = '#d97706';
                                $rankIcon = 'fa-trophy';
                            } elseif ($rank === 2) {
                                $rankBadgeBg = '#e0f2fe';
                                $rankBadgeColor = '#0284c7';
                                $rankIcon = 'fa-medal';
                            } elseif ($rank === 3) {
                                $rankBadgeBg = '#ffedd5';
                                $rankBadgeColor = '#c2410c';
                                $rankIcon = 'fa-medal';
                            } else {
                                $rankBadgeBg = '#ecfdf5';
                                $rankBadgeColor = '#059669';
                                $rankIcon = 'fa-seedling';
                            }
                        ?>
                            <tr>
                                <!-- Rank Column -->
                                <td>
                                    <div style="display:inline-flex; align-items:center; gap:8px;">
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:<?php echo $rankBadgeBg; ?>; color:<?php echo $rankBadgeColor; ?>; font-size:15px; font-weight:800; border:1px solid rgba(0,0,0,0.05);">
                                            <?php echo $rank; ?>
                                        </span>
                                        <i class="fas <?php echo $rankIcon; ?>" style="color:<?php echo $rankBadgeColor; ?>; font-size:13px;" title="Rank <?php echo $rank; ?>"></i>
                                    </div>
                                </td>

                                <!-- Department Column -->
                                <td>
                                    <div>
                                        <strong style="font-size:14px; color:#0f172a; display:block;"><?php echo $dept; ?></strong>
                                        <span style="font-size:11.5px; color:#64748b; display:block; line-height:1.3; margin-top:2px;">
                                            <?php echo $deptName; ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Period Column -->
                                <td>
                                    <div style="font-size:12.5px;">
                                        <div style="display:flex; align-items:center; gap:6px;">
                                            <span style="display:inline-block; padding:2px 7px; background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:11px; border-radius:6px;">
                                                <?php echo $quarter; ?>
                                            </span>
                                            <strong style="color:#1e293b;">AY <?php echo $year; ?></strong>
                                        </div>
                                        <div style="font-size:11px; color:#94a3b8; margin-top:3px;">
                                            <i class="far fa-calendar-alt"></i> Published: <?php echo $pubDate ?: 'N/A'; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Award Category & Badge -->
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:4px; align-items:flex-start;">
                                        <span style="font-size:12px; font-weight:700; color:<?php echo $rankBadgeColor; ?>; background:<?php echo $rankBadgeBg; ?>; padding:3px 8px; border-radius:6px; letter-spacing:0.3px;">
                                            <?php echo $awardCat; ?>
                                        </span>
                                        <?php if (!empty($awardBadge)): ?>
                                            <span style="font-size:11px; font-weight:600; color:#475569; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 7px; border-radius:6px;">
                                                <?php echo $awardBadge; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Achievements Column -->
                                <td>
                                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:3px;">
                                        <?php foreach (array_slice($achievements, 0, 3) as $ach): ?>
                                            <li style="font-size:12px; color:#334155; display:flex; align-items:center; gap:6px;">
                                                <i class="fas fa-check-circle" style="color:#10b981; font-size:11px; flex-shrink:0;"></i>
                                                <span><?php echo htmlspecialchars($ach); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php if (count($achievements) > 3): ?>
                                            <li style="font-size:11px; color:#64748b; font-style:italic;">
                                                + <?php echo (count($achievements) - 3); ?> more achievements
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </td>

                                <!-- Score Column -->
                                <td style="text-align:center;">
                                    <?php if ($score !== null): ?>
                                        <span style="display:inline-block; font-weight:700; color:#0f172a; font-size:13px; background:#f8fafc; border:1px solid #e2e8f0; padding:3px 8px; border-radius:6px;">
                                            <?php echo $score; ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Column -->
                                <td style="text-align:center;">
                                    <?php if ($status === 'published'): ?>
                                        <span class="badge-status-published" style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px; border-radius:999px; background:#ecfdf5; color:#059669; font-size:11px; font-weight:700;">
                                            <span style="width:6px; height:6px; border-radius:50%; background:#10b981; display:inline-block;"></span> LIVE
                                        </span>
                                    <?php elseif ($status === 'archived'): ?>
                                        <span class="badge-status-archived" style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px; border-radius:999px; background:#f1f5f9; color:#64748b; font-size:11px; font-weight:700;">
                                            <span style="width:6px; height:6px; border-radius:50%; background:#94a3b8; display:inline-block;"></span> ARCHIVED
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status-draft" style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px; border-radius:999px; background:#fef3c7; color:#d97706; font-size:11px; font-weight:700;">
                                            <span style="width:6px; height:6px; border-radius:50%; background:#f59e0b; display:inline-block;"></span> DRAFT
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions Column -->
                                <td style="text-align:right; white-space:nowrap;">
                                    <div style="display:inline-flex; align-items:center; gap:6px;">
                                        <!-- Toggle Status Form -->
                                        <form action="../backend/rnd_crud.php" method="POST" style="display:inline; margin:0;">
                                            <input type="hidden" name="rnd_action" value="toggle_rnd_status">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <button type="submit" class="btn-action-icon" title="<?php echo $status === 'published' ? 'Archive Result' : 'Publish Result'; ?>" style="border:none; cursor:pointer; background:transparent; padding:4px;">
                                                <i class="fas <?php echo $status === 'published' ? 'fa-eye-slash' : 'fa-globe'; ?>" style="color:#64748b;"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Button -->
                                        <button type="button" class="btn-action-icon" title="Edit Ranking" onclick='openEditRndModal(<?php echo json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' style="border:none; cursor:pointer; background:transparent; padding:4px;">
                                            <i class="fas fa-edit" style="color:#2563eb;"></i>
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="../backend/rnd_crud.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete the ranking for Department: <?php echo addslashes($dept); ?> (<?php echo $quarter; ?> <?php echo $year; ?>)? This cannot be undone.');">
                                            <input type="hidden" name="rnd_action" value="delete_rnd_ranking">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <button type="submit" class="btn-action-icon" title="Delete Ranking" style="border:none; cursor:pointer; background:transparent; padding:4px;">
                                                <i class="fas fa-trash-alt" style="color:#ef4444;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function handlePeriodDropdownChange(select) {
    const val = select.value;
    const yearInput = document.getElementById('filterYearInput');
    const quarterInput = document.getElementById('filterQuarterInput');
    if (!val) {
        yearInput.value = '';
        quarterInput.value = '';
    } else {
        const parts = val.split('|');
        yearInput.value = parts[0] || '';
        quarterInput.value = parts[1] || '';
    }
    select.form.submit();
}
</script>
