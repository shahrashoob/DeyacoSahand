@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت پیمانکاران")
@section('content')
    <div class="row">

        @include("warehouse.out.exit_form.qr._index")

        <div class="col-sm-12" style="text-align: center">


                <a href="{{route("contractor.admin.dashboard.log",$contractor_allocation)}}"
                   class="btn btn-outline-dark">بازگشت</a>



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


