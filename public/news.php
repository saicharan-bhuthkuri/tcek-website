<?php
$page = 'news';
@include_once __DIR__ . '/backend/config/database.php';
@include_once __DIR__ . '/backend/crud.php';
$all_news = function_exists('get_news') ? get_news(100) : [];

// Extract unique sources for filter pills
$sources = [];
foreach ($all_news as $n) {
    if (!empty($n['source'])) {
        $src = trim($n['source']);
        if (!in_array($src, $sources)) {
            $sources[] = $src;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>News &amp; Media Coverage - Trinity College of Engineering &amp; Technology</title>
    <meta name="description" content="Official newspaper clippings, print media highlights, and regional press coverage of Trinity College of Engineering and Technology (TCEK), Peddapalli.">
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dedicated News & Press Page Styles */
        .news-page-hero {
            background: linear-gradient(135deg, #0b1e36 0%, #1e3a5f 50%, #0f766e 100%);
            color: #ffffff;
            padding: 70px 0 50px;
            position: relative;
            overflow: hidden;
        }
        .news-page-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(0, 184, 148, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .news-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #55efc4;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        .news-page-hero h1 {
            font-size: 38px;
            font-weight: 800;
            margin: 0 0 12px;
            letter-spacing: -0.5px;
            color: #ffffff;
        }
        .news-page-hero p.hero-subtitle {
            font-size: 16px;
            color: #cbd5e1;
            max-width: 780px;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .news-breadcrumbs {
            font-size: 13px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .news-breadcrumbs a {
            color: #55efc4;
            text-decoration: none;
            transition: color 0.2s;
        }
        .news-breadcrumbs a:hover {
            color: #ffffff;
        }

        /* Stats Strip */
        .news-stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin: -30px auto 40px;
            position: relative;
            z-index: 10;
        }
        .news-stat-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .news-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px -5px rgba(15, 23, 42, 0.1);
        }
        .news-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .icon-mint { background: #e6fcf5; color: #00b894; }
        .icon-blue { background: #eff6ff; color: #2563eb; }
        .icon-amber { background: #fffbeb; color: #d97706; }
        .icon-purple { background: #f5f3ff; color: #7c3aed; }
        .news-stat-details h4 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
        }
        .news-stat-details span {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Filter & Search Bar */
        .news-filter-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 32px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .news-filter-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }
        .news-search-box {
            position: relative;
            flex: 1;
            min-width: 280px;
            max-width: 520px;
        }
        .news-search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }
        .news-search-box input {
            width: 100%;
            height: 44px;
            padding: 10px 14px 10px 40px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .news-search-box input:focus {
            border-color: #00b894;
            box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.15);
        }
        .news-count-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            background: #f1f5f9;
            padding: 7px 16px;
            border-radius: 999px;
        }

        .news-sources-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .news-source-btn {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .news-source-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .news-source-btn.active {
            background: #00b894;
            color: #ffffff;
            border-color: #00b894;
            box-shadow: 0 3px 8px rgba(0, 184, 148, 0.25);
        }

        /* News Cards Grid */
        .news-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 50px;
        }
        .news-card-item {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            cursor: pointer;
        }
        .news-card-item:hover {
            transform: translateY(-5px);
            border-color: #00b894;
            box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.1);
        }
        .news-card-thumb-wrap {
            height: 200px;
            position: relative;
            background: #ffffff;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
        }
        .news-card-thumb-wrap img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.35s ease;
        }
        .news-card-item:hover .news-card-thumb-wrap img {
            transform: scale(1.04);
        }
        .news-card-hover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .news-card-item:hover .news-card-hover-overlay {
            opacity: 1;
        }

        .news-card-body {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .news-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }
        .news-source-tag {
            font-size: 11.5px;
            font-weight: 700;
            color: #00b894;
            background: #e6fcf5;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .news-date-text {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .news-headline-title {
            font-size: 15.5px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
            margin: 0 0 10px;
        }
        .news-excerpt-text {
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        .news-card-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-view-clipping {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #00b894;
            font-size: 13px;
            font-weight: 700;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: gap 0.2s;
        }
        .news-card-item:hover .btn-view-clipping {
            gap: 9px;
            color: #059669;
        }

        /* Lightbox modal styles */
        .news-lightbox-box {
            max-width: 900px;
            width: 95%;
            max-height: 92vh;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }
        .news-lightbox-img-wrap {
            background: #0f172a;
            max-height: 62vh;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .news-lightbox-img-wrap img {
            max-width: 100%;
            max-height: 58vh;
            object-fit: contain;
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }
        .news-lightbox-info-bar {
            padding: 16px 24px 20px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
        }
        .news-lightbox-info-meta {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 6px;
        }
        .news-lightbox-info-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px;
            line-height: 1.4;
        }
        .news-lightbox-info-desc {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
            white-space: pre-line;
        }

        @media (max-width: 768px) {
            .news-page-hero h1 { font-size: 28px; }
            .news-page-hero { padding: 50px 0 35px; }
            .news-cards-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body class="rnd-page-body">
    <?php include 'header.php'; ?>

    <!-- Hero Header -->
    <section class="news-page-hero">
        <div class="container">
            <div class="news-hero-badge">
                <i class="fas fa-newspaper"></i> Press &amp; Media Highlights 2026
            </div>
            <h1>TCEK In Regional &amp; National Press</h1>
            <p class="hero-subtitle">
                Explore authentic newspaper coverage, innovation hackathon achievements, academic excellence, and landmark UGC autonomous milestones of Trinity College of Engineering &amp; Technology reported in prominent regional and state daily newspapers.
            </p>
            <div class="news-breadcrumbs">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span>News &amp; Media Coverage</span>
            </div>
        </div>
    </section>

    <!-- Main Content Container -->
    <main class="rnd-main-content">
        <div class="container">

            <!-- Stats Strip -->
            <div class="news-stats-strip">
                <div class="news-stat-card">
                    <div class="news-stat-icon icon-mint">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="news-stat-details">
                        <h4><?php echo count($all_news); ?></h4>
                        <span>Press Clippings</span>
                    </div>
                </div>
                <div class="news-stat-card">
                    <div class="news-stat-icon icon-blue">
                        <i class="fas fa-broadcast-tower"></i>
                    </div>
                    <div class="news-stat-details">
                        <h4><?php echo count($sources); ?>+</h4>
                        <span>Publications</span>
                    </div>
                </div>
                <div class="news-stat-card">
                    <div class="news-stat-icon icon-amber">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="news-stat-details">
                        <h4>168+</h4>
                        <span>Innovations Featured</span>
                    </div>
                </div>
                <div class="news-stat-card">
                    <div class="news-stat-icon icon-purple">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="news-stat-details">
                        <h4>UGC &amp; JNTUH</h4>
                        <span>Autonomous Status</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Filter & Search Bar -->
            <div class="news-filter-bar">
                <div class="news-filter-top">
                    <div class="news-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="newsSearchInput" placeholder="Search headline, newspaper, or keyword..." onkeyup="filterNews()">
                    </div>
                    <div class="news-count-pill">
                        <i class="fas fa-file-alt" style="color: #00b894;"></i>
                        <span id="newsDisplayCount"><?php echo count($all_news); ?></span> Newspaper Articles
                    </div>
                </div>

                <!-- Publication Source Buttons -->
                <div class="news-sources-pills">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Publications:</span>
                    <button type="button" class="news-source-btn active" onclick="filterBySource('all', this)">
                        <i class="fas fa-globe"></i> All Publications
                    </button>
                    <?php foreach ($sources as $src): ?>
                        <button type="button" class="news-source-btn" onclick="filterBySource('<?php echo htmlspecialchars($src, ENT_QUOTES); ?>', this)">
                            <?php echo htmlspecialchars($src); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- News Cards Grid -->
            <?php if (empty($all_news)): ?>
                <div style="background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:60px 20px; text-align:center; margin-bottom:50px;">
                    <div style="width:70px; height:70px; border-radius:50%; background:#f1f5f9; color:#94a3b8; display:inline-flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:14px;">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3 style="color:#0f172a; margin:0 0 6px;">No Newspaper Clippings Published Yet</h3>
                    <p style="color:#64748b; font-size:14px; margin:0;">Newspaper news articles will appear here automatically once uploaded via the admin panel.</p>
                </div>
            <?php else: ?>
                <div class="news-cards-grid" id="newsGridContainer">
                    <?php foreach ($all_news as $item): 
                        $raw_img = !empty($item['image_path']) ? $item['image_path'] : 'assets/Gallery/paper1.jpg';
                        $img_src = htmlspecialchars($raw_img);
                        $title   = htmlspecialchars($item['title']);
                        $source  = !empty($item['source']) ? htmlspecialchars($item['source']) : 'Press';
                        $date_raw= !empty($item['publish_date']) ? $item['publish_date'] : '';
                        $date_fmt= !empty($date_raw) ? date('M d, Y', strtotime($date_raw)) : '';
                        $desc    = !empty($item['description']) ? $item['description'] : (!empty($item['summary']) ? $item['summary'] : '');
                        $search_corpus = strtolower($title . ' ' . $source . ' ' . $desc . ' ' . $date_fmt);
                    ?>
                        <div class="news-card-item" 
                             data-source="<?php echo htmlspecialchars($source, ENT_QUOTES); ?>" 
                             data-search="<?php echo htmlspecialchars($search_corpus, ENT_QUOTES); ?>"
                             onclick="openNewsLightbox('<?php echo $img_src; ?>', '<?php echo addslashes($title); ?>', '<?php echo addslashes($source); ?>', '<?php echo addslashes($date_fmt); ?>', '<?php echo addslashes($desc); ?>')">
                            
                            <!-- Thumbnail Area -->
                            <div class="news-card-thumb-wrap">
                                <img src="<?php echo $img_src; ?>" alt="<?php echo $title; ?>" loading="lazy" onerror="this.src='assets/Gallery/paper1.jpg'">
                                <div class="news-card-hover-overlay">
                                    <span class="zoom-pill"><i class="fas fa-search-plus"></i> View Article</span>
                                </div>
                            </div>

                            <!-- Body Area -->
                            <div class="news-card-body">
                                <div class="news-meta-row">
                                    <span class="news-source-tag"><?php echo $source; ?></span>
                                    <?php if ($date_fmt): ?>
                                        <span class="news-date-text"><i class="far fa-calendar-alt" style="color:#00b894;"></i> <?php echo $date_fmt; ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="news-headline-title"><?php echo $title; ?></h3>
                                <p class="news-excerpt-text">
                                    <?php echo !empty($desc) ? htmlspecialchars($desc) : 'Official regional newspaper coverage regarding Trinity College of Engineering & Technology.'; ?>
                                </p>
                                <div class="news-card-footer">
                                    <button type="button" class="btn-view-clipping">
                                        <span>Read Full Clipping</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty state for filtered search -->
                <div id="newsNoMatches" style="display:none; background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:50px 20px; text-align:center; margin-bottom:50px;">
                    <div style="width:60px; height:60px; border-radius:50%; background:#fef2f2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center; font-size:24px; margin-bottom:12px;">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 style="color:#0f172a; margin:0 0 6px;">No Matching Articles Found</h4>
                    <p style="color:#64748b; font-size:13.5px; margin:0;">Try adjusting your search terms or publication filter.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- News Zoom Lightbox Modal -->
    <div id="news-lightbox-modal" class="news-lightbox" onclick="closeNewsLightbox(event)">
        <div class="news-lightbox-box">
            <button type="button" class="news-lightbox-close" onclick="closeNewsLightbox(event)" aria-label="Close modal">&times;</button>
            <div class="news-lightbox-img-wrap">
                <img id="news-lightbox-target" src="" alt="Zoomed Newspaper Clipping">
            </div>
            <div class="news-lightbox-info-bar">
                <div class="news-lightbox-info-meta">
                    <span id="modal-news-source" class="news-source-tag"></span>
                    <span id="modal-news-date" class="news-date-text"></span>
                </div>
                <h3 id="modal-news-title" class="news-lightbox-info-title"></h3>
                <p id="modal-news-desc" class="news-lightbox-info-desc"></p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        let currentActiveSource = 'all';

        function openNewsLightbox(src, title, source, date, desc) {
            const modal = document.getElementById('news-lightbox-modal');
            const img = document.getElementById('news-lightbox-target');
            const titleEl = document.getElementById('modal-news-title');
            const sourceEl = document.getElementById('modal-news-source');
            const dateEl = document.getElementById('modal-news-date');
            const descEl = document.getElementById('modal-news-desc');

            if (modal && img) {
                img.src = src;
                img.alt = title || 'Newspaper Clipping';
                if (titleEl) titleEl.textContent = title || '';
                if (sourceEl) {
                    sourceEl.textContent = source || 'Press & Media';
                    sourceEl.style.display = source ? 'inline-block' : 'none';
                }
                if (dateEl) {
                    dateEl.innerHTML = date ? '<i class="far fa-calendar-alt" style="color:#00b894;"></i> ' + date : '';
                }
                if (descEl) {
                    descEl.textContent = desc || '';
                    descEl.style.display = desc ? 'block' : 'none';
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

        function filterBySource(sourceName, btnElement) {
            currentActiveSource = sourceName;
            document.querySelectorAll('.news-source-btn').forEach(b => b.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');
            filterNews();
        }

        function filterNews() {
            const query = (document.getElementById('newsSearchInput').value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.news-card-item');
            const noMatches = document.getElementById('newsNoMatches');
            const countEl = document.getElementById('newsDisplayCount');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardSource = (card.getAttribute('data-source') || '').toLowerCase();
                const cardSearch = (card.getAttribute('data-search') || '').toLowerCase();

                const matchesSource = (currentActiveSource === 'all') || (cardSource === currentActiveSource.toLowerCase());
                const matchesQuery = (!query) || (cardSearch.indexOf(query) !== -1);

                if (matchesSource && matchesQuery) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (countEl) countEl.textContent = visibleCount;
            if (noMatches) {
                noMatches.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
            }
        }
    </script>
</body>
</html>
