@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل خروج از انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> اطلاعات سفارش درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>

                    <a class="btn btn-outline-dark" href="{{route("wh.out.dashboard.view",[$product_request_form])}}">بازگشت</a>
                </div>


            </div>


        </div>

        @include("sales.dashboard._order_info")
        @php $product_request_permissions=$product_request_form->get_product_request_permissions(); @endphp

        @if($product_request_permissions)
            <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5> اطلاعات آدرس و تماس دریافت کننده</h5>
                </div>
                <div class="card-block overflow-auto">

                    <div class="row">

                        @include("component.input._lable",["id"=>"city_name", "lable"=>"کشور","value"=>$product_request_permissions->address->country->caption??"","class_col"=>"col-md-4"])
                        @include("component.input._lable",["id"=>"city_name", "lable"=>"استان","value"=>$product_request_permissions->address->province->caption??"","class_col"=>"col-md-4"])

                        @include("component.input._lable",["id"=>"city_name", "lable"=>"شهرستان","value"=>$product_request_permissions->address->city_name??"","class_col"=>"col-md-4"])

                        @include("component.input._lable",["id"=>"phone", "lable"=>"شماره ثابت / نمابر ","value"=>$product_request_permissions->address->phone??"","class_col"=>"col-md-4"])
                        @include("component.input._lable",["id"=>"mobile", "lable"=>"شماره همراه ","value"=> ($product_request_permissions->address->country->area_code??"").($order->address->mobile??""),"class_col"=>"col-md-4"])
                        @include("component.input._lable",["id"=>"postal_code", "lable"=>"کد پستی","value"=>$product_request_permissions->address->postal_code??"","class_col"=>"col-md-4"])
                        @include("component.input._lable",["id"=>"address", "lable"=>"نشانی ","value"=>$product_request_permissions->address->address??""])


                    </div>

                </div>
            </div>
            </div>
        @else
            @include("customer.group.buy._order_factor_customer_info")
        @endif
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


    </script>
@endsection


