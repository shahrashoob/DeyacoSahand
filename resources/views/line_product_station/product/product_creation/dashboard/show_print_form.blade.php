@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست طراحی کالا {{$product_creation_process->getCode()}}</h5>
                </div>
                <div class="card-block">

                    <div class="col-md-12 center">
                        <table class="content_form1">
                            <tr>
                                <td>
                                    فرم درخواست طراحی کالا
                                    <br/>
                                    کد درخوست: {{$product_creation_process->getCode()}}
                                    <br/>
                                    نام پشنهادی کالا
                                    {{$product_creation_process->caption}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br/>
                                    <br/>
                                    جناب آقای/سرکار خانم
                                    {{$product_creation_process->worker->fullName()}}
                                    لطفا پس از الصاق این فرم به نمونه کالا به آن را به آدرس زیر ارسال فرمایید.

                                    <br/>
                                    <br/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    فرستنده
                                </td>
                            </tr>
                            <tr>
                                <td>

                                    @include("component.input._lable",["id"=>"caption",'label'=>"آدرس فرستنده ","value"=>$customer?$customer->getDefaultAddress()->address:$product_creation_unit_address,"class_col"=>"col-sm-12"])
                                    @include("component.input._lable",["id"=>"caption",'label'=>"تلفن فرستنده ","value"=>$customer?$customer->getDefaultAddress()->phone:$product_creation_unit_phone,"class_col"=>"col-sm-12"])
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    گیرنده
                                </td>
                            </tr>

                            <tr>
                                <td>

                                    @include("component.input._lable",["id"=>"caption",'label'=>"آدرس گیرنده ","value"=>$product_creation_unit_address,"class_col"=>"col-sm-12"])
                                    @include("component.input._lable",["id"=>"caption",'label'=>"تلفن گیرنده ","value"=>$product_creation_unit_phone,"class_col"=>"col-sm-12"])
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-12 center">
                        <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <a class="btn btn-primary" href="{{route("line_product_station.product.product_creation.dashboard.download",$product_creation_process)}}"
                        >
                            <i class="fa fa-download"></i> دانلود فرم
                        </a>
                        <a class="btn btn-primary" href="{{route("line_product_station.product.product_creation.dashboard.print",$product_creation_process)}}"
                                onclick="return confirm(' آیا از چاپ فرم اطمینان دارید؟')"><i class="fa fa-print"></i> چاپ فرم
                        </a>

                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <style>
        .content_form1 td {
            border: 3px solid #000;
            font-size: 16px;
            font-weight: bold;
        }

        .content_form1 {
            text-align: center;
            width: 50% !important;
            margin: auto;
            margin-bottom: 20px;
        }

    </style>
@endsection

@section("scripts")

@endsection
