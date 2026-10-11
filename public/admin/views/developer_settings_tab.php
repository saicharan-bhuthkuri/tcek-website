<?php
/**
 * Developer Settings Tab View - TCEK Admin Console
 * EXCLUSIVELY ACCESSIBLE BY DEVELOPER (saivortex.dev@gmail.com)
 * Controls Global 404 Error Page Settings
 */

if (!defined('APP_INIT') && !function_exists('is_authorized_developer')) {
    require_once __DIR__ . '/../../backend/auth.php';
}

if (!function_exists('is_authorized_developer') || !is_authorized_developer()) {
    http_response_code(403);
    echo '<div style="padding:40px; text-align:center; color:#dc2626;"><h3>HTTP 403 Forbidden</h3><p>Access restricted to the authorized Developer account only.</p></div>';
    return;
}

$settings = function_exists('load_system_settings') ? load_system_settings() : [];
$is_404_on = !empty($settings['global_404_enabled']);
$updated_time = $settings['updated_at'] ?? date('Y-m-d H:i:s');
$updated_by = $settings['updated_by'] ?? 'saivortex.dev@gmail.com';
?>

<div class="tab-pane active" id="tabDeveloperSettings">
    <!-- Header Area -->
    <div class="tab-header-row" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">Global 404 Error Page Settings</h2>
                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:800; background:#ede9fe; color:#6366f1; border:1px solid #c7d2fe;">
                    <i class="fas fa-shield-alt"></i> DEVELOPER EXCLUSIVE
                </span>
            </div>
            <p style="font-size:13.5px; color:#64748b; margin:0;">
                Root-level master switch for site-wide missing URL routing and custom error presentation.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <a href="../404.php" target="_blank" class="btn-outline-pill" style="display:inline-flex; align-items:center; gap:6px; font-size:12.5px; font-weight:700; color:#6366f1; border-color:#c7d2fe; background:#f5f3ff;">
                <i class="fas fa-external-link-alt"></i> Live 404 Preview
            </a>
        </div>
    </div>

    <!-- Security & Protection Notice Banner -->
    <div style="background:linear-gradient(90deg, #ede9fe 0%, #f5f3ff 100%); border:1px solid #c7d2fe; border-radius:14px; padding:16px 20px; margin-bottom:28px; display:flex; align-items:flex-start; gap:14px;">
        <div style="width:36px; height:36px; border-radius:10px; background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
            <i class="fas fa-lock"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:13.5px; font-weight:700; color:#312e81; margin-bottom:3px;">Strict Role-Level Authorization Active</div>
            <div style="font-size:12.5px; color:#4338ca; line-height:1.5;">
                This configuration is strictly isolated to <strong>Charan (saivortex.dev@gmail.com)</strong>. All other administrative roles—including <strong>SUPERADMIN</strong> and <strong>ADMIN</strong>—are completely restricted from viewing or altering this setting. All backend endpoints and APIs enforce HTTP 403 Forbidden rejection for unauthorized callers.
            </div>
        </div>
    </div>

    <!-- Main Setting Card -->
    <div class="table-container" style="padding:28px; border-radius:16px; background:#ffffff; box-shadow:0 4px 20px rgba(15,23,42,0.04); border:1px solid #e2e8f0; margin-bottom:24px;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:20px; padding-bottom:24px; border-bottom:1px solid #f1f5f9;">
            <div style="max-width:620px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                    <div style="width:42px; height:42px; border-radius:12px; background:<?php echo $is_404_on ? 'rgba(0,184,148,0.12)' : 'rgba(100,116,139,0.12)'; ?>; color:<?php echo $is_404_on ? '#00b894' : '#64748b'; ?>; display:flex; align-items:center; justify-content:center; font-size:18px;">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div>
                        <h3 style="font-size:17px; font-weight:800; color:#0f172a; margin:0 0 2px 0;">Global Custom 404 Error Page</h3>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span id="badge404Status" style="display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:800; padding:2px 8px; border-radius:10px; background:<?php echo $is_404_on ? '#dcfce7' : '#f1f5f9'; ?>; color:<?php echo $is_404_on ? '#15803d' : '#64748b'; ?>; border:1px solid <?php echo $is_404_on ? '#bbf7d0' : '#e2e8f0'; ?>;">
                                <span style="width:6px; height:6px; border-radius:50%; background:<?php echo $is_404_on ? '#22c55e' : '#94a3b8'; ?>;"></span>
                                <?php echo $is_404_on ? 'ACTIVE (ON)' : 'DISABLED (OFF)'; ?>
                            </span>
                            <span style="font-size:11.5px; color:#94a3b8;">&bull; Site-wide Route Guard</span>
                        </div>
                    </div>
                </div>
                <p style="font-size:13.5px; color:#475569; line-height:1.6; margin:12px 0 0 0;">
                    When enabled, any missing URL, broken link, or non-existent resource automatically displays Trinity College's custom branded 404 page featuring institutional navigation, quick links, and responsive styling. When disabled, standard minimal server 404 headers are emitted.
                </p>
            </div>

            <!-- Interactive Toggle Control Switch -->
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
                <form id="formToggle404" method="POST" action="../backend/crud.php" style="margin:0;">
                    <input type="hidden" name="action" value="toggle_global_404">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="redirect_tab" value="developer_settings">
                    
                    <label class="switch-box" style="width:58px; height:32px; margin:0; cursor:pointer;" title="Click to toggle Global 404 Error Page">
                        <input type="checkbox" id="switchGlobal404" <?php echo $is_404_on ? 'checked' : ''; ?> onchange="handle404ToggleChange(this)">
                        <span class="switch-slider" style="border-radius:34px;"></span>
                    </label>
                </form>

                <div id="toggleFeedbackMsg" style="font-size:11.5px; font-weight:700; color:<?php echo $is_404_on ? '#16a34a' : '#64748b'; ?>;">
                    <?php echo $is_404_on ? 'Custom 404 is Serving Visitors' : 'Standard 404 Mode Active'; ?>
                </div>
            </div>
        </div>

        <!-- Meta Details and System State -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; margin-top:24px;">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
                <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">Persistent Storage</span>
                <span style="font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-database" style="color:#00b894;"></i> MySQL + JSON Fallback
                </span>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
                <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">Authorized Controller</span>
                <span style="font-size:13px; font-weight:700; color:#6366f1; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-user-shield"></i> saivortex.dev@gmail.com
                </span>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
                <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">Last Modification</span>
                <span id="text404UpdatedAt" style="font-size:13px; font-weight:600; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i class="far fa-clock" style="color:#94a3b8;"></i> <?php echo htmlspecialchars($updated_time); ?>
                </span>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
                <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:4px;">Server Status Code</span>
                <span style="font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-check-circle" style="color:#00b894;"></i> HTTP 404 Header Intact
                </span>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * AJAX Toggle Handler with Form Fallback
 * Directly communicates with backend/api_settings.php
 * Rejects non-developer calls with HTTP 403 Forbidden
 */
function handle404ToggleChange(checkbox) {
    const isChecked = checkbox.checked ? 1 : 0;
    const badge = document.getElementById('badge404Status');
    const feedback = document.getElementById('toggleFeedbackMsg');
    const updatedAt = document.getElementById('text404UpdatedAt');
    const form = document.getElementById('formToggle404');
    const csrfToken = "<?php echo htmlspecialchars($csrf_token); ?>";

    // Immediate optimistic UI feedback
    if (feedback) {
        feedback.innerText = isChecked ? 'Enabling Custom 404...' : 'Disabling Custom 404...';
        feedback.style.color = '#6366f1';
    }

    const formData = new FormData();
    formData.append('action', 'save_global_404');
    formData.append('is_enabled', isChecked);
    formData.append('csrf_token', csrfToken);

    fetch('../backend/api_settings.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (response.status === 403) {
            alert('HTTP 403 Forbidden: Only the authorized Developer account (saivortex.dev@gmail.com) can modify this setting.');
            checkbox.checked = !checkbox.checked;
            throw new Error('403 Forbidden');
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            if (badge) {
                if (isChecked) {
                    badge.innerHTML = '<span style="width:6px; height:6px; border-radius:50%; background:#22c55e;"></span> ACTIVE (ON)';
                    badge.style.background = '#dcfce7';
                    badge.style.color = '#15803d';
                    badge.style.borderColor = '#bbf7d0';
                } else {
                    badge.innerHTML = '<span style="width:6px; height:6px; border-radius:50%; background:#94a3b8;"></span> DISABLED (OFF)';
                    badge.style.background = '#f1f5f9';
                    badge.style.color = '#64748b';
                    badge.style.borderColor = '#e2e8f0';
                }
            }
            if (feedback) {
                feedback.innerText = isChecked ? 'Custom 404 is Serving Visitors' : 'Standard 404 Mode Active';
                feedback.style.color = isChecked ? '#16a34a' : '#64748b';
            }
            if (updatedAt) {
                const now = new Date();
                updatedAt.innerHTML = '<i class="far fa-clock" style="color:#94a3b8;"></i> Just now';
            }
        } else {
            // Fallback to standard form submit if JSON didn't return success
            form.submit();
        }
    })
    .catch(err => {
        if (err.message !== '403 Forbidden') {
            form.submit();
        }
    });
}
</script>
