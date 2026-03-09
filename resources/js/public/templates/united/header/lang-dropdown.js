/*
|--------------------------------------------------------------------------
| LANGUAGE DROPDOWN
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const toggle = document.getElementById("langToggle");
    const menu   = document.getElementById("langMenu");

    if (!toggle || !menu) return;

    toggle.addEventListener("click", (e) => {

        e.stopPropagation();

        menu.classList.toggle("open");

    });

    document.addEventListener("click", () => {

        menu.classList.remove("open");

    });

});
