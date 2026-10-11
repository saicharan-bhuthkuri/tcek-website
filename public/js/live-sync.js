/**
 * TCEK Real-Time Website Synchronization Engine
 * Automatically syncs announcements, circulars, news, courses, hero slides,
 * gallery, and R&D rankings across all open tabs and browsers without manual refresh.
 *
 * Technologies:
 * 1. BroadcastChannel API for 0ms instant multi-tab communication.
 * 2. Adaptive Heartbeat Polling (3.5s active / 15s idle) against backend/sync.php.
 * 3. Smooth DOM cross-fade patching without disrupting user scroll or animations.
 */

(function () {
    'use strict';

    // Configuration & State
    const SYNC_CONFIG = {
        checkUrl: 'backend/sync.php?action=check',
        getSectionUrl: 'backend/sync.php?action=get_section',
        activeInterval: 3500,     // 3.5 seconds when tab is focused
        idleInterval: 15000,      // 15 seconds when tab is in background
        channelName: 'tcek_live_sync_bus'
    };

    let lastKnownTimestamp = 0;
    let pollTimer = null;
    let isChecking = false;
    let broadcastChannel = null;

    // Cross-Tab Broadcast Channel
    if (typeof BroadcastChannel !== 'undefined') {
        try {
            broadcastChannel = new BroadcastChannel(SYNC_CONFIG.channelName);
            broadcastChannel.onmessage = function (event) {
                if (event.data && event.data.type === 'sync_update') {
                    handleIncomingUpdate(event.data.modules || ['global'], event.data.timestamp);
                }
            };
        } catch (e) {
            console.warn('BroadcastChannel initialization failed, falling back to storage events:', e);
        }
    }

    // Storage Event Fallback for Older Browsers
    window.addEventListener('storage', function (e) {
        if (e.key === 'tcek_sync_ping' && e.newValue) {
            try {
                const data = JSON.parse(e.newValue);
                handleIncomingUpdate(data.modules || ['global'], data.timestamp);
            } catch (err) {}
        }
    });

    /**
     * Broadcast content update to other tabs (invoked by admin console or save operations)
     */
    function notifyUpdate(modules) {
        const modArray = Array.isArray(modules) ? modules : [modules];
        const now = Math.floor(Date.now() / 1000);
        const payload = { type: 'sync_update', modules: modArray, timestamp: now };

        if (broadcastChannel) {
            try { broadcastChannel.postMessage(payload); } catch (e) {}
        }
        try {
            localStorage.setItem('tcek_sync_ping', JSON.stringify({ modules: modArray, timestamp: now, rand: Math.random() }));
        } catch (e) {}

        // Immediate check on this tab
        checkServerUpdates(true);
    }

    /**
     * Check server for updates since lastKnownTimestamp
     */
    async function checkServerUpdates(force = false) {
        if (isChecking && !force) return;
        isChecking = true;

        try {
            // Adjust path depending on whether inside /admin/ or at root
            const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
            const url = `${basePath}${SYNC_CONFIG.checkUrl}&since=${lastKnownTimestamp}&_t=${Date.now()}`;

            const response = await fetch(url, {
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                isChecking = false;
                return;
            }

            const data = await response.json();

            // First run: calibrate baseline timestamp
            if (lastKnownTimestamp === 0) {
                lastKnownTimestamp = data.timestamp || Math.floor(Date.now() / 1000);
                updateLiveSyncIndicators(data.last_updated, false);
                isChecking = false;
                return;
            }

            if (data.has_updates && data.timestamp > lastKnownTimestamp) {
                lastKnownTimestamp = data.timestamp;
                const modules = data.updated_modules || ['global'];
                handleIncomingUpdate(modules, data.timestamp, data.last_updated);
            } else if (data.last_updated) {
                updateLiveSyncIndicators(data.last_updated, false);
            }
        } catch (error) {
            // Silently suppress network errors
        } finally {
            isChecking = false;
        }
    }

    /**
     * Dispatch DOM updates for modified modules
     */
    function handleIncomingUpdate(modules, timestamp, formattedDate) {
        if (formattedDate) {
            updateLiveSyncIndicators(formattedDate, true);
        }

        modules.forEach(function (module) {
            switch (module) {
                case 'announcements':
                case 'scrollbar':
                    syncAnnouncements();
                    break;
                case 'notifications':
                case 'circulars':
                    syncCirculars();
                    break;
                case 'news':
                    syncNews();
                    break;
                case 'courses':
                case 'departments':
                    syncCourses();
                    break;
                case 'rnd':
                    syncRndRankings();
                    break;
                case 'hero':
                    syncHeroSection();
                    break;
                case 'gallery':
                    syncGallery();
                    break;
                case 'global':
                    syncAnnouncements();
                    syncCirculars();
                    syncNews();
                    syncCourses();
                    syncRndRankings();
                    syncHeroSection();
                    syncGallery();
                    break;
            }
        });
    }

    /**
     * 1. Dynamic Announcement Bar Sync
     */
    async function syncAnnouncements() {
        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=announcements&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            let tickerWrap = document.querySelector('.news-ticker-wrap');

            // If disabled, smoothly slide up and hide
            if (!data.is_enabled) {
                if (tickerWrap) {
                    tickerWrap.style.transition = 'opacity 0.4s ease, max-height 0.4s ease';
                    tickerWrap.style.opacity = '0';
                    tickerWrap.style.maxHeight = '0px';
                    setTimeout(() => { tickerWrap.style.display = 'none'; }, 400);
                }
                return;
            }

            // If announcement was enabled and wrap doesn't exist, create it below nav
            if (!tickerWrap) {
                const nav = document.querySelector('nav');
                if (nav) {
                    tickerWrap = document.createElement('div');
                    tickerWrap.className = 'news-ticker-wrap';
                    tickerWrap.innerHTML = `
                        <div class="news-ticker" style="--ticker-speed: ${data.speed || 60}s;">
                            <div class="ticker-bar-container">
                                <div class="ticker-meta-side">
                                    <span class="ticker-live-badge"><i class="fas fa-bullhorn"></i> ANNOUNCEMENT</span>
                                    <span class="ticker-meta-divider">&bull;</span>
                                    <span class="ticker-updated-badge"><i class="far fa-calendar-check"></i> Last Updated: ${data.last_updated}</span>
                                </div>
                                <div class="ticker-flow-track">
                                    <div class="ticker-content" style="animation-duration: ${data.speed || 60}s;">
                                        ${data.ticker_html}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    nav.parentNode.insertBefore(tickerWrap, nav.nextSibling);
                    return;
                }
            }

            // Update existing wrap smoothly
            tickerWrap.style.display = 'block';
            tickerWrap.style.opacity = '1';
            tickerWrap.style.maxHeight = '';

            const tickerEl = tickerWrap.querySelector('.news-ticker');
            if (tickerEl) {
                tickerEl.style.setProperty('--ticker-speed', (data.speed || 60) + 's');
            }

            const updatedBadge = tickerWrap.querySelector('.ticker-updated-badge');
            if (updatedBadge) {
                updatedBadge.innerHTML = `<i class="far fa-calendar-check"></i> Last Updated: ${data.last_updated}`;
            }

            const tickerContent = tickerWrap.querySelector('.ticker-content');
            if (tickerContent && tickerContent.innerHTML !== data.ticker_html) {
                tickerContent.style.transition = 'opacity 0.25s ease';
                tickerContent.style.opacity = '0.3';
                setTimeout(() => {
                    tickerContent.innerHTML = data.ticker_html;
                    tickerContent.style.animationDuration = (data.speed || 60) + 's';
                    tickerContent.style.opacity = '1';
                }, 250);
            }
        } catch (e) {}
    }

    /**
     * 2. Circulars & Notifications Sync
     */
    async function syncCirculars() {
        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';

        // Homepage Notice Board Bulletin
        const bulletinContainer = document.getElementById('bulletin-list-container') || document.querySelector('[data-sync-module="circulars"]') || document.querySelector('[data-sync-module="notifications"]');
        if (bulletinContainer) {
            try {
                const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=notifications&_t=${Date.now()}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.html) {
                        smoothReplaceContent(bulletinContainer, data.html);
                    }
                }
            } catch (e) {}
        }

        // Circulars & Notifications Table Page (circulars.php)
        const tableBody = document.getElementById('circularsTableBody');
        if (tableBody) {
            try {
                const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=circulars_table&_t=${Date.now()}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.html) {
                        tableBody.style.transition = 'opacity 0.25s ease';
                        tableBody.style.opacity = '0.35';
                        setTimeout(() => {
                            tableBody.innerHTML = data.html;
                            tableBody.style.opacity = '1';
                        }, 250);
                        const countEl = document.getElementById('circulars-live-count');
                        if (countEl && typeof data.count !== 'undefined') {
                            countEl.textContent = data.count;
                        }
                    }
                }
            } catch (e) {}
        }
    }

    /**
     * 3. News & Media Sync
     */
    async function syncNews() {
        const newsContainer = document.querySelector('[data-sync-module="news"]') || document.querySelector('.press-modern-grid');
        if (!newsContainer) return;

        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=news&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.html) {
                smoothReplaceContent(newsContainer, data.html);
            }
        } catch (e) {}
    }

    /**
     * 4. Courses & Department Grid Sync
     */
    async function syncCourses() {
        const coursesContainer = document.getElementById('dynamic-courses-container') || document.querySelector('[data-sync-module="courses"]');
        if (!coursesContainer) return;

        // Remember which course tab was active
        const activeTabBtn = coursesContainer.querySelector('.tab-btn.active');
        let activeTabId = 'btech';
        if (activeTabBtn) {
            const onClickAttr = activeTabBtn.getAttribute('onclick') || '';
            const match = onClickAttr.match(/'([^']+)'/);
            if (match) activeTabId = match[1];
        }

        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=courses&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.html) {
                smoothReplaceContent(coursesContainer, data.html, function () {
                    // Restore active tab
                    if (typeof window.openCourseTab === 'function') {
                        const targetBtn = coursesContainer.querySelector(`.tab-btn[onclick*="${activeTabId}"]`);
                        if (targetBtn) {
                            window.openCourseTab({ currentTarget: targetBtn }, activeTabId);
                        }
                    }
                });
            }
        } catch (e) {}
    }

    /**
     * 5. R&D Department Rankings Sync
     */
    async function syncRndRankings() {
        // If the page already has loadRankingsData (index.php and rnd-rankings.php), trigger it
        if (typeof window.loadRankingsData === 'function') {
            const yearSel = document.getElementById('rnd-year-filter');
            const qtrSel  = document.getElementById('rnd-quarter-filter');
            const yr = yearSel ? yearSel.value : null;
            const qt = qtrSel ? qtrSel.value : null;
            window.loadRankingsData(yr, qt);
            return;
        }

        const rndContainer = document.querySelector('[data-sync-module="rnd"]');
        if (!rndContainer) return;

        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=rnd&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.data) {
                // If on ranking page, re-render dynamically
                window.location.reload();
            }
        } catch (e) {}
    }

    /**
     * 6. Hero Slider & Headline Content Sync
     */
    async function syncHeroSection() {
        const heroSection = document.querySelector('[data-sync-module="hero"]') || document.querySelector('.hero-stage-container');
        if (!heroSection) return;

        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=hero&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success || !data.data) return;

            const c = data.data.content || {};

            // Update Left Badge
            const leftBadge = heroSection.querySelector('.hero-glass-badge:not(.badge-accent) span:last-child');
            if (leftBadge && c.badge_left_text) leftBadge.textContent = c.badge_left_text;

            // Update Right Badge
            const rightBadge = heroSection.querySelector('.hero-glass-badge.badge-accent span');
            if (rightBadge && c.badge_right_text) rightBadge.textContent = c.badge_right_text;
            const rightIcon = heroSection.querySelector('.hero-glass-badge.badge-accent i');
            if (rightIcon && c.badge_right_icon) rightIcon.className = c.badge_right_icon;

            // Update Headline & Subtitle
            const titleEl = heroSection.querySelector('.hero-main-title');
            if (titleEl && c.title) titleEl.textContent = c.title;

            const subEl = heroSection.querySelector('.hero-subtitle span');
            if (subEl && c.subtitle) subEl.textContent = c.subtitle;
            const subIcon = heroSection.querySelector('.hero-subtitle i');
            if (subIcon && c.subtitle_icon) subIcon.className = c.subtitle_icon;

            // Update CTA Button
            const ctaBtn = heroSection.querySelector('.hero-cta-btn');
            if (ctaBtn) {
                const ctaSpan = ctaBtn.querySelector('span');
                if (ctaSpan && c.cta_text) ctaSpan.textContent = c.cta_text;
                if (c.cta_link) ctaBtn.href = c.cta_link;
            }
        } catch (e) {}
    }

    /**
     * 7. Gallery Grid Sync
     */
    async function syncGallery() {
        const grid = document.getElementById('gallery-grid-container');
        if (!grid) return;

        const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
        try {
            const res = await fetch(`${basePath}${SYNC_CONFIG.getSectionUrl}&section=gallery&_t=${Date.now()}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.html) {
                smoothReplaceContent(grid, data.html, function () {
                    if (typeof window.updateCategoryCounts === 'function') {
                        window.updateCategoryCounts();
                    }
                    if (typeof window.filterGallery === 'function' && window.currentFilter) {
                        const activeBtn = document.querySelector(`.gallery-tab-btn.active`);
                        window.filterGallery(window.currentFilter, activeBtn);
                    }
                });
            }
        } catch (e) {}
    }

    /**
     * Smooth DOM content replacer with cross-fade transition
     */
    function smoothReplaceContent(element, newHtml, callback) {
        if (!element || element.innerHTML === newHtml) return;

        element.style.transition = 'opacity 0.25s ease';
        element.style.opacity = '0.4';

        setTimeout(() => {
            element.innerHTML = newHtml;
            element.style.opacity = '1';
            if (typeof callback === 'function') callback();
        }, 250);
    }

    /**
     * Update Live Sync Indicator Pill & Date stamps
     */
    function updateLiveSyncIndicators(lastUpdatedStr, isRecentUpdate) {
        // Update all [data-sync-timestamp] elements
        const dateEls = document.querySelectorAll('[data-sync-timestamp]');
        dateEls.forEach(el => {
            el.textContent = lastUpdatedStr;
        });

        // Update footer pill if present
        const pill = document.querySelector('.live-sync-pill');
        if (pill) {
            const timeSpan = pill.querySelector('.live-sync-time');
            if (timeSpan && lastUpdatedStr) {
                timeSpan.textContent = `Updated: ${lastUpdatedStr}`;
            }
            if (isRecentUpdate) {
                pill.classList.add('sync-flash');
                setTimeout(() => { pill.classList.remove('sync-flash'); }, 3000);
            }
        }
    }

    /**
     * Adaptive Heartbeat Loop (Active vs Background)
     */
    function scheduleNextCheck() {
        if (pollTimer) clearTimeout(pollTimer);
        const interval = document.hidden ? SYNC_CONFIG.idleInterval : SYNC_CONFIG.activeInterval;
        pollTimer = setTimeout(async function () {
            await checkServerUpdates();
            scheduleNextCheck();
        }, interval);
    }

    // Visibility state changes trigger immediate check
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            checkServerUpdates(true);
        }
        scheduleNextCheck();
    });

    // Window focus triggers immediate check
    window.addEventListener('focus', function () {
        checkServerUpdates(true);
    });

    // Start engine on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            checkServerUpdates();
            scheduleNextCheck();
        });
    } else {
        checkServerUpdates();
        scheduleNextCheck();
    }

    // Expose global controller
    window.TCEK_Sync = {
        notify: notifyUpdate,
        checkNow: function () { checkServerUpdates(true); },
        getTimestamp: function () { return lastKnownTimestamp; }
    };

})();
