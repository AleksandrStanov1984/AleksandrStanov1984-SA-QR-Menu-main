{{-- resources/views/admin/restaurants/components/branding-backgrounds/og/_scripts.blade.php --}}


<script>
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.og-input').forEach(input => {

            const container = input.closest('.og-item');
            const img = container.querySelector('.og-preview');
            const btn = container.querySelector('button');

            if (btn) {
                btn.disabled = true;
            }

            input.addEventListener('change', function () {

                const file = this.files[0];

                if (!file) {
                    if (btn) btn.disabled = true;
                    return;
                }

                if (btn) btn.disabled = false;

                const reader = new FileReader();

                reader.onload = function (e) {
                    if (img) img.src = e.target.result;
                };

                reader.readAsDataURL(file);
            });

        });

    });
</script>
