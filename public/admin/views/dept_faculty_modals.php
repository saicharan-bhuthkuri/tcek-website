<?php
/**
 * Modals and Supporting JavaScript for Department and Faculty Management
 * Includes:
 * - Add Department Modal
 * - Edit Department Modal
 * - Add Faculty Modal (with 6 designated roles, HOD & R&D auto-linking)
 * - Edit Faculty Modal
 * - Quick HOD Assignment Modal
 */
?>

<!-- 1. Modal: Add New Department -->
<div class="modal-overlay" id="modalAddDepartment" onclick="handleBackdropClick(event, 'modalAddDepartment')">
    <div class="modal-dialog" style="max-width: 680px;">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle" style="color:#00b894;"></i> Add New Department</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalAddDepartment')">&times;</button>
        </div>
        <form action="../backend/crud.php" method="POST">
            <input type="hidden" name="action" value="add_department">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="modal-body" style="padding: 24px;">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Department Code <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="dept_code" class="form-control" placeholder="e.g. CSE, MECH" required style="text-transform:uppercase; font-weight:700;">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Department Name <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Computer Science &amp; Engineering" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Degree Program</label>
                        <select name="degree_level" class="form-control">
                            <option value="B.Tech">B.Tech (UG)</option>
                            <option value="Diploma">Polytechnic Diploma</option>
                            <option value="MBA">MBA (PG)</option>
                            <option value="M.Tech">M.Tech (PG)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Intake Capacity</label>
                        <input type="text" name="intake" class="form-control" placeholder="e.g. 60 Seats" value="60 Seats">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Duration / Year</label>
                        <input type="text" name="duration" class="form-control" placeholder="e.g. 4 Years" value="4 Years">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Est. Year</label>
                        <input type="number" name="established_year" class="form-control" value="2008" min="1950" max="2030">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Icon Class</label>
                        <input type="text" name="icon_class" class="form-control" value="fas fa-graduation-cap" placeholder="fas fa-laptop-code">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Theme Style</label>
                        <select name="theme_class" class="form-control">
                            <option value="theme-cse">CSE (Emerald / Blue)</option>
                            <option value="theme-ece">ECE (Blue / Cyan)</option>
                            <option value="theme-eee">EEE (Amber / Gold)</option>
                            <option value="theme-aiml">AIML (Purple / Violet)</option>
                            <option value="theme-cse-aiml">CSE-AIML (Teal)</option>
                            <option value="theme-hs">H&amp;S (Indigo / Slate)</option>
                            <option value="theme-mba">MBA (Crimson / Rose)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Department Description / Tagline</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief academic overview of the department..."></textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Department Vision</label>
                        <textarea name="vision" class="form-control" rows="3" placeholder="Vision statement..."></textarea>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Department Mission</label>
                        <textarea name="mission" class="form-control" rows="3" placeholder="Mission objectives..."></textarea>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Public Page URL</label>
                        <input type="text" name="page_url" class="form-control" placeholder="e.g. department.php?slug=cse">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalAddDepartment')">Cancel</button>
                <button type="submit" class="btn-emerald-pill"><i class="fas fa-save"></i> Save Department</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal: Edit Department -->
<div class="modal-overlay" id="modalEditDepartment" onclick="handleBackdropClick(event, 'modalEditDepartment')">
    <div class="modal-dialog" style="max-width: 680px;">
        <div class="modal-header">
            <h3><i class="fas fa-edit" style="color:#00b894;"></i> Edit Department Details</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalEditDepartment')">&times;</button>
        </div>
        <form action="../backend/crud.php" method="POST">
            <input type="hidden" name="action" value="update_department">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="id" id="edit_dept_id">

            <div class="modal-body" style="padding: 24px;">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Department Code <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="dept_code" id="edit_dept_code" class="form-control" required style="text-transform:uppercase; font-weight:700;">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Department Name <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="name" id="edit_dept_name" class="form-control" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Degree Program</label>
                        <select name="degree_level" id="edit_dept_degree" class="form-control">
                            <option value="B.Tech">B.Tech (UG)</option>
                            <option value="Diploma">Polytechnic Diploma</option>
                            <option value="MBA">MBA (PG)</option>
                            <option value="M.Tech">M.Tech (PG)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Intake Capacity</label>
                        <input type="text" name="intake" id="edit_dept_intake" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Duration</label>
                        <input type="text" name="duration" id="edit_dept_duration" class="form-control">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Est. Year</label>
                        <input type="number" name="established_year" id="edit_dept_year" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Icon Class</label>
                        <input type="text" name="icon_class" id="edit_dept_icon" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Theme Class</label>
                        <select name="theme_class" id="edit_dept_theme" class="form-control">
                            <option value="theme-cse">CSE (Emerald / Blue)</option>
                            <option value="theme-ece">ECE (Blue / Cyan)</option>
                            <option value="theme-eee">EEE (Amber / Gold)</option>
                            <option value="theme-aiml">AIML (Purple / Violet)</option>
                            <option value="theme-cse-aiml">CSE-AIML (Teal)</option>
                            <option value="theme-hs">H&amp;S (Indigo / Slate)</option>
                            <option value="theme-mba">MBA (Crimson / Rose)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Description / Tagline</label>
                    <textarea name="description" id="edit_dept_description" class="form-control" rows="2"></textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Vision</label>
                        <textarea name="vision" id="edit_dept_vision" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Mission</label>
                        <textarea name="mission" id="edit_dept_mission" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:16px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Public Page URL</label>
                        <input type="text" name="page_url" id="edit_dept_page_url" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Order</label>
                        <input type="number" name="display_order" id="edit_dept_order" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Status</label>
                        <select name="is_active" id="edit_dept_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditDepartment')">Cancel</button>
                <button type="submit" class="btn-emerald-pill"><i class="fas fa-check"></i> Update Department</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal: Add Faculty Member -->
<div class="modal-overlay" id="modalAddFaculty" onclick="handleBackdropClick(event, 'modalAddFaculty')">
    <div class="modal-dialog" style="max-width: 720px;">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus" style="color:#00b894;"></i> Add Faculty Member</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalAddFaculty')">&times;</button>
        </div>
        <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_faculty">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="modal-body" style="padding: 24px; max-height:75vh; overflow-y:auto;">
                <!-- Full Name and Department Selection -->
                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Faculty Full Name <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Dr. Rajesh Kumar / P. Padmini" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Assigned Department <span style="color:#ef4444;">*</span>
                        </label>
                        <select name="dept_code" id="add_faculty_dept_code" class="form-control" required>
                            <?php foreach ($all_depts_list as $d): ?>
                                <option value="<?php echo htmlspecialchars($d['dept_code']); ?>">
                                    <?php echo htmlspecialchars($d['dept_code']); ?> &ndash; <?php echo htmlspecialchars($d['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Academic Title and Standard Role Category -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Academic Designation Title <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="designation" id="add_faculty_desig" class="form-control" placeholder="e.g. Associate Professor, Assistant Professor" value="Assistant Professor" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Mandatory Role Category <span style="color:#ef4444;">*</span>
                        </label>
                        <select name="role_category" id="add_faculty_role_cat" class="form-control" required onchange="onRoleCategoryChange(this, 'add')">
                            <option value="Faculty – Junior (JR)">Faculty – Junior (JR)</option>
                            <option value="Faculty – Senior (SR)">Faculty – Senior (SR)</option>
                            <option value="HOD">HOD – Head of Department</option>
                            <option value="R&D">R&D</option>
                            <option value="R&D + Senior Faculty (SR)">R&D + Senior Faculty (SR)</option>
                            <option value="R&D + Junior Faculty (JR)">R&D + Junior Faculty (JR)</option>
                        </select>
                    </div>
                </div>

                <!-- Role Flags Bar -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; margin-bottom:14px; display:flex; align-items:center; gap:24px;">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" name="is_hod" id="add_faculty_is_hod" value="1" style="width:17px; height:17px;">
                        <span><i class="fas fa-crown" style="color:#f59e0b;"></i> Appoint as Active Head of Department (HOD)</span>
                    </label>
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" name="is_rnd" id="add_faculty_is_rnd" value="1" style="width:17px; height:17px;">
                        <span><i class="fas fa-flask" style="color:#8b5cf6;"></i> Research &amp; Development (R&amp;D) Cell</span>
                    </label>
                </div>

                <!-- Qualifications, JNTUH Reg ID, Experience -->
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Qualification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="e.g. M.Tech, (Ph.D)" value="M.Tech">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">JNTUH Reg. ID</label>
                        <input type="text" name="jntuh_reg_id" class="form-control" placeholder="e.g. 2717-150427-180153" value="N/A">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Experience</label>
                        <input type="text" name="experience" class="form-control" placeholder="e.g. 8 Years" value="5+ Years">
                    </div>
                </div>

                <!-- Contact Details -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="faculty@tcek.in">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Phone / Mobile</label>
                        <input type="text" name="phone" class="form-control" placeholder="9848012345">
                    </div>
                </div>

                <!-- Profile Photo Upload -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                        Profile Photo (JPG, PNG, WEBP)
                    </label>
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding:6px 12px; height:auto;">
                    <span style="font-size:11.5px; color:#64748b;">Square portrait recommended. Standard avatar will be used if omitted.</span>
                </div>

                <!-- Bio & Order -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Faculty Bio / Academic Profile</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Academic background, research areas, and institutional responsibilities..."></textarea>
                </div>

                <div class="form-group" style="margin:0; width:160px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Display Order</label>
                    <input type="number" name="display_order" class="form-control" value="0">
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalAddFaculty')">Cancel</button>
                <button type="submit" class="btn-emerald-pill"><i class="fas fa-save"></i> Save Faculty Member</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Modal: Edit Faculty Member -->
<div class="modal-overlay" id="modalEditFaculty" onclick="handleBackdropClick(event, 'modalEditFaculty')">
    <div class="modal-dialog" style="max-width: 720px;">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit" style="color:#00b894;"></i> Edit Faculty Member Profile</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalEditFaculty')">&times;</button>
        </div>
        <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_faculty">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="id" id="edit_faculty_id">

            <div class="modal-body" style="padding: 24px; max-height:75vh; overflow-y:auto;">
                <!-- Full Name and Department Selection -->
                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Faculty Full Name <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="full_name" id="edit_faculty_name" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Assigned Department <span style="color:#ef4444;">*</span>
                        </label>
                        <select name="dept_code" id="edit_faculty_dept" class="form-control" required>
                            <?php foreach ($all_depts_list as $d): ?>
                                <option value="<?php echo htmlspecialchars($d['dept_code']); ?>">
                                    <?php echo htmlspecialchars($d['dept_code']); ?> &ndash; <?php echo htmlspecialchars($d['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Academic Title and Standard Role Category -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Academic Designation Title <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="designation" id="edit_faculty_desig" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Mandatory Role Category <span style="color:#ef4444;">*</span>
                        </label>
                        <select name="role_category" id="edit_faculty_role_cat" class="form-control" required onchange="onRoleCategoryChange(this, 'edit')">
                            <option value="Faculty – Junior (JR)">Faculty – Junior (JR)</option>
                            <option value="Faculty – Senior (SR)">Faculty – Senior (SR)</option>
                            <option value="HOD">HOD – Head of Department</option>
                            <option value="R&D">R&D</option>
                            <option value="R&D + Senior Faculty (SR)">R&D + Senior Faculty (SR)</option>
                            <option value="R&D + Junior Faculty (JR)">R&D + Junior Faculty (JR)</option>
                        </select>
                    </div>
                </div>

                <!-- Role Flags Bar -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; margin-bottom:14px; display:flex; align-items:center; gap:24px;">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" name="is_hod" id="edit_faculty_is_hod" value="1" style="width:17px; height:17px;">
                        <span><i class="fas fa-crown" style="color:#f59e0b;"></i> Appoint as Active Head of Department (HOD)</span>
                    </label>
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b;">
                        <input type="checkbox" name="is_rnd" id="edit_faculty_is_rnd" value="1" style="width:17px; height:17px;">
                        <span><i class="fas fa-flask" style="color:#8b5cf6;"></i> Research &amp; Development (R&amp;D) Cell</span>
                    </label>
                </div>

                <!-- Qualifications, JNTUH Reg ID, Experience -->
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Qualification</label>
                        <input type="text" name="qualification" id="edit_faculty_qual" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">JNTUH Reg. ID</label>
                        <input type="text" name="jntuh_reg_id" id="edit_faculty_reg" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Experience</label>
                        <input type="text" name="experience" id="edit_faculty_exp" class="form-control">
                    </div>
                </div>

                <!-- Contact Details -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Email Address</label>
                        <input type="email" name="email" id="edit_faculty_email" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Phone / Mobile</label>
                        <input type="text" name="phone" id="edit_faculty_phone" class="form-control">
                    </div>
                </div>

                <!-- Profile Photo Upload -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                        Update Profile Photo
                    </label>
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding:6px 12px; height:auto;">
                    <span style="font-size:11.5px; color:#64748b;">Leave blank to preserve current photo.</span>
                </div>

                <!-- Bio & Order & Status -->
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Faculty Bio / Profile Summary</label>
                    <textarea name="bio" id="edit_faculty_bio" class="form-control" rows="3"></textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Display Order</label>
                        <input type="number" name="display_order" id="edit_faculty_order" class="form-control">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">Profile Status</label>
                        <select name="is_active" id="edit_faculty_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive / On Leave</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditFaculty')">Cancel</button>
                <button type="submit" class="btn-emerald-pill"><i class="fas fa-check"></i> Update Faculty Member</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. Modal: Quick Assign Department HOD -->
<div class="modal-overlay" id="modalAssignHOD" onclick="handleBackdropClick(event, 'modalAssignHOD')">
    <div class="modal-dialog" style="max-width: 520px;">
        <div class="modal-header">
            <h3><i class="fas fa-crown" style="color:#f59e0b;"></i> Appoint Head of Department (HOD)</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalAssignHOD')">&times;</button>
        </div>
        <form action="../backend/crud.php" method="POST">
            <input type="hidden" name="action" value="assign_hod">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="modal-body" style="padding: 24px;">
                <p style="font-size:13px; color:#475569; margin-bottom:16px; line-height:1.5;">
                    Select the department and the faculty member to appoint as the active Head of Department. 
                    Any previous HOD for this department will be automatically reassigned to ensure <strong>at most one active HOD</strong>.
                </p>

                <!-- 1. Select Department -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                        Department <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="dept_code" id="assign_hod_dept_select" class="form-control" required onchange="filterHODCandidates(this.value)">
                        <?php foreach ($all_depts_list as $d): ?>
                            <option value="<?php echo htmlspecialchars($d['dept_code']); ?>" <?php echo ($faculty_dept_filter === $d['dept_code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d['dept_code']); ?> &ndash; <?php echo htmlspecialchars($d['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Select Faculty Candidate -->
                <div class="form-group" style="margin:0;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:5px;">
                        Select Faculty Member to Appoint <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="faculty_id" id="assign_hod_faculty_select" class="form-control" required>
                        <!-- Populated dynamically via JS -->
                    </select>
                </div>
            </div>

            <div class="modal-footer" style="padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalAssignHOD')">Cancel</button>
                <button type="submit" class="btn-emerald-pill" style="background:#d97706; border-color:#d97706;">
                    <i class="fas fa-crown"></i> Appoint as HOD
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Global faculty list for HOD assignment candidates
const tcekAllFaculty = <?php echo json_encode(array_map(function($f) {
    return [
        'id'        => (int)$f['id'],
        'name'      => $f['full_name'],
        'dept_code' => $f['dept_code'],
        'is_hod'    => !empty($f['is_hod']) ? 1 : 0,
        'desig'     => $f['designation']
    ];
}, get_faculty_members([])), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

function filterHODCandidates(deptCode) {
    const select = document.getElementById('assign_hod_faculty_select');
    if (!select) return;
    select.innerHTML = '';
    
    const candidates = tcekAllFaculty.filter(f => f.dept_code.toUpperCase() === deptCode.toUpperCase());
    if (candidates.length === 0) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = '-- No faculty members in this department yet --';
        select.appendChild(opt);
        return;
    }
    
    candidates.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id;
        opt.textContent = f.name + ' (' + f.desig + ')' + (f.is_hod ? ' [Current HOD]' : '');
        if (f.is_hod) opt.selected = true;
        select.appendChild(opt);
    });
}

// Initialize HOD candidate list on load
document.addEventListener('DOMContentLoaded', function() {
    const deptSelect = document.getElementById('assign_hod_dept_select');
    if (deptSelect) {
        filterHODCandidates(deptSelect.value);
    }
});

// Open Add Faculty modal with specific department pre-selected
function openAddFacultyModal(defaultDept) {
    const deptSelect = document.getElementById('add_faculty_dept_code');
    if (deptSelect && defaultDept) {
        for (let i = 0; i < deptSelect.options.length; i++) {
            if (deptSelect.options[i].value.toUpperCase() === defaultDept.toUpperCase()) {
                deptSelect.selectedIndex = i;
                break;
            }
        }
    }
    openModal('modalAddFaculty');
}

// Auto-sync HOD & R&D checkboxes based on Role Category selection
function onRoleCategoryChange(selectElem, prefix) {
    const val = selectElem.value;
    const hodBox = document.getElementById(prefix + '_faculty_is_hod');
    const rndBox = document.getElementById(prefix + '_faculty_is_rnd');
    const desigInput = document.getElementById(prefix + '_faculty_desig');

    if (val === 'HOD') {
        if (hodBox) hodBox.checked = true;
        if (desigInput && !desigInput.value.toLowerCase().includes('head')) {
            desigInput.value = 'Head of Department & Assoc. Prof';
        }
    } else {
        if (hodBox) hodBox.checked = false;
    }

    if (val.includes('R&D')) {
        if (rndBox) rndBox.checked = true;
    } else {
        if (rndBox) rndBox.checked = false;
    }
}

// Populate and open Edit Department Modal
function openEditDepartmentModal(d) {
    if (!d) return;
    document.getElementById('edit_dept_id').value          = d.id || '';
    document.getElementById('edit_dept_code').value        = d.dept_code || '';
    document.getElementById('edit_dept_name').value        = d.name || '';
    document.getElementById('edit_dept_degree').value      = d.degree_level || 'B.Tech';
    document.getElementById('edit_dept_intake').value      = d.intake || '';
    document.getElementById('edit_dept_duration').value    = d.duration || '';
    document.getElementById('edit_dept_year').value        = d.established_year || '2008';
    document.getElementById('edit_dept_icon').value        = d.icon_class || 'fas fa-graduation-cap';
    document.getElementById('edit_dept_theme').value       = d.theme_class || 'theme-cse';
    document.getElementById('edit_dept_description').value = d.description || '';
    document.getElementById('edit_dept_vision').value      = d.vision || '';
    document.getElementById('edit_dept_mission').value     = d.mission || '';
    document.getElementById('edit_dept_page_url').value    = d.page_url || '';
    document.getElementById('edit_dept_order').value       = d.display_order || 0;
    document.getElementById('edit_dept_active').value      = (d.is_active !== undefined) ? d.is_active : 1;

    openModal('modalEditDepartment');
}

// Populate and open Edit Faculty Modal
function openEditFacultyModal(f) {
    if (!f) return;
    document.getElementById('edit_faculty_id').value       = f.id || '';
    document.getElementById('edit_faculty_name').value     = f.full_name || '';
    document.getElementById('edit_faculty_desig').value    = f.designation || '';
    document.getElementById('edit_faculty_role_cat').value = f.role_category || 'Faculty – Junior (JR)';
    document.getElementById('edit_faculty_qual').value     = f.qualification || '';
    document.getElementById('edit_faculty_reg').value      = f.jntuh_reg_id || '';
    document.getElementById('edit_faculty_exp').value      = f.experience || '';
    document.getElementById('edit_faculty_email').value    = f.email || '';
    document.getElementById('edit_faculty_phone').value    = f.phone || '';
    document.getElementById('edit_faculty_bio').value      = f.bio || '';
    document.getElementById('edit_faculty_order').value    = f.display_order || 0;
    document.getElementById('edit_faculty_active').value   = (f.is_active !== undefined) ? f.is_active : 1;

    const deptSelect = document.getElementById('edit_faculty_dept');
    if (deptSelect && f.dept_code) {
        for (let i = 0; i < deptSelect.options.length; i++) {
            if (deptSelect.options[i].value.toUpperCase() === f.dept_code.toUpperCase()) {
                deptSelect.selectedIndex = i;
                break;
            }
        }
    }

    const hodBox = document.getElementById('edit_faculty_is_hod');
    if (hodBox) hodBox.checked = (f.is_hod == 1 || f.role_category === 'HOD');

    const rndBox = document.getElementById('edit_faculty_is_rnd');
    if (rndBox) rndBox.checked = (f.is_rnd == 1 || (f.role_category && f.role_category.includes('R&D')));

    openModal('modalEditFaculty');
}
</script>
