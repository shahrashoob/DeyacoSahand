@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.buy._order_factor_products",["type_show"=>"minimal"])
    </div>
    <form id="form1" style="display: inline"
          action="{{route("customer_group.retail_customer.buy.shopping_cart_submit",$order)}}" method="post"
          autocomplete="off">
        @csrf


        <div class="row">

            <div class="col-md-12">


                        <input type="hidden" name="selling_type_id" class="selling_type"
                               value="{{$order->selling_type_id}}">

            </div>


        </div>

        <div class="row center" >
            <div class="w-100"><br/></div>


            <div class="col-md-12">
                <a class="btn btn-dark"
                   href="{{route("customer_group.retail_customer.buy.index",$order)}}">بازگشت</a>
                <button type="submit" class="btn btn-primary" id="btn_other"
                > تایید و ادامه
                </button>
            </div>
        </div>
    </form>

@endsection

@section("styles")

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "payment_method_id_auto": "required",

            }
        });

    </script>
@endsection
