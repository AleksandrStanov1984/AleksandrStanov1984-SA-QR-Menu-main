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
        <aside class="admin-sidebar">
            @include('admin.restaurants.components.sidebar.index')
        </aside>
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
</script>


</body>
</html>
