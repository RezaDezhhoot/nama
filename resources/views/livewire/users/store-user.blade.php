@use('App\Enums\PageAction')
<div wire:init="init" class="h-100">
    <x-admin.big-loader :loading="$loading"/>
    @section('title', 'نقش'.(' '.$header ?? '') )
    <x-admin.form-control :deleteAble="false" :store="false" title="نقش"/>
    <div class="card card-custom h-100 gutter-b example example-compact">
        <div class="card-body ">
            <x-admin.form-section label="نقش های سامانه پروفایل">
                @if($profileRoles)
                    <div class="col-12  table-responsive">
                        <h4>نقش های ستادی</h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان</th>
                                <th>نوع نقش</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($profileRoles['setad_roles'] ?? [] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r }}</td>
                                    <td>ستادی</td>
                                </tr>
                            @endforeach
                            @foreach($profileRoles['educational_roles'] ?? [] as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r }}</td>
                                    <td>آمورشی</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="col-12">
                        <p class="alert alert-warning">
                            مشکلی در استعلام نقش های سامانه پروفایل وجود دارد!
                        </p>
                    </div>
                @endif
            </x-admin.form-section>
        </div>
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <x-admin.nav-tabs-list>
            @foreach($data['items'] as $key => $value)
                <x-admin.nav-tabs-item :active="$key == $item" :title="$value" key="item" :value="$key" icon="far fa-newspaper"/>
            @endforeach
        </x-admin.nav-tabs-list>
        <div class="card-body">
            <x-admin.form-section label="تنطیمات نقش کاربر">
                <x-admin.forms.dropdown :required="true" id="role" :data="$data['role']" label="نقش" wire:model.live="role"/>
                <div class="col-12 row {{$role == \App\Enums\OperatorRole::MOSQUE_CULTURAL_OFFICER->value ? '' :"d-none"}}" >
                    <x-admin.forms.select2
                        id="unit"
                        :data="[]"
                        text="title"
                        :required="true"
                        label="مرکز محوری"
                        ajaxUrl="{{route('admin.feed.units')}}"
                        wire:model.live="unit"/>
                    <x-admin.forms.input type="number" help="برحسب ساعت"  id="auto_accept_period" label="تایید خودکار درخواست/گزارش ها بعد از گذشت" wire:model.defer="auto_accept_period" />
                </div>
                <div class="col-12 row {{ $role == \App\Enums\OperatorRole::MOSQUE_HEAD_COACH->value && $item ? '' : 'd-none' }}">
                    <x-admin.forms.select2
                        id="main_unit"
                        :data="[]"
                        text="title"
                        :required="true"
                        label="مرکز محوری"
                        ajaxUrl="{{route('admin.feed.units')}}"
                        wire:model.live="main_unit"/>

                    <x-admin.forms.select2
                        id="unit2"
                        :data="[]"
                        text="title"
                        :required="true"
                        label="مرکز"
                        ajaxUrl="{{route('admin.feed.units')}}"
                        wire:model.live="unit"/>
                    @if($itemModel->type === \App\Enums\UnitType::SCHOOL)
                        <x-admin.forms.dropdown :required="true" id="coach_type" :data="$data['coach_type']" label="نوع مربی" wire:model.defer="coach_type"/>
                    @endif
                </div>
                <div class="col-12 row {{ $role == \App\Enums\OperatorRole::AREA_INTERFACE->value ? '' : 'd-none' }}">
                    <x-admin.forms.input type="number" help="برحسب ساعت"  id="notify_period" label="ارسال پیامک یاداوری پس از گذشت" wire:model.defer="notify_period" />
                    <x-admin.forms.select2 id="city" :required="true" :data="$city ?? []" text="title" label="شهر" width="3" ajaxUrl="{{route('admin.feed.cities')}}" wire:model.live="city"/>
                    <x-admin.forms.select2 id="region" :required="true" :data="$region ?? []" text="title" label="منطقه"  width="3" :ajaxUrl="$regionAjax" wire:model.live="region"/>
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
{{--                <x-admin.forms.checkbox id="auto_accept" label="تایید خودکار درخواست/گزارش ها" wire:model.defer="auto_accept"/>--}}

               <div class="row col-12 col-md-6 border-bottom mb-2">
                   <x-admin.forms.input type="text" width="6"  id="sheba1" label="شماره شبا 1" wire:model.defer="sheba1" />
                   <x-admin.forms.input type="text" width="6"  id="sheba1_title" label="عنوان شماره شبا 1" wire:model.defer="sheba1_title" />
               </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba2" label="شماره شبا 2" wire:model.defer="sheba2" />
                    <x-admin.forms.input type="text" width="6"  id="sheba2_title" label="عنوان شماره شبا 2" wire:model.defer="sheba2_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba3" label="شماره شبا 3" wire:model.defer="sheba3" />
                    <x-admin.forms.input type="text" width="6"  id="sheba3_title" label="عنوان شماره شبا 3" wire:model.defer="sheba3_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba4" label="شماره شبا 4" wire:model.defer="sheba4" />
                    <x-admin.forms.input type="text" width="6"  id="sheba4_title" label="عنوان شماره شبا 4" wire:model.defer="sheba4_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba5" label="شماره شبا 5" wire:model.defer="sheba5" />
                    <x-admin.forms.input type="text" width="6"  id="sheba5_title" label="عنوان شماره شبا 5" wire:model.defer="sheba5_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba6" label="شماره شبا 6" wire:model.defer="sheba6" />
                    <x-admin.forms.input type="text" width="6"  id="sheba6_title" label="عنوان شماره شبا 6" wire:model.defer="sheba6_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba7" label="شماره شبا 7" wire:model.defer="sheba7" />
                    <x-admin.forms.input type="text" width="6"  id="sheba7_title" label="عنوان شماره شبا 7" wire:model.defer="sheba7_title" />
                </div>
                <div class="row col-12 col-md-6 border-bottom mb-2">
                    <x-admin.forms.input type="text" width="6"  id="sheba8" label="شماره شبا 8" wire:model.defer="sheba8" />
                    <x-admin.forms.input type="text" width="6"  id="sheba8_title" label="عنوان شماره شبا 8" wire:model.defer="sheba8_title" />
                </div>

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
                                            <th>تاریخ ثبت</th>
                                            <th>ثبت شده توسط</th>
                                            <th>ادیت شده توسط</th>
                                            <th>مرکز</th>
                                            <th>نوع مرکز</th>
                                            <th>شهر</th>
                                            <th>منطقه</th>
                                            <th>محله</th>
                                            <th>ناحیه</th>
                                            <th>نوع مربی</th>
                                            <th>تایید خودکار</th>
                                            <th>ارسال پیامک یاداوری</th>
                                            <th>شبا 1</th>
                                            <th>شبا 2</th>
                                            <th>شبا 3</th>
                                            <th>شبا 4</th>
                                            <th>شبا 5</th>
                                            <th>شبا 6</th>
                                            <th>شبا 7</th>
                                            <th>شبا 8</th>
                                            <th>عملیات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($roles[$item] ?? [] as $r)
                                            <tr>
                                                <td>{{ $r->role->label($r->item->type) }}</td>
                                                <td>{{ persian_date($r->created_at) }}</td>
                                                <td>{{ $r->causer?->name ?? 'نامشخص' }}</td>
                                                <td>{{ $r->editor?->name ?? 'نامشخص' }}</td>
                                                <td>{{ $r->unit?->full ?? '-' }}</td>
                                                <td>{{ $r->unit ? ($r->unit->parent ? "معمولی" : "محوری") : '-'  }}</td>
                                                <td>{{ $r?->city?->title ?? '-' }}</td>
                                                <td>{{ $r?->region?->title ?? '-' }}</td>
                                                <td>{{ $r?->neighborhood?->title ?? '-' }}</td>
                                                <td>{{ $r?->area?->title ?? '-' }}</td>
                                                <td>{{ $r?->school_coach_type?->label() ?? '-' }}</td>
                                                <td>{{ $r?->auto_accept_period ? $r?->auto_accept_period.' ساعت ' : '-' }}</td>
                                                <td>{{ $r?->notify_period ? $r?->notify_period.' ساعت ' : '-' }}</td>
                                                <td>{{ $r->sheba1.' : '.$r->sheba1_title }}</td>
                                                <td>{{ $r->sheba2.' : '.$r->sheba2_title }}</td>
                                                <td>{{ $r->sheba3.' : '.$r->sheba3_title }}</td>
                                                <td>{{ $r->sheba4.' : '.$r->sheba4_title }}</td>
                                                <td>{{ $r->sheba5.' : '.$r->sheba5_title }}</td>
                                                <td>{{ $r->sheba6.' : '.$r->sheba6_title }}</td>
                                                <td>{{ $r->sheba7.' : '.$r->sheba7_title }}</td>
                                                <td>{{ $r->sheba8.' : '.$r->sheba8_title }}</td>
                                                <td>
                                                    <x-admin.delete-btn onclick="deleteRole('{{$r->id}}')"  />
                                                    <x-admin.edit-btn  wire:click="editRole({{$r->id}})"/>

                                                </td>
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
    <x-admin.modal-page id="role" title="ویرایش" wire:click="updateRole">
        <div class="row">
            <div class="col-12 {{$role == \App\Enums\OperatorRole::MOSQUE_CULTURAL_OFFICER->value ? '' :"d-none"}}" >
                <x-admin.forms.select2
                    id="edit_unit"
                    :data="[]"
                    text="title"
                    :required="true"
                    label="مرکز محوری"
                    ajaxUrl="{{route('admin.feed.units')}}"
                    wire:model.live="unit"/>
                <x-admin.forms.input type="number" help="برحسب ساعت"  id="edit_auto_accept_period" label="تایید خودکار درخواست/گزارش ها بعد از گذشت" wire:model.defer="auto_accept_period" />
            </div>
            <div class="col-12 {{ $role == \App\Enums\OperatorRole::MOSQUE_HEAD_COACH->value && $item ? '' : 'd-none' }}">
                <x-admin.forms.select2
                    id="edit_main_unit"
                    :data="[]"
                    text="title"
                    :required="true"
                    label="مرکز محوری"
                    ajaxUrl="{{route('admin.feed.units')}}"
                    wire:model.live="main_unit"/>

                <x-admin.forms.select2
                    id="edit_unit2"
                    :data="[]"
                    text="title"
                    :required="true"
                    label="مرکز"
                    ajaxUrl="{{route('admin.feed.units')}}"
                    wire:model.live="unit"/>
                @if($itemModel->type === \App\Enums\UnitType::SCHOOL)
                    <x-admin.forms.dropdown :required="true" id="coach_type" :data="$data['coach_type']" label="نوع مربی" wire:model.defer="coach_type"/>
                @endif
            </div>

            <div class="col-12 row {{ $role == \App\Enums\OperatorRole::AREA_INTERFACE->value ? '' : 'd-none' }}">
                <x-admin.forms.input type="number" help="برحسب ساعت"  id="edit_notify_period" label="ارسال پیامک یاداوری پس از گذشت" wire:model.defer="notify_period" />
                <x-admin.forms.select2 id="edit_city" :required="true" :data="$city ?? []" text="title" label="شهر" width="3" ajaxUrl="{{route('admin.feed.cities')}}" wire:model.live="city"/>
                <x-admin.forms.select2 id="edit_region" :required="true" :data="$region ?? []" text="title" label="منطقه"  width="3" :ajaxUrl="$regionAjax" wire:model.live="region"/>
                <x-admin.forms.select2 id="edit_neighborhood" :data="$neighborhood ?? []" text="title" label="محله" width="3" :ajaxUrl="$neighborhoodAjax" wire:model.live="neighborhood"/>
                <x-admin.forms.select2 id="edit_area" :data="$area ?? []" text="title" label="ناحیه" width="3" :ajaxUrl="$areaAjax" wire:model.live="area"/>
                <div class="col-12" x-data="{map: null , marker: null}">
                    <label> لوکیشن مسجد<span class="text-danger">*</span></label>
                    <div wire:ignore id="edit_location"  style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 10px;"
                         x-init="
                                                  map = L.map('edit_location').setView([35.6892, 51.3890], 13);
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
                <x-admin.forms.input type="text" width="6" :required="true" id="edit_lat" label="عرض جغرافیایی Y" wire:model.defer="lat" />
                <x-admin.forms.input type="text" width="6" :required="true" id="edit_lng" label="طول جغرافیایی X" wire:model.defer="lng" />
            </div>

            <x-admin.forms.input type="text" width="6"  id="edit_sheba1" label="شماره شبا 1" wire:model.defer="sheba1" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba1_title" label="عنوان شماره شبا 1" wire:model.defer="sheba1_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba2" label="شماره شبا 2" wire:model.defer="sheba2" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba2_title" label="عنوان شماره شبا 2" wire:model.defer="sheba2_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba3" label="شماره شبا 3" wire:model.defer="sheba3" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba3_title" label="عنوان شماره شبا 3" wire:model.defer="sheba3_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba4" label="شماره شبا 4" wire:model.defer="sheba4" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba4_title" label="عنوان شماره شبا 4" wire:model.defer="sheba4_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba5" label="شماره شبا 5" wire:model.defer="sheba5" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba5_title" label="عنوان شماره شبا 5" wire:model.defer="sheba5_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba6" label="شماره شبا 6" wire:model.defer="sheba6" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba6_title" label="عنوان شماره شبا 6" wire:model.defer="sheba6_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba7" label="شماره شبا 7" wire:model.defer="sheba7" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba7_title" label="عنوان شماره شبا 7" wire:model.defer="sheba7_title" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba8" label="شماره شبا 8" wire:model.defer="sheba8" />
            <x-admin.forms.input type="text" width="6"  id="edit_sheba8_title" label="عنوان شماره شبا 8" wire:model.defer="sheba8_title" />
        </div>
    </x-admin.modal-page>
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
