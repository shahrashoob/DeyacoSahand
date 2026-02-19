@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت ارسال بار (ویژه دوره پیاده سازی)  </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.transport.dashboard.store_transport")}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"series",'label'=>"سری سفارش","value"=>$series??""])
                            @include("component.input._text",["id"=>"order_code",'label'=>"شماره سفارش","value"=>$order_code??""])
                            @include("component.input._text",["id"=>"customer_caption",'label'=>"نام مشتری","value"=>$customer_caption??""])
                            @include("component.input._hidden",["id"=>"new_customer",'label'=>"نام مشتری","value"=>isset($customer_caption)?1:0])

                            @if($customer_caption!=false)

                                <div class="col-md-12 alert alert-warning">
                                    مشتری  {{$customer_caption}}
                                    در سیستم تعریف نشده است، در صورت تایید فرم یک مشتری جدید تعریف می گردد.
                                </div>

                            @endif

                        </div>

                        <a href="{{route("utility.transport.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "start_datetime_value": "required",
                "end_datetime_value": "required",
                "currency_id_auto": "required",
                "currency_caption": "required"
            }
        });
    </script>
@endsection
