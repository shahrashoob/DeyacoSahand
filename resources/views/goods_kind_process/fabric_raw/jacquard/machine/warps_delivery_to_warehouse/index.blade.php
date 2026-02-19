@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> تایید تحویل چله به انبار برای {{$machine->getCode()}} - {{$machine->caption}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.warps_delivery_to_warehouse.submit",[$machine,$form])}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div class="w-100"></div>
                            @include("component.input._lable",["label"=>"شماره فرم","value"=>$form->code])

                            @include("component.input._lable",["label"=>"استخراج کننده چله","value"=>$form->worker->fullname()])
                            @include("component.input._lable",["label"=>"کالا","value"=>$form->item[0]->product->fullCaption()])
                            @include("component.input._lable",["label"=>"انبار تحویل کالا","value"=>$form->warehouse->caption])
                            @include("component.input._lable",["label"=>" حامل","value"=>$form->item[0]->carrier->getCaption()])
                            @include("component.input._lable",["label"=>"درجه","value"=>$form->item[0]->degree->caption])
                            @include("component.input._lable",["label"=>"متراژ","value"=>$form->item[0]->amount])
                            @include("component.input._lable",["label"=>"وزن","value"=>$form->item[0]->sub_amount])
                            @include("component.input._lable",["label"=>"وضعیت فرم","value"=>$form->status->caption])

                        </div>
                        <hr/>
                        <div style="text-align: center">
                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('آیا از  تحویل کالا به انبار اطمینان دارید؟')"> تحویل کالا به انبار
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
