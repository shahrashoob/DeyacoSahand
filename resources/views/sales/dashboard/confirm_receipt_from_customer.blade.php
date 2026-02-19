@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش")
@section("content")
    <form id="form1" autocomplete="off"
          action="{{route("sales.dashboard.confirm_receipt_from_customer",[$order])}}"
          method="post"
          novalidate="novalidate"
          enctype="multipart/form-data"
    >
        @csrf

        <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        تصویر تاییدیه مشتری
                    </h5>
                </div>
                <div class="card-body">
                    <div class="col-md-6">
                        <div class="row">
                            @include("component.input._file_upload",["id"=>"image_file","label"=>"تصویر تایید توسط مشتری ( 300*300 پیکسل)","value"=>""])

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary"
                onclick="return confirm('آیا از تایید برگ خروج اطمینان دارید؟')">تایید سفارش از طرف
            مشتری

        </button>
        <a href="{{route("sales.dashboard.view_order",$order)}}" class="btn btn-dark">بازگشت</a>
    </div>

    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
