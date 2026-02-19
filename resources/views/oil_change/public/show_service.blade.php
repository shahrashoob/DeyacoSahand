@extends('oil_change.public._layout')

@section("content")

    <div class="card">
        <div class="row no-gutters">
            <div class="col-md-12">
                <h4 style="text-align: center; font-weight: bold;
                        margin-top: 50px;
                        margin-bottom: 10px; " class="mb-4">
                </h4>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="card-body text-center">
                    <div class="row justify-content-center">
                        <div class="col-sm-12 col-md-6">

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
                                    </td>
                                </tr>

                                @if($before_service)
                                    <tr style="font-size:14px;border-top: 3px solid #3c3c3c;margin: 3px">
                                        <td colspan="2" style="text-align: right">

                                            <span class="fa fa-clock fa-3"></span>
                                            تاریخ سرویس قبلی

                                        </td>
                                        <td colspan="3" style="text-align: left"
                                            style="font-size:16px;border: none;">
                                            {{$before_service->get_datetime()}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align:right;font-size:14px;border: none;">
                                            <span class="fa fa-car fa-3"></span>
                                            کیلومتر سرویس قبلی
                                        </td>
                                        <td colspan="3"
                                            style="text-align: left"> {{$before_service->current_km??"---"}}
                                        </td>
                                    </tr>
                                @endif
                                @if($option=$before_service->before_service_option_by_caption(1))
                                    <tr style="font-size:14px;border: none;">
                                        <td colspan="2" style="text-align: right">
                                            <span class="fa  fa-filter fa-3"></span>
                                            روغن موتور قبلی:
                                        </td>
                                        <td colspan="3" style="text-align: left"> {{$option->caption}}</td>
                                    </tr>


                                @endif
                                <tr style="height:40px;border-top: 3px solid #3c3c3c;margin: 3px; margin-top: 5px">
                                    <td colspan="3">

                                    </td>
                                    <td colspan="2" style="text-align: center">

                                        کارکرد(km)
                                    </td>

                                </tr>
                                @foreach($service_option_types as $item)
                                    <tr>
                                        <td colspan="3">
                                            {{$item->caption}}
                                        </td>

                                        <td colspan="2" style="text-align: center">
                                            {{$before_service->before_service_option_by_km($item->id,$car->current_km)}}
                                        </td>

                                    </tr>
                                @endforeach

                            </table>

                        </div>
                        <div class="col-sm-12">
                            <br/>
                            <br/>
                            <div class="alert alert-info" style="font-size: 18px">
                                شماره کیلومتر بعدی: {{$before_service->next_km}}
                            </div>
                            @if($before_service->next_km-$car->current_km > 0)
                                <div class="alert alert-warning" style="font-size: 18px; direction: rtl">
                                    مانده کیلومتر مجاز: {{$before_service->next_km-$car->current_km}}
                                </div>
                            @else
                                <div class="alert alert-danger" style="font-size: 18px; direction: rtl">
                                    خودرو شما بیش از {{$car->current_km-$before_service->next_km+1}} کیلومتر پیمایش غیر
                                    مجاز داشته است.
                                    <br/>
                                    جهت کاهش آسیب به خودرو در اسرع وقت جهت سرویس اقدام نمایید.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
    </div>

@endsection

@section("scripts")
    <script type="text/javascript">
        $('#form1').validate({
            rules: {

                current_km: {
                    required: true
                },

            }
        });
    </script>
@endsection
@section("styles")
    <link rel="stylesheet" href="{{asset('oil_change/css/pluck.css')}}">
@endsection
