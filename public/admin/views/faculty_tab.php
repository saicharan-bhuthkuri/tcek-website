<?php
/**
 * Faculty Directory & Staff Management View (Admin Console)
 * Dedicated department-first interface where the admin selects a department
 * and manages its faculty members, designations, HOD leadership, and R&D roles.
 * Includes search, multi-factor filters, pagination, and Excel export.
 */

// Selected department details (if filtered to a specific department)
$current_selected_dept = null;
if ($faculty_dept_filter !== '' && strtolower($faculty_dept_filter) !== 'all') {
    $current_selected_dept = get_department_by_code($faculty_dept_filter);
}

// Current active export query string matching all active filters
$export_query_params = array_filter([
    'action'        => 'export_faculty_excel',
    'department'    => $faculty_dept_filter,
    'search'        => $faculty_search,
    'role_category' => $faculty_role_filter,
    'is_hod'        => $faculty_hod_filter,
    'is_rnd'        => $faculty_rnd_filter,
    'sort_by'       => $faculty_sort_by,
    'sort_dir'      => $faculty_sort_dir
], function ($v) { return $v !== null && $v !== '' && $v !== 'all'; });

$export_url_current = '../backend/crud.php?' . http_build_query(array_merge(['action' => 'export_faculty_excel'], $export_query_params));
$export_url_all     = '../backend/crud.php?action=export_faculty_excel&department=all';
?>
<div class="tab-pane active">
    <!-- Header with Action Buttons -->
    <div class="pane-header-image2">
        <div class="pane-title-group">
            <div class="pane-text">
                <h2>
                    <i class="fas fa-chalkboard-teacher" style="color:#00b894; margin-right:8px;"></i> 
                    Faculty Directory &amp; Staff Management
                </h2>
                <p>
                    <?php if ($current_selected_dept): ?>
                        Managing faculty profiles for <strong><?php echo htmlspecialchars($current_selected_dept['name']); ?> (<?php echo htmlspecialchars($current_selected_dept['dept_code']); ?>)</strong>
                    <?php else: ?>
                        Manage academic appointments, HOD assignments, and R&amp;D affiliations across all departments
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <!-- Department Excel Export Action Dropdown / Button -->
            <div class="export-dropdown-wrap" style="position:relative; display:inline-block;">
                <button type="button" class="btn-outline-pill" style="font-size:12.5px; font-weight:600; color:#0f766e; border-color:#99f6e4; background:#f0fdfa;" onclick="toggleExportMenu(event)">
                    <i class="fas fa-file-excel" style="color:#10b981;"></i> Download Department Excel <i class="fas fa-chevron-down" style="font-size:10px; margin-left:4px;"></i>
                </button>
                <div id="exportMenuDropdown" style="display:none; position:absolute; right:0; top:110%; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:260px; z-index:100; overflow:hidden;">
                    <a href="<?php echo htmlspecialchars($export_url_current); ?>" style="display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:12.5px; color:#1e293b; text-decoration:none; border-bottom:1px solid #f1f5f9; transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                        <i class="fas fa-filter" style="color:#0ea5e9;"></i>
                        <span>Export Current Filtered View</span>
                    </a>
                    <?php if ($current_selected_dept): ?>
                        <a href="../backend/crud.php?action=export_faculty_excel&department=<?php echo urlencode($current_selected_dept['dept_code']); ?>" style="display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:12.5px; color:#1e293b; text-decoration:none; border-bottom:1px solid #f1f5f9; transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            <i class="fas fa-building" style="color:#00b894;"></i>
                            <span>Export <?php echo htmlspecialchars($current_selected_dept['dept_code']); ?> Department</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($export_url_all); ?>" style="display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:12.5px; color:#1e293b; text-decoration:none; transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                        <i class="fas fa-globe" style="color:#6366f1;"></i>
                        <span>Export All Departments</span>
                    </a>
                </div>
            </div>

            <!-- Quick HOD Appointment -->
            <button type="button" class="btn-outline-pill" style="font-size:12.5px; font-weight:600; color:#d97706; border-color:#fde68a; background:#fffbeb;" onclick="openModal('modalAssignHOD')">
                <i class="fas fa-user-tie"></i> Assign HOD
            </button>

            <!-- Add Faculty Button -->
            <button type="button" class="btn-emerald-pill" onclick="openAddFacultyModal('<?php echo htmlspecialchars($faculty_dept_filter !== 'all' ? $faculty_dept_filter : 'CSE'); ?>')">
                <i class="fas fa-user-plus"></i> Add Faculty Member
            </button>
        </div>
    </div>

    <!-- Department Selector & Comprehensive Filter Bar -->
    <div class="faculty-filter-card">
        <form method="GET" action="dashboard.php" id="facultyFilterForm" style="display:flex; flex-direction:column; gap:14px; margin:0;">
            <input type="hidden" name="tab" value="faculty">

            <!-- Row 1: Primary Department Selector & Search -->
            <div class="faculty-filter-grid-primary">
                <!-- 1. Department Selection (Primary Selector) -->
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fas fa-sitemap" style="color:#00b894; margin-right:4px;"></i> Select Department
                    </label>
                    <select name="dept" class="form-control" style="width:100%; height:42px; font-size:13px; font-weight:500;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($faculty_dept_filter === 'all' || empty($faculty_dept_filter)) ? 'selected' : ''; ?>>
                            &bull; All Academic Departments (<?php echo $total_faculty; ?>)
                        </option>
                        <?php foreach ($all_depts_list as $d): ?>
                            <option value="<?php echo htmlspecialchars($d['dept_code']); ?>" <?php echo (strtoupper($faculty_dept_filter) === strtoupper($d['dept_code'])) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d['dept_code']); ?> &ndash; <?php echo htmlspecialchars($d['name']); ?> (<?php echo (int)($d['faculty_count'] ?? 0); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Search by Name / Qual / Reg ID -->
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fas fa-search" style="color:#0ea5e9; margin-right:4px;"></i> Search Faculty
                    </label>
                    <div class="search-box-pill" style="margin:0; width:100%; height:42px;">
                        <i class="fas fa-search"></i>
                        <input type="text" name="faculty_search" value="<?php echo htmlspecialchars($faculty_search); ?>" class="search-pill-input" style="height:42px;" placeholder="Name, qualification, designation, JNTUH Reg ID..." onkeydown="if(event.key==='Enter'){event.preventDefault(); this.form.submit();}">
                    </div>
                </div>
            </div>

            <!-- Row 2: Secondary Filters & Action Buttons -->
            <div class="faculty-filter-grid-secondary">
                <!-- 3. Designation / Role Category Filter -->
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fas fa-id-badge" style="color:#8b5cf6; margin-right:4px;"></i> Role / Category
                    </label>
                    <select name="role" class="form-control" style="width:100%; height:40px; font-size:13px;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($faculty_role_filter === 'all') ? 'selected' : ''; ?>>All Roles</option>
                        <option value="HOD" <?php echo ($faculty_role_filter === 'HOD') ? 'selected' : ''; ?>>HOD – Head of Department</option>
                        <option value="Faculty – Senior (SR)" <?php echo ($faculty_role_filter === 'Faculty – Senior (SR)') ? 'selected' : ''; ?>>Faculty – Senior (SR)</option>
                        <option value="Faculty – Junior (JR)" <?php echo ($faculty_role_filter === 'Faculty – Junior (JR)') ? 'selected' : ''; ?>>Faculty – Junior (JR)</option>
                        <option value="R&D" <?php echo ($faculty_role_filter === 'R&D') ? 'selected' : ''; ?>>R&D</option>
                        <option value="R&D + Senior Faculty (SR)" <?php echo ($faculty_role_filter === 'R&D + Senior Faculty (SR)') ? 'selected' : ''; ?>>R&D + Senior Faculty (SR)</option>
                        <option value="R&D + Junior Faculty (JR)" <?php echo ($faculty_role_filter === 'R&D + Junior Faculty (JR)') ? 'selected' : ''; ?>>R&D + Junior Faculty (JR)</option>
                    </select>
                </div>

                <!-- 4. HOD Status Filter -->
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fas fa-crown" style="color:#f59e0b; margin-right:4px;"></i> HOD Status
                    </label>
                    <select name="is_hod" class="form-control" style="width:100%; height:40px; font-size:13px;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($faculty_hod_filter === 'all') ? 'selected' : ''; ?>>All Faculty</option>
                        <option value="1" <?php echo ($faculty_hod_filter === '1') ? 'selected' : ''; ?>>HOD Only</option>
                        <option value="0" <?php echo ($faculty_hod_filter === '0') ? 'selected' : ''; ?>>Non-HOD Only</option>
                    </select>
                </div>

                <!-- 5. R&D Affiliation Filter -->
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                        <i class="fas fa-flask" style="color:#ec4899; margin-right:4px;"></i> R&amp;D Affiliation
                    </label>
                    <select name="is_rnd" class="form-control" style="width:100%; height:40px; font-size:13px;" onchange="this.form.submit()">
                        <option value="all" <?php echo ($faculty_rnd_filter === 'all') ? 'selected' : ''; ?>>All Roles</option>
                        <option value="1" <?php echo ($faculty_rnd_filter === '1') ? 'selected' : ''; ?>>R&amp;D Affiliated Only</option>
                        <option value="0" <?php echo ($faculty_rnd_filter === '0') ? 'selected' : ''; ?>>Non-R&amp;D Only</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="submit" class="btn-emerald-pill" style="height:40px; padding:0 22px; font-size:13px; font-weight:600; white-space:nowrap;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="?tab=faculty" class="btn-outline-pill" style="height:40px; padding:0 16px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; white-space:nowrap;" title="Reset all filters">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Active Filter Indicator Tags -->
        <?php 
        $has_active_filters = ($faculty_dept_filter !== 'all' && $faculty_dept_filter !== '') || 
                              ($faculty_search !== '') || 
                              ($faculty_role_filter !== 'all' && $faculty_role_filter !== '') || 
                              ($faculty_hod_filter !== 'all') || 
                              ($faculty_rnd_filter !== 'all');
        ?>
        <?php if ($has_active_filters): ?>
            <div style="display:flex; align-items:center; gap:8px; margin-top:14px; padding-top:12px; border-top:1px dashed #e2e8f0; flex-wrap:wrap;">
                <span style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Active Filters:</span>
                <?php if ($faculty_dept_filter !== 'all' && $faculty_dept_filter !== ''): ?>
                    <span style="background:#ecfdf5; color:#059669; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #a7f3d0;">
                        Dept: <?php echo htmlspecialchars($faculty_dept_filter); ?>
                    </span>
                <?php endif; ?>
                <?php if ($faculty_search !== ''): ?>
                    <span style="background:#eff6ff; color:#1d4ed8; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #dbeafe;">
                        Search: "<?php echo htmlspecialchars($faculty_search); ?>"
                    </span>
                <?php endif; ?>
                <?php if ($faculty_role_filter !== 'all' && $faculty_role_filter !== ''): ?>
                    <span style="background:#f3e8ff; color:#7e22ce; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #e9d5ff;">
                        Role: <?php echo htmlspecialchars($faculty_role_filter); ?>
                    </span>
                <?php endif; ?>
                <?php if ($faculty_hod_filter === '1'): ?>
                    <span style="background:#fffbeb; color:#b45309; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #fde68a;">
                        <i class="fas fa-crown"></i> HOD Only
                    </span>
                <?php elseif ($faculty_hod_filter === '0'): ?>
                    <span style="background:#f1f5f9; color:#475569; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #cbd5e1;">
                        Non-HOD Only
                    </span>
                <?php endif; ?>
                <?php if ($faculty_rnd_filter === '1'): ?>
                    <span style="background:#fce7f3; color:#be185d; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:600; border:1px solid #fbcfe8;">
                        <i class="fas fa-flask"></i> R&amp;D Affiliated
                    </span>
                <?php endif; ?>
                <a href="?tab=faculty" style="font-size:11.5px; color:#ef4444; text-decoration:none; margin-left:6px; font-weight:600;">
                    Clear all filters
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Faculty Table Card -->
    <div class="table-card-image2">
        <div class="table-responsive">
            <table class="table-image2">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">S.NO</th>
                        <th style="min-width: 190px;">FACULTY MEMBER</th>
                        <th style="width: 80px; text-align: center;">DEPARTMENT</th>
                        <th style="min-width: 135px;">DESIGNATION</th>
                        <th style="min-width: 145px;">ROLE CATEGORY</th>
                        <th style="width: 95px; text-align: center;">HOD STATUS</th>
                        <th style="width: 75px; text-align: center;">R&amp;D</th>
                        <th style="min-width: 125px;">QUALIFICATION &amp; EXP</th>
                        <th style="width: 110px; text-align: right; white-space: nowrap;">ACTIONS</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (empty($faculty_list)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:45px 20px; color:#94a3b8;">
                            <i class="fas fa-user-slash" style="font-size:36px; margin-bottom:12px; color:#cbd5e1; display:block;"></i>
                            No faculty members found matching the selected filters.
                            <br><a href="?tab=faculty" style="color:#00b894; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">Reset filters</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $start_idx = (int)($faculty_pagination['start_index'] ?? 1);
                    foreach ($faculty_list as $index => $f): 
                        $sno = $start_idx + $index;
                        $fId = (int)$f['id'];
                        $name = htmlspecialchars($f['full_name']);
                        $deptCode = htmlspecialchars($f['dept_code']);
                        $desig = htmlspecialchars($f['designation']);
                        $roleCat = htmlspecialchars($f['role_category']);
                        $isHod = !empty($f['is_hod']);
                        $isRnd = !empty($f['is_rnd']) || (stripos($roleCat, 'R&D') !== false);
                        $qual = htmlspecialchars($f['qualification'] ?? 'M.Tech');
                        $exp = htmlspecialchars($f['experience'] ?? '5+ Years');
                        $regId = htmlspecialchars($f['jntuh_reg_id'] ?? 'N/A');
                        $email = htmlspecialchars($f['email'] ?? '');
                        $phone = htmlspecialchars($f['phone'] ?? '');
                        $avatar = !empty($f['profile_image']) ? htmlspecialchars($f['profile_image']) : 'assets/Dept/faculty-avatar.png';
                        $initial = strtoupper(substr(trim($f['full_name']), 0, 1));
                    ?>
                        <tr style="<?php echo $isHod ? 'background:#fbfefc;' : ''; ?>">
                            <td style="text-align: center;"><span style="font-weight:600; color:#64748b;"><?php echo $sno; ?></span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:38px; height:38px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; border:2px solid <?php echo $isHod ? '#f59e0b' : '#00b894'; ?>;">
                                        <?php if (!empty($f['profile_image']) && file_exists(__DIR__ . '/../../' . $f['profile_image'])): ?>
                                            <img src="../<?php echo $avatar; ?>" alt="<?php echo $name; ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <span style="font-size:14px; font-weight:700; color:#334155;"><?php echo $initial; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="item-main-title" style="font-weight:600; font-size:13.5px; color:#0f172a;">
                                            <?php echo $name; ?>
                                            <?php if ($isHod): ?>
                                                <i class="fas fa-crown" style="color:#f59e0b; margin-left:4px; font-size:12px;" title="Active Head of Department"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size:11px; color:#64748b; margin-top:1px; white-space:nowrap;">
                                            Reg ID: <strong style="color:#475569;"><?php echo $regId; ?></strong>
                                            <?php if ($email): ?>
                                                &bull; <a href="mailto:<?php echo $email; ?>" style="color:#0284c7; text-decoration:none;"><?php echo $email; ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="?tab=faculty&dept=<?php echo urlencode($f['dept_code']); ?>" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none; font-weight:600; font-size:12.5px; color:#0f172a;">
                                    <span style="background:#eff6ff; color:#1d4ed8; padding:2px 7px; border-radius:6px; font-size:11px; font-weight:700; border:1px solid #dbeafe;">
                                        <?php echo $deptCode; ?>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span style="font-weight:500; font-size:12.5px; color:#334155;">
                                    <?php echo $desig; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                // Distinct styling for each of the 6 roles
                                $badgeStyle = 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;';
                                $iconClass = 'fas fa-user-tag';
                                if ($roleCat === 'HOD') {
                                    $badgeStyle = 'background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-weight:700;';
                                    $iconClass = 'fas fa-crown';
                                } elseif ($roleCat === 'Faculty – Senior (SR)') {
                                    $badgeStyle = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-weight:600;';
                                    $iconClass = 'fas fa-user-graduate';
                                } elseif ($roleCat === 'Faculty – Junior (JR)') {
                                    $badgeStyle = 'background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-weight:600;';
                                    $iconClass = 'fas fa-user-tie';
                                } elseif ($roleCat === 'R&D') {
                                    $badgeStyle = 'background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff; font-weight:600;';
                                    $iconClass = 'fas fa-flask';
                                } elseif ($roleCat === 'R&D + Senior Faculty (SR)') {
                                    $badgeStyle = 'background:#e0e7ff; color:#3730a3; border:1px solid #c7d2fe; font-weight:600;';
                                    $iconClass = 'fas fa-award';
                                } elseif ($roleCat === 'R&D + Junior Faculty (JR)') {
                                    $badgeStyle = 'background:#ccfbf1; color:#0f766e; border:1px solid #99f6e4; font-weight:600;';
                                    $iconClass = 'fas fa-microscope';
                                }
                                ?>
                                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:999px; font-size:11px; <?php echo $badgeStyle; ?> white-space:nowrap;">
                                    <i class="<?php echo $iconClass; ?>"></i>
                                    <span><?php echo $roleCat; ?></span>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($isHod): ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; background:#fef3c7; color:#b45309; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #fde68a; white-space:nowrap;">
                                        <i class="fas fa-check-circle"></i> Active HOD
                                    </span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:12px;">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($isRnd): ?>
                                    <span style="display:inline-flex; align-items:center; gap:4px; background:#f5f3ff; color:#7c3aed; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; border:1px solid #ddd6fe; white-space:nowrap;">
                                        <i class="fas fa-flask"></i> R&amp;D
                                    </span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:12px;">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:12px; font-weight:600; color:#1e293b;"><?php echo $qual; ?></div>
                                <div style="font-size:11px; color:#64748b;"><?php echo $exp; ?></div>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display:inline-flex; align-items:center; gap:6px; justify-content:flex-end;">
                                    <!-- Edit Faculty Button -->
                                    <button type="button" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px;"
                                            onclick='openEditFacultyModal(<?php echo json_encode($f, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' 
                                            title="Edit Faculty Details">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Quick Assign as HOD Button (if not already HOD) -->
                                    <?php if (!$isHod): ?>
                                        <form action="../backend/crud.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Assign \'<?php echo addslashes($name); ?>\' as the active Head of Department for <?php echo $deptCode; ?>? Any current HOD for this department will be reassigned.');">
                                            <input type="hidden" name="action" value="assign_hod">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="dept_code" value="<?php echo $deptCode; ?>">
                                            <input type="hidden" name="faculty_id" value="<?php echo $fId; ?>">
                                            <button type="submit" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px; color:#d97706; border-color:#fde68a;" title="Make Head of Department">
                                                <i class="fas fa-crown"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Delete Faculty Button -->
                                    <form action="../backend/crud.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete faculty member \'<?php echo addslashes($name); ?>\' from <?php echo $deptCode; ?>?');">
                                        <input type="hidden" name="action" value="delete_faculty">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="id" value="<?php echo $fId; ?>">
                                        <input type="hidden" name="dept_code" value="<?php echo $deptCode; ?>">
                                        <button type="submit" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px; color:#ef4444; border-color:#fecaca;" title="Delete Faculty Member">
                                            <i class="fas fa-trash-alt"></i>
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

    <!-- Pagination -->
    <?php echo render_pagination_bar($faculty_pagination, [
        'tab'           => 'faculty',
        'dept'          => $faculty_dept_filter,
        'faculty_search'=> $faculty_search,
        'role'          => $faculty_role_filter,
        'is_hod'        => $faculty_hod_filter,
        'is_rnd'        => $faculty_rnd_filter
    ]); ?>
</div>

<script>
function toggleExportMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('exportMenuDropdown');
    if (menu) {
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
}
document.addEventListener('click', function(e) {
    const menu = document.getElementById('exportMenuDropdown');
    if (menu && menu.style.display === 'block') {
        menu.style.display = 'none';
    }
});
</script>
