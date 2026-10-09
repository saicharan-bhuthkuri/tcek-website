<?php
/**
 * Department Management View (Admin Console)
 * Dedicated interface to add, edit, view, and safely delete departments.
 * Displays all departments with names, codes, degrees, and faculty counts.
 * Enforces rule: Prevents accidental deletion of departments with active faculty.
 */
?>
<div class="tab-pane active">
    <!-- Header with Action Buttons -->
    <div class="pane-header-image2">
        <div class="pane-title-group">
            <div class="pane-text">
                <h2><i class="fas fa-sitemap" style="color:#00b894; margin-right:8px;"></i> Department Management</h2>
                <p>Manage college academic departments, degree programs, intake capacity, and HOD leadership</p>
            </div>
        </div>
        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <a href="../departments.php" target="_blank" class="btn-outline-pill" style="text-decoration:none; font-size:12.5px; font-weight:600;">
                <i class="fas fa-external-link-alt"></i> Public Departments Portal
            </a>
            <a href="../backend/crud.php?action=export_faculty_excel&department=all" class="btn-outline-pill" style="text-decoration:none; font-size:12.5px; font-weight:600; color:#0f766e; border-color:#99f6e4;">
                <i class="fas fa-file-excel" style="color:#10b981;"></i> Export All Faculty (.XLS)
            </a>
            <button type="button" class="btn-emerald-pill" onclick="openModal('modalAddDepartment')">
                <i class="fas fa-plus"></i> Add New Department
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="action-filter-bar-image2" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <form method="GET" action="dashboard.php" style="display:flex; align-items:center; gap:10px; margin:0; flex-wrap:wrap;">
            <input type="hidden" name="tab" value="departments">
            <div class="search-box-pill" style="margin:0; width:320px;">
                <i class="fas fa-search"></i>
                <input type="text" name="dept_search" value="<?php echo htmlspecialchars($dept_search); ?>" class="search-pill-input" placeholder="Search by name, code, or degree..." onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn-emerald-pill" style="padding:7px 16px; font-size:12.5px;">Search</button>
            <?php if (!empty($dept_search)): ?>
                <a href="?tab=departments" class="btn-outline-pill" style="text-decoration:none; font-size:12px; padding:6px 12px;">
                    <i class="fas fa-times"></i> Clear Search
                </a>
            <?php endif; ?>
        </form>

        <div style="font-size:12.5px; color:#64748b; font-weight:500;">
            Showing <strong><?php echo count($departments_list); ?></strong> of <strong><?php echo $total_departments; ?></strong> Departments
        </div>
    </div>

    <!-- Departments Table Card -->
    <div class="table-card-image2">
        <div class="table-responsive">
            <table class="table-image2">
                <thead>
                    <tr>
                        <th style="width: 100px;">CODE &amp; THEME</th>
                        <th style="min-width: 180px;">DEPARTMENT NAME</th>
                        <th style="width: 95px; text-align: center;">DEGREE LEVEL</th>
                        <th style="min-width: 125px;">INTAKE / DURATION</th>
                        <th style="width: 90px; text-align: center;">ESTABLISHED</th>
                        <th style="width: 110px; text-align: center;">FACULTY COUNT</th>
                        <th style="min-width: 150px;">HEAD OF DEPT (HOD)</th>
                        <th style="width: 85px; text-align: center;">STATUS</th>
                        <th style="width: 130px; text-align: right; white-space: nowrap;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments_list)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:45px 20px; color:#94a3b8;">
                                <i class="fas fa-building" style="font-size:36px; margin-bottom:12px; color:#cbd5e1; display:block;"></i>
                                <?php if (!empty($dept_search)): ?>
                                    No departments found matching "<strong><?php echo htmlspecialchars($dept_search); ?></strong>". 
                                    <br><a href="?tab=departments" style="color:#00b894; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">View all departments</a>
                                <?php else: ?>
                                    No departments registered yet. Click <strong>“Add New Department”</strong> above to create one.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departments_list as $d): ?>
                            <?php 
                            $fCount = (int)($d['faculty_count'] ?? 0);
                            $dCode  = htmlspecialchars($d['dept_code']);
                            $dName  = htmlspecialchars($d['name']);
                            $theme  = htmlspecialchars($d['theme_class'] ?? 'theme-cse');
                            $icon   = htmlspecialchars($d['icon_class'] ?? 'fas fa-graduation-cap');
                            $hodName= !empty($d['hod_name']) ? htmlspecialchars($d['hod_name']) : null;
                            $isActive = !empty($d['is_active']);
                            ?>
                            <tr>
                                <td>
                                    <div style="display:inline-flex; align-items:center; gap:8px;">
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; background:#ecfdf5; color:#00b894; font-size:14px;">
                                            <i class="<?php echo $icon; ?>"></i>
                                        </span>
                                        <strong style="font-size:13.5px; color:#0f172a;"><?php echo $dCode; ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="item-main-title" style="font-weight:600; font-size:13.5px; color:#0f172a;">
                                        <?php echo $dName; ?>
                                    </div>
                                    <div style="font-size:11.5px; color:#64748b; margin-top:3px;">
                                        Page URL: <code style="color:#0284c7; background:#f0f9ff; padding:1px 6px; border-radius:4px; font-size:11px; white-space:nowrap; display:inline-block;"><?php echo htmlspecialchars($d['page_url'] ?? 'department.php?slug=' . ($d['slug'] ?? 'cse')); ?></code>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="display:inline-block; padding:3px 10px; border-radius:999px; font-size:11.5px; font-weight:600; background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;">
                                        <?php echo htmlspecialchars($d['degree_level'] ?? 'B.Tech'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:12.5px; font-weight:600; color:#334155;">
                                        <?php echo htmlspecialchars($d['intake'] ?? '60 Seats'); ?>
                                    </div>
                                    <div style="font-size:11.5px; color:#64748b;">
                                        Duration: <?php echo htmlspecialchars($d['duration'] ?? '4 Years'); ?>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size:12.5px; font-weight:600; color:#475569;">
                                        <?php echo htmlspecialchars($d['established_year'] ?? '2008'); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="?tab=faculty&dept=<?php echo urlencode($d['dept_code']); ?>" 
                                       style="display:inline-flex; align-items:center; gap:6px; text-decoration:none; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; background:<?php echo $fCount > 0 ? '#ecfdf5' : '#fef2f2'; ?>; color:<?php echo $fCount > 0 ? '#059669' : '#dc2626'; ?>; border:1px solid <?php echo $fCount > 0 ? '#a7f3d0' : '#fecaca'; ?>;"
                                       title="Click to view and manage faculty members for <?php echo $dCode; ?>">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $fCount; ?> Faculty</span>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($hodName): ?>
                                        <div style="display:inline-flex; align-items:center; gap:6px;">
                                            <i class="fas fa-user-tie" style="color:#f59e0b; font-size:12px;"></i>
                                            <span style="font-size:12.5px; font-weight:600; color:#0f172a;"><?php echo $hodName; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <a href="?tab=faculty&dept=<?php echo urlencode($d['dept_code']); ?>" style="display:inline-block; font-size:11.5px; color:#d97706; background:#fffbeb; padding:2px 8px; border-radius:6px; border:1px dashed #fcd34d; text-decoration:none;">
                                            + Assign HOD
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($isActive): ?>
                                        <span class="status-pill-dot status-active" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">&bull; ACTIVE</span>
                                    <?php else: ?>
                                        <span class="status-pill-dot status-pending" style="background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">&bull; INACTIVE</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <div style="display:inline-flex; align-items:center; gap:6px; justify-content:flex-end;">
                                        <!-- Manage Faculty Button -->
                                        <a href="?tab=faculty&dept=<?php echo urlencode($d['dept_code']); ?>" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px; text-decoration:none;" title="Manage <?php echo $dCode; ?> Faculty">
                                            <i class="fas fa-users"></i> Staff
                                        </a>

                                        <!-- Edit Department Button -->
                                        <button type="button" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px;" 
                                                onclick='openEditDepartmentModal(<?php echo json_encode($d, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' 
                                                title="Edit Department Details">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete Button with Strict Protection Check -->
                                        <?php if ($fCount > 0): ?>
                                            <button type="button" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px; color:#94a3b8; border-color:#e2e8f0; cursor:not-allowed;" 
                                                    onclick="alert('Cannot delete department \'<?php echo addslashes($dName); ?>\' (<?php echo $dCode; ?>): It currently has <?php echo $fCount; ?> assigned faculty record(s). Please reassign or delete these faculty records first.')"
                                                    title="Deletion Protected: <?php echo $fCount; ?> faculty associated">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <form action="../backend/crud.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to permanently delete department \'<?php echo addslashes($dName); ?>\'?');">
                                                <input type="hidden" name="action" value="delete_department">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$d['id']; ?>">
                                                <button type="submit" class="btn-outline-pill" style="padding:4px 9px; font-size:11.5px; color:#ef4444; border-color:#fecaca;" title="Delete Department">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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
    <?php echo render_pagination_bar($departments_pagination, ['tab' => 'departments', 'dept_search' => $dept_search]); ?>
</div>
