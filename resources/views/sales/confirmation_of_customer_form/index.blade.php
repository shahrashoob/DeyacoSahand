@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش  ".$order->code())

@section('content')

    <form id="form1" autocomplete="off"
          action="{{route("sales.confirmation_of_customer_form.confirm_exist_form",[$order,$form])}}"
          method="post"
          novalidate="novalidate"
          enctype="multipart/form-data"
    >
        @csrf

        <div class="row">

            @include("warehouse.out.exit_form.qr._index")

            @if($form->status_id ==500000500)
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
            @endif

            <div class="col-md-12 center">
                <a href="{{route("sales.dashboard.view_order",$order)}}"
                   class="btn btn-outline-dark">بازگشت</a>

                @if($form->status_id ==500000500)

                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا از تایید برگ خروج اطمینان دارید؟')">تایید دریافت محصول از طرف
                        مشتری

                    </button>
                    <a class="btn btn-danger"
                       href="{{route("sales.confirmation_of_customer_form.reject_exist_form",[$order,$form])}}"
                       onclick="return confirm('آیا از عدم تایید برگ خروج اطمینان دارید؟')">عدم تایید</a>
                @endif
            </div>
        </div>
    </form>

@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <style>
        .form-group {
            margin: 0px !important;
        }

        .form-control {
            width: 150px !important;
            margin: auto;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "image_file": "required",
            }
        });
    </script>
@endsection
