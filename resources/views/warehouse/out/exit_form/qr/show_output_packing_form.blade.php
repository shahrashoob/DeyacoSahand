@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  خروج از انبار  ")

@section('content')

        <div class="row">
            @include("component.input._hidden",["id"=>"confirm_type","value"=>""])

            @include("warehouse.out.exit_form.qr._index",["show_packing_form"=>1])


            <div class="col-md-12 center">
                <a href="{{ url()->previous() }}"
                   class="btn btn-outline-dark">بازگشت</a>

        </div>


@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>

        function reject_form() {
            $("#confirm_type").val("reject");
            return confirm("آیا از عدم تایید فرم خروج اطمینان دارید؟");
        }

        function confirm_form(text) {
            $("#confirm_type").val("confirm");
            if (text == '')
                return confirm("آیا از  تایید فرم خروج اطمینان دارید؟");
            else
                return confirm(text);
        }
    </script>
@endsection


