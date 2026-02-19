@extends('layouts.admin._master')
@section("page_header_title","  داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">



        <div class="col-xl-12 col-md-12">

            <div class="card">
                <div class="card-header">
                    <a href="{{route("report.1010.submit")}}" >
                        <h5>بهره وری کل ماشین آلات</h5>
                    </a>

                    <div class="card-header-right">
                        <a href="{{route("report.1010.export_ReportForCrossSection")}}"><i class="fa fa-download"></i> فایل اکسل گزارش </a>
                    </div>

                </div>
                <div class="card-block" style="width:{{min(count($machine_bar_chart)*100+150,1000)}}px; text-align: center; margin: auto">
                    <div id="BarChart1010" style="width: 100%; height: 300px; overflow: auto ">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                 <h5>بهره وری کل منابع انسانی</h5>

                </div>
                <div class="card-block" style="width:{{min(count($user_bar_chart)*100+150,1000)}}px; text-align: center; margin: auto">

                    <div id="BarChartUser1010" style="width: 100%; height: 300px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 center">
            <a href="{{route("report.cross_sectional_management.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
        </div>
    </div>

@endsection
@section("styles")


@endsection


@section("scripts")
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>

    @include("component.echart.scripts",["data"=>$machine_bar_chart,"type"=>'bar',"id"=>"BarChart1010"])
    @include("component.echart.scripts",["data"=>$user_bar_chart,"type"=>'bar',"id"=>"BarChartUser1010","color"=>"#92C6F3"])


@endsection

@section("menu_styler")
    <div id="styleSelector" class="menu-styler ">
        <div class="style-toggler"><a href="#!" id="my_open_menu_styler" onclick="open_menu_styler()" ></a></div>
        <div class="style-block "><h6 class="mb-2">تنظیمات گزارش</h6>


        </div>
    </div>

@endsection
