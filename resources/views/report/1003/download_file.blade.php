@extends('layouts.admin._master')
@section("page_header_title","گزارش 1003 - گردش کالا")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1003- گردش کالا</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1003.submit_download_file")}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="alert alert-warning">
                            با توجه به اینکه تعداد رکوردهای تراکنش انبار در بازه انتخاب شده بیش از حد مجاز است، امکان دریافت گزارش به صورت آنی
                            امکان پذیر نمی باشد، در صورت نیاز این فرم را تایید نموده و پس از 10 دقیقه فایل گزارش
                             را از همین صفحه دانلود کنید.
                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> تایید دریافت گزارش</button>

                        <input type="hidden" id="leading_false" value="1">
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
                "start_date_value": "required",
                "end_date_value": "required",
            }
        });
    </script>
@endsection
