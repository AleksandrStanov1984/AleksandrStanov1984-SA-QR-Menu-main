// resources/js/public/templates/united/banners/banners.js

document.addEventListener('DOMContentLoaded', () => {

    console.log('BANNERS JS LOADED');

    console.log('banner items:', document.querySelectorAll('[data-banner-src]').length);

    const carousels = document.querySelectorAll('[data-banner-carousel]');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {

        const track = carousel.querySelector('.banner-track');
        const prev = carousel.querySelector('[data-banner-prev]');
        const next = carousel.querySelector('[data-banner-next]');
        const dots = carousel.querySelectorAll('.banner-dot');

        if (!track) return;

        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;

        let moved = false;
        let downTarget = null;

        const step = () => Math.max(track.clientWidth * 0.9, 300);

        // =========================
        // SNAP
        // =========================
        function snapToClosest() {

            const item = track.querySelector('.banner-item');
            if (!item) return;

            const gap = 12;
            const itemWidth = item.offsetWidth + gap;

            const index = Math.round(track.scrollLeft / itemWidth);

            track.scrollTo({
                left: index * itemWidth,
                behavior: 'smooth'
            });
        }

        // =========================
        // DOTS
        // =========================
        function updateDots() {

            if (!dots.length) return;

            const item = track.querySelector('.banner-item');
            if (!item) return;

            const gap = 12;
            const itemWidth = item.offsetWidth + gap;

            const index = Math.round(track.scrollLeft / itemWidth);

            dots.forEach(dot => dot.classList.remove('active'));

            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }

        dots.forEach(dot => {

            dot.addEventListener('click', () => {

                const index = parseInt(dot.dataset.dot);

                const item = track.querySelector('.banner-item');
                if (!item) return;

                const gap = 12;
                const itemWidth = item.offsetWidth + gap;

                track.scrollTo({
                    left: itemWidth * index,
                    behavior: 'smooth'
                });

            });

        });

        // =========================
        // SCROLL + SNAP
        // =========================
        let scrollTimeout;

        track.addEventListener('scroll', () => {

            requestAnimationFrame(updateDots);

            clearTimeout(scrollTimeout);

            scrollTimeout = setTimeout(() => {
                snapToClosest();
            }, 120);

        });

        // =========================
        // ARROWS
        // =========================
        if (prev) {

            prev.addEventListener('click', () => {

                track.scrollBy({
                    left: -step(),
                    behavior: 'smooth'
                });

            });
        }

        if (next) {

            next.addEventListener('click', () => {

                track.scrollBy({
                    left: step(),
                    behavior: 'smooth'
                });

            });
        }

        // =========================
        // POINTER DRAG + CLICK
        // =========================
        track.addEventListener('pointerdown', (e) => {

            isDown = true;
            moved = false;

            downTarget = e.target.closest('.banner-item');

            startX = e.clientX;
            scrollLeft = track.scrollLeft;

            track.setPointerCapture(e.pointerId);
            track.classList.add('dragging');
        });

        track.addEventListener('pointermove', (e) => {

            if (!isDown) return;

            const x = e.clientX - startX;

            if (Math.abs(x) > 12) {
                moved = true;
            }

            track.scrollLeft = scrollLeft - x;
        });

        track.addEventListener('pointerup', (e) => {

            isDown = false;

            try {
                track.releasePointerCapture(e.pointerId);
            } catch {}

            track.classList.remove('dragging');

            // =========================
            // OPEN MODAL
            // =========================
            if (!moved && downTarget) {

                const src = downTarget.dataset.bannerSrc;

                if (src) {

                    window.dispatchEvent(new CustomEvent('banner-modal:open', {
                        detail: { src }
                    }));

                }
            }

            downTarget = null;

            snapToClosest();
        });

        track.addEventListener('pointerleave', () => {

            isDown = false;
            downTarget = null;

            track.classList.remove('dragging');
        });

        // =========================
        // CURSOR
        // =========================
        carousel.querySelectorAll('.banner-item').forEach(item => {
            item.style.cursor = 'pointer';
        });

        // =========================
        // INIT
        // =========================
        updateDots();

        // =========================
        // iOS TOUCH SUPPORT
        // =========================
        track.addEventListener('touchstart', (e) => {

            isDown = true;
            moved = false;

            downTarget = e.target.closest('.banner-item');

            startX = e.touches[0].clientX;
            scrollLeft = track.scrollLeft;

        }, { passive: true });

        track.addEventListener('touchmove', (e) => {

            if (!isDown) return;

            const x = e.touches[0].clientX - startX;

            if (Math.abs(x) > 12) {
                moved = true;
            }

            track.scrollLeft = scrollLeft - x;

        }, { passive: true });

        track.addEventListener('touchend', () => {

            isDown = false;

            track.classList.remove('dragging');

            downTarget = null;

            snapToClosest();

        }, { passive: true });

    });

    // =========================
    // MODAL OPEN / CLOSE
    // =========================
    const modal = document.getElementById('bannerModal');
    const modalImg = document.getElementById('bannerModalImage');

    document.querySelectorAll('[data-banner-src]').forEach(el => {
        el.addEventListener('click', () => {

            modalImg.src = el.dataset.bannerSrc;

            modal.classList.add('active');
        });
    });

    modal?.querySelectorAll('[data-close]').forEach(el => {
        el.addEventListener('click', () => {
            modal.classList.remove('active');
            modalImg.src = '';
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            modal?.classList.remove('active');
            modalImg.src = '';
        }
    });

});
