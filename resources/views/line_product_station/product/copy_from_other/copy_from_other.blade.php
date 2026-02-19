@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

            <div class="col-sm-12">
                <h5>کپی کالا</h5>

                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="home-tab" data-toggle="tab" href="#home"
                           role="tab" aria-controls="home" aria-selected="true">تنظیمات کپی کالا</a>
                    </li>

                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form id="form1"
                              action="{{route("line_product_station.product.submit_copy_from_other")}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf

                            @include("line_product_station.product.copy_from_other._setting",["get_new_product_code"=>true])

                            <div class="" style="margin-top: 15px">
                                <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به داشبورد</a>


                                <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>


                            </div>

                        </form>
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

                "caption": "required",
                "code": "required",
                "product_id_auto": "required",

            }
        });
        $("#copy_consumed_0").change(function (){
            $("#copy_bom_0").prop("checked", true);
            $("#copy_material_flow_0").prop("checked", true);
        });

        $("#copy_route_0").change(function (){
            $("#copy_route_property_0").prop("checked", true);
            $("#copy_bom_0").prop("checked", true);
            $("#copy_material_flow_0").prop("checked", true);
        });

        $("#copy_route_property_1").change(function (){
            $("#copy_route_1").prop("checked", true);
        });
        $("#copy_bom_1").change(function (){
            $("#copy_route_1").prop("checked", true);
            $("#copy_consumed_1").prop("checked", true);
        });
        $("#copy_bom_0").change(function (){
            $("#copy_material_flow_0").prop("checked", true);
        });
        $("#copy_material_flow_1").change(function (){
            $("#copy_route_1").prop("checked", true);
            $("#copy_consumed_1").prop("checked", true);
            $("#copy_bom_1").prop("checked", true);
        });
    </script>
@endsection
