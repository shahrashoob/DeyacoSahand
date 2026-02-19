@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درجه بندی فرم {{$production_form->getCode()}} - باند {{$production_form_item->band_code}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.production_form.grading.submit",$production_form_item)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="w-100"></div>

                        <div class="table-responsive">
                            @include("component.input._lable",["id"=>"","lable"=>"کارت تولید  ","value"=>$production_form_item->production->serial,"class_col"=>"col-md-12"])

                            @include("component.input._lable_product",["id"=>"product".$production_form_item->id,"lable"=>"   پارچه خام",
                        "product_property"=>$production_form_item->production->product,
                        "value"=>$production_form_item->production->product->code."-".$production_form_item->production->product->caption,"col"=>4])

                            @include("component.input._lable",["id"=>"","lable"=>" ماشین","value"=>$production_form->machine->code." - ".$production_form->machine->caption])

                            @include("component.input._lable",["id"=>"","lable"=>" کد غلطک پارچه","value"=>$production_form->carrier->code])

                            @include("component.input._lable",["id"=>"","lable"=>"متراژ  ","value"=>$production_form_item->amount." ".$production_form_item->product->unit->caption,"class_col"=>"col-md-12"])

                            <div class="form-group">
                                <b>متراژ (پس از کنترل کیفیت): </b>
                            </div>
                            @foreach($production_form_item->lot_numbers()->orderByDesc("id")->get() as $item)
                                @include("component.input._number",[
                                    "id"=>"data[lot_number][".($item->lot_number->id??0)."]",
                                    "lable"=>"متراژ همبافت  "."  ".$item->lot_number->code??"***",
                                    "value"=>"",
                                    "class_col"=>"col-md-3"])
                            @endforeach

                            <div class="col-md-12">
                                <a href="{{route("fabric_raw.production_form.dashboard.view",$production_form_item->production_form)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ادامه</button>
                            </div>


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
                @foreach($production_form_item->lot_numbers as $item)
                    "data[lot_number][{{$item->lot_number->id??0}}]": "required",
                @endforeach
            }
        });
    </script>
@endsection
