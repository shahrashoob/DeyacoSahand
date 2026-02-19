@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')




    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم انبار - کد {{$form->getCode()}} </h5>
                </div>
                <div class="card-block">


                    <div class="row">
                        <div class="col-md-12">
                            <h4></h4>
                        </div>


                    </div>
                    @foreach($form->item as $form_item)
                        @include("warehouse.public._form_details",["form"=>$form_item,"product"=>$form_item->product,"form"=>$form])
                    @endforeach

                    <div class="row">
                        @include("component.input._lable",["id"=>"warehouse",'label'=>"انبار کالا","value"=>$form->warehouse->caption??""])
                        @include("component.input._lable",["id"=>"trans_kind",'label'=>"نوع تراکنش","value"=>$form->trans_kind_item->caption??""])
                        @include("component.input._lable",["id"=>"status_id",'label'=>"وضعیت فرم ","value"=>$form->status->caption??""])
                        @include("component.input._lable",["id"=>"packing_type",'label'=>"نوع بسته بندی ","value"=>isset($form_item->packing_type->caption)?$form_item->packing_type->fullCaption():""])

                    </div>
                    <div style="text-align: center">

                        @if($form->status_id==500000410)

                            <form id="form1" action="{{route("wh.dashboard.confirm_form",$form)}}" method="post"
                                  style="display: inline"
                                  autocomplete="off"
                                  novalidate="novalidate">
                                @csrf

                                @if($form->status_id==500000200)
                                    @include("component.input._lable",["id"=>"carrier_code",'label'=>" شماره حامل (غلطک | پالت)     ","value"=>$form_item->carrier->code])
                                @else
                                    @include("component.input._text",["id"=>"carrier_code",'label'=>"لطفا شماره حامل (غلطک | پالت) را وارد نمایید    ","value"=>""])
                                @endif

                                <button type="submit"
                                        class="btn btn-success"
                                        onclick="return confirm('آیا از تایید  اطمینان دارید؟')">تایید انبار
                                </button>
                            </form>

                            <form id="form1" action="{{route("wh.dashboard.reject_form",$form)}}" method="post"
                                  style="display: inline"
                                  autocomplete="off"
                                  novalidate="novalidate">
                                @csrf
                                <button type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('آیا از عدم تایید  اطمینان دارید؟')">عدم تایید انبار
                                </button>
                            </form>


                        @endif
                            <a href="{{route("wh.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

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


