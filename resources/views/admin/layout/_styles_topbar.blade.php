{{-- resources/views/admin/layout/_styles_topbar.blade.php --}}
{{-- admin/layout/_styles_topbar --}}
<style>
    /* ======================================
    MOBILE (все телефоны)
====================================== */
    @media (max-width: 768px){

        .layout-with-sidebar{
            display:block;
        }

        .sb-burger{
            display:inline-flex;
        }

        .admin-sidebar{
            position:fixed;
            top:0;
            left:-320px;
            width:312px;
            height:100vh;
            z-index:1001;
            border-radius:0;
            overflow:auto;
            transition:left .25s ease;
        }

        .admin-sidebar.is-open{
            left:0;
        }

        .sb-backdrop{
            display:block;
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.55);
            z-index:1000;
            opacity:0;
            pointer-events:none;
            transition:opacity .2s ease;
        }

        .sb-backdrop.is-open{
            opacity:1;
            pointer-events:auto;
        }

        body.sb-lock{
            overflow:hidden;
        }

        .topbar{
            display:flex;
            flex-direction:column;
            align-items:stretch;
        }

        .topbar__left{
            display:flex;
            flex-direction: row-reverse;
            align-items:center;
            gap:20%;
        }

        .topbar__left > div{
            display:flex;
            align-items:center;
            gap:6px;
        }

        .topbar__left > div{
            display:flex;
            flex-direction:column;
            align-items:flex-start;
            text-align:left;
        }

        .brand{
            text-align:left;
        }

        .mut{
            text-align:left;
        }

        .topbar__right{
            display:flex;
            gap:8px;
            flex-wrap:nowrap;
            overflow-x:auto;
        }

        .topbar__right > *{
            flex:0 0 auto;
        }

        .topbar__right .btn,
        .topbar__right select{
            font-size:12px;
            white-space:nowrap;
        }

        .brand{
            white-space:nowrap;
        }

        .card input,
        .card select,
        .card textarea{
            width:100%;
        }

        .perm-grid{
            grid-template-columns:1fr;
            grid-auto-flow:row;
        }

        .sb-burger{
            display:inline-flex;
            margin:0;
            flex-shrink:0;
            position:static;
        }

    }

    /* ======================================
       TABLET (iPad, Galaxy Tab)
    ====================================== */
    @media (min-width: 768px) and (max-width: 1200px){

        .layout-with-sidebar{
            display:flex;
        }

        .admin-sidebar{
            width:240px;
        }

        .main-content{
            min-width:0;
        }

        .banners-grid{
            grid-template-columns: repeat(2, 1fr);
        }

        .topbar{
            flex-direction:row;
            justify-content:space-between;
        }

        .crumbs{
            max-width:100%;
        }

        .wrap{
            max-width:100%;
            overflow-x:hidden;
        }

    }

    /* ======================================
       DESKTOP
    ====================================== */
    @media (min-width: 1441px){

        .crumbs{
            max-width:1200px;
        }

        .wrap{
            max-width:1400px;
        }

    }
</style>
