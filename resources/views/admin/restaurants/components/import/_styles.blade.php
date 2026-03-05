<style>
/* container look like cards */
.mb-import-acc{
  border: 0;
  border-radius: 0;
  background: transparent;
  padding: 0;
  margin: 0 0 8px 0;
}
.mb-acc-body{
  padding: 8px 0 12px 0; /* внутри контейнера без лишних рамок */
}


/* accordion header layout (как в menu-builder) */
.mb-acc-summary {
  cursor: pointer;
  padding: 6px 8px;
  border-radius: 12px;
}
.mb-acc-head{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:14px;
}
.mb-acc-head-full{ width:100%; }  /* 🔥 это фиксит caret "в угол" */
.mb-acc-left{
  display:flex;
  align-items:center;
  gap:10px;
  min-width:0;
  flex:1 1 auto;
}
.mb-acc-title{
  display:block;
  min-width:0;
  flex:1 1 auto;
  white-space:normal;
  overflow:visible;
}
.mb-acc-right{
  display:flex;
  align-items:center;
  gap:10px;
  flex:0 0 auto;
}
.mb-acc-body{ padding: 10px 12px; }

/* caret */
.mb-acc-caret{
  width:18px;
  height:18px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:10px;
  border:1px solid var(--line);
  background: rgba(255,255,255,.04);
  position:relative;
}
.mb-acc-caret::before{
  content:"";
  width:0;height:0;
  border-left:5px solid transparent;
  border-right:5px solid transparent;
  border-top:6px solid var(--text);
  opacity:.85;
}
details[open] .mb-acc-caret::before{
  transform: rotate(180deg);
}

/* hide default marker */
details > summary::-webkit-details-marker { display:none; }
details > summary::marker { content:""; }

/* import row layout (file left, buttons right) */
.mb-import-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}
.mb-import-left{
  display:flex;
  align-items:center;
  gap:10px;
  min-width:260px;
  flex:1 1 auto;
}
.mb-import-right{
  display:flex;
  align-items:center;
  gap:10px;
  flex:0 0 auto;
}

/* modal base (как в menu-builder) */
.modal[aria-hidden="true"] { display:none; }
.modal{
  position: fixed;
  inset: 0;
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 18px;
}
.modal__overlay{
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.55);
}
.modal__panel{
  position: relative;
  z-index: 2;
  width: min(920px, calc(100vw - 48px));
  max-height: calc(100vh - 48px);
  overflow: auto;
  border-radius: 16px;
  background: var(--card, rgba(20,28,44,.98));
  border: 1px solid var(--line);
  box-shadow: 0 20px 60px rgba(0,0,0,.45);
  padding: 16px;
}

.mb-code{
  white-space:pre;
  overflow:auto;
  padding: 10px;
  border-radius: 12px;
  border: 1px solid var(--line);
}

.mb-list{ padding-left: 18px; margin: 8px 0 0 0; }
</style>
