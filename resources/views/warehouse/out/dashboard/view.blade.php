@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")
@php $permission_confirm=$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout");@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">


                    <div class="row">
                        @include("warehouse.out.dashboard._small_info")
                    </div>
                    <div class="row" id="prf_list">
                        @include("warehouse.out.dashboard._prf_list_small")
                    </div>

                    <hr/>

                    <div style="text-align: center">
                        <a href="{{route("wh.out.dashboard.index")."?page=".$page}}"
                           class="btn btn-outline-dark">بازگشت</a>


                        @if(in_array( $product_request_form->status_id, [7005001,7005002,7005005,7005008,7005004]) && $permission_confirm && $allow_select_product)

                            <a class="btn btn-primary"
                               href="{{route("wh.out.delivery.index",[$product_request_form,0,$page])}}">
                                <i class=" fas fa-list"></i>
                                انتخاب کالا</a>

                            <a class="btn btn-primary"
                               href="{{route("wh.out.delivery.delivery_product_btn",[$product_request_form,0,$page])}}">
                                <i class="fas fa-outdent"></i>
                                تحویل کالا </a>

                        @endif

                        @if($post_user->checkButtonPermission("wh.transport.dashboard.index") && in_array( $product_request_form->status_id, [7005001,7005002,7005005,7005008,7005004])  )
                            <a href="{{route("wh.transport.dashboard.index",[$product_request_form,$page])}}"
                               class="btn btn-primary"
                            >
                                <i class="fas fa-truck"></i>
                                ثبت بسته بندی حمل و نقل
                            </a>
                        @endif
                        <a href="{{route("wh.out.check_packing_form.index",[$product_request_form,$page])}}"
                           class="btn btn-info"
                        >
                            <i class="fas fa-question"></i>
                            استعلام بسته بندی
                        </a>


                            <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-print"></i> پرینت درخواست
                            </button>
                            <div class="dropdown-menu center" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                                <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,3,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۵ </a>
                                <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,4,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۴ </a>
                                <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,3,"download"])}}" > دانلود A5 </a>
                                <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,4,"download"])}}" >  دانلود A4 </a>
                            </div>




                    </div>
                    @if(!$allow_select_product)
                        <div class="alert alert-warning">با توجه به تعداد برگ های خروج ثبت شده، امکان ثبت برگ خروج
                            جدید برای درخواست وجود ندارد
                        </div>
                    @endif



                </div>


            </div>

        </div>
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5><a href="{{route("wh.out.dashboard.view_order",[$product_request_form,$page])}}">
                            آدرس ارسال بار </a></h5>
                </div>
            </div>
        </div>
        <div id="exit_form_list" style="width: 100%">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h5><a href="#sdf" id="load_exit_form_list"> لیست فرم های ثبت شده برای تحویل کالا </a></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5><a href="{{route("wh.out.dashboard.log",[$product_request_form,$page])}}"> سابقه عملیات بر روی
                            درخواست </a></h5>
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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });

        // بارگذاری لیست فرم ها
        $("#load_exit_form_list").click(function () {
            request = $.ajax({
                url: "{{route("api.product_request_form.get_exit_form_list_api",[$product_request_form,$page])}}",
                type: "post",
                headers: {
                    'Authorization': `Bearer {{$bearer_token}}`,
                },
                data: {}
            });
            request.done(function (response, textStatus, jqXHR) {
                lock_submit = false;
                $("#exit_form_list").html(response)

            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                lock_submit = false;
                // Log the error to the console
                alert("اطلاعات نامعتبر است، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
                result_response = true;
            });
        })
        // بارگذاری آیتم های درخواست
        const myTimeout = setTimeout(prf_list_loader, 1500);

        function prf_list_loader() {
            request = $.ajax({
                url: "{{route("api.product_request_form.get_prf_list_api",[$product_request_form,$page])}}",
                type: "post",
                headers: {
                    'Authorization': `Bearer {{$bearer_token}}`,
                },
                data: {}
            });
            request.done(function (response, textStatus, jqXHR) {
                lock_submit = false;
                $("#prf_list").html(response)

            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                lock_submit = false;
                // Log the error to the console
                alert("اطلاعات ناممعتبر است، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
                result_response = true;
            });
        }

    </script>
@endsection


