<script>
    window.showConfirm = function (message, onConfirm) {

        const modal = document.getElementById('confirmModal');
        const text = document.getElementById('confirmText');
        const btnYes = document.getElementById('confirmYes');
        const btnNo = document.getElementById('confirmNo');

        if (!modal || !text || !btnYes || !btnNo) {
            console.error('Confirm modal not found');
            return;
        }

        text.innerText = message;

        modal.classList.remove('hidden');

        const cleanup = () => {
            modal.classList.add('hidden');

            btnYes.onclick = null;
            btnNo.onclick = null;
        };

        btnYes.onclick = () => {
            cleanup();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        };

        btnNo.onclick = () => {
            cleanup();
        };
    };
</script>
