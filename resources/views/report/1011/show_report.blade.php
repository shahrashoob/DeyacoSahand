@extends('layouts.admin._master')
@section("page_header_title","  داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">

        <div class="col-md-12 col-xl-4">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع کالای تولید شده</h5>
                    @foreach($sum_amount as $unit_id=>$amount)
                    <h4 class="f-w-300 mb-3">{{$amount}} {{$unit_list[$unit_id]}}
                    </h4>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12">

            <div class="card">
                <div class="card-header">

                    <h5> میزان کالای تولید شده </h5>

                    <div class="btn-group mb-2 mr-2">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            @if($machine)
                                {{$machine->caption}}
                            @else
                                انتخاب ماشین
                            @endif
                        </button>
                        <div class="dropdown-menu" x-placement="bottom-start"
                             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                            <a class="dropdown-item"
                               href="#">انتخاب ماشین ها</a>
                            <a class="dropdown-item"
                               href="{{route("report.1011.submit")}}">همه ماشین ها</a>

                            @foreach($bar_stack_chart_data["data"] as $key=>$item)
                                <a class="dropdown-item"
                                   href="{{route("report.1011.submit",$key)}}">{{$item["caption"]}}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-header-right">
                        <a href="{{route("report.1011.export_ReportForCrossSection")}}"><i class="fa fa-download"></i>
                            فایل اکسل گزارش </a>
                    </div>
                </div>
                <div class="card-block">
                    <div id="lineProductChartClassification" style="width: 100%; height: 300px; ">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">

            <div class="card">
                <div class="card-header">

                    <h5> میزان کالای تولید شده به تفکیک هر ماشین</h5>


                    <div class="card-header-right">
                    </div>
                </div>
                <div class="card-block"
                     style="width:{{min(count($bar_stack_chart_data["data"])*100+150,1000)}}px; text-align: center; margin: auto">

                    <div id="barMachineProductChartClassification" style="width: 100%; height: 500px; ">
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12 center">
            <a href="{{route("report.cross_sectional_management.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>
    </div>

@endsection
@section("styles")


@endsection


@section("scripts")
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>

    @include("component.echart.scripts",["data"=>$line_list_classification_value["list_line_report"]["classification_item"]["data"],"series"=>$line_list_classification_value["list_line_report"],"type"=>'multi_line',"id"=>"lineProductChartClassification"])
    @include("component.echart.scripts",["data"=>$bar_stack_chart_data["data"],"series"=>$bar_stack_chart_data["series"],"type"=>'bar_stack2',"id"=>"barMachineProductChartClassification"])


@endsection

@section("menu_styler")
    <div id="styleSelector" class="menu-styler ">
        <div class="style-toggler"><a href="#!" id="my_open_menu_styler" onclick="open_menu_styler()"></a></div>
        <div class="style-block "><h6 class="mb-2">تنظیمات گزارش</h6>


        </div>
    </div>

@endsection
