// resources/js/public/templates/united/banners/banners.js

document.addEventListener('DOMContentLoaded', () => {

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

        const step = () => Math.max(track.clientWidth * 0.9, 300);

        // =========================
        // SNAP HELPER (NEW)
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
        // DOTS LOGIC (FIXED)
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
                snapToClosest(); // 🔥 авто-выравнивание
            }, 120);
        });

        // =========================
        // ARROWS
        // =========================
        if (prev) {
            prev.addEventListener('click', () => {
                track.scrollBy({ left: -step(), behavior: 'smooth' });
            });
        }

        if (next) {
            next.addEventListener('click', () => {
                track.scrollBy({ left: step(), behavior: 'smooth' });
            });
        }

        // =========================
        // POINTER DRAG (улучшен)
        // =========================
        track.addEventListener('pointerdown', (e) => {
            isDown = true;
            startX = e.clientX;
            scrollLeft = track.scrollLeft;

            track.setPointerCapture(e.pointerId);
            track.classList.add('dragging');
        });

        track.addEventListener('pointerup', (e) => {
            isDown = false;
            try { track.releasePointerCapture(e.pointerId); } catch {}
            track.classList.remove('dragging');

            snapToClosest(); // 🔥 фикс после drag
        });

        track.addEventListener('pointerleave', () => {
            isDown = false;
            track.classList.remove('dragging');
        });

        track.addEventListener('pointermove', (e) => {
            if (!isDown) return;

            const x = e.clientX - startX;
            track.scrollLeft = scrollLeft - x;
        });

        // =========================
        // MOUSE DRAG (fallback)
        // =========================
        track.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX;
            scrollLeft = track.scrollLeft;
            track.classList.add('dragging');
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('dragging');
        });

        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('dragging');

            snapToClosest(); // 🔥 фикс
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - startX;
            track.scrollLeft = scrollLeft - x;
        });

        // =========================
        // INIT
        // =========================
        updateDots();

    });

});
