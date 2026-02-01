@php
  $mode = $mode ?? 'view';
@endphp

@foreach($grouped as $g => $items)
  <template id="tplPermGroup_{{ $g }}">
    <div class="perm-grid"
         style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:10px;">
      @foreach($items as $permKey => $label)
        @if($mode === 'edit')
          <label class="perm-item" style="
              display:flex;
              align-items:flex-start;
              justify-content:space-between;
              gap:12px;
              padding:12px 14px;
              border:1px solid var(--line);
              border-radius:12px;
          ">
              <span style="
                  flex:1 1 auto;
                  min-width:0;
                  white-space:normal;
                  overflow:visible;
                  word-break:break-word;
                  line-height:1.25;
              ">{{ $label }}</span>

              <input type="checkbox"
                     name="perm[{{ $permKey }}]"
                     value="1"
                     @checked(!empty($p[$permKey]))
                     style="margin-top:2px; flex:0 0 auto;">
          </label>

        @else
          <div class="perm-item" style="
              display:flex;
              align-items:flex-start;
              justify-content:space-between;
              gap:12px;
              padding:12px 14px;
              border:1px solid var(--line);
              border-radius:12px;
          ">
              <span style="
                  flex:1 1 auto;
                  min-width:0;
                  white-space:normal;
                  overflow:visible;
                  word-break:break-word;
                  line-height:1.25;
              ">{{ $label }}</span>

              <span class="pill green" style="font-size:12px; margin-top:1px;">
                  {{ __('admin.permissions.enabled') ?? 'Включено' }}
              </span>
          </div>

        @endif
      @endforeach
    </div>

<style>
  .perm-grid--modal{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap:10px;
  }

.perm-item span{
  white-space: normal !important;
  overflow: visible !important;
  text-overflow: clip !important;
  word-break: break-word;
}

  @media (max-width: 720px){
    .perm-grid--modal{
      grid-template-columns: 1fr;
    }
  }

</style>
  </template>
@endforeach
