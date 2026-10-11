<?php
/**
 * Dynamic Bulletin List Snippet for Live Sync & index.php
 */
if (empty($live_notices)) {
    @include_once __DIR__ . '/../crud.php';
    if (function_exists('get_notifications')) {
        $live_notices = get_notifications(8, false);
    }
}
?>
<?php if (!empty($live_notices)): ?>
    <?php foreach ($live_notices as $notice): 
        $filePath = !empty($notice['attachment_path']) ? $notice['attachment_path'] : (!empty($notice['file_path']) ? $notice['file_path'] : '');
        $link = !empty($filePath) ? htmlspecialchars($filePath) : (!empty($notice['link_url']) ? htmlspecialchars($notice['link_url']) : '#');
        $target = (!empty($filePath) || !empty($notice['link_url'])) ? 'target="_blank"' : '';
        
        $fileType = strtolower($notice['attachment_type'] ?? '');
        if (empty($fileType) || $fileType === 'none') {
            if (!empty($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if ($ext === 'pdf') $fileType = 'pdf';
                elseif (in_array($ext, ['doc', 'docx'])) $fileType = 'docx';
                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $fileType = 'image';
                else $fileType = 'doc';
            }
        }
        
        $badge = !empty($fileType) && $fileType !== 'none' ? strtoupper($fileType) : ($notice['badge'] ?? 'NOTICE');
    ?>
        <a href="<?php echo $link; ?>" <?php echo $target; ?> class="bulletin-item-card">
            <div class="bulletin-date-badge theme-<?php echo strtolower($badge); ?>">
                <span class="date-month"><?php echo $badge; ?></span>
                <span class="date-day"><?php echo date('d M', strtotime($notice['publish_date'] ?? date('Y-m-d'))); ?></span>
            </div>
            <div class="bulletin-content">
                <span class="bulletin-category cat-<?php echo strtolower($notice['category'] ?? 'circular'); ?>"><?php echo htmlspecialchars($notice['category'] ?? 'Circular'); ?></span>
                <h5><?php echo htmlspecialchars($notice['title']); ?></h5>
                <?php if (!empty($notice['description'])): ?>
                    <p><?php echo htmlspecialchars($notice['description']); ?></p>
                <?php endif; ?>
                <span class="bulletin-link-text">
                    <?php 
                    if (!empty($filePath)) {
                        if ($fileType === 'pdf') echo 'View PDF Document <i class="fas fa-file-pdf"></i>';
                        elseif ($fileType === 'docx') echo 'Download DOCX Circular <i class="fas fa-file-word"></i>';
                        elseif ($fileType === 'image') echo 'View Attached Image <i class="fas fa-file-image"></i>';
                        else echo 'View Attached File <i class="fas fa-paperclip"></i>';
                    } else {
                        echo 'Read Circular Notice <i class="fas fa-arrow-right"></i>';
                    }
                    ?>
                </span>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <div style="text-align: center; padding: 30px; color: #94a3b8;">
        <i class="far fa-bell" style="font-size: 28px; margin-bottom: 8px; display: block;"></i>
        No active notifications published at this moment.
    </div>
<?php endif; ?>
