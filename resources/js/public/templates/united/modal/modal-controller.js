// resources/js/public/templates/united/modal/modal-controller.js

document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("itemModal");
    if (!modal) return;

    const box = modal.querySelector(".modal-box");

    const titleEl = modal.querySelector(".modal-title");
    const descEl = modal.querySelector(".modal-description");
    const priceEl = modal.querySelector(".modal-price");
    const imgEl = modal.querySelector(".modal-image");

    const badgeEl = modal.querySelector(".modal-badges");
    const spicyEl = modal.querySelector(".modal-spicy");
    const detailsEl = modal.querySelector(".modal-details");
    const backBtn = modal.querySelector(".modal-back-btn");

    // =========================
    // IMAGE VIEWER
    // =========================

    const viewer = modal.querySelector('.modal-image-viewer');
    const viewerImg = modal.querySelector('.modal-image-full');

    let scale = 1;
    let translateX = 0;
    let translateY = 0;

    let isDragging = false;

    let startX = 0;
    let startY = 0;

    function applyTransform() {

        if (!viewerImg) return;

        clampPosition();

        viewerImg.style.transform =
            `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }

    function clampPosition() {

        if (!viewerImg || scale <= 1) {
            translateX = 0;
            translateY = 0;
            return;
        }

        const rect = viewerImg.getBoundingClientRect();

        const maxX = Math.max(0, (rect.width - window.innerWidth * 0.8) / 2);
        const maxY = Math.max(0, (rect.height - window.innerHeight * 0.8) / 2);

        translateX = Math.min(maxX, Math.max(-maxX, translateX));
        translateY = Math.min(maxY, Math.max(-maxY, translateY));
    }

    function resetViewer() {

        scale = 1;
        translateX = 0;
        translateY = 0;

        applyTransform();
    }

    function openViewer(src) {

        if (!viewer || !viewerImg || !src) return;

        viewerImg.src = src;

        viewer.classList.add('is-visible');
        viewer.setAttribute('aria-hidden', 'false');

        resetViewer();
    }

    function closeViewer() {

        if (!viewer || !viewerImg) return;

        viewer.classList.remove('is-visible');
        viewer.setAttribute('aria-hidden', 'true');

        viewerImg.src = '';

        resetViewer();
    }

    // OPEN IMAGE VIEWER
    if (imgEl) {

        imgEl.addEventListener('click', () => {

            if (!imgEl.src) return;

            openViewer(imgEl.src);

        });

    }

    // CLOSE IMAGE VIEWER
    if (viewer) {

        viewer.addEventListener('click', (e) => {

            // click outside image
            if (!e.target.closest('.modal-image-full')) {

                e.preventDefault();
                e.stopPropagation();

                closeViewer();
            }

        });

    }

    // WHEEL ZOOM
    if (viewer) {

        viewer.addEventListener('wheel', (e) => {

            if (!viewer.classList.contains('is-visible')) return;

            e.preventDefault();

            const delta = e.deltaY > 0 ? -0.2 : 0.2;

            scale += delta;

            scale = Math.max(1, Math.min(5, scale));

            applyTransform();

        }, { passive:false });

    }

    // DRAG START
    if (viewerImg) {

        viewerImg.addEventListener('pointerdown', (e) => {

            if (scale <= 1) return;

            isDragging = true;

            startX = e.clientX - translateX;
            startY = e.clientY - translateY;

            viewerImg.classList.add('dragging');

        });

    }

    // DRAG MOVE
    window.addEventListener('pointermove', (e) => {

        if (!isDragging) return;

        translateX = e.clientX - startX;
        translateY = e.clientY - startY;

        applyTransform();

    });

    // DRAG END
    window.addEventListener('pointerup', () => {

        isDragging = false;

        if (viewerImg) {
            viewerImg.classList.remove('dragging');
        }

    });

    // DOUBLE CLICK ZOOM
    if (viewerImg) {

        viewerImg.addEventListener('dblclick', () => {

            if (scale > 1) {

                resetViewer();

            } else {

                scale = 2;
                applyTransform();

            }

        });

    }

    function openModalFromTrigger(trigger) {

        if (backBtn) {
            backBtn.style.display = "";
        }

        const title = trigger.getAttribute("data-title") || "";
        const desc = trigger.getAttribute("data-description") || "";
        const details = trigger.getAttribute("data-details") || "";
        const price = trigger.getAttribute("data-price") || "";
        const img = trigger.getAttribute("data-image") || "";

        const isNew = trigger.getAttribute("data-is-new") === "1";
        const isDish = trigger.getAttribute("data-is-dish") === "1";
        const spicy = Math.max(0, Math.min(5, parseInt(trigger.getAttribute("data-spicy") || "0", 10)));

        titleEl.textContent = title;

        descEl.textContent = "";

        if (detailsEl) {
            detailsEl.textContent = "";
        }

        priceEl.textContent = "";
        badgeEl.innerHTML = "";
        spicyEl.innerHTML = "";

        if (desc.trim().length) {

            descEl.textContent = desc;
            descEl.style.display = "";

        } else {

            descEl.style.display = "none";
        }

        if (detailsEl && details.trim().length) {

            detailsEl.innerHTML = details;
            detailsEl.style.display = "";

        } else if (detailsEl) {

            detailsEl.style.display = "none";
        }

        if (price.trim().length) {

            priceEl.textContent = price;
            priceEl.style.display = "";

        } else {

            priceEl.style.display = "none";
        }

        if (img && img.trim().length && img !== "null") {

            imgEl.src = img;
            imgEl.alt = title || "Menu item";
            imgEl.style.display = "";

        } else {

            imgEl.style.display = "none";
        }

        // BADGES
        const isBestseller = trigger.getAttribute("data-bestseller") === "1";

        const badges = [];

        if (isDish) {

            badges.push(`<span class="menu-item-badge menu-item-badge--dish">
        ${window.UI_LANG.badge_dish}
    </span>`);
        }

        if (isNew) {

            badges.push(`<span class="menu-item-badge menu-item-badge--new">
        ${window.UI_LANG.badge_new}
    </span>`);
        }

        if (isBestseller) {

            badges.push(`<span class="menu-item-badge menu-item-badge--bestseller">
        ★ ${window.UI_LANG.badge_bestseller}
    </span>`);
        }

        if (badges.length) {

            badgeEl.innerHTML = badges.join('');
            badgeEl.style.display = "";

        } else {

            badgeEl.innerHTML = "";
            badgeEl.style.display = "none";
        }

        if (spicy > 0) {

            let spicyHtml = "";

            for (let i = 1; i <= spicy; i++) {
                spicyHtml += `<i class="is-on">🌶</i>`;
            }

            spicyEl.innerHTML = spicyHtml;
            spicyEl.style.display = "";

        } else {

            spicyEl.innerHTML = "";
            spicyEl.style.display = "none";
        }

        // BORDER
        box.classList.remove("is-new","is-dish");

        if (isNew) box.classList.add("is-new");
        if (isDish) box.classList.add("is-dish");

        modal.classList.add("modal-visible");
        modal.setAttribute("aria-hidden", "false");

        document.documentElement.style.overflow = "hidden";
        document.body.style.overflow = "hidden";
    }

    function closeModal() {

        if (backBtn) {
            backBtn.style.display = "none";
        }

        closeViewer();

        modal.classList.remove("modal-visible");
        modal.setAttribute("aria-hidden", "true");

        document.documentElement.style.overflow = "";
        document.body.style.overflow = "";

        imgEl.src = "";
    }

    // OPEN
    document.addEventListener("click", (e) => {

        const trigger = e.target.closest("[data-open-modal]");
        if (!trigger) return;

        const modalType = trigger.getAttribute("data-open-modal");

        if (modalType && modalType !== "" && modalType !== "item") return;

        const img = trigger.getAttribute("data-image") || "";

        if (!img || img.trim() === "" || img === "null") {
            return;
        }

        e.preventDefault();

        openModalFromTrigger(trigger);

    });

    // CLOSE MODAL
    modal.addEventListener("click", (e) => {

        // ignore viewer clicks
        if (e.target.closest('.modal-image-viewer')) {
            return;
        }

        if (e.target.closest("[data-close-modal]")) {

            e.preventDefault();
            closeModal();

            return;
        }

        if (box && !box.contains(e.target)) {
            closeModal();
        }

    });

    // ESC
    document.addEventListener("keydown", (e) => {

        if (
            e.key === "Escape" &&
            viewer &&
            viewer.classList.contains("is-visible")
        ) {

            closeViewer();
            return;
        }

        if (
            e.key === "Escape" &&
            modal.classList.contains("modal-visible")
        ) {

            closeModal();
        }

    });

});
