{{-- resources/views/admin/restaurants/components/ui/toast/_scripts.blade.php --}}
{{-- admin/restaurants/components/ui/toast/_scripts --}}
<script>
    window.showToast = function (text) {
        const toast = document.getElementById('toast');
        toast.innerText = text;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 2500);
    };
</script>
