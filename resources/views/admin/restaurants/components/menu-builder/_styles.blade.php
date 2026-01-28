<style>
  .mb-row{display:flex; gap:10px; align-items:center; justify-content:space-between;}
  .mb-left{display:flex; gap:10px; align-items:center; flex-wrap:wrap;}
  .mb-right{display:flex; gap:8px; align-items:center; flex-wrap:wrap;}
  .mb-muted{color: var(--mut); font-size:12px;}
  .mb-inactive{border-color: rgba(255,90,95,.35); background: rgba(255,90,95,.08);}
  .mb-sub{margin-left:18px; border-left: 1px dashed var(--line); padding-left:12px;}
  .mb-items{margin-top:10px; display:flex; flex-direction:column; gap:8px;}
  .mb-item{padding:10px; border: 1px solid var(--line); border-radius: 12px; background: rgba(255,255,255,.02);}
  .mb-item.mb-inactive{background: rgba(255,90,95,.08);}
  .mb-item-head{display:flex; align-items:center; justify-content:space-between; gap:10px;}
  .mb-item-title{font-weight:600;}
  .mb-mini{font-size:12px; color: var(--mut);}
  .mb-handle{cursor:grab; user-select:none; padding:2px 8px; border:1px solid var(--line); border-radius: 10px;}
  .mb-actions form{display:inline;}

  /* Modal base */
  .modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
  }

  .modal[aria-hidden="false"] {
    display: block;
  }

  /* Backdrop */
  .modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(2px);
  }

  /* Panel centered */
  .modal__panel {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);

    width: min(920px, calc(100vw - 48px));
    max-height: calc(100vh - 48px);
    overflow: auto;

    border-radius: 16px;
    background: var(--panel);
    border: 1px solid var(--line);
    box-shadow: 0 20px 60px rgba(0,0,0,.45);
    padding: 16px;
  }


</style>
