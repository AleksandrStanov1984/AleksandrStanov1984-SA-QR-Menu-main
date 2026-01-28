const THEME_KEY = 'sa_theme'; // 'light' | 'dark' | 'auto'

function setThemeIcon(modeApplied, modeStored) {
    const el = document.querySelector('[data-theme-icon]');
    if (!el) return;

    // modeStored: auto/dark/light (что выбрал пользователь)
    // modeApplied: dark/light (что реально применилось)
    if (modeStored === 'auto') {
        el.textContent = (modeApplied === 'dark') ? '🌙' : '☀️';
        el.title = 'Auto';
        return;
    }

    el.textContent = (modeApplied === 'dark') ? '🌙' : '☀️';
    el.title = modeStored;
}

function computeApplied(modeStored) {
    if (modeStored === 'dark' || modeStored === 'light') return modeStored;
    // auto
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    return prefersDark ? 'dark' : 'light';
}

function applyTheme(modeStored, { persist = false } = {}) {
    const root = document.documentElement;

    // если ключа нет — один раз ставим auto
    if (!localStorage.getItem(THEME_KEY)) {
        localStorage.setItem(THEME_KEY, 'auto');
    }

    const applied = computeApplied(modeStored);

    root.setAttribute('data-theme', applied);

    // ВАЖНО: не перетирать localStorage на каждом apply — только когда реально меняем режим
    if (persist) {
        localStorage.setItem(THEME_KEY, modeStored);
    }

    setThemeIcon(applied, modeStored);
}

function initTheme() {
    const saved = localStorage.getItem(THEME_KEY) || 'auto';
    applyTheme(saved, { persist: false });

    // слушаем смену системной темы ТОЛЬКО когда пользователь в auto
    const mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
    if (mq && mq.addEventListener) {
        mq.addEventListener('change', () => {
            const cur = localStorage.getItem(THEME_KEY) || 'auto';
            if (cur === 'auto') applyTheme('auto', { persist: false });
        });
    }
}

/**
 * Сейчас делаем как ты просишь: кнопка = day/night.
 * То есть переключаем ТОЛЬКО light <-> dark.
 * (Auto остаётся опционально — можно вернуть позже отдельной кнопкой/в настройках.)
 */
function toggleTheme() {
    const cur = localStorage.getItem(THEME_KEY) || 'auto';
    const applied = document.documentElement.getAttribute('data-theme') || computeApplied(cur);

    const next = (applied === 'dark') ? 'light' : 'dark';
    applyTheme(next, { persist: true });
}

/* Drawer */
function drawerEls() {
    return {
        drawer: document.querySelector('[data-drawer]'),
        backdrop: document.querySelector('[data-drawer-backdrop]'),
    };
}
function openDrawer() {
    const { drawer, backdrop } = drawerEls();
    if (!drawer || !backdrop) return;

    document.body.classList.add('is-drawer-open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    const { drawer, backdrop } = drawerEls();
    if (!drawer || !backdrop) return;

    document.body.classList.remove('is-drawer-open');
    document.body.style.overflow = '';
}

/* Modal */
function modalEls() {
    const modal = document.querySelector('[data-modal]');
    if (!modal) return null;
    return {
        modal,
        img: modal.querySelector('[data-modal-el="img"]'),
        title: modal.querySelector('[data-modal-el="title"]'),
        desc: modal.querySelector('[data-modal-el="desc"]'),
        price: modal.querySelector('[data-modal-el="price"]'),
    };
}
function openModal(fromEl) {
    const m = modalEls();
    if (!m) return;

    const img = fromEl.getAttribute('data-modal-img') || '';
    const title = fromEl.getAttribute('data-modal-title') || '';
    const desc = fromEl.getAttribute('data-modal-desc') || '';
    const price = fromEl.getAttribute('data-modal-price') || '';

    if (m.img) {
        if (img) { m.img.src = img; m.img.style.display = ''; }
        else { m.img.removeAttribute('src'); m.img.style.display = 'none'; }
    }
    if (m.title) m.title.textContent = title;
    if (m.desc) m.desc.textContent = desc;
    if (m.price) m.price.textContent = price;

    m.modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    const m = modalEls();
    if (!m) return;
    m.modal.classList.remove('is-open');
    document.body.style.overflow = '';
}

/* Chips active on scroll */
function initChipsSpy() {
    const chipsWrap = document.querySelector('[data-chips]');
    const chips = chipsWrap ? Array.from(chipsWrap.querySelectorAll('[data-chip]')) : [];
    const sections = Array.from(document.querySelectorAll('.std-sec[id^="sec-"]'));

    if (!chipsWrap || !chips.length || !sections.length) return;

    const setActive = (hash) => {
        chips.forEach(c => c.classList.toggle('is-active', c.getAttribute('href') === hash));
    };

    // горизонтальный скролл контейнера чипсов БЕЗ scrollIntoView (он ломает вертикальный скролл)
    const scrollChipIntoCenterX = (chipEl) => {
        if (!chipEl) return;

        const wrapRect = chipsWrap.getBoundingClientRect();
        const chipRect = chipEl.getBoundingClientRect();

        const current = chipsWrap.scrollLeft;
        const chipCenter = (chipRect.left - wrapRect.left) + (chipRect.width / 2);
        const target = current + chipCenter - (wrapRect.width / 2);

        chipsWrap.scrollTo({ left: target, behavior: 'smooth' });
    };

    const obs = new IntersectionObserver((entries) => {
        const visible = entries
            .filter(e => e.isIntersecting)
            .sort((a,b) => a.boundingClientRect.top - b.boundingClientRect.top);

        if (!visible.length) return;

        const id = '#' + visible[0].target.id;
        setActive(id);

        const activeChip = chips.find(c => c.getAttribute('href') === id);
        if (activeChip) scrollChipIntoCenterX(activeChip);
    }, { rootMargin: '-40% 0px -55% 0px', threshold: [0, 1] });

    sections.forEach(sec => obs.observe(sec));

    if (location.hash) setActive(location.hash);
}


/* Global click handler */
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const act = btn.getAttribute('data-action');

    if (act === 'toggle-theme') toggleTheme();

    if (act === 'open-menu') openDrawer();
    if (act === 'close-menu') closeDrawer();

    if (act === 'open-modal') openModal(btn);
    if (act === 'close-modal') closeModal();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDrawer();
        closeModal();
    }
});

// backdrop click closes
document.addEventListener('click', (e) => {
    if (e.target && e.target.matches('[data-drawer-backdrop]')) closeDrawer();
});

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initChipsSpy();
});
