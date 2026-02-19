@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> عدم راه اندازی شیفت {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.failure_to_launch.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")
                        @include("component.input._textarea",["id"=>"description","label"=>"علت عدم راه اندازی شیفت"])


                        <div class="col-md-12">
                            آیا با همین رول پارچه ادامه می دهید
                            <input type="radio" name="no_doff_is_force" id="no_doff_is_force1" value="1"> بله
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            <input type="radio" name="no_doff_is_force"  id="no_doff_is_force2" value="-1"> خیر
                        </div>
                        <div id="get_carrier_id" style="display: none">
                            @if( count( $packing_type_option["items"] ) != 1 )
                                @include(
                                "component.input._aotocomplet2",
                                ["id"=>"packing_type_id",
                                'label'=>"نوع بسته بندی",
                                "option"=>$packing_type_option["items"],
                                "val"=>$packing_type_option["value"],
                                "text"=>$packing_type_option["text"]
                            ])
                            @else
                                @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_option["items"][0]["value"]])

                            @endif
                            @include("component.input._number",["id"=>"carrier_id","lable"=>" شماره غلطک پارچه خام جدید ","value"=>$carrier_id])

                        </div>
                        <br/>
                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-danger">ثبت عدم راه اندازی شیفت</button>
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
