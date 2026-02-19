@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> {{$shift_delivery_module->caption}} </h5>
                </div>
                <div class="card-block ">


                    <div class="row">

                        <div class="alert alert-info md-col-12" style="width: 100%">
                            <h5> بافنده محترم، ضمن عرض خسته نباشید و خداقوت؛ نمره ارزیابی عملکرد شما در این شیفت به صورت
                                زیر می باشد.</h5>


                        </div>
                        <div class="center col-md-12">
                            <h1> <span class="badge  ">

                                    نمره ارزیابی عملکرد شما:
                                    {{round($efficiency,2)}}%

                                </span></h1>
                        </div>
                        <div class="col-sm-12 center">
                            <a class="btn btn-outline-dark"
                               href="{{route("hr.personal.index",[$worker,$worker->random])}}">بازگشت</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
