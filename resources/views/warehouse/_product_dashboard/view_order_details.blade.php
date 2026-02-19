@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار محصول "." سفارش:".$order->code())

@section('content')

    <div class="row">


        @include("component.input._lable",["id"=>"","lable"=>" کد سفارش  ","value"=>$order->code(),"class_col"=>"col-md-3"])


        @include("component.input._lable",["id"=>"","lable"=>" کانال توزیع ",
                        "value"=>$order->customer->channelType->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                        "value"=>$order->customer->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" کد مرکز  ",
                        "value"=>$order->customer->code??"","class_col"=>"col-md-3"])


        @include("component.input._lable",["id"=>"","lable"=>" تاریخ درخواست   ",
                        "value"=>$order->order_date(),"class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" تعداد روز در انتظار    ",
        "value"=>$order->number_of_days_waiting()??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" مجوز خروج   ",
        "value"=>$order->exit_date()??"","class_col"=>"col-md-3"])





        @include("component.input._lable",["id"=>"","lable"=>" اولویت سفارش   ",
                        "value"=>$order->priority->caption??"","class_col"=>"col-md-3"])



        @include("component.input._lable",["id"=>"","lable"=>" وزن کل     ",
        "value"=>$order->total_weight??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" وزن بار ارسال نشده     ",
        "value"=>$order->total_weight_remaining??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" نسبت وزنی ارسال شده     ",
        "value"=>($order->total_weight_sent_raito??"" ). "%","class_col"=>"col-md-3"])



        @include("component.input._lable",["id"=>"","lable"=>" وزن بخش از بار موجود در انبار     ",
        "value"=>$order->total_weight_in_warehouse??"","class_col"=>"col-md-6"])

        @include("component.input._lable",["id"=>"","lable"=>" نسبت وزنی موجود در انبار     ",
        "value"=>($order->total_weight_in_warehouse_raito??"" ). "%","class_col"=>"col-md-6"])




        @include("component.input._lable",["id"=>"","lable"=>" وضعیت    ",
        "value"=>$order->status->caption??""])


        @include("component.input._lable",["id"=>"","lable"=>" شرح برگه    ",
        "value"=>$order->description_sheet->text??""])


    </div>

    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت فرم خروج از انبار</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.product.confirm_form_request",$order)}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        @include("warehouse.product_dashboard._order_list",["order"=>$order])


                        <a href="{{route("wh.product.list")}}" class="btn btn-outline-dark">بازگشت</a>


                        <button class="btn btn-outline-info dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print"></i>
                            پرینت درخواست

                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{route("wh.print.print_rfw_order",[$order,1])}}">با
                                توضیحات</a>
                            <a class="dropdown-item" href="{{route("wh.print.print_rfw_order",[$order,0])}}"> بدون
                                توضیحات</a>
                        </div>

                        @if($order->exit_status()==460000200)
                            <a href="{{route("wh.product.delivery_form_request",$order)}}" class="btn btn-info"> تحویل
                                به مقدار درخواست</a>

                            <button type="submit" class="btn btn-primary"> ثبت فرم</button>
                        @endif

                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> سوابق ثبت درخواست</h5>
                </div>
                <div class="card-block">

                    @include("warehouse.product_dashboard._form_list",["order"=>$order])


                </div>
            </div>
        </div>
    </div>
@endsection
