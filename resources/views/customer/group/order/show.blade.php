@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.order._order_info")
        @include("customer.group.buy._order_consumed_products")
        @include("sales.dashboard._order_packing_form")
        @include("customer.group.buy._order_factor_products")
        @include("customer.group.order._exist_form_list")
        @include("customer.group.order._reject_product_form_list",["route_path"=>"customer_group.order.view_reject_product_form"])

        @include("customer.group.order._order_finance_info")
        @include("customer.group.order._correspondence")
        @include("customer.group.buy._order_factor_customer_info")



    </div>

    <div class="w-100"></div>
    <br/>
    <br/>
    <div class="col-md-12" style="text-align: center">
        <a class="btn btn-dark "
           href="{{route("customer_group.order.index",$order)}}">بازگشت</a>


        @if($order->status_id== 304030)
            <a href="#!" class="btn btn-success  md-trigger md-setperspective" data-modal="modal-18" id="btn_submit"
            > <i class="fa fa-check"></i> تایید درخواست
            </a>

            <a href="{{route("customer_group.order.reject",$order)}}"
               onclick="return confirm('آیا از خاتمه یافته کردن درخواست اطمینان دارید؟')"
               class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-18" id="btn_edit"
            > <i class="fa fa-times"></i> خاتمه یافته کردن
            </a>
        @endif
        @if(in_array($order->status_id, [304010, 304030]))
            <a href="{{route("customer_group.buy.index",$order)}}"
               onclick="return confirm(' در صورت ویرایش فاکتور تخفیف ها  با توجه به شرایط روز اعمال می گردد.')"
               class="btn btn-primary " id="btn_edit"
            > <i class="fa fa-edit"></i> ویرایش درخواست
            </a>
        @endif

        <a href="{{route("customer_group.print.factor",$order)}}" class="btn btn-primary " id="btn_other"
        > <i class="fa fa-print"></i> پرینت پیش فاکتور
        </a>


    </div>


    </div>
    </div>

@endsection

@section("modals")


    @include("component.modal.md-modal._modal_input",[
        "id"=>"18",
        "theme"=>"",
        "title"=>"تایید درخواست",
        "content"=>view("customer.group.buy._form",["order"=>$order,"type"=>1])->render(),
        "btn_class"=>"btn-success",
        "btn_title"=>"حذف",
        "url"=>route("customer_group.order.confirm",[$order, 1])
    ])
@endsection

@section("styles")
    @include("component.modal.md-modal._style")
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
    </script>
@endsection
