@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم تحویل کالا از انبار - کد {{$warps_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">
                    <form id="form1" action="{{route("wh.delivery_dashboard.delivery",$warps_request_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">

                            <br/>
                            <br/>
                            <div class="w-100"></div>
                            @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$warps_request_form->worker->   fullname()])


                            @foreach($warps_request_form->items as $item)


                                @if($warps_request_form->status_id == "7005001" || $warps_request_form->status_id == "7005005")
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"warehouse_product_id_".$item->id,
                                        "label"=>("انتخاب حامل (غلطک) برای ".$item->product->caption??"")." (خط ورودی ".$item->input_line_code.")",
                                        "option"=>$lot_number_options[$item->product_id]["items"],

                                        ])
                                @else
                                    @include("component.input._lable",[
                                        "class_col"=>"col-sm-12",
                                      "label"=>" درخواست خط ورودی ".$item->input_line_code,
                                        "value"=>
                                        ($item->warehouse_product->carrier->carrier_type->caption??"") ." ".
                                        ($item->warehouse_product->carrier->code??"")." - ".
                                        ($item->product->caption??"")." (".
                                        "همبافت ". ($item->warehouse_product->lot_number->code??"").")"
                                      ])
                                @endif

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label> بسته بندی های مجاز ورودی {{$item->input_line_code}}:</label>
                                        @foreach($item->warps_request_form_packing_types as $product_packing_item)
                                            {{$product_packing_item->packing_type->fullCaption()}} ,
                                        @endforeach
                                        <b></b>

                                    </div>
                                </div>
                            @endforeach


                            @include("component.input._lable",["label"=>"وضعیت","value"=>$warps_request_form->status->caption??""])

                        </div>

                        <hr/>

                        <div style="text-align: center">
                            <a href="{{route("wh.delivery_dashboard.index")}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            @if($warps_request_form->status_id == "7005001" || $warps_request_form->status_id == "7005005")
                                <button type="submit"
                                        class="btn btn-success"
                                        onclick="return confirm('آیا از تحویل کالا  اطمینان دارید؟')">تحویل کالا
                                </button>
                            @endif

                        </div>
                    </form>
                </div>


            </div>

        </div>

        @include("warehouse.delivery_dashboard.log")

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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });
    </script>
@endsection


