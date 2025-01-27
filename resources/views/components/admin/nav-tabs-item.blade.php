@props(['title' , 'key' ,'value' ,'icon','active' => false])
<li wire:click="$set('{{$key}}','{{$value}}')" class="nav-item cursor-pointer">
    <a class="nav-link {{$active ? 'active' : ''}}" data-toggle="tab">
        @if($icon)
            <span class="nav-icon">
                <i class="{{$icon}}"></i>
            </span>
        @endif
        <span  class="nav-text mx-1 font-size-lg">{{$title}}</span>
    </a>
</li>
