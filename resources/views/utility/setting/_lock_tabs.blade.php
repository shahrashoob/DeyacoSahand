<ul class="nav nav-pills" id="hcol-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{$tab_name=="lock_setting"?"show active ":""}}" id="hcol-default-tab" data-toggle="pill"
           href="#hcol-default" role="tab" aria-controls="hcol-default" aria-selected="true">تنظیمات اولیه
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link h-blue show" id="company-tab" data-toggle="pill"
           href="#company" role="tab" aria-controls="company"
           aria-selected="false">اطلاعات شرکت</a>
    </li>


    <li class="nav-item">
        <a class="nav-link h-blue show" id="production_public-tab" data-toggle="pill"
           href="#production_public" role="tab" aria-controls="production_public"
           aria-selected="false"> تولید </a>
    </li>

    <li class="nav-item">
        <a class="nav-link h-blue show" id="hcol-primary-tab2" data-toggle="pill"
           href="#hcol-primary" role="tab" aria-controls="hcol-primary"
           aria-selected="false">لوگو</a>
    </li>


</ul>
<div class="tab-content pt-2" id="hcol-tabContent">
    <div class="tab-pane {{$tab_name=="lock_setting"?"show active ":""}}" id="hcol-default" role="tabpanel"
         aria-labelledby="hcol-default-tab">

        @if($tab_name=="lock_setting")
            @include("utility.setting._lock_setting_info")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.software_lock")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane show" id="hcol-primary" role="tabpanel" aria-labelledby="hcol-primary-tab">

        @if($tab_name=="lock_setting")
            @include("utility.setting.upload._app_icon")
            @include("utility.setting.upload._logo_login")
            @include("utility.setting.upload._dashboard_logo")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.software_lock")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>
    <div class="tab-pane show" id="company" role="tabpanel" aria-labelledby="company-tab">

        @if($tab_name=="lock_setting")
            @include("utility.setting._company_info")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.software_lock")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>

    <div class="tab-pane show" id="production_public" role="tabpanel" aria-labelledby="production_public-tab">

        @if($tab_name=="lock_setting")
            @include("utility.setting._production_public")
        @else
            <a class="btn btn-primary" href="{{route("utility.setting.software_lock")}}">
                <i class="fa fa-undo"></i>
                بارگذاری
            </a>
        @endif
    </div>

</div>
