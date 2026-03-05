/*
|--------------------------------------------------------------------------
| THEME SWITCHER
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const btn = document.getElementById("themeToggle");

    if (!btn) return;

    btn.addEventListener("click", () => {

        const body = document.body;

        if (body.classList.contains("theme-dark")) {
            body.classList.remove("theme-dark");
            body.classList.add("theme-light");
        } else {
            body.classList.remove("theme-light");
            body.classList.add("theme-dark");
        }

    });

});
