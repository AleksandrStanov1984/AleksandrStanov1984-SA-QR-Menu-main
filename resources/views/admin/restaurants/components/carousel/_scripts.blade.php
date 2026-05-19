<script>

document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById(
        'carouselToggle'
    );

    const sourceInput = document.querySelector(
        'input[name="carousel_source"]'
    );

    const sourceBlock = document.getElementById(
        'carouselSourceBlock'
    );

    const categoryBlock = document.getElementById(
        'carouselCategoryBlock'
    );

    const categoryInput = document.querySelector(
        'input[name="carousel_category_id"]'
    );

    const subcategoryInput = document.querySelector(
        'input[name="carousel_subcategory_id"]'
    );

    const subcategoryWrapper = document.getElementById(
        'carouselSubcategoryWrapper'
    );

    const subcategoryMenu = document.getElementById(
        'carouselSubcategoryMenu'
    );

    const subcategoryBtn = document.getElementById(
        'carouselSubcategoryBtn'
    );

    if (!toggle)
        return;

    // =====================
    // RESET
    // =====================
    function resetCategoryState() {

        if (categoryInput) {
            categoryInput.value = '';
        }

        resetSubcategoryState();
    }

    function resetSubcategoryState() {

        if (subcategoryInput) {
            subcategoryInput.value = '';
        }

        if (subcategoryBtn) {

            subcategoryBtn.textContent =
                window.carouselLangAll || 'All';
        }

        if (subcategoryMenu) {
            subcategoryMenu.innerHTML = '';
        }

        if (subcategoryWrapper) {

            subcategoryWrapper.style.display =
                'none';
        }
    }

    // =====================
    // SUBCATEGORY BUILD
    // =====================
    function buildSubcategories(categoryId) {

        if (!subcategoryWrapper || !subcategoryMenu)
            return;

        const allSubcategories =
            window.carouselSubcategories || {};

        console.log(
            'carouselSubcategories:',
            allSubcategories
        );

        console.log(
            'selected category:',
            categoryId
        );

        const items =
            allSubcategories[String(categoryId)] || [];

        console.log(
            'subcategory items:',
            items
        );

        // =====================
        // NO CHILDREN
        // =====================
        if (!Array.isArray(items) || items.length === 0) {

            subcategoryWrapper.style.display =
                'none';

            subcategoryMenu.innerHTML = '';

            if (subcategoryInput) {
                subcategoryInput.value = '';
            }

            if (subcategoryBtn) {

                subcategoryBtn.textContent =
                    window.carouselLangAll || 'All';
            }

            return;
        }

        // =====================
        // SHOW WRAPPER
        // =====================
        subcategoryWrapper.style.display =
            'block';

        subcategoryMenu.innerHTML = '';

        // =====================
        // ALL OPTION
        // =====================
        const allOption =
            document.createElement('div');

        allOption.className =
            'ui-select-option active';

        allOption.dataset.value = '';

        allOption.textContent =
            window.carouselLangAll || 'All';

        allOption.addEventListener('click', () => {

            if (subcategoryInput) {
                subcategoryInput.value = '';
            }

            if (subcategoryBtn) {

                subcategoryBtn.textContent =
                    window.carouselLangAll || 'All';
            }

            subcategoryMenu
                .querySelectorAll('.ui-select-option')
                .forEach(el => {
                    el.classList.remove('active');
                });

            allOption.classList.add('active');

        });

        subcategoryMenu.appendChild(allOption);

        // =====================
        // REAL OPTIONS
        // =====================
        items.forEach(item => {

            const option =
                document.createElement('div');

            option.className =
                'ui-select-option';

            option.dataset.value =
                item.id;

            option.textContent =
                item.title;

            option.addEventListener('click', () => {

                if (subcategoryInput) {

                    subcategoryInput.value =
                        item.id;
                }

                if (subcategoryBtn) {

                    subcategoryBtn.textContent =
                        item.title;
                }

                subcategoryMenu
                    .querySelectorAll('.ui-select-option')
                    .forEach(el => {
                        el.classList.remove('active');
                    });

                option.classList.add('active');

            });

            subcategoryMenu.appendChild(option);

        });
    }

    // =====================
    // VISIBILITY
    // =====================
    function syncVisibility() {

        // SOURCE BLOCK
        if (sourceBlock) {

            sourceBlock.classList.toggle(
                'active',
                toggle.checked
            );
        }

        // CATEGORY BLOCK
        if (categoryBlock) {

            const isCategory =
                sourceInput?.value === 'category';

            categoryBlock.style.display =
                (toggle.checked && isCategory)
                    ? 'block'
                    : 'none';

            if (!isCategory) {

                resetCategoryState();
            }
        }
    }

    // =====================
    // TOGGLE
    // =====================
    toggle.addEventListener(
        'change',
        syncVisibility
    );

    // =====================
    // SOURCE WATCHER
    // =====================
    document.querySelectorAll(
        '[data-name="carousel_source"] .ui-select-option'
    ).forEach(option => {

        option.addEventListener('click', () => {

            const source =
                option.dataset.value || '';

            if (sourceInput) {
                sourceInput.value = source;
            }

            setTimeout(() => {

                syncVisibility();

            }, 10);

        });

    });

    // =====================
    // CATEGORY WATCHER
    // =====================
    document.querySelectorAll(
        '[data-name="carousel_category_id"] .ui-select-option'
    ).forEach(option => {

        option.addEventListener('click', () => {

            const categoryId =
                String(option.dataset.value || '');

            console.log(
                'clicked category:',
                categoryId
            );

            // hidden input
            if (categoryInput) {

                categoryInput.value =
                    categoryId;
            }

            // button text
            const btn = document.querySelector(
                '[data-name="carousel_category_id"] .ui-select-btn'
            );

            if (btn) {

                btn.textContent =
                    option.textContent.trim();
            }

            // active state
            document.querySelectorAll(
                '[data-name="carousel_category_id"] .ui-select-option'
            ).forEach(el => {
                el.classList.remove('active');
            });

            option.classList.add('active');

            // rebuild
            buildSubcategories(categoryId);

        });

    });

    // =====================
    // INIT
    // =====================
    syncVisibility();

    if (categoryInput?.value) {

        buildSubcategories(
            String(categoryInput.value)
        );
    }

});

</script>
