@extends('layouts.admin._master')
@section("page_header_title","گزارش 1005 - وضعیت ماشین آلات")

@section("content")
    <div class="row">
        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار ماشین ها به تفکیک وضعیت تولید(ساعت)</h5>
                    <div class="card-header-right">
                        <a href="{{route("report.1005.index")}}" class="btn btn-outline-dark">بازگشت</a>
                    </div>
                </div>
                <div class="card-block">


                    <div id="productionStatusPieChart" style="width: 100%; height: 600px; ">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>نمودار ماشین ها به تفکیک وضعیت روشن بودن(ساعت)</h5>
                    <div class="card-header-right">
                    </div>
                </div>
                <div class="card-block">
                    <div id="onStatusPieChart" style="width: 100%; height: 500px; ">
                    </div>
                    <div id="offReasonPieChart" style="width: 100%; height: 350px; ">
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

    {{-- $assessmentData    --}}
    @include("component.echart.scripts",["data"=>$time_list_production_status,"type"=>'pie',"id"=>"productionStatusPieChart"])

    @include("component.echart.scripts",["data"=>$time_list_on_status,"type"=>'pie',"id"=>"onStatusPieChart"])
    @include("component.echart.scripts",["data"=>$time_list_off_reason,"type"=>'pie2',"id"=>"offReasonPieChart"])

@endsection
