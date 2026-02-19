@extends('customer.tmp.layouts.admin._master')
@section("page_header_title",__("user panel"))
@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> {{__("product design form, :code",["code"=>$product_creation_process->getCode()])}}</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-8">
                        @include("customer.tmp.basic_information._info")

                    </div>
                    <div class="col-md-4" style="margin-bottom: 10px; text-align: center ">
                        <img style="max-width: 300px; max-height: 300px"
                             src="{{asset("upload/product_creation/".($product_creation_process->image->filename??''))}}"
                             onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"
                        />
                    </div>
                    <div class="col-md-12 center">

                        @include("customer.tmp.product_creation._action")

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
