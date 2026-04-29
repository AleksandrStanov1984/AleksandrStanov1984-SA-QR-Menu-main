// resources/js/public/templates/united/header/header-carousel.js

export function initHeaderCarousel() {
    const carousels = document.querySelectorAll('[data-header-carousel]');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
        if (carousel.dataset.inited === '1') return;
        carousel.dataset.inited = '1';

        const track = carousel.querySelector('.header-carousel__track');
        const prev = carousel.querySelector('[data-carousel-prev]');
        const next = carousel.querySelector('[data-carousel-next]');

        if (!track) return;

        let isDown = false;
        let startX = 0;
        let startScrollLeft = 0;
        let moved = false;

        const step = () => Math.max(track.clientWidth * 0.82, 220);

        const updateArrows = () => {
            if (!prev || !next) return;

            const maxScroll = track.scrollWidth - track.clientWidth;
            prev.disabled = track.scrollLeft <= 2;
            next.disabled = track.scrollLeft >= maxScroll - 2;
        };

        if (prev) {
            prev.addEventListener('click', (e) => {
                e.preventDefault();
                track.scrollBy({ left: -step(), behavior: 'smooth' });
            });
        }

        if (next) {
            next.addEventListener('click', (e) => {
                e.preventDefault();
                track.scrollBy({ left: step(), behavior: 'smooth' });
            });
        }

        track.addEventListener('wheel', (e) => {
            const isVertical = Math.abs(e.deltaY) > Math.abs(e.deltaX);

            if (isVertical) {
                e.preventDefault();

                const horizontalDelta = e.deltaY;

                track.scrollBy({
                    left: horizontalDelta,
                    behavior: 'auto'
                });
            }
        }, { passive: false });

        // =========================
        // POINTER EVENTS (NEW)
        // =========================
        track.addEventListener('pointerdown', (e) => {
            isDown = true;
            moved = false;

            startX = e.clientX;
            startScrollLeft = track.scrollLeft;

            try { track.setPointerCapture(e.pointerId); } catch {}
            track.classList.add('is-dragging');
        });

        track.addEventListener('pointermove', (e) => {
            if (!isDown) return;

            const dx = e.clientX - startX;

            if (Math.abs(dx) > 5) {
                moved = true;
            }

            if (moved) {
                track.scrollLeft = startScrollLeft - dx;
            }
        });

        track.addEventListener('pointerup', (e) => {
            isDown = false;
            try { track.releasePointerCapture(e.pointerId); } catch {}
            track.classList.remove('is-dragging');
        });

        track.addEventListener('pointercancel', () => {
            isDown = false;
            track.classList.remove('is-dragging');
        });

        // 🔥 не даём ложный клик после свайпа
        track.addEventListener('click', (e) => {
            if (moved) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // =========================
        // OLD MOUSE (НЕ ТРОГАЕМ)
        // =========================
        track.addEventListener('mousedown', (e) => {
            if (e.target.closest('[data-open-modal]')) {
            }

            isDown = true;
            startX = e.pageX;
            startScrollLeft = track.scrollLeft;
            track.classList.add('is-dragging');
        });

        window.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('is-dragging');
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('is-dragging');
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const dx = e.pageX - startX;
            track.scrollLeft = startScrollLeft - dx;
        });

        track.addEventListener('scroll', updateArrows, { passive: true });
        window.addEventListener('resize', updateArrows, { passive: true });

        updateArrows();
    });
}
