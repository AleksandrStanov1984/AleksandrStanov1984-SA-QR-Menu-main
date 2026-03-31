<style>
    .toast {
        position: fixed;

        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);

        background: #111;
        color: #fff;
        padding: 12px 16px;
        border-radius: 10px;

        opacity: 0;
        transition: .3s;

        z-index: 9999;
        pointer-events: none;
    }

    .toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
