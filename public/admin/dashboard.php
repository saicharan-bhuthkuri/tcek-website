<?php
/**
 * TCEK Master Admin Dashboard
 * Clean Modern Light Theme strictly following Image 2 Reference & Modal Pop-ups
 * Navigation: 8 Curated Items Only (Overview, Users, Workshops & Tasks, Events, News, Circulars & Notifications, Scrollbar, Activity Logs)
 */

require_once __DIR__ . '/../backend/auth.php';
require_admin_login();

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/upload.php';
require_once __DIR__ . '/../backend/crud.php';

$admin = get_current_admin();
$csrf_token = get_csrf_token();
$db_connected = isDbConnected();
$is_admin = is_admin_role();

// Handle Flash Messages
$flash_msg  = $_SESSION['flash_msg'] ?? null;
$flash_type = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

// Allowed Tabs
$allowed_tabs = ['overview', 'users', 'workshops', 'events', 'event_files', 'news', 'circulars', 'notifications', 'scrollbar', 'activity_logs'];

// Active Tab
$current_tab = $_GET['tab'] ?? 'overview';
if (!in_array($current_tab, $allowed_tabs)) {
    $current_tab = 'overview';
}
if ($current_tab === 'circulars') {
    $current_tab = 'notifications';
}

// Protect Admin-Only Tabs
if (in_array($current_tab, ['activity_logs', 'users']) && !$is_admin) {
    $current_tab = 'overview';
    $flash_msg = 'Access restricted to administrator role.';
    $flash_type = 'danger';
}

// Filters for Activity Logs
$log_module_filter = $_GET['log_module'] ?? 'all';
$log_action_filter = $_GET['log_action'] ?? 'all';

// Fetch Data for the 8 Curated Modules
$notifications  = get_notifications(50);
$events         = get_events(50);
$all_event_media = function_exists('get_all_event_media') ? get_all_event_media(100) : [];
$workshops_list = get_workshops(50);
$news_list      = get_news(50);
$scrollbar_list = get_scrollbar_items(30);
$activity_logs  = $is_admin ? get_activity_logs($log_module_filter, $log_action_filter, 100) : [];
$users_list     = $is_admin ? get_users(50) : [];
$all_uploads    = get_all_uploads(null, 100);

if (!function_exists('format_file_size')) {
    function format_file_size($bytes) {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}

if (!function_exists('format_log_time')) {
    function format_log_time($timestamp) {
        if (!$timestamp) return date('d-m-Y h:i A');
        return date('d-m-Y h:i A', strtotime($timestamp));
    }
}

// Helper: Initials for User Avatar Circle
$admin_name = $admin['full_name'] ?? 'Charan';
$initials = strtoupper(substr($admin_name, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console - Trinity College of Engineering &amp; Technology</title>
    <!-- Fonts & Icons matching index.php -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main College Website Stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Admin Extension Stylesheet -->
    <link rel="stylesheet" href="css/admin.css?v=<?php echo filemtime(__DIR__ . '/css/admin.css'); ?>">
</head>
<body class="dashboard-body">

    <!-- Top Navigation Bar (Clean Light Theme) -->
    <header class="admin-topbar">
        <div class="topbar-left">
            <button type="button" class="btn-sidebar-toggle" id="btnToggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-brand">
                <a href="../index.php" style="display:flex; align-items:center;">
                    <img src="../assets/Top Header/header_banner.png" alt="Trinity College Logo" style="height:40px; object-fit:contain;" onerror="this.style.display='none'; document.getElementById('alt-dash-logo').style.display='flex';">
                </a>
                <div id="alt-dash-logo" style="display:none; align-items:center; gap:8px;">
                    <i class="fas fa-university brand-icon" style="color:#00b894;"></i>
                    <span class="brand-title" style="font-size:15px; font-weight:700; color:#0f172a;">TRINITY COLLEGE</span>
                </div>
                <span class="dashboard-badge-tag"><span class="pulse-indicator"></span> Control Panel</span>
            </div>
        </div>

        <div class="topbar-right">
            <div class="live-connection-badge">
                <span class="pulse-indicator"></span>
                <span>Live Connection</span>
            </div>
            <a href="../index.php" target="_blank" class="topbar-action-btn" title="View Public Website">
                <i class="fas fa-external-link-alt"></i>
                <span>Live Website</span>
            </a>
            <div class="admin-user-pill">
                <i class="fas fa-user-circle" style="color:#6366f1;"></i>
                <span><?php echo htmlspecialchars($admin['full_name']); ?></span>
            </div>
            <a href="logout.php" class="btn-logout" title="Sign Out">
                <i class="fas fa-power-off"></i>
                <span>Logout</span>
            </a>
        </div>
    </header>

    <div class="dashboard-layout">
        <!-- Sidebar Navigation (Strict Image 2 Light Theme - 8 Curated Items Only) -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div>
                <div class="sidebar-header">
                    <span class="sidebar-title">Admin Console</span>
                    <button type="button" class="sidebar-collapse-btn" id="btnCollapseSidebar" title="Collapse Sidebar">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>

                <div class="sidebar-menu">
                    <!-- 1. Overview -->
                    <a href="?tab=overview" class="menu-item <?php echo $current_tab === 'overview' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Overview</span>
                    </a>

                    <!-- 2. Users -->
                    <?php if ($is_admin): ?>
                        <a href="?tab=users" class="menu-item <?php echo $current_tab === 'users' ? 'active' : ''; ?>">
                            <i class="fas fa-users-cog"></i>
                            <span>Users</span>
                        </a>
                    <?php endif; ?>

                    <!-- 3. Workshops & Tasks -->
                    <a href="?tab=workshops" class="menu-item <?php echo $current_tab === 'workshops' ? 'active' : ''; ?>">
                        <i class="fas fa-laptop-code"></i>
                        <span>Workshops &amp; Tasks</span>
                    </a>

                    <!-- 4. Events -->
                    <a href="?tab=events" class="menu-item <?php echo $current_tab === 'events' ? 'active' : ''; ?>">
                        <i class="far fa-calendar-check"></i>
                        <span>Events</span>
                    </a>

                    <!-- 5. Event Files -->
                    <a href="?tab=event_files" class="menu-item <?php echo $current_tab === 'event_files' ? 'active' : ''; ?>">
                        <i class="fas fa-photo-video"></i>
                        <span>Event Files</span>
                    </a>

                    <!-- 6. News -->
                    <a href="?tab=news" class="menu-item <?php echo $current_tab === 'news' ? 'active' : ''; ?>">
                        <i class="far fa-newspaper"></i>
                        <span>News</span>
                    </a>

                    <!-- 7. Circulars & Notifications -->
                    <a href="?tab=notifications" class="menu-item <?php echo $current_tab === 'notifications' ? 'active' : ''; ?>">
                        <i class="far fa-bell"></i>
                        <span>Circulars &amp; Notifications</span>
                    </a>

                    <!-- 8. Activity Logs -->
                    <?php if ($is_admin): ?>
                        <a href="?tab=activity_logs" class="menu-item <?php echo $current_tab === 'activity_logs' ? 'active' : ''; ?>">
                            <i class="fas fa-history"></i>
                            <span>Activity Logs</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom Profile Card - Strictly Matching Image 2 Bottom Left -->
            <div class="sidebar-user-card">
                <div class="user-avatar-circle">
                    <?php echo htmlspecialchars($initials); ?>
                </div>
                <div class="user-meta-wrap">
                    <span class="user-display-name"><?php echo htmlspecialchars($admin_name); ?></span>
                    <span class="user-role-badge">&bull; <?php echo strtoupper($admin['role'] === 'admin' ? 'DEVELOPER' : $admin['role']); ?></span>
                </div>
                <a href="logout.php" class="user-logout-btn" title="Sign Out">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="dashboard-main">
            <!-- Flash Message Alert -->
            <?php if ($flash_msg): ?>
                <div class="alert alert-<?php echo $flash_type; ?> dismissible">
                    <i class="fas <?php echo $flash_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <span><?php echo htmlspecialchars($flash_msg); ?></span>
                    <button type="button" class="close-alert" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 1: OVERVIEW -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'overview'): ?>
                <div class="tab-pane active">
                    <!-- Overview Summary 6 Cards Row Strictly Matching Reference -->
                    <div class="metrics-row-image2">
                        <!-- Card 1: TOTAL FILES -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">TOTAL FILES</span>
                                <div class="metric-icon-circle icon-blue-pastel"><i class="fas fa-layer-group"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($all_uploads); ?></div>
                        </div>

                        <!-- Card 2: NO. OF USERS -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">NO. OF USERS</span>
                                <div class="metric-icon-circle icon-amber-pastel"><i class="fas fa-users"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($users_list); ?></div>
                        </div>

                        <!-- Card 3: WORKSHOPS & TASKS -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">WORKSHOPS &amp; TASKS</span>
                                <div class="metric-icon-circle icon-emerald-pastel"><i class="fas fa-laptop-code"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($workshops_list); ?></div>
                        </div>

                        <!-- Card 4: NO. OF EVENTS -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">NO. OF EVENTS</span>
                                <div class="metric-icon-circle icon-red-pastel"><i class="far fa-calendar-alt"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($events); ?></div>
                        </div>

                        <!-- Card 5: NEWS -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">NEWS</span>
                                <div class="metric-icon-circle icon-teal-pastel"><i class="far fa-newspaper"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($news_list); ?></div>
                        </div>

                        <!-- Card 6: CIRCULARS & NOTIFICATIONS -->
                        <div class="metric-card-image2">
                            <div class="metric-card-top">
                                <span class="metric-label-image2">CIRCULARS &amp; NOTIFICATIONS</span>
                                <div class="metric-icon-circle icon-orange-pastel"><i class="far fa-bell"></i></div>
                            </div>
                            <div class="metric-value-image2"><?php echo count($notifications); ?></div>
                        </div>
                    </div>

                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Control Center Overview</h2>
                                <p>Role-Based System Access Control Active</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalNotification')">
                                <i class="fas fa-plus"></i> Create Notification
                            </button>
                            <button type="button" class="btn-outline-pill" onclick="openModal('modalWorkshop')">
                                <i class="fas fa-laptop-code"></i> New Workshop
                            </button>
                        </div>
                    </div>

                    <!-- Action & Filter Bar matching Image 2 -->
                    <div class="action-filter-bar-image2">
                        <div class="filter-left-group">
                            <div class="search-box-pill">
                                <i class="fas fa-search"></i>
                                <input type="text" class="search-pill-input" placeholder="Search notices, events, or workshops..." onkeyup="filterGenericTable('overviewTable', this.value)">
                            </div>
                            <select class="filter-pill-select" onchange="window.location.href='?tab='+this.value">
                                <option value="overview">All Modules</option>
                                <option value="users">Users</option>
                                <option value="workshops">Workshops &amp; Tasks</option>
                                <option value="events">Events</option>
                                <option value="news">News</option>
                                <option value="notifications">Circulars</option>
                                <option value="scrollbar">Scrollbar</option>
                            </select>
                        </div>
                        <div class="filter-right-actions">
                            <a href="?tab=activity_logs" class="btn-outline-pill"><i class="fas fa-history"></i> Task History</a>
                            <button type="button" class="btn-outline-pill" onclick="window.print()"><i class="fas fa-download"></i> Export CSV</button>
                        </div>
                    </div>

                    <div class="table-card-image2">
                        <table class="table-image2" id="overviewTable">
                            <thead>
                                <tr>
                                    <th>ANNOUNCEMENT INFO</th>
                                    <th>MODULE / CATEGORY</th>
                                    <th>ATTACHMENT &amp; FILE</th>
                                    <th>STATEMENT / SUMMARY</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($notifications, 0, 8) as $n): ?>
                                    <tr>
                                        <td>
                                            <div class="item-main-title"><?php echo htmlspecialchars($n['title']); ?></div>
                                            <div class="item-sub-text">Date: <?php echo htmlspecialchars($n['publish_date']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600; color:#334155;"><?php echo htmlspecialchars($n['category']); ?></div>
                                            <div class="item-sub-text">Academic AY: 2026-27</div>
                                        </td>
                                        <td>
                                            <?php if (!empty($n['attachment_path'])): ?>
                                                <a href="../<?php echo htmlspecialchars($n['attachment_path']); ?>" target="_blank" class="topbar-action-btn" style="font-size:11.5px; padding:3px 10px;">
                                                    <i class="fas fa-paperclip"></i> <?php echo strtoupper($n['attachment_type'] ?? 'DOC'); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="item-sub-text">Text Announcement</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="max-width:280px; font-size:12.5px; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                <?php echo htmlspecialchars($n['description'] ?: 'Official notice published by Trinity College of Engineering & Technology.'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($n['is_marquee'])): ?>
                                                <span class="status-pill-dot status-active">&bull; LIVE</span>
                                            <?php else: ?>
                                                <span class="status-pill-dot status-pending">&bull; PENDING</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-icons-wrap">
                                                <?php if (!empty($n['attachment_path'])): ?>
                                                    <a href="../<?php echo htmlspecialchars($n['attachment_path']); ?>" target="_blank" class="btn-icon-circle btn-icon-check" title="View Attachment"><i class="fas fa-check"></i></a>
                                                <?php else: ?>
                                                    <span class="btn-icon-circle btn-icon-check"><i class="fas fa-check"></i></span>
                                                <?php endif; ?>
                                                <button type="button" class="btn-icon-circle btn-icon-edit" onclick="openModal('modalNotification')"><i class="fas fa-pencil-alt"></i></button>
                                                <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Delete this notification?');" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_notification">
                                                    <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <button type="submit" class="btn-icon-circle btn-icon-trash"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 2: USERS -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'users' && $is_admin): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>User &amp; Role Management</h2>
                                <p>Role-Based System Access Control Active</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalUser')">
                                <i class="fas fa-user-plus"></i> Create User
                            </button>
                        </div>
                    </div>

                    <div class="table-card-image2">
                        <table class="table-image2">
                            <thead>
                                <tr>
                                    <th>USERNAME</th>
                                    <th>FULL NAME</th>
                                    <th>ROLE</th>
                                    <th>EMAIL ADDRESS</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users_list)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:36px; color:#64748b;">
                                            <i class="fas fa-users-slash" style="font-size:24px; margin-bottom:8px; display:block; color:#94a3b8;"></i>
                                            No users registered yet. Click <strong>+ Create User</strong> above to add an administrator or staff member.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users_list as $usr): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($usr['username']); ?></code></td>
                                            <td><strong><?php echo htmlspecialchars($usr['full_name']); ?></strong></td>
                                            <td><span class="status-pill-dot status-active">&bull; <?php echo strtoupper($usr['role']); ?></span></td>
                                            <td><?php echo htmlspecialchars($usr['email'] ?? 'officetcek@gmail.com'); ?></td>
                                            <td><span class="status-pill-dot status-active">&bull; ACTIVE</span></td>
                                            <td>
                                                <div class="action-icons-wrap">
                                                    <button type="button" class="btn-icon-circle btn-icon-check"><i class="fas fa-check"></i></button>
                                                    <button type="button" class="btn-icon-circle btn-icon-edit" onclick="openModal('modalUser')"><i class="fas fa-pencil-alt"></i></button>
                                                    <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Delete this user?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_user">
                                                        <input type="hidden" name="id" value="<?php echo $usr['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <button type="submit" class="btn-icon-circle btn-icon-trash"><i class="fas fa-trash-alt"></i></button>
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
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 3: WORKSHOPS & TASKS -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'workshops'): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Workshops &amp; Student Technical Tasks</h2>
                                <p>Role-Based System Access Control Active</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalWorkshop')">
                                <i class="fas fa-laptop-code"></i> Add Workshop / Task
                            </button>
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="action-filter-bar-image2">
                        <div class="filter-left-group">
                            <div class="search-box-pill">
                                <i class="fas fa-search"></i>
                                <input type="text" class="search-pill-input" placeholder="Search workshop title, instructor, or lab..." onkeyup="filterGenericTable('workshopsTable', this.value)">
                            </div>
                            <select class="filter-pill-select" onchange="filterBranchTable(this.value)">
                                <option value="all">All Domains</option>
                                <option value="AI / ML">AI / ML</option>
                                <option value="Web Dev">Web Dev</option>
                                <option value="Embedded">Embedded / IoT</option>
                            </select>
                        </div>
                        <div class="filter-right-actions">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalWorkshop')">
                                <i class="fas fa-plus"></i> New Task
                            </button>
                            <button type="button" class="btn-outline-pill" onclick="window.print()"><i class="fas fa-download"></i> Export CSV</button>
                        </div>
                    </div>

                    <div class="table-card-image2">
                        <table class="table-image2" id="workshopsTable">
                            <thead>
                                <tr>
                                    <th>WORKSHOP / TASK TITLE</th>
                                    <th>INSTRUCTOR / LEAD</th>
                                    <th>VENUE / LAB</th>
                                    <th>SCHEDULED DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workshops_list as $w): ?>
                                    <tr class="data-row" data-branch="<?php echo htmlspecialchars($w['category']); ?>">
                                        <td>
                                            <div class="item-main-title"><?php echo htmlspecialchars($w['title']); ?></div>
                                            <div class="item-sub-text">Category: <?php echo htmlspecialchars($w['category']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($w['instructor'] ?? 'Faculty Mentor'); ?></div>
                                            <div class="item-sub-text">CSE / ECE / AIML</div>
                                        </td>
                                        <td>
                                            <div style="color:#334155; font-weight:500;"><?php echo htmlspecialchars($w['venue']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($w['event_date']); ?></div>
                                        </td>
                                        <td>
                                            <?php if (($w['status'] ?? '') === 'ACTIVE'): ?>
                                                <span class="status-pill-dot status-active">&bull; ACTIVE</span>
                                            <?php else: ?>
                                                <span class="status-pill-dot status-pending">&bull; UPCOMING</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-icons-wrap">
                                                <button type="button" class="btn-icon-circle btn-icon-check"><i class="fas fa-check"></i></button>
                                                <button type="button" class="btn-icon-circle btn-icon-edit" onclick="openModal('modalWorkshop')"><i class="fas fa-pencil-alt"></i></button>
                                                <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Delete this workshop?');" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_workshop">
                                                    <input type="hidden" name="id" value="<?php echo $w['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <button type="submit" class="btn-icon-circle btn-icon-trash"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 4: EVENTS -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- TAB 4: EVENTS (CREATING & MANAGING EVENTS ONLY)                 -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'events'): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Events</h2>
                                <p>Create and manage scheduled college events, fests, and celebrations</p>
                            </div>
                        </div>
                        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center;">
                            <a href="?tab=event_files" class="btn-outline-pill" style="text-decoration:none; font-size:12.5px; font-weight:600;">
                                <i class="fas fa-photo-video"></i> Go to Event Files
                            </a>
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalEvent')">
                                <i class="far fa-calendar-plus"></i> Create Event
                            </button>
                        </div>
                    </div>

                    <div class="table-card-image2">
                        <table class="table-image2">
                            <thead>
                                <tr>
                                    <th style="min-width: 240px;">EVENT NAME</th>
                                    <th style="min-width: 170px;">DATE</th>
                                    <th>DESCRIPTION</th>
                                    <th style="min-width: 200px; text-align: right;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($events)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding:36px; color:#94a3b8;">
                                            <i class="far fa-calendar-times" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                                            No events scheduled yet. Click <strong>“Create Event”</strong> above to add one.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($events as $ev): ?>
                                        <tr>
                                            <td>
                                                <div class="item-main-title"><?php echo htmlspecialchars($ev['title']); ?></div>
                                                <div style="font-size:11.5px; color:#64748b; margin-top:3px; display:flex; align-items:center; gap:8px;">
                                                    <span><i class="fas fa-map-marker-alt" style="color:#00b894;"></i> <?php echo htmlspecialchars($ev['venue'] ?? 'Trinity Campus Auditorium'); ?></span>
                                                    <?php $mCount = count($ev['media_items'] ?? []); ?>
                                                    <?php if ($mCount > 0): ?>
                                                        <a href="?tab=event_files&event_id=<?php echo $ev['id']; ?>" style="color:#059669; font-weight:600; text-decoration:none; background:#ecfdf5; padding:1px 7px; border-radius:999px;">
                                                            <i class="fas fa-paperclip"></i> <?php echo $mCount; ?> file<?php echo $mCount > 1 ? 's' : ''; ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($ev['event_date']); ?></div>
                                                <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">
                                                    <i class="far fa-clock" style="color:#00b894;"></i> <?php echo htmlspecialchars($ev['event_time'] ?? '10:00 AM'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="max-width:320px; font-size:12.5px; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($ev['description'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($ev['description'] ?: 'Annual fest and celebration at Trinity College.'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-icons-wrap" style="justify-content: flex-end; gap:6px;">
                                                    <!-- View Action -->
                                                    <button type="button" class="btn-action-view" onclick='viewEventDetails(<?php echo htmlspecialchars(json_encode($ev), ENT_QUOTES); ?>)' title="View Event &amp; Attached Files">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <!-- Edit Action -->
                                                    <button type="button" class="btn-action-edit" onclick='openEditEventModal(<?php echo htmlspecialchars(json_encode($ev), ENT_QUOTES); ?>)' title="Edit Event Details">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <!-- Delete Action -->
                                                    <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Are you sure you want to delete &quot;<?php echo addslashes(htmlspecialchars($ev['title'])); ?>&quot; and all its attached media?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_event">
                                                        <input type="hidden" name="id" value="<?php echo $ev['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <button type="submit" class="btn-action-delete" title="Delete Event">
                                                            <i class="fas fa-trash-alt"></i> Delete
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
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 5: EVENT FILES (UPLOADING & MANAGING MEDIA ONLY)            -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'event_files'): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Event Files</h2>
                                <p>Upload images or videos directly linked to an event &bull; Automatically displayed on the public website</p>
                            </div>
                        </div>
                        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center;">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalEventMedia')">
                                <i class="fas fa-cloud-upload-alt"></i> Upload Event File
                            </button>
                            <span class="linked-event-pill" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:12.5px; padding:7px 14px;">
                                <i class="fas fa-photo-video"></i> <?php echo count($all_event_media); ?> Total Files
                            </span>
                        </div>
                    </div>

                    <!-- Table of Uploaded Files -->
                    <div class="table-card-image2" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <h3 style="margin:0; font-size:16px; font-weight:700; color:#1e293b;">
                                    <i class="fas fa-photo-video" style="color:#00b894;"></i> Uploaded Event Files
                                </h3>
                                <span style="background:#f1f5f9; color:#475569; font-size:12px; font-weight:700; padding:2px 10px; border-radius:999px;">
                                    <?php echo count($all_event_media); ?> Files
                                </span>
                            </div>

                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                <div class="search-box-pill" style="margin:0;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="eventFileSearchInput" class="search-pill-input" placeholder="Search event file..." onkeyup="filterEventFilesTable(this.value)">
                                </div>
                                <button type="button" class="btn-emerald-pill" onclick="openModal('modalEventMedia')" style="padding:7px 18px; font-size:12.5px;">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Event File
                                </button>
                            </div>
                        </div>

                        <table class="table-image2" id="eventFilesTable">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">PREVIEW</th>
                                    <th style="min-width: 200px;">FILE NAME</th>
                                    <th style="min-width: 180px;">LINKED EVENT</th>
                                    <th>DESCRIPTION</th>
                                    <th style="min-width: 150px; text-align: right;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($all_event_media)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:36px; color:#94a3b8;">
                                            <i class="fas fa-photo-video" style="font-size:32px; margin-bottom:8px; display:block; color:#cbd5e1;"></i>
                                            No event files uploaded yet. Click <strong>“Upload Event File”</strong> above to add files.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($all_event_media as $m): ?>
                                        <?php $isVid = ($m['media_type'] === 'video'); ?>
                                        <tr>
                                            <td>
                                                <div class="event-file-thumb-wrap">
                                                    <?php if ($isVid): ?>
                                                        <div style="color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; width:100%; height:100%; background:#1e293b;">
                                                            <i class="fas fa-video" style="color:#60a5fa; font-size:14px;"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <img src="../<?php echo htmlspecialchars($m['file_path']); ?>" alt="" onerror="this.src='../assets/events/tcek-fresher.jpg'">
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="item-main-title"><?php echo htmlspecialchars($m['media_title']); ?></div>
                                                <span class="media-type-tag <?php echo $isVid ? 'tag-video' : 'tag-image'; ?>">
                                                    <i class="fas <?php echo $isVid ? 'fa-video' : 'fa-image'; ?>"></i> <?php echo strtoupper($m['media_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="linked-event-pill" title="<?php echo htmlspecialchars($m['event_title'] ?? ''); ?>">
                                                    <i class="far fa-calendar-check" style="color:#00b894;"></i> 
                                                    <?php echo htmlspecialchars($m['event_title'] ?? ('Event #' . ($m['event_id'] ?? ''))); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="max-width:260px; font-size:12px; color:#64748b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($m['media_description'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($m['media_description'] ?: 'No description provided'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-icons-wrap" style="justify-content: flex-end; gap:6px;">
                                                    <!-- Edit Action -->
                                                    <button type="button" class="btn-action-edit" onclick='openEditMediaModal(<?php echo htmlspecialchars(json_encode($m), ENT_QUOTES); ?>)' title="Edit File Name &amp; Description">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <!-- Delete Action -->
                                                    <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Delete this media file?');" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_event_media">
                                                        <input type="hidden" name="media_id" value="<?php echo $m['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <button type="submit" class="btn-action-delete" title="Delete File">
                                                            <i class="fas fa-trash-alt"></i> Delete
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
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 5: NEWS -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- TAB 5: NEWS (NEWS MANAGEMENT) -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'news'): ?>
                <div class="tab-pane active">
                    <!-- Top Page Header -->
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>News Management</h2>
                                <p>Role-Based System Access Control Active &bull; Newspaper clippings automatically appear in the public website News section.</p>
                            </div>
                        </div>
                        <div class="pane-actions-right" style="display:flex; gap:10px; align-items:center;">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalUploadNews')">
                                <i class="fas fa-newspaper"></i> Upload News
                            </button>
                            <span class="events-count-badge">
                                <i class="fas fa-newspaper"></i> <?php echo count($news_list); ?> Published Articles
                            </span>
                        </div>
                    </div>

                    <!-- LIST OF NEWS -->
                    <div class="events-section-card">
                        <div class="events-section-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                            <div class="events-section-title-wrap">
                                <span class="events-section-num num-blue"><i class="fas fa-list"></i></span>
                                <div>
                                    <h3 class="events-section-heading">List of News</h3>
                                    <p class="events-section-sub">Display uploaded newspaper news &bull; Actions: View / Edit / Delete</p>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                <div class="search-box-pill" style="margin:0;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="newsSearchInput" class="search-pill-input" placeholder="Search news title..." onkeyup="filterNewsTable(this.value)">
                                </div>
                                <span class="events-count-badge">
                                    <i class="fas fa-newspaper"></i> <?php echo count($news_list); ?> News Items
                                </span>
                                <button type="button" class="btn-emerald-pill" onclick="openModal('modalUploadNews')" style="padding:7px 18px; font-size:12.5px;">
                                    <i class="fas fa-newspaper"></i> Upload News
                                </button>
                            </div>
                        </div>

                        <?php if (empty($news_list)): ?>
                            <div style="padding: 48px 24px; text-align: center;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <h4 style="margin: 0 0 6px; color: #1e293b; font-size: 16px;">No Newspaper News Uploaded Yet</h4>
                                <p style="margin: 0; color: #64748b; font-size: 13px;">Click <strong>"Upload News"</strong> above to publish your first newspaper clipping image.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-card-image2" style="border:none; border-radius:0; box-shadow:none;">
                                <table class="table-image2" id="newsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 110px;">NEWSPAPER IMAGE</th>
                                            <th>NEWS TITLE</th>
                                            <th style="width: 130px;">DATE</th>
                                            <th>DESCRIPTION</th>
                                            <th style="width: 230px; text-align: center;">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($news_list as $nws): 
                                            $raw_img = !empty($nws['image_path']) ? $nws['image_path'] : 'assets/Gallery/paper1.jpg';
                                            $thumb_src = '../' . ltrim($raw_img, '/');
                                            $formatted_date = !empty($nws['publish_date']) ? date('M d, Y', strtotime($nws['publish_date'])) : '—';
                                            $clean_desc = $nws['description'] ?? ($nws['summary'] ?? '');
                                        ?>
                                            <tr>
                                                <!-- Newspaper Image -->
                                                <td>
                                                    <div class="news-table-thumb-wrap" onclick="openViewNewsModal(<?php echo htmlspecialchars(json_encode($nws), ENT_QUOTES, 'UTF-8'); ?>)" title="Click to view high-res image">
                                                        <img src="<?php echo htmlspecialchars($thumb_src); ?>" alt="<?php echo htmlspecialchars($nws['title']); ?>" onerror="this.src='../assets/Gallery/paper1.jpg'">
                                                        <span class="news-thumb-zoom-icon"><i class="fas fa-search-plus"></i></span>
                                                    </div>
                                                </td>

                                                <!-- News Title -->
                                                <td>
                                                    <div class="item-main-title" style="font-size:14px; font-weight:700; color:#0f172a; line-height:1.4;">
                                                        <?php echo htmlspecialchars($nws['title']); ?>
                                                    </div>
                                                    <?php if (!empty($nws['source'])): ?>
                                                        <span class="linked-event-pill" style="margin-top:5px; font-size:11px;">
                                                            <i class="fas fa-bookmark" style="color:#00b894;"></i> <?php echo htmlspecialchars($nws['source']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Date -->
                                                <td>
                                                    <div style="font-weight:600; color:#0f172a; font-size:13px; display:inline-flex; align-items:center; gap:6px;">
                                                        <i class="far fa-calendar-alt" style="color:#00b894;"></i> <?php echo htmlspecialchars($formatted_date); ?>
                                                    </div>
                                                </td>

                                                <!-- Description -->
                                                <td>
                                                    <div style="max-width:320px; font-size:12.5px; color:#475569; line-height:1.5;">
                                                        <?php if (!empty($clean_desc)): ?>
                                                            <?php 
                                                            $desc_short = (strlen($clean_desc) > 140) ? substr($clean_desc, 0, 140) . '...' : $clean_desc;
                                                            echo htmlspecialchars($desc_short); 
                                                            ?>
                                                        <?php else: ?>
                                                            <span style="color:#94a3b8; font-style:italic;">No description provided</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <!-- Actions: View / Edit / Delete -->
                                                <td style="text-align: center;">
                                                    <div style="display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">
                                                        <!-- View Button -->
                                                        <button type="button" class="btn-action-view" onclick="openViewNewsModal(<?php echo htmlspecialchars(json_encode($nws), ENT_QUOTES, 'UTF-8'); ?>)" title="View Full Newspaper Clipping">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>

                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn-action-edit" onclick="openEditNewsModal(<?php echo htmlspecialchars(json_encode($nws), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit News">
                                                            <i class="fas fa-pencil-alt"></i> Edit
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this newspaper news clipping?');" style="display:inline; margin:0;">
                                                            <input type="hidden" name="action" value="delete_news">
                                                            <input type="hidden" name="id" value="<?php echo $nws['id']; ?>">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                            <button type="submit" class="btn-action-delete" title="Delete News">
                                                                <i class="fas fa-trash-alt"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 6: CIRCULARS & NOTIFICATIONS -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'notifications'): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Official College Circulars</h2>
                                <p>Upload and manage official circulars, examination schedules, and administrative notices</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalUploadCircular')">
                                <i class="fas fa-cloud-upload-alt"></i> Upload Circular
                            </button>
                            <a href="../circulars.php" target="_blank" class="topbar-action-btn">
                                <i class="fas fa-external-link-alt"></i> View on Public Website
                            </a>
                        </div>
                    </div>

                    <!-- LIST OF CIRCULARS SECTION -->
                    <div class="events-section-card">
                        <div class="events-section-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                            <div class="events-section-title-wrap">
                                <span class="events-section-num num-blue"><i class="fas fa-file-alt"></i></span>
                                <div>
                                    <h3 class="events-section-heading">List of Circulars</h3>
                                    <p class="events-section-sub">Official notifications, examination schedules &amp; academic circulars &bull; Actions: View / Edit / Delete</p>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                <div class="search-box-pill" style="margin:0;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="circularSearchInput" class="search-pill-input" placeholder="Search circular title..." onkeyup="filterCircularsTable(this.value)">
                                </div>
                                <span class="events-count-badge">
                                    <i class="fas fa-file-invoice"></i> <?php echo count($notifications); ?> Circulars
                                </span>
                                <button type="button" class="btn-emerald-pill" onclick="openModal('modalUploadCircular')" style="padding:7px 18px; font-size:12.5px;">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Circular
                                </button>
                            </div>
                        </div>

                        <?php if (empty($notifications)): ?>
                            <div style="padding: 56px 24px; text-align: center;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h4 style="margin: 0 0 6px; color: #1e293b; font-size: 16px;">No Circulars Uploaded Yet</h4>
                                <p style="margin: 0 auto; max-width: 440px; color: #64748b; font-size: 13px;">Click <strong>"Upload Circular"</strong> above to publish your first official college notice or exam schedule.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-card-image2" style="border:none; border-radius:0; box-shadow:none;">
                                <table class="table-image2" id="circularsTable">
                                    <thead>
                                        <tr>
                                            <th style="min-width:260px;">CIRCULAR TITLE</th>
                                            <th style="width:130px;">DATE</th>
                                            <th style="width:120px;">FILE TYPE</th>
                                            <th style="min-width:260px;">DESCRIPTION</th>
                                            <th style="width:230px; text-align:center;">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notifications as $cir): 
                                            $cir_file = !empty($cir['attachment_path']) ? $cir['attachment_path'] : (!empty($cir['file_path']) ? $cir['file_path'] : '');
                                            $cir_type = strtolower($cir['attachment_type'] ?? '');
                                            if (empty($cir_type) || $cir_type === 'none') {
                                                if (!empty($cir_file)) {
                                                    $ext = strtolower(pathinfo($cir_file, PATHINFO_EXTENSION));
                                                    if ($ext === 'pdf') $cir_type = 'pdf';
                                                    elseif (in_array($ext, ['doc', 'docx'])) $cir_type = 'docx';
                                                    elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $cir_type = 'image';
                                                    else $cir_type = 'other';
                                                } else {
                                                    $cir_type = 'none';
                                                }
                                            }
                                            $clean_desc = trim($cir['description'] ?? '');
                                        ?>
                                            <tr class="circular-row" data-title="<?php echo strtolower(htmlspecialchars($cir['title'])); ?>">
                                                <!-- Circular Title -->
                                                <td>
                                                    <div class="item-main-title" style="font-size:14px; font-weight:700; color:#0f172a; line-height:1.4;">
                                                        <?php echo htmlspecialchars($cir['title']); ?>
                                                    </div>
                                                    <div style="display:flex; align-items:center; gap:8px; margin-top:5px; flex-wrap:wrap;">
                                                        <span style="font-size:11px; color:#64748b; font-weight:600;">ID: CIR-<?php echo $cir['id']; ?></span>
                                                        <?php if (!empty($cir['is_marquee'])): ?>
                                                            <span style="font-size:10.5px; font-weight:700; color:#059669; background:#ecfdf5; border:1px solid #a7f3d0; padding:2px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                                                                <i class="fas fa-bullhorn" style="font-size:10px;"></i> Live Marquee
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <!-- Date -->
                                                <td>
                                                    <div style="font-weight:600; font-size:13px; color:#334155; display:inline-flex; align-items:center; gap:6px; white-space:nowrap;">
                                                        <i class="far fa-calendar-alt" style="color:#00b894;"></i>
                                                        <?php echo date('d M Y', strtotime($cir['publish_date'] ?? date('Y-m-d'))); ?>
                                                    </div>
                                                </td>

                                                <!-- File Type -->
                                                <td>
                                                    <?php if ($cir_type === 'pdf'): ?>
                                                        <a href="../<?php echo htmlspecialchars($cir_file); ?>" target="_blank" class="badge-file-type badge-pdf" title="Open PDF in new tab">
                                                            <i class="fas fa-file-pdf"></i> PDF
                                                        </a>
                                                    <?php elseif ($cir_type === 'docx'): ?>
                                                        <a href="../<?php echo htmlspecialchars($cir_file); ?>" download class="badge-file-type badge-docx" title="Download DOCX document">
                                                            <i class="fas fa-file-word"></i> DOCX
                                                        </a>
                                                    <?php elseif ($cir_type === 'image'): ?>
                                                        <a href="../<?php echo htmlspecialchars($cir_file); ?>" target="_blank" class="badge-file-type badge-image" title="View image in new tab">
                                                            <i class="fas fa-file-image"></i> IMAGE
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge-file-type badge-none">
                                                            <i class="fas fa-align-left"></i> TEXT
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Description -->
                                                <td>
                                                    <div style="font-size:12.5px; color:#475569; line-height:1.5; max-width:320px;">
                                                        <?php if (!empty($clean_desc)): ?>
                                                            <?php 
                                                            $desc_short = (strlen($clean_desc) > 130) ? substr($clean_desc, 0, 130) . '...' : $clean_desc;
                                                            echo htmlspecialchars($desc_short); 
                                                            ?>
                                                        <?php else: ?>
                                                            <span style="color:#94a3b8; font-style:italic;">No description provided</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <!-- Actions: View / Edit / Delete (Single Row, No Wrap) -->
                                                <td style="text-align:center; width:230px; white-space:nowrap;">
                                                    <div style="display:inline-flex; align-items:center; justify-content:center; gap:6px; flex-wrap:nowrap;">
                                                        <!-- View Button -->
                                                        <button type="button" class="btn-action-view" onclick="openViewCircularModal(<?php echo htmlspecialchars(json_encode($cir), ENT_QUOTES, 'UTF-8'); ?>)" title="View Circular">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>

                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn-action-edit" onclick="openEditCircularModal(<?php echo htmlspecialchars(json_encode($cir), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit Circular">
                                                            <i class="fas fa-pencil-alt"></i> Edit
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this circular?');" style="display:inline; margin:0;">
                                                            <input type="hidden" name="action" value="delete_circular">
                                                            <input type="hidden" name="id" value="<?php echo $cir['id']; ?>">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                            <button type="submit" class="btn-action-delete" title="Delete Circular">
                                                                <i class="fas fa-trash-alt"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 7: SCROLLBAR (Marquee Ticker Controls for index.php) -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'scrollbar'): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Homepage Scrollbar (Marquee Ticker)</h2>
                                <p>Manage live scrolling ticker announcements running on Trinity College Homepage</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <button type="button" class="btn-emerald-pill" onclick="openModal('modalScrollbar')">
                                <i class="fas fa-plus"></i> Add Scrollbar Ticker
                            </button>
                        </div>
                    </div>

                    <!-- Live Ticker Preview Card -->
                    <div class="form-card-light" style="padding:16px 20px; margin-bottom:20px; background:#ffffff;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <span style="font-size:12px; font-weight:700; color:#008f72; letter-spacing:0.5px;">
                                <i class="fas fa-broadcast-tower"></i> LIVE STREAMING TICKER ON INDEX.PHP:
                            </span>
                            <span class="status-pill-dot status-active">&bull; RUNNING</span>
                        </div>
                        <div style="background:#00b894; color:#ffffff; padding:10px 18px; border-radius:10px; font-size:13.5px; font-weight:600; overflow:hidden; white-space:nowrap;">
                            <span>⚡ We Proudly Announce That We Got JNTUH &amp; UGC AUTONOMOUS Status for Five Years &bull; NAAC Accredited &bull; Admissions Open 2024-25 | Helpline: 7396903383</span>
                        </div>
                    </div>

                    <div class="table-card-image2">
                        <table class="table-image2">
                            <thead>
                                <tr>
                                    <th>MARQUEE TICKER TEXT</th>
                                    <th>PUBLISHED DATE</th>
                                    <th>EXTERNAL URL</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scrollbar_list as $sb): ?>
                                    <tr>
                                        <td>
                                            <div class="item-main-title"><?php echo htmlspecialchars($sb['title']); ?></div>
                                            <div class="item-sub-text"><?php echo htmlspecialchars($sb['description'] ?: 'Homepage marquee display active'); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($sb['publish_date']); ?></div>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($sb['link_url'] ?: 'index.php'); ?></code>
                                        </td>
                                        <td><span class="status-pill-dot status-active">&bull; SCROLLING</span></td>
                                        <td>
                                            <div class="action-icons-wrap">
                                                <button type="button" class="btn-icon-circle btn-icon-check"><i class="fas fa-check"></i></button>
                                                <button type="button" class="btn-icon-circle btn-icon-edit" onclick="openModal('modalScrollbar')"><i class="fas fa-pencil-alt"></i></button>
                                                <form action="../backend/crud.php" method="POST" onsubmit="return confirm('Remove this ticker?');" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_scrollbar">
                                                    <input type="hidden" name="id" value="<?php echo $sb['id']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <button type="submit" class="btn-icon-circle btn-icon-trash"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================== -->
            <!-- TAB 8: ACTIVITY LOGS -->
            <!-- ============================================================== -->
            <?php if ($current_tab === 'activity_logs' && $is_admin): ?>
                <div class="tab-pane active">
                    <div class="pane-header-image2">
                        <div class="pane-title-group">
                            <div class="pane-text">
                                <h2>Activity Logs &amp; Audit Trail</h2>
                                <p>Historical registry of who changed what and when &bull; Enterprise security logging</p>
                            </div>
                        </div>
                        <div class="pane-actions-right">
                            <span class="events-count-badge">
                                <i class="fas fa-shield-alt"></i> Audit Trail Active
                            </span>
                        </div>
                    </div>

                    <!-- System Audit Trail Table Card -->
                    <div class="events-section-card">
                        <div class="events-section-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                            <div class="events-section-title-wrap">
                                <span class="events-section-num num-blue"><i class="fas fa-history"></i></span>
                                <div>
                                    <h3 class="events-section-heading">System Audit Trail</h3>
                                    <p class="events-section-sub">Real-time log of administrative actions &bull; Security, content updates &amp; authentication</p>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                <div class="search-box-pill" style="margin:0;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="logSearchInput" class="search-pill-input" placeholder="Search admin, record, or action..." onkeyup="filterLogsTable(this.value)">
                                </div>
                                <span class="events-count-badge">
                                    <i class="fas fa-clipboard-list"></i> <?php echo count($activity_logs); ?> Log Entries
                                </span>
                            </div>
                        </div>

                        <!-- Filter Toolbar -->
                        <div style="padding:14px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                            <form method="GET" action="dashboard.php" class="log-filter-groups" style="display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin:0;">
                                <input type="hidden" name="tab" value="activity_logs">
                                
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Module:</span>
                                    <select name="log_module" class="filter-pill-select" onchange="this.form.submit()" style="background:#fff; border:1px solid #cbd5e1; border-radius:20px; padding:6px 14px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                                        <option value="all" <?php echo $log_module_filter === 'all' ? 'selected' : ''; ?>>All Modules</option>
                                        <option value="Users" <?php echo $log_module_filter === 'Users' ? 'selected' : ''; ?>>Users</option>
                                        <option value="Workshops" <?php echo $log_module_filter === 'Workshops' ? 'selected' : ''; ?>>Workshops</option>
                                        <option value="Events" <?php echo $log_module_filter === 'Events' ? 'selected' : ''; ?>>Events</option>
                                        <option value="News" <?php echo $log_module_filter === 'News' ? 'selected' : ''; ?>>News</option>
                                        <option value="Notifications" <?php echo $log_module_filter === 'Notifications' ? 'selected' : ''; ?>>Notifications</option>
                                    </select>
                                </div>

                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Action:</span>
                                    <select name="log_action" class="filter-pill-select" onchange="this.form.submit()" style="background:#fff; border:1px solid #cbd5e1; border-radius:20px; padding:6px 14px; font-size:12.5px; font-weight:600; color:#334155; cursor:pointer;">
                                        <option value="all" <?php echo $log_action_filter === 'all' ? 'selected' : ''; ?>>All Actions</option>
                                        <option value="Added" <?php echo $log_action_filter === 'Added' ? 'selected' : ''; ?>>Added</option>
                                        <option value="Updated" <?php echo $log_action_filter === 'Updated' ? 'selected' : ''; ?>>Updated</option>
                                        <option value="Deleted" <?php echo $log_action_filter === 'Deleted' ? 'selected' : ''; ?>>Deleted</option>
                                        <option value="Login" <?php echo $log_action_filter === 'Login' ? 'selected' : ''; ?>>Login</option>
                                    </select>
                                </div>

                                <?php if ($log_module_filter !== 'all' || $log_action_filter !== 'all'): ?>
                                    <a href="?tab=activity_logs" style="font-size:12px; color:#ef4444; text-decoration:none; font-weight:600; margin-left:4px; display:inline-flex; align-items:center; gap:4px;">
                                        <i class="fas fa-times-circle"></i> Reset Filters
                                    </a>
                                <?php endif; ?>
                            </form>

                            <div style="font-size:12.5px; color:#64748b;">
                                Showing <strong><?php echo count($activity_logs); ?></strong> recorded actions
                            </div>
                        </div>

                        <?php if (empty($activity_logs)): ?>
                            <div style="padding: 56px 24px; text-align: center;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h4 style="margin: 0 0 6px; color: #1e293b; font-size: 16px;">No Activity Logs Found</h4>
                                <p style="margin: 0 auto; max-width: 440px; color: #64748b; font-size: 13px;">
                                    There are currently no recorded administrative activities matching your selected filters.
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-card-image2" style="border:none; border-radius:0; box-shadow:none;">
                                <table class="table-image2" id="activityLogsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 170px;">ADMINISTRATOR</th>
                                            <th style="width: 120px;">ACTION</th>
                                            <th style="width: 140px;">MODULE</th>
                                            <th style="min-width: 200px;">RECORD / TARGET</th>
                                            <th style="width: 180px;">DATE &amp; TIME</th>
                                            <th style="min-width: 260px;">DETAILS &amp; NOTES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activity_logs as $log): 
                                            $act = strtoupper($log['action'] ?? 'ACTION');
                                            $badgeStyle = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;';
                                            if ($act === 'ADDED' || $act === 'CREATE') {
                                                $badgeStyle = 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;';
                                            } elseif ($act === 'UPDATED' || $act === 'EDIT') {
                                                $badgeStyle = 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;';
                                            } elseif ($act === 'DELETED' || $act === 'REMOVE') {
                                                $badgeStyle = 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;';
                                            }
                                            $adminInitial = strtoupper(substr($log['admin_name'] ?? 'A', 0, 1));
                                        ?>
                                            <tr>
                                                <!-- Administrator -->
                                                <td>
                                                    <div style="display:flex; align-items:center; gap:10px;">
                                                        <div style="width:34px; height:34px; border-radius:50%; background:#ecfdf5; color:#00b894; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center; border:1px solid #a7f3d0;">
                                                            <?php echo htmlspecialchars($adminInitial); ?>
                                                        </div>
                                                        <div>
                                                            <div style="font-weight:700; color:#0f172a; font-size:13.5px;"><?php echo htmlspecialchars($log['admin_name']); ?></div>
                                                            <span style="font-size:11px; color:#64748b;">Admin</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Action Badge -->
                                                <td>
                                                    <span style="display:inline-block; font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px; letter-spacing:0.4px; <?php echo $badgeStyle; ?>">
                                                        <?php echo htmlspecialchars($act); ?>
                                                    </span>
                                                </td>

                                                <!-- Module -->
                                                <td>
                                                    <span class="linked-event-pill" style="font-size:11.5px; padding:4px 10px; background:#f1f5f9; color:#334155; font-weight:600; border-radius:6px;">
                                                        <i class="fas fa-layer-group" style="color:#00b894;"></i> <?php echo htmlspecialchars($log['module']); ?>
                                                    </span>
                                                </td>

                                                <!-- Record / Target -->
                                                <td>
                                                    <div class="item-main-title" style="font-size:13.5px; font-weight:700; color:#0f172a;">
                                                        <?php echo htmlspecialchars($log['record_name']); ?>
                                                    </div>
                                                </td>

                                                <!-- Date & Time -->
                                                <td>
                                                    <div style="font-size:12.5px; color:#475569; font-weight:600; display:inline-flex; align-items:center; gap:6px; white-space:nowrap;">
                                                        <i class="far fa-calendar-alt" style="color:#00b894;"></i> <?php echo format_log_time($log['created_at']); ?>
                                                    </div>
                                                </td>

                                                <!-- Details & Notes -->
                                                <td>
                                                    <div style="max-width:340px; font-size:12.5px; color:#475569; line-height:1.5;">
                                                        <?php if (!empty($log['description'])): ?>
                                                            <i class="fas fa-info-circle" style="color:#94a3b8; font-size:11.5px; margin-right:4px;"></i>
                                                            <?php echo htmlspecialchars($log['description']); ?>
                                                        <?php else: ?>
                                                            <span style="color:#94a3b8; font-style:italic;">No additional notes</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL POP-UPS (Clean Light Theme) -->
    <!-- ============================================================== -->

    <!-- Modal Pop-up: Upload Official Circular -->
    <div class="modal-overlay" id="modalUploadCircular" onclick="handleBackdropClick(event, 'modalUploadCircular')">
        <div class="modal-dialog" style="max-width: 680px;">
            <div class="modal-header">
                <h3><i class="fas fa-cloud-upload-alt" style="color:#00b894;"></i> Upload Official Circular</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalUploadCircular')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_circular">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="category" value="Circular">

                <div class="modal-body" style="padding: 24px;">
                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:16px;">
                        <!-- Circular Title -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                Circular Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. B.Tech Autonomous End Semester Examination Schedule AY 2026-27" required style="width:100%; height:44px; font-size:13.5px;">
                        </div>

                        <!-- Date -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                Date <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="publish_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; height:44px; font-size:13.5px;">
                        </div>
                    </div>

                    <!-- Upload PDF / Image / DOCX Dropzone -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Upload Document (PDF / Image / DOCX)
                        </label>
                        <div class="circular-dropzone-box" onclick="document.getElementById('modal_circular_upload_input').click()" style="padding:22px; text-align:center; border:2px dashed #cbd5e1; border-radius:12px; background:#f8fafc; cursor:pointer; transition:all 0.2s;">
                            <input type="file" id="modal_circular_upload_input" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="display:none;" onchange="previewModalCircularUpload(this)">
                            
                            <div id="modal_circular_upload_placeholder" style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="width:46px; height:46px; border-radius:50%; background:#ecfdf5; color:#00b894; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div style="font-size:13.5px; font-weight:600; color:#1e293b;">
                                    Click to browse or drag &amp; drop official circular file
                                </div>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; justify-content:center; margin-top:2px;">
                                    <span class="badge-file-type badge-pdf"><i class="fas fa-file-pdf"></i> PDF (.pdf)</span>
                                    <span class="badge-file-type badge-image"><i class="fas fa-file-image"></i> Image (.jpg, .png, .webp)</span>
                                    <span class="badge-file-type badge-docx"><i class="fas fa-file-word"></i> Word (.docx, .doc)</span>
                                </div>
                            </div>

                            <div id="modal_circular_upload_preview" style="display:none; align-items:center; justify-content:space-between; width:100%; background:#ffffff; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px;">
                                <div style="display:flex; align-items:center; gap:12px; text-align:left;">
                                    <span id="modal_circular_preview_icon" class="circular-doc-icon-large circular-doc-icon-pdf" style="width:42px; height:42px; font-size:18px;"><i class="fas fa-file"></i></span>
                                    <div>
                                        <div id="modal_circular_preview_name" style="font-size:13px; font-weight:700; color:#1e293b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></div>
                                        <div id="modal_circular_preview_size" style="font-size:11.5px; color:#64748b;"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn-outline-pill" style="padding:4px 12px; font-size:11px;" onclick="event.stopPropagation(); resetModalCircularUpload();">Change File</button>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Description
                        </label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter key points, department instructions, eligibility criteria, or circular summary..." style="font-size:13.5px; width:100%;"></textarea>
                    </div>

                    <div style="background:#f8fafc; padding:12px 16px; border-radius:10px; border:1px solid #f1f5f9; display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" id="modal_cir_marquee" name="is_marquee" value="1" checked style="accent-color:#00b894; width:16px; height:16px;">
                        <label for="modal_cir_marquee" style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#475569; margin:0; cursor:pointer;">
                            <i class="fas fa-bullhorn" style="color:#00b894;"></i> Broadcast on Homepage Live Ticker Marquee
                        </label>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalUploadCircular')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill" style="padding:9px 24px; font-weight:700;">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Circular
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 1. Modal Pop-up: Create Notification / Circular -->
    <div class="modal-overlay" id="modalNotification" onclick="handleBackdropClick(event, 'modalNotification')">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3><i class="fas fa-bullhorn" style="color:#00b894;"></i> Publish Circular &amp; Notification</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalNotification')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_notification">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_notif_title">Notification Title *</label>
                        <input type="text" id="modal_notif_title" name="title" class="form-control" placeholder="e.g. B.Tech Autonomous Examination Schedule" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="modal_notif_category">Category</label>
                            <select id="modal_notif_category" name="category" class="form-control">
                                <option value="Examination" selected>Examination</option>
                                <option value="Admissions">Admissions</option>
                                <option value="Academic">Academic</option>
                                <option value="Placements">Placements</option>
                                <option value="Circular">Official Circular</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modal_publish_date">Publish Date</label>
                            <input type="date" id="modal_publish_date" name="publish_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_notif_file">Attachment File (PDF, Image, or DOCX)</label>
                        <input type="file" id="modal_notif_file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                        <small class="field-hint">Stored in GoDaddy <code>uploads/pdfs/</code> or <code>uploads/documents/</code>.</small>
                    </div>

                    <div class="form-group">
                        <label for="modal_link_url">External / Page Link (Optional)</label>
                        <input type="text" id="modal_link_url" name="link_url" class="form-control" placeholder="e.g. circulars.php or https://...">
                    </div>

                    <div class="form-group">
                        <label for="modal_notif_desc">Notification Text / Details</label>
                        <textarea id="modal_notif_desc" name="description" class="form-control" rows="3" placeholder="Enter detailed exam or notification summary..."></textarea>
                    </div>

                    <div style="background: #fffbeb; padding: 10px 14px; border-radius: 10px; border: 1px solid #fef3c7; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="modal_is_marquee" name="is_marquee" value="1" checked style="accent-color:#00b894; width:16px; height:16px;">
                        <label for="modal_is_marquee" style="font-weight: 700; color: #92400e; margin:0; cursor:pointer;">
                            <i class="fas fa-bullhorn"></i> Also Show on Homepage Scrollbar Marquee
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalNotification')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-paper-plane"></i> Publish Notification</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Modal Pop-up: Add Workshop / Task -->
    <div class="modal-overlay" id="modalWorkshop" onclick="handleBackdropClick(event, 'modalWorkshop')">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3><i class="fas fa-laptop-code" style="color:#00b894;"></i> Add Workshop / Technical Task</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalWorkshop')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="add_workshop">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Workshop / Task Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Full-Stack AI &amp; Cloud Deployment" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Instructor / Mentor</label>
                            <input type="text" name="instructor" class="form-control" placeholder="e.g. Dr. A. K. Vootla">
                        </div>
                        <div class="form-group">
                            <label>Domain / Category</label>
                            <input type="text" name="category" class="form-control" value="AI / ML">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Venue / Lab</label>
                            <input type="text" name="venue" class="form-control" value="TCEK Seminar Hall">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Task Description / Roadmap</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Learning objectives, student prerequisites..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalWorkshop')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-check-circle"></i> Save Workshop</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Modal Pop-up: Step 1 — Create Event -->
    <div class="modal-overlay" id="modalEvent" onclick="handleBackdropClick(event, 'modalEvent')">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3><i class="far fa-calendar-plus" style="color:#00b894;"></i> Create College Event</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Step 1: Enter event information and schedule</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEvent')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="add_event">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Event Name / Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Freshers Aarambh 2K26 / Annual Tech Fest" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Event Date *</label>
                            <input type="date" name="event_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Event Time *</label>
                            <input type="text" name="event_time" class="form-control" placeholder="e.g. 10:00 AM – 04:30 PM" value="10:00 AM" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Campus Venue</label>
                        <input type="text" name="venue" class="form-control" value="Trinity Campus Auditorium" placeholder="Auditorium, Seminar Hall, Ground...">
                    </div>

                    <div class="form-group">
                        <label>Event Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Brief event description, key highlights, schedule, or guests..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalEvent')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-calendar-plus"></i> Create Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3b. Modal Pop-up: Upload Event File -->
    <div class="modal-overlay" id="modalEventMedia" onclick="handleBackdropClick(event, 'modalEventMedia')">
        <div class="modal-dialog" style="max-width: 680px;">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-cloud-upload-alt" style="color:#00b894;"></i> Upload Event File</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Upload images or videos directly linked to an event &bull; Automatically displayed on public website</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEventMedia')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_event_media">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body" style="padding: 24px;">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <!-- Select Event -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                Select Event <span style="color:#ef4444;">*</span>
                            </label>
                            <select name="event_id" id="event_media_select_id" class="form-control" required style="width:100%; height:44px; font-size:13.5px; border-radius:10px;">
                                <option value="">-- Choose an Event --</option>
                                <?php 
                                $preselected_eid = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
                                foreach ($events as $evOption): 
                                ?>
                                    <option value="<?php echo $evOption['id']; ?>" <?php echo $preselected_eid === (int)$evOption['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($evOption['title']); ?> (<?php echo htmlspecialchars($evOption['event_date']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Media Name / Title -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                File Name / Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="media_title" id="event_media_title_input" class="form-control" placeholder="e.g. Flash Mob Highlight Video / Welcome Poster" required style="width:100%; height:44px; font-size:13.5px; border-radius:10px;">
                        </div>
                    </div>

                    <!-- File Dropzone -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Upload Image or Video <span style="color:#ef4444;">*</span> <span style="font-weight:400; color:#64748b; font-size:12px;">(JPG, PNG, WEBP, MP4, WEBM)</span>
                        </label>
                        <div class="media-dropzone-box" onclick="document.getElementById('event_media_file_input').click();" style="border:2px dashed #cbd5e1; border-radius:12px; background:#f8fafc; padding:20px; text-align:center; cursor:pointer; transition:all 0.2s;">
                            <input type="file" name="media_file" id="event_media_file_input" class="form-control" accept="image/*,video/mp4,video/webm" required onchange="previewMediaUpload(this)" style="display:none;">
                            <div id="mediaUploadPlaceholder" style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px;">
                                <div style="width:48px; height:48px; border-radius:50%; background:#ecfdf5; color:#00b894; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                    <i class="fas fa-photo-video"></i>
                                </div>
                                <div style="font-size:13.5px; font-weight:600; color:#1e293b;">Click to upload image or video file</div>
                                <div style="font-size:12px; color:#64748b;">Supports high-resolution photos and MP4/WEBM video clips up to 50MB</div>
                            </div>
                            <div id="mediaUploadPreviewContainer" style="display:none; padding:8px; align-items:center; gap:16px; justify-content:center; flex-wrap:wrap;">
                                <!-- Instant JS preview -->
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Media Description (Optional)
                        </label>
                        <textarea name="media_description" id="event_media_desc_input" class="form-control" rows="3" placeholder="Brief caption, performance highlights, or photo description..." style="font-size:13.5px; width:100%; border-radius:10px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12px; color:#64748b;">
                        <i class="fas fa-info-circle" style="color:#00b894;"></i> Automatically displayed under the selected event on the public website.
                    </span>
                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn-outline-pill" onclick="closeModal('modalEventMedia')">Cancel</button>
                        <button type="submit" class="btn-emerald-pill" style="padding:9px 24px; font-weight:700;">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Event File
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 3c. Modal Pop-up: View & Manage Attached Event Media -->
    <div class="modal-overlay" id="modalViewEventMedia" onclick="handleBackdropClick(event, 'modalViewEventMedia')">
        <div class="modal-dialog modal-dialog-lg">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-photo-video" style="color:#00b894;"></i> <span id="viewMediaModalTitle">Event Media</span></h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;" id="viewMediaModalSubtitle">Attached photos and videos for this event</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalViewEventMedia')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="viewMediaGrid" class="event-media-mgmt-grid">
                    <!-- Populated dynamically via viewEventMedia() -->
                </div>
            </div>
            <div class="modal-footer" style="justify-content:space-between;">
                <button type="button" class="btn-emerald-pill" onclick="openMediaUploadFromView()">
                    <i class="fas fa-cloud-upload-alt"></i> Upload More Details
                </button>
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalViewEventMedia')">Close</button>
            </div>
        </div>
    </div>

    <!-- 3d. Modal Pop-up: Edit College Event -->
    <div class="modal-overlay" id="modalEditEvent" onclick="handleBackdropClick(event, 'modalEditEvent')">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-edit" style="color:#d97706;"></i> Edit Event Details</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Update schedule, timing, venue, or description</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditEvent')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="update_event">
                <input type="hidden" name="id" id="edit_event_id" value="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Event Name / Title *</label>
                        <input type="text" name="title" id="edit_event_title" class="form-control" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Event Date *</label>
                            <input type="date" name="event_date" id="edit_event_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Event Time *</label>
                            <input type="text" name="event_time" id="edit_event_time" class="form-control" placeholder="e.g. 10:00 AM – 04:30 PM" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Campus Venue</label>
                        <input type="text" name="venue" id="edit_event_venue" class="form-control" placeholder="Auditorium, Seminar Hall, Ground...">
                    </div>

                    <div class="form-group">
                        <label>Event Description</label>
                        <textarea name="description" id="edit_event_description" class="form-control" rows="4" placeholder="Brief event description, key highlights, schedule, or guests..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditEvent')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3e. Modal Pop-up: View Event Details & Attached Media -->
    <div class="modal-overlay" id="modalViewEvent" onclick="handleBackdropClick(event, 'modalViewEvent')">
        <div class="modal-dialog modal-dialog-lg">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-calendar-check" style="color:#00b894;"></i> <span id="viewEventDetailsTitle">Event Details</span></h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;" id="viewEventDetailsSubtitle">Event summary &amp; attached media files</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalViewEvent')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:16px;">
                    <div style="display:flex; gap:16px; flex-wrap:wrap; font-size:13px; color:#334155; margin-bottom:8px;">
                        <span><i class="far fa-calendar-alt" style="color:#00b894;"></i> <strong id="viewEventDateText"></strong></span>
                        <span><i class="far fa-clock" style="color:#00b894;"></i> <span id="viewEventTimeText"></span></span>
                        <span><i class="fas fa-map-marker-alt" style="color:#00b894;"></i> <span id="viewEventVenueText"></span></span>
                    </div>
                    <p id="viewEventDescText" style="margin:0; font-size:13px; color:#475569; line-height:1.5;"></p>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <h4 style="font-size:14px; font-weight:700; color:#0f172a; margin:0;">Attached Photos &amp; Videos:</h4>
                    <span id="viewEventFilesCount" style="font-size:12px; color:#64748b; font-weight:600;"></span>
                </div>
                <div id="viewEventAttachedGrid" class="event-media-mgmt-grid">
                    <!-- Populated dynamically via viewEventDetails() -->
                </div>
            </div>
            <div class="modal-footer" style="justify-content:space-between;">
                <button type="button" class="btn-emerald-pill" onclick="quickUploadForViewingEvent()">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Files to this Event
                </button>
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalViewEvent')">Close</button>
            </div>
        </div>
    </div>

    <!-- 3f. Modal Pop-up: Edit Uploaded Event File -->
    <div class="modal-overlay" id="modalEditEventMedia" onclick="handleBackdropClick(event, 'modalEditEventMedia')">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-edit" style="color:#d97706;"></i> Edit Event File</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Update file name, description, or change linked event</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditEventMedia')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="update_event_media">
                <input type="hidden" name="media_id" id="edit_media_id" value="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Linked Event *</label>
                        <select name="event_id" id="edit_media_event_id" class="form-control" required>
                            <?php foreach ($events as $evOption): ?>
                                <option value="<?php echo $evOption['id']; ?>">
                                    <?php echo htmlspecialchars($evOption['title']); ?> (<?php echo htmlspecialchars($evOption['event_date']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>File Name / Title *</label>
                        <input type="text" name="media_title" id="edit_media_title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="media_description" id="edit_media_description" class="form-control" rows="3" placeholder="Caption or file notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditEventMedia')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. Modal Pop-up: Upload News Clipping -->
    <div class="modal-overlay" id="modalUploadNews" onclick="handleBackdropClick(event, 'modalUploadNews')">
        <div class="modal-dialog" style="max-width: 680px;">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-newspaper" style="color:#00b894;"></i> Upload Newspaper News</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Upload newspaper clippings with title, date, and description for public website display</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalUploadNews')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data" id="formModalUploadNews">
                <input type="hidden" name="action" value="add_news">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="source" value="Press &amp; Media">

                <div class="modal-body" style="padding: 24px;">
                    <!-- Upload Newspaper Image -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Upload Newspaper Image <span style="color:#ef4444;">*</span> <span style="font-weight:400; color:#64748b; font-size:12px;">(JPG, PNG, WEBP — Press clipping scan or photo)</span>
                        </label>
                        <div class="news-dropzone-box" onclick="document.getElementById('modal_news_image_upload_input').click();" style="border:2px dashed #cbd5e1; border-radius:12px; background:#f8fafc; padding:20px; text-align:center; cursor:pointer; transition:all 0.2s;">
                            <input type="file" name="newspaper_image" id="modal_news_image_upload_input" accept="image/jpeg,image/png,image/webp,image/gif" required onchange="previewNewsUpload(this)" style="display:none;">
                            <div id="news_upload_placeholder" style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px;">
                                <div style="width:48px; height:48px; border-radius:50%; background:#ecfdf5; color:#00b894; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div style="font-size:13.5px; font-weight:600; color:#1e293b;">Click to upload newspaper image or drag and drop</div>
                                <div style="font-size:12px; color:#64748b;">Supports high-resolution press clippings up to 25MB (JPG, PNG, WEBP)</div>
                            </div>
                            <div id="news_upload_preview" style="display:none; padding:8px; align-items:center; gap:16px; justify-content:center; flex-wrap:wrap;">
                                <img id="news_upload_preview_img" src="" alt="Preview" style="max-height:110px; max-width:200px; object-fit:contain; border-radius:8px; border:1px solid #cbd5e1; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <div style="text-align:left;">
                                    <div id="news_upload_preview_name" style="font-size:13px; font-weight:700; color:#0f172a;"></div>
                                    <div id="news_upload_preview_size" style="font-size:12px; color:#64748b;"></div>
                                    <span style="display:inline-block; margin-top:6px; font-size:11.5px; color:#00b894; font-weight:600;"><i class="fas fa-check-circle"></i> Image selected. Click box to change</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:16px;">
                        <!-- News Title -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                News Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="title" id="modal_news_title_input" class="form-control" placeholder="e.g. Mana Telangana: Yuva Sangam - Trinity Student Selected for IIT Guwahati" required style="width:100%; height:44px; font-size:13.5px; border-radius:10px;">
                        </div>

                        <!-- Date -->
                        <div class="form-group" style="margin:0;">
                            <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                                Date <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="date" name="publish_date" id="modal_news_date_input" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; height:44px; font-size:13.5px; border-radius:10px;">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px;">
                            Description
                        </label>
                        <textarea name="description" id="modal_news_desc_input" class="form-control" rows="3" placeholder="Provide details, summary, or publication context of this newspaper clipping..." style="font-size:13.5px; width:100%; border-radius:10px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12px; color:#64748b;">
                        <i class="fas fa-globe" style="color:#00b894;"></i> Automatically appears in the News section of the public website.
                    </span>
                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn-outline-pill" onclick="closeModal('modalUploadNews')">Cancel</button>
                        <button type="submit" class="btn-emerald-pill" style="padding:9px 24px; font-weight:700;">
                            <i class="fas fa-newspaper"></i> Upload News
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 4a. Modal Pop-up: View Newspaper News Clipping -->
    <div class="modal-overlay" id="modalViewNews" onclick="handleBackdropClick(event, 'modalViewNews')">
        <div class="modal-dialog modal-dialog-lg">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-newspaper" style="color:#00b894;"></i> <span id="view_news_modal_title">Newspaper Clipping</span></h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">
                        <span id="view_news_modal_source" class="linked-event-pill" style="font-size:11px;">Press &amp; Media</span> &bull; 
                        Published: <strong id="view_news_modal_date"></strong>
                    </p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalViewNews')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Large Clipping Image Viewer -->
                <div class="news-full-view-container">
                    <img id="view_news_modal_img" src="" alt="Newspaper Clipping" class="news-full-view-img">
                </div>

                <!-- Description / Text -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
                    <h5 style="margin:0 0 8px; font-size:13px; font-weight:700; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px;">
                        <i class="fas fa-align-left" style="color:#00b894;"></i> Article Summary / Details:
                    </h5>
                    <p id="view_news_modal_desc" style="margin:0; font-size:13.5px; color:#334155; line-height:1.6; white-space:pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalViewNews')">Close</button>
            </div>
        </div>
    </div>

    <!-- 4b. Modal Pop-up: Edit Newspaper News -->
    <div class="modal-overlay" id="modalEditNews" onclick="handleBackdropClick(event, 'modalEditNews')">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-edit" style="color:#d97706;"></i> Edit Newspaper News</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Update news headline, publication date, description, or newspaper image</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditNews')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_news">
                <input type="hidden" name="id" id="edit_news_id" value="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <!-- Current Image Preview -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Current Newspaper Image</label>
                        <div style="display:flex; align-items:center; gap:14px; background:#f8fafc; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0;">
                            <img id="edit_news_current_img" src="" alt="Current image" style="height:64px; width:92px; object-fit:cover; border-radius:8px; border:1px solid #cbd5e1;">
                            <div style="font-size:12px; color:#64748b;">
                                <strong>Active clipping</strong><br>
                                Leave upload empty below to keep this image.
                            </div>
                        </div>
                    </div>

                    <!-- Replace Newspaper Image -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">
                            Replace Newspaper Image (Optional)
                        </label>
                        <input type="file" name="newspaper_image" id="edit_news_file_input" class="form-control" accept="image/*" onchange="previewEditNewsImage(this)" style="background:#fff; height:42px; padding:7px 12px; border-radius:10px;">
                        <div id="edit_news_new_preview_box" style="display:none; margin-top:8px;">
                            <img id="edit_news_new_img" src="" alt="New preview" style="max-height:80px; border-radius:6px; border:1px solid #00b894;">
                            <span style="font-size:11.5px; color:#00b894; margin-left:8px; font-weight:600;"><i class="fas fa-check"></i> New image selected</span>
                        </div>
                    </div>

                    <!-- News Title -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">News Title *</label>
                        <input type="text" name="title" id="edit_news_title" class="form-control" required style="background:#fff; height:42px; border-radius:10px;">
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Date *</label>
                        <input type="date" name="publish_date" id="edit_news_date" class="form-control" required style="background:#fff; height:42px; border-radius:10px;">
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Description</label>
                        <textarea name="description" id="edit_news_desc" class="form-control" rows="4" placeholder="Description of the newspaper clipping..." style="background:#fff; border-radius:10px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditNews')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- 4c. Modal Pop-up: View Official Circular -->
    <div class="modal-overlay" id="modalViewCircular" onclick="handleBackdropClick(event, 'modalViewCircular')">
        <div class="modal-dialog modal-dialog-lg">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-file-alt" style="color:#00b894;"></i> <span id="view_cir_modal_title">Circular Details</span></h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">
                        Published: <strong id="view_cir_modal_date"></strong> &bull; 
                        Type: <span id="view_cir_modal_badge"></span>
                    </p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalViewCircular')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Attached Document Preview Card -->
                <div id="view_cir_attachment_container" class="circular-preview-box" style="margin-top:0; margin-bottom:16px;">
                    <!-- Populated dynamically via JS: Image preview, PDF viewer link, or DOCX download -->
                </div>

                <!-- Description / Text -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
                    <h5 style="margin:0 0 8px; font-size:13px; font-weight:700; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px;">
                        <i class="fas fa-align-left" style="color:#00b894;"></i> Circular Summary / Instructions:
                    </h5>
                    <p id="view_cir_modal_desc" style="margin:0; font-size:13.5px; color:#334155; line-height:1.6; white-space:pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-pill" onclick="closeModal('modalViewCircular')">Close</button>
            </div>
        </div>
    </div>

    <!-- 4d. Modal Pop-up: Edit Official Circular -->
    <div class="modal-overlay" id="modalEditCircular" onclick="handleBackdropClick(event, 'modalEditCircular')">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3><i class="fas fa-edit" style="color:#00b894;"></i> Edit Circular</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Update circular title, publish date, document attachment, or description</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalEditCircular')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_circular">
                <input type="hidden" name="id" id="edit_cir_id" value="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <!-- Circular Title -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Circular Title *</label>
                        <input type="text" name="title" id="edit_cir_title" class="form-control" required style="background:#fff; height:42px; border-radius:10px;">
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Date *</label>
                        <input type="date" name="publish_date" id="edit_cir_date" class="form-control" required style="background:#fff; height:42px; border-radius:10px;">
                    </div>

                    <!-- Current Attached File -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Current Document Attachment</label>
                        <div id="edit_cir_current_file_wrap" style="background:#f8fafc; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0;">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Replace Document File -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">
                            Replace Document (PDF / Image / DOCX) - Optional
                        </label>
                        <input type="file" name="attachment" id="edit_cir_file_input" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="background:#fff; height:42px; padding:7px 12px; border-radius:10px;">
                        <small style="color:#64748b; font-size:11.5px; margin-top:4px; display:block;">Leave blank to keep current attached document.</small>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:6px; display:block;">Description</label>
                        <textarea name="description" id="edit_cir_desc" class="form-control" rows="3" placeholder="Circular summary..." style="background:#fff; border-radius:10px;"></textarea>
                    </div>

                    <div style="background:#fffbeb; padding:10px 14px; border-radius:10px; border:1px solid #fef3c7; display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" id="edit_cir_is_marquee" name="is_marquee" value="1" style="accent-color:#00b894; width:16px; height:16px;">
                        <label for="edit_cir_is_marquee" style="font-weight:700; color:#92400e; margin:0; cursor:pointer; font-size:12.5px;">
                            <i class="fas fa-bullhorn"></i> Broadcast on Homepage Scrollbar Marquee
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalEditCircular')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. Modal Pop-up: Add Scrollbar Marquee Ticker -->
    <div class="modal-overlay" id="modalScrollbar" onclick="handleBackdropClick(event, 'modalScrollbar')">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3><i class="fas fa-scroll" style="color:#00b894;"></i> Add Scrollbar Marquee Announcement</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalScrollbar')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="add_scrollbar">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Scrolling Ticker Headline *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Admissions Open 2024-25: B.Tech, Diploma &amp; MBA | Code: TCEK" required>
                    </div>

                    <div class="form-group">
                        <label>Detailed Ticker Subtitle / Contact</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Helpline: 7396903383, 8522954369"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Clickable URL (Optional)</label>
                        <input type="text" name="link_url" class="form-control" value="admission.php">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalScrollbar')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-play"></i> Add to Live Scrollbar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. Modal Pop-up: Create User Account -->
    <div class="modal-overlay" id="modalUser" onclick="handleBackdropClick(event, 'modalUser')">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3><i class="fas fa-user-shield" style="color:#00b894;"></i> Create Portal User Account</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalUser')">&times;</button>
            </div>
            <form action="../backend/crud.php" method="POST">
                <input type="hidden" name="action" value="add_user">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="modal-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. charan" required>
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="e.g. Charan" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="admin">Administrator (Full Access)</option>
                                <option value="editor">Editor (Uploads &amp; Notices)</option>
                                <option value="staff">Staff (Standard)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="user@tcek.in">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline-pill" onclick="closeModal('modalUser')">Cancel</button>
                    <button type="submit" class="btn-emerald-pill"><i class="fas fa-user-check"></i> Save User Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Pop-up Controls
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function handleBackdropClick(event, modalId) {
            if (event.target && event.target.id === modalId) {
                closeModal(modalId);
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModals = document.querySelectorAll('.modal-overlay.active');
                activeModals.forEach(m => m.classList.remove('active'));
                document.body.style.overflow = '';
            }
        });

        // Toggle Sidebar on mobile or desktop collapse
        const btnToggle = document.getElementById('btnToggleSidebar');
        const btnCollapse = document.getElementById('btnCollapseSidebar');
        const sidebar = document.getElementById('adminSidebar');

        if (btnToggle && sidebar) {
            btnToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
        if (btnCollapse && sidebar) {
            btnCollapse.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Generic Table Filter
        function filterGenericTable(tableId, query) {
            const q = query.toLowerCase();
            const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        function filterLiveTable(query) {
            filterGenericTable('mainDataTable', query);
        }

        function filterBranchTable(val) {
            const rows = document.querySelectorAll('tbody tr.data-row');
            rows.forEach(r => {
                const branch = r.getAttribute('data-branch') || '';
                if (val === 'all' || branch.toLowerCase().includes(val.toLowerCase())) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        function filterStatusTable(val) {
            const rows = document.querySelectorAll('tbody tr.data-row');
            rows.forEach(r => {
                const status = r.getAttribute('data-status') || '';
                if (val === 'all' || status.toUpperCase() === val.toUpperCase()) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // --- Step 2 & Media Management Helpers ---
        function openEventMediaModal(eventId, eventTitle) {
            const selectEl = document.getElementById('event_media_select_id');
            if (selectEl && eventId) {
                selectEl.value = eventId;
            }
            const titleInput = document.getElementById('event_media_title_input');
            if (titleInput && eventTitle) {
                titleInput.placeholder = eventTitle + ' - Photo / Video';
            }
            const previewBox = document.getElementById('mediaUploadPreviewContainer');
            if (previewBox) {
                previewBox.style.display = 'none';
                previewBox.innerHTML = '';
            }
            const placeholder = document.getElementById('mediaUploadPlaceholder');
            if (placeholder) placeholder.style.display = 'flex';
            const fileInput = document.getElementById('event_media_file_input');
            if (fileInput) fileInput.value = '';
            openModal('modalEventMedia');
        }

        let currentViewingEvent = null;
        function viewEventMedia(eventData) {
            currentViewingEvent = eventData;
            const titleEl = document.getElementById('viewMediaModalTitle');
            const subtitleEl = document.getElementById('viewMediaModalSubtitle');
            const gridEl = document.getElementById('viewMediaGrid');

            if (titleEl) titleEl.textContent = eventData.title || 'Event Media Gallery';
            const count = (eventData.media_items && Array.isArray(eventData.media_items)) ? eventData.media_items.length : 0;
            if (subtitleEl) subtitleEl.textContent = count + ' attached photos & videos (' + (eventData.event_date || '') + ')';

            if (gridEl) {
                gridEl.innerHTML = '';
                const mediaList = eventData.media_items || [];
                if (mediaList.length === 0) {
                    gridEl.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:36px; color:#94a3b8;"><i class="fas fa-photo-video" style="font-size:36px; margin-bottom:10px; display:block; color:#cbd5e1;"></i>No photos or videos uploaded for this event yet.<br><small style="color:#64748b;">Click "Upload More Details" below to attach media.</small></div>';
                } else {
                    mediaList.forEach(m => {
                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'media-mgmt-card';
                        const isVid = m.media_type === 'video';
                        let mediaPreviewHtml = '';
                        if (isVid) {
                            mediaPreviewHtml = '<div class="media-mgmt-preview"><video src="../' + m.file_path + '" muted preload="metadata"></video><span class="video-play-tag"><i class="fas fa-play"></i> Video</span></div>';
                        } else {
                            mediaPreviewHtml = '<div class="media-mgmt-preview"><img src="../' + m.file_path + '" alt=""></div>';
                        }
                        itemDiv.innerHTML = mediaPreviewHtml + 
                            '<div class="media-mgmt-info">' +
                                '<h5>' + escapeHtml(m.media_title || 'Untitled') + '</h5>' +
                                '<p>' + escapeHtml(m.media_description || 'No description provided.') + '</p>' +
                                '<div class="media-mgmt-actions">' +
                                    '<span style="font-size:11px; font-weight:600; color:' + (isVid ? '#2563eb' : '#059669') + ';">' + 
                                        (isVid ? '<i class="fas fa-video"></i> Video' : '<i class="fas fa-image"></i> Photo') + 
                                    '</span>' +
                                    '<form action="../backend/crud.php" method="POST" onsubmit="return confirm(\'Delete this media item?\');" style="display:inline;">' +
                                        '<input type="hidden" name="action" value="delete_event_media">' +
                                        '<input type="hidden" name="media_id" value="' + m.id + '">' +
                                        '<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">' +
                                        '<button type="submit" class="btn-delete-media-pill" title="Delete media"><i class="fas fa-trash-alt"></i> Delete</button>' +
                                    '</form>' +
                                '</div>' +
                            '</div>';
                        gridEl.appendChild(itemDiv);
                    });
                }
            }
            openModal('modalViewEventMedia');
        }

        function openMediaUploadFromView() {
            closeModal('modalViewEventMedia');
            if (currentViewingEvent) {
                openEventMediaModal(currentViewingEvent.id, currentViewingEvent.title);
            } else {
                openModal('modalEventMedia');
            }
        }

        function previewMediaUpload(input) {
            const container = document.getElementById('mediaUploadPreviewContainer');
            const placeholder = document.getElementById('mediaUploadPlaceholder');
            if (!container) return;
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                const isVid = file.type.startsWith('video');

                reader.onload = function(e) {
                    container.style.display = 'flex';
                    if (placeholder) placeholder.style.display = 'none';
                    if (isVid) {
                        container.innerHTML = '<video src="' + e.target.result + '" controls style="max-height:140px; max-width:220px; border-radius:8px; border:1px solid #cbd5e1;"></video><div style="text-align:left;"><div style="font-size:13px; font-weight:700; color:#0f172a;">' + escapeHtml(file.name) + '</div><div style="font-size:12px; color:#64748b;">' + (file.size/1024/1024).toFixed(2) + ' MB • Video</div><span style="display:inline-block; margin-top:6px; font-size:11.5px; color:#00b894; font-weight:600;"><i class="fas fa-check-circle"></i> Video selected. Click box to change</span></div>';
                    } else {
                        container.innerHTML = '<img src="' + e.target.result + '" style="max-height:140px; max-width:220px; border-radius:8px; object-fit:contain; border:1px solid #cbd5e1;"><div style="text-align:left;"><div style="font-size:13px; font-weight:700; color:#0f172a;">' + escapeHtml(file.name) + '</div><div style="font-size:12px; color:#64748b;">' + (file.size/1024/1024).toFixed(2) + ' MB • Image</div><span style="display:inline-block; margin-top:6px; font-size:11.5px; color:#00b894; font-weight:600;"><i class="fas fa-check-circle"></i> Image selected. Click box to change</span></div>';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
                if (placeholder) placeholder.style.display = 'flex';
            }
        }

        // --- 1. Event List Actions (View / Edit / Delete) ---
        let currentInspectedEvent = null;

        function viewEventDetails(eventData) {
            currentInspectedEvent = eventData;
            const titleEl = document.getElementById('viewEventDetailsTitle');
            const dateEl  = document.getElementById('viewEventDateText');
            const timeEl  = document.getElementById('viewEventTimeText');
            const venueEl = document.getElementById('viewEventVenueText');
            const descEl  = document.getElementById('viewEventDescText');
            const countEl = document.getElementById('viewEventFilesCount');
            const gridEl  = document.getElementById('viewEventAttachedGrid');

            if (titleEl) titleEl.textContent = eventData.title || 'Event Details';
            if (dateEl)  dateEl.textContent  = eventData.event_date || '';
            if (timeEl)  timeEl.textContent  = eventData.event_time || '10:00 AM';
            if (venueEl) venueEl.textContent = eventData.venue || 'Trinity Campus Auditorium';
            if (descEl)  descEl.textContent  = eventData.description || 'No detailed description provided.';

            const items = (eventData.media_items && Array.isArray(eventData.media_items)) ? eventData.media_items : [];
            if (countEl) countEl.textContent = items.length + ' attached file' + (items.length !== 1 ? 's' : '');

            if (gridEl) {
                gridEl.innerHTML = '';
                if (items.length === 0) {
                    gridEl.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:32px; color:#94a3b8;"><i class="fas fa-photo-video" style="font-size:32px; margin-bottom:8px; display:block; color:#cbd5e1;"></i>No photos or videos attached to this event yet.<br><small style="color:#64748b;">Click "Upload Files to this Event" below to attach media.</small></div>';
                } else {
                    items.forEach(m => {
                        const card = document.createElement('div');
                        card.className = 'media-mgmt-card';
                        const isVid = (m.media_type === 'video');
                        let previewHtml = '';
                        if (isVid) {
                            previewHtml = '<div class="media-mgmt-preview"><video src="../' + m.file_path + '" muted preload="metadata"></video><span class="video-play-tag"><i class="fas fa-play"></i> Video</span></div>';
                        } else {
                            previewHtml = '<div class="media-mgmt-preview"><img src="../' + m.file_path + '" alt=""></div>';
                        }
                        card.innerHTML = previewHtml +
                            '<div class="media-mgmt-info">' +
                                '<h5>' + escapeHtml(m.media_title || 'Untitled') + '</h5>' +
                                '<p>' + escapeHtml(m.media_description || 'No description.') + '</p>' +
                                '<div class="media-mgmt-actions">' +
                                    '<span style="font-size:11px; font-weight:600; color:' + (isVid ? '#2563eb' : '#059669') + ';">' +
                                        (isVid ? '<i class="fas fa-video"></i> Video' : '<i class="fas fa-image"></i> Image') +
                                    '</span>' +
                                    '<form action="../backend/crud.php" method="POST" onsubmit="return confirm(\'Delete this media file?\');" style="display:inline;">' +
                                        '<input type="hidden" name="action" value="delete_event_media">' +
                                        '<input type="hidden" name="media_id" value="' + m.id + '">' +
                                        '<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">' +
                                        '<button type="submit" class="btn-delete-media-pill" title="Delete file"><i class="fas fa-trash-alt"></i> Delete</button>' +
                                    '</form>' +
                                '</div>' +
                            '</div>';
                        gridEl.appendChild(card);
                    });
                }
            }
            openModal('modalViewEvent');
        }

        function quickUploadForViewingEvent() {
            closeModal('modalViewEvent');
            if (currentInspectedEvent && currentInspectedEvent.id) {
                window.location.href = '?tab=event_files&event_id=' + currentInspectedEvent.id;
            } else {
                window.location.href = '?tab=event_files';
            }
        }

        function openEditEventModal(eventData) {
            document.getElementById('edit_event_id').value = eventData.id || '';
            document.getElementById('edit_event_title').value = eventData.title || '';
            document.getElementById('edit_event_date').value = eventData.event_date || '';
            document.getElementById('edit_event_time').value = eventData.event_time || '10:00 AM';
            document.getElementById('edit_event_venue').value = eventData.venue || '';
            document.getElementById('edit_event_description').value = eventData.description || '';
            openModal('modalEditEvent');
        }

        // --- 2. Upload Event Files Actions (Edit / Delete / Preview) ---
        function openEditMediaModal(mediaData) {
            document.getElementById('edit_media_id').value = mediaData.id || '';
            document.getElementById('edit_media_title').value = mediaData.media_title || '';
            document.getElementById('edit_media_description').value = mediaData.media_description || '';
            const selectEl = document.getElementById('edit_media_event_id');
            if (selectEl && mediaData.event_id) {
                selectEl.value = mediaData.event_id;
            }
            openModal('modalEditEventMedia');
        }

        function previewInlineUpload(input) {
            const container = document.getElementById('inlineUploadPreviewContainer');
            if (!container) return;
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                const isVid = file.type.startsWith('video');

                reader.onload = function(e) {
                    container.style.display = 'block';
                    if (isVid) {
                        container.innerHTML = '<video src="' + e.target.result + '" controls style="max-height:160px; max-width:100%; border-radius:8px;"></video><div style="font-size:12px; color:#64748b; margin-top:4px;">' + escapeHtml(file.name) + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)</div>';
                    } else {
                        container.innerHTML = '<img src="' + e.target.result + '" style="max-height:160px; max-width:100%; border-radius:8px; object-fit:contain;"><div style="font-size:12px; color:#64748b; margin-top:4px;">' + escapeHtml(file.name) + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)</div>';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
            }
        }

        // --- 3. News Management Actions (Preview / View / Edit) ---
        function previewNewsUpload(input) {
            const placeholder = document.getElementById('news_upload_placeholder');
            const preview     = document.getElementById('news_upload_preview');
            const previewImg  = document.getElementById('news_upload_preview_img');
            const previewName = document.getElementById('news_upload_preview_name');
            const previewSize = document.getElementById('news_upload_preview_size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) previewImg.src = e.target.result;
                    if (previewName) previewName.textContent = file.name;
                    if (previewSize) previewSize.textContent = (file.size / 1024).toFixed(1) + ' KB (' + (file.type || 'image') + ')';
                    if (placeholder) placeholder.style.display = 'none';
                    if (preview) preview.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            } else {
                if (placeholder) placeholder.style.display = 'flex';
                if (preview) preview.style.display = 'none';
            }
        }

        function openViewNewsModal(newsData) {
            const modalImg    = document.getElementById('view_news_modal_img');
            const modalTitle  = document.getElementById('view_news_modal_title');
            const modalDate   = document.getElementById('view_news_modal_date');
            const modalDesc   = document.getElementById('view_news_modal_desc');
            const modalSource = document.getElementById('view_news_modal_source');

            let rawPath = newsData.image_path || 'assets/Gallery/paper1.jpg';
            let imgSrc = '../' + rawPath.replace(/^\/+/, '');
            if (modalImg) {
                modalImg.src = imgSrc;
                modalImg.alt = newsData.title || 'Newspaper Clipping';
            }
            if (modalTitle) modalTitle.textContent = newsData.title || 'Newspaper Clipping';
            if (modalDate) {
                modalDate.textContent = newsData.publish_date || '';
            }
            if (modalDesc) {
                modalDesc.textContent = newsData.description || (newsData.summary || 'No detailed description provided.');
            }
            if (modalSource) {
                modalSource.textContent = newsData.source || 'Press & Media';
            }

            openModal('modalViewNews');
        }

        function openEditNewsModal(newsData) {
            document.getElementById('edit_news_id').value = newsData.id || '';
            document.getElementById('edit_news_title').value = newsData.title || '';
            document.getElementById('edit_news_date').value = newsData.publish_date || '';
            document.getElementById('edit_news_desc').value = newsData.description || (newsData.summary || '');

            const currImg = document.getElementById('edit_news_current_img');
            let rawPath = newsData.image_path || 'assets/Gallery/paper1.jpg';
            let imgSrc = '../' + rawPath.replace(/^\/+/, '');
            if (currImg) currImg.src = imgSrc;

            const newPreviewBox = document.getElementById('edit_news_new_preview_box');
            if (newPreviewBox) newPreviewBox.style.display = 'none';
            const fileInput = document.getElementById('edit_news_file_input');
            if (fileInput) fileInput.value = '';

            openModal('modalEditNews');
        }

        function previewEditNewsImage(input) {
            const previewContainer = document.getElementById('edit_news_new_preview_box');
            const previewImg       = document.getElementById('edit_news_new_img');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) previewImg.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            } else {
                if (previewContainer) previewContainer.style.display = 'none';
            }
        }

        // --- 4. Circulars & Notifications Actions ---
        function previewCircularUpload(input) {
            const placeholder = document.getElementById('circular_upload_placeholder');
            const preview     = document.getElementById('circular_upload_preview');
            const iconEl      = document.getElementById('circular_preview_icon');
            const nameEl      = document.getElementById('circular_preview_name');
            const sizeEl      = document.getElementById('circular_preview_size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const name = file.name;
                const ext  = name.split('.').pop().toLowerCase();
                const size = (file.size >= 1048576) ? (file.size / 1048576).toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB';

                if (nameEl) nameEl.textContent = name;
                if (sizeEl) sizeEl.textContent = size + ' (' + ext.toUpperCase() + ')';

                if (iconEl) {
                    iconEl.className = 'circular-doc-icon-large';
                    if (ext === 'pdf') {
                        iconEl.classList.add('circular-doc-icon-pdf');
                        iconEl.innerHTML = '<i class="fas fa-file-pdf"></i>';
                    } else if (ext === 'doc' || ext === 'docx') {
                        iconEl.classList.add('circular-doc-icon-docx');
                        iconEl.innerHTML = '<i class="fas fa-file-word"></i>';
                    } else if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                        iconEl.classList.add('circular-doc-icon-image');
                        iconEl.innerHTML = '<i class="fas fa-file-image"></i>';
                    } else {
                        iconEl.innerHTML = '<i class="fas fa-file-alt"></i>';
                    }
                }

                if (placeholder) placeholder.style.display = 'none';
                if (preview) preview.style.display = 'flex';
            } else {
                resetCircularUpload();
            }
        }

        function resetCircularUpload() {
            const input       = document.getElementById('circular_upload_input');
            const placeholder = document.getElementById('circular_upload_placeholder');
            const preview     = document.getElementById('circular_upload_preview');
            if (input) input.value = '';
            if (placeholder) placeholder.style.display = 'flex';
            if (preview) preview.style.display = 'none';
        }

        function previewModalCircularUpload(input) {
            const placeholder = document.getElementById('modal_circular_upload_placeholder');
            const preview     = document.getElementById('modal_circular_upload_preview');
            const iconEl      = document.getElementById('modal_circular_preview_icon');
            const nameEl      = document.getElementById('modal_circular_preview_name');
            const sizeEl      = document.getElementById('modal_circular_preview_size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const name = file.name;
                const ext  = name.split('.').pop().toLowerCase();
                const size = (file.size >= 1048576) ? (file.size / 1048576).toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB';

                if (nameEl) nameEl.textContent = name;
                if (sizeEl) sizeEl.textContent = size + ' (' + ext.toUpperCase() + ')';

                if (iconEl) {
                    iconEl.className = 'circular-doc-icon-large';
                    if (ext === 'pdf') {
                        iconEl.classList.add('circular-doc-icon-pdf');
                        iconEl.innerHTML = '<i class="fas fa-file-pdf"></i>';
                    } else if (ext === 'doc' || ext === 'docx') {
                        iconEl.classList.add('circular-doc-icon-docx');
                        iconEl.innerHTML = '<i class="fas fa-file-word"></i>';
                    } else if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                        iconEl.classList.add('circular-doc-icon-image');
                        iconEl.innerHTML = '<i class="fas fa-file-image"></i>';
                    } else {
                        iconEl.innerHTML = '<i class="fas fa-file-alt"></i>';
                    }
                }

                if (placeholder) placeholder.style.display = 'none';
                if (preview) preview.style.display = 'flex';
            } else {
                resetModalCircularUpload();
            }
        }

        function resetModalCircularUpload() {
            const input       = document.getElementById('modal_circular_upload_input');
            const placeholder = document.getElementById('modal_circular_upload_placeholder');
            const preview     = document.getElementById('modal_circular_upload_preview');
            if (input) input.value = '';
            if (placeholder) placeholder.style.display = 'flex';
            if (preview) preview.style.display = 'none';
        }

        function openViewCircularModal(cir) {
            const titleEl     = document.getElementById('view_cir_modal_title');
            const dateEl      = document.getElementById('view_cir_modal_date');
            const badgeEl     = document.getElementById('view_cir_modal_badge');
            const descEl      = document.getElementById('view_cir_modal_desc');
            const containerEl = document.getElementById('view_cir_attachment_container');

            if (titleEl) titleEl.textContent = cir.title || 'Official Circular';
            if (dateEl) dateEl.textContent = cir.publish_date || '';

            let rawFile = cir.attachment_path || (cir.file_path || '');
            let fileType = (cir.attachment_type || '').toLowerCase();
            if (!fileType || fileType === 'none') {
                if (rawFile) {
                    let ext = rawFile.split('.').pop().toLowerCase();
                    if (ext === 'pdf') fileType = 'pdf';
                    else if (['doc', 'docx'].includes(ext)) fileType = 'docx';
                    else if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) fileType = 'image';
                }
            }

            if (badgeEl) {
                if (fileType === 'pdf') {
                    badgeEl.innerHTML = '<span class="badge-file-type badge-pdf"><i class="fas fa-file-pdf"></i> PDF</span>';
                } else if (fileType === 'docx') {
                    badgeEl.innerHTML = '<span class="badge-file-type badge-docx"><i class="fas fa-file-word"></i> DOCX</span>';
                } else if (fileType === 'image') {
                    badgeEl.innerHTML = '<span class="badge-file-type badge-image"><i class="fas fa-file-image"></i> IMAGE</span>';
                } else {
                    badgeEl.innerHTML = '<span class="badge-file-type badge-none"><i class="fas fa-align-left"></i> TEXT</span>';
                }
            }

            if (descEl) {
                descEl.textContent = cir.description || 'No detailed description provided for this circular.';
            }

            if (containerEl) {
                containerEl.innerHTML = '';
                if (rawFile) {
                    let fullPath = '../' + rawFile.replace(/^\/+/, '');
                    if (fileType === 'image') {
                        containerEl.innerHTML = 
                            '<div style="text-align:center;">' +
                                '<img src="' + escapeHtml(fullPath) + '" alt="' + escapeHtml(cir.title) + '" style="max-height:360px; max-width:100%; border-radius:10px; box-shadow:0 4px 14px rgba(0,0,0,0.1); object-fit:contain;">' +
                                '<div style="margin-top:10px;">' +
                                    '<a href="' + escapeHtml(fullPath) + '" target="_blank" class="topbar-action-btn" style="font-size:12px;">' +
                                        '<i class="fas fa-external-link-alt"></i> View Full Image in New Tab' +
                                    '</a>' +
                                '</div>' +
                            '</div>';
                    } else if (fileType === 'pdf') {
                        containerEl.innerHTML = 
                            '<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; background:#ffffff; border:1.5px solid #fecdd3; border-radius:12px; padding:18px 24px;">' +
                                '<div style="display:flex; align-items:center; gap:16px;">' +
                                    '<div class="circular-doc-icon-large circular-doc-icon-pdf" style="width:52px; height:52px; font-size:24px;">' +
                                        '<i class="fas fa-file-pdf"></i>' +
                                    '</div>' +
                                    '<div>' +
                                        '<div style="font-weight:700; font-size:14px; color:#1e293b;">Official PDF Document Attached</div>' +
                                        '<div style="font-size:12px; color:#64748b; margin-top:2px;">' + escapeHtml(rawFile.split('/').pop()) + '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<a href="' + escapeHtml(fullPath) + '" target="_blank" class="btn-emerald-pill" style="padding:9px 20px; font-size:13px;">' +
                                    '<i class="fas fa-eye"></i> View PDF' +
                                '</a>' +
                            '</div>';
                    } else if (fileType === 'docx') {
                        containerEl.innerHTML = 
                            '<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; background:#ffffff; border:1.5px solid #c7d2fe; border-radius:12px; padding:18px 24px;">' +
                                '<div style="display:flex; align-items:center; gap:16px;">' +
                                    '<div class="circular-doc-icon-large circular-doc-icon-docx" style="width:52px; height:52px; font-size:24px;">' +
                                        '<i class="fas fa-file-word"></i>' +
                                    '</div>' +
                                    '<div>' +
                                        '<div style="font-weight:700; font-size:14px; color:#1e293b;">Word Document (.DOCX) Attached</div>' +
                                        '<div style="font-size:12px; color:#64748b; margin-top:2px;">' + escapeHtml(rawFile.split('/').pop()) + '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<a href="' + escapeHtml(fullPath) + '" download class="btn-emerald-pill" style="padding:9px 20px; font-size:13px;">' +
                                    '<i class="fas fa-download"></i> Download DOCX' +
                                '</a>' +
                            '</div>';
                    } else {
                        containerEl.innerHTML = 
                            '<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">' +
                                '<div style="font-size:13px; color:#475569;"><i class="fas fa-paperclip"></i> ' + escapeHtml(rawFile) + '</div>' +
                                '<a href="' + escapeHtml(fullPath) + '" target="_blank" class="topbar-action-btn">Open File</a>' +
                            '</div>';
                    }
                } else {
                    containerEl.innerHTML = 
                        '<div style="text-align:center; padding:14px; color:#64748b; font-size:13px;">' +
                            '<i class="fas fa-info-circle"></i> This is a text circular notice without file attachment.' +
                        '</div>';
                }
            }

            openModal('modalViewCircular');
        }

        function openEditCircularModal(cir) {
            document.getElementById('edit_cir_id').value = cir.id || '';
            document.getElementById('edit_cir_title').value = cir.title || '';
            document.getElementById('edit_cir_date').value = cir.publish_date || '';
            document.getElementById('edit_cir_desc').value = cir.description || '';

            const marqBox = document.getElementById('edit_cir_is_marquee');
            if (marqBox) marqBox.checked = (cir.is_marquee == 1);

            const fileWrap = document.getElementById('edit_cir_current_file_wrap');
            let rawFile = cir.attachment_path || (cir.file_path || '');
            let fileType = (cir.attachment_type || '').toLowerCase();

            if (fileWrap) {
                if (rawFile) {
                    let fullPath = '../' + rawFile.replace(/^\/+/, '');
                    fileWrap.innerHTML = 
                        '<div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">' +
                            '<div style="display:flex; align-items:center; gap:8px;">' +
                                '<span class="badge-file-type badge-' + (fileType === 'pdf' ? 'pdf' : (fileType === 'docx' ? 'docx' : 'image')) + '">' +
                                    (fileType.toUpperCase() || 'FILE') +
                                '</span>' +
                                '<span style="font-size:12.5px; color:#334155; font-weight:600;">' + escapeHtml(rawFile.split('/').pop()) + '</span>' +
                            '</div>' +
                            '<a href="' + escapeHtml(fullPath) + '" target="_blank" class="topbar-action-btn" style="font-size:11px; padding:3px 10px;">' +
                                '<i class="fas fa-external-link-alt"></i> View File' +
                            '</a>' +
                        '</div>';
                } else {
                    fileWrap.innerHTML = '<span style="font-size:12px; color:#94a3b8; font-style:italic;">No file currently attached.</span>';
                }
            }

            const fileInput = document.getElementById('edit_cir_file_input');
            if (fileInput) fileInput.value = '';

            openModal('modalEditCircular');
        }

        function filterCircularsTable(query) {
            const q = query.toLowerCase().trim();
            const rows = document.querySelectorAll('.circular-row');
            rows.forEach(r => {
                const title = r.getAttribute('data-title') || r.textContent.toLowerCase();
                r.style.display = title.includes(q) ? '' : 'none';
            });
        }

        function filterEventFilesTable(query) {
            const q = (query || '').toLowerCase().trim();
            const table = document.getElementById('eventFilesTable');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        function filterNewsTable(query) {
            const q = (query || '').toLowerCase().trim();
            const table = document.getElementById('newsTable');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        function filterLogsTable(query) {
            const q = (query || '').toLowerCase().trim();
            const table = document.getElementById('activityLogsTable');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // Auto-open Event Media upload modal if event_id is specified in URL query
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'event_files' && urlParams.get('event_id')) {
                openModal('modalEventMedia');
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</body>
</html>
