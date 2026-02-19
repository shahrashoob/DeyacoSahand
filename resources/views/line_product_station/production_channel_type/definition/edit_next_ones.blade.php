@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست کانال های مجاز بعدی برای
                        <b>
                            {{ $production_channel_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">




                        @include("line_product_station.machine_type.production_channel._next_ones",["route_path"=>"line_product_station.production_channel_type.definition"])





                </div>

            </div>
            <a href="{{route("line_product_station.production_channel_type.definition.edit",$production_channel_type)}}"
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
                "next_production_channel_type_id": "required",
                "priority_number": {"required":true,"min":1},
            }
        });
    </script>
@endsection
