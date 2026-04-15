{{-- resources/views/public/templates/united/layout/scripts.blade.php --}}


@vite('resources/js/app.js')
@vite('resources/js/public/templates/united/modal/modal-hours.js')


<script>
    (function(){

        const body = document.body;
        const mode = body.dataset.themeMode || 'light';

        const applyTheme = (theme) => {
            body.classList.remove('theme-light','theme-dark');
            body.classList.add('theme-' + theme);
        };

        if(mode === 'auto'){

            const mq = window.matchMedia('(prefers-color-scheme: dark)');

            const update = () => {
                applyTheme(mq.matches ? 'dark' : 'light');
            };

            update();

            mq.addEventListener('change', update);

        }else{
            applyTheme(mode);
        }

    })();
</script>
