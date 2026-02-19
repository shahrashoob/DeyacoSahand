@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش   ".$order->code())
@section('content')

    <div class="row">
        @include("warehouse.out.exit_form.qr._index")

        <div class="col-sm-12">

            <div style="text-align: center">

                <a href="{{route("sales.dashboard.view_order",$order)}}"
                   class="btn btn-outline-dark">بازگشت</a>
                @if($allow_create_reject_form)
                    <a href="{{route("sales.dashboard.reject_product.index",[$order,$form])}}"
                       class="btn btn-outline-danger">ثبت مرجوعی</a>
                @endif


            </div>

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


