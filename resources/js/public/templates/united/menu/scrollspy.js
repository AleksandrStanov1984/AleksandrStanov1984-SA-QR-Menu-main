/*
|--------------------------------------------------------------------------
| SCROLLSPY
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    const sections = document.querySelectorAll(".menu-section");
    const navLinks = document.querySelectorAll(".category-link");

    if (!sections.length) return;

    const observer = new IntersectionObserver(
        (entries) => {

            entries.forEach(entry => {

                if (!entry.isIntersecting) return;

                const id = entry.target.getAttribute("id");

                navLinks.forEach(link => {
                    link.classList.remove("active");

                    if (link.getAttribute("href") === "#" + id) {
                        link.classList.add("active");
                    }
                });

            });

        },
        {
            rootMargin: "-40% 0px -55% 0px",
            threshold: 0
        }
    );

    sections.forEach(section => {
        observer.observe(section);
    });

});
