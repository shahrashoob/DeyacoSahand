@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> {{ $customer->shop_name }}

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("oil_change.home.store")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <table style="margin: auto ">
                                  <tr>
                                      <td colspan="5">
                                          @include("oil_change.home._input_pluck")
                                      </td>
                                  </tr>
                                    <tr>
                                        <td colspan="5" style="border: none">
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; border-bottom:3px #CDCDD4 solid; border-top:3px #CDCDD4 solid">
                                            <span style="font-weight: bold"> کیلومتر جاری</span>
                                        </td>
                                        <td colspan="4" style=" border-bottom:3px #CDCDD4 solid; border-top:3px #CDCDD4 solid">
                                            @include("component.input._number",["id"=>"current_km",'label'=>"   ","class_col"=>"col-md-12","autofocus"=>1])

                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12" style="text-align: center">
                                <br/>
                                <button type="submit" class="btn btn-primary btn-lg">استعلام و ثبت سرویس</button>
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
                current_km: { required: true},
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
