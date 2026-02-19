@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <form id="form1" style="display: inline" action="{{route("hr.post.update.info",$post)}}" method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

                <div class="col-sm-12">

                    <h5> مدیریت پست {{$post->code ." - ".$post->caption}} ({{$post->shift->caption??""}})
                        <a href="{{route("hr.post.index")}}" class="btn btn-dark btn-sm">بازگشت</a>

                    </h5>

                        <hr>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link  text-uppercase" id="home-tab" data-toggle="tab" href="#home"
                               role="tab" aria-controls="home" aria-selected="true">دسترسی به منو ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="line-tab" data-toggle="tab" href="#line"
                               role="tab"
                               aria-controls="line" aria-selected="false">دسترسی به خط ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="contact-tab" data-toggle="tab" href="#contact"
                               role="tab"
                               aria-controls="contact" aria-selected="false">دسترسی به انبار</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="channel-tab" data-toggle="tab" href="#channel"
                               role="tab"
                               aria-controls="channel" aria-selected="false">دسترسی به کانال های توزیع</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="province-tab" data-toggle="tab" href="#province"
                               role="tab"
                               aria-controls="province" aria-selected="false">دسترسی به استان ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="order_status-tab" data-toggle="tab"
                               href="#order_status" role="tab"
                               aria-controls="order_status" aria-selected="false"> دسترسی به وضیعت سفارش ها </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="production_status-tab" data-toggle="tab"
                               href="#production_status" role="tab"
                               aria-controls="production_status" aria-selected="false"> دسترسی به رسته های کالا</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="packing_form_status-tab" data-toggle="tab"
                               href="#packing_form_status" role="tab"
                               aria-controls="packing_form_status" aria-selected="false"> دسترسی به بسته بندی ها</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="product_creation-tab" data-toggle="tab"
                               href="#product_creation" role="tab"
                               aria-controls="product_creation" aria-selected="false">  طراحی کالا</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="contractor-tab" data-toggle="tab"
                               href="#contractor" role="tab"
                               aria-controls="contractor" aria-selected="false"> دسترسی پیمانکاران</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="product_property-tab" data-toggle="tab"
                               href="#product_property" role="tab"
                               aria-controls="product_property" aria-selected="false"> دسترسی مشخصات کالا</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="script-tab" data-toggle="tab"
                               href="#script" role="tab"
                               aria-controls="script" aria-selected="false">دسترسی به دستیارهای هوشمند</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="cooperation_status-tab" data-toggle="tab"
                               href="#cooperation_status" role="tab"
                               aria-controls="cooperation_status" aria-selected="false"> دسترسی  به مشاهده لیست شاغلین</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="employment-status-tab" data-toggle="tab"
                               href="#employment-status" role="tab"
                               aria-controls="employment-status" aria-selected="false"> دسترسی  به مشاهده درخواست های همکاری</a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="edit-tab" data-toggle="tab" href="#edit" role="tab"
                               aria-controls="contact" aria-selected="false">ویرایش اطلاعات پست</a>
                        </li>


                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade " id="home" role="tabpanel" aria-labelledby="home-tab">
                            @include("hr.post._menu_permission")
                        </div>
                        <div class="tab-pane fade" id="line" role="tabpanel" aria-labelledby="line-tab">
                            @include("hr.post._line_permission")
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            @include("hr.post._warehouse_permission")
                        </div>
                        <div class="tab-pane fade" id="channel" role="tabpanel" aria-labelledby="channel-tab">
                            @include("hr.post._channel_permission")

                        </div>
                        <div class="tab-pane fade" id="province" role="tabpanel" aria-labelledby="province-tab">
                            @include("hr.post._province_permission")
                        </div>
                        <div class="tab-pane fade" id="order_status" role="tabpanel" aria-labelledby="order_status-tab">
                            @include("hr.post._order_status_permission")
                        </div>
                        <div class="tab-pane fade" id="production_status" role="tabpanel"
                             aria-labelledby="production_status-tab">
                            @include("hr.post._goods_kind_permission")
                        </div>
                        <div class="tab-pane fade" id="packing_form_status" role="tabpanel"
                             aria-labelledby="packing_form_status-tab">
                            @include("hr.post._packing_form")
                        </div>
                        <div class="tab-pane fade" id="product_creation" role="tabpanel"
                             aria-labelledby="product_creation-tab">
                            @include("hr.post._product_creation")
                        </div>
                        <div class="tab-pane fade" id="contractor" role="tabpanel"
                             aria-labelledby="contractor-tab">
                            @include("hr.post._contractor_permission")
                        </div>
                        <div class="tab-pane fade" id="product_property" role="tabpanel"
                             aria-labelledby="product_property-tab">
                            @include("hr.post._product_property_permission")
                        </div>
                        <div class="tab-pane fade" id="script" role="tabpanel"
                             aria-labelledby="script-tab">
                            @include("hr.post._script_permission")
                        </div>
                        <div class="tab-pane fade" id="cooperation_status" role="tabpanel"
                             aria-labelledby="cooperation_status-tab">
                            @include("hr.post._cooperation_permission")
                        </div>
                        <div class="tab-pane fade" id="employment-status" role="tabpanel"
                             aria-labelledby="employment-status-tab">
                            @include("hr.post._edit_post_employment")
                        </div>
                        <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                            @include("hr.post._edit_post_info")
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include("component.input._hidden",["id"=>"last_tab_open","value"=>isset($last_tab_open)?$last_tab_open:"home"])
    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>

        $('#form1').validate({
            rules: {
                caption: "required",
                role_id_auto: "required",
                chart_id_auto: "required",
                active_status_id_auto: "required",
                organization_category_id_auto: "required",
            }
        });

       $("#for_leave_required_to_replace_person").change(function (){

           $("#post_replace_list").css("display",$("#for_leave_required_to_replace_person").is(":checked")?"inline":"none")

       })
        $(".nav-link").click(function () {
            $("#last_tab_open").val($(this).attr('href').replace("#", ""));
        })

        let id = $("#last_tab_open").val();

        $("#" + id).addClass("show active");
        $("#" + id + "-tab").addClass(" active");

        $("#select_all_menu").change(function () {

            $(".myCheckBox_menu").prop('checked', $("#select_all_menu").is(':checked'));
        })
        $("#select_all_warehouse").change(function () {

            $(".myCheckBox_warehouse").prop('checked', $("#select_all_warehouse").is(':checked'));
        })
        $("#select_all_warehouse_operation").change(function () {

            $(".myCheckBox_warehouse_operation").prop('checked', $("#select_all_warehouse_operation").is(':checked'));
        })
        $("#select_all_product_request").change(function () {

            $(".myCheckBox_product_request").prop('checked', $("#select_all_product_request").is(':checked'));
        })
        $("#select_all_employment").change(function () {

            $(".myCheckBox_post_employment").prop('checked', $("#select_all_employment").is(':checked'));
        })

        $("#select_all_employment_operation").change(function () {

            $(".myCheckBox_employment_operation").prop('checked', $("#select_all_employment_operation").is(':checked'));
        })
        $("#select_all_chanel").change(function () {

            $(".myCheckBox_chanel").prop('checked', $("#select_all_chanel").is(':checked'));
        })
        $("#select_all_province").change(function () {

            $(".myCheckBox_province").prop('checked', $("#select_all_province").is(':checked'));
        })
        $("#select_all_order").change(function () {

            $(".myCheckBox_order").prop('checked', $("#select_all_order").is(':checked'));
        })
        $("#select_all_contractor").change(function () {

            $(".myCheckBox_contractor").prop('checked', $("#select_all_contractor").is(':checked'));
        })

        $("#select_all_cooperation").change(function () {

            $(".myCheckBox_cooperation").prop('checked', $("#select_all_cooperation").is(':checked'));
        })

        $("#select_all_goods_kind_property").change(function () {

            $(".myCheckBox_goods_kind_property").prop('checked', $("#select_all_goods_kind_property").is(':checked'));
            $(".myCheckBox_property_0").prop('checked', $("#select_all_goods_kind_property").is(':checked'));
        })

        $("#select_all_module").change(function () {

            $(".myCheckBox_module").prop('checked', $("#select_all_module").is(':checked'));
        })
        $("#select_all_module_operation").change(function () {

            $(".myCheckBox_module_operation").prop('checked', $("#select_all_module_operation").is(':checked'));
        })
        $("#select_all_product_creation").change(function () {

            $(".myCheckBox_product_creation").prop('checked', $("#select_all_product_creation").is(':checked'));
        })
        $("#select_all_product_creation_operation").change(function () {

            $(".myCheckBox_product_creation_operation").prop('checked', $("#select_all_product_creation_operation").is(':checked'));
        })
        $("#select_all_goods_kind").change(function () {

            $(".myCheckBox_goods_kind").prop('checked', $("#select_all_goods_kind").is(':checked'));
        })

        $("#select_all_cooperation_type").change(function () {

            $(".myCheckBox_cooperation_type").prop('checked', $("#select_all_cooperation_type").is(':checked'));
        })
        $("#select_all_cooperation_status").change(function () {

            $(".myCheckBox_cooperation_status").prop('checked', $("#select_all_cooperation_status").is(':checked'));
        })


        $("#select_all_script").change(function () {

            $(".myCheckBox_script").prop('checked', $("#select_all_script").is(':checked'));
        })

        $("#select_all_script_allow_edit").change(function () {

            $(".myCheckBox_script_allow_edit").prop('checked', $("#select_all_script_allow_edit").is(':checked'));
        })
        $("#select_all_script_allow_view").change(function () {

            $(".myCheckBox_script_allow_view").prop('checked', $("#select_all_script_allow_view").is(':checked'));
        })

        $("#select_all_personal_status").change(function () {

            $(".myCheckBox_personal_status").prop('checked', $("#select_all_personal_status").is(':checked'));
        })
    </script>
@endsection
