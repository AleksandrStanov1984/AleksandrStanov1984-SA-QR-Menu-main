<style>
    /* =========================================
      CAROUSEL
      ========================================= */

    .carousel-card{
        margin-top:16px;
        width:100%;
    }

    /* делаем нормальную ширину */
    .carousel-card .card{
        width:100%;
    }

    /* ВНУТРЕННИЙ ОТСТУП КАК У ВСЕХ */
    .carousel-card{
        padding:16px;
    }

    /* ЗАГОЛОВОК */
    .carousel-card h2{
        margin-bottom:14px;
    }

    /* строки */
    .carousel-row{
        display:grid;
        grid-template-columns: minmax(140px, 180px) 1fr;
        align-items:center;
        gap:12px;

        padding:10px 12px;
        border-radius:12px;
        border:1px solid var(--line);
        background:rgba(255,255,255,.02);

        margin-bottom:8px;
    }

    /* источник */
    .carousel-source{
        margin-top:8px;
        padding:10px 12px;
        border-radius:12px;
        border:1px solid var(--line);
        background:rgba(255,255,255,.02);
    }

    /* заблокированный */
    .carousel-locked{
        padding:12px 14px;
        border-radius:12px;
        border:1px dashed var(--line);
        background:rgba(255,255,255,.02);
        opacity:.7;
    }

    /* кнопка */
    .carousel-actions{
        margin-top:16px;
        display:flex;
        justify-content:flex-end;
    }

    /* =========================================
       RESPONSIVE
       ========================================= */

    @media (max-width: 768px){
        .carousel-row{
            grid-template-columns: 1fr;
            gap:6px;
        }
    }
</style>
