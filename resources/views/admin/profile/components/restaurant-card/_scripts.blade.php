<script>
    document.addEventListener('DOMContentLoaded', () => {

        const form = document.getElementById('restaurantForm');
        if (!form) return;

        const submitBtn = form.querySelector('button[type="submit"]');

        const fields = {
            postal: form.querySelector('[name="postal_code"]'),
            house: form.querySelector('[name="house_number"]'),
            street: form.querySelector('[name="street"]'),
            city: form.querySelector('[name="city"]'),
            phone: form.querySelector('[name="phone"]'),
            email: form.querySelector('[name="contact_email"]'),
        };

        function setError(el, message) {
            if (!el) return;

            const hint = form.querySelector(`[data-error-for="${el.name}"]`);

            if (message) {
                el.classList.add('input-error');
                el.classList.remove('input-valid');

                if (hint) {
                    hint.textContent = message;
                    hint.classList.add('active');
                }

            } else {
                el.classList.remove('input-error');
                el.classList.add('input-valid');

                if (hint) {
                    hint.textContent = '';
                    hint.classList.remove('active');
                }
            }
        }

        function validateAll() {
            let valid = true;

            if (!/^\d{5}$/.test(fields.postal.value.trim())) {
                setError(fields.postal, '5 цифр');
                valid = false;
            } else setError(fields.postal, null);

            if (!/^[1-9]\d*[a-zA-Z]{0,2}$/.test(fields.house.value.trim())) {
                setError(fields.house, 'Напр: 12A');
                valid = false;
            } else setError(fields.house, null);

            if (!/^[a-zA-Zа-яА-ЯäöüÄÖÜß\s\-]+$/.test(fields.street.value.trim())) {
                setError(fields.street, 'Только текст');
                valid = false;
            } else setError(fields.street, null);

            if (!/^[a-zA-Zа-яА-ЯäöüÄÖÜß\s\-]+$/.test(fields.city.value.trim())) {
                setError(fields.city, 'Только текст');
                valid = false;
            } else setError(fields.city, null);

            if (!/^\+\d{10,15}$/.test(fields.phone.value.trim())) {
                setError(fields.phone, 'Формат: +491234567890');
                valid = false;
            } else setError(fields.phone, null);

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fields.email.value.trim())) {
                setError(fields.email, 'Некорректный email');
                valid = false;
            } else setError(fields.email, null);

            submitBtn.disabled = !valid;

            return valid;
        }

        // INPUT FILTERS
        fields.postal.addEventListener('input', () => {
            fields.postal.value = fields.postal.value.replace(/\D/g, '').slice(0,5);
            validateAll();
        });

        fields.house.addEventListener('input', () => {
            fields.house.value = fields.house.value.replace(/[^0-9a-zA-Z]/g, '');
            validateAll();
        });

        fields.street.addEventListener('input', () => {
            fields.street.value = fields.street.value.replace(/[^a-zA-Zа-яА-ЯäöüÄÖÜß\s\-]/g, '');
            validateAll();
        });

        fields.city.addEventListener('input', () => {
            fields.city.value = fields.city.value.replace(/[^a-zA-Zа-яА-ЯäöüÄÖÜß\s\-]/g, '');
            validateAll();
        });

        fields.phone.addEventListener('input', () => {
            let val = fields.phone.value;
            val = val.replace(/[^\d+]/g, '');
            val = val.replace(/(?!^)\+/g, '');
            fields.phone.value = val;
            validateAll();
        });

        fields.email.addEventListener('input', validateAll);

        form.addEventListener('submit', (e) => {
            if (!validateAll()) {
                e.preventDefault();
            }
        });

        validateAll();
    });
</script>
