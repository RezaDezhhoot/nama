@use('App\Modules\Base\Enums\PageAction')
@use('App\Models\Request')
@use('App\Models\Report')
@use('App\Enums\UnitType')

<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="" class="brand-logo">
            <img alt="Logo" style="max-width: 4rem;" src="https://armaniran.app/site/sa/arman_logo.png" />
        </a>
        <!--end::Logo-->
        <!--begin::Toggle-->
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
							<span class="svg-icon svg-icon svg-icon-xl">
								<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
                                <!--end::Svg Icon-->
							</span>
        </button>
        <!--end::Toolbar-->
    </div>
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
            <ul class="menu-nav">
                <li class="menu-section">
                    <h4 class="menu-text">داشبورد</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li>
                <li class="menu-item {{ url()->current() == route('admin.dashboard.index') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                    <a href="{{ route('admin.dashboard.index') }}" class="menu-link">
                                    <span class="svg-icon menu-icon">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<polygon points="0 0 24 0 24 24 0 24" />
													<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
													<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
												</g>
											</svg>
                                        <!--end::Svg Icon-->
										</span>
                        <span class="menu-text">داشبورد</span>
                    </a>
                </li>

                @if(isAdmin() || isOperator())
                    <ul class="menu-nav">
                        @if(isAdmin() || isOperator())
                            <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">درخواست ها / گزارش ها</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::MOSQUE)" :report_counter="Report::counter(UnitType::MOSQUE)" icon="fas fa-mosque" :name="UnitType::MOSQUE->value" :title="UnitType::MOSQUE->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::SCHOOL)" :report_counter="Report::counter(UnitType::SCHOOL)" icon="fas fa-school" :name="UnitType::SCHOOL->value" :title="UnitType::SCHOOL->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::UNIVERSITY)" :report_counter="Report::counter(UnitType::UNIVERSITY)" icon="fas fa-university" :name="UnitType::UNIVERSITY->value" :title="UnitType::UNIVERSITY->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::CENTER)" :report_counter="Report::counter(UnitType::CENTER)" icon="fas fa-ticket-alt" :name="UnitType::CENTER->value" :title="UnitType::CENTER->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::GARDEN)" :report_counter="Report::counter(UnitType::GARDEN)" icon="fas fa-tree" :name="UnitType::GARDEN->value" :title="UnitType::GARDEN->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::HALL)" :report_counter="Report::counter(UnitType::HALL)" icon="fas fa-city" :name="UnitType::HALL->value" :title="UnitType::HALL->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::STADIUM)" :report_counter="Report::counter(UnitType::STADIUM)" icon="fas fa-drum-steelpan" :name="UnitType::STADIUM->value" :title="UnitType::STADIUM->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::DARUL_QURAN)" :report_counter="Report::counter(UnitType::DARUL_QURAN)" icon="fas fa-quran" :name="UnitType::DARUL_QURAN->value" :title="UnitType::DARUL_QURAN->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::CULTURAL_INSTITUTE)" :report_counter="Report::counter(UnitType::CULTURAL_INSTITUTE)" icon="fas fa-dungeon" :name="UnitType::CULTURAL_INSTITUTE->value" :title="UnitType::CULTURAL_INSTITUTE->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::SEMINARY)" :report_counter="Report::counter(UnitType::SEMINARY)" icon="fas fa-kaaba" :name="UnitType::SEMINARY->value" :title="UnitType::SEMINARY->label()" />
                                        <x-admin.menu-item-header :request_counter="Request::counter(UnitType::QURANIC_CENTER)" :report_counter="Report::counter(UnitType::QURANIC_CENTER)" icon="fas fa-quran" :name="UnitType::QURANIC_CENTER->value" :title="UnitType::QURANIC_CENTER->label()" />
                                    </ul>
                                </div>
                            </li>
                        @endif
                    </ul>

                    @canany(['show_requests_written','edit_requests_written','delete_requests_written','export_requests_written'])
                            <li class="menu-section">
                                <h4 class="menu-text">سایر</h4>
                                <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                            </li>
                    <x-admin.menu-item
                        href="{{route('admin.written-requests.index')}}"
                        icon="fas fa-ticket-alt"
                        :active="request()->routeIs(['admin.written-requests.index','admin.written-requests.store'])"
                        label="درخواست های مکتوب({{ $writtenRequests }})" />
                    @endcanany
                @endif


                @if(isAdmin())
                    <li class="menu-section">
                        <h4 class="menu-text">گزارش های سیستمی</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md "></i>
                    </li>
                    @if(auth()->user()->hasAnyRole(['super_admin','administrator']))
                        <x-admin.menu-item href="{{route('telescope')}}" icon="flaticon-book" :active="false" label="Telescope" />
                        <x-admin.menu-item href="{{route('pulse')}}" icon="flaticon-book" :active="false" label="Pulse" />
                        <x-admin.menu-item href="{{route('log-viewer.index')}}" icon="flaticon-book" :active="false" label="Logs" />
                        <x-admin.menu-item href="{{route('admin.client-logs')}}" icon="flaticon-book" :active="request()->routeIs(['admin.client-logs'])" label="Client logs" />
                    @endif
                    @can('show_log_activities')
                        <x-admin.menu-item href="{{ route('admin.log-activities.index') }}" :active="request()->routeIs(['admin.log-activities.index'])" icon="fas fa-history" label="فعالیت کاربران" />
                        <x-admin.menu-item href="{{ route('admin.log-activities.roles') }}" :active="request()->routeIs(['admin.log-activities.roles'])" icon="fas fa-history" label="فعالیت سایر نقش ها" />
                    @endcan
                    @canany(['show_accounting','export_accounting'])
                        <x-admin.menu-item href="{{ route('admin.accounting.records') }}" :active="request()->routeIs(['admin.accounting.records'])" icon="flaticon2-medical-records" label="حسابداری" />
                    @endcanany
                    <li class="menu-section">
                        <h4 class="menu-text">تنظیمات</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md "></i>
                    </li>
                    @canany(['show_rings','delete_rings','export_rings'])
                        <x-admin.menu-item
                            href="{{route('admin.rings.index')}}"
                            icon="fas fa-ring"
                            :active="request()->routeIs(['admin.rings.index','admin.rings.store'])"
                            label="حلقه ها" />
                    @endcanany
                    @canany(['show_forms','delete_forms','edit_forms'])
                        <x-admin.menu-item
                            href="{{route('admin.forms.index')}}"
                            icon="flaticon2-file"
                            :active="request()->routeIs(['admin.forms.index','admin.forms.store'])"
                            label="فرم ها" />
                    @endcanany
                    @canany(['show_form_reports','delete_form_reports','edit_form_reports'])
                        <x-admin.menu-item
                            href="{{route('admin.form-reports.index')}}"
                            icon="fas fa-ticket-alt"
                            :active="request()->routeIs(['admin.form-reports.index','admin.form-reports.store'])"
                            label="گزارش گیر({{ number_format($reports) }})" />
                    @endcanany
                    @canany(['show_locations','delete_locations','edit_locations'])
                        <x-admin.menu-item
                            href="{{route('admin.cities.index')}}"
                            icon="fas fa-city"
                            :active="request()->routeIs(['admin.cities.index','admin.cities.store'])"
                            label="شهر ها و مناطق" />
                    @endcanany
                    @canany(['show_units','delete_units','edit_units','export_units'])
                        <x-admin.menu-item
                            href="{{route('admin.units.index')}}"
                            icon="fas fa-mosque"
                            :active="request()->routeIs(['admin.units.index','admin.units.store'])"
                            label="مراکز حقوقی" />
                    @endcanany
                    @canany(['show_request_plans','delete_request_plans','edit_request_plans'])
                        <x-admin.menu-item
                            href="{{route('admin.plans.index')}}"
                            icon="fas fa-gem"
                            :active="request()->routeIs(['admin.plans.index','admin.plans.store'])"
                            label="اکشن پلن ها" />
                    @endcanany
                    @canany(['show_banners','delete_banners','edit_banners'])
                        <x-admin.menu-item
                            href="{{route('admin.banners.index')}}"
                            icon="far fa-image"
                            :active="request()->routeIs(['admin.banners.index','admin.banners.store'])"
                            label="بنر ها" />
                    @endcanany
                    @canany(['show_dashboard_items','delete_dashboard_items','edit_dashboard_items'])
                        <x-admin.menu-item
                            href="{{route('admin.dashboard-items.index')}}"
                            icon="fas fa-home"
                            :active="request()->routeIs(['admin.dashboard-items.index','admin.dashboard-items.store'])"
                            label="ایتم های داشبورد" />
                    @endcanany
                    @canany(['show_roles','delete_roles','edit_roles','export_roles'])
                        <x-admin.menu-item
                            href="{{route('admin.users.roles')}}"
                            icon="fas fa-key"
                            :active="request()->routeIs(['admin.users.roles','admin.users.roles.store','admin.users.permissions.store'])"
                            label="مدیریت نقش ها" />
                    @endcanany
                @endif
            </ul>
        </div>
    </div>
</div>
