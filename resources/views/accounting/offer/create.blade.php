@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن تخفیف جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.offer.store")}}" method="post"
                          novalidate="novalidate" autocomplete="off">
                        @csrf
                        <div class="row">
                            @include("component.input._hidden",["id"=>"offer_type_id", "value"=>$offer_type_id])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>" نام کالا   ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            @include("component.input._number",["id"=>"degree_code","lable"=>" کد درجه کالا", "value"=>""])
                            @include("component.input._number",["id"=>"min_buy","lable"=>" حداقل خرید", "value"=>$offer->min_buy])
                            @include("component.input._number",["id"=>"max_buy","lable"=>" حداکثر خرید", "value"=>$offer->max_buy])
                            @include("component.input.datepicker._datepicker",["id"=>"start_datetime","lable"=>"از تاریخ ","value"=>$offer->start_datetime??null])

                            @include("component.input.datepicker._datepicker",["id"=>"end_datetime","lable"=>"تا تاریخ ","value"=>$offer->end_datetime??null])

                            @if($offer_type_id==500)
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"channel_type_id",
                                        "label"=>"کانال مشتری  ",
                                        "option"=>$channel_type_option["items"],
                                        "val"=>$channel_type_option["value"],
                                        "text"=>$channel_type_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            @else
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"customer_id",
                                        "label"=>"مشتریان  ",
                                        "option"=>$customer_option["items"],
                                        "val"=>$customer_option["value"],
                                        "text"=>$customer_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            @endif

                            @include("component.input._number",["id"=>"percent_off","lable"=>"  درصد تخفیف", "value"=>$offer->percent])
                            @include("component.input._number",["id"=>"percent_free","lable"=>"  درصد رایگان ", "value"=>$offer->free_count])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_free_id",
                                    "label"=>" نام کالای رایگان   ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])

                            </div>
                            @include("component.input._number",["id"=>"degree_free_code","lable"=>" کد درجه کالای رایگان", "value"=>""])

                        </div>

                        <a href="{{route("accounting.offer.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت تخفیف جدید</button>

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

        $("#product_id").click(function () {
            alert();
         //  get_degree()
        })

        function get_degree() {
            request = $.ajax({
                url: "{{url("api/option/get_degree")}}",
                type: "post",
                data: {
                    "type": 0,
                    "$product_id": $("#product_id").val(),
                    "option_id":"degree_id",
                    "option_label":"درجه کالا"
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#degree_option").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }

        $('#form1').validate({
            rules: {
                "product_id_auto": "required",
                "min_buy": "required",
                "max_buy": "required",
                "start_datetime_value": "required",
                "end_datetime_value": "required",
                "channel_type_id_auto": "required",
                "customer_id_auto": "required",
                "product_free_id_auto": "required",
                "percent_off": "required",
                "percent_free": "required",
            }
        });
    </script>
@endsection
