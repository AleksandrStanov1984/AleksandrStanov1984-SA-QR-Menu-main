{{-- resources/views/admin/_scripts.blade.php --}}

<script>
    // ===== MODAL =====

    window.openModal = function(id){
        const m = document.getElementById(id);
        if(!m) return;

        m.querySelectorAll('input').forEach(inp => {
            inp.value = '';
            inp.defaultValue = '';
        });

        m.classList.add('is-open');
        m.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    window.closeModal = function(id){
        const m = document.getElementById(id);
        if(!m) return;

        m.classList.remove('is-open');
        m.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // ESC modal
    document.addEventListener('keydown', function(e){
        if(e.key !== 'Escape') return;

        const opened = document.querySelector('.modal.is-open');
        if(opened) {
            opened.classList.remove('is-open');
            opened.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    });

    // password toggle
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.pw-toggle');
        if(!btn) return;

        const input = btn.closest('.pw-field')?.querySelector('input');
        if(!input) return;

        input.type = (input.type === 'password') ? 'text' : 'password';
    });

    // ===== SIDEBAR =====

    (function () {
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.querySelector('[data-sidebar-backdrop]');
        const openBtn = document.querySelector('[data-sidebar-open]');
        const closeBtn = document.querySelector('[data-sidebar-close]');

        if (!sidebar || !openBtn) return;

        function openSidebar() {
            sidebar.classList.add('is-open');
            sidebar.setAttribute('aria-hidden', 'false');
            if (backdrop) backdrop.classList.add('is-open');
            document.body.classList.add('sb-lock');
        }

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            sidebar.setAttribute('aria-hidden', 'true');
            if (backdrop) backdrop.classList.remove('is-open');
            document.body.classList.remove('sb-lock');
        }

        openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });

        sidebar.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (!a) return;

            if (window.matchMedia('(max-width: 900px)').matches) {
                closeSidebar();
            }
        });
    })();

    // ===== FLASH AUTO HIDE =====

    document.addEventListener('DOMContentLoaded', function () {
        const flashes = document.querySelectorAll('.flash');

        flashes.forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.3s ease';
                el.style.opacity = '0';

                setTimeout(() => el.remove(), 300);
            }, 5000);
        });
    });

    // ===== FLASH (AJAX SUPPORT) =====
    window.showFlash = function(message, type = 'success') {

        const container = document.querySelector('.main-content');
        if (!container) return;

        const div = document.createElement('div');

        div.className = 'flash flash-' + type;
        div.textContent = message;

        // вставляем как Laravel делает (сверху)
        container.prepend(div);

        // авто скрытие (единый стиль)
        setTimeout(() => {
            div.style.transition = 'opacity 0.3s ease';
            div.style.opacity = '0';

            setTimeout(() => div.remove(), 300);
        }, 5000);
    };
</script>
