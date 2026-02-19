@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <form id="form1" action="{{route($route_path."show_tariff_together",[$product,$productTariffPricing,$product_creation_process])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>تعرفه گذاری همراه با کالای  {{$product->caption}}</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-6">
                            @include("component.input.select2._select2",[
                                "id"=>"product_ids",
                                "label"=>" کالای همراه جهت تعرفه گذاری ",
                                "option"=>$product_option["items"],
                                "val"=>$product_option["value"],
                                "text"=>$product_option["text"],
                                "class_col"=>"",
                                ])

                        </div>
                        <div class="col-md-12">
                            <a href="{{route($route_path."index",[$product_creation_process])}}" class="btn btn-outline-dark"> بازگشت</a>

                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    </form>

@endsection
@section("styles")

    @include("component.input.select2._script")
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "product_ids": "required",
            }
        });

    </script>
@endsection

