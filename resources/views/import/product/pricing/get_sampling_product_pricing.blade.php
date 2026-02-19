@extends('layouts.admin._master')

@section('page_header_title',"دانلود فایل قیمت بروز مواد اولیه")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دانلود فایل قیمت بروز مواد اولیه</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('import.product.pricing.submit_sampling_product_pricing')}}" method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._select",[
                                "id"=>"goods_kind_id",
                                "label"=>"رسته کالایی",
                                "option"=>$goods_kind_option["items"],
                                ])

                        </div>
                        <div class="w-100"><br/></div>

                        <a href="{{route('import.product.pricing.index')}}" class="btn btn-outline-dark">بازگشت</a>
                        <input type="hidden" id="leading_false" value="1">
                        <button type="submit" class="btn btn-primary">دانلود فایل</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "goods_kind_id": "required",

            }
        });
    </script>
@endsection
