@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش "." سفارش:".$order->code())

@section('content')


    <div class="row">
        @if($post_user->checkButtonPermission("sales.show_order_info"))
            @include("sales.dashboard._special_panel")
            @include("sales.dashboard._order_info")
        @endif

        @if($post_user->checkButtonPermission("sales.show_order_finance_info"))
            @include("sales.dashboard._order_finance_info")
            @include("sales.dashboard._shipping_method_info")
        @endif

        @if($post_user->checkButtonPermission("sales.show_order_consumed_products"))
                @include("customer.group.buy._order_consumed_products")

                @include("sales.dashboard._order_packing_form")
        @endif

        @if($post_user->checkButtonPermission("sales.show_order_factor_products"))
            @include("customer.group.buy._order_factor_products")
        @endif

        @if($post_user->checkButtonPermission("sales._exist_form_list"))
            @include("sales.dashboard._exist_form_list")
        @endif

        @if($post_user->checkButtonPermission("sales.show_reject_product_form_list"))
                @include("customer.group.order._reject_product_form_list",[
                    "route_path"=>"sales.dashboard.view_reject_product_form",
                 ])

            @endif



{{--        @if($post_user->checkButtonPermission("sales.order_to_collection"))--}}
{{--            @include("sales.dashboard._loading_process_list")--}}
{{--        @endif--}}


        @if($post_user->checkButtonPermission("sales.show_production_processing"))
            @include("sales.dashboard._production_processing_list")
        @endif

        @if($post_user->checkButtonPermission("sales.show_correspondence"))
            @include("sales.dashboard._correspondence")
        @endif

        @if($post_user->checkButtonPermission("sales.show_order_factor_customer_info"))
            @include("customer.group.buy._order_factor_customer_info")
        @endif

        <div class="col-md-12">
            @include("sales.dashboard._confirm")

        </div>
    </div>
@endsection


@section("modals")


    @include("component.modal.md-modal._modal_input",[
        "id"=>"18",
        "theme"=>"-danger",
        "title"=>" آیا از کنسل کردن درخواست اطمینان دارید؟ ",
        "content"=>view("customer.group.order._form",["order"=>$order,"reject_type"=>"1"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("sales.dashboard.reject",[$order, 1])
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"19",
        "theme"=>"-danger",
        "title"=>"  آیا از عدم تایید و ارجاع به مرحله قبل اطمینان دارید؟",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"2"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"حذف",
        "url"=>route("sales.dashboard.reject",[$order, 2])
    ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"15",
        "theme"=>"-danger",
        "title"=>"بازگشت به کارتابل مشتری  ",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"3"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"حذف",
        "url"=>route("sales.dashboard.reject",[$order, 3])
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"16",
        "theme"=>"-danger",
        "title"=>" بازگشت به کارتابل کارشناس فروش  ",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"5"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"حذف",
        "url"=>route("sales.dashboard.reject",[$order, 5])
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"17",
        "theme"=>"",
        "title"=>"تایید درخواست و ارجاع به مرحله بعد",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"4"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"حذف",
        "url"=>route("sales.dashboard.confirm",[$order])
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"10",
        "theme"=>"",
        "title"=>"ثبت تخفیف خاص برای مشتری",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"100"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"تخفیف",
        "url"=>route("sales.dashboard.special_off",[$order])
    ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"13",
        "theme"=>"",
        "title"=>"تایید ثبت مجوز بارگیری",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"120"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"مجوز بارگیری",
        "url"=>route("sales.dashboard.loading_permission",[$order])
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"14",
         "theme"=>"-danger",
        "title"=>"خاتمه یافته کردن سفارش",
        "content"=>view("sales.dashboard._form",["order"=>$order,"reject_type"=>"130"])->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"تایید خاتمه یافته شدن",
        "url"=>route("sales.dashboard.terminate_order",[$order])
    ])

@endsection

@section("scripts")
    @include("component.modal.md-modal._script")

    <script>
        $('#form-18').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-14').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-15').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-19').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-10').validate({
            rules: {
                "special_off_price": {
                    required: true,
                    number: true,
                },
            }
        });

        $('#form-17').validate({
            rules: {}
        });

    </script>

@endsection

@section("styles")
    @include("component.modal.md-modal._style")
@endsection

