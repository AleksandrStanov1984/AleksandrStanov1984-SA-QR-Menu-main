/*
|--------------------------------------------------------------------------
| THEME SWITCHER (FINAL)
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const body = document.body;
    const btn  = document.getElementById("themeToggle");

    // 🔥 берём из backend (пробрасывается из Blade)
    const mode = body.dataset.themeMode || "light";

    const applyTheme = (theme) => {
        body.classList.remove("theme-light", "theme-dark");
        body.classList.add(`theme-${theme}`);
    };

    const getSystemTheme = () => {
        return window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    };

    // ----------------------------
    // INIT
    // ----------------------------

    if (mode === "auto") {
        applyTheme(getSystemTheme());
    } else {
        applyTheme(mode);
    }

    // ----------------------------
    // AUTO REACT (если система поменялась)
    // ----------------------------

    if (mode === "auto") {
        window.matchMedia("(prefers-color-scheme: dark)")
            .addEventListener("change", (e) => {
                applyTheme(e.matches ? "dark" : "light");
            });
    }

    // ----------------------------
    // MANUAL TOGGLE (кнопка)
    // ----------------------------

    if (btn) {
        btn.addEventListener("click", () => {

            // manual override
            const isDark = body.classList.contains("theme-dark");

            applyTheme(isDark ? "light" : "dark");

        });
    }

});
