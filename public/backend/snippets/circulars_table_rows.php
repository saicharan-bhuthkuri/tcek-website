<?php
/**
 * TCEK Circulars & Notifications Table Rows Snippet
 * Included by circulars.php and served via sync.php?action=get_section&section=circulars_table
 */
if (!isset($circulars_list)) {
    if (!function_exists('get_notifications')) {
        @include_once __DIR__ . '/../crud.php';
    }
    $circulars_list = function_exists('get_notifications') ? get_notifications(100, false) : [];
}

if (!empty($circulars_list)):
    foreach ($circulars_list as $idx => $cir): 
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
<?php 
    endforeach;
else: 
?>
    <tr>
        <td colspan="5" style="text-align:center; padding:40px; color:#64748b;">
            No circulars published at this time.
        </td>
    </tr>
<?php endif; ?>
