<?php
$page = 'events';
@include_once __DIR__ . '/backend/config/database.php';
@include_once __DIR__ . '/backend/crud.php';
$db_events = function_exists('get_events') ? get_events(10, false) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Campus Events &amp; Fests - Trinity College of Engineering &amp; Technology</title>
    <meta name="description" content="Official Campus Mega Events at Trinity College of Engineering and Technology (TCEK), Peddapalli. Freshers Aarambh 2K26, College Sports Week, Flash Mob, and Cultural Celebrations.">
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="rnd-page-body">
    <?php include 'header.php'; ?>

    <!-- Page Hero Header -->
    <section class="page-header rnd-hero-banner" style="margin-bottom: 0;">
        <div class="container">
            <div class="rnd-hero-badge">
                <i class="fas fa-sparkles"></i> Campus Life &amp; Mega Events 2026
            </div>
            <h1>College Events &amp; Celebrations</h1>
            <p class="rnd-breadcrumbs">
                <a href="index.php">Home</a> <i class="fas fa-chevron-right"></i>
                <span>College Events</span>
            </p>
        </div>
    </section>

    <!-- Main Content Section -->
    <main class="rnd-main-content">
        <div class="container">

            <!-- Section Header -->
            <div class="event-section-header" style="margin-top: 10px;">
                <div class="event-badge">
                    <i class="fas fa-fire"></i> Annual Mega Festival
                </div>
                <h2>Freshers Aarambh 2K26 &amp; College Sports Week</h2>
                <p>A week of energy, talent &amp; togetherness — Experience unforgettable celebrations, thrilling tournaments, and memories forever.</p>
            </div>

<?php
// Prepare dynamic event media
$spotlightEvent = null;
foreach ($db_events as $ev) {
    if (!empty($ev['is_featured'])) {
        $spotlightEvent = $ev;
        break;
    }
}
if (!$spotlightEvent && !empty($db_events)) {
    $spotlightEvent = $db_events[0];
}

// Find spotlight media (video preferred, else image)
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

// Extract all attached videos and photos across all events
$all_videos = [];
$all_photos = [];

foreach ($db_events as $ev) {
    if (!empty($ev['media_items'])) {
        foreach ($ev['media_items'] as $m) {
            if ($m['media_type'] === 'video') {
                $all_videos[] = [
                    'event_id'    => $ev['id'],
                    'event_title' => $ev['title'],
                    'file_path'   => $m['file_path'],
                    'title'       => $m['media_title'],
                    'desc'        => $m['media_description'] ?: $ev['description'],
                    'venue'       => $ev['venue'] ?? 'Trinity Campus'
                ];
            } else {
                $all_photos[] = [
                    'event_id'    => $ev['id'],
                    'event_title' => $ev['title'],
                    'file_path'   => $m['file_path'],
                    'title'       => $m['media_title'],
                    'desc'        => $m['media_description'] ?: $ev['description'],
                    'venue'       => $ev['venue'] ?? 'Trinity Campus'
                ];
            }
        }
    } else {
        if (!empty($ev['video_path'])) {
            $all_videos[] = [
                'event_id'    => $ev['id'],
                'event_title' => $ev['title'],
                'file_path'   => $ev['video_path'],
                'title'       => $ev['title'] . ' Highlights',
                'desc'        => $ev['description'],
                'venue'       => $ev['venue'] ?? 'Trinity Campus'
            ];
        }
        if (!empty($ev['image_path'])) {
            $all_photos[] = [
                'event_id'    => $ev['id'],
                'event_title' => $ev['title'],
                'file_path'   => $ev['image_path'],
                'title'       => $ev['title'] . ' Official Poster',
                'desc'        => $ev['description'],
                'venue'       => $ev['venue'] ?? 'Trinity Campus'
            ];
        }
    }
}
?>

            <!-- Event Schedule Quick Ribbon -->
            <div class="event-schedule-ribbon">
                <?php if (!empty($db_events)): ?>
                    <?php foreach (array_slice($db_events, 0, 5) as $idx => $ev): ?>
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
                            <strong>College Events 2026</strong>
                            <span>Trinity College of Engineering &amp; Technology</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Featured Hero Spotlight Card -->
            <?php if ($spotlightEvent): ?>
                <div class="event-spotlight-card">
                    <!-- Left: Featured Video with Audio & Fullscreen Prompt -->
                    <div class="spotlight-media" onclick="openEventVideoModal('<?php echo htmlspecialchars($spotlightVideo); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoTitle)); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoDesc)); ?>')">
                        <div class="spotlight-video-preview">
                            <video class="bg-preview-vid" muted autoplay loop playsinline poster="<?php echo htmlspecialchars($spotlightImage); ?>">
                                <source src="<?php echo htmlspecialchars($spotlightVideo); ?>" type="video/mp4">
                            </video>
                            <div class="spotlight-overlay">
                                <div class="pulse-play-btn" title="Click to Play with Audio">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="spotlight-action-text">
                                    <span class="audio-pill"><i class="fas fa-volume-up"></i> CLICK FOR FULL SCREEN WITH AUDIO</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Event Overview & Highlights -->
                    <div class="spotlight-info">
                        <div>
                            <div class="spotlight-meta">
                                <span class="spotlight-tag"><i class="fas fa-fire"></i> <?php echo !empty($spotlightEvent['is_featured']) ? 'Mega Spotlight' : 'Campus Event'; ?></span>
                                <span class="spotlight-date"><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($spotlightEvent['event_date']); ?><?php echo !empty($spotlightEvent['event_time']) ? ' · ' . htmlspecialchars($spotlightEvent['event_time']) : ''; ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($spotlightEvent['title']); ?></h3>
                            <p class="spotlight-tagline"><i class="fas fa-map-marker-alt" style="color:#00b894;"></i> Venue: <?php echo htmlspecialchars($spotlightEvent['venue'] ?? 'Trinity Campus Auditorium'); ?></p>
                            <p class="spotlight-desc">
                                <?php echo htmlspecialchars($spotlightEvent['description'] ?: 'Annual event and festival celebrations at Trinity College of Engineering & Technology.'); ?>
                            </p>

                            <div class="spotlight-perks-grid">
                                <div class="perk-chip"><i class="fas fa-music"></i> Live Music</div>
                                <div class="perk-chip"><i class="fas fa-shoe-prints"></i> Celebrations</div>
                                <div class="perk-chip"><i class="fas fa-theater-masks"></i> Competitions</div>
                                <div class="perk-chip"><i class="fas fa-users"></i> Meet Friends</div>
                                <div class="perk-chip"><i class="fas fa-trophy"></i> Awards &amp; Honors</div>
                                <div class="perk-chip"><i class="fas fa-camera"></i> Memories Forever</div>
                            </div>
                        </div>

                        <div class="spotlight-cta-row">
                            <button type="button" class="btn-event-play" onclick="openEventVideoModal('<?php echo htmlspecialchars($spotlightVideo); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoTitle)); ?>', '<?php echo addslashes(htmlspecialchars($spotlightVideoDesc)); ?>')">
                                <i class="fas fa-expand"></i> <span>Play Video with Audio</span>
                            </button>
                            <?php if ($spotlightImage): ?>
                                <button type="button" class="btn-event-poster" onclick="openEventImageModal('<?php echo htmlspecialchars($spotlightImage); ?>', '<?php echo addslashes(htmlspecialchars($spotlightEvent['title'])); ?> Poster')">
                                    <i class="fas fa-image"></i> <span>View Official Poster</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Video Highlights Section -->
            <?php if (!empty($all_videos)): ?>
                <div class="event-gallery-subhead">
                    <h3>Event &amp; Tournament Highlights (HD Video with Audio)</h3>
                    <p>Click any video card to launch the player in full screen with high definition sound</p>
                </div>

                <div class="event-cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); margin-bottom: 50px;">
                    <?php foreach ($all_videos as $vid): ?>
                        <div class="event-card video-card" onclick="openEventVideoModal('<?php echo htmlspecialchars($vid['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($vid['title'])); ?>', '<?php echo addslashes(htmlspecialchars($vid['desc'])); ?>')">
                            <div class="event-card-thumb">
                                <video muted loop playsinline class="card-video-loop">
                                    <source src="<?php echo htmlspecialchars($vid['file_path']); ?>" type="video/mp4">
                                </video>
                                <div class="card-video-overlay">
                                    <span class="card-play-icon"><i class="fas fa-play"></i></span>
                                    <span class="audio-badge"><i class="fas fa-volume-up"></i> Full Screen &amp; Audio</span>
                                </div>
                                <span class="media-type-badge video"><i class="fas fa-video"></i> Video</span>
                            </div>
                            <div class="event-card-content">
                                <div>
                                    <span class="event-category-tag sports"><?php echo htmlspecialchars($vid['event_title']); ?></span>
                                    <h4><?php echo htmlspecialchars($vid['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($vid['desc'] ?: 'College event highlight video.'); ?></p>
                                </div>
                                <span class="click-hint"><i class="fas fa-expand"></i> Click full screen with audio</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Official Posters & Photo Gallery Section -->
            <?php if (!empty($all_photos)): ?>
                <div class="event-gallery-subhead">
                    <h3>Official Posters &amp; Event Photos</h3>
                    <p>Click any poster or photo to inspect in high-resolution detail</p>
                </div>

                <div class="event-cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); margin-bottom: 50px;">
                    <?php foreach ($all_photos as $pic): ?>
                        <div class="event-card poster-card" onclick="openEventImageModal('<?php echo htmlspecialchars($pic['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($pic['title'])); ?>')">
                            <div class="event-card-thumb" style="height: 240px;">
                                <img src="<?php echo htmlspecialchars($pic['file_path']); ?>" alt="<?php echo htmlspecialchars($pic['title']); ?>" loading="lazy" onerror="this.src='assets/events/tcek-fresher.jpg'">
                                <div class="card-image-overlay">
                                    <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                                    <span class="zoom-badge">View Full Image</span>
                                </div>
                                <span class="media-type-badge image"><i class="fas fa-image"></i> Photo</span>
                            </div>
                            <div class="event-card-content">
                                <div>
                                    <span class="event-category-tag cultural"><?php echo htmlspecialchars($pic['event_title']); ?></span>
                                    <h4><?php echo htmlspecialchars($pic['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($pic['desc'] ?: 'Event photo/poster.'); ?></p>
                                </div>
                                <span class="click-hint"><i class="fas fa-search-plus"></i> View High-Res Image</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Complete Events Schedule & Details Section -->
            <div class="event-gallery-subhead">
                <h3>Campus Events Schedule &amp; Activity Details</h3>
                <p>Complete official schedule with dedicated photo &amp; video records</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 50px;">
                <?php foreach ($db_events as $ev): ?>
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column; gap: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <span style="display: inline-block; background: rgba(0, 184, 148, 0.1); color: #009473; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 999px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="far fa-calendar-check"></i> <?php echo htmlspecialchars($ev['event_date']); ?>
                                </span>
                                <h3 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 6px;"><?php echo htmlspecialchars($ev['title']); ?></h3>
                                <div style="display: flex; gap: 16px; font-size: 13px; color: #64748b; font-weight: 500; flex-wrap: wrap;">
                                    <span><i class="far fa-clock" style="color: #00b894;"></i> <?php echo htmlspecialchars($ev['event_time'] ?: '10:00 AM'); ?></span>
                                    <span><i class="fas fa-map-marker-alt" style="color: #00b894;"></i> <?php echo htmlspecialchars($ev['venue'] ?? 'Trinity Campus'); ?></span>
                                </div>
                            </div>
                            <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 999px;">
                                <i class="fas fa-photo-video"></i> <?php echo count($ev['media_items'] ?? []); ?> Attached Media
                            </span>
                        </div>

                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($ev['description'] ?: 'Join Trinity students and faculty for this vibrant campus gathering.'); ?>
                        </p>

                        <!-- Attached Media Row for this specific Event -->
                        <?php if (!empty($ev['media_items'])): ?>
                            <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; margin-top: 4px;">
                                <div style="font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                                    Event Media &amp; Details:
                                </div>
                                <div style="display: flex; gap: 12px; overflow-x: auto; padding-bottom: 6px;">
                                    <?php foreach ($ev['media_items'] as $item): ?>
                                        <?php if ($item['media_type'] === 'video'): ?>
                                            <div style="flex: 0 0 160px; height: 105px; border-radius: 10px; overflow: hidden; position: relative; cursor: pointer; background: #000; box-shadow: 0 2px 6px rgba(0,0,0,0.1);" onclick="openEventVideoModal('<?php echo htmlspecialchars($item['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($item['media_title'])); ?>', '<?php echo addslashes(htmlspecialchars($item['media_description'])); ?>')">
                                                <video src="<?php echo htmlspecialchars($item['file_path']); ?>" muted style="width: 100%; height: 100%; object-fit: cover;"></video>
                                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.35); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff;">
                                                    <i class="fas fa-play-circle" style="font-size: 24px; color: #00b894;"></i>
                                                    <span style="font-size: 10.5px; font-weight: 600; margin-top: 4px; padding: 0 6px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;"><?php echo htmlspecialchars($item['media_title']); ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div style="flex: 0 0 160px; height: 105px; border-radius: 10px; overflow: hidden; position: relative; cursor: pointer; background: #f8fafc; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.05);" onclick="openEventImageModal('<?php echo htmlspecialchars($item['file_path']); ?>', '<?php echo addslashes(htmlspecialchars($item['media_title'])); ?>')">
                                                <img src="<?php echo htmlspecialchars($item['file_path']); ?>" alt="<?php echo htmlspecialchars($item['media_title']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='assets/events/tcek-fresher.jpg'">
                                                <div style="position: absolute; bottom: 0; inset-inline: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 4px 6px; color: #fff; font-size: 10.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo htmlspecialchars($item['media_title']); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Organizing Committees Banner -->
            <div class="rnd-framework-section" style="padding: 32px 28px; margin-bottom: 40px;">
                <div class="rnd-framework-header" style="margin-bottom: 24px;">
                    <span class="rnd-framework-pill"><i class="fas fa-users-cog"></i> Event Organization</span>
                    <h2 style="font-size: 24px;">Organized Under Institutional Leadership</h2>
                    <p>Conducting campus activities with sportsmanship, discipline, and creative excellence.</p>
                </div>
                <div class="rnd-framework-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="framework-card" style="align-items: center; text-align: center;">
                        <div class="framework-icon" style="color: #4f46e5;"><i class="fas fa-users"></i></div>
                        <h4>College Management</h4>
                        <p>Providing state-of-the-art campus infrastructure, sponsorships, and encouragement.</p>
                    </div>
                    <div class="framework-card" style="align-items: center; text-align: center;">
                        <div class="framework-icon" style="color: #00b894;"><i class="fas fa-graduation-cap"></i></div>
                        <h4>Director – Academics</h4>
                        <p>Harmonizing academic rigor with holistic co-curricular vibrancy.</p>
                    </div>
                    <div class="framework-card" style="align-items: center; text-align: center;">
                        <div class="framework-icon" style="color: #e11d48;"><i class="fas fa-user-tie"></i></div>
                        <h4>Principal &amp; Deans</h4>
                        <p>Directing faculty advisors, student coordinators, and overall event execution.</p>
                    </div>
                    <div class="framework-card" style="align-items: center; text-align: center;">
                        <div class="framework-icon" style="color: #f59e0b;"><i class="fas fa-trophy"></i></div>
                        <h4>Sports &amp; Cultural Committees</h4>
                        <p>Managing tournaments, judging panels, rehearsals, and celebration logistics.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Fullscreen Video & Audio Player Modal (Light Theme) -->
    <div id="event-video-modal" class="event-media-modal" onclick="closeEventVideoModal(event)">
        <div class="event-video-container" onclick="event.stopPropagation()">
            <div class="event-modal-topbar">
                <div class="event-modal-title-wrap">
                    <span class="modal-live-tag"><i class="fas fa-circle"></i> NOW PLAYING WITH AUDIO</span>
                    <h4 id="event-modal-title">Event Video</h4>
                </div>
                <div class="event-modal-actions">
                    <button type="button" class="btn-modal-fullscreen" onclick="toggleNativeFullscreen()" title="Toggle Fullscreen (F)">
                        <i class="fas fa-expand"></i> <span>Full Screen</span>
                    </button>
                    <button type="button" class="btn-modal-close" onclick="closeEventVideoModal(null)" aria-label="Close modal">&times;</button>
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
            <button type="button" class="event-image-close" onclick="closeEventImageModal(null)" aria-label="Close">&times;</button>
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
                video.muted = false; // ENABLE AUDIO
                video.volume = 1.0;  // FULL VOLUME
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                const playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function(err) {
                        console.log('Autoplay audio fallback:', err);
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

                    if (document.fullscreenElement) {
                        document.exitFullscreen().catch(function() {});
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
                    target.requestFullscreen().catch(function() {});
                } else if (target.webkitRequestFullscreen) {
                    target.webkitRequestFullscreen();
                } else if (video && video.webkitEnterFullscreen) {
                    video.webkitEnterFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(function() {});
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

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
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

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
