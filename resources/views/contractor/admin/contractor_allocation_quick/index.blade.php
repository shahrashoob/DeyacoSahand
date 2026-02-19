@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تخصیص سریع کارت پیمان</h5>
                </div>

                <div class="card-block">

                    <form id="form1" action="{{route("contractor.admin.contractor_allocation_quick.submit")}}"
                          method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">


                            @include("component.input._select",["id"=>"contractor_id","label"=>"پیمانکار","option"=>$contractor_option["items"]])
                            @include("component.input._aotocomplet2",["id"=>"production_channel_type_id","label"=>"کانال پیمان پیمانکار","option"=>$production_channel_type_option["items"]])


                            <div class="col-md-12">

                                <br/>
                                <a href="{{route("contractor.admin.dashboard.index")}}"
                                   class="btn btn-outline-dark">بارگشت </a>
                                <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $("#contractor_id").change(function () {
            window.location.href = "{{route("contractor.admin.contractor_allocation_quick.index")}}/"+$("#contractor_id").val();
        })
        $('#form1').validate({
            rules: {
                "production_channel_type_id": "required",
                "contractor_id": "required",
            }
        });
    </script>
@endsection