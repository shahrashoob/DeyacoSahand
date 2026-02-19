@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')


    <form id="form1" autocomplete="off" action="{{route("wh.input.entry_form.submit")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">
                @include("warehouse.input.entry_form._packing_form_rows")
            </div>


        </div>
    </form>



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("warehouse.input.entry_form._script")
    <script>
        $('#form1').validate({
            rules: {
                "product_id": "required",
                "packing_type_id": "required",
                "degree_id": "required",
                "lot_number_code": "required",
                "warehouse_id": "required",
                "trans_kind_id": "required",
                "opp_kind_id": "required",
                "cost_center_id": "required",
                "description": "required",
                "packing_form_rows": "required",
            }
        });
    </script>
@endsection
