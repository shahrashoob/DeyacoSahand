@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> {{ $customer->shop_name }} </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("oil_change.home.service_confirm",[$car,$service])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <table style="margin: auto ">
                                    <tr>
                                        <td colspan="5"
                                            style="font-size:18px;border: none; text-align: center">

                                            <span class="fa fa-user fa-3"></span>
                                            {{$car->firstname." ".$car->lastname}}
                                            ({{$car->mobile}})
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("oil_change.home._pluck")

                                        </td>
                                    </tr>
                                    <tr><td style="padding-top: 15px"> </td></tr>
                                    <tr>
                                        @if($customer->calculator_option)
                                            <td colspan="3"
                                                style="text-align: center; padding:5px 0px; font-weight: bold; font-size: 18px">
                                                سرویس های انجام شده
                                            </td>
                                            <td colspan="2"
                                                style="text-align: center; padding:5px 0px; font-weight: bold; font-size: 18px">
                                                مبلغ (تومان)
                                            </td>
                                        @else
                                            <td colspan="5"
                                                style="text-align: center; padding:5px 0px; font-weight: bold; font-size: 18px">
                                                سرویس های انجام شده
                                            </td>
                                        @endif
                                    </tr>
                                    @php $sum_price=0;@endphp
                                    @foreach($service_option_types as $item)
                                        @if($option= $service->get_option_value($item->id))
                                            @if($customer->calculator_option)
                                                <tr>
                                                    <td colspan="3"
                                                        style="text-align: center;padding:5px 0px; font-weight: bold; font-size: 14px">
                                                        {{$item->caption}} {{$option->caption?"(".$option->caption.")":"" }}
                                                    </td>
                                                    <td colspan="2"
                                                        style="text-align: center;padding:5px 0px; font-weight: bold; font-size: 14px">
                                                        {{$option->price}}
                                                        @php $sum_price+=$option->price;@endphp
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="5"
                                                        style="text-align: center;padding:5px 0px; font-weight: bold; font-size: 14px">

                                                        <span class="fa fa-check fa-3 text-success"></span>
                                                        {{$item->caption}} {{$option->caption?"(".$option->caption.")":"" }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                    @endforeach
                                    @if($customer->calculator_option)
                                        <tr style="border-top: 3px solid #3c3c3c">
                                            <td colspan="3"
                                                style="text-align: center;padding:5px 0px; font-weight: bold; font-size: 14px">
                                                جمع کل
                                            </td>
                                            <td colspan="2"
                                                style="text-align: center;padding:5px 0px; font-weight: bold; font-size: 14px">
                                                {{$sum_price}}
                                            </td>
                                        </tr>
                                    @endif


                                    <tr>
                                        <td colspan="5" style="text-align: center">
                                            <br/>
                                            <br/>
                                            <div type="button" class="alert alert-primary " style="font-size: 20px">سرویس بعدی: {{$service->next_km}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12" style="text-align: center">
                                <br/>
                                <a href="{{route("oil_change.home.create_service",[$car,$service->current_km,$service])}}"
                                   class="btn btn-lg btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary btn-lg">ثبت نهایی</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <link rel="stylesheet" href="{{asset('oil_change/css/pluck.css')}}">
    <style>
        .custom-select, .form-control {
            padding: 4px 0px 3px !important;
            text-align: center;
        }

        select.form-control:not([size]):not([multiple]) {
            height: auto;
        }
    </style>
@endsection

@section("scripts")
    <script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_fa2.js')}}"></script>

    <script>

        $('#form1').validate({
            rules: {
                alphabet_id: "required",
                city_id: {minlength: 2, maxlength: 2, number: true, required: true},
                number1: {minlength: 3, maxlength: 3, number: true, required: true,},
                number2: {minlength: 2, maxlength: 2, number: true, required: true},
            }
        });
        $("#city_id").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 2) {
                $("#number1").focus();
                $("#number1").val("");
            }
        });
        $("#number1").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 3) {
                $("#alphabet_id").focus();
                $("#alphabet_id").val("");
            }
        });
        $("#alphabet_id").on('change', function (e) {
            var dInput = this.value;
            if (this.value.length >= 1) {
                $("#number2").focus();
                $("#number2").val("");
            }
        });
        $("#number2").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 2) {
                $("#current_km").focus();
            }
        });
    </script>
@endsection
