<script>
    // =========================
    // LOADER
    // =========================
    function showQrLoader() {
        const loader = document.getElementById('qrLoader');
        if (loader) {
            loader.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function hideQrLoader() {
        const loader = document.getElementById('qrLoader');
        if (loader) {
            loader.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // =========================
    // FORCE RENDER FRAME
    // =========================
    function nextFrame() {
        return new Promise(resolve => requestAnimationFrame(resolve));
    }


    // =========================
    // GENERATE QR (FETCH)
    // =========================
    document.addEventListener('click', async (e) => {

        const btn = e.target.closest('[data-generate-qr]');
        if (!btn) return;

        e.preventDefault();

        const restaurantId = btn.dataset.restaurantId;

        const logoInput = document.getElementById('qrLogoInput');
        const bgInput   = document.getElementById('qrBgInput');

        const formData = new FormData();

        if (logoInput?.files[0]) {
            formData.append('logo', logoInput.files[0]);
        }

        if (bgInput?.files[0]) {
            formData.append('background', bgInput.files[0]);
        }

        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = '...';

        try {
            showQrLoader();

            await nextFrame();
            await nextFrame();

            const res = await fetch(`/admin/restaurants/${restaurantId}/qr/generate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                location.reload();
                return;
            }

            console.error('QR generate failed', data);
            hideQrLoader();

        } catch (err) {
            console.error(err);
            hideQrLoader();
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });


    // =========================
    // DOWNLOAD WITH LOADER (FINAL WORKING)
    // =========================
    document.addEventListener('click', async (e) => {

        const link = e.target.closest('[data-qr-download]');
        if (!link) return;

        e.preventDefault();

        const url = link.getAttribute('href');

        showQrLoader();

        // 🔥 создаём скрытый iframe
        let iframe = document.getElementById('qrDownloadFrame');

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'qrDownloadFrame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }

        // 🔥 запускаем download через iframe
        iframe.src = url;

        // 🔥 авто-hide loader (fallback)
        setTimeout(() => {
            hideQrLoader();
        }, 2000);

    });


    // =========================
    // DOWNLOAD DROPDOWN
    // =========================
    (function(){

        const btn = document.getElementById('qrDownloadBtn');
        const dropdown = document.getElementById('qrDropdown');

        if (!btn || !dropdown) return;

        btn.addEventListener('click', function(e){
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function(){
            dropdown.style.display = 'none';
        });

        dropdown.addEventListener('click', function(e){
            e.stopPropagation();
        });

    })();


    // =========================
    // MODAL ENGINE
    // =========================
    document.addEventListener('click', (e) => {

        const open = e.target.closest('[data-mb-open]');
        if (open) {
            const id = open.getAttribute('data-mb-open');
            const modal = document.getElementById(id);

            if (modal) {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            return;
        }

        const close = e.target.closest('[data-mb-close]');
        if (close) {
            const modal = close.closest('.modal');

            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            return;
        }

    });


    // =========================
    // ESC CLOSE
    // =========================
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        document.querySelectorAll('.modal.is-open').forEach(modal => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
    });


    // =========================
    // ENABLE QR BUTTON
    // =========================
    (function(){

        const logoInput = document.getElementById('qrLogoInput');
        const bgInput   = document.getElementById('qrBgInput');
        const btn       = document.getElementById('qrGenerateBtn');

        if (!logoInput || !bgInput || !btn) return;

        const updateState = () => {
            const hasLogo = logoInput.files.length > 0;
            const hasBg   = bgInput.files.length > 0;

            if (hasLogo || hasBg) {
                btn.disabled = false;
                btn.style.opacity = '1';
            } else {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            }
        };

        logoInput.addEventListener('change', updateState);
        bgInput.addEventListener('change', updateState);

    })();

</script>
