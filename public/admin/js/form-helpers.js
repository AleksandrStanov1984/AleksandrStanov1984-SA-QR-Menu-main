/**
 * Admin Form Helpers
 * Used for create/edit restaurant and similar forms
 */

(function () {
    'use strict';

    // ---------- utils ----------
    const capFirst = (s) => {
        s = (s || '').trim();
        if (!s) return s;
        return s.charAt(0).toUpperCase() + s.slice(1);
    };

    // ---------- capitalize first letter ----------
    document.querySelectorAll('[data-capitalize="first"]').forEach((inp) => {
        inp.addEventListener('blur', () => {
            inp.value = capFirst(inp.value);
        });

        inp.addEventListener('input', () => {
            const v = inp.value;
            if (v.length === 1) {
                inp.value = v.toUpperCase();
            }
        });
    });

    // ---------- remove digits ----------
    document.querySelectorAll('[data-no-digits="1"]').forEach((inp) => {
        inp.addEventListener('input', () => {
            inp.value = (inp.value || '').replace(/\d/g, '');
        });
    });

    // ---------- phone (E.164-like) ----------
    document.querySelectorAll('[data-phone-e164="1"]').forEach((inp) => {
        inp.addEventListener('input', () => {
            let v = inp.value || '';

            // keep only + and digits
            v = v.replace(/[^\d+]/g, '');

            // only one +
            if (v.includes('+')) {
                v = '+' + v.replace(/\+/g, '');
            } else {
                v = '+' + v;
            }

            // max 15 digits
            const digits = v.replace('+', '').slice(0, 15);
            inp.value = '+' + digits;
        });
    });

    document.addEventListener('click', function (e) {
        const interactive = e.target.closest(
            'input, button, select, textarea, .mb-handle, [data-no-accordion]'
        );

        if (interactive) {
            const summary = e.target.closest('summary');
            if (summary) {
                e.stopPropagation();
                e.preventDefault();
            }
        }
    });

})();
