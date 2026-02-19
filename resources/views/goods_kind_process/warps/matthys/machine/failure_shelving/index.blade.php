@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> عدم قفسه گذاری {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("warps.matthys.machine.failure_shelving.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        @include("component.input._textarea",["id"=>"description","label"=>"علت عدم قفسه گذاری"])




                        <br/>
                        <div class="col-md-12">
                            <a href="{{route("warps.matthys.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-danger">ثبت عدم قفسه گذاری</button>
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
                "shift_work_id_auto": "required",
                "packing_type_id_auto": "required",
                "contour_1_value": "required",
                "carrier_id": "required",
                "description": "required",
                "no_doff_is_force": "required",
            }
        });
        $("#no_doff_is_force1,#no_doff_is_force2").click(function (){
            if($("#no_doff_is_force1").is(":checked")){
                $("#get_carrier_id").css("display","none");
            }else{
                $("#get_carrier_id").css("display","");
            }
        })
    </script>
@endsection
