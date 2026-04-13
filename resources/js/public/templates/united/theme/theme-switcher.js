/*
|--------------------------------------------------------------------------
| THEME SWITCHER (FINAL + BACKGROUND)
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const body = document.body;
    const btn  = document.getElementById("themeToggle");

    const mode = body.dataset.themeMode || "light";

    const bgLight = body.dataset.bgLight || "";
    const bgDark  = body.dataset.bgDark || "";

    const applyTheme = (theme) => {
        body.classList.remove("theme-light", "theme-dark");
        body.classList.add(`theme-${theme}`);

        let bg = theme === "dark"
            ? (bgDark || bgLight)
            : (bgLight || bgDark);

        if (bg) {
            body.style.setProperty("--menu-bg-image", `url('${bg}')`);
        }
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
