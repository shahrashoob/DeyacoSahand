@extends('layouts.admin._master')
@section("page_header_title","گزارش 1006 - گزارش کنترل کیفیت (بافندگی) - بر اساس تولید کالا")

@section("content")
    <div class="row">

        <div class="col-md-12 col-xl-4">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع متراژ سیستم</h5>
                    <h4 class="f-w-300 mb-3">{{$sum_all_list->sum_amount}} متر </h4>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">مجموع متراژ کنترل کیفیت</h5>
                    <h4 class="f-w-300 mb-3">{{$sum_all_list->sum_amount_after_control}} متر </h4>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card user-card">
                <div class="card-block">
                    <h5 class="m-b-15">تعداد فرم تولید</h5>
                    <h4 class="f-w-300 mb-3"> {{$sum_all_list->count_packing_forms}} عدد </h4>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار خطی کنترل کیفیت به تفکیک روز</h5>
                    <div class="card-header-right">
                        <a href="{{route("report.1006.index")}}" class="btn btn-outline-dark">بازگشت</a>
                    </div>
                </div>
                <div class="card-block">


                    <div id="lineProductChart" style="width: 100%; height: 300px; ">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار کنترل کیفیت به تفکیک کالا ها (متراژ)</h5>

                    <div class="card-header-right">
                        <a href="{{route("report.1006.index")}}" class="btn btn-outline-dark">بازگشت</a>
{{--                        <a href="{{route("report.1006.faults_export")}}" class="btn btn-outline-dark">دانلود فایل اکسل نقص ها</a>--}}
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
                    <h5>نمودار کنترل کیفیت به تفکیک درجه ها (متراژ)</h5>
                    <div class="card-header-right">
                        <a href="{{route("report.1006.index")}}" class="btn btn-outline-dark">بازگشت</a>
                    </div>
                </div>
                <div class="card-block">


                    <div id="degreePieChart" style="width: 100%; height: 400px; ">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار کنترل کیفیت به تفکیک درجه هر کالا</h5>
                    <div class="card-header-right">
                        <a href="{{route("report.1006.index")}}" class="btn btn-outline-dark">بازگشت</a>
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
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase  show" id="tab4-tab" data-toggle="tab"
                                       href="#tab4" role="tab"
                                       aria-controls="tab4" aria-selected="false">نقص ها </a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTab1Content">

                                <div class="tab-pane fade active show" id="tab1" role="tabpanel"
                                     aria-labelledby="tab1-tab">
                                    <div class="row">
                                        @foreach($degree_pie_charts as $item)
                                            <div class="col-md-6">
                                                <div id="degree_chart{{$item["product_id"]}}"
                                                     style="width: 100%; height:400px; ">
                                                </div>
                                                <div class="center">
                                                    {{$item["title"]}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane fade  " id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                    <div class="table-responsive">
                                        <table class="table table-styling center">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>کالا / درجه</th>
                                                <th>کل متراژ</th>
                                                @foreach($degree_header_list as $header)
                                                    <th>{{$header->fullCaption()}}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $row=1;@endphp
                                            @foreach($degree_pie_charts as $item)
                                                <tr>
                                                    <td>{{$row++}}</td>
                                                    <td>
                                                        {{$item["title"]}}
                                                    </td>
                                                    <td>
                                                        {{$item["sum"]}}
                                                    </td>
                                                    @foreach($degree_header_list as $header)
                                                        <td>
                                                            @php $value=isset($item["data"][$header->id])?$item["data"][$header->id]["value"]:"0";@endphp
                                                            {{$value!=0?$value." متر ":""}}
                                                            {{$value!=0?"(".(round($value/$item["sum"]*100))."%)":""}}
                                                        </td>
                                                    @endforeach


                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade  " id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                                    <div class="table-responsive">
                                        <table class="table table-styling center">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ماشین / درجه</th>
                                                <th>راندمان</th>
                                                <th>کل متراژ</th>
                                                @foreach($degree_header_list as $header)
                                                    <th>{{$header->fullCaption()}}</th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $row=1;@endphp
                                            @foreach($machine_table_list as $item)
                                                <tr>
                                                    <td>{{$row++}}</td>
                                                    <td>
                                                        {{$item["title"]}}
                                                    </td>
                                                    <td>
                                                        {{isset($machine_efficiency[$item["machine_id"]]["efficiency_1"])?$machine_efficiency[$item["machine_id"]]["efficiency_1"]."%":""}}
                                                    </td>
                                                    <td>
                                                        {{$item["sum"]}}
                                                    </td>
                                                    @foreach($degree_header_list as $header)
                                                        <td>
                                                            @php $value=isset($item["data"][$header->id])?$item["data"][$header->id]["value"]:"0";@endphp
                                                            {{$value!=0?$value." متر ":""}}
                                                            {{$value!=0?"(".(round($value/$item["sum"]*100))."%)":""}}
                                                        </td>

                                                    @endforeach


                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade  " id="tab4" role="tabpanel" aria-labelledby="tab4-tab">

                                    @include("goods_kind_process.fabric_raw.packing_form._quality_control_product_faults")
                                </div>

                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")


@endsection


@section("scripts")
    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>


    @include("component.echart.scripts",["data"=>$list_line_report["amount"]["data"],"series"=>$list_line_report,"type"=>'multi_line',"id"=>"lineProductChart"])
    @include("component.echart.scripts",["data"=>$product_pie_chart,"type"=>'pie',"id"=>"productPieChart"])
    @include("component.echart.scripts",["data"=>$degree_pie_chart,"type"=>'pie2',"id"=>"degreePieChart"])

    @foreach($degree_pie_charts as $item)
        @include("component.echart.scripts",["data"=>$item["data"],"type"=>'pie',"id"=>"degree_chart".$item["product_id"]])

    @endforeach

@endsection
