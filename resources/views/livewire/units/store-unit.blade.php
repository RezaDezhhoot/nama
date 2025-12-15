@use('App\Enums\PageAction')
<div wire:init="init">
    @section('title',$header)
    <x-admin.form-control deleteAble="{{$mode === PageAction::UPDATE}}" :title="$header"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.nav-tabs-list>
            <x-admin.nav-tabs-item active="{{$tab =='base'}}" title="پایه" key="tab" value="base" icon="flaticon2-gear"/>
            <x-admin.nav-tabs-item active="{{$tab =='location'}}" title="لوکیشن" key="tab" value="location" icon="fas fa-city"/>
            <x-admin.nav-tabs-item active="{{$tab =='numbers'}}" title="شماره ها" key="tab" value="numbers" icon="flaticon2-phone"/>
        </x-admin.nav-tabs-list>
        <x-admin.forms.validation-errors/>
        <div class="card-body {{$tab != 'base' ? 'd-none' : ''}}">
            <div class="row">
                <x-admin.forms.input type="text" width="3" :required="true" id="title" label="عنوان مرکز" wire:model.defer="title" />
                <x-admin.forms.input type="text" width="2"  id="code" label="شناسه یکتای واحد" wire:model.defer="code" />
                <x-admin.forms.input type="text" width="2" :disabled="true" id="systematic_code" label="شناسه یکتای واحد(سیستمی)" wire:model.defer="systematic_code" />
                <x-admin.forms.dropdown :data="$data['type']" :required="true" width="2"  id="type" label="نوع پروفایل" wire:model.live="type"/>
                @if(! $model || ! is_null($model->parent))
                    <x-admin.forms.select2
                        id="parent"
                        :data="$model?->parent?->toArray() ?? []"
                        text="title"
                        :required="$type != \App\Enums\UnitType::MOSQUE->value && $type != \App\Enums\UnitType::UNIVERSITY->value"
                        label="مرکز بالادست"
                        width="3"
                        ajaxUrl="{{route('admin.feed.units')}}"
                        wire:model.live="parent"/>
                @endif
                @if(sizeof($data['sub_type']) > 0)
                    <x-admin.forms.dropdown :data="$data['sub_type']" id="sub_type" label="نوع فرعی" wire:model.defer="sub_type"/>
                @endif
            </div>
            <div class="row">
                <x-admin.forms.checkbox width="2" id="armani" label="آرمانی" wire:model.defer="armani"/>
                <x-admin.forms.checkbox width="2" id="auto_accept" label="تایید خودکار درخواست ها" wire:model.defer="auto_accept"/>
            </div>
            <div class="row">
                <x-admin.forms.input type="text" width="3" id="responsible" label="نام مسئول" wire:model.defer="responsible" />
                <x-admin.forms.input type="text" width="3" id="responsible_phone" label="شماره مسئول" wire:model.defer="responsible_phone" />
                <x-admin.forms.dropdown width="2" :data="$data['gender']"  id="gender" label="جنسیت" wire:model.defer="gender"/>
                <x-admin.forms.input type="text" width="2" id="tell" label="شماره تماس مرکز" wire:model.defer="tell" />
                <x-admin.forms.input type="text" width="2" id="scope_activity" label="حوزه ی فعالیت های مرکز" wire:model.defer="scope_activity" />

                <x-admin.forms.input type="number" width="6" id="from_age" label="محدوه سن از" wire:model.defer="from_age" />
                <x-admin.forms.input type="number" width="6" id="to_age" label="محدوه سن تا"  wire:model.defer="to_age" />

                <x-admin.forms.text-area dir="auto" wire:model.defer="description" id="description" label="توضیحات" />
            </div>
        </div>
        <div class="card-body {{$tab != 'location' ? 'd-none' : ''}}">
            <div class="row">
                <x-admin.forms.input type="text" id="postal_code" label="کد پستی" wire:model.defer="postal_code" />
                <x-admin.forms.dropdown :data="$data['states']"  width="3"  id="state" label="استان" wire:model.defer="state"/>
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
                    label="منطقه"
                    :required="true"
                    width="2"
                    :ajaxUrl="$regionAjax"
                    wire:model.live="region"/>
                <x-admin.forms.select2
                    id="neighborhood"
                    :data="$neighborhood ?? []"
                    text="title"
                    label="محله"
                    :required="true"
                    width="2"
                    :ajaxUrl="$neighborhoodAjax"
                    wire:model.live="neighborhood"/>
                <x-admin.forms.select2
                    id="area"
                    :data="$area ?? []"
                    text="title"
                    label="ناحیه"
                    width="2"
                    :ajaxUrl="$areaAjax"
                    wire:model.live="area"/>
                <div class="col-12" x-data="{map: null , marker: null}">
                    <label> لوکیشن مسجد<span class="text-danger">*</span></label>
                    <div wire:ignore id="location"  style="height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 10px;"
                         x-init="
                                                  map = L.map('location').setView(['{{ $lat ?? 35.6892 }}', '{{$lng ?? 51.3890}}'], 13);
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
        <div class="card-body {{$tab != 'numbers' ? 'd-none' : ''}}">
            <div class="row">
                @if(! $parent)
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone1" label="شماره 1" wire:model.defer="phone1" />
                        <x-admin.forms.input type="text" width="6"  id="phone1_title" label="عنوان شماره 1" wire:model.defer="phone1_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone2" label="شماره 2" wire:model.defer="phone2" />
                        <x-admin.forms.input type="text" width="6"  id="phone2_title" label="عنوان شماره 2" wire:model.defer="phone2_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone3" label="شماره 3" wire:model.defer="phone3" />
                        <x-admin.forms.input type="text" width="6"  id="phone3_title" label="عنوان شماره 3" wire:model.defer="phone3_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone4" label="شماره 4" wire:model.defer="phone4" />
                        <x-admin.forms.input type="text" width="6"  id="phone4_title" label="عنوان شماره 4" wire:model.defer="phone4_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone5" label="شماره 5" wire:model.defer="phone5" />
                        <x-admin.forms.input type="text" width="6"  id="phone5_title" label="عنوان شماره 5" wire:model.defer="phone5_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone6" label="شماره 6" wire:model.defer="phone6" />
                        <x-admin.forms.input type="text" width="6"  id="phone6_title" label="عنوان شماره 6" wire:model.defer="phone6_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone7" label="شماره 7" wire:model.defer="phone7" />
                        <x-admin.forms.input type="text" width="6"  id="phone7_title" label="عنوان شماره 7" wire:model.defer="phone7_title" />
                    </div>
                    <div class="row col-12 col-md-6 border-bottom mb-2">
                        <x-admin.forms.input type="text" width="6"  id="phone8" label="شماره 8" wire:model.defer="phone8" />
                        <x-admin.forms.input type="text" width="6"  id="phone8_title" label="عنوان شماره 8" wire:model.defer="phone8_title" />
                    </div>
                @else
                    <x-admin.forms.select2
                        id="numbers"
                        :data="$model?->number_list_select2 ?? []"
                        :options="$model?->parent?->numbers ?? []"
                        text="text"
                        label="انتخاب شماره ها"
                        wire:model.live="numbers"
                        :multiple="true"
                    />
                @endif
            </div>
        </div>
    </div>

</div>
@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
@endpush
