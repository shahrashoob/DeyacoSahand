@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">
        @include("customer.group.buy._order_factor_products")
    </div>
    <form id="form1" style="display: inline"
          action="{{route("customer_group.buy.address_submit",$order)}}" method="post"
          autocomplete="off">
        @csrf
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>اطلاعات آدرس و تماس تحویل گیرنده بار</h5>

                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("customer.public._address_item_input",["address"=>$order->address])
                            <div class="col-md-4">
                                <a class="btn btn-dark"
                                   href="{{route("customer_group.buy.shopping_cart",$order)}}">بازگشت</a>
                                <button type="submit" class="btn btn-primary" id="btn_other"
                                > تایید و ادامه
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </form>


@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                mobile: { minlength: 10, maxlength: 10},
                phone: { minlength: 11, maxlength: 11},
                postal_code: { minlength: 10, maxlength: 10},
            }
        });


       @include("customer.group.buy._address_script")
    </script>
@endsection
