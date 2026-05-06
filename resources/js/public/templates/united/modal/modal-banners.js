document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('bannerModal');
    const img = document.getElementById('bannerModalImage');

    if (!modal || !img) return;

    // =========================
    // STATE
    // =========================
    let scale = 1;

    let translateX = 0;
    let translateY = 0;

    let isDragging = false;

    let startX = 0;
    let startY = 0;

    let lastTouchDistance = null;

    // =========================
    // APPLY
    // =========================
    function applyTransform() {

        clampPosition();

        img.style.transform =
            `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }

    // =========================
    // LIMITS
    // =========================
    function clampPosition() {

        const wrapper = img.parentElement;

        if (!wrapper) return;

        const rect = wrapper.getBoundingClientRect();

        const scaledWidth  = rect.width * scale;
        const scaledHeight = rect.height * scale;

        const maxX = (scaledWidth - rect.width) / 2;
        const maxY = (scaledHeight - rect.height) / 2;

        translateX = Math.min(maxX, Math.max(-maxX, translateX));
        translateY = Math.min(maxY, Math.max(-maxY, translateY));
    }

    // =========================
    // RESET
    // =========================
    function reset() {

        scale = 1;

        translateX = 0;
        translateY = 0;

        applyTransform();
    }

    // =========================
    // CLICK ZOOM
    // =========================
    img.addEventListener('dblclick', () => {

        scale = scale === 1 ? 2 : 1;

        if (scale === 1) {
            translateX = 0;
            translateY = 0;
        }

        applyTransform();
    });

    // =========================
    // WHEEL ZOOM
    // =========================
    img.addEventListener('wheel', (e) => {

        e.preventDefault();

        scale += e.deltaY * -0.002;

        scale = Math.min(Math.max(1, scale), 4);

        if (scale === 1) {
            translateX = 0;
            translateY = 0;
        }

        applyTransform();

    }, { passive: false });

    // =========================
    // DRAG START
    // =========================
    img.addEventListener('pointerdown', (e) => {

        if (scale <= 1) return;

        isDragging = true;

        startX = e.clientX - translateX;
        startY = e.clientY - translateY;

        img.style.cursor = 'grabbing';
    });

    // =========================
    // DRAG MOVE
    // =========================
    window.addEventListener('pointermove', (e) => {

        if (!isDragging) return;

        translateX = e.clientX - startX;
        translateY = e.clientY - startY;

        applyTransform();
    });

    // =========================
    // DRAG END
    // =========================
    window.addEventListener('pointerup', () => {

        isDragging = false;

        img.style.cursor = 'grab';
    });

    // =========================
    // TOUCH START
    // =========================
    img.addEventListener('touchstart', (e) => {

        if (e.touches.length === 2) {
            lastTouchDistance = getDistance(e.touches);
        }

        if (e.touches.length === 1 && scale > 1) {

            isDragging = true;

            startX = e.touches[0].clientX - translateX;
            startY = e.touches[0].clientY - translateY;
        }

    }, { passive: false });

    // =========================
    // TOUCH MOVE
    // =========================
    img.addEventListener('touchmove', (e) => {

        // PINCH
        if (e.touches.length === 2) {

            const newDistance = getDistance(e.touches);

            if (lastTouchDistance) {

                const delta = newDistance - lastTouchDistance;

                scale += delta * 0.01;

                scale = Math.min(Math.max(1, scale), 4);

                applyTransform();
            }

            lastTouchDistance = newDistance;
        }

        // PAN
        if (e.touches.length === 1 && isDragging && scale > 1) {

            translateX = e.touches[0].clientX - startX;
            translateY = e.touches[0].clientY - startY;

            applyTransform();
        }

    }, { passive: false });

    // =========================
    // TOUCH END
    // =========================
    img.addEventListener('touchend', () => {

        isDragging = false;

        lastTouchDistance = null;
    });

    // =========================
    // DISTANCE
    // =========================
    function getDistance(touches) {

        const dx =
            touches[0].clientX - touches[1].clientX;

        const dy =
            touches[0].clientY - touches[1].clientY;

        return Math.sqrt(dx * dx + dy * dy);
    }

    // =========================
// OPEN
// =========================
    window.addEventListener('banner-modal:open', (e) => {

        if (!e.detail?.src) return;

        const newSrc = e.detail.src;

        // скрываем пока грузится
        img.style.opacity = '0';

        const preload = new Image();

        preload.onload = () => {

            img.src = newSrc;

            reset();

            modal.classList.add('active');

            requestAnimationFrame(() => {
                img.style.opacity = '1';
            });
        };

        preload.src = newSrc;
    });

    // =========================
// CLOSE ELEMENTS
// =========================
    const closeElements = modal.querySelectorAll('[data-banner-modal-close]');

    closeElements.forEach(el => {

        el.addEventListener('click', () => {

            modal.classList.remove('active');

            reset();
        });

    });

// =========================
// ESC CLOSE
// =========================
    document.addEventListener('keydown', (e) => {

        if (e.key === 'Escape') {

            modal.classList.remove('active');

            reset();
        }
    });

});
