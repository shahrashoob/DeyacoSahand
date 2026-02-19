@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$contractor_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <div id="panel_packing_item">
                        @include("contractor.panel.register_production._add_packing_item")
                    </div>

                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .table-styling input,.table-styling select{
            width: 120px;
        }
    </style>
@endsection
@section("scripts")
<script>

    $('#form1').validate({
        rules: {
            "packing_type_id": "required",
            "carrier_code": "required",
            "degree_id_auto": "required",
        }
    });
</script>
@endsection
