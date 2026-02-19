@extends('layouts.admin._master')
@section("page_header_title","داشبورد ارسال بار ")

@section('content')


{{--    <form id="form1" autocomplete="off"--}}
{{--          action="{{route("utility.transport.loading.dashboard.confirm_exist_form",[$form])}}"--}}
{{--          method="post"--}}
{{--          novalidate="novalidate">--}}
{{--        @csrf--}}


        @include("warehouse.out.exit_form.qr._index")

        <div class="col-md-12 center">
            <a href="{{route("utility.transport.loading.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>

            @if (!$transport_form && $form->status_id == 500000525  && $post_user->checkButtonPermission( "utility.transport.loading.show_form" ))


                    <a href="{{route("utility.transport.loading.load_registration.create",$form)}}"
                       class="btn btn-primary">
                        ثبت ارسال (بارگیری)
                    </a>


            @endif
        </div>
{{--    </form>--}}


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

    </script>
@endsection
