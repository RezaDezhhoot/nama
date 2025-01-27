@props(['loading' => true ,'with_bg' => true,'width' => 80 , 'height' => 80,'table' => false])
@if($loading && ! $table)
    <div class="big-loader">
        <div class="lds-roller" style="width: {{$width}}px;height: {{$height}}px">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
@endif
@if($table)
    <div wire:loading.class.remove="d-none" class="lds-roller d-none position-absolute  mx-auto" style="width: {{$width}}px;height: {{$height}}px;left: calc(50% - {{$width/2}}px);bottom: calc(50% - {{$height/2}}px);">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
@endif
