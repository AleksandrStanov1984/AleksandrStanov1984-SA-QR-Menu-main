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

    function openDrawer() {

        drawer.classList.add("drawer-open");
        document.body.classList.add("drawer-active");

        overlay?.classList.add("drawer-open");

    }

    function closeDrawer() {

        drawer.classList.remove("drawer-open");
        document.body.classList.remove("drawer-active");

        overlay?.classList.remove("drawer-open");

    }

    /* open */

    openBtn.addEventListener("click", openDrawer);

    /* close button */

    closeBtn?.addEventListener("click", closeDrawer);

    /* click menu item */

    document.querySelectorAll("[data-drawer-link]").forEach(link => {
        link.addEventListener("click", closeDrawer);
    });

    /* click overlay */

    overlay?.addEventListener("click", closeDrawer);

    /* ESC */

    document.addEventListener("keydown", (e) => {

        if (e.key === "Escape") {
            closeDrawer();
        }

    });

});
