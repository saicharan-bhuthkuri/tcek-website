<!DOCTYPE html>
<html lang="en">

<head>
    <title>Photo Gallery - Trinity College of Engineering & Technology</title>
    <?php include 'head.php'; ?>
</head>

<body>
    <?php 
    $page = 'gallery';
    include 'header.php'; 
    @include_once __DIR__ . '/backend/config/database.php';
    @include_once __DIR__ . '/backend/crud.php';
    $dyn_photos = function_exists('get_gallery_items') ? get_gallery_items(null, 50) : [];
    $dyn_news   = function_exists('get_news') ? get_news(50) : [];
    ?>

    <main>
        <!-- Gallery Hero Header -->
        <section class="gallery-hero-header">
            <div class="container">
                <span class="gallery-top-pill">
                    <span class="pulse-indicator"></span> Visual Chronicles &amp; Campus Life
                </span>
                <h1>PHOTO GALLERY</h1>
                <p>Explore memorable moments of academic brilliance, cultural festivals, campus infrastructure &amp; regional press coverage at TCEK</p>

                <!-- Interactive Filter Tabs -->
                <div class="gallery-filter-tabs">
                    <button type="button" class="gallery-tab-btn active" onclick="filterGallery('all', this)">
                        <i class="fas fa-th-large"></i> All Moments (<span id="count-all">0</span>)
                    </button>
                    <button type="button" class="gallery-tab-btn" onclick="filterGallery('events', this)">
                        <i class="fas fa-calendar-star"></i> Events &amp; Celebrations (<span id="count-events">0</span>)
                    </button>
                    <button type="button" class="gallery-tab-btn" onclick="filterGallery('campus', this)">
                        <i class="fas fa-university"></i> Campus &amp; Labs (<span id="count-campus">0</span>)
                    </button>
                    <button type="button" class="gallery-tab-btn" onclick="filterGallery('milestones', this)">
                        <i class="fas fa-award"></i> Academic Milestones (<span id="count-milestones">0</span>)
                    </button>
                    <button type="button" class="gallery-tab-btn" onclick="filterGallery('press', this)">
                        <i class="fas fa-newspaper"></i> Press &amp; Media (<span id="count-press">0</span>)
                    </button>
                </div>
            </div>
        </section>

        <!-- Featured Spotlight Card -->
        <section class="gallery-spotlight-wrap">
            <div class="gallery-spotlight-card">
                <div class="gallery-spotlight-media" onclick="openGalleryModalBySrc('assets/College Event/caps.jpg')">
                    <img src="assets/College Event/caps.jpg" alt="Trinity College Convocation Ceremony">
                    <div class="gallery-spotlight-overlay">
                        <span class="gallery-spotlight-badge"><i class="fas fa-camera"></i> Featured Story</span>
                        <div class="gallery-spotlight-zoom-btn" title="Expand Photo">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                <div class="gallery-spotlight-content">
                    <span class="meta-tag" style="align-self: flex-start; margin-bottom: 12px;">
                        <i class="fas fa-graduation-cap"></i> Convocation &amp; Graduation Day
                    </span>
                    <h3>Celebrating Graduate Excellence — Convocation Ceremony at TCEK</h3>
                    <p>A triumphant celebration marking the graduation of our future technocrats. Guided by visionary faculty and world-class laboratory training, TCEK graduates step proudly into top global multinationals and premier research universities.</p>
                    <div class="gallery-spotlight-meta-row">
                        <div class="spotlight-stat">
                            <i class="fas fa-images"></i>
                            <span>50+ Archival Photos</span>
                        </div>
                        <div class="spotlight-stat">
                            <i class="fas fa-check-circle"></i>
                            <span>100% Student Participation</span>
                        </div>
                        <div class="spotlight-stat">
                            <i class="fas fa-award"></i>
                            <span>UGC Autonomous</span>
                        </div>
                    </div>
                    <button type="button" class="btn-spotlight-action" style="align-self: flex-start; border: none; cursor: pointer;" onclick="openGalleryModalBySrc('assets/College Event/caps.jpg')">
                        <i class="fas fa-expand"></i> View High-Res Photo
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Photo Grid Section -->
        <section class="gallery-section-body">
            <div class="gallery-modern-grid" id="gallery-grid-container">
                <?php if (!empty($dyn_photos)): ?>
                    <!-- Dynamic Uploaded Photos from GoDaddy MySQL & Storage -->
                    <?php foreach ($dyn_photos as $dp): 
                        $cat = htmlspecialchars($dp['category']);
                        $title = htmlspecialchars($dp['title']);
                        $path = htmlspecialchars($dp['file_path']);
                    ?>
                        <div class="gallery-modern-card" data-category="<?php echo $cat; ?>" onclick="openGalleryModal(this)">
                            <div class="gallery-card-thumb">
                                <span class="gallery-card-badge badge-event"><i class="fas fa-camera"></i> <?php echo ucfirst($cat); ?></span>
                                <img src="<?php echo $path; ?>" alt="<?php echo $title; ?>">
                                <div class="gallery-card-overlay">
                                    <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- ================= EVENTS & CELEBRATIONS ================= -->
                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-calendar-alt"></i> Event</span>
                        <img src="assets/College Event/feli1.jpg" alt="Grand Academic Felicitation Ceremony">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-calendar-alt"></i> Event</span>
                        <img src="assets/College Event/feli2.jpg" alt="Faculty & Student Honors Presentation">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-calendar-alt"></i> Event</span>
                        <img src="assets/College Event/all1.jpg" alt="College Assembly & Annual Gathering">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-music"></i> Cultural</span>
                        <img src="assets/Gallery/im.jpg" alt="Cultural Fest & Student Celebrations">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-tshirt"></i> Traditional</span>
                        <img src="assets/Gallery/im1.jpg" alt="Traditional Day Celebrations">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-running"></i> Sports</span>
                        <img src="assets/Gallery/im2.jpg" alt="Annual Sports Meet & Athletics">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="events" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-event"><i class="fas fa-users"></i> Campus Fest</span>
                        <img src="assets/Gallery/im3.jpg" alt="Campus Day Festival Celebrations">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <!-- ================= ACADEMIC MILESTONES ================= -->
                <div class="gallery-modern-card" data-category="milestones" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-milestone"><i class="fas fa-university"></i> UGC Milestone</span>
                        <img src="assets/Gallery/autonomous.jpg" alt="UGC Autonomous Status Announcement">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="milestones" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-milestone"><i class="fas fa-certificate"></i> Accreditation</span>
                        <img src="assets/Gallery/naac2.jpg" alt="NAAC B++ Grade Accreditation Recognition">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="milestones" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-milestone"><i class="fas fa-briefcase"></i> Placements</span>
                        <img src="assets/Gallery/infosys.jpg" alt="Infosys & Multi-Company Campus Drive">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <!-- ================= CAMPUS & LABS ================= -->
                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-laptop-code"></i> Tech Labs</span>
                        <img src="assets/Gallery/im4.jpg" alt="Advanced Computer Science Lab">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-microchip"></i> Electronics</span>
                        <img src="assets/Gallery/im5.jpg" alt="Electronics & VLSI Testing Workstation">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-book-reader"></i> Library</span>
                        <img src="assets/Gallery/im6.jpg" alt="Central Library & Knowledge Resource Center">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-tree"></i> Campus</span>
                        <img src="assets/Gallery/im8.jpg" alt="Main College Campus & Lawns">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-tools"></i> Workshops</span>
                        <img src="assets/Gallery/IMG-1.jpg" alt="Engineering Innovation Workshops">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-chalkboard-teacher"></i> Seminar Hall</span>
                        <img src="assets/Gallery/IMG-4.jpg" alt="Air-Conditioned Seminar Hall">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-desktop"></i> Smart Class</span>
                        <img src="assets/Gallery/IMG-5.jpg" alt="Smart Classrooms & Digital Projectors">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-futbol"></i> Grounds</span>
                        <img src="assets/Gallery/IMG-6.jpg" alt="Sports Grounds & Physical Education">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-lightbulb"></i> Project Expo</span>
                        <img src="assets/Gallery/IMG-7.jpg" alt="Student Technical Project Exhibition">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-theater-masks"></i> Auditorium</span>
                        <img src="assets/Gallery/IMG-8.jpg" alt="Central Auditorium & Cultural Stage">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <div class="gallery-modern-card" data-category="campus" onclick="openGalleryModal(this)">
                    <div class="gallery-card-thumb">
                        <span class="gallery-card-badge badge-campus"><i class="fas fa-landmark"></i> Administration</span>
                        <img src="assets/Gallery/IMG2.jpg" alt="College Administrative Complex">
                        <div class="gallery-card-overlay">
                            <span class="gallery-expand-pill"><i class="fas fa-search-plus"></i> Expand</span>
                        </div>
                    </div>
                </div>

                <!-- ================= PRESS & MEDIA CLIPPINGS ================= -->
                <?php if (!empty($dyn_news)): ?>
                    <?php foreach ($dyn_news as $p_item): 
                        $p_raw = !empty($p_item['image_path']) ? $p_item['image_path'] : 'assets/Gallery/paper1.jpg';
                        $p_title = htmlspecialchars($p_item['title']);
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
            </div>
        </section>
    </main>

    <!-- Interactive Lightbox Modal with Next/Prev Arrows -->
    <div id="gallery-lightbox-modal" class="gallery-modal" onclick="handleModalBackdropClick(event)">
        <div class="gallery-modal-content">
            <button type="button" class="gallery-modal-close" onclick="closeGalleryModal()" aria-label="Close modal">&times;</button>
            <button type="button" class="gallery-nav-arrow gallery-nav-prev" onclick="navigateGallery(-1)" aria-label="Previous Photo">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="gallery-nav-arrow gallery-nav-next" onclick="navigateGallery(1)" aria-label="Next Photo">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="gallery-modal-img-wrap">
                <img id="gallery-lightbox-img" src="" alt="Photo Preview">
            </div>
            <div class="gallery-modal-caption-bar">
                <div class="gallery-modal-caption-text" id="gallery-lightbox-caption">Photo Title</div>
                <div class="gallery-modal-counter" id="gallery-lightbox-counter">1 / 1</div>
            </div>
        </div>
    </div>

    <!-- Gallery Interaction Script -->
    <script>
        let currentFilter = 'all';
        let activeGalleryItems = [];
        let currentIndex = 0;

        function updateCategoryCounts() {
            const cards = document.querySelectorAll('.gallery-modern-card');
            const counts = { all: cards.length, events: 0, campus: 0, milestones: 0, press: 0 };
            cards.forEach(card => {
                const cat = card.getAttribute('data-category');
                if (counts[cat] !== undefined) counts[cat]++;
            });
            document.getElementById('count-all').textContent = counts.all;
            document.getElementById('count-events').textContent = counts.events;
            document.getElementById('count-campus').textContent = counts.campus;
            document.getElementById('count-milestones').textContent = counts.milestones;
            document.getElementById('count-press').textContent = counts.press;
        }

        function filterGallery(category, btnElement) {
            currentFilter = category;
            // Update active tab buttons
            document.querySelectorAll('.gallery-tab-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            const cards = document.querySelectorAll('.gallery-modern-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all' || cardCat === category) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            const emptyMsg = document.getElementById('gallery-empty-state');
            if (emptyMsg) {
                emptyMsg.style.display = (visibleCount === 0) ? 'flex' : 'none';
            }
            refreshActiveGalleryList();
        }

        function refreshActiveGalleryList() {
            const visibleCards = Array.from(document.querySelectorAll('.gallery-modern-card')).filter(card => card.style.display !== 'none');
            activeGalleryItems = visibleCards.map(card => {
                const img = card.querySelector('.gallery-card-thumb img');
                const title = card.querySelector('.gallery-card-info h4');
                return {
                    src: img ? img.src : '',
                    caption: title ? title.textContent : (img && img.alt ? img.alt : '')
                };
            });
        }

        function openGalleryModal(cardElement) {
            refreshActiveGalleryList();
            const img = cardElement.querySelector('.gallery-card-thumb img');
            if (!img) return;
            const targetSrc = img.src;
            currentIndex = activeGalleryItems.findIndex(item => item.src === targetSrc);
            if (currentIndex === -1) currentIndex = 0;
            displayCurrentModalImage();
        }

        function openGalleryModalBySrc(src) {
            activeGalleryItems = [{ src: src, caption: 'Trinity College Convocation Ceremony' }];
            currentIndex = 0;
            displayCurrentModalImage();
        }

        function displayCurrentModalImage() {
            if (activeGalleryItems.length === 0) return;
            const item = activeGalleryItems[currentIndex];
            const modal = document.getElementById('gallery-lightbox-modal');
            const modalImg = document.getElementById('gallery-lightbox-img');
            const caption = document.getElementById('gallery-lightbox-caption');
            const counter = document.getElementById('gallery-lightbox-counter');

            modalImg.src = item.src;
            caption.textContent = item.caption;
            counter.textContent = `${currentIndex + 1} / ${activeGalleryItems.length}`;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function navigateGallery(direction) {
            if (activeGalleryItems.length <= 1) return;
            currentIndex += direction;
            if (currentIndex < 0) currentIndex = activeGalleryItems.length - 1;
            if (currentIndex >= activeGalleryItems.length) currentIndex = 0;
            displayCurrentModalImage();
        }

        function closeGalleryModal() {
            const modal = document.getElementById('gallery-lightbox-modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function handleModalBackdropClick(e) {
            if (e.target.id === 'gallery-lightbox-modal') {
                closeGalleryModal();
            }
        }

        // Keyboard controls
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('gallery-lightbox-modal');
            if (modal && modal.classList.contains('active')) {
                if (e.key === 'Escape') closeGalleryModal();
                if (e.key === 'ArrowLeft') navigateGallery(-1);
                if (e.key === 'ArrowRight') navigateGallery(1);
            }
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            updateCategoryCounts();
            refreshActiveGalleryList();
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>