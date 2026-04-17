// resources/js/public/templates/united/drawer/drawer-controller.js

/*
|--------------------------------------------------------------------------
| MOBILE DRAWER CONTROLLER
|--------------------------------------------------------------------------
*/
document.addEventListener("DOMContentLoaded", () => {

    const drawer   = document.getElementById("mobileDrawer");
    const openBtn  = document.getElementById("drawerOpen");
    const closeBtn = document.getElementById("drawerClose");
    const overlay  = document.getElementById("drawerOverlay");

    if (!drawer || !openBtn) return;

    /* ------------------------------
    | STATE
    ------------------------------ */
    function isOpen() {
        return drawer.classList.contains("drawer-open");
    }

    /* ------------------------------
    | OPEN
    ------------------------------ */
    function openDrawer() {

        if (isOpen()) return;

        drawer.classList.add("drawer-open");
        document.body.classList.add("drawer-active");

        overlay?.classList.add("active");

    }

    /* ------------------------------
    | CLOSE
    ------------------------------ */
    function closeDrawer() {

        if (!isOpen()) return;

        drawer.classList.remove("drawer-open");
        document.body.classList.remove("drawer-active");

        overlay?.classList.remove("active");

    }

    /* ------------------------------
    | OPEN BUTTON
    ------------------------------ */
    openBtn.addEventListener("click", (e) => {
        e.preventDefault();
        openDrawer();
    });

    /* ------------------------------
    | CLOSE BUTTON
    ------------------------------ */
    closeBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        closeDrawer();
    });

    /* ------------------------------
    | CLICK MENU LINK
    ------------------------------ */
    document.querySelectorAll("[data-drawer-link]").forEach(link => {
        link.addEventListener("click", closeDrawer);
    });

    /* ------------------------------
    | CLICK OVERLAY
    ------------------------------ */
    overlay?.addEventListener("click", closeDrawer);

    /* ------------------------------
    | CLICK OUTSIDE (fallback)
    ------------------------------ */
    document.addEventListener("click", (e) => {

        if (!isOpen()) return;

        const isInsideDrawer = drawer.contains(e.target);
        const isOpenBtn = e.target.closest("#drawerOpen");

        if (!isInsideDrawer && !isOpenBtn) {
            closeDrawer();
        }

    });

    /* ------------------------------
    | ESC KEY
    ------------------------------ */
    document.addEventListener("keydown", (e) => {

        if (e.key === "Escape") {
            closeDrawer();
        }

    });

});
