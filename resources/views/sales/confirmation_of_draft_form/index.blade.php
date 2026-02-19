@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش  ".$order->code())

@section('content')



    <form id="form1" autocomplete="off"
          action="{{route("sales.confirmation_of_draft_form.confirm_exist_form",[$order,$form])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            @include("warehouse.out.exit_form.qr._index")

            <div class="col-md-12 center">
                <a href="{{route("sales.dashboard.view_order",$order)}}"
                   class="btn btn-outline-dark">بازگشت</a>

                @if($form->status_id ==500000515)

                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا از تایید برگ خروج اطمینان دارید؟')">ثبت تایید پیش
                        نویس
                    </button>
                    <a class="btn btn-danger"
                       href="{{route("sales.confirmation_of_draft_form.reject_exist_form",[$order,$form])}}"
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
