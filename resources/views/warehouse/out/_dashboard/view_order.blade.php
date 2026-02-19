@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل خروج از انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> اطلاعات سفارش  درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>

                    <a class="btn btn-outline-dark"  href="{{route("wh.out.dashboard.index")}}">بازگشت</a>
                </div>



            </div>


        </div>

        @include("sales.dashboard._order_info")

        @include("customer.group.buy._order_factor_customer_info")

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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });



    </script>
@endsection


