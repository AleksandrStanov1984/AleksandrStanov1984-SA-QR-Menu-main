{{-- resources/views/admin/restaurants/components/branding-backgrounds/_scripts.blade.php --}}


<script>
    (function(){

        const form = document.querySelector('[data-branding-form]');
        if (!form) return;

        // =========================
        // AUTO SAVE THEME MODE
        // =========================
        form.querySelectorAll('input[name="theme_mode"]').forEach(radio => {
            radio.addEventListener('change', submitForm);
        });

        // =========================
        // PREVIEW IMAGES
        // =========================
        form.querySelectorAll('input[type="file"]').forEach(input => {

            input.addEventListener('change', (e) => {

                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();

                reader.onload = function(ev){

                    // ищем правильный контейнер
                    const col = input.closest('.branding-col');
                    if (!col) return;

                    const wrap = col.querySelector('.branding-preview-wrap');
                    if (!wrap) return;

                    let img = wrap.querySelector('img');

                    // если нет картинки — создаём правильную
                    if (!img) {
                        img = document.createElement('img');
                        img.className = 'branding-preview';
                        wrap.innerHTML = ''; // убираем "No image"
                        wrap.appendChild(img);
                    }

                    img.src = ev.target.result;
                };

                reader.readAsDataURL(file);
            });

        });

        // =========================
        // SUBMIT
        // =========================
        function submitForm(){

            const btn = form.querySelector('button[type="submit"]');

            if (btn){
                btn.disabled = true;
                btn.innerText = 'Saving...';
            }

            form.submit();
        }

    })();
</script>
