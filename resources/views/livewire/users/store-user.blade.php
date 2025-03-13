@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'نقش'.(' '.$header ?? '') )
    <x-admin.form-control :deleteAble="false" :store="false" title="نقش"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <x-admin.nav-tabs-list>
            @foreach($data['items'] as $key => $value)
                <x-admin.nav-tabs-item :active="$key == $item" :title="$value" key="item" :value="$key" icon="far fa-newspaper"/>
            @endforeach

        </x-admin.nav-tabs-list>
        <div class="card-body ">
            <x-admin.form-section label="تنطیمات نقش کاربر">
                <x-admin.forms.dropdown  id="role" :data="$data['role']" label="نقش" wire:model.live="role"/>
                @if($role == \App\Enums\OperatorRole::MOSQUE_CULTURAL_OFFICER->value)
                    <x-admin.forms.dropdown :required="true" id="main_unit" :data="$data['main_units']" label="مرکز محوری" wire:model.defer="unit"/>
                @endif
{{--                <x-admin.forms.dropdown  id="item" :data="$data['items']" label="پروژه" wire:model.live="item"/>--}}
                @if($role == \App\Enums\OperatorRole::MOSQUE_HEAD_COACH->value && $item)
                    <x-admin.forms.dropdown :required="true" id="main_unit" :data="$data['main_units']" label="مرکز محوری" wire:model.live="main_unit"/>
                    <x-admin.forms.dropdown :required="true" id="unit" :data="$data['units']" label="مرکز" wire:model.defer="unit"/>
                @endif
                <div class="col-12 row {{ $role == \App\Enums\OperatorRole::AREA_INTERFACE->value ? '' : 'd-none' }}">
                    <x-admin.forms.select2 id="city" :required="true" :data="$city ?? []" text="title" label="شهر" width="3" ajaxUrl="{{route('admin.feed.cities')}}" wire:model.live="city"/>
                    <x-admin.forms.select2 id="region" :required="true" :data="$region ?? []" text="title" label="منظقه"  width="3" :ajaxUrl="$regionAjax" wire:model.live="region"/>
                    <x-admin.forms.select2 id="neighborhood" :data="$neighborhood ?? []" text="title" label="محله" width="3" :ajaxUrl="$neighborhoodAjax" wire:model.live="neighborhood"/>
                    <x-admin.forms.select2 id="area" :data="$area ?? []" text="title" label="ناحیه" width="3" :ajaxUrl="$areaAjax" wire:model.live="area"/>
                    <div class="col-12" x-data="{map: null , marker: null}">
                        <label> لوکیشن مسجد<span class="text-danger">*</span></label>
                        <div wire:ignore id="location"  style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 10px;"
                             x-init="
                                                  map = L.map('location').setView([35.6892, 51.3890], 13);
                                                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map)
                                                  marker = L.marker(['{{ $lat ?? 35.6892 }}', '{{$lng ?? 51.3890}}']).addTo(map);
                                                  map.on('click', function (e) {
                                                        lat = e.latlng.lat;
                                                        lng = e.latlng.lng;
                                                        marker.setLatLng([lat, lng]);
                                                        @this.set('lat' ,lat)
                                                        @this.set('lng' , lng)
                                                    });
                                                  "
                        ></div>
                    </div>
                    <x-admin.forms.input type="text" width="6" :required="true" id="lat" label="عرض جغرافیایی Y" wire:model.defer="lat" />
                    <x-admin.forms.input type="text" width="6" :required="true" id="lng" label="طول جغرافیایی X" wire:model.defer="lng" />
                </div>
                <x-admin.forms.checkbox id="auto_accept" label="تایید خودکار درخواست/گزارش ها" wire:model.defer="auto_accept"/>
                <div class="col-12">
                    <button class="btn w-100 my-2 btn-outline-primary" type="button" wire:click="attachRole">ارسال نقش</button>
                </div>
            </x-admin.form-section>
            <x-admin.form-section label="نقش های کاربر">
                <div class="col-12  table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>عنوان پروژه</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><h2>{{ $data['items'][$item] }}</h2></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div >
                                    <table class="table table-info ">
                                        <thead>
                                        <tr>
                                            <th>عنوان نقش</th>
                                            <th>مرکز</th>
                                            <th>نوع مرکز</th>
                                            <th>شهر</th>
                                            <th>منطقه</th>
                                            <th>محله</th>
                                            <th>ناحیه</th>
                                            <th>تایید خودکار</th>
                                            <th>عملیات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($roles[$item] ?? [] as $r)
                                            <tr>
                                                <td>{{ $r->role->label() }}</td>
                                                <td>{{ $r->unit?->title ?? '-' }}</td>
                                                <td>{{ $r->unit ? ($r->unit->parent ? "معمولی" : "محوری") : '-'  }}</td>
                                                <td>{{ $r?->city?->title ?? '-' }}</td>
                                                <td>{{ $r?->region?->title ?? '-' }}</td>
                                                <td>{{ $r?->neighborhood?->title ?? '-' }}</td>
                                                <td>{{ $r?->area?->title ?? '-' }}</td>
                                                <td>{{ $r?->auto_accept ? 'بله' : '-' }}</td>
                                                <td><x-admin.delete-btn onclick="deleteRole('{{$r->id}}')"  /></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </x-admin.form-section>
        </div>
    </div>
</div>
@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        function deleteRole(id) {
            Swal.fire({
                title: 'حذف کردن',
                text: 'آیا از حذف کردن این نقش اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                if (result.value) {
                @this.call('deleteRole' , id)
                }
            })
        }
    </script>
@endpush
