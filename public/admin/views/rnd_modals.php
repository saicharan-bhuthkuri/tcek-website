<?php
/**
 * Modals and Supporting JavaScript for R&D Department Rankings Management
 * Includes:
 * - Add New Ranking Result Modal
 * - Edit Ranking Result Modal
 * - Real-time duplicate validation & presets
 */
?>

<!-- 1. Modal: Add New Ranking Result -->
<div class="modal-overlay" id="modalAddRndRanking" onclick="handleBackdropClick(event, 'modalAddRndRanking')">
    <div class="modal-dialog" style="max-width: 720px;">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle" style="color:#d97706;"></i> Add New Ranking Result</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalAddRndRanking')">&times;</button>
        </div>
        <form action="../backend/rnd_crud.php" method="POST" id="formAddRndRanking" onsubmit="return validateRndSubmit('add')">
            <input type="hidden" name="rnd_action" value="add_rnd_ranking">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="modal-body" style="padding: 24px; max-height: calc(85vh - 120px); overflow-y: auto;">
                <!-- Real-time Duplicate Warning Banner -->
                <div id="addRndDuplicateAlert" class="alert alert-danger" style="display:none; padding:10px 14px; margin-bottom:16px; font-size:12.5px; border-radius:8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="addRndDuplicateText">A ranking result for this department already exists in the selected period.</span>
                </div>

                <!-- Section 1: Evaluation Period -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="far fa-calendar-alt" style="color:#d97706;"></i> Evaluation Period &amp; Timing
                    </span>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Academic Year <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="academic_year" id="add_rnd_year" class="form-control" placeholder="e.g. 2025-26" value="2025-26" required oninput="checkRndDuplicateClient('add')">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Quarter <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="quarter" id="add_rnd_quarter" class="form-control" required onchange="checkRndDuplicateClient('add')">
                                <option value="Q4" selected>Q4 (Apr - Jun)</option>
                                <option value="Q3">Q3 (Jan - Mar)</option>
                                <option value="Q2">Q2 (Oct - Dec)</option>
                                <option value="Q1">Q1 (Jul - Sep)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Publication Date <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="publication_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:10px; margin-bottom:0;">
                        <label style="display:block; font-size:11.5px; font-weight:600; color:#64748b; margin-bottom:4px;">
                            Quarter Announcement Title / Subtitle (Optional)
                        </label>
                        <input type="text" name="quarter_label" class="form-control" placeholder="e.g. Annual Research &amp; Benchmark Review (AY 2025-26)">
                    </div>
                </div>

                <!-- Section 2: Department Information -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-university" style="color:#2563eb;"></i> Department Details
                    </span>
                    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Department Code <span style="color:#ef4444;">*</span>
                            </label>
                            <input list="rndDeptPresets" name="department" id="add_rnd_dept" class="form-control" placeholder="Select or type (e.g. AIML)" required oninput="onRndDeptChange('add'); checkRndDuplicateClient('add');" style="font-weight:700;">
                            <datalist id="rndDeptPresets">
                                <option value="AIML">Artificial Intelligence &amp; Machine Learning</option>
                                <option value="CSE &amp; CSM">Computer Science &amp; Engineering / CSM</option>
                                <option value="EEE">Electrical &amp; Electronics Engineering</option>
                                <option value="ECE">Electronics &amp; Communication Engineering</option>
                                <option value="MBA">Department of Management Studies</option>
                                <option value="CSE">Computer Science &amp; Engineering</option>
                                <option value="CSM">Computer Science &amp; Engineering (AI &amp; ML)</option>
                                <option value="MECH">Mechanical Engineering</option>
                                <option value="CIVIL">Civil Engineering</option>
                                <option value="H&amp;S">Humanities &amp; Sciences</option>
                            </datalist>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Full Department Name <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="department_name" id="add_rnd_dept_name" class="form-control" placeholder="e.g. Artificial Intelligence &amp; Machine Learning" required>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ranking Position & Award -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-trophy" style="color:#f59e0b;"></i> Ranking Position &amp; Honors
                    </span>
                    <div style="display:grid; grid-template-columns: 100px 1fr 1fr; gap:14px; margin-bottom:12px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Rank # <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="rank" id="add_rnd_rank" class="form-control" required onchange="onRndRankChange('add')" style="font-weight:800; font-size:14px;">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                                <option value="6">6th</option>
                                <option value="7">7th</option>
                                <option value="8">8th</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Award Category Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="award_category" id="add_rnd_cat" class="form-control" value="1ST · GOLD" placeholder="e.g. 1ST · GOLD" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Honor Badge Banner <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="award_badge" id="add_rnd_badge" class="form-control" value="🏆 GOLD CHAMPION" placeholder="e.g. 🏆 GOLD CHAMPION" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Evaluation Score / Points (Optional)
                            </label>
                            <input type="number" step="0.01" name="score" class="form-control" placeholder="e.g. 96.50">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Publication Status <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="status" class="form-control" required>
                                <option value="published" selected>Published (Visible on Website)</option>
                                <option value="archived">Archived (In History Archive)</option>
                                <option value="draft">Draft (Internal Only)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Highlights & Achievements -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-list-check" style="color:#10b981;"></i> Department Key Achievements
                    </span>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Achievements &amp; Benchmarks (One bullet per line) <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea name="achievements" id="add_rnd_achievements" class="form-control" rows="4" placeholder="High-Impact Research Papers&#10;Patents &amp; Innovation Leads&#10;NPTEL &amp; IIC Star Rating" required></textarea>
                        <span style="font-size:11px; color:#64748b; display:block; margin-top:4px;">
                            Each line will be rendered with an emerald checkmark badge on the dynamic ranking card.
                        </span>
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Citation / Description Statement (Optional)
                        </label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of department's research breakthroughs, publications, or patent filings..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalAddRndRanking')">Cancel</button>
                <button type="submit" id="btnSubmitAddRnd" class="btn-emerald-pill" style="background:#d97706; border-color:#d97706; padding:8px 24px;">
                    <i class="fas fa-check"></i> Publish Ranking Result
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal: Edit Ranking Result -->
<div class="modal-overlay" id="modalEditRndRanking" onclick="handleBackdropClick(event, 'modalEditRndRanking')">
    <div class="modal-dialog" style="max-width: 720px;">
        <div class="modal-header">
            <h3><i class="fas fa-edit" style="color:#2563eb;"></i> Edit Ranking Result</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalEditRndRanking')">&times;</button>
        </div>
        <form action="../backend/rnd_crud.php" method="POST" id="formEditRndRanking" onsubmit="return validateRndSubmit('edit')">
            <input type="hidden" name="rnd_action" value="update_rnd_ranking">
            <input type="hidden" name="id" id="edit_rnd_id">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="modal-body" style="padding: 24px; max-height: calc(85vh - 120px); overflow-y: auto;">
                <!-- Real-time Duplicate Warning Banner -->
                <div id="editRndDuplicateAlert" class="alert alert-danger" style="display:none; padding:10px 14px; margin-bottom:16px; font-size:12.5px; border-radius:8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="editRndDuplicateText">Another ranking result for this department already exists in the selected period.</span>
                </div>

                <!-- Section 1: Evaluation Period -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="far fa-calendar-alt" style="color:#2563eb;"></i> Evaluation Period &amp; Timing
                    </span>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Academic Year <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="academic_year" id="edit_rnd_year" class="form-control" required oninput="checkRndDuplicateClient('edit')">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Quarter <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="quarter" id="edit_rnd_quarter" class="form-control" required onchange="checkRndDuplicateClient('edit')">
                                <option value="Q4">Q4 (Apr - Jun)</option>
                                <option value="Q3">Q3 (Jan - Mar)</option>
                                <option value="Q2">Q2 (Oct - Dec)</option>
                                <option value="Q1">Q1 (Jul - Sep)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Publication Date <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="publication_date" id="edit_rnd_pubdate" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:10px; margin-bottom:0;">
                        <label style="display:block; font-size:11.5px; font-weight:600; color:#64748b; margin-bottom:4px;">
                            Quarter Announcement Title / Subtitle (Optional)
                        </label>
                        <input type="text" name="quarter_label" id="edit_rnd_label" class="form-control">
                    </div>
                </div>

                <!-- Section 2: Department Information -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-university" style="color:#2563eb;"></i> Department Details
                    </span>
                    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Department Code <span style="color:#ef4444;">*</span>
                            </label>
                            <input list="rndDeptPresets" name="department" id="edit_rnd_dept" class="form-control" required oninput="onRndDeptChange('edit'); checkRndDuplicateClient('edit');" style="font-weight:700;">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Full Department Name <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="department_name" id="edit_rnd_dept_name" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ranking Position & Award -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-trophy" style="color:#f59e0b;"></i> Ranking Position &amp; Honors
                    </span>
                    <div style="display:grid; grid-template-columns: 100px 1fr 1fr; gap:14px; margin-bottom:12px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Rank # <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="rank" id="edit_rnd_rank" class="form-control" required onchange="onRndRankChange('edit')" style="font-weight:800; font-size:14px;">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                                <option value="6">6th</option>
                                <option value="7">7th</option>
                                <option value="8">8th</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Award Category Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="award_category" id="edit_rnd_cat" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Honor Badge Banner <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="award_badge" id="edit_rnd_badge" class="form-control" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Evaluation Score / Points (Optional)
                            </label>
                            <input type="number" step="0.01" name="score" id="edit_rnd_score" class="form-control">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                                Publication Status <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="status" id="edit_rnd_status" class="form-control" required>
                                <option value="published">Published (Visible on Website)</option>
                                <option value="archived">Archived (In History Archive)</option>
                                <option value="draft">Draft (Internal Only)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Highlights & Achievements -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px;">
                    <span style="display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                        <i class="fas fa-list-check" style="color:#10b981;"></i> Department Key Achievements
                    </span>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Achievements &amp; Benchmarks (One bullet per line) <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea name="achievements" id="edit_rnd_achievements" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">
                            Citation / Description Statement (Optional)
                        </label>
                        <textarea name="description" id="edit_rnd_desc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditRndRanking')">Cancel</button>
                <button type="submit" id="btnSubmitEditRnd" class="btn-emerald-pill" style="padding:8px 24px;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Known dataset for client-side duplicate checking
const ALL_RND_RECORDS = <?php echo json_encode(array_map(function($r) {
    return [
        'id'            => (int)$r['id'],
        'department'    => trim(strtolower($r['department'] ?? '')),
        'academic_year' => trim(strtolower($r['academic_year'] ?? '')),
        'quarter'       => trim(strtoupper($r['quarter'] ?? ''))
    ];
}, $all_rnd_rankings), JSON_UNESCAPED_SLASHES); ?>;

// Department Preset Directory
const DEPT_NAME_MAP = {
    'AIML': 'Artificial Intelligence & Machine Learning',
    'CSE & CSM': 'Computer Science & Engineering / CSM',
    'EEE': 'Electrical & Electronics Engineering',
    'ECE': 'Electronics & Communication Engineering',
    'MBA': 'Department of Management Studies',
    'CSE': 'Computer Science & Engineering',
    'CSM': 'Computer Science & Engineering (AI & ML)',
    'MECH': 'Mechanical Engineering',
    'CIVIL': 'Civil Engineering',
    'H&S': 'Humanities & Sciences'
};

// Rank Defaults
const RANK_PRESETS = {
    1: { cat: '1ST · GOLD', badge: '🏆 GOLD CHAMPION' },
    2: { cat: '2ND · BRONZE', badge: '🎖️ BRONZE HONOR' },
    3: { cat: '3RD · BRONZE', badge: '🎖️ BRONZE HONOR' },
    4: { cat: '4TH · EMERGING', badge: '🌱 EMERGING' },
    5: { cat: '5TH · EMERGING', badge: '🌱 EMERGING' },
    6: { cat: '6TH · MERIT', badge: '✨ MERIT HONOR' },
    7: { cat: '7TH · MERIT', badge: '✨ MERIT HONOR' },
    8: { cat: '8TH · MERIT', badge: '✨ MERIT HONOR' }
};

function onRndDeptChange(mode) {
    const deptInput = document.getElementById(mode + '_rnd_dept');
    const nameInput = document.getElementById(mode + '_rnd_dept_name');
    if (!deptInput || !nameInput) return;
    const val = deptInput.value.trim().toUpperCase();
    if (DEPT_NAME_MAP[val] && (!nameInput.value || mode === 'add')) {
        nameInput.value = DEPT_NAME_MAP[val];
    }
}

function onRndRankChange(mode) {
    const rankSelect = document.getElementById(mode + '_rnd_rank');
    const catInput = document.getElementById(mode + '_rnd_cat');
    const badgeInput = document.getElementById(mode + '_rnd_badge');
    if (!rankSelect || !catInput || !badgeInput) return;
    const r = parseInt(rankSelect.value) || 1;
    if (RANK_PRESETS[r]) {
        catInput.value = RANK_PRESETS[r].cat;
        badgeInput.value = RANK_PRESETS[r].badge;
    }
}

function checkRndDuplicateClient(mode) {
    const yearEl = document.getElementById(mode + '_rnd_year');
    const qEl = document.getElementById(mode + '_rnd_quarter');
    const deptEl = document.getElementById(mode + '_rnd_dept');
    const alertEl = document.getElementById(mode + 'RndDuplicateAlert');
    const alertText = document.getElementById(mode + 'RndDuplicateText');
    const submitBtn = document.getElementById(mode === 'add' ? 'btnSubmitAddRnd' : 'btnSubmitEditRnd');
    const excludeId = mode === 'edit' ? parseInt(document.getElementById('edit_rnd_id').value) : null;

    if (!yearEl || !qEl || !deptEl || !alertEl) return false;

    const yVal = yearEl.value.trim().toLowerCase();
    const qVal = qEl.value.trim().toUpperCase();
    const dVal = deptEl.value.trim().toLowerCase();

    if (!yVal || !qVal || !dVal) {
        alertEl.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;
        return false;
    }

    const isDup = ALL_RND_RECORDS.some(rec => {
        if (excludeId && rec.id === excludeId) return false;
        return rec.department === dVal && rec.academic_year === yVal && rec.quarter === qVal;
    });

    if (isDup) {
        alertText.textContent = `A ranking result for Department "${deptEl.value.trim().toUpperCase()}" in ${qVal} (AY ${yearEl.value.trim()}) already exists in database. Duplicate entries are not allowed.`;
        alertEl.style.display = 'block';
        if (submitBtn) submitBtn.disabled = true;
        return true;
    } else {
        alertEl.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;
        return false;
    }
}

function validateRndSubmit(mode) {
    if (checkRndDuplicateClient(mode)) {
        alert('Cannot save: A ranking for this department in the selected period already exists!');
        return false;
    }
    // Broadcast live update signal to other tabs
    try {
        if (window.BroadcastChannel) {
            new BroadcastChannel('tcek_rnd_sync').postMessage({ type: 'rnd_updated', timestamp: Date.now() });
        }
    } catch(e) {}
    return true;
}

function openAddRndModal() {
    const form = document.getElementById('formAddRndRanking');
    if (form) {
        form.reset();
        document.getElementById('add_rnd_year').value = '2025-26';
        document.getElementById('add_rnd_quarter').value = 'Q4';
        document.getElementById('add_rnd_rank').value = '1';
        onRndRankChange('add');
        document.getElementById('add_rnd_achievements').value = "High-Impact Research Papers\nPatents & Innovation Leads\nNPTEL & IIC Star Rating";
        document.getElementById('addRndDuplicateAlert').style.display = 'none';
        document.getElementById('btnSubmitAddRnd').disabled = false;
    }
    openModal('modalAddRndRanking');
}

function openEditRndModal(r) {
    if (!r) return;
    document.getElementById('edit_rnd_id').value = r.id || '';
    document.getElementById('edit_rnd_year').value = r.academic_year || '2025-26';
    document.getElementById('edit_rnd_quarter').value = r.quarter || 'Q4';
    document.getElementById('edit_rnd_label').value = r.quarter_label || '';
    document.getElementById('edit_rnd_pubdate').value = r.publication_date || '';
    document.getElementById('edit_rnd_dept').value = r.department || '';
    document.getElementById('edit_rnd_dept_name').value = r.department_name || '';
    document.getElementById('edit_rnd_rank').value = r.rank || '1';
    document.getElementById('edit_rnd_cat').value = r.award_category || '';
    document.getElementById('edit_rnd_badge').value = r.award_badge || '';
    document.getElementById('edit_rnd_score').value = (r.score !== null && r.score !== undefined) ? r.score : '';
    document.getElementById('edit_rnd_status').value = r.status || 'published';
    document.getElementById('edit_rnd_desc').value = r.description || '';

    const achs = Array.isArray(r.achievements) ? r.achievements.join('\n') : (r.achievements || '');
    document.getElementById('edit_rnd_achievements').value = achs;

    document.getElementById('editRndDuplicateAlert').style.display = 'none';
    document.getElementById('btnSubmitEditRnd').disabled = false;

    openModal('modalEditRndRanking');
}
</script>
