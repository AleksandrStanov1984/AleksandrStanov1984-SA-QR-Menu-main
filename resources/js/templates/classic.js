/* =========================================================
   SA QR Menu — CLASSIC JS (template-specific)
   - Chips: click => FILTER sections (toggle)
   - Drawer links: click => FILTER sections (and close drawer)
   ========================================================= */

(function () {
    /* ---------- FILTERING (sections) ---------- */
    const sections = Array.from(document.querySelectorAll('.std-sec[id^="sec-"]'));

    // current active section id (for filter)
    let activeSectionId = sections[0]?.id || null;

    function showAllSections() {
        sections.forEach(sec => sec.classList.remove('is-hidden'));
        activeSectionId = null;
    }

    function filterToSection(id) {
        sections.forEach(sec => {
            sec.classList.toggle('is-hidden', sec.id !== id);
        });
        activeSectionId = id;
    }

    /* ---------- CHIPS ---------- */
    const chipsWrap = document.querySelector('[data-chips]');
    const chips = chipsWrap ? Array.from(chipsWrap.querySelectorAll('[data-chip]')) : [];

    function setActiveChipById(idOrNull) {
        chips.forEach((a) => {
            const href = a.getAttribute('href') || '';
            const id = href.startsWith('#') ? href.slice(1) : null;
            const active = idOrNull && id === idOrNull;

            a.classList.toggle('is-active', !!active);

            if (active && chipsWrap) {
                const rect = a.getBoundingClientRect();
                const wrapRect = chipsWrap.getBoundingClientRect();
                if (rect.left < wrapRect.left || rect.right > wrapRect.right) {
                    a.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                }
            }
        });
    }

    // click chip => FILTER + scroll to section start
    if (chipsWrap) {
        chipsWrap.addEventListener('click', (e) => {
            const a = e.target.closest('[data-chip]');
            if (!a) return;

            const href = a.getAttribute('href');
            if (!href || !href.startsWith('#')) return;

            e.preventDefault();

            const id = href.slice(1);
            const target = document.getElementById(id);
            if (!target) return;

            // toggle logic: click active => show all
            if (activeSectionId === id) {
                showAllSections();
                setActiveChipById(null);
                return;
            }

            // filter + set active
            filterToSection(id);
            setActiveChipById(id);

            // scroll to section (now it is visible)
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // initial state: show all
    showAllSections();
    setActiveChipById(null);

    /* ---------- DRAWER LINKS FILTER ---------- */
    const drawer = document.querySelector('[data-drawer]');
    if (drawer) {
        drawer.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#sec-"]');
            if (!link) return;

            e.preventDefault();

            const href = link.getAttribute('href');
            const id = href ? href.slice(1) : null;
            const target = id ? document.getElementById(id) : null;
            if (!id || !target) return;

            // close drawer (универсально: просто убрать класс)
            document.body.classList.remove('is-drawer-open');
            document.body.style.overflow = '';

            // filter
            filterToSection(id);
            setActiveChipById(id);

            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
})();
