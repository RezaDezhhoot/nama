@props(['name' , 'title','icon' => null ,'request_counter' => 0 ,'report_counter' => 0])
@canany(['show_requests_'.$name,'edit_requests_'.$name,'delete_requests_'.$name,'export_requests_'.$name])
    <li class="menu-item menu-item-submenu {{ request()->routeIs(['admin.requests.index','admin.requests.store','admin.reports.index','admin.reports.store']) && request()->route()->parameter('type') == $name ?  'menu-item-active' : '' }}" data-menu-toggle="hover" aria-haspopup="true">
        <a href="javascript:;" class="menu-link menu-toggle">
            <span class="svg-icon menu-icon"><i class="{{ $icon }}"></i></span>
            <span class="menu-text">{{ $title }} ({{ number_format($request_counter + $report_counter) }})</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="menu-submenu menu-submenu-classic menu-submenu-right">
            <ul class="menu-subnav">
                <li class="menu-item" aria-haspopup="true">
                    <a href="{{route('admin.requests.index',[$name])}}" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">درخواست ها({{ number_format($request_counter) }})</span>
                    </a>
                </li>
                <li class="menu-item" aria-haspopup="true">
                    <a href="{{route('admin.reports.index',[$name])}}" class="menu-link">
                        <i class="menu-bullet menu-bullet-dot">
                            <span></span>
                        </i>
                        <span class="menu-text">گزارش ها({{ number_format($report_counter) }})</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endcanany
