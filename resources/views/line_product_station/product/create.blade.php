@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

            <div class="col-sm-12">
                <h5>افزودن کالای جدید</h5>

                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="home-tab" data-toggle="tab" href="#home"
                           role="tab" aria-controls="home" aria-selected="true">اطلاعات پایه</a>
                    </li>

                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @include("line_product_station.product.init_info._init_info",["copy_from_other"=>1])
                    </div>



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
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "product_service_type_id":"required",
                @for($k=1;$k<=2;$k++)
                "product_service_type_id{{$k}}": "required",
                "caption{{$k}}": "required",
                "code{{$k}}": "required",
                "active_status_id{{$k}}": "required",
                "unit_id{{$k}}": "required",
                "goods_kind_id{{$k}}": "required",
                "goods_type_id{{$k}}": "required",
                "number_in_carton{{$k}}": {required: true, min: 1},
                "weight{{$k}}": "required",
                "ic{{$k}}": "required",
                "service_id_in_employer_system{{$k}}": "required",
                "supply_type_id{{$k}}": "required",
                @endfor
                    @if(!$product->image)
                "image_file": "required",
                @endif
            }
        });
    </script>
    @include("line_product_station.product._init_info_script")
{{--    <script>--}}
{{--        $('#form1').validate({--}}
{{--            rules: {--}}
{{--                "caption": "required",--}}
{{--                "code": "required",--}}
{{--                "status_id_auto": "required",--}}
{{--                "unit_id_auto": "required",--}}
{{--                "goods_kind_id_auto": "required",--}}
{{--                "goods_type_id_auto": "required",--}}
{{--                "number_in_carton": "required",--}}
{{--                "weight": "required",--}}
{{--                "ic": "required",--}}
{{--                "image_file": "required",--}}
{{--            }--}}
{{--        });--}}
{{--        $("#goods_kind_id").change(function () {--}}
{{--            get_new_option(--}}
{{--                $("#product_type_id").val(),--}}
{{--                $("#goods_kind_id").val(),--}}
{{--                "گروه کالایی",--}}
{{--                "product_type_id",--}}
{{--                "product_type"--}}
{{--            )--}}
{{--        })--}}
{{--    </script>--}}
@endsection
