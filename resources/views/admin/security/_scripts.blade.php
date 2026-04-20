<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('SECURITY SCRIPT LOADED');

        const t = window.UI_LANG || {};

        document.querySelectorAll('.modal-form').forEach(form => {

            const submitBtn = form.querySelector('.js-submit');
            if (!submitBtn) return;

            // ================= HELPERS =================

            const showMessage = (el, text, type = 'error') => {
                const field = el.closest('.modal-form__field');
                let msg = field.querySelector('.input-msg');

                if (!msg) {
                    msg = document.createElement('div');
                    msg.className = 'input-msg';
                    field.appendChild(msg);
                }

                msg.textContent = text;
                msg.classList.remove('error', 'success');
                msg.classList.add(type === 'error' ? 'error' : 'success');
            };

            const clearMessage = (el) => {
                const field = el.closest('.modal-form__field');
                const msg = field.querySelector('.input-msg');
                if (msg) msg.remove();
            };

            const setValid = (el) => {
                el.classList.add('is-valid');
                el.classList.remove('is-invalid');
            };

            const setInvalid = (el) => {
                el.classList.add('is-invalid');
                el.classList.remove('is-valid');
            };

            const clearState = (el) => {
                el.classList.remove('is-valid', 'is-invalid');
                clearMessage(el);
            };

            const disable = () => submitBtn.disabled = true;
            const enable = () => submitBtn.disabled = false;

            // ================= EMAIL =================

            const emailInput = form.querySelector('input[name="new_email"]');
            const emailCurrentEl = form.querySelector('.input-readonly');
            const passInput = form.querySelector('input[name="current_password"]');

            if (emailInput && emailCurrentEl) {

                const currentEmail = emailCurrentEl.textContent.trim().toLowerCase();

                const validateEmailForm = () => {

                    const emailVal = emailInput.value.trim().toLowerCase();
                    const passVal = passInput?.value || '';

                    clearState(emailInput);

                    let emailOk = false;
                    let passOk = false;

                    // EMAIL
                    if (!emailVal) {
                        emailOk = false;
                    } else if (emailVal === currentEmail) {
                        setInvalid(emailInput);
                        showMessage(emailInput, t.email_same || 'Ошибка');
                    } else {
                        setValid(emailInput);
                        showMessage(emailInput, t.email_ok || 'OK', 'success');
                        emailOk = true;
                    }

                    if (passInput) {
                        passOk = passVal.length >= 8;
                    } else {
                        passOk = true;
                    }

                    submitBtn.disabled = !(emailOk && passOk);
                };

                emailInput.addEventListener('input', validateEmailForm);
                passInput?.addEventListener('input', validateEmailForm);
            }

            // ================= PASSWORD =================

            const passCurrent = form.querySelector('input[name="current_password"]');
            const passNew = form.querySelector('input[name="new_password"]');
            const passConfirm = form.querySelector('input[name="new_password_confirm"]');

            if (passNew && passConfirm) {

                const strengthEls = form.querySelectorAll('.password-strength span');

                const validatePasswordForm = () => {

                    const currentVal = passCurrent?.value || '';
                    const newVal = passNew.value;
                    const confirmVal = passConfirm.value;

                    clearState(passNew);
                    clearState(passConfirm);

                    if (!newVal || !confirmVal) {
                        disable();
                        return;
                    }

                    // mismatch
                    if (newVal !== confirmVal) {
                        setInvalid(passNew);
                        setInvalid(passConfirm);
                        showMessage(passConfirm, t.password_mismatch || 'Ошибка');
                        disable();
                        return;
                    }

                    // same as old
                    if (currentVal && newVal === currentVal) {
                        setInvalid(passNew);
                        showMessage(passNew, t.password_same || 'Ошибка');
                        disable();
                        return;
                    }

                    // strength rules
                    const rules = {
                        upper: /[A-Z]/.test(newVal),
                        lower: /[a-z]/.test(newVal),
                        number: /\d/.test(newVal),
                        symbol: /[^A-Za-z0-9]/.test(newVal),
                        length: newVal.length >= 8,
                    };

                    strengthEls.forEach(el => {
                        const key = el.dataset.rule;
                        el.classList.toggle('active', rules[key]);
                    });

                    const strong = Object.values(rules).every(Boolean);

                    if (!strong) {
                        setInvalid(passNew);
                        showMessage(passNew, t.password_weak || 'Ошибка');
                        disable();
                        return;
                    }

                    setValid(passNew);
                    setValid(passConfirm);
                    showMessage(passConfirm, t.password_ok || 'OK', 'success');

                    enable();
                };

                passNew.addEventListener('input', validatePasswordForm);
                passConfirm.addEventListener('input', validatePasswordForm);
                passCurrent?.addEventListener('input', validatePasswordForm);
            }

            // ================= INIT =================

            setTimeout(() => {
                form.querySelectorAll('input').forEach(i => {
                    i.dispatchEvent(new Event('input'));
                });
            }, 50);

        });
    });
</script>
