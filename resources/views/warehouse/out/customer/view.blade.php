@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")
@php $permission_confirm=$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout");@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست درخواست های کالا از انبار    {{$customer->caption}} </h5>
                </div>

                <div class="card-block">



                    <div class="row" id="prf_list">
                        @include("warehouse.out.customer._product_request_form_item",["type"=>"out.customer"])
                    </div>

                    <hr/>

                    <div style="text-align: center">
                        <a href="{{route("wh.out.customer.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>


                        <a class="btn btn-primary"
                           href="{{route("wh.out.delivery.index",[$product_request_form,0,$product_request_form_item_by_product->currentPage(),"customer"])}}">
                            <i class=" fas fa-list"></i>
                            انتخاب کالا</a>

                        <a class="btn btn-primary"
                           href="{{route("wh.out.delivery.delivery_product_btn",[$product_request_form,$product_request_form_item_by_product->currentPage(),"customer"])}}">
                            <i class="fas fa-outdent"></i>
                            تحویل کالا </a>

                        @if($post_user->checkButtonPermission("wh.transport.dashboard.index"))
                            <a href="{{route("wh.transport.dashboard.index",[$product_request_form,$product_request_form_item_by_product->currentPage(),"customer"])}}"
                               class="btn btn-primary"
                            >
                                <i class="fas fa-truck"></i>
                                ثبت بسته بندی حمل و نقل
                            </a>
                        @endif

                        <a href="{{route("wh.out.check_packing_form.index",[$product_request_form,$product_request_form_item_by_product->currentPage(),"customer"])}}"
                           class="btn btn-info"
                        >
                            <i class="fas fa-question"></i>
                            استعلام بسته بندی
                        </a>
                        <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print"></i> پرینت درخواست ها
                        </button>
                        <div class="dropdown-menu center" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                            <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,3,"print","customer"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۵ </a>
                            <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,4,"print","customer"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۴ </a>
                            <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,3,"download","customer"])}}" > دانلود A5 </a>
                            <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$product_request_form,4,"download","customer"])}}" >  دانلود A4 </a>
                        </div>

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


        // بارگذاری آیتم های درخواست
        const myTimeout = setTimeout(prf_list_loader, 1500);

        {{--function prf_list_loader() {--}}
        {{--    request = $.ajax({--}}
        {{--        url: "{{route("api.product_request_form.get_prf_list_api",[$product_request_form,$page])}}",--}}
        {{--        type: "post",--}}
        {{--        headers: {--}}
        {{--            'Authorization': `Bearer {{$bearer_token}}`,--}}
        {{--        },--}}
        {{--        data: {}--}}
        {{--    });--}}
        {{--    request.done(function (response, textStatus, jqXHR) {--}}
        {{--        lock_submit = false;--}}
        {{--        $("#prf_list").html(response)--}}

        {{--    });--}}
        {{--    request.fail(function (jqXHR, textStatus, errorThrown) {--}}
        {{--        lock_submit = false;--}}
        {{--        // Log the error to the console--}}
        {{--        alert("اطلاعات ناممعتبر است، لطفا دوباره تلاش کنید.")--}}
        {{--        console.error(--}}
        {{--            "The following error occurred: " +--}}
        {{--            textStatus, errorThrown--}}
        {{--        );--}}
        {{--        result_response = true;--}}
        {{--    });--}}
        {{--}--}}


    </script>
@endsection


