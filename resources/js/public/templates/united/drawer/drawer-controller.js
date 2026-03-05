/*
|--------------------------------------------------------------------------
| MOBILE DRAWER CONTROLLER
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const drawer = document.getElementById("mobileDrawer");
    const openBtn = document.getElementById("drawerOpen");
    const closeBtn = document.getElementById("drawerClose");

    if (!drawer) return;

    openBtn?.addEventListener("click", () => {
        drawer.classList.add("drawer-open");
    });

    closeBtn?.addEventListener("click", () => {
        drawer.classList.remove("drawer-open");
    });

});
