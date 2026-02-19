@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <div class="row">
        <div class="col-md-4 text-center" style="margin-bottom: 30px">
            <h3 class="f-w-300">
                <a href="{{route("hr.personal.shift_work_day.index",[$worker,$month-1==0?$year-1:$year,$month-1==0?12: $month-1])}}">
                    ماه قبل
                </a>
            </h3>
        </div>
        <div class="col-md-4 text-center">
            <h3 class="f-w-300 pcoded-badge label label-primary">{{$caption}}</h3>
        </div>
        <div class="col-md-4 text-center ">
            <h3 class="f-w-300">
                <a href="{{route("hr.personal.shift_work_day.index",[$worker,$month+1==13?$year+1:$year,$month+1==13?1: $month+1])}}">
                    ماه بعد
                </a>
            </h3>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h5> {{$worker->fullname()}}</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills" id="hcol-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" id="hcol-default-tab" data-toggle="pill"
                               href="#hcol-default" role="tab" aria-controls="hcol-default" aria-selected="true">
                                گزارش عملکرد در ماه
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link h-blue show" id="panel2-tab" data-toggle="pill"
                               href="#panel2" role="tab" aria-controls="panel2"
                               aria-selected="false">برنامه ساعت های کار در ماه</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link h-blue show" id="panel3-tab" data-toggle="pill"
                               href="#panel3" role="tab" aria-controls="panel3"
                               aria-selected="false">مانده مرخصی</a>
                        </li>
                        <li>

                        </li>

                    </ul>
                    <div class="tab-content pt-2" id="hcol-tabContent">
                        <div class="tab-pane show active" id="hcol-default" role="tabpanel"
                             aria-labelledby="hcol-default-tab">
                            @include("hr.personal.shift_work_day._user_operation_list")
                        </div>
                        <div class="tab-pane show" id="panel2" role="tabpanel" aria-labelledby="panel2-tab">
                            @include("hr.personal.shift_work_day._shift_day_list")
                        </div>
                        <div class="tab-pane show" id="panel3" role="tabpanel" aria-labelledby="panel3-tab">
                            @include("hr.personal.shift_work_day._leave_remainder")
                        </div>

                        <div class="tab-pane show" id="panel4" role="tabpanel" aria-labelledby="panel4-tab">

                        </div>
                        <div class="tab-pane show" id="panel5" role="tabpanel" aria-labelledby="panel5-tab">

                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12 center">
            <a href="{{route("hr.personal.index",[$worker,$worker->random])}}" class="btn btn-outline-dark">بازگشت</a>
            <a href="{{route("hr.personal.shift_work_day.download_operation_list",[$worker,$year,$month])}}"
               class="btn btn-info"><i class="fa fa-download"></i> دانلود گزارش عملکرد در {{$caption}} </a>


        </div>

    </div>

@endsection
@section("styles")
    @include("component.modal.md-modal._style")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
