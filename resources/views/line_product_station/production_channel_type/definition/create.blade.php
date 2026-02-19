@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">

        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ایجاد کانال تولید جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.production_channel_type.definition.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"نام کانال تولید ","value"=>"","autofocus"=>1])
                            @include("component.input._text",["id"=>"min_capacity",'label'=>"حداقل ظرفیت ","value"=>""])
                            @include("component.input._text",["id"=>"max_capacity",'label'=>"حداکثر ظرفیت ","value"=>""])
                            @include("component.input._text",["id"=>"max_number_of_sequences",'label'=>"تعداد کانال مشابه ","value"=>1])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"production_channel_category_id",
                                    "label"=>"گروه کانال تولید ",
                                    "option"=>$machine_type_production_channel_type_option["items"],
                                    "val"=>$machine_type_production_channel_type_option["value"],
                                    "text"=>$machine_type_production_channel_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>



                        </div>

                        <a href="{{route("line_product_station.production_channel_type.definition.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">افزودن</button>

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
                "production_channel_category_id_auto": "required",
                "max_number_of_sequences": {required: true, min: 1},
                "min_capacity": {required: true, min: 1},
                "max_capacity": {required: true, min: 1}
            }
        });
    </script>
@endsection
