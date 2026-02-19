@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش    {{$bom->caption}} </h5>
                </div>
                <div class="card-block">

                    @include("line_product_station.product.bom.bom._info_edit")

                </div>
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
                "caption": "required",
                "code": "required",
                "active_status_id_auto": "required",
            }
        });
    </script>
@endsection
