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
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>{{ __('admin.common.fix_these') }}</strong>
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

    // ✅ жёстко чистим все input в модалке (и пароли, и email)
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

</script>


</body>
</html>
