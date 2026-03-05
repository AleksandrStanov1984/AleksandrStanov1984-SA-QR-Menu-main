export function initFooterCarousel() {
    const track = document.querySelector('.footer-gallery__track');
    if (!track) return;

    track.addEventListener('wheel', (e) => {
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
            e.preventDefault();
            track.scrollLeft += e.deltaY;
        }
    }, { passive: false });

    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;

    track.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX;
        scrollLeft = track.scrollLeft;
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
        const walk = (e.pageX - startX) * 1.2;
        track.scrollLeft = scrollLeft - walk;
    });
}
