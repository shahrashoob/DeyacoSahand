@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان - سفارش  ".$order->code())

@section('content')
    <div class="row">

        <div class="col-sm-12">


            <form id="form1" autocomplete="off"
                  action="{{route($route_path."step1",[$order,$form])}}"
                  method="post"
                  novalidate="novalidate">
                @csrf


                @include("warehouse.out.exit_form.qr._index",["list_group_by_packing_form"=>"customer.group.reject_product._list_group_by_packing_form","show_packing_form"=>1])

                <div class="center">
                    <br/>
                    <br/>
                    <a class="btn btn-outline-dark"
                       href="{{route($dashboard_route,$order)}}">بارگشت</a>

                    <button id="reject_btn" type="button" class="btn btn-danger">ثبت مرجوعی</button>
                    <button id="submit_btn" type="submit" class="btn btn-primary hidden">تایید و ادامه</button>

                </div>
            </form>

        </div>


    </div>

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

        .hidden {
            display: none;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $("#check_all").click(function () {
            $(".check_box").prop('checked', $("#check_all").is(':checked'));
        })
        $('#form1').validate({
            rules: {
                "unit_id": "required",
            }
        });

        $("#reject_btn").click(function () {
            $(".reject_packing").removeClass("hidden");
            $("#submit_btn").removeClass("hidden");
            $(this).addClass("hidden");
            return false;
        })
        $("#submit_btn").click(function () {
            var total = 0;
            $('.reject_packing:checked').each(function () {
                total++;
            });
            if (total == 0) {
                alert('لطفا حداقل یک بسته جهت مرجوعی وارد نمایید.')
            }
            return total == 0 ? false : true;
        })
        $(".reject_packing").click(function () {

            if ($(this).is(':checked')) {

                $("#packing_is_safe_" + $(this).data("id")).removeClass("hidden");
            } else {
                $("#packing_is_safe_" + $(this).data("id")).addClass("hidden");
            }
        })

        $(".packing_is_safe").click(function () {

            if ($(this).is(':checked')) {

                $("#amount_remaining_" + $(this).data("id")).addClass("hidden");
            } else {
                $("#amount_remaining_" + $(this).data("id")).removeClass("hidden");
            }
        })

    </script>
@endsection
