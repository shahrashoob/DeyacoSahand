@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>  {{ $customer->shop_name }}

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("oil_change.home.store_service",[$car,$current_km])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <table style="margin: auto ">
                                    <tr>
                                        <td colspan="5">
                                            @include("oil_change.home._pluck")
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="padding:15px;font-size:18px;border: none; text-align: center">

                                            <span class="fa fa-user fa-3"></span>
                                            {{$car->firstname." ".$car->lastname}}
                                            ({{$car->mobile}})
                                        </td>
                                    </tr>

                                    @if($before_service)
                                        <tr style="font-size:14px;border-top: 3px solid #3c3c3c;margin: 3px">
                                            <td colspan="3">

                                                <span class="fa fa-clock fa-3"></span>
                                                تاریخ سرویس قبلی

                                            </td>
                                            <td colspan="2" style="text-align: left"
                                                style="font-size:16px;border: none;">
                                                {{$before_service->get_datetime()}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size:14px;border: none;">
                                                <span class="fa fa-car fa-3"></span>
                                                کیلومتر سرویس قبلی
                                            </td>
                                            <td colspan="2"
                                                style="text-align: left"> {{$before_service->current_km??"---"}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($before_service && $option=$before_service->before_service_option_by_caption(1))
                                        <tr style="font-size:14px;border: none;">
                                            <td colspan="3">
                                                <span class="fa  fa-filter fa-3"></span>
                                                روغن موتور قبلی:
                                            </td>
                                            <td colspan="2" style="text-align: left"> {{$option->caption}}</td>
                                        </tr>


                                    @endif

                                    <tr style="height:45px;border-top: 3px solid #3c3c3c;margin: 3px; margin-top: 5px">
                                        <td colspan="2">

                                        </td>
                                        <td style="text-align: center">

                                            کارکرد(km)
                                        </td>
                                        <td colspan="2" style="text-align: center; ">

                                            @if($customer->calculator_option)
                                                مبلغ (تومان)
                                            @else
                                                سرویس فعلی
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($service_option_types as $item)
                                        <tr>
                                            <td colspan="2">
                                                {{$item->caption}}
                                            </td>

                                            <td style="text-align: center">
                                                @if($before_service)
                                                    {{$before_service->before_service_option_by_km($item->id,$current_km)}}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td colspan="2" style="text-align: left;width: 70px;">
                                                @if($customer->calculator_option)
                                                    @include("component.input._number",["id"=>"option_".$item->id,"class_col"=>"","value"=>$service_value[$item->id]??"","autofocus"=>1])
                                                @else
                                                    @include("component.input._checkbox",["id"=>"option_".$item->id,"class_col"=>"col-md-12","autofocus"=>1,"checked"=>(isset($service_value[$item->id])?"checked":null)])
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5">

                                            <div class="form-group">
                                                <label> کیلومتر سرویس بعدی </label>
                                                <select id="next_km" name="next_km" class="form-control">
                                                    <option value="">لطفا انتخاب کنید</option>
                                                    <option value="1000">1000</option>
                                                    <option value="2000">2000</option>
                                                    <option value="3000">3000</option>
                                                    <option value="4000">4000</option>
                                                    <option value="5000">5000</option>
                                                    <option value="6000">6000</option>
                                                    <option value="7000">7000</option>
                                                    <option value="8000">8000</option>
                                                    <option value="9000">9000</option>
                                                </select>
                                            </div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("component.input._text",["id"=>"option_1_text",'label'=>" نام روغن موتور ","class_col"=>"","value"=>$option_1_text_value??""])

                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12" style="text-align: center">
                                <br/>
                                <br/>
                                <a href="{{route("oil_change.home.index")}}"
                                   class="btn btn-lg btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary btn-lg">ثبت سرویس جدید</button>
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

        .form-group {
            margin: 0;
            padding: 5px;
        }

        select.form-control:not([size]):not([multiple]) {
            height: auto;
        }

    </style>
@endsection

@section("scripts")
    <script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_fa2.js')}}"></script>

    <script>
        @if(!$option_1_text_value)
        $("#option_1_text").parent().parent().css("display", "none");
        @endif
        $("#switch-option_1").change(function () {

            if ($('#switch-option_1').is(":checked")) {
                $("#option_1_text").parent().parent().css("display", "block");
            } else {
                $("#option_1_text").parent().parent().css("display", "none");
            }

        });
        $("#option_1").change(function () {
            if ($('#option_1').val() != "") {
                $("#option_1_text").parent().parent().css("display", "block");
            } else {
                $("#option_1_text").parent().parent().css("display", "none");
            }

        });
        $('#form1').validate({
            rules: {
                next_km: "required",

            }
        });

    </script>
@endsection
