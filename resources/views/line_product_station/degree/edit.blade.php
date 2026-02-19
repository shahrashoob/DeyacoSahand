@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش {{$degree->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.degree.update",[$goods_kind,$degree])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._lable",["id"=>"code",'label'=>"کد درجه ","value"=>$degree->getCode(),"readonly"=>1])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان درجه ","value"=>$degree->caption])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"warehouse_id",
                                    "label"=>"انبار ذخیره کالا ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"degree_type_id",
                                    "label"=>" نوع درجه ",
                                    "option"=>$degree_type_option["items"],
                                    "val"=>$degree_type_option["value"],
                                    "text"=>$degree_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            @include("component.input._number",["id"=>"percent_of_price_reduction",'label'=>"درصد کاهش قیمت  ","value"=>$degree->percent_of_price_reduction,"autofocus"=>1])


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>"وضعیت ",
                                    "option"=>$active_status_option["items"],
                                    "val"=>$active_status_option["value"],
                                    "text"=>$active_status_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("line_product_station.degree.index",$degree->goods_kind_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "warehouse_id_auto": "required",
                "active_status_id": "required",
                "max_sale_type_auto": "required",
                "degree_type_auto": "required",
                "percent_of_price_reduction": "required",
            }
        });
    </script>
@endsection
