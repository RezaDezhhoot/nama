@use('App\Enums\PageAction')
<div wire:init="init">
    @section('title',$header)
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" :title="$header"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body ">
            <div class="row">
                <x-admin.forms.input type="text" width="3" :required="true" id="title" label="عنوان مرکز" wire:model.defer="title" />
                <x-admin.forms.dropdown :data="$data['type']" :required="true" width="3"  id="type" label="نوع پروفایل" wire:model.defer="type"/>
                <x-admin.forms.dropdown :data="$data['sub_type']"  width="3"  id="sub_type" label="نوع فرعی" wire:model.defer="sub_type"/>
                <x-admin.forms.dropdown :data="$data['parent']"  width="3"  id="parent" label="مرکز بالادست" wire:model.defer="parent"/>

                <x-admin.forms.select2
                    id="city"
                    :data="$city ?? []"
                    text="title"
                    :required="true"
                    label="شهر"
                    width="3"
                    ajaxUrl="{{route('admin.feed.cities')}}"
                    wire:model.live="city"/>
                <x-admin.forms.select2
                    id="region"
                    :data="$region ?? []"
                    text="title"
                    label="منظقه"
                    :required="true"
                    width="3"
                    :ajaxUrl="$regionAjax"
                    wire:model.live="region"/>
                <x-admin.forms.select2
                    id="neighborhood"
                    :data="$neighborhood ?? []"
                    text="title"
                    label="محله"
                    :required="true"
                    width="3"
                    :ajaxUrl="$neighborhoodAjax"
                    wire:model.live="neighborhood"/>
                <x-admin.forms.select2
                    id="area"
                    :data="$area ?? []"
                    text="title"
                    label="ناحیه"
                    width="3"
                    :ajaxUrl="$areaAjax"
                    wire:model.live="area"/>
                <x-admin.forms.checkbox id="auto_accept" label="تایید خودکار درخواست ها" wire:model.defer="auto_accept"/>

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
        </div>
    </div>

</div>
@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


@endpush
