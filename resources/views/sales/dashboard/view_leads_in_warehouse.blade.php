@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش "." سفارش:".$order->code())

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">

                    <div class="row">


                        <div class="col-md-12">
                            @include("warehouse.out.dashboard._prf_list")

                        </div>
                        <div class="col-md-12 center">
                            <br/>
                            <a href="{{url()->previous()}}" class="btn btn-outline-dark" type="button">
                                <i class="fa fa-arrow-right"></i> بازگشت
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section("modals")


    @include("component.modal.md-modal._modal_input",[
        "id"=>"18",
        "theme"=>"-danger",
        "title"=>" آیا از خاتمه یافته کردن درخواست اطمینان دارید؟ ",
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

@endsection

@section("scripts")
    @include("component.modal.md-modal._script")

    <script>
        $('#form-18').validate({
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

