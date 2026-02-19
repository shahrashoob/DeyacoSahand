@extends('layouts.admin._master')
@section("page_header_title","داشبورد نگهبانی ")

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("guarding.dashboard.confirm_exist_form",[$form])}}"
          method="post"
          novalidate="novalidate">
        @csrf


        @include("warehouse.out.exit_form.qr._index")

        <div class="col-md-12 center">
            <a href="{{route("guarding.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>

            @if ( $form->status_id == 500000530 && $post_user->checkButtonPermission( "guarding.dashboard.show_form" ))
                <button type="submit" class="btn btn-primary"
                        onclick="return confirm('آیا از تایید برگ خروج اطمینان دارید؟')">ثبت خروج بار
                    (نگهبانی)
                </button>

                <a href="{{route("guarding.dashboard.reject_exist_form",[$form])}}" type="submit" class="btn btn-danger"
                   onclick="return confirm('آیا از عدم تایید برگ خروج اطمینان دارید؟')">عدم تایید
                </a>
            @endif
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
                "caption": "required",
            }
        });
    </script>
@endsection
