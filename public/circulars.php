<!DOCTYPE html>
<html lang="en">

<head>
    <title>Circulars &amp; Notifications - Trinity College of Engineering &amp; Technology</title>
    <?php include 'head.php'; ?>
    <style>
        /* Standalone Circulars & Notifications Portal */
        .circulars-section {
            padding: 40px 0 80px;
            background: #f8fafc;
            min-height: calc(100vh - 450px);
        }

        .circulars-container {
            max-width: 1440px;
            width: 95%;
            margin: 0 auto;
            padding: 0 10px;
        }

        .page-header {
            background: linear-gradient(135deg, #00b894 0%, #009375 100%);
            padding: 70px 20px 35px;
            text-align: center;
            color: #fff;
            box-shadow: 0 4px 20px rgba(0, 184, 148, 0.15);
        }

        .page-header h1 {
            font-size: 38px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .page-header p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.92);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Top Action Bar */
        .circulars-action-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
            background: #ffffff;
            padding: 18px 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .circulars-action-title h2 {
            margin: 0 0 4px 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
        }

        .circulars-badge-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            padding: 4px 12px;
            border-radius: 999px;
            border: 1px solid #a7f3d0;
        }

        .circular-search-box {
            position: relative;
            max-width: 360px;
            width: 100%;
        }

        .circular-search-box input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: 1px solid #cbd5e1;
            border-radius: 30px;
            font-size: 14px;
            outline: none;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            background: #f8fafc;
        }

        .circular-search-box input:focus {
            border-color: #00b894;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.15);
        }

        .circular-search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        /* Full Width Table Card Container */
        .circulars-table-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            width: 100%;
        }

        .circulars-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            margin: 0;
        }

        .circulars-table thead tr {
            background: linear-gradient(135deg, #00b894 0%, #009375 100%);
        }

        .circulars-table th {
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            border: none;
            padding: 16px 20px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .circulars-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .circulars-table tbody tr:last-child {
            border-bottom: none;
        }

        .circulars-table tbody tr:hover {
            background: #f8fafc;
        }

        .circulars-table td {
            background: transparent;
            padding: 18px 20px;
            vertical-align: middle;
            color: #2d3436;
            font-size: 14.5px;
            white-space: normal !important; /* CRITICAL: Allows natural wrap of text */
        }

        /* Explicit Column Widths for Perfect Full View */
        .col-sno {
            width: 70px;
            text-align: center;
            font-weight: 800;
            color: #0f172a;
            font-size: 15px;
        }

        .col-title {
            width: auto;
            min-width: 320px;
            text-align: left;
        }

        .col-title .title-head {
            font-weight: 700;
            color: #0f172a;
            font-size: 15.5px;
            margin-bottom: 5px;
            line-height: 1.45;
            word-break: break-word;
        }

        .col-title .desc-text {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            word-break: break-word;
        }

        .col-date {
            width: 140px;
            text-align: center;
            white-space: nowrap !important;
        }

        .result-date-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            color: #15803d;
            font-weight: 700;
            font-size: 13px;
            white-space: nowrap;
        }

        .col-type {
            width: 130px;
            text-align: center;
            white-space: nowrap !important;
        }

        .badge-file-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            text-decoration: none;
        }

        .badge-file-type.badge-pdf {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .badge-file-type.badge-docx {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #a5b4fc;
        }

        .badge-file-type.badge-image {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .badge-file-type.badge-none {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .col-action {
            width: 155px;
            text-align: center;
            white-space: nowrap !important;
        }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: #00b894;
            color: #fff;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 184, 148, 0.2);
            white-space: nowrap;
        }

        .view-btn:hover {
            background: #008f72;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 184, 148, 0.3);
            color: #fff;
        }

        /* Responsive Mobile Layout */
        @media (max-width: 900px) {
            .circulars-table,
            .circulars-table thead,
            .circulars-table tbody,
            .circulars-table th,
            .circulars-table td,
            .circulars-table tr {
                display: block;
            }

            .circulars-table thead {
                display: none;
            }

            .circulars-table tbody tr {
                padding: 16px;
                border-bottom: 2px solid #e2e8f0;
            }

            .circulars-table td {
                padding: 8px 0;
                width: 100% !important;
                text-align: left !important;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .col-title {
                display: block !important;
            }

            .col-sno::before { content: "S.No: "; font-weight: 700; color: #64748b; }
            .col-date::before { content: "Published: "; font-weight: 700; color: #64748b; }
            .col-type::before { content: "File Type: "; font-weight: 700; color: #64748b; }
            .col-action::before { content: "Action: "; font-weight: 700; color: #64748b; }
        }
    </style>
</head>

<body>

    <?php 
    $page = 'circulars'; 
    include 'header.php'; 
    
    // Dynamic Circulars & Notifications from backend
    @include_once __DIR__ . '/backend/config/database.php';
    @include_once __DIR__ . '/backend/crud.php';
    $circulars_list = function_exists('get_notifications') ? get_notifications(100, false) : [];
    ?>

    <!-- Main Content -->
    <main>
        <!-- Page Header -->
        <section class="page-header">
            <h1>Circulars &amp; Notifications</h1>
            <p>Official College Circulars, Academic Notifications &amp; Administrative Announcements</p>
        </section>

        <section class="circulars-section">
            <div class="circulars-container">

                <!-- Action Bar with Live Counter & Filter -->
                <div class="circulars-action-bar">
                    <div class="circulars-action-title">
                        <h2>Official Circulars &amp; Notifications</h2>
                        <span class="circulars-badge-count">
                            <i class="fas fa-bolt"></i> Live Official Feed (<?php echo count($circulars_list); ?> Circulars)
                        </span>
                    </div>

                    <div class="circular-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="circularSearchInput" placeholder="Filter circulars by title..." onkeyup="filterCirculars()">
                    </div>
                </div>

                <!-- Full-Width Responsive Table Card -->
                <div class="circulars-table-card">
                    <table class="circulars-table" id="circularsTable">
                        <thead>
                            <tr>
                                <th class="col-sno">S.No.</th>
                                <th class="col-title">Circular Title &amp; Details</th>
                                <th class="col-date">Date</th>
                                <th class="col-type">File Type</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($circulars_list)): ?>
                                <?php foreach ($circulars_list as $idx => $cir): 
                                    $filePath = !empty($cir['attachment_path']) ? $cir['attachment_path'] : (!empty($cir['file_path']) ? $cir['file_path'] : '');
                                    $link     = !empty($filePath) ? htmlspecialchars($filePath) : (!empty($cir['link_url']) ? htmlspecialchars($cir['link_url']) : '#');
                                    $target   = (!empty($filePath) || !empty($cir['link_url'])) ? 'target="_blank"' : '';
                                    
                                    $fileType = strtolower($cir['attachment_type'] ?? '');
                                    if (empty($fileType) || $fileType === 'none') {
                                        if (!empty($filePath)) {
                                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                            if ($ext === 'pdf') $fileType = 'pdf';
                                            elseif (in_array($ext, ['doc', 'docx'])) $fileType = 'docx';
                                            elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $fileType = 'image';
                                            else $fileType = 'doc';
                                        }
                                    }
                                    $pubDate = !empty($cir['publish_date']) ? date('d-m-Y', strtotime($cir['publish_date'])) : date('d-m-Y');
                                ?>
                                    <tr class="circular-row">
                                        <td class="col-sno" data-label="S.No."><?php echo $idx + 1; ?></td>
                                        <td class="col-title" data-label="Circular Title">
                                            <div class="title-head">
                                                <?php echo htmlspecialchars($cir['title']); ?>
                                            </div>
                                            <?php if (!empty($cir['description'])): ?>
                                                <div class="desc-text">
                                                    <?php echo htmlspecialchars($cir['description']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="col-date" data-label="Date">
                                            <span class="result-date-pill">
                                                <i class="far fa-calendar-alt"></i><?php echo $pubDate; ?>
                                            </span>
                                        </td>
                                        <td class="col-type" data-label="File Type">
                                            <?php if ($fileType === 'pdf'): ?>
                                                <span class="badge-file-type badge-pdf"><i class="fas fa-file-pdf"></i> PDF</span>
                                            <?php elseif ($fileType === 'docx'): ?>
                                                <span class="badge-file-type badge-docx"><i class="fas fa-file-word"></i> DOCX</span>
                                            <?php elseif ($fileType === 'image'): ?>
                                                <span class="badge-file-type badge-image"><i class="fas fa-file-image"></i> IMAGE</span>
                                            <?php else: ?>
                                                <span class="badge-file-type badge-none"><i class="fas fa-bullhorn"></i> NOTICE</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="col-action" data-label="Action">
                                            <?php if (!empty($filePath)): ?>
                                                <a href="<?php echo $link; ?>" <?php echo $target; ?> class="view-btn">
                                                    <?php 
                                                    if ($fileType === 'pdf') echo '<i class="fas fa-file-pdf"></i> View PDF';
                                                    elseif ($fileType === 'docx') echo '<i class="fas fa-download"></i> Download';
                                                    elseif ($fileType === 'image') echo '<i class="fas fa-image"></i> View Image';
                                                    else echo '<i class="fas fa-paperclip"></i> View File';
                                                    ?>
                                                </a>
                                            <?php elseif (!empty($cir['link_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($cir['link_url']); ?>" class="view-btn">
                                                    <i class="fas fa-external-link-alt"></i> Open Link
                                                </a>
                                            <?php else: ?>
                                                <span style="font-size:12.5px; color:#94a3b8; font-style:italic;">No Document</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:40px; color:#64748b;">
                                        No circulars published at this time.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>

    <script>
    function filterCirculars() {
        var query = document.getElementById('circularSearchInput').value.toLowerCase();
        var rows = document.querySelectorAll('#circularsTable tbody tr.circular-row');
        rows.forEach(function(row) {
            var text = row.innerText.toLowerCase();
            row.style.display = text.indexOf(query) > -1 ? '' : 'none';
        });
    }
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
