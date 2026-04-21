{{-- resources/views/admin/restaurants/components/qr/_scripts.blade.php --}}

<script>

    function nextFrame() {
        return new Promise(resolve => requestAnimationFrame(resolve));
    }

    function setLoading(state) {
        document.body.classList.toggle('is-loading', state);

        if (state) {
            window.showLoader?.();
        } else {
            window.hideLoader?.();
        }
    }


    // GENERATE QR
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
            setLoading(true);

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

            if (data.success || data.status) {
                location.reload();
                return;
            }

            console.error('QR generate failed', data);

        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    // DOWNLOAD
    document.addEventListener('click', async (e) => {

        const link = e.target.closest('[data-qr-download]');
        if (!link) return;

        e.preventDefault();

        const url = link.getAttribute('href');

        setLoading(true);

        let iframe = document.getElementById('qrDownloadFrame');

        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'qrDownloadFrame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }

        iframe.src = url;

        setTimeout(() => {
            setLoading(false);
        }, 2000);

    });

    // DROPDOWN
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

    // MODAL ENGINE
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

    // ESC
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        document.querySelectorAll('.modal.is-open').forEach(modal => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
    });

    // COPY
    document.querySelectorAll('[data-copy-url]').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.copyUrl;

            navigator.clipboard.writeText(url).then(() => {
                btn.innerText = '✓';

                setTimeout(() => {
                    btn.innerText = '📋';
                }, 1200);
            });
        });
    });

    // UPLOAD + REMOVE
    function bindQrUpload({
                              inputId,
                              previewId,
                              pickBtnId,
                              removeBtnId,
                              type
                          }) {

        const modal = document.getElementById('mbModalQrUpload');
        const restaurantId = modal?.dataset.restaurantId;

        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const pick = document.getElementById(pickBtnId);
        const remove = document.getElementById(removeBtnId);

        if (!input || !restaurantId) return;

        pick.addEventListener('click', () => input.click());

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);

            preview.src = url;
            preview.hidden = false;

            preview.dataset.existing = '0';

            remove.hidden = false;
        });

        // REMOVE
        remove.addEventListener('click', async () => {

            const isExisting = preview.dataset.existing === '1';

            if (!isExisting) {
                input.value = '';
                preview.src = '';
                preview.hidden = true;
                remove.hidden = true;
                return;
            }

            try {
                setLoading(true);

                const endpoint = type === 'logo'
                    ? `/admin/restaurants/${restaurantId}/qr/logo`
                    : `/admin/restaurants/${restaurantId}/qr/background`;

                const res = await fetch(endpoint, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await res.json();

                if (data.success) {
                    preview.src = '';
                    preview.hidden = true;
                    remove.hidden = true;

                    preview.dataset.existing = '0';

                    location.reload();
                }

            } catch (e) {
                console.error(e);
            } finally {
                setLoading(false);
            }
        });
    }

    bindQrUpload({
        inputId: 'qrLogoInput',
        previewId: 'qrLogoPreview',
        pickBtnId: 'qrLogoPick',
        removeBtnId: 'qrLogoRemove',
        type: 'logo'
    });

    bindQrUpload({
        inputId: 'qrBgInput',
        previewId: 'qrBgPreview',
        pickBtnId: 'qrBgPick',
        removeBtnId: 'qrBgRemove',
        type: 'background'
    });

    // ENABLE BUTTON
    document.addEventListener('click', (e) => {

        const open = e.target.closest('[data-mb-open]');
        if (!open) return;

        const id = open.getAttribute('data-mb-open');
        const modal = document.getElementById(id);

        if (!modal) return;

        setTimeout(() => {
            const logoInput = document.getElementById('qrLogoInput');
            const bgInput   = document.getElementById('qrBgInput');
            const btn       = document.getElementById('qrGenerateBtn');

            if (!logoInput || !bgInput || !btn) return;

            const updateState = () => {
                const hasLogo = logoInput.files.length > 0;
                const hasBg   = bgInput.files.length > 0;

                btn.disabled = !(hasLogo || hasBg);
                btn.style.opacity = btn.disabled ? '0.5' : '1';
            };

            logoInput.addEventListener('change', updateState);
            bgInput.addEventListener('change', updateState);

            updateState();

        }, 50);
    });

</script>
