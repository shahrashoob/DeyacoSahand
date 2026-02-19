@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> فرم درخواست طراحی کالا {{$product_creation_process->getCode()}}</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-8">
                        @include("line_product_station.product.product_creation.basic_information_registration._info", [
                                     "url_product" => route("line_product_station.product.product_creation.product_show.index",$product_creation_process)
                                  ])


                        @include("line_product_station.product.product_creation.dashboard._action")


                    </div>
                    <div class="col-md-12 center">
                        <div class="row">
                            @if(isset($product_creation_process->image))
                                <div class="col-md-6 center">
                                    تصویر اولیه
                                    <br/>

                                    <div class=" " style="
    background-image: url('{{asset("upload/product_creation/".($product_creation_process->image->filename??''))}}');
  background-size: cover;
  width: 300px;
  height: 300px;
  max-width: 300px;
  max-height: 300px;
  border: 2px solid #0b0b0b;
  margin: auto;">


                                        {{--                                    <img style="width: 300px; display: inline"--}}
                                        {{--                                         src="{{asset("upload/product_creation/".($product_creation_process->image->filename??''))}}"--}}
                                        {{--                                            --}}{{--                                             onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"--}}
                                        {{--                                    />--}}
                                    </div>
                                </div>
                            @endif

                            @if(isset($product_creation_process->product->image))
                                <div class="col-md-6 center">
                                    تصویر نهایی
                                    <div style="
    background-image: url('{{asset("upload/product/".($product_creation_process->product->image->filename??''))}}');
  background-size: cover;
  width: 300px;
  height: 300px;
  max-width: 300px;
  max-height: 300px;
  border: 2px solid #0b0b0b;
  margin: auto;">


                                        {{--                                    <img style="width: 300px; display: inline"--}}
                                        {{--                                         src="{{asset("upload/product/".($product_creation_process->product->image->filename??''))}}"--}}
                                        {{--                                         onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"--}}
                                        {{--                                    />--}}
                                    </div>
                                </div>
                            @endif


                        </div>
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
