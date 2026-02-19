@extends('layouts.admin._master')
@section("page_header_title","  داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">


        @foreach($report_list as $report_id=>$on_value)
{{--            @if (  $post_user->checkButtonPermission( "report.".$report_id. ".index" ) )--}}

                @include("report.".$report_id."._index",["chart_id"=>"chart_".$report_id,"chart_data"=>$chart_data[$report_id],"chart_caption"=>$chart_caption[$report_id]])
{{--            @endif--}}
        @endforeach

        <div class="col-md-12 center">
            <a href="{{route("report.cross_sectional_management.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>

@endsection


{{--@section("scripts")--}}



{{--    --}}{{--    @include("component.echart.scripts",["data"=>$degree_pie_chart,"type"=>'pie2',"id"=>"degreePieChart"])--}}

{{--    --}}{{--    @foreach($degree_pie_charts as $item)--}}
{{--    --}}{{--        @include("component.echart.scripts",["data"=>$item["data"],"type"=>'pie',"id"=>"degree_chart".$item["product_id"]])--}}

{{--    --}}{{--    @endforeach--}}
{{--    <div id="styleSelector" class="menu-styler ">--}}
{{--        <div class="style-toggler"><a href="#!" id="my_open_menu_styler" onclick="open_menu_styler()"></a></div>--}}
{{--        <div class="style-block "><h6 class="mb-2">تنظیمات گزارش</h6>--}}

{{--            <div class="row">--}}
{{--                <form id="form1" autocomplete="off"--}}
{{--                      action="{{route("report.cross_sectional_management.dashboard.submit")}}" method="post"--}}
{{--                      novalidate="novalidate">--}}
{{--                    @csrf--}}

{{--                    <div class="row">--}}
{{--                        @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ","formatDate"=>"hh:mm:ss YYYY/MM/DD","class_col"=>"col-md-12"])--}}

{{--                        @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ","formatDate"=>"hh:mm:ss YYYY/MM/DD","class_col"=>"col-md-12"])--}}

{{--                        --}}{{--                        <div class="col-md-6">--}}
{{--                        --}}{{--                            @include("component.input._select",[--}}
{{--                        --}}{{--                                "id"=>"goods_kind_id",--}}
{{--                        --}}{{--                                "label"=>"رسته کالایی  ",--}}
{{--                        --}}{{--                                "option"=>$goods_kind_option["items"],--}}
{{--                        --}}{{--                                "val"=>$goods_kind_option["value"],--}}
{{--                        --}}{{--                                "text"=>$goods_kind_option["text"],--}}
{{--                        --}}{{--                                "class_col"=>""--}}
{{--                        --}}{{--                                ])--}}
{{--                        --}}{{--                        </div>--}}
{{--                        --}}{{--                        <div class="w-100"></div>--}}
{{--                        --}}{{--                        <div class="col-md-6">--}}
{{--                        --}}{{--                            @include("component.input._select",[--}}
{{--                        --}}{{--                                "id"=>"classification_id",--}}
{{--                        --}}{{--                                "label"=>"طبقه بندی ",--}}
{{--                        --}}{{--                                "option"=>[],--}}
{{--                        --}}{{--                                "val"=>[],--}}
{{--                        --}}{{--                                "text"=>[],--}}
{{--                        --}}{{--                                "class_col"=>""--}}
{{--                        --}}{{--                                ])--}}
{{--                        --}}{{--                        </div>--}}


{{--                        <div class="col-md-12">--}}
{{--                            <button type="submit" class="btn btn-primary"> مشاهده گزارش</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--    <script>--}}
{{--        function open_menu_styler() {--}}
{{--            if ($("#my_open_menu_styler").parent().parent().hasClass("open")) {--}}
{{--                $("#my_open_menu_styler").parent().parent().removeClass("open")--}}
{{--            } else {--}}
{{--                $("#my_open_menu_styler").parent().parent().addClass("open")--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
{{--@endsection--}}

{{--@section("menu_styler")--}}
{{--    <div id="styleSelector" class="menu-styler ">--}}
{{--        <div class="style-toggler"><a href="#!" id="my_open_menu_styler" onclick="open_menu_styler()"></a></div>--}}
{{--        <div class="style-block "><h6 class="mb-2">تنظیمات گزارش</h6>--}}


{{--        </div>--}}
{{--    </div>--}}


{{--@endsection--}}
