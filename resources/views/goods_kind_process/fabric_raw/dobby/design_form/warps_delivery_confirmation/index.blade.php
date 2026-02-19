@extends('layouts.admin._master')

@section("page_header_title"," داشبورد طراحی - بافندگی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تایید تحویل چله از انبار برای  فرم طراحی {{$design_form->getCode()}}    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.design_form.warps_delivery_confirmation.submit",$design_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">


                            <div class="w-100"></div>
                            @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$warps_request_form->getCreateFormUser()->   fullname()])
                            @foreach($warps_request_form->items as $item)
                                <div class="col-sm-12">
                                    <h5>
                                        {{$item->product->caption}}
                                    </h5>
                                </div>
                                @include("component.input._lable",["label"=>"شماره غلطک","value"=>$item->warehouse_product->carrier->code??""])
                                @include("component.input._lable",["label"=>"شماره لات (شید)","value"=>$item->warehouse_product->lot_number->code??""])
                                @include("component.input._lable",["label"=>"مقدار اصلی","value"=>$item->warehouse_product->input." ".$item->product->unit->caption])
                                @if(isset($item->product->sub_unit))
                                    @include("component.input._lable",["label"=>"مقدار فرعی","value"=>$item->warehouse_product->sub_input." ".$item->product->sub_unit->caption])
                                @endif
                            @endforeach


                        </div>

                        <hr/>

                        <div style="text-align: center">
                            <a href="{{route("wh.delivery_dashboard.index")}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit"
                                    class="btn btn-success"
                                    onclick="return confirm('آیا از تایید تحویل کالا  اطمینان دارید؟')">تایید تحویل
                                کالا
                            </button>

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
                "contour_1_value": "required",
                "contour_2_value": "required",
                "contour_3_value": "required",
                "carrier_id": "required",
                "lot_1_code": "required",
                "lot_2_code": "required",
                "lot_3_code": "required",
                "lot_4_code": "required",
            }
        });
    </script>
@endsection
