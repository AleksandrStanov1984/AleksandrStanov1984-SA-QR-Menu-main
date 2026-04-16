{{-- admin/restaurants/components/carousel/_scripts --}}
{{-- resources/views/admin/restaurants/components/carousel/_scripts.blade.php --}}

<script>

    document.addEventListener('DOMContentLoaded', () => {

        const toggle = document.getElementById('carouselToggle');
        const block  = document.getElementById('carouselSourceBlock');

        if (!toggle || !block)
            return;

        toggle.addEventListener('change', () => {
            block.classList.toggle('active', toggle.checked);
        });

    });

</script>
