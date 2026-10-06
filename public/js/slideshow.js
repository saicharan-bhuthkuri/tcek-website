document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.hero-slide');
    if (!slides.length) return;

    const prevBtn = document.querySelector('.hero-nav-arrow.prev-slide');
    const nextBtn = document.querySelector('.hero-nav-arrow.next-slide');
    const dotsContainer = document.querySelector('.hero-dots-container');
    const stage = document.querySelector('.hero-stage') || document.querySelector('.controller-card');

    let currentIndex = 0;
    let slideTimer = null;
    let isHovered = false;

    // Dynamically generate dot indicators if container exists
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = `hero-dot ${i === 0 ? 'active' : ''}`;
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                goToSlide(i);
            });
            dotsContainer.appendChild(dot);
        });
    }

    function updateDots(index) {
        if (!dotsContainer) return;
        const dots = dotsContainer.querySelectorAll('.hero-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
                if (slide.tagName === 'VIDEO') {
                    slide.currentTime = 0;
                    const playPromise = slide.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(() => {
                            // Autoplay restricted fallback
                        });
                    }
                    slide.onended = () => {
                        if (!isHovered) nextSlide();
                    };
                }
            } else {
                slide.classList.remove('active');
                if (slide.tagName === 'VIDEO') {
                    slide.pause();
                    slide.currentTime = 0;
                }
            }
        });

        updateDots(index);
    }

    function scheduleNext() {
        clearTimeout(slideTimer);
        const currentSlide = slides[currentIndex];
        if (currentSlide && currentSlide.tagName === 'VIDEO') {
            // Video will call nextSlide() on end
            return;
        }

        slideTimer = setTimeout(() => {
            if (!isHovered) {
                nextSlide();
            } else {
                scheduleNext();
            }
        }, 3600); // 3.6 seconds per image
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
        scheduleNext();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(currentIndex);
        scheduleNext();
    }

    function goToSlide(index) {
        if (index === currentIndex) return;
        currentIndex = index;
        showSlide(currentIndex);
        scheduleNext();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            prevSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            nextSlide();
        });
    }

    if (stage) {
        stage.addEventListener('mouseenter', () => {
            isHovered = true;
        });
        stage.addEventListener('mouseleave', () => {
            isHovered = false;
            scheduleNext();
        });
    }

    // Initialize first slide
    showSlide(0);
    scheduleNext();
});
