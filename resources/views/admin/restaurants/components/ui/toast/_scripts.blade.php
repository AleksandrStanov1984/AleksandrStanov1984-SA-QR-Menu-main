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
