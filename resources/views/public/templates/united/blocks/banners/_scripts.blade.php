<script>
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
            // DOTS LOGIC (NEW)
            // =========================
            function updateDots() {

                if (!dots.length) return;

                const width = track.clientWidth;
                const index = Math.round(track.scrollLeft / width);

                dots.forEach(dot => dot.classList.remove('active'));

                if (dots[index]) {
                    dots[index].classList.add('active');
                }
            }

            // click on dot
            dots.forEach(dot => {
                dot.addEventListener('click', () => {

                    const index = parseInt(dot.dataset.dot);

                    track.scrollTo({
                        left: track.clientWidth * index,
                        behavior: 'smooth'
                    });

                });
            });

            // update on scroll
            track.addEventListener('scroll', () => {
                requestAnimationFrame(updateDots);
            });

            // =========================
            // ARROWS (unchanged)
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
            // DRAG (unchanged)
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
</script>
