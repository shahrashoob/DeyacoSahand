@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید  ")

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("warps.production_form.implementation_period_form.submit_complete_form",[$product,$machine_warps_id])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ثبت فرم تولید چله کشی ( ویژه دوره پیاده سازی) </h5>
                    </div>
                    <div class="card-block">

                        <div class="row">
                            <br/>
                            <br/>
                            @include("component.input._lable",["id"=>"machine_warps_id",'label'=>"شماره ماشین","value"=>$machine_warps_id])

                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"amount",'label'=>"متراژ چله","value"=>$request->amount??"","autofocus"=>1])
                            @if(isset($product->sub_unit))
                                @include("component.input._number",["id"=>"sub_amount",'label'=>"وزن خالص چله","value"=>$request->sub_amount??""])
                            @endif

                            @include(
                            "component.input._aotocomplet2",
                            ["id"=>"packing_type_id",
                            'label'=>"نوع بسته بندی",
                            "option"=>$packing_type_option["items"],
                            "val"=>$packing_type_option["value"],
                            "text"=>$packing_type_option["text"]
                        ])
                            @include("component.input._number",["id"=>"carrier_code",'label'=>"شماره غلطک","value"=>$request->carrier_code??""])
                            @include("component.input._text",["id"=>"lot_number",'label'=>"شماره همبافت ","value"=>$request->lot_number??""])




                            @include(
                            "component.input._aotocomplet2",
                            ["id"=>"degree_id",
                            'label'=>"درجه چله",
                            "option"=>$degree_option["items"],
                            "val"=>$degree_option["value"],
                            "text"=>$degree_option["text"]
                        ])

                        </div>

                    </div>
                    <div class="col-md-12">
                        @if(isset($request->lot_number))
                            @include("component.input._hidden",["id"=>"new_lot_number","value"=>$request->lot_number??""])

                            <div class="alert alert-warning">
                                شماره همبافت  {{$request->lot_number}} در سیستم یافت نشد، آیا تمایل دارید شماره جدید تعریف کنید.
                            </div>
                            <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-success">تعریف شماره همبافت جدید   و ادامه</button>

                        @else
                            <a href="{{route("warps.production_form.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                                                 <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                            @endif
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
                "amount": "required",
                "sub_amount": "required",
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
                "packing_type_id_auto": "required",
            }
        });
    </script>
@endsection


