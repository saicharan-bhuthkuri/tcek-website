<?php
/**
 * Dynamic Gallery Grid Snippet
 * Renders all gallery media and press clippings dynamically from database / JSON store
 */
if (empty($dyn_photos)) {
    @include_once __DIR__ . '/../crud.php';
    if (function_exists('get_gallery_items')) {
        $dyn_photos = get_gallery_items(null, null, 100);
    } else {
        $dyn_photos = [];
    }
}
if (!isset($dyn_news)) {
    @include_once __DIR__ . '/../crud.php';
    $dyn_news = function_exists('get_news') ? get_news(50) : [];
}
?>
<?php if (!empty($dyn_photos)): ?>
    <?php foreach ($dyn_photos as $dp): 
        $cat = htmlspecialchars($dp['category'] ?? 'events');
        $title = htmlspecialchars($dp['title'] ?? 'Campus Moment');
        $path = htmlspecialchars($dp['file_path'] ?? '');
        $badgeClass = 'badge-event';
        $iconClass  = 'fas fa-calendar-alt';
        if ($cat === 'milestones') {
            $badgeClass = 'badge-milestone';
            $iconClass  = 'fas fa-award';
        } elseif ($cat === 'campus') {
            $badgeClass = 'badge-campus';
            $iconClass  = 'fas fa-university';
        }
    ?>
        <div class="gallery-modern-card" data-category="<?php echo $cat; ?>" onclick="openGalleryModal(this)">
            <div class="gallery-card-thumb">
                <span class="gallery-card-badge <?php echo $badgeClass; ?>">
                    <i class="<?php echo $iconClass; ?>"></i> <?php echo ucfirst($cat); ?>
                </span>
                <img src="<?php echo $path; ?>" alt="<?php echo $title; ?>">
                <div class="gallery-card-overlay">
                    <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Press Clippings from News Engine -->
<?php if (!empty($dyn_news)): ?>
    <?php foreach ($dyn_news as $p_item): 
        $p_raw = !empty($p_item['image_path']) ? $p_item['image_path'] : 'assets/Gallery/paper1.jpg';
        $p_title = htmlspecialchars($p_item['title'] ?? 'Press Clipping');
        $p_source = !empty($p_item['source']) ? htmlspecialchars($p_item['source']) : 'Press';
    ?>
        <div class="gallery-modern-card category-press" data-category="press" onclick="openGalleryModal(this)">
            <div class="gallery-card-thumb">
                <span class="gallery-card-badge badge-press"><i class="fas fa-newspaper"></i> <?php echo $p_source; ?></span>
                <img src="<?php echo htmlspecialchars($p_raw); ?>" alt="<?php echo $p_title; ?>" onerror="this.src='assets/Gallery/paper1.jpg'">
                <div class="gallery-card-overlay">
                    <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> View Article</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div id="gallery-empty-state" class="gallery-empty-state" style="display: none;">
    <div class="gallery-empty-icon"><i class="fas fa-newspaper"></i></div>
    <h4>No Photos Currently Listed</h4>
    <p>No photos found for this category.</p>
</div>
