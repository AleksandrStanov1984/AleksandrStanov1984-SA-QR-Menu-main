// resources/js/public/templates/united/menu/scrollspy.js

document.addEventListener("DOMContentLoaded", () => {

    const nav = document.getElementById('categoryNav');
    const group = document.getElementById('menuStickyGroup');
    const anchor = document.getElementById('menuStickyAnchor');

    if (!nav || !group || !anchor) return;

    const header = document.querySelector('.site-header');

    const sections = document.querySelectorAll(".menu-section");
    const navLinks = document.querySelectorAll(".category-link");

    if (!sections.length || !navLinks.length) return;

    /*
    |------------------------------------------------------------------
    | HELPERS
    |------------------------------------------------------------------
    */

    function getHeaderHeight() {
        return header ? header.offsetHeight : 0;
    }

    function getStickyOffset() {
        const headerHeight = getHeaderHeight();
        const navHeight = nav.offsetHeight;

        const search = document.querySelector('.menu-search-wrap');
        const searchHeight = search ? search.offsetHeight : 0;

        return headerHeight + navHeight + searchHeight + 8;
    }

    function centerActive(link) {
        const linkRect = link.getBoundingClientRect();
        const navRect = nav.getBoundingClientRect();

        const offset =
            linkRect.left -
            navRect.left -
            (navRect.width / 2) +
            (linkRect.width / 2);

        nav.scrollBy({
            left: offset,
            behavior: 'smooth'
        });
    }

    /*
    |------------------------------------------------------------------
    | 🔥 STICKY GROUP (ДВИЖЕНИЕ)
    |------------------------------------------------------------------
    */

    function updateStickyGroup() {

        const headerHeight = getHeaderHeight();
        const anchorTop = anchor.getBoundingClientRect().top;

        if (anchorTop <= headerHeight) {

            if (!group.classList.contains('is-fixed')) {

                anchor.style.height = group.offsetHeight + 'px';

                group.classList.add('is-fixed');

                group.style.top = '0px';
            }

        } else {

            if (group.classList.contains('is-fixed')) {

                group.classList.remove('is-fixed');

                group.style.top = '';

                anchor.style.height = '0px';
            }
        }
    }

    window.addEventListener('scroll', updateStickyGroup, { passive: true });
    window.addEventListener('resize', updateStickyGroup);

    updateStickyGroup();

    /*
    |------------------------------------------------------------------
    | 🔥 INTERSECTION OBSERVER
    |------------------------------------------------------------------
    */

    let observer;

    function createObserver() {

        if (observer) observer.disconnect();

        const offset = getStickyOffset();

        observer = new IntersectionObserver(
            (entries) => {

                entries.forEach(entry => {

                    if (!entry.isIntersecting) return;

                    const id = entry.target.getAttribute("id");

                    navLinks.forEach(link => {

                        const isMatch = link.getAttribute("href") === "#" + id;

                        link.classList.toggle("active", isMatch);

                        if (isMatch) {
                            centerActive(link);
                        }

                    });

                });

            },
            {
                rootMargin: `-${offset}px 0px -55% 0px`,
                threshold: 0.01
            }
        );

        sections.forEach(section => observer.observe(section));
    }

    createObserver();

    /*
    |------------------------------------------------------------------
    | RESIZE
    |------------------------------------------------------------------
    */

    window.addEventListener('resize', () => {
        createObserver();
    });

    /*
    |------------------------------------------------------------------
    | CLICK SCROLL
    |------------------------------------------------------------------
    */

    navLinks.forEach(link => {

        link.addEventListener('click', (e) => {

            e.preventDefault();

            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;

            const OFFSET = getStickyOffset();

            const y =
                target.getBoundingClientRect().top +
                window.scrollY -
                OFFSET;

            window.scrollTo({
                top: y,
                behavior: 'smooth'
            });

        });

    });

});
