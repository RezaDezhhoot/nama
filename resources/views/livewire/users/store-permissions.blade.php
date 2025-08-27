@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'درسترسی های '.(' '.$header ?? '') )
    <x-admin.form-control :deleteAble="false" title="درسترسی ها"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات درسترسی های کاربر">
                <x-admin.forms.select2
                    id="roles"
                    :data="$user?->roles?->toArray()"
                    text="name"
                    :multiple="true"
                    label="نقش ها"
                    ajaxUrl="{{route('admin.feed.roles')}}"
                    wire:model.live="roles"/>

                <div class="row w-100 p-5">
                    @foreach($data['permissions'] as $key => $permission)
                        <div class="col-4 p-2 col-md-2 border">
                            <x-admin.forms.checkbox :label="$permission" :value="$key"
                                                    id="permissions-{{$key}}"
                                                    wire:model.defer="selectedPermissions"/>
                        </div>

                    @endforeach
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
