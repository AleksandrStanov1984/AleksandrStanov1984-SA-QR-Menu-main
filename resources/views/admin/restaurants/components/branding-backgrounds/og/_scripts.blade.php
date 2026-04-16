{{-- admin/restaurants/components/branding-backgrounds/og/_scripts --}}
{{-- resources/views/admin/restaurants/components/branding-backgrounds/og/_scripts.blade.php --}}


<script>
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.og-input').forEach(input => {

            input.addEventListener('change', function () {

                const file = this.files[0];
                if (!file) return;

                const container = this.closest('.og-item');
                const img = container.querySelector('.og-preview');

                const reader = new FileReader();

                reader.onload = function (e) {
                    img.src = e.target.result;
                };

                reader.readAsDataURL(file);
            });

        });

    });
</script>
