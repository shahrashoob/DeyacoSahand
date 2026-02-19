@extends('layouts.admin._master',["header_client_buy"=>false])
@section("page_header_title","داشبورد ".($machine_allocation->machine?"ماشین آلات ":"پیمانکاران")."-  ".
($machine_allocation->machine?$machine_allocation->machine->fullCaption():$machine_allocation->contractor->caption)
)
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine_allocation->machine?"کارت تولید ":"دستور پیمان"}} {{$machine_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">
                    @include("production.public_module.register_production._source_production_form_item")

                    <div id="panel_packing_item">
                        @include("production.public_module.register_production._add_packing_item")
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
        .table-styling input, .table-styling select {
            width: 120px;
        }
        .table td , .table th{
            white-space: none !important;
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
