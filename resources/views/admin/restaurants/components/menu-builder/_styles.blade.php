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

  .modal__content{
    position: relative;
    z-index: 2;
    width: min(720px, 100%);
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 20px 80px rgba(0,0,0,.45);
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

 /* accordion header layout */
 .mb-acc-summary {
   cursor: pointer;
   padding: 6px 8px;           /* вот этот сдвиг делает как на скрине */
   border-radius: 12px;
 }
 .mb-acc-head{
   display:flex;
   align-items:center;
   justify-content:space-between;
   gap:14px;
 }
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
   white-space:normal;          /* без обрезания */
   overflow:visible;
 }
 .mb-acc-right{
   display:flex;
   align-items:center;
   gap:10px;
   flex:0 0 auto;
 }
 .mb-acc-price{
   font-weight:700;
   color:var(--text);
   min-width:80px;
   text-align:right;
 }

 /* caret справа */
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
   width:0; height:0;
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



</style>
