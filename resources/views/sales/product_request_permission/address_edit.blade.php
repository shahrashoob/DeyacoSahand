@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")


    <form id="form1" style="display: inline"
          action="{{route("sales.product_request_permission.address_edit_submit",[$order,$address])}}" method="post"
          autocomplete="off">
        @csrf
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ویرایش آدرس ارسال بار</h5>

                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">

                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-block">
                                        <div class="row d-flex align-items-center">
                                            <div class="col-auto">

                                            </div>
                                            <div class="col">
                                                <div class="row">

                                                    <div class="col-md-2">
                                                        @include("component.input._aotocomplet2",[
                                                            "id"=>"country_id",
                                                            "label"=>"کشور ",
                                                            "option"=>$country_option["items"],
                                                            "val"=>$country_option["value"],
                                                            "text"=>$country_option["text"],
                                                            "class_col"=>""
                                                            ])
                                                    </div>

                                                    <div class="col-md-2">
                                                        @include("component.input._aotocomplet2",[
                                                            "id"=>"province_id",
                                                            "label"=>"استان ",
                                                            "option"=>$province_option["items"],
                                                            "val"=>$province_option["value"],
                                                            "text"=>$province_option["text"],
                                                            "class_col"=>""
                                                            ])
                                                    </div>

                                                    @include("component.input._text",["id"=>"city_name", "lable"=>"شهرستان","value"=>$address->city_name,"class_col"=>"col-md-2"])

                                                    @include("component.input._text",["id"=>"phone", "lable"=>"شماره ثابت / نمابر ","value"=>$address->phone,"class_col"=>"col-md-2"])

                                                    @include("component.input._text",["id"=>"mobile", "lable"=>"شماره همراه (بدون صفر) ","value"=>$address->mobile,"class_col"=>"col-md-2"])

                                                    @include("component.input._text",["id"=>"postal_code", "lable"=>"کد پستی","value"=>$address->postal_code,"class_col"=>"col-md-2"])
                                                    <div class="w-100"></div>
                                                    @include("component.input._textarea",["id"=>"address", "lable"=>"نشانی ","value"=>$address->address])
                                                    <div class="w-100"><br/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a class="btn btn-dark"
                                   href="{{route("customer_group.buy.shopping_cart",$order)}}">بازگشت</a>
                                <button type="submit" class="btn btn-primary" id="btn_other"
                                > ویرایش آدرس و ادامه
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </form>


@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                mobile: {required:true minlength: 10, maxlength: 10},
                phone: {required:true, minlength: 11, maxlength: 11},
                postal_code: {required:true, minlength: 10, maxlength: 10},
                country_id_auto:{required:true},
                province_id_auto:{required:true},
                city_name:{required:true},
                address:{required:true},
            }
        });


    </script>
@endsection
