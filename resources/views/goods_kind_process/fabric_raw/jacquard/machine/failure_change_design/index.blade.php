@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> عدم تغییر کالیته {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.failure_change_design.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        @include("component.input._textarea",["id"=>"description","label"=>"علت عدم انجام تغییر کالیته"])




                        <br/>
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-danger">ثبت عدم تغیر کالیته</button>
                        </div>

                    </form>
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

    <script>
        $('#form1').validate({
            rules: {
                "description": "required",
            }
        });

    </script>
@endsection
