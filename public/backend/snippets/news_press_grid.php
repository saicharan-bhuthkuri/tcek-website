<?php
/**
 * Dynamic News & Press Grid Snippet for Live Sync & index.php
 */
if (empty($live_news)) {
    @include_once __DIR__ . '/../crud.php';
    if (function_exists('get_news')) {
        $live_news = get_news(6);
    }
}
?>
<?php if (!empty($live_news)): ?>
    <?php foreach ($live_news as $nws_item): 
        $raw_path   = !empty($nws_item['image_path']) ? $nws_item['image_path'] : 'assets/Gallery/paper1.jpg';
        $img_url    = htmlspecialchars($raw_path);
        $nws_title  = htmlspecialchars($nws_item['title']);
        $nws_source = !empty($nws_item['source']) ? htmlspecialchars($nws_item['source']) : 'Press Coverage';
        $nws_date   = !empty($nws_item['publish_date']) ? date('d M Y', strtotime($nws_item['publish_date'])) : '';
        $nws_desc   = !empty($nws_item['description']) ? $nws_item['description'] : ($nws_item['summary'] ?? '');
        $caption    = $nws_source . ': ' . $nws_title . ($nws_date ? ' (' . $nws_date . ')' : '');
    ?>
        <div class="press-card" onclick="openNewsLightbox('<?php echo $img_url; ?>', '<?php echo addslashes($caption); ?>', '<?php echo addslashes($nws_desc); ?>')">
            <div class="press-thumb-wrap">
                <img src="<?php echo $img_url; ?>" alt="<?php echo $nws_title; ?>" loading="lazy" onerror="this.src='assets/Gallery/paper1.jpg'">
                <div class="press-overlay-badge">
                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                </div>
            </div>
            <div class="press-info">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <span class="press-source"><?php echo $nws_source; ?></span>
                    <?php if (!empty($nws_date)): ?>
                        <span style="font-size:11px; color:#64748b;"><i class="far fa-calendar-alt"></i> <?php echo $nws_date; ?></span>
                    <?php endif; ?>
                </div>
                <h6 class="press-headline"><?php echo $nws_title; ?></h6>
                <?php if (!empty($nws_desc)): ?>
                    <p style="font-size:12px; color:#64748b; margin:6px 0 0; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                        <?php echo htmlspecialchars($nws_desc); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
        <i class="fas fa-newspaper" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
        No newspaper clippings published yet.
    </div>
<?php endif; ?>
