<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    @include('admin.layout.head')
</head>
<body>

@include('admin.layout.topbar')

@include('admin.layout.breadcrumbs')

<div class="wrap layout-with-sidebar">

    {{-- LEFT SIDEBAR --}}
    @auth
        @include('admin.restaurants.components.sidebar.index')
    @endauth

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        @if(session('status'))
            <div class="flash flash-success">
                {{ __(session('status')) }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash flash-error">
                {{ __(session('error')) }}
            </div>
        @endif

        @if($errors->any())
            <div class="flash flash-error">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </div>

</div>

@include('admin.layout.footer')

@include('admin.layout._modals')

<div id="qrLoader" class="qr-loader" style="display:none;" aria-hidden="true">
    <div class="qr-loader__backdrop"></div>
    <div class="qr-loader__spinner"></div>
</div>

<style>
    .qr-loader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        pointer-events: all;
    }

    .qr-loader__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(4px);
    }

    .qr-loader__spinner {
        position: relative;
        z-index: 1;
        width: 56px;
        height: 56px;
        border: 4px solid rgba(255,255,255,0.25);
        border-top-color: #fff;
        border-radius: 50%;
        animation: qr-spin 0.9s linear infinite;
    }

    .flash-success {
        background: #1f8f6a;
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .flash-error {
        background: #e74c3c;
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .flash-success,
    .flash-error {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-weight: 500;
    }

    @keyframes qr-spin {
        to {
            transform: rotate(360deg);
        }
    }

</style>

<script>
window.openModal = function(id){
    const m = document.getElementById(id);
    if(!m) return;
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

// ESC закрывает открытую модалку
document.addEventListener('keydown', function(e){
    if(e.key !== 'Escape') return;
    const opened = document.querySelector('.modal.is-open');
    if(opened) {
        opened.classList.remove('is-open');
        opened.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
});

window.openModal = function(id){
    const m = document.getElementById(id);
    if(!m) return;

    // жёстко чистим все input в модалке (и пароли, и email)
    m.querySelectorAll('input').forEach(inp => {
        inp.value = '';
        // сбросить также автозаполненные "ghost" значения
        inp.defaultValue = '';
    });

    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

document.addEventListener('click', function(e){
  const btn = e.target.closest('.pw-toggle');
  if(!btn) return;
  const input = btn.closest('.pw-field')?.querySelector('input');
  if(!input) return;
  input.type = (input.type === 'password') ? 'text' : 'password';
});

// ===== Mobile sidebar drawer =====
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

  // optional: на мобилке закрывать меню при клике на ссылку
  sidebar.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    if (window.matchMedia('(max-width: 900px)').matches) {
      closeSidebar();
    }
  });
})();

document.addEventListener('DOMContentLoaded', function () {
    const flashes = document.querySelectorAll('.flash-success');

    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';

            setTimeout(() => {
                el.remove();
            }, 300);
        }, 5000);
    });
});

</script>


</body>
</html>
