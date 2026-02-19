@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تکه پارچه در انتظار بسته بندی {{$fabric_raw_grading->code}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.packing.fabric_waiting_for_packing.submit",$fabric_raw_grading)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            @include("component.input._lable",["id"=>"","lable"=>" نام کالا","value"=>$fabric_raw_grading->product->code." - ".$fabric_raw_grading->product->caption])
                            @include("component.input._lable",["id"=>"","lable"=>" درجه کالا ","value"=>$fabric_raw_grading->degree->caption??""])
                            @include("component.input._lable",["id"=>"","lable"=>"  لات (همبافت)","value"=>$fabric_raw_grading->lot_number->code??""])
                            @include("component.input._lable",["id"=>"","lable"=>"متراژ کنترل کیفیت","value"=>$fabric_raw_grading->amount_after_control??""])

                            @if( $post_user->checkButtonPermission($info["route"]."index") && $fabric_raw_grading->status_id ==7006002 )
                                <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"packing_type_id",
                                        "label"=>"نوع بسته بندی ",
                                        "option"=>$packing_type_option["items"],
                                        "val"=>$packing_type_option["value"],
                                        "text"=>$packing_type_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>

                                @include("component.input._text",["id"=>"carrier_id","lable"=>"  حامل بسته بندی","value"=>$carrier->code??""])
                                @include("component.input._number",["id"=>"final_amount","lable"=>"  متراژ نهایی","value"=>""])

                                <div class="col col-md-12">

                                    <a href="{{route("fabric_raw.packing.fabric_waiting_for_packing.index")}}"
                                       class="btn btn-outline-dark">
                                        بازگشت
                                    </a>
                                    <button class="btn btn-primary"
                                            onclick="return confirm('آیا از ثبت حامل اطمینان دارید؟')">
                                        ثبت حامل
                                    </button>
                                </div>
                            @else
                                @include("component.input._lable",["id"=>"","lable"=>"حامل","value"=>isset($fabric_raw_grading->packing_form_item->packing_form->carrier)?$fabric_raw_grading->packing_form_item->packing_form->carrier->getCaption():""])

                                <a href="{{route("fabric_raw.packing.fabric_waiting_for_packing.index")}}"
                                   class="btn btn-outline-dark">
                                    بازگشت
                                </a>
                            @endif

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
                "packing_type_id_auto": "required",
                "carrier_id": "required",
                "final_amount": "required",
            }
        })
    </script>
@endsection
