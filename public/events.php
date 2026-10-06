<?php
$page = 'events';
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

            <!-- Event Schedule Quick Ribbon -->
            <div class="event-schedule-ribbon">
                <div class="event-ribbon-item">
                    <span class="ribbon-date">1 – 8 OCT</span>
                    <div class="ribbon-info">
                        <strong>College Sports Week</strong>
                        <span>Cricket, Kabaddi, Badminton &amp; Athletics</span>
                    </div>
                </div>
                <div class="event-ribbon-item">
                    <span class="ribbon-date">9 OCT</span>
                    <div class="ribbon-info">
                        <strong>Flash Mob</strong>
                        <span>High-Voltage Dance Showcase</span>
                    </div>
                </div>
                <div class="event-ribbon-item active-event">
                    <span class="ribbon-date">12 OCT</span>
                    <div class="ribbon-info">
                        <strong>Freshers Aarambh 2K26</strong>
                        <span>Music, Dance, Celebrations &amp; Welcoming 1st Years</span>
                    </div>
                </div>
                <div class="event-ribbon-item">
                    <span class="ribbon-date">13 OCT</span>
                    <div class="ribbon-info">
                        <strong>Traditional &amp; Bathukamma</strong>
                        <span>Heritage, Flowers &amp; Cultural Fest</span>
                    </div>
                </div>
            </div>

            <!-- Featured Hero Spotlight Card: Freshers Aarambh 2K26 -->
            <div class="event-spotlight-card">
                <!-- Left: Featured Video with Audio & Fullscreen Prompt -->
                <div class="spotlight-media" onclick="openEventVideoModal('assets/events/freshers.mp4', 'Freshers Aarambh 2K26 Celebration', 'Official highlight video of Freshers Aarambh 2K26 at Trinity College of Engineering &amp; Technology. A new beginning, a brighter tomorrow!')">
                    <div class="spotlight-video-preview">
                        <video class="bg-preview-vid" muted autoplay loop playsinline poster="assets/events/tcek-fresher.jpg">
                            <source src="assets/events/freshers.mp4" type="video/mp4">
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
                            <span class="spotlight-tag"><i class="fas fa-fire"></i> Mega Event</span>
                            <span class="spotlight-date"><i class="far fa-calendar-alt"></i> 12 October 2026 · Monday</span>
                        </div>
                        <h3>Freshers Aarambh 2K26</h3>
                        <p class="spotlight-tagline">"A New Beginning • A Brighter Tomorrow • Let the Journey Begin..."</p>
                        <p class="spotlight-desc">
                            Welcoming the incoming batch of engineers and technocrats to the Trinity family with electrifying music, dazzling dance performances, interactive fun games, and unforgettable memories!
                        </p>

                        <div class="spotlight-perks-grid">
                            <div class="perk-chip"><i class="fas fa-music"></i> Live Music</div>
                            <div class="perk-chip"><i class="fas fa-shoe-prints"></i> Dance &amp; Flash Mob</div>
                            <div class="perk-chip"><i class="fas fa-theater-masks"></i> Fun Games</div>
                            <div class="perk-chip"><i class="fas fa-users"></i> Meet New Friends</div>
                            <div class="perk-chip"><i class="fas fa-trophy"></i> Exciting Awards</div>
                            <div class="perk-chip"><i class="fas fa-camera"></i> Memories Forever</div>
                        </div>
                    </div>

                    <div class="spotlight-cta-row">
                        <button type="button" class="btn-event-play" onclick="openEventVideoModal('assets/events/freshers.mp4', 'Freshers Aarambh 2K26 Celebration', 'Official highlight video of Freshers Aarambh 2K26 at Trinity College of Engineering &amp; Technology. A new beginning, a brighter tomorrow!')">
                            <i class="fas fa-expand"></i> <span>Click Full Screen with Audio</span>
                        </button>
                        <button type="button" class="btn-event-poster" onclick="openEventImageModal('assets/events/tcek-fresher.jpg', 'Freshers Aarambh 2K26 Official Event Poster')">
                            <i class="fas fa-image"></i> <span>View Official Poster</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video Highlights Section -->
            <div class="event-gallery-subhead">
                <h3>Event &amp; Tournament Highlights (HD Video with Audio)</h3>
                <p>Click any video card to launch the player in full screen with high definition sound</p>
            </div>

            <!-- 3-Column Video Cards Grid -->
            <div class="event-cards-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 50px;">
                <!-- Card 1: Cricket Campaign Video -->
                <div class="event-card video-card" onclick="openEventVideoModal('assets/events/cricket-campaigns.mp4', 'AIML &amp; CSE Cricket Campaigns', 'College Sports Week 2026 cricket championship clashes between Department of AIML and Department of CSE.')">
                    <div class="event-card-thumb">
                        <video muted loop playsinline class="card-video-loop">
                            <source src="assets/events/cricket-campaigns.mp4" type="video/mp4">
                        </video>
                        <div class="card-video-overlay">
                            <span class="card-play-icon"><i class="fas fa-play"></i></span>
                            <span class="audio-badge"><i class="fas fa-volume-up"></i> Full Screen &amp; Audio</span>
                        </div>
                        <span class="media-type-badge video"><i class="fas fa-video"></i> Video</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag sports">College Sports Week</span>
                            <h4>AIML &amp; CSE Cricket Campaigns</h4>
                            <p>High-stakes inter-departmental cricket showdowns, thrilling match boundaries, and celebratory cheers.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-expand"></i> Click full screen with audio</span>
                    </div>
                </div>

                <!-- Card 2: Kabaddi Wins Video -->
                <div class="event-card video-card" onclick="openEventVideoModal('assets/events/kabaddi-wins.mp4', 'Kabaddi Championship Wins', 'Sensational raid points, tackles, and trophy celebration in the annual college Kabaddi tournament.')">
                    <div class="event-card-thumb">
                        <video muted loop playsinline class="card-video-loop">
                            <source src="assets/events/kabaddi-wins.mp4" type="video/mp4">
                        </video>
                        <div class="card-video-overlay">
                            <span class="card-play-icon"><i class="fas fa-play"></i></span>
                            <span class="audio-badge"><i class="fas fa-volume-up"></i> Full Screen &amp; Audio</span>
                        </div>
                        <span class="media-type-badge video"><i class="fas fa-video"></i> Video</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag sports">College Sports Week</span>
                            <h4>Kabaddi Championship Wins</h4>
                            <p>Super-tackles, lightning raids, and championship victory celebrations by Trinity athletes.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-expand"></i> Click full screen with audio</span>
                    </div>
                </div>

                <!-- Card 3: Autonomous Status Celebration Video -->
                <div class="event-card video-card" onclick="openEventVideoModal('assets/College Event/autonomus.mp4', 'UGC Autonomous Status Felicitation Ceremony', 'Grand celebration on UGC granting Autonomous Status to Trinity College of Engineering and Technology.')">
                    <div class="event-card-thumb">
                        <video muted loop playsinline class="card-video-loop">
                            <source src="assets/College Event/autonomus.mp4" type="video/mp4">
                        </video>
                        <div class="card-video-overlay">
                            <span class="card-play-icon"><i class="fas fa-play"></i></span>
                            <span class="audio-badge"><i class="fas fa-volume-up"></i> Full Screen &amp; Audio</span>
                        </div>
                        <span class="media-type-badge video"><i class="fas fa-video"></i> Video</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag fest">Milestone Event</span>
                            <h4>Autonomous Status Felicitation</h4>
                            <p>Special institutional felicitation ceremonies celebrating UGC Autonomous conferment with faculty and guests.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-expand"></i> Click full screen with audio</span>
                    </div>
                </div>
            </div>

            <!-- Official Posters & Schedule Section -->
            <div class="event-gallery-subhead">
                <h3>Official Posters &amp; Event Schedules</h3>
                <p>Click any poster to inspect in high-resolution detail</p>
            </div>

            <div class="event-cards-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 50px;">
                <!-- Poster 1: Freshers Aarambh 2K26 Poster -->
                <div class="event-card poster-card" onclick="openEventImageModal('assets/events/tcek-fresher.jpg', 'Freshers Aarambh 2K26 Official Event Poster')">
                    <div class="event-card-thumb" style="height: 240px;">
                        <img src="assets/events/tcek-fresher.jpg" alt="Freshers Aarambh 2K26 Poster" loading="lazy">
                        <div class="card-image-overlay">
                            <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                            <span class="zoom-badge">View Full Poster</span>
                        </div>
                        <span class="media-type-badge image"><i class="fas fa-image"></i> Poster</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag cultural">Aarambh 2K26</span>
                            <h4>Freshers Aarambh 2K26</h4>
                            <p>Official banner and schedule for 12 October 2026 Monday.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-search-plus"></i> View High-Res Poster</span>
                    </div>
                </div>

                <!-- Poster 2: Sports & Cultural Week Schedule Poster -->
                <div class="event-card poster-card" onclick="openEventImageModal('assets/events/tcek-poster.jpg', 'College Sports &amp; Cultural Week 2026 Schedule &amp; Poster')">
                    <div class="event-card-thumb" style="height: 240px;">
                        <img src="assets/events/tcek-poster.jpg" alt="Sports & Cultural Week 2026 Poster" loading="lazy">
                        <div class="card-image-overlay">
                            <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                            <span class="zoom-badge">View Full Schedule</span>
                        </div>
                        <span class="media-type-badge image"><i class="fas fa-image"></i> Schedule</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag fest">Mega Fest 2026</span>
                            <h4>Sports &amp; Cultural Week</h4>
                            <p>Complete 2-week schedule across sports, flash mob and traditional days.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-search-plus"></i> View High-Res Schedule</span>
                    </div>
                </div>

                <!-- Poster 3: Graduation & Convocation Caps -->
                <div class="event-card poster-card" onclick="openEventImageModal('assets/College Event/caps.jpg', 'Annual Convocation &amp; Graduation Day Ceremony')">
                    <div class="event-card-thumb" style="height: 240px;">
                        <img src="assets/College Event/caps.jpg" alt="Graduation Day Ceremony" loading="lazy">
                        <div class="card-image-overlay">
                            <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                            <span class="zoom-badge">View Photo</span>
                        </div>
                        <span class="media-type-badge image"><i class="fas fa-camera"></i> Graduation</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag cultural">Convocation</span>
                            <h4>Graduation Ceremony</h4>
                            <p>Graduating engineers celebrating academic degrees and milestones.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-search-plus"></i> View Full Photo</span>
                    </div>
                </div>

                <!-- Poster 4: Felicitation Honors -->
                <div class="event-card poster-card" onclick="openEventImageModal('assets/College Event/feli1.jpg', 'Merit Felicitation &amp; Award Ceremony')">
                    <div class="event-card-thumb" style="height: 240px;">
                        <img src="assets/College Event/feli1.jpg" alt="Merit Felicitation Ceremony" loading="lazy">
                        <div class="card-image-overlay">
                            <span class="card-zoom-icon"><i class="fas fa-search-plus"></i></span>
                            <span class="zoom-badge">View Photo</span>
                        </div>
                        <span class="media-type-badge image"><i class="fas fa-trophy"></i> Awards</span>
                    </div>
                    <div class="event-card-content">
                        <div>
                            <span class="event-category-tag fest">Honors</span>
                            <h4>Merit Felicitation</h4>
                            <p>Recognizing outstanding student achievers, rank holders and sports stars.</p>
                        </div>
                        <span class="click-hint"><i class="fas fa-search-plus"></i> View Full Photo</span>
                    </div>
                </div>
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
