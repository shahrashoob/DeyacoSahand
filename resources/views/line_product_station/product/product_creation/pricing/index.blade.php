@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="col-md-12">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>مشخصات کالا </b></h5>
                    </div>
                    <div class="card-block" style="overflow: auto">
                        <div class="row">


                            @include("component.input._lable_product",["id"=>"product1","lable"=>"",
                                "product_property"=>$product,"show_collapse"=>1,
                                "value"=>$product->fullCaption()])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    @include("line_product_station.product.bom.bom._info",["route_path"=>"line_product_station.product.bom.","view_path"=>null])

    <div class="col-md-12">

        @if($has_actual_cost)

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><b> پیش بینی بهای تمام شده </b></h5>
                        </div>
                        <div class="card-block" style="overflow: auto">
                            @include("line_product_station.product.actual_cost._predict_info")
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><b> بهای تمام شده </b></h5>
                        </div>
                        <div class="card-block" style="overflow: auto">
                            @include("line_product_station.product.actual_cost._info")
                        </div>
                    </div>
                </div>
            </div>

        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>قیمت گذاری </b></h5>
                    </div>
                    <div class="card-block" style="overflow: auto">
                        <div class="row">
                            @include("line_product_station.product.pricing._info",["is_pricing"=>1])
                        </div>
                    </div>
                </div>
            </div>

        </div>


        {{--        <div class="row">--}}
        {{--            <div class="col-sm-12">--}}
        {{--                <div class="card">--}}
        {{--                    <div class="card-header">--}}
        {{--                        <h5><b>کالاهای مشابه در انتظار قیمت گذاری </b></h5>--}}
        {{--                    </div>--}}
        {{--                    <div class="card-block" style="overflow: auto">--}}
        {{--                        <div class="row">--}}
        {{--                         @foreach($product_creation_process_for_pricing as $item)--}}
        {{--                             {{$item->product->caption}}--}}
        {{--                         @endforeach--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}

        {{--        </div>--}}
    </div>
@endsection
@section("styles")

    <link rel="stylesheet" href="{{asset("assets/plugins/jstreeview/style.css")}}"/>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });

    </script>
@endsection

