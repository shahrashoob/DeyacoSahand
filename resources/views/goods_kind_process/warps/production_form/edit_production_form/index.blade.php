@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید  ")

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("warps.production_form.edit_production_form.submit",$form)}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ویرایش فرم تولید چله کشی - شماره {{$form->getCode()}} </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            <div class="col-md-12">
                                <h4>{{$product->caption}}</h4>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>"انتخاب چله ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"amount",'label'=>"متراژ چله","value"=>$form_item->amount,"autofocus"=>1])
                            @if(isset($product->sub_unit))
                                @include("component.input._number",["id"=>"sub_amount",'label'=>"وزن چله","value"=>$form_item->sub_amount??""])
                            @endif

                            @include("component.input._number",["id"=>"carrier_code",'label'=>"شماره غلطک","value"=>$form_item->carrier->code??""])
                            @include("component.input._number",["id"=>"lot_number",'label'=>"شماره همبافت","value"=>$request->lot_number??$form_item->lot_number->code])

                            @include(
                          "component.input._aotocomplet2",
                          ["id"=>"degree_id",
                          'label'=>"درجه چله",
                          "option"=>$degree_option["items"],
                          "val"=>$degree_option["value"],
                          "text"=>$degree_option["text"]
                      ])

                        </div>

                        <div style="text-align: center">
                            @if(isset($request->lot_number))
                                @include("component.input._hidden",["id"=>"new_lot_number","value"=>$request->lot_number??""])

                                <div class="alert alert-warning">
                                    شماره همبافت {{$request->lot_number}} در سیستم یافت نشد، آیا تمایل دارید شماره جدید
                                    تعریف کنید.
                                </div>
                                <a href="{{route("warps.production_form.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تعریف شماره همبافت جدید و ادامه</button>

                            @else
                                <a href="{{route("warps.production_form.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success"
                                        onclick="return confirm('آیا از تغییرات فرم چله اطمینان دارید؟')">ذخیره فرم
                                </button>
                            @endif

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </form>


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


