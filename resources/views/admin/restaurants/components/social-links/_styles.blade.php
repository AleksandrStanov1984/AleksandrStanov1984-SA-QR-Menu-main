<style>
  details > summary::-webkit-details-marker { display:none; }
  details > summary::marker { content:""; }

  .sl-acc { border:1px solid var(--line); border-radius:16px; padding:10px; background:rgba(255,255,255,.03); }
  .sl-acc + .sl-acc { margin-top:10px; }

  .sl-acc-summary { cursor:pointer; }
  .sl-acc-head { display:flex; align-items:center; justify-content:space-between; gap:12px; }
  .sl-acc-caret { width:10px; height:10px; border-right:2px solid var(--mut); border-bottom:2px solid var(--mut); transform:rotate(45deg); opacity:.8; margin-left:8px; }

  .sl-acc[open] .sl-acc-caret { transform:rotate(225deg); }

  .sl-acc-body { margin-top:10px; }

  .sl-icon-box {
    width:64px;
    height:64px;
    border-radius:12px;
    overflow:hidden;
    border:1px solid var(--line);
    background:rgba(255,255,255,.04);
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .sl-acc.is-deleted { border-color: rgba(255,0,0,.45); background: rgba(255,0,0,.06); }
  .sl-acc.is-inactive { opacity:.55; }

  /* при inactive — пусть кнопки выглядят disabled визуально (реально мы их не показываем) */
</style>
