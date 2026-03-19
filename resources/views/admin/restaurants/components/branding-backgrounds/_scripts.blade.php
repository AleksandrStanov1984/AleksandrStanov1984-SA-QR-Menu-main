<script>
    (function(){

        const form = document.querySelector('[data-branding-form]');
        if (!form) return;

        // =========================
        // 🔥 AUTO SAVE THEME MODE
        // =========================
        form.querySelectorAll('input[name="theme_mode"]').forEach(radio => {
            radio.addEventListener('change', () => {
                submitForm();
            });
        });

        // =========================
        // 🖼 PREVIEW IMAGES
        // =========================
        form.querySelectorAll('input[type="file"]').forEach(input => {

            input.addEventListener('change', (e) => {

                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();

                reader.onload = function(ev){

                    let img = input.closest('.col6')?.querySelector('img');

                    if (!img) {
                        img = document.createElement('img');
                        img.style.width = '100%';
                        img.style.borderRadius = '10px';
                        img.style.border = '1px solid var(--line)';
                        img.style.marginBottom = '8px';
                        input.before(img);
                    }

                    img.src = ev.target.result;
                };

                reader.readAsDataURL(file);
            });

        });

        // =========================
        // 🚀 SUBMIT
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
