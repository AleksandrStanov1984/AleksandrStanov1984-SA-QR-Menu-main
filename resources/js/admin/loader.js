// resources/js/admin/loader.js
function initLoader() {
    const loader = document.getElementById('appLoader');
    if (!loader) {
        return;
    }

    let counter = 0;
    let timer = null;

    function show() {
        counter++;

        if (timer) return;

        timer = setTimeout(() => {
            loader.classList.remove('hidden');
        }, 150);
    }

    function hide() {
        counter--;

        if (counter > 0) return;

        counter = 0;

        clearTimeout(timer);
        timer = null;

        loader.classList.add('hidden');
    }

    function reset() {
        counter = 0;

        clearTimeout(timer);
        timer = null;

        loader.classList.add('hidden');
    }

    window.resetLoader = reset;

    window.showLoader = show;
    window.hideLoader = hide;

    // submit
    document.addEventListener('submit', function (e) {
        const form = e.target;

        if (form.hasAttribute('data-no-loader')) return;

        show();
    });

    // axios
    if (typeof window.axios !== 'undefined') {
        if (window.axios) {
            window.axios.interceptors.request.use(config => {
                show();
                return config;
            });

            window.axios.interceptors.response.use(
                response => {
                    hide();
                    return response;
                },
                error => {
                    hide();
                    return Promise.reject(error);
                }
            );
        }
    }

    // buttons
    document.addEventListener('submit', function (e) {
        e.target.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = true;
        });
    });

    // back cache
    window.addEventListener('pageshow', function () {
        hide();
    });

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoader);
} else {
    initLoader();
}
