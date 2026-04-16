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
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                e.preventDefault();
                track.scrollLeft += e.deltaY;
            }
        }, { passive: false });

        track.addEventListener('mousedown', (e) => {
            if (e.target.closest('[data-open-modal]')) {
                // всё равно разрешаем drag, но не ломаем клик потом
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
