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

// description
        if (desc.trim().length) {
            descEl.textContent = desc;
            descEl.style.display = "";
        } else {
            descEl.style.display = "none";
        }

// details
        if (detailsEl && details.trim().length) {
            detailsEl.innerHTML = details;
            detailsEl.style.display = "";
        } else if (detailsEl) {
            detailsEl.style.display = "none";
        }

// price
        if (price.trim().length) {
            priceEl.textContent = price;
            priceEl.style.display = "";
        } else {
            priceEl.style.display = "none";
        }

// image
        if (img && img.trim().length && img !== "null") {
            imgEl.src = img;
            imgEl.alt = title || "Menu item";
            imgEl.style.display = "";
        } else {
            imgEl.style.display = "none";
        }

// BADGES
        let badges = "";

        if (isNew) {
            badges += '<span class="menu-item-badge menu-item-badge--new">NEW</span>';
        }

        if (isDish) {
            badges += '<span class="menu-item-badge menu-item-badge--dish">Dish of Day</span>';
        }

        if (badges) {
            badgeEl.innerHTML = badges;
            badgeEl.style.display = "";
        } else {
            badgeEl.style.display = "none";
        }

// SPICY
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

        // если нет изображения — не открываем модалку
        if (!img || img.trim() === "" || img === "null") {
            return;
        }

        e.preventDefault();

        openModalFromTrigger(trigger);

    });

// CLOSE BUTTON
    modal.addEventListener("click", (e) => {

        if (e.target.closest("[data-close-modal]")) {
            e.preventDefault();
            closeModal();
        }

    });

// CLOSE OVERLAY
    modal.addEventListener("click", (e) => {

        if (box && !box.contains(e.target)) {
            closeModal();
        }

    });

// ESC
    document.addEventListener("keydown", (e) => {

        if (e.key === "Escape" && modal.classList.contains("modal-visible")) {
            closeModal();
        }

    });

});
