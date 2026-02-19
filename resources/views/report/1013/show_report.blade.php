@extends('layouts.admin._master')
@section("page_header_title","  داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">


        <div class="col-xl-12 col-md-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش سفارشات {{$start_jdate}} {{$end_jdate}}</h5>

                    {{--                    <div class="card-header-right">--}}
                    {{--                        <a href="{{route("report.1010.export_ReportForCrossSection")}}"><i class="fa fa-download"></i> فایل اکسل گزارش </a>--}}
                    {{--                    </div>--}}

                </div>
                {{--                <div class="card-block" style="width:{{min(count($machine_bar_chart)*100+150,1000)}}px; text-align: center; margin: auto">--}}
                {{--                    <div id="BarChart1010" style="width: 100%; height: 300px; overflow: auto ">--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
        </div>

        @include("report.1013._index",["chart_id"=>"chart_customer_price","report_type"=>"price","chart_data"=>$bar_list["customer_price"],"chart_other_percent"=>$chart_percent["customer"]["price"],"chart_caption"=>" گزارش سفارشات به تفکیک مشتری (ریالی)","type_percent"=>1])
        @include("report.1013._index",["chart_id"=>"chart_customer_amount","report_type"=>"amount","chart_data"=>$bar_list["customer_amount"],"chart_other_percent"=>$chart_percent["customer"]["amount"],"chart_caption"=>"گزارش سفارشات به تفکیک مشتری (مقداری)","type_percent"=>2])

        @include("report.1013._index",["chart_id"=>"chart_product_price","report_type"=>"price","chart_data"=>$bar_list["product_price"],"chart_other_percent"=>$chart_percent["product"]["price"],"chart_caption"=>" گزارش سفارشات به تفکیک کالا (ریالی)","type_percent"=>3])
        @include("report.1013._index",["chart_id"=>"chart_product_amount","report_type"=>"amount","chart_data"=>$bar_list["product_amount"],"chart_other_percent"=>$chart_percent["product"]["amount"],"chart_caption"=>"گزارش سفارشات به تفکیک کالا (مقداری)","type_percent"=>4])

        @include("report.1013._index",["chart_id"=>"chart_classification_price","report_type"=>"price","chart_data"=>$bar_list["classification_price"],"chart_other_percent"=>$chart_percent["classification"]["price"],"chart_caption"=>" گزارش سفارشات به تفکیک طبقه بندی (ریالی)","type_percent"=>5])
        @include("report.1013._index",["chart_id"=>"chart_classification_amount","report_type"=>"amount","chart_data"=>$bar_list["classification_amount"],"chart_other_percent"=>$chart_percent["classification"]["amount"],"chart_caption"=>"گزارش سفارشات به تفکیک طبقه بندی (مقداری)","type_percent"=>6])

        <div class="col-md-12 center">
            <a href="{{route("report.cross_sectional_management.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>

@endsection


