@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> تایید تحویل چله از انبار برای {{$machine->getCode()}}
                        - {{$machine->caption}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.warps_delivery_confirmation.submit",$machine)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div
                                class="w-100"></div>
                            @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->getCreateFormUser()->   fullname()])
                            @if(isset($product_request_form->forms[0]))
                                @foreach($product_request_form->forms[0]->form->item as $form_item)
                                    <div class="col-sm-12"><h5> ورودی
                                            {{$form_item->io_line_code}}
                                            - {{$form_item->product->caption}} </h5>
                                    </div>
                                    @include("component.input._text",["id"=>"carrier_".$form_item->id, "label"=>"شماره غلطک ورودی ".$form_item->io_line_code,"value"=>""])
                                    @include("component.input._lable",["label"=>" لات ","value"=>$form_item->lot_number->code??""])
                                    @include("component.input._lable",["label"=>$form_item->product->unit->measurement,"value"=>$form_item->amount." ".$form_item->product->unit->caption])
                                    @if(isset($form_item->product->sub_unit))
                                        @include("component.input._lable",["label"=>$form_item->product->sub_unit->measurement,"value"=>$form_item->sub_input." ".$form_item->product->sub_unit->caption])
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <hr/>
                        <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('آیا از تایید تحویل کالا  اطمینان دارید؟')">تایید تحویل کالا
                        </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div> @endsection @section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection @section("scripts")
    <script> $('#form1').validate({
            rules: {
                "carrier_1": "required",
                "carrier_2": "required",
                "carrier_3": "required",
                "carrier_4": "required",
            }
        }); </script> @endsection
