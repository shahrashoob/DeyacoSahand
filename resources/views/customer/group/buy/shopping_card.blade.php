@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.buy._order_factor_products",["allow_delete_rows"=>1])
    </div>
    <form id="form1" style="display: inline"
          action="{{route("customer_group.buy.shopping_cart_submit",$order)}}" method="post"
          autocomplete="off">
        @csrf


        <div class="row">

            <div class="col-md-12">

                {{--                    <div class="card">--}}
                {{--                        <div class="card-header">--}}
                {{--                            <h5>انتخاب نوع فاکتور</h5>--}}
                {{--                        </div>--}}
                {{--                        <div class="card-block overflow-auto">--}}


                <div class="row" style="text-align: center">
                    <div class="col-md-12">
                        {{--                                    <input type="radio" name="selling_type_id" class="selling_type"--}}
                        {{--                                           value="1" @if($order->selling_type_id==1) {{"checked"}} @endif > رسمی--}}
                        {{--                                    <input type="radio" name="selling_type_id" class="selling_type"--}}
                        {{--                                           value="2" @if($order->selling_type_id==2) {{"checked"}} @endif> غیر رسمی--}}
                        <input type="hidden" name="selling_type_id" class="selling_type"
                               value="{{$order->selling_type_id}}">
                    </div>

                </div>

                {{--                        </div>--}}
                {{--                    </div>--}}
            </div>


        </div>
        <div class="row">

            @include("customer.group.buy._order_consumed_products_form")
        </div>
        <div class="row center" >
            <div class="w-100"><br/></div>

            <input type="hidden" name="has_order_consumed_product" value="{{$has_order_consumed_product}}">

            <div class="col-md-12">
                <a class="btn btn-dark"
                   href="{{route("customer_group.buy.index",$order)}}">بازگشت</a>
                <button type="submit" class="btn btn-primary" id="btn_other"
                > تایید و ادامه
                </button>
            </div>
        </div>
    </form>

@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .select_class {
            height: 35px;
            padding: 2px;
        }
    </style>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "payment_method_id_auto": "required",

            }
        });
        $(".selling_type").change(function () {
            window.location.replace("{{route("customer_group.buy.shopping_cart",$order)}}" + "/" + $(this).val());
        })

    </script>
@endsection
