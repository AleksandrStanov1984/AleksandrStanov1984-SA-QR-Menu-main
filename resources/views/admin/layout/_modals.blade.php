<style>
    .modal-close {
    position: absolute;
    top: 14px;
    right: 14px;

    width: 32px;
    height: 32px;

    border-radius: 50%;
    border: 1px solid rgba(255,255,255,.15);
    background: rgba(255,255,255,.04);

    cursor: pointer;
    transition: all .2s ease;
}

.modal-close::before,
.modal-close::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;

    width: 14px;
    height: 2px;

    background: #fff;
    border-radius: 2px;

    transform-origin: center;
}

.modal-close::before {
    transform: translate(-50%, -50%) rotate(45deg);
}

.modal-close::after {
    transform: translate(-50%, -50%) rotate(-45deg);
}

.modal-close:hover {
    background: rgba(255,255,255,.12);
    border-color: rgba(255,255,255,.35);
}

.modal-close:active {
    transform: scale(.95);
}
</style>

{{-- Общий JS --}}
<script>
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-modal-close]');
    if (!btn) return;

    const modal = btn.closest('.modal');
    if (modal) modal.classList.remove('open');
});
</script>
