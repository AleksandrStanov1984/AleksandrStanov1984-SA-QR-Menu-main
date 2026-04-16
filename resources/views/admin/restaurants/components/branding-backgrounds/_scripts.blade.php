{{-- admin/restaurants/components/branding-backgrounds/_scripts --}}
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

        document.addEventListener('change', function(e){

            const input = e.target.closest('[data-input]');
            if(!input) return;

            const key = input.dataset.input;
            const file = input.files[0];
            if(!file) return;

            const preview = document.querySelector(`[data-preview="${key}"]`);
            if(preview){
                preview.src = URL.createObjectURL(file);
            }

        });

        // =========================
        // DELETE BACKGROUND
        // =========================
        document.addEventListener('click', async (e) => {

            const btn = e.target.closest('[data-bg-delete]');
            if (!btn) return;

            if (btn.disabled) return;

            const url  = btn.getAttribute('data-bg-url');
            const type = btn.getAttribute('data-bg-delete');

            if (!url || !type) return;

            try {

                window.showLoader?.();

                const resp = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!resp.ok) {
                    throw new Error(await resp.text());
                }

                const fallback = "{{ app(\App\Services\ImageService::class)->url(null) }}";

                const preview = document.querySelector(`[data-preview="${type}"]`);
                if (preview) {
                    preview.src = fallback;
                }

                btn.style.display = 'none';

                showFlash(
                    window.UI_LANG.saved || 'Saved',
                    'success'
                );

            } catch (err) {
                console.error(err);

                showFlash(
                    window.UI_LANG.delete_error || 'Error',
                    'error'
                );

            } finally {
                window.hideLoader?.();
            }

        });

    })();
</script>
