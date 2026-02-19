@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت پیمانکاران"." - ".$machine_allocation->production->serial)

@section('content')



    <form id="form1" autocomplete="off"
          action="{{route("contractor.admin.confirmation_of_draft_form.confirm_exist_form",[$machine_allocation,$form])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            @include("warehouse.out.exit_form.qr._index")

            <div class="col-md-12 center">
             <a href="   {{route("contractor.admin.dashboard.log",$machine_allocation)}}" class="btn btn-outline-dark">
                 بازگشت
             </a>

                @if($form->status_id ==500000515)

                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا از تایید برگ خروج اطمینان دارید؟')">ثبت تایید پیش
                        نویس
                    </button>
                    <a class="btn btn-danger"
                       href="{{route("contractor.admin.confirmation_of_draft_form.reject_exist_form",[$machine_allocation,$form])}}"
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

    </script>
@endsection
