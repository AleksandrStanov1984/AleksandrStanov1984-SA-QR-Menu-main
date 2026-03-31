<script>
    (function () {

        const modal = document.getElementById('permModal');
        const titleEl = document.getElementById('permModalTitle');
        const bodyEl  = document.getElementById('permModalBody');

        const isEdit = @json(($mode ?? 'view') === 'edit');

        let currentGroup = null;

        function openPermModal(groupKey, groupTitle) {
            const tpl = document.getElementById('tplPermGroup_' + groupKey);
            if (!tpl) return;

            currentGroup = groupKey;

            titleEl.textContent = groupTitle || groupKey;
            bodyEl.innerHTML = '';
            bodyEl.appendChild(tpl.content.cloneNode(true));

            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closePermModal() {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            bodyEl.innerHTML = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('click', (e) => {

            const btn = e.target.closest('[data-open-perm-group]');
            if (btn) {
                openPermModal(
                    btn.getAttribute('data-group-key'),
                    btn.getAttribute('data-group-title')
                );
                return;
            }

            if (e.target.closest('[data-close-perm-modal]')) {
                closePermModal();
                return;
            }

        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                closePermModal();
            }
        });

        if (!isEdit) return;

        // 🔥 SAVE BUTTON (НОВАЯ ЛОГИКА)
        document.getElementById('permSaveBtn')?.addEventListener('click', async () => {

            const inputs = Array.from(
                document.querySelectorAll('#permModalBody input[type="checkbox"]')
            );

            const formData = new FormData();

            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // собираем только текущую модалку
            inputs.forEach(input => {
                const key = input.dataset.permKey;
                const value = input.checked ? 1 : 0;

                formData.append(`perm[${key}]`, value);
            });

            // (опционально) передаём группу
            if (currentGroup) {
                formData.append('group', currentGroup);
            }

            try {
                await fetch("{{ route('admin.restaurants.user_permissions', $restaurant) }}", {
                    method: 'POST',
                    body: formData
                });

                closePermModal();
                location.reload();

            } catch (e) {
                console.error(e);
                showToast(window.UI_LANG.error, 'error');
            }

        });

    })();
</script>
