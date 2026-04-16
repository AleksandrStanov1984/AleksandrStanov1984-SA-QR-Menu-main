// resources/js/public/templates/united/menu/scrollspy.js
/*
|--------------------------------------------------------------------------
| SCROLLSPY (FINAL - FIXED + CENTER ACTIVE)
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const nav = document.getElementById('categoryNav');
    if (!nav) return;

    const header = document.querySelector('.site-header');

    const sections = document.querySelectorAll(".menu-section");
    const navLinks = document.querySelectorAll(".category-link");

    if (!sections.length || !navLinks.length) return;

    /*
    |--------------------------------------------------------------------------
    | PLACEHOLDER (prevents jump)
    |--------------------------------------------------------------------------
    */

    const placeholder = document.createElement('div');
    placeholder.className = 'category-nav-placeholder';

    nav.parentNode.insertBefore(placeholder, nav);

    let navTop = nav.offsetTop;

    function getHeaderHeight() {
        return header ? header.offsetHeight : 80;
    }

    /*
    |--------------------------------------------------------------------------
    | STICKY CONTROL (FIXED)
    |--------------------------------------------------------------------------
    */

    function handleSticky() {

        const scrollY = window.scrollY;
        const headerHeight = getHeaderHeight();

        if (scrollY + headerHeight >= navTop) {

            if (!nav.classList.contains('is-fixed')) {

                nav.classList.add('is-fixed');

                nav.style.top = '0px';

                placeholder.style.height = nav.offsetHeight + 'px';
            }

        } else {

            if (nav.classList.contains('is-fixed')) {

                nav.classList.remove('is-fixed');

                nav.style.top = '';

                placeholder.style.height = '0px';
            }
        }
    }

    window.addEventListener('scroll', handleSticky);

    window.addEventListener('resize', () => {
        navTop = placeholder.offsetTop || nav.offsetTop;
    });

    /*
    |--------------------------------------------------------------------------
    | CENTER ACTIVE LINK
    |--------------------------------------------------------------------------
    */

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
    |--------------------------------------------------------------------------
    | INTERSECTION OBSERVER
    |--------------------------------------------------------------------------
    */

    const observer = new IntersectionObserver(
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
            rootMargin: "-30% 0px -60% 0px",
            threshold: 0
        }
    );

    sections.forEach(section => {
        observer.observe(section);
    });

    /*
    |--------------------------------------------------------------------------
    | CLICK SCROLL (OFFSET FIX)
    |--------------------------------------------------------------------------
    */

    navLinks.forEach(link => {

        link.addEventListener('click', (e) => {

            e.preventDefault();

            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;

            const headerHeight = getHeaderHeight();
            const navHeight = nav.offsetHeight;

            const OFFSET = navHeight;

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
