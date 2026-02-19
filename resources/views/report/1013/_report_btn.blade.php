@if(isset($report_type))
    <a class="btn "  href="{{route("report.1013.download_excel")}}">دریافت فایل گزارش</a>

    {{--<div class="btn-group mb-2 mr-2 ">--}}
{{--    <button class="btn btn-outline-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">دریافت گزارش</button>--}}

{{--    <div class="dropdown-menu " >--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304020])}}">تایید کارشناس فروش</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304030])}}">تایید پیش فاکتور توسط مشتری</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304040])}}">تایید کارشناس وصول مطالبات</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304050])}}">تایید مدیر فروش</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304060])}}">تایید مدیر  مالی</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304070])}}">تایید مدیر عامل</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304075])}}">تایید هئیت مدیره</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,304080])}}">تایید پردازش</a>--}}
{{--        <a class="dropdown-item"  href="{{route("report.1013.excel",[$report_type,500000515])}}">پیش نویس واحد مالی</a>--}}

{{--    </div>--}}
{{--</div>--}}
@endif