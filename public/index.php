<!DOCTYPE html>
<html lang="en">

<head>
    <title>Trinity College of Engineering & Technology - Peddapalli</title>
    <?php include 'head.php'; ?>
</head>

<body>

    <?php 
    $page = 'home';
    include 'header.php'; 
    // Dynamic notifications & marquee ticker from GoDaddy MySQL backend
    @include_once __DIR__ . '/backend/config/database.php';
    @include_once __DIR__ . '/backend/crud.php';
    @include_once __DIR__ . '/backend/hero_crud.php';
    @include_once __DIR__ . '/backend/rnd_crud.php';
    $live_notices    = function_exists('get_notifications') ? get_notifications(5, false) : [];
    $marquee_notices = function_exists('get_notifications') ? get_notifications(3, true) : [];
    $live_events     = function_exists('get_events') ? get_events(10, false) : [];
    $live_news       = function_exists('get_news') ? get_news(30) : [];
    $hero_data       = function_exists('load_hero_data') ? load_hero_data() : [];
    $hero_content    = $hero_data['content'] ?? [];
    $hero_slides     = array_values(array_filter($hero_data['slides'] ?? [], function($s) { return !empty($s['is_active']); }));

    // R&D Department Rankings data
    $rnd_periods          = function_exists('get_distinct_rnd_periods') ? get_distinct_rnd_periods(true) : [];
    $rnd_selected_year    = $_GET['rnd_year'] ?? ($rnd_periods[0]['academic_year'] ?? '2025-26');
    $rnd_selected_quarter = $_GET['rnd_quarter'] ?? ($rnd_periods[0]['quarter'] ?? 'Q4');
    $rnd_data             = function_exists('get_rnd_rankings_by_period') ? get_rnd_rankings_by_period($rnd_selected_year, $rnd_selected_quarter, 'published') : [];
    $rnd_rankings         = $rnd_data['rankings'] ?? [];
    ?>
    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero hero-full-landscape" data-sync-module="hero">
            <div class="hero-stage-container">
                <div class="hero-stage">
                    <!-- Badges (Floating on top) -->
                    <div class="hero-top-badges">
                        <div class="hero-glass-badge">
                            <?php if (!empty($hero_content['badge_left_pulse'])): ?>
                                <span class="live-pulse-dot"></span>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($hero_content['badge_left_text'] ?? 'Campus Highlights'); ?></span>
                        </div>
                        <div class="hero-glass-badge badge-accent">
                            <i class="<?php echo htmlspecialchars($hero_content['badge_right_icon'] ?? 'fas fa-certificate'); ?>"></i>
                            <span><?php echo htmlspecialchars($hero_content['badge_right_text'] ?? "NAAC 'B++' Grade & Autonomous"); ?></span>
                        </div>
                    </div>

                    <!-- Slide Media Items -->
                    <div class="hero-slides-track">
                        <?php if (!empty($hero_slides)): ?>
                            <?php $slideIdx = 0; foreach ($hero_slides as $slide): ?>
                                <?php if (($slide['media_type'] ?? 'image') === 'video'): ?>
                                    <video src="<?php echo htmlspecialchars($slide['file_path']); ?>" class="hero-slide <?php echo $slideIdx === 0 ? 'active' : ''; ?>" muted playsinline></video>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($slide['file_path']); ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? 'College Event'); ?>" class="hero-slide <?php echo $slideIdx === 0 ? 'active' : ''; ?>">
                                <?php endif; ?>
                            <?php $slideIdx++; endforeach; ?>
                        <?php else: ?>
                            <img src="assets/College Event/all1.jpg" alt="College Event" class="hero-slide active">
                            <img src="assets/College Event/caps.jpg" alt="Graduation" class="hero-slide">
                            <img src="assets/College Event/feli1.jpg" alt="Felicitation 1" class="hero-slide">
                            <img src="assets/College Event/feli2.jpg" alt="Felicitation 2" class="hero-slide">
                            <video src="assets/College Event/autonomus.mp4" class="hero-slide" muted playsinline></video>
                        <?php endif; ?>
                    </div>

                    <!-- Cinematic Bottom Overlay Bar -->
                    <div class="hero-bottom-bar">
                        <div class="hero-bar-text">
                            <h3><?php echo htmlspecialchars($hero_content['title'] ?? 'Excellence in Education'); ?></h3>
                            <p><i class="<?php echo htmlspecialchars($hero_content['subtitle_icon'] ?? 'fas fa-shield-halved'); ?>"></i> <?php echo htmlspecialchars($hero_content['subtitle'] ?? 'Inspiring Future Innovators Since 2008'); ?></p>
                        </div>

                        <!-- Controls: Arrows + Dots -->
                        <div class="hero-controls-wrapper">
                            <button class="hero-nav-arrow prev-slide" aria-label="Previous Slide">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="hero-dots-container"></div>
                            <button class="hero-nav-arrow next-slide" aria-label="Next Slide">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Quick Action CTA -->
                        <div class="hero-bar-cta">
                            <a href="<?php echo htmlspecialchars($hero_content['cta_link'] ?? 'admission.php'); ?>" class="hero-pill-btn">
                                <span><?php echo htmlspecialchars($hero_content['cta_text'] ?? 'Admissions Open'); ?></span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TCEK News Section (Magazine & Bulletin Redesign) -->
        <section class="tcek-news-section" id="news">
            <div class="container">
                <!-- Section Header -->
                <div class="news-header-wrap">
                    <span class="news-top-pill">
                        <span class="pulse-indicator"></span> Official Announcements
                    </span>
                    <h2>TCEK NEWS &amp; ANNOUNCEMENTS</h2>
                    <p>Stay informed with our latest university milestones, academic circulars, campus drives &amp;
                        press coverage</p>
                </div>

                <!-- Modern Breaking Ticker Bar -->
                <div class="news-ticker-modern">
                    <div class="ticker-badge"><i class="fas fa-bullhorn"></i> LATEST NOTICE</div>
                    <div class="ticker-marquee">
                        <?php if (!empty($marquee_notices)): ?>
                            <span>
                                <?php foreach ($marquee_notices as $mn): ?>
                                    ⚡ <strong><?php echo htmlspecialchars($mn['title']); ?>:</strong> <?php echo htmlspecialchars($mn['description'] ?: 'Notice from Trinity College of Engineering & Technology.'); ?> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                                <?php endforeach; ?>
                            </span>
                        <?php elseif (!empty($live_notices)): ?>
                            <span>⚡ <strong><?php echo htmlspecialchars($live_notices[0]['title']); ?>:</strong> <?php echo htmlspecialchars($live_notices[0]['description'] ?: 'Official notice published by Trinity College of Engineering & Technology.'); ?></span>
                        <?php else: ?>
                            <span>⚡ <strong>Admissions Open 2024–25:</strong> B.Tech, Diploma (Polytechnic) &amp; MBA |
                                EAPCET / POLYCET / ICET Code: <strong>TCEK</strong> | Helpline: <strong>7396903383</strong>,
                                <strong>8522954369</strong></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($live_notices) && !empty($live_notices[0]['file_path'])): ?>
                        <a href="<?php echo htmlspecialchars($live_notices[0]['file_path']); ?>" target="_blank" class="ticker-link-pill">
                            <span>Download PDF</span>
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    <?php else: ?>
                        <a href="admission.php" class="ticker-link-pill">
                            <span>Admissions Portal</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Main Circulars & Notifications Hub -->
                <div class="news-main-hub full-width-board">
                    <!-- Digital Bulletin Board -->
                    <div class="news-bulletin-board">
                        <div class="bulletin-header">
                            <div class="bulletin-heading">
                                <div class="bulletin-icon-pulse"><i class="fas fa-bell"></i></div>
                                <h4>Circulars &amp; Notifications</h4>
                            </div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <a href="circulars.php" style="font-size:12px; font-weight:700; color:#00b894; text-decoration:none; padding:4px 12px; background:#e6f9f4; border-radius:15px; border:1px solid #a3e9d7; transition:all 0.2s;" onmouseover="this.style.background='#00b894';this.style.color='#fff';" onmouseout="this.style.background='#e6f9f4';this.style.color='#00b894';">View All <i class="fas fa-arrow-right"></i></a>
                                <span class="bulletin-badge-live"><span class="live-dot"></span> LIVE FEED</span>
                            </div>
                        </div>

                        <div class="bulletin-items-list" id="bulletin-list-container" data-sync-module="notifications">
                            <?php include __DIR__ . '/backend/snippets/bulletin_list.php'; ?>
                        </div>
                    </div>
                </div>

                <!-- Bottom Press & Newspaper Clippings Section -->
                <div class="news-press-section">
                    <div class="press-strip-header">
                        <div class="press-title">
                            <i class="fas fa-newspaper"></i>
                            <span>TCEK In Regional &amp; National Press</span>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <a href="news.php" class="view-all-press-btn">
                                <span>View All Press News</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="press-clippings-grid" data-sync-module="news">
                        <?php include __DIR__ . '/backend/snippets/news_press_grid.php'; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Lightbox Modal -->
        <div id="news-lightbox-modal" class="news-lightbox" onclick="closeNewsLightbox(event)">
            <div class="news-lightbox-box">
                <button type="button" class="news-lightbox-close" onclick="closeNewsLightbox(event)"
                    aria-label="Close modal">&times;</button>
                <img id="news-lightbox-target" src="" alt="Zoomed Newspaper Clipping">
                <div id="news-lightbox-caption"
                    style="padding: 14px 20px 4px; background: #ffffff; border-top: 1px solid #f1f5f9; font-size: 15px; font-weight: 700; color: #1e293b; text-align: center;">
                </div>
                <div id="news-lightbox-description"
                    style="padding: 4px 20px 14px; background: #ffffff; font-size: 13px; color: #475569; text-align: center; line-height: 1.5; display: none;">
                </div>
            </div>
        </div>
        <script>
            function openNewsLightbox(src, caption, description) {
                const modal = document.getElementById('news-lightbox-modal');
                const img = document.getElementById('news-lightbox-target');
                const captionEl = document.getElementById('news-lightbox-caption');
                const descEl = document.getElementById('news-lightbox-description');
                if (modal && img) {
                    img.src = src;
                    img.alt = caption || 'News Article';
                    if (captionEl) captionEl.textContent = caption || '';
                    if (descEl) {
                        descEl.textContent = description || '';
                        descEl.style.display = description ? 'block' : 'none';
                    }
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }
            function closeNewsLightbox(e) {
                if (e.target.id === 'news-lightbox-modal' || e.target.classList.contains('news-lightbox-close')) {
                    const modal = document.getElementById('news-lightbox-modal');
                    if (modal) {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            }
        </script>

        <!-- College Events Section: Freshers Aarambh 2K26 & Sports Week -->
        <section id="events" class="college-events-section">
            <div class="container">
                <!-- Section Header -->
                <div class="event-section-header">
                    <div class="event-badge">
                        <i class="fas fa-star"></i> Campus Mega Events 2026
                    </div>
                    <h2>Freshers Aarambh 2K26 &amp; College Sports Week</h2>
                    <p>A week of energy, talent &amp; togetherness — Experience unforgettable celebrations, thrilling
                        tournaments, and memories forever.</p>
                    <div style="margin-top: 14px;">
                        <a href="events.php" class="view-all-press-btn" style="display: inline-flex;">
                            <span>Explore Dedicated Events Portal</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

<?php
// Compute spotlight and highlights from $live_events
$spotlightEvent = null;
foreach ($live_events as $ev) {
    if (!empty($ev['is_featured'])) {
        $spotlightEvent = $ev;
        break;
    }
}
if (!$spotlightEvent && !empty($live_events)) {
    $spotlightEvent = $live_events[0];
}

$spotlightVideo = null;
$spotlightImage = 'assets/events/tcek-fresher.jpg';
$spotlightVideoTitle = $spotlightEvent ? $spotlightEvent['title'] : 'Freshers Aarambh 2K26 Celebration';
$spotlightVideoDesc  = $spotlightEvent ? $spotlightEvent['description'] : 'Official campus celebration video.';

if ($spotlightEvent) {
    if (!empty($spotlightEvent['media_items'])) {
        foreach ($spotlightEvent['media_items'] as $m) {
            if ($m['media_type'] === 'video' && !$spotlightVideo) {
                $spotlightVideo = $m['file_path'];
                $spotlightVideoTitle = $m['media_title'];
                $spotlightVideoDesc = $m['media_description'];
            }
            if ($m['media_type'] === 'image' && $spotlightImage === 'assets/events/tcek-fresher.jpg') {
                $spotlightImage = $m['file_path'];
            }
        }
    }
    if (!$spotlightVideo && !empty($spotlightEvent['video_path'])) {
        $spotlightVideo = $spotlightEvent['video_path'];
    }
    if ($spotlightImage === 'assets/events/tcek-fresher.jpg' && !empty($spotlightEvent['image_path'])) {
        $spotlightImage = $spotlightEvent['image_path'];
    }
}
if (!$spotlightVideo) {
    $spotlightVideo = 'assets/events/freshers.mp4';
}

// 4 Top Media Cards
$media_cards = [];
foreach ($live_events as $ev) {
    if (!empty($ev['media_items'])) {
        foreach ($ev['media_items'] as $m) {
            $media_cards[] = [
                'type'        => $m['media_type'],
                'file_path'   => $m['file_path'],
                'title'       => $m['media_title'],
                'desc'        => $m['media_description'] ?: $ev['description'],
                'event_title' => $ev['title']
            ];
            if (count($media_cards) >= 4) break 2;
        }
    } else {
        if (!empty($ev['video_path'])) {
            $media_cards[] = [
                'type' => 'video',
                'file_path' => $ev['video_path'],
                'title' => $ev['title'] . ' Video',
                'desc' => $ev['description'],
                'event_title' => $ev['title']
            ];
            if (count($media_cards) >= 4) break;
        }
        if (!empty($ev['image_path'])) {
            $media_cards[] = [
                'type' => 'image',
                'file_path' => $ev['image_path'],
                'title' => $ev['title'] . ' Poster',
                'desc' => $ev['description'],
                'event_title' => $ev['title']
            ];
            if (count($media_cards) >= 4) break;
        }
    }
}
?>
                <!-- Event Schedule Quick Ribbon -->
                <div class="event-schedule-ribbon">
                    <?php if (!empty($live_events)): ?>
                        <?php foreach (array_slice($live_events, 0, 4) as $idx => $ev): ?>
                            <?php 
                                $dTime = strtotime($ev['event_date']);
                                $dateLabel = $dTime ? date('j M', $dTime) : $ev['event_date'];
                                $isActive = ($idx === 0) ? 'active-event' : '';
                            ?>
                            <div class="event-ribbon-item <?php echo $isActive; ?>">
                                <span class="ribbon-date"><?php echo strtoupper($dateLabel); ?></span>
                                <div class="ribbon-info">
                                    <strong><?php echo htmlspecialchars($ev['title']); ?></strong>
                                    <span><?php echo htmlspecialchars($ev['event_time'] ?: ($ev['venue'] ?? 'Campus Event')); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="event-ribbon-item active-event">
                            <span class="ribbon-date">UPCOMING</span>
                            <div class="ribbon-info">
                                <strong>Campus Events 2026</strong>
                                <span>Trinity College of Engineering &amp; Technology</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Featured Hero Spotlight Card -->
                <?php if ($spotlightEvent): ?>
                    <div class="event-spotlight-card">
                        <!-- Left: Featured Video with Audio & Fullscreen Prompt -->
                        <div class="spotlight-media"
                            onclick="openEventVideoModal('<?php echo htmlspecialchars($spotlightVideo); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoTitle)); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoDesc)); ?>')">
                            <div class="spotlight-video-preview">
                                <video class="bg-preview-vid" muted autoplay loop playsinline
                                    poster="<?php echo htmlspecialchars($spotlightImage); ?>">
                                    <source src="<?php echo htmlspecialchars($spotlightVideo); ?>" type="video/mp4">
                                </video>
                                <div class="spotlight-overlay">
                                    <div class="pulse-play-btn" title="Click to Play with Audio">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="spotlight-action-text">
                                        <span class="audio-pill"><i class="fas fa-volume-up"></i> CLICK FOR FULL SCREEN WITH
                                            AUDIO</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Event Overview & Highlights -->
                        <div class="spotlight-info">
                            <div>
                                <div class="spotlight-meta">
                                    <span class="spotlight-tag"><i class="fas fa-fire"></i> <?php echo !empty($spotlightEvent['is_featured']) ? 'Mega Event' : 'Featured Event'; ?></span>
                                    <span class="spotlight-date"><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($spotlightEvent['event_date']); ?><?php echo !empty($spotlightEvent['event_time']) ? ' · ' . htmlspecialchars($spotlightEvent['event_time']) : ''; ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($spotlightEvent['title']); ?></h3>
                                <p class="spotlight-tagline"><i class="fas fa-map-marker-alt" style="color:#00b894;"></i> Venue: <?php echo htmlspecialchars($spotlightEvent['venue'] ?? 'Trinity Campus Auditorium'); ?></p>
                                <p class="spotlight-desc">
                                    <?php echo htmlspecialchars($spotlightEvent['description'] ?: 'Annual event and celebrations at Trinity College of Engineering & Technology.'); ?>
                                </p>

                                <div class="spotlight-perks-grid">
                                    <div class="perk-chip"><i class="fas fa-music"></i> Live Music</div>
                                    <div class="perk-chip"><i class="fas fa-shoe-prints"></i> Celebrations</div>
                                    <div class="perk-chip"><i class="fas fa-theater-masks"></i> Competitions</div>
                                    <div class="perk-chip"><i class="fas fa-users"></i> Meet Friends</div>
                                    <div class="perk-chip"><i class="fas fa-trophy"></i> Exciting Awards</div>
                                    <div class="perk-chip"><i class="fas fa-camera"></i> Memories Forever</div>
                                </div>
                            </div>

                            <div class="spotlight-cta-row">
                                <button type="button" class="btn-event-play"
                                    onclick="openEventVideoModal('<?php echo htmlspecialchars($spotlightVideo); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoTitle)); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoDesc)); ?>')">
                                    <i class="fas fa-expand"></i> <span>Click Full Screen with Audio</span>
                                </button>
                                <?php if ($spotlightImage): ?>
                                    <button type="button" class="btn-event-poster"
                                        onclick="openEventImageModal('<?php echo htmlspecialchars($spotlightImage); ?>', '<?php echo addslashes(htmlspecialchars($spotlightEvent['title'])); ?> Poster')">
                                        <i class="fas fa-image"></i> <span>View Official Poster</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Event Highlights Subhead -->
                <div class="event-gallery-subhead">
                    <h3>Sports &amp; Cultural Week Highlights</h3>
                    <p>Click any video to watch in full screen with high definition audio</p>
                </div>

                <!-- 4-Card Media Grid (Videos & Posters) -->
                <div class="event-cards-grid">
                    <?php if (!empty($media_cards)): ?>
                        <?php foreach ($media_cards as $c): ?>
                            <?php if ($c['type'] === 'video'): ?>
                                <div class="event-card video-card"
                                    onclick="openEventVideoModal('<?php echo htmlspecialchars($c['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($c['title'])); ?>', '<?php echo addslashes(htmlspecialchars($c['desc'])); ?>')">
                                    <div class="event-card-thumb">
                                        <video muted loop playsinline class="card-video-loop">
                                            <source src="<?php echo htmlspecialchars($c['file_path']); ?>" type="video/mp4">
                                        </video>
                                        <div class="card-video-overlay">
                                            <span class="card-play-icon"><i class="fas fa-play"></i></span>
                                            <span class="audio-badge"><i class="fas fa-volume-up"></i> Full Screen &amp; Audio</span>
                                        </div>
                                        <span class="media-type-badge video"><i class="fas fa-video"></i> Video</span>
                                    </div>
                                    <div class="event-card-content">
                                        <div>
                                            <span class="event-category-tag sports"><?php echo htmlspecialchars($c['event_title']); ?></span>
                                            <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                                            <p><?php echo htmlspecialchars($c['desc'] ?: 'Event highlight video.'); ?></p>
                                        </div>
                                        <span class="click-hint"><i class="fas fa-expand"></i> Click full screen with audio</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="event-card poster-card"
                                    onclick="openEventImageModal('<?php echo htmlspecialchars($c['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($c['title'])); ?>')">
                                    <div class="event-card-thumb">
                                        <img src="<?php echo htmlspecialchars($c['file_path']); ?>" alt="<?php echo htmlspecialchars($c['title']); ?>" loading="lazy" onerror="this.src='assets/events/tcek-fresher.jpg'">
                                        <div class="card-image-overlay">
                                            <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                                            <span class="zoom-badge">View Full Image</span>
                                        </div>
                                        <span class="media-type-badge image"><i class="fas fa-image"></i> Photo</span>
                                    </div>
                                    <div class="event-card-content">
                                        <div>
                                            <span class="event-category-tag cultural"><?php echo htmlspecialchars($c['event_title']); ?></span>
                                            <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                                            <p><?php echo htmlspecialchars($c['desc'] ?: 'Event poster/photo.'); ?></p>
                                        </div>
                                        <span class="click-hint"><i class="fas fa-search-plus"></i> Click to view high-res</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Fullscreen Video & Audio Player Modal -->
        <div id="event-video-modal" class="event-media-modal" onclick="closeEventVideoModal(event)">
            <div class="event-video-container" onclick="event.stopPropagation()">
                <div class="event-modal-topbar">
                    <div class="event-modal-title-wrap">
                        <span class="modal-live-tag"><i class="fas fa-circle"></i> NOW PLAYING WITH AUDIO</span>
                        <h4 id="event-modal-title">Event Video</h4>
                    </div>
                    <div class="event-modal-actions">
                        <button type="button" class="btn-modal-fullscreen" onclick="toggleNativeFullscreen()"
                            title="Toggle Fullscreen (F)">
                            <i class="fas fa-expand"></i> <span>Full Screen</span>
                        </button>
                        <button type="button" class="btn-modal-close" onclick="closeEventVideoModal(null)"
                            aria-label="Close modal">&times;</button>
                    </div>
                </div>

                <div class="event-video-player-wrap" id="event-player-wrapper">
                    <video id="event-modal-video" controls playsinline preload="auto">
                        Your browser does not support HTML5 video.
                    </video>
                </div>

                <div class="event-modal-footer">
                    <p id="event-modal-desc">Event highlight video</p>
                    <div class="modal-shortcuts">
                        <span><kbd>Esc</kbd> Close</span>
                        <span><kbd>Space</kbd> Play / Pause</span>
                        <span><kbd>F</kbd> Full Screen</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- High-Res Image Lightbox Modal for Posters -->
        <div id="event-image-modal" class="event-image-modal" onclick="closeEventImageModal(event)">
            <div class="event-image-box" onclick="event.stopPropagation()">
                <button type="button" class="event-image-close" onclick="closeEventImageModal(null)"
                    aria-label="Close">&times;</button>
                <img id="event-modal-img" src="" alt="Event Poster">
                <div class="event-image-caption" id="event-image-caption"></div>
            </div>
        </div>

        <!-- Event Modal Interactive Scripts -->
        <script>
            function openEventVideoModal(src, title, desc) {
                const modal = document.getElementById('event-video-modal');
                const video = document.getElementById('event-modal-video');
                const titleEl = document.getElementById('event-modal-title');
                const descEl = document.getElementById('event-modal-desc');

                if (modal && video) {
                    titleEl.textContent = title || 'Event Video';
                    descEl.textContent = desc || '';
                    video.src = src;
                    video.muted = false; // ENABLE AUDIO!
                    video.volume = 1.0;  // FULL VOLUME!
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';

                    // Play with audio (user gesture initiated)
                    const playPromise = video.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(function (err) {
                            console.log('Autoplay audio fallback:', err);
                            // If browser blocks unmuted autoplay, unmute on next click
                            video.muted = false;
                        });
                    }
                }
            }

            function closeEventVideoModal(e) {
                if (!e || e.target.id === 'event-video-modal' || e.target.classList.contains('btn-modal-close')) {
                    const modal = document.getElementById('event-video-modal');
                    const video = document.getElementById('event-modal-video');
                    if (modal && video) {
                        video.pause();
                        video.src = '';
                        modal.classList.remove('active');
                        document.body.style.overflow = '';

                        // Exit native fullscreen if active
                        if (document.fullscreenElement) {
                            document.exitFullscreen().catch(function () { });
                        }
                    }
                }
            }

            function toggleNativeFullscreen() {
                const playerWrap = document.getElementById('event-player-wrapper');
                const video = document.getElementById('event-modal-video');
                const target = playerWrap || video;

                if (!document.fullscreenElement) {
                    if (target.requestFullscreen) {
                        target.requestFullscreen().catch(function () { });
                    } else if (target.webkitRequestFullscreen) {
                        target.webkitRequestFullscreen();
                    } else if (video && video.webkitEnterFullscreen) {
                        video.webkitEnterFullscreen(); // iOS Safari
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen().catch(function () { });
                    }
                }
            }

            function openEventImageModal(src, caption) {
                const modal = document.getElementById('event-image-modal');
                const img = document.getElementById('event-modal-img');
                const captionEl = document.getElementById('event-image-caption');
                if (modal && img) {
                    img.src = src;
                    img.alt = caption || 'Event Poster';
                    if (captionEl) captionEl.textContent = caption || '';
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeEventImageModal(e) {
                if (!e || e.target.id === 'event-image-modal' || e.target.classList.contains('event-image-close')) {
                    const modal = document.getElementById('event-image-modal');
                    if (modal) {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            }

            // Keyboard navigation for video and image modals
            document.addEventListener('keydown', function (e) {
                const videoModal = document.getElementById('event-video-modal');
                const imgModal = document.getElementById('event-image-modal');
                const video = document.getElementById('event-modal-video');

                if (videoModal && videoModal.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        closeEventVideoModal(null);
                    } else if (e.key === ' ' || e.code === 'Space') {
                        e.preventDefault();
                        if (video) {
                            video.paused ? video.play() : video.pause();
                        }
                    } else if (e.key === 'f' || e.key === 'F') {
                        e.preventDefault();
                        toggleNativeFullscreen();
                    }
                } else if (imgModal && imgModal.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        closeEventImageModal(null);
                    }
                }
            });
        </script>

        <!-- Features/Courses Section -->
        <section id="courses">
            <div class="container">
                <div class="section-header">
                    <h2>Our Courses</h2>
                    <p>World-class academic programmes designed for future leaders</p>
                </div>

                <!-- Dynamic Courses Tabs & Grid from Department Store / Database -->
                <div id="dynamic-courses-container" data-sync-module="courses">
                    <?php include __DIR__ . '/backend/snippets/courses_grid.php'; ?>
                </div>
            </div>
        </section>

        <!-- Achievements & Placements Section -->
        <section id="achievements">
            <div class="container">
                <div class="ach-header-wrap">
                    <div class="ach-pill">
                        <span class="ach-pill-dot"></span>
                        <span>Campus Placement Success • Class of 2022-2024</span>
                    </div>
                    <h2>Our Achievements & Placements</h2>
                    <p>Celebrating the remarkable success stories of our students advancing into global IT leaders and
                        multinational technology corporations</p>
                </div>

                <!-- Placement Highlights Stats Bar -->
                <div class="ach-stats-grid">
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">85%+</span>
                            <span class="ach-stat-label">Placement Record</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-trophy"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">₹12 LPA</span>
                            <span class="ach-stat-label">Highest Package</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-building"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">50+</span>
                            <span class="ach-stat-label">Corporate Recruiters</span>
                        </div>
                    </div>
                    <div class="ach-stat-item">
                        <div class="ach-stat-icon"><i class="fas fa-award"></i></div>
                        <div class="ach-stat-info">
                            <span class="ach-stat-number">TASK Partner</span>
                            <span class="ach-stat-label">Govt. Skill Synergy</span>
                        </div>
                    </div>
                </div>

                <!-- Main Showcase Card Slider -->
                <div class="achievements-container">
                    <div class="achievements-showcase-card">
                        <div class="achievement-slider">
                            <!-- Slide 1: G. Bhavitha -->
                            <div class="achievement-slide active">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/Achievements/bhavitha_capgemini.png"
                                                alt="G. Bhavitha - Placed in Capgemini (4 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK
                                                Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of
                                                2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill capgemini">
                                                <i class="fas fa-briefcase"></i> Placed in Capgemini
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "As a CSE student at our institution, I, Bhavitha, can confidently say that
                                            our campus placement services are <span
                                                class="ach-text-bold">exceptional</span>. The training and support we
                                            receive are tailored to ensure we are <span
                                                class="ach-text-bold">well-prepared for the job market</span>. From
                                            enhancing our technical skills to providing interview preparation, the focus
                                            on our future careers is evident. Thanks to these efforts, I was
                                            successfully placed in <span class="ach-text-bold">Capgemini</span>."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>GB</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">G. Bhavitha</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 18UD1AO410</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">4.0 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2: Zainab Khatoon -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/Achievements/zainab_tcs.png"
                                                alt="Zainab Khatoon - Placed in TCS (3.6 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK
                                                Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of
                                                2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill tcs">
                                                <i class="fas fa-briefcase"></i> Placed in TCS
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "The dedicated training and comprehensive support provided by our
                                            institution have been instrumental in <span class="ach-text-bold">shaping my
                                                engineering career</span>. The focus on practical skills, mock
                                            interviews, and industry-specific knowledge thoroughly prepared me for the
                                            job market. Thanks to these efforts, I secured a placement with <span
                                                class="ach-text-bold">Tata Consultancy Services</span> as Assistant
                                            System Engineer."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>ZK</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">Zainab Khatoon</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 19UD1A0542</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.6 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 3: J. Pooja -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/placements/student1.jpeg"
                                                alt="J. Pooja - Placed in Infosys (3.6 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK
                                                Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of
                                                2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill infosys">
                                                <i class="fas fa-briefcase"></i> Placed in Infosys
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "Trinity College provided an enriching academic ecosystem with active
                                            guidance from experienced mentors and the <span
                                                class="ach-text-bold">Training & Placement Cell</span>. Continuous
                                            aptitude assessments, soft-skill workshops, and coding challenges gave me
                                            the edge required to crack the <span class="ach-text-bold">Infosys</span>
                                            national assessment and interview rounds."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>JP</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">J. Pooja</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 19UD5A0206</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.6 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 4: Ananth Kumar -->
                            <div class="achievement-slide">
                                <div class="ach-card-layout">
                                    <div class="ach-poster-side">
                                        <div class="ach-badge-tag">
                                            <i class="fas fa-certificate"></i> Verified Campus Placement
                                        </div>
                                        <div class="ach-poster-frame">
                                            <img src="assets/placements/student5.jpeg"
                                                alt="Ananth Kumar - Placed in Wipro (3.5 LPA)" class="ach-poster-img">
                                        </div>
                                        <div class="ach-poster-footer">
                                            <span class="poster-univ-code"><i class="fas fa-university"></i> TCEK
                                                Peddapalli</span>
                                            <span class="poster-verified"><i class="fas fa-check-circle"></i> Batch of
                                                2022</span>
                                        </div>
                                    </div>
                                    <div class="ach-story-side">
                                        <div class="ach-story-header">
                                            <div class="ach-quote-bubble">
                                                <i class="fas fa-quote-left"></i>
                                            </div>
                                            <div class="company-badge-pill wipro">
                                                <i class="fas fa-briefcase"></i> Placed in Wipro
                                            </div>
                                        </div>
                                        <p class="ach-story-text">
                                            "The hands-on laboratory experience and guidance from our faculty at TCEK
                                            helped me build strong engineering fundamentals. The college's industry
                                            partnerships, <span class="ach-text-bold">TASK skill bootcamps</span>, and
                                            placement training gave us real-world corporate readiness, helping me secure
                                            an offer at <span class="ach-text-bold">Wipro</span>."
                                        </p>
                                        <div class="ach-student-profile">
                                            <div class="ach-avatar">
                                                <span>AK</span>
                                            </div>
                                            <div class="ach-profile-meta">
                                                <h4 class="ach-student-name">Ananth Kumar</h4>
                                                <div class="ach-student-sub">
                                                    <span class="ach-dept-text">Computer Science & Engineering</span>
                                                    <span class="ach-roll-tag">HT No: 18UD1AO422</span>
                                                </div>
                                            </div>
                                            <div class="ach-offer-pill">
                                                <span class="offer-lbl">ANNUAL PACKAGE</span>
                                                <span class="offer-val">3.5 LPA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Controls Bar -->
                        <div class="ach-controls-bar">
                            <div class="ach-counter">
                                <span class="ach-current-num">01</span>
                                <span class="ach-separator">/</span>
                                <span class="ach-total-num">04</span>
                            </div>
                            <div class="ach-dots" id="achDots">
                                <button class="ach-dot active" data-index="0"
                                    aria-label="Slide 1: G. Bhavitha"></button>
                                <button class="ach-dot" data-index="1" aria-label="Slide 2: Zainab Khatoon"></button>
                                <button class="ach-dot" data-index="2" aria-label="Slide 3: J. Pooja"></button>
                                <button class="ach-dot" data-index="3" aria-label="Slide 4: Ananth Kumar"></button>
                            </div>
                            <div class="ach-nav-btns">
                                <button class="ach-ctrl-btn ach-prev" aria-label="Previous Student"><i
                                        class="fas fa-arrow-left"></i></button>
                                <button class="ach-ctrl-btn ach-next" aria-label="Next Student"><i
                                        class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placed Students Quick-Strip / Mini Wall -->
                <div class="ach-alumni-strip">
                    <div class="ach-alumni-heading">
                        <i class="fas fa-users"></i>
                        <span>More Star Placements:</span>
                    </div>
                    <div class="ach-alumni-badges">
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student2.jpeg" alt="B. Sravani - Capgemini">
                            <div class="pill-meta">
                                <strong>B. Sravani</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student4.jpeg" alt="G. Swetha - Capgemini">
                            <div class="pill-meta">
                                <strong>G. Swetha</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student6.jpeg" alt="J. Hima Bindu - Capgemini">
                            <div class="pill-meta">
                                <strong>J. Hima Bindu</strong>
                                <span>Capgemini (4 LPA)</span>
                            </div>
                        </div>
                        <div class="ach-alumni-pill">
                            <img src="assets/placements/student7.jpeg" alt="M. Meghana - TCS">
                            <div class="pill-meta">
                                <strong>M. Meghana</strong>
                                <span>TCS (3.36 LPA)</span>
                            </div>
                        </div>
                    </div>
                    <div class="ach-cta-wrap">
                        <a href="placement-cell.php" class="btn-ach-explore">
                            <span>Explore Placement Cell</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Recruiter Logos Strip -->
                <div class="ach-recruiters-section">
                    <div class="ach-recruiters-title">Proud Corporate Hiring & Training Partners</div>
                    <div class="ach-recruiters-grid">
                        <div class="recruiter-chip"><span class="chip-dot"></span> Capgemini</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Tata Consultancy Services</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Infosys</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Wipro</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Tech Mahindra</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> Cognizant</div>
                        <div class="recruiter-chip"><span class="chip-dot"></span> TASK Telangana</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- R&D Department Rankings Section (Dynamic Database-Driven) -->
        <section id="rnd-rankings" data-sync-module="rnd">
            <div class="container">
                <div class="rnd-header-wrap">
                    <div class="rnd-pill">
                        <span class="rnd-pill-icon"><i class="fas fa-atom"></i></span>
                        <span id="rndHeaderPeriodLabel"><?php echo htmlspecialchars($rnd_data['quarter_label'] ?: "{$rnd_selected_quarter} (AY {$rnd_selected_year}) Official Announcement"); ?></span>
                    </div>
                    <h2>R&amp;D Department Rankings</h2>
                    <p>Congratulations to all academic departments for outstanding achievements in research, innovation,
                        patents, international publications, NPTEL benchmarks, IIC and R&amp;D activities.</p>
                </div>

                <!-- Interactive Evaluation Period Selector Strip -->
                <?php if (!empty($rnd_periods)): ?>
                    <div class="rnd-period-selector-bar">
                        <div class="rnd-selector-inner">
                            <span class="rnd-selector-label"><i class="fas fa-history"></i> Evaluation Period:</span>
                            <div class="rnd-period-tabs" id="rndPeriodTabs">
                                <?php foreach ($rnd_periods as $p): 
                                    $isSelected = (strcasecmp($p['academic_year'], $rnd_selected_year) === 0 && strcasecmp($p['quarter'], $rnd_selected_quarter) === 0);
                                ?>
                                    <button type="button" 
                                            class="rnd-period-btn <?php echo $isSelected ? 'active' : ''; ?>"
                                            data-year="<?php echo htmlspecialchars($p['academic_year']); ?>"
                                            data-quarter="<?php echo htmlspecialchars($p['quarter']); ?>"
                                            onclick="switchRndPeriod('<?php echo htmlspecialchars($p['academic_year']); ?>', '<?php echo htmlspecialchars($p['quarter']); ?>', this)">
                                        <?php if (!empty($p['is_latest'])): ?>
                                            <span class="rnd-latest-tag">LATEST</span>
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($p['quarter']); ?> · AY <?php echo htmlspecialchars($p['academic_year']); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Criteria Pills Strip -->
                <div class="rnd-criteria-strip">
                    <span class="rnd-crit-tag"><i class="fas fa-microscope"></i> Research &amp; Innovation</span>
                    <span class="rnd-crit-tag"><i class="fas fa-certificate"></i> Patents &amp; IPR</span>
                    <span class="rnd-crit-tag"><i class="fas fa-book-open"></i> Scopus &amp; IEEE Publications</span>
                    <span class="rnd-crit-tag"><i class="fas fa-medal"></i> NPTEL Honors</span>
                    <span class="rnd-crit-tag"><i class="fas fa-lightbulb"></i> IIC Initiatives</span>
                </div>

                <!-- Dynamic 5 Department Rankings Grid -->
                <div class="rnd-podium-grid" id="rndPodiumGrid">
                    <?php if (empty($rnd_rankings)): ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 16px;">
                            <i class="fas fa-medal" style="font-size: 32px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
                            No ranking results published yet for this period.
                        </div>
                    <?php else: ?>
                        <?php foreach ($rnd_rankings as $idx => $r): 
                            $rank = (int)($r['rank'] ?? ($idx + 1));
                            $rankClass = 'rank-' . min(5, $rank);
                            $suffix = 'th';
                            if ($rank === 1) $suffix = 'st';
                            elseif ($rank === 2) $suffix = 'nd';
                            elseif ($rank === 3) $suffix = 'rd';
                            $achievements = $r['achievements'] ?? [];
                        ?>
                            <div class="rnd-card <?php echo $rankClass; ?>">
                                <div>
                                    <div class="rnd-card-top">
                                        <span class="rnd-rank-num"><?php echo $rank; ?><sup><?php echo $suffix; ?></sup></span>
                                        <span class="rnd-medal-badge"><?php echo htmlspecialchars($r['award_category'] ?? "Rank {$rank}"); ?></span>
                                    </div>
                                    <h3 class="rnd-dept-code"><?php echo htmlspecialchars($r['department']); ?></h3>
                                    <div class="rnd-dept-full"><?php echo htmlspecialchars($r['department_name']); ?></div>
                                    <ul class="rnd-dept-perks">
                                        <?php foreach ($achievements as $ach): ?>
                                            <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($ach); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="rnd-status-tag">
                                    <?php echo htmlspecialchars($r['award_badge'] ?? 'Emerging'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Motivation & Acknowledgement Card -->
                <div class="rnd-footer-card">
                    <div class="rnd-motto-side">
                        <div class="rnd-motto-quote">
                            ✨ "Your Innovation. <span>Our Pride.</span> Keep Innovating. Keep Inspiring."
                        </div>
                        <div class="rnd-signature">
                            <i class="fas fa-signature"></i> — Team Research &amp; Development (R&amp;D)
                        </div>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="rnd-rankings.php" class="btn-rnd-explore"
                                style="background: #00b894; color: #ffffff;">
                                <span>View Full R&amp;D Rankings &amp; Scorecard</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="research-publications.php" class="btn-rnd-explore"
                                style="background: #f1f5f9; color: #334155;">
                                <span>Research Publications</span>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="rnd-ack-side">
                        <div class="rnd-ack-box">
                            <div class="rnd-ack-icon">👏</div>
                            <div class="rnd-ack-text">
                                <h5>Heartiest Congratulations</h5>
                                <p>To all the Faculty, HoDs, Students and Department Coordinators for their dedication
                                    and continuous contribution towards building a strong research and innovation
                                    ecosystem.</p>
                            </div>
                        </div>
                        <div class="rnd-ack-box">
                            <div class="rnd-ack-icon">🙏</div>
                            <div class="rnd-ack-text">
                                <h5>Sincere Gratitude</h5>
                                <p>Our sincere thanks to the Management, Staff, Stakeholders, Students, Parents &amp;
                                    Alumni for their constant encouragement and invaluable support.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Us, Vision & Mission Section -->
        <section id="why-us" style="background: #ffffff; padding: 90px 20px; border-top: 1px solid #f1f5f9;">
            <div class="container">
                <div class="section-header">
                    <h2>Why Us?</h2>
                    <!-- Intro Text -->
                    <div
                        style="font-size: 16px; color: #636e72; line-height: 1.8; margin-bottom: 40px; text-align: left;">
                        <p style="margin-bottom: 20px;">
                            To implement this thought, we established an Education Society which aims at breaking
                            grounds for high level educational institutions. Trinity College of Engineering and
                            Technology (T.C.E.K.) is a premier initiative of this society. The institute aims to impart
                            knowledge by attracting and involving well experienced, qualified faculty and providing best
                            infrastructural facilities to the students. T.C.E.K. views interaction and collaboration
                            with industry as critical for preparing successful and trend setter technocrats for
                            tomorrow. Workshops and guest lectures with a focus on developing entrepreneur skills will
                            be our mainstay.
                        </p>
                        <p>
                            We are leading step by step to achieve our objectives to transform the Institute into one of
                            the notable technical institutes of the country. T.C.E.K. is ready with the facilities to
                            provide best services to you. It is for you to avail this opportunity.
                        </p>
                    </div>
                </div>

                <div class="features-grid">
                    <div class="card">
                        <div class="modal-icon" style="color:#00b894;"><i class="fas fa-eye"></i></div>
                        <h3>Our Vision</h3>
                        <p>Becoming a vibrant knowledge hub and a center of excellence in education. Generating cutting
                            edge technology using research and innovation to make India a developed nation. Creating
                            leaders in the field of science, technology and management by providing quality education.
                            To be the fountain head in producing highly skilled, globally competent engineers.</p>
                    </div>
                    <div class="card">
                        <div class="modal-icon" style="color:#00b894;"><i class="fas fa-heart"></i></div>
                        <h3>Our Values</h3>
                        <p>Such an esteemed institutions are Trinity Educationaly Institutions, where excellence
                            exemplifies setting new standards in the field of academics with it continuous process to
                            its consistency.</p>
                    </div>
                </div>

                <!-- Mission Section (Split Layout) -->
                <div class="mission-wrapper" style="margin-top: 50px;">
                    <div class="mission-split-container">
                        <!-- Left Side: Visual & Title -->
                        <div class="mission-content-left">
                            <div class="section-header" style="text-align: left; margin-bottom: 30px;">
                                <span
                                    style="display: block; font-size: 14px; font-weight: 700; color: #00b894; margin-bottom: 10px; letter-spacing: 1px; text-transform: uppercase;">Our
                                    Goal</span>
                                <h2 style="margin-bottom: 15px;">Our Mission</h2>
                                <p style="font-size: 16px; margin-bottom: 0;">Driving innovation and excellence in
                                    technical education to shape the future.</p>
                            </div>
                            <!-- Generated Illustration -->
                            <img src="assets/Top Header/mission_abstract.png" alt="Mission and Growth Illustration">
                        </div>

                        <!-- Right Side: Vertical List -->
                        <div class="mission-list">
                            <!-- M1 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Accessible Education</h4>
                                    <p>Committed to make higher education available to all those who are deprived of
                                        object-oriented modular education with an emphasis on practical knowledge
                                        keeping in view the emerging industrial needs.</p>
                                </div>
                            </div>

                            <!-- M2 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Skill-Based Training</h4>
                                    <p>To provide an affordable high-quality education student centered
                                        teaching-learning processes to the professional aspirants of rural areas to
                                        impart skill-based training and achieve 100% placements.</p>
                                </div>
                            </div>

                            <!-- M3 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Conducive Atmosphere</h4>
                                    <p>To create a healthy and conducive atmosphere among the faculty, students both
                                        professionally and ethically and to have an effective interaction with industry
                                        professionals and alumni.</p>
                                </div>
                            </div>

                            <!-- M4 -->
                            <div class="mission-item">
                                <div class="mission-icon-box">
                                    <i class="fas fa-microscope"></i>
                                </div>
                                <div class="mission-info">
                                    <h4>Research & Development</h4>
                                    <p>To promote research activities among the students and to generate technically
                                        sound and highly skilled Engineers to cater the needs of the nation.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>


    <script src="js/tabs.js"></script>
    <script src="js/achievements.js"></script>
    <script src="js/slideshow.js"></script>
    <script>
    function switchRndPeriod(year, quarter, btnEl) {
        if (btnEl) {
            document.querySelectorAll('#rndPeriodTabs .rnd-period-btn').forEach(b => b.classList.remove('active'));
            btnEl.classList.add('active');
        }

        const grid = document.getElementById('rndPodiumGrid');
        const headerLabel = document.getElementById('rndHeaderPeriodLabel');
        if (!grid) return;

        grid.style.opacity = '0.35';
        grid.style.transition = 'opacity 0.2s ease';

        fetch(`backend/rnd_crud.php?action=get_rnd_rankings_json&academic_year=${encodeURIComponent(year)}&quarter=${encodeURIComponent(quarter)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    const data = res.data;
                    if (headerLabel && data.quarter_label) {
                        headerLabel.textContent = data.quarter_label;
                    } else if (headerLabel) {
                        headerLabel.textContent = `${quarter} (AY ${year}) Official Announcement`;
                    }

                    const rankings = data.rankings || [];
                    if (rankings.length === 0) {
                        grid.innerHTML = `
                            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 16px;">
                                <i class="fas fa-medal" style="font-size: 32px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
                                No ranking results published yet for this period.
                            </div>
                        `;
                    } else {
                        let html = '';
                        rankings.forEach((r, idx) => {
                            const rank = parseInt(r.rank) || (idx + 1);
                            const rankClass = 'rank-' + Math.min(5, rank);
                            let suffix = 'th';
                            if (rank === 1) suffix = 'st';
                            else if (rank === 2) suffix = 'nd';
                            else if (rank === 3) suffix = 'rd';

                            const perks = (r.achievements || []).map(p => `<li><i class="fas fa-check-circle"></i> ${escapeHtml(p)}</li>`).join('');

                            html += `
                                <div class="rnd-card ${rankClass}">
                                    <div>
                                        <div class="rnd-card-top">
                                            <span class="rnd-rank-num">${rank}<sup>${suffix}</sup></span>
                                            <span class="rnd-medal-badge">${escapeHtml(r.award_category || ('Rank ' + rank))}</span>
                                        </div>
                                        <h3 class="rnd-dept-code">${escapeHtml(r.department || '')}</h3>
                                        <div class="rnd-dept-full">${escapeHtml(r.department_name || '')}</div>
                                        <ul class="rnd-dept-perks">
                                            ${perks}
                                        </ul>
                                    </div>
                                    <div class="rnd-status-tag">
                                        ${escapeHtml(r.award_badge || 'Emerging')}
                                    </div>
                                </div>
                            `;
                        });
                        grid.innerHTML = html;
                    }
                }
                grid.style.opacity = '1';
            })
            .catch(err => {
                console.error('Failed to load rankings:', err);
                grid.style.opacity = '1';
            });
    }

    // Auto-sync updates from dashboard in real time without manual page reload
    if (window.BroadcastChannel) {
        try {
            const rndChannel = new BroadcastChannel('tcek_rnd_sync');
            rndChannel.onmessage = function(e) {
                if (e.data && e.data.type === 'rnd_updated') {
                    const activeBtn = document.querySelector('#rndPeriodTabs .rnd-period-btn.active');
                    const year = activeBtn ? activeBtn.getAttribute('data-year') : null;
                    const quarter = activeBtn ? activeBtn.getAttribute('data-quarter') : null;
                    if (year && quarter) {
                        switchRndPeriod(year, quarter, activeBtn);
                    }
                }
            };
        } catch(e) {}
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    </script>
    <?php include 'footer.php'; ?>