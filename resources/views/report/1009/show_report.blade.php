@extends('layouts.admin._master')
@section("page_header_title"," 1009 - داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">

        <div class="col-md-12 col-xl-3">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع مقدار اصلی (اولیه)</h5>
                    @foreach($sum_all_list as $unit_id => $amount)
                        <h4 class="f-w-300 mb-3">

                            <div class=" btn-group ">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">{{$amount["sum_amount"]}} {{$unit_list[$unit_id]}}
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start"
                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                    @php $sum=0;@endphp
                                    @foreach($list_classification as $item)
                                        @if($item->unit_id == $unit_id)
                                            <a href="#"
                                               class="dropdown-item">{{$goods_kind_classification_options[$item->id]}}
                                                : {{$item->amount}} {{$unit_list[$unit_id]}}</a>
                                            @php $sum+=$item->amount; @endphp
                                        @endif
                                    @endforeach
                                    @if(round($amount["sum_amount"] -$sum,2) >0 )
                                        <a href="#"
                                           class="dropdown-item">طبقه بندی نشده
                                            : {{round($amount["sum_amount"] -$sum,2)}} {{$unit_list[$unit_id]}}</a>

                                    @endif
                                </div>
                            </div>
                            @endforeach

                        </h4>

                </div>
            </div>
        </div>


        <div class="col-md-12 col-xl-3">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع مقدار فرعی (اولیه)</h5>
                    @foreach($sum_sub_all_list as $unit_id => $amount)
                        @if($unit_id> 0)
                            <h4 class="f-w-300 mb-3">

                                <div class=" btn-group ">
                                    <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">{{$amount["sum_sub_amount"]}} {{$unit_list[$unit_id]}}
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start"
                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                        @php $sum=0;@endphp
                                        @foreach($list_classification as $item)
                                            @if($item->sub_unit_id == $unit_id)
                                                <a href="#"
                                                   class="dropdown-item">{{$goods_kind_classification_options[$item->id]}}
                                                    : {{$item->sub_amount}} {{$unit_list[$unit_id]}}</a>
                                                @php $sum+=$item->sub_amount; @endphp
                                            @endif
                                        @endforeach

                                        @if(round($amount["sum_sub_amount"] -$sum) >0 )
                                            <a href="#"
                                               class="dropdown-item">طبقه بندی نشده
                                                : {{round($amount["sum_sub_amount"] -$sum,2)}} {{$unit_list[$unit_id]}}</a>

                                        @endif

                                    </div>
                                </div>

                            </h4>
                        @endif
                    @endforeach


                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-3">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع مقدار  اصلی (نهایی)</h5>
                    @foreach($sum_all_list as $unit_id => $amount)
                        <h4 class="f-w-300 mb-3">

                            <div class=" btn-group ">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">{{$amount["sum_final_amount"]}} {{$unit_list[$unit_id]}}
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start"
                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                    @php $sum=0;@endphp
                                    @foreach($list_classification as $item)
                                        @if($item->unit_id == $unit_id)
                                            <a href="#"
                                               class="dropdown-item">{{$goods_kind_classification_options[$item->id]}}
                                                : {{$item->final_amount}} {{$unit_list[$unit_id]}}</a>
                                        @endif
                                        @php $sum+=$item->final_amount; @endphp
                                    @endforeach

                                        @if(round($amount["sum_final_amount"] -$sum )>0 )
                                            <a href="#"
                                               class="dropdown-item">طبقه بندی نشده
                                                : {{round($amount["sum_final_amount"] -$sum,2)}} {{$unit_list[$unit_id]}}</a>

                                        @endif
                                </div>
                            </div>
                            @endforeach
                </div>
            </div>
        </div>


        <div class="col-md-12 col-xl-3">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">تعداد بسته بندی</h5>
                    <h4 class="f-w-300 mb-3"> {{$count_packing_form}} عدد </h4>


                </div>
            </div>
        </div>


        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>میزان کالای استخراج شده به طبقه بندی {{$classification->caption}}</h5>

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
                    <h5>میزان کالای استخراج شده به تفکیک کالا


                    </h5>

                    <div class="card-header-right">
                        @include("report.public._other_percent",["percent_chart"=>$percent_pie_chart,"route"=>"report.1009.submit"])

                    </div>
                </div>
                <div class="card-block">


                    <div id="productPieChart" style="width: 100%; height: 600px; ">
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار کنترل کیفیت به تفکیک درجه هر ماشین</h5>
                    <div class="card-header-right">

                    </div>
                </div>
                <div class="card-block">

                    <div class="row">

                        <div class="col-md-12">
                            <ul class="nav nav-tabs" role="tablist" id="myTab1">

                                <li class="nav-item">
                                    <a class="nav-link text-uppercase active show" id="tab1-tab" data-toggle="tab"
                                       href="#tab1" role="tab"
                                       aria-controls="tab1" aria-selected="true">نمودار دایره ای کالا - درجه</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase  show" id="tab2-tab" data-toggle="tab"
                                       href="#tab2" role="tab"
                                       aria-controls="tab2" aria-selected="false">جدول کالا - درجه </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase  show" id="tab3-tab" data-toggle="tab"
                                       href="#tab3" role="tab"
                                       aria-controls="tab3" aria-selected="false">جدول ماشین - درجه </a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTab1Content">

                                <div class="tab-pane fade active show" id="tab1" role="tabpanel"
                                     aria-labelledby="tab1-tab">
                                    <div class="row">
                                        {{--                                        @foreach($degree_pie_charts as $item)--}}
                                        {{--                                            <div class="col-md-6">--}}
                                        {{--                                                <div id="degree_chart{{$item["product_id"]}}"--}}
                                        {{--                                                     style="width: 100%; height:400px; ">--}}
                                        {{--                                                </div>--}}
                                        {{--                                                <div class="center">--}}
                                        {{--                                                    {{$item["title"]}}--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        @endforeach--}}
                                    </div>
                                </div>
                                <div class="tab-pane fade  " id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                    <div class="table-responsive">
                                        {{--                                        <table class="table table-styling center">--}}
                                        {{--                                            <thead>--}}
                                        {{--                                            <tr>--}}
                                        {{--                                                <th>#</th>--}}
                                        {{--                                                <th>کالا / درجه</th>--}}
                                        {{--                                                <th>کل متراژ</th>--}}
                                        {{--                                                @foreach($degree_header_list as $header)--}}
                                        {{--                                                    <th>{{$header->fullCaption()}}</th>--}}
                                        {{--                                                @endforeach--}}
                                        {{--                                            </tr>--}}
                                        {{--                                            </thead>--}}
                                        {{--                                            <tbody>--}}
                                        {{--                                            @php $row=1;@endphp--}}
                                        {{--                                            @foreach($degree_pie_charts as $item)--}}
                                        {{--                                                <tr>--}}
                                        {{--                                                    <td>{{$row++}}</td>--}}
                                        {{--                                                    <td>--}}
                                        {{--                                                        {{$item["title"]}}--}}
                                        {{--                                                    </td>--}}
                                        {{--                                                    <td>--}}
                                        {{--                                                        {{$item["sum"]}}--}}
                                        {{--                                                    </td>--}}
                                        {{--                                                    @foreach($degree_header_list as $header)--}}
                                        {{--                                                        <td>--}}
                                        {{--                                                            @php $value=isset($item["data"][$header->id])?$item["data"][$header->id]["value"]:"0";@endphp--}}
                                        {{--                                                            {{$value!=0?$value." متر ":""}}--}}
                                        {{--                                                            {{$value!=0?"(".(round($value/$item["sum"]*100))."%)":""}}--}}
                                        {{--                                                        </td>--}}
                                        {{--                                                    @endforeach--}}


                                        {{--                                                </tr>--}}
                                        {{--                                            @endforeach--}}
                                        {{--                                            </tbody>--}}

                                        {{--                                        </table>--}}
                                    </div>
                                </div>
                                <div class="tab-pane fade  " id="tab3" role="tabpanel" aria-labelledby="tab3-tab">

                                </div>

                            </div>
                        </div>


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

{{--    @include("component.echart.scripts",["data"=>$line_list_value["list_line_report"]["amount"]["data"],"series"=>$line_list_value["list_line_report"],"type"=>'multi_line',"id"=>"lineProductChart"])--}}

    @include("component.echart.scripts",["data"=>$product_pie_chart,"type"=>'pie',"id"=>"productPieChart"])


    {{--    @foreach($degree_pie_charts as $item)--}}
    {{--        @include("component.echart.scripts",["data"=>$item["data"],"type"=>'pie',"id"=>"degree_chart".$item["product_id"]])--}}

    {{--    @endforeach--}}

@endsection

@section("menu_styler")
    <div id="styleSelector" class="menu-styler ">
        <div class="style-toggler"><a href="#!" id="my_open_menu_styler" onclick="open_menu_styler()"></a></div>
        <div class="style-block "><h6 class="mb-2">تنظیمات گزارش</h6>


        </div>
    </div>

    </body>

    </html>
@endsection
