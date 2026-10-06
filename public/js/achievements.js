document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.achievement-slide');
    const nextBtn = document.querySelector('.ach-next');
    const prevBtn = document.querySelector('.ach-prev');
    const dots = document.querySelectorAll('.ach-dot');
    const currentNumEl = document.querySelector('.ach-current-num');
    const totalNumEl = document.querySelector('.ach-total-num');
    const cardEl = document.querySelector('.achievements-showcase-card');

    let currentAchIndex = 0;
    let autoPlayInterval = null;

    if (!slides || slides.length === 0) return;

    if (totalNumEl) {
        totalNumEl.textContent = String(slides.length).padStart(2, '0');
    }

    function showAchSlide(index) {
        if (index >= slides.length) currentAchIndex = 0;
        else if (index < 0) currentAchIndex = slides.length - 1;
        else currentAchIndex = index;

        slides.forEach((slide, i) => {
            if (i === currentAchIndex) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });

        dots.forEach((dot, i) => {
            if (i === currentAchIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        if (currentNumEl) {
            currentNumEl.textContent = String(currentAchIndex + 1).padStart(2, '0');
        }
    }

    function nextAchSlide() {
        showAchSlide(currentAchIndex + 1);
    }

    function prevAchSlide() {
        showAchSlide(currentAchIndex - 1);
    }

    // Button Click Handlers
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            nextAchSlide();
            resetTimer();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            prevAchSlide();
            resetTimer();
        });
    }

    // Dot Navigation
    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const targetIndex = parseInt(dot.getAttribute('data-index'), 10);
            if (!isNaN(targetIndex)) {
                showAchSlide(targetIndex);
                resetTimer();
            }
        });
    });

    // Touch / Swipe Navigation
    let touchStartX = 0;
    let touchEndX = 0;

    if (cardEl) {
        cardEl.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        cardEl.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        // Pause on hover
        cardEl.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });

        cardEl.addEventListener('mouseleave', () => {
            resetTimer();
        });
    }

    function handleSwipe() {
        const threshold = 50;
        if (touchEndX < touchStartX - threshold) {
            nextAchSlide();
            resetTimer();
        } else if (touchEndX > touchStartX + threshold) {
            prevAchSlide();
            resetTimer();
        }
    }

    function startTimer() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(nextAchSlide, 6000);
    }

    function resetTimer() {
        clearInterval(autoPlayInterval);
        startTimer();
    }

    // Initialize
    showAchSlide(0);
    startTimer();
});
