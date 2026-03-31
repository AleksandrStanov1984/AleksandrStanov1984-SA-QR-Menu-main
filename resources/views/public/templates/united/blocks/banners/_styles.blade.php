<style>
    .menu-banners {
        margin-bottom: 16px;
        padding: 0 12px;
    }

    .banner-carousel {
        position: relative;
        width: 100%;
    }

    .banner-viewport {
        overflow: hidden;
    }

    .banner-track {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 6px 0;
    }

    .banner-track::-webkit-scrollbar {
        display: none;
    }

    .banner-item {
        flex: 0 0 auto;
        border-radius: 14px;
        overflow: hidden;
    }

    .banner-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .banner-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
    }

    .banner-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .banner-dot.active {
        background: #fff;
        transform: scale(1.2);
    }

    /* MOBILE */
    @media (max-width: 639px) {
        .banner-item {
            width: 100%;
        }
    }

    /* TABLET */
    @media (min-width: 640px) and (max-width: 1023px) {
        .banner-item {
            width: calc((100% - 12px) / 2);
        }
    }

    /* DESKTOP */
    @media (min-width: 1024px) {
        .banner-item {
            width: calc((100% - 24px) / 3);
        }
    }
</style>
