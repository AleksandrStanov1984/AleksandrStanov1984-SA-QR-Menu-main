// resources/js/public/templates/united/theme/theme-switcher.js
/*
|--------------------------------------------------------------------------
| THEME SWITCHER
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const body = document.body;
    const btn  = document.getElementById("themeToggle");

    const mode = body.dataset.themeMode || "light";

    const bgLight = body.dataset.bgLight || "";
    const bgDark  = body.dataset.bgDark || "";

    // ----------------------------
    // APPLY THEME
    // ----------------------------
    const applyTheme = (theme) => {

        body.classList.remove("theme-light", "theme-dark");
        body.classList.add(`theme-${theme}`);

        applyBackground(theme);
    };

    // ----------------------------
    // APPLY BACKGROUND
    // ----------------------------
    const applyBackground = (theme) => {

        let bg = null;

        if (theme === "light") {
            bg = bgLight || null;
        }

        if (theme === "dark") {
            bg = bgDark || null;
        }

        if (!bg) {
            body.style.setProperty("--menu-bg-image", "none");
            return;
        }

        body.style.setProperty("--menu-bg-image", `url('${bg}')`);
    };

    // ----------------------------
    // SYSTEM THEME
    // ----------------------------
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
    // AUTO REACT
    // ----------------------------
    if (mode === "auto") {
        window.matchMedia("(prefers-color-scheme: dark)")
            .addEventListener("change", (e) => {
                applyTheme(e.matches ? "dark" : "light");
            });
    }

    // ----------------------------
    // MANUAL TOGGLE
    // ----------------------------
    if (btn) {
        btn.addEventListener("click", () => {
            const isDark = body.classList.contains("theme-dark");
            applyTheme(isDark ? "light" : "dark");
        });
    }

});
