<ul class="nav nav-pills" id="hcol-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{$tab_name=="utility_setting"?"show active ":""}}" id="hcol-default-tab" data-toggle="pill"
           href="#hcol-default" role="tab" aria-controls="hcol-default" aria-selected="true">تنظیمات بین سامانه ای
        </a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link h-blue show" id="company-tab" data-toggle="pill"--}}
{{--           href="#company" role="tab" aria-controls="company"--}}
{{--           aria-selected="false">اطلاعات شرکت</a>--}}
{{--    </li>--}}

    <li class="nav-item">
        <a class="nav-link h-blue show" id="panel2-tab" data-toggle="pill"
           href="#panel2" role="tab" aria-controls="panel2"
           aria-selected="false">تنظیمات کارت تولید / دستور پیمان</a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link h-blue show" id="production_public-tab" data-toggle="pill"--}}
{{--           href="#production_public" role="tab" aria-controls="production_public"--}}
{{--           aria-selected="false"> تولید </a>--}}
{{--    </li>--}}
    <li class="nav-item">
        <a class="nav-link h-blue show" id="panel3-tab" data-toggle="pill"
           href="#panel3" role="tab" aria-controls="panel3"
           aria-selected="false">تنظیمات انبار</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue show" id="panel4-tab" data-toggle="pill"
           href="#panel4" role="tab" aria-controls="panel4"
           aria-selected="false">تنظیمات اتوماسیون اداری</a>
    </li>

    <li class="nav-item">
        <a class="nav-link h-blue {{$tab_name=="product_creation"?"show active ":""}}" id="panel6-tab"
           data-toggle="pill"
           href="#panel6" role="tab" aria-controls="panel6"
           aria-selected="false">تنظیمات طراحی کالا</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue show" id="logistic-tab" data-toggle="pill"
           href="#logistic" role="tab" aria-controls="logistic"
           aria-selected="false">لوجستیک</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue {{$tab_name=="supplier_setting"?"show active ":""}}" id="supplier-tab" data-toggle="pill"
           href="#supplier" role="tab" aria-controls="supplier"
           aria-selected="false">تامین کنندگان</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue show" id="quality_control-tab" data-toggle="pill"
           href="#quality_control" role="tab" aria-controls="quality_control"
           aria-selected="false">کنترل کیفیت</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue show" id="accounting-tab" data-toggle="pill"
           href="#accounting" role="tab" aria-controls="accounting"
           aria-selected="false">مالی</a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link h-blue show" id="hcol-primary-tab2" data-toggle="pill"--}}
{{--           href="#hcol-primary" role="tab" aria-controls="hcol-primary"--}}
{{--           aria-selected="false">لوگو</a>--}}
{{--    </li>--}}
    <li class="nav-item">
        <a class="nav-link h-blue{{$tab_name=="customer_setting"?"show active ":""}}"  id="customer_tab" data-toggle="pill"
           href="#customer" role="tab" aria-controls="hcol-primary"
           aria-selected="false">مشتری</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue{{$tab_name=="contractor_setting"?"show active ":""}}"  id="contractor_tab" data-toggle="pill"
           href="#contractor" role="tab" aria-controls="hcol-primary"
           aria-selected="false">پیمانکاران</a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue{{$tab_name=="store"?"show active ":""}}"  id="store_tab" data-toggle="pill"
           href="#store" role="tab" aria-controls="hcol-primary"
           aria-selected="false">تنظیمات فروشگاه</a>
    </li>
</ul>
<div class="tab-content pt-2" id="hcol-tabContent">
    <div class="tab-pane {{$tab_name=="utility_setting"?"show active ":""}}" id="hcol-default" role="tabpanel"
         aria-labelledby="hcol-default-tab">

        @if($tab_name=="utility_setting")
            @include("utility.setting._basic_info")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
{{--    <div class="tab-pane show" id="hcol-primary" role="tabpanel" aria-labelledby="hcol-primary-tab">--}}

{{--        @if($tab_name=="utility_setting")--}}
{{--            @include("utility.setting.upload._app_icon")--}}
{{--            @include("utility.setting.upload._logo_login")--}}
{{--            @include("utility.setting.upload._dashboard_logo")--}}
{{--        @else--}}
{{--            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">--}}
{{--                <i class="fa fa-undo"></i>--}}
{{--                بارگذاری--}}
{{--            </a>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    <div class="tab-pane show" id="company" role="tabpanel" aria-labelledby="company-tab">--}}

{{--        @if($tab_name=="utility_setting")--}}
{{--            @include("utility.setting._company_info")--}}
{{--        @else--}}
{{--            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">--}}
{{--                <i class="fa fa-undo"></i>--}}
{{--                بارگذاری--}}
{{--            </a>--}}
{{--        @endif--}}
{{--    </div>--}}
    <div class="tab-pane show" id="panel2" role="tabpanel" aria-labelledby="panel2-tab">

        @if($tab_name=="utility_setting")
            @include("utility.setting._production")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
{{--    <div class="tab-pane show" id="production_public" role="tabpanel" aria-labelledby="production_public-tab">--}}

{{--        @if($tab_name=="utility_setting")--}}
{{--            @include("utility.setting._production_public")--}}
{{--        @else--}}
{{--            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">--}}
{{--                <i class="fa fa-undo"></i>--}}
{{--                بارگذاری--}}
{{--            </a>--}}
{{--        @endif--}}
{{--    </div>--}}
    <div class="tab-pane " id="panel3" role="tabpanel" aria-labelledby="panel3-tab">

        @if($tab_name=="utility_setting")
            @include("utility.setting._warehouse")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>

    <div class="tab-pane show" id="panel4" role="tabpanel" aria-labelledby="panel4-tab">

        @if($tab_name=="utility_setting")
            @include("utility.setting._office_automation")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>

    <div class="tab-pane {{$tab_name=="product_creation"?"show active ":""}}" id="panel6" role="tabpanel"
         aria-labelledby="panel6-tab">
        @if($tab_name=="product_creation")
            @include("line_product_station.product.product_creation.priority_setting._info")
        @else
            <a class="btn btn-primary"
               href="{{route("line_product_station.product.product_creation.priority_setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif

    </div>
    <div class="tab-pane show" id="logistic" role="tabpanel" aria-labelledby="logistic-tab">
        @if($tab_name=="utility_setting")
            @include("utility.setting._logistic")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane {{$tab_name=="supplier_setting"?"show active ":""}}" id="supplier" role="tabpanel" aria-labelledby="supplier-tab">
        @if($tab_name=="supplier_setting")
            @include("supplier.definition.default._info")

        @else
            <a class="btn btn-primary" href="{{route("supplier.definition.default.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane show" id="quality_control" role="tabpanel" aria-labelledby="quality_control-tab">
        @if($tab_name=="utility_setting")
            @include("utility.setting._quality_control")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane show" id="accounting" role="tabpanel" aria-labelledby="accounting-tab">
        @if($tab_name=="utility_setting")
            @include("utility.setting._accounting")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane {{$tab_name=="customer_setting"?"show active ":""}}" id="customer"  role="tabpanel" aria-labelledby="customer-tab">
        @if($tab_name=="customer_setting")
            @include("customer.definition.default._info")

        @else
            <a class="btn btn-primary" href="{{route("customer_group.definition.default.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane {{$tab_name=="contractor_setting"?"show active ":""}}" id="contractor"  role="tabpanel" aria-labelledby="contractor-tab">
        @if($tab_name=="contractor_setting")
            @include("contractor.definition.default._info")

        @else
            <a class="btn btn-primary" href="{{route("contractor.definition.default.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane {{$tab_name=="store"?"show active ":""}}" id="store"  role="tabpanel" aria-labelledby="store-tab">
        @if($tab_name=="store")
            @include("accounting.store.setting._store")

        @else
            <a class="btn btn-primary" href="{{route("accounting.store.setting.index")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
</div>
