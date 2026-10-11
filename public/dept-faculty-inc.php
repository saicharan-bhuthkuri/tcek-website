<?php
/**
 * Synchronized HOD Showcase and Faculty Directory Section
 * Dynamically rendered from the single source of truth database/backend
 * Automatically updates when changes are saved in the Admin Dashboard.
 */

require_once __DIR__ . '/backend/crud.php';

// Fallback to active dept from sidebar if not explicitly set
if (!isset($dept_code) && isset($active_dept)) {
    $dept_code = strtoupper($active_dept);
    if ($dept_code === 'HS') $dept_code = 'H&S';
    if ($dept_code === 'CSE_AIML') $dept_code = 'CSE-AIML';
}

$dept_code = strtoupper($dept_code ?? 'CSE');
$hod_profile  = get_department_hod($dept_code);
$faculty_list = get_department_faculty($dept_code, true);
?>

<!-- Section 3: Head of the Department (HOD) - Real-time Synced -->
<section class="dept-section-card" id="hod">
    <div class="dept-section-head">
        <div class="dept-section-icon-badge"><i class="fas fa-user-tie"></i></div>
        <div>
            <h2>Head of the Department</h2>
            <div class="dept-section-sub">Leadership profile, academic credentials, and departmental vision</div>
        </div>
    </div>
    <?php if ($hod_profile): ?>
        <?php
        $hod_name = trim($hod_profile['full_name'] ?? '');
        $hod_initial = strtoupper(mb_substr($hod_name, 0, 1, 'UTF-8')) ?: 'H';
        $hod_photo = trim($hod_profile['profile_image'] ?? '');
        $hod_has_photo = false;
        if (!empty($hod_photo) && $hod_photo !== 'assets/Dept/faculty-avatar.png') {
            $photo_disk = __DIR__ . '/' . ltrim($hod_photo, '/');
            if (file_exists($photo_disk)) {
                $hod_has_photo = true;
            }
        }
        ?>
        <div class="dept-hod-showcase">
            <div class="dept-hod-photo-wrap">
                <?php if ($hod_has_photo): ?>
                    <img src="<?php echo htmlspecialchars($hod_photo); ?>" 
                         alt="<?php echo htmlspecialchars($hod_name); ?> - HOD <?php echo htmlspecialchars($dept_code); ?>" 
                         class="dept-hod-photo"
                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                    <div class="dept-hod-letter-avatar" style="display:none;">
                        <span class="avatar-letter"><?php echo $hod_initial; ?></span>
                        <span class="avatar-sub">HOD</span>
                    </div>
                <?php else: ?>
                    <div class="dept-hod-letter-avatar">
                        <span class="avatar-letter"><?php echo $hod_initial; ?></span>
                        <span class="avatar-sub">HOD</span>
                    </div>
                <?php endif; ?>
                <span class="dept-hod-badge-ribbon"><i class="fas fa-check-circle"></i> Department Head</span>
            </div>
            <div class="dept-hod-details">
                <h3><?php echo htmlspecialchars($hod_profile['full_name']); ?></h3>
                <span class="dept-hod-desig"><?php echo htmlspecialchars($hod_profile['designation']); ?></span>
                
                <div class="dept-hod-credentials-row">
                    <?php if (!empty($hod_profile['qualification'])): ?>
                        <span class="dept-cred-chip"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($hod_profile['qualification']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($hod_profile['jntuh_reg_id']) && $hod_profile['jntuh_reg_id'] !== 'N/A'): ?>
                        <span class="dept-cred-chip"><i class="fas fa-id-badge"></i> JNTUH Reg: <?php echo htmlspecialchars($hod_profile['jntuh_reg_id']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($hod_profile['experience'])): ?>
                        <span class="dept-cred-chip"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($hod_profile['experience']); ?></span>
                    <?php endif; ?>
                </div>

                <p class="dept-hod-bio">
                    <?php echo nl2br(htmlspecialchars($hod_profile['bio'] ?: 'Head of Department actively directing academic programs, student technical chapters, and research development at Trinity College of Engineering & Technology.')); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div style="padding:28px; text-align:center; color:#64748b;">
            <i class="fas fa-user-tie" style="font-size:32px; color:#cbd5e1; margin-bottom:8px; display:block;"></i>
            HOD profile currently being assigned for this academic department.
        </div>
    <?php endif; ?>
</section>

<!-- Section 4: Faculty Directory - Real-time Synced -->
<section class="dept-section-card" id="faculty">
    <div class="dept-section-head">
        <div class="dept-section-icon-badge"><i class="fas fa-chalkboard-teacher"></i></div>
        <div>
            <h2>Faculty Directory</h2>
            <div class="dept-section-sub">Distinguished professors, associate professors, and research mentors</div>
        </div>
    </div>
    <p class="dept-section-p" style="margin-bottom: 20px;">
        The department is powered by experienced and devoted faculty members committed to student academic excellence and research mentorship.
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
                <?php if (empty($faculty_list)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:#64748b;">
                            No faculty members currently listed for this department.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $gradList = [
                        'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                        'linear-gradient(135deg, #10b981, #059669)',
                        'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                        'linear-gradient(135deg, #06b6d4, #0891b2)',
                        'linear-gradient(135deg, #ec4899, #be185d)',
                        'linear-gradient(135deg, #f97316, #c2410c)'
                    ];
                    foreach ($faculty_list as $idx => $f): 
                        $fName = trim($f['full_name'] ?? '');
                        $fInitial = strtoupper(mb_substr($fName, 0, 1, 'UTF-8')) ?: 'F';
                        $fPhoto = trim($f['profile_image'] ?? '');
                        $fHasPhoto = false;
                        if (!empty($fPhoto) && $fPhoto !== 'assets/Dept/faculty-avatar.png') {
                            $fDisk = __DIR__ . '/' . ltrim($fPhoto, '/');
                            if (file_exists($fDisk)) {
                                $fHasPhoto = true;
                            }
                        }
                        $fGrad = !empty($f['is_hod']) ? 'linear-gradient(135deg, #f59e0b, #d97706)' : $gradList[abs(crc32($fName)) % count($gradList)];
                    ?>
                        <tr>
                            <td data-label="S.No"><?php echo ($idx + 1); ?></td>
                            <td data-label="Faculty Name">
                                <div style="display:inline-flex; align-items:center; gap:10px;">
                                    <?php if ($fHasPhoto): ?>
                                        <img src="<?php echo htmlspecialchars($fPhoto); ?>" alt="<?php echo htmlspecialchars($fName); ?>" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid <?php echo !empty($f['is_hod']) ? '#f59e0b' : '#e2e8f0'; ?>; flex-shrink:0;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                        <span style="display:none; width:36px; height:36px; border-radius:50%; background:<?php echo $fGrad; ?>; color:#ffffff; font-size:14px; font-weight:700; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                                            <?php echo $fInitial; ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="display:inline-flex; width:36px; height:36px; border-radius:50%; background:<?php echo $fGrad; ?>; color:#ffffff; font-size:14px; font-weight:700; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                                            <?php echo $fInitial; ?>
                                        </span>
                                    <?php endif; ?>
                                    <div>
                                        <?php if (!empty($f['is_hod'])): ?>
                                            <strong class="dept-faculty-name" style="color:#0f172a;"><?php echo htmlspecialchars($fName); ?> <i class="fas fa-crown" style="color:#f59e0b; font-size:11px; margin-left:3px;" title="Head of Department"></i></strong>
                                        <?php else: ?>
                                            <span class="dept-faculty-name"><?php echo htmlspecialchars($fName); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Designation">
                                <span class="dept-role-pill <?php echo !empty($f['is_hod']) ? 'hod' : ''; ?>">
                                    <?php echo htmlspecialchars($f['designation']); ?>
                                </span>
                            </td>
                            <td data-label="Qualification">
                                <span class="dept-qual-pill"><?php echo htmlspecialchars($f['qualification']); ?></span>
                            </td>
                            <td data-label="Reg ID">
                                <span class="dept-reg-id"><?php echo htmlspecialchars($f['jntuh_reg_id']); ?></span>
                            </td>
                            <td data-label="Experience"><?php echo htmlspecialchars($f['experience']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
