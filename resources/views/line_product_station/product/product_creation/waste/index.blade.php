@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت اطلاعات ضایعات کالا</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.product_creation.basic_information_registration._info")
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row" id="div_content">

                    @include("line_product_station.product.waste._info")

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>

       #div_content .card-header{
            background: #dfe1f3 !important;
        }
       #div_content .multi-collapse{
            border:5px solid #dfe1f3;
        }
       #div_content .accordion .card{
            margin-bottom: 5px;
        }
    </style>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "waste_during_consumption_id_1": "required",
                @foreach($product->route()->where("active_status_id",1200)->get() as $route)
                "waste_in_route_{{$route->id}}_1": "required",
                "waste_in_route_{{$route->id}}_1_percent": "required",
                @endforeach
            }
        });

    </script>
@endsection
