@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد ارسال بار ")

@section('content')



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> بار شماره: {{$transport->getCode()}} </h5>
                </div>

                <div class="card-block">

                    @include("utility.transport.public._transport_info")

                </div>
            </div>
        </div>

        @if($transport->status_id ==6010103 )

            <div class="col-sm-12" id="panel_packing">
                <div class="card">
                    <div class="card-header">
                        <h5>      گزارش بسته بندی های خوانده شده در بار </h5>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نام کالا</th>
                                    <th>کد کالا</th>
                                    <th>تعداد بسته بندی خوانده شده</th>
                                    <th>تعداد بسته بندی خوانده نشده</th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($packing_form_product_all as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$item->code}}

                                        </td>
                                        <td>
                                            {{$item->caption}}

                                        </td>
                                        <td>

                                            {{isset($packing_form_read[$item->id])?$packing_form_read[$item->id]:0}}
                                            بسته

                                        </td>
                                        <td>
                                            @if(isset($packing_form_no_read[$item->id]))
                                            {{$packing_form_no_read[$item->id]}}
                                            بسته
                                                <a title="مجوز مشاهده بارکدها" href="{{route("utility.special_license.panel.new_special_license.index",[14,$transport->id,$item->id,$packing_form_no_read[$item->id]])}}">
                                                    <i class="fa fa-unlock-alt"></i>
                                                </a>
                                            @endif
                                        </td>



                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>


        @endif
       <div class="col-md-12 center">
           <a href="{{route("utility.transport.loading.load_registration.show_transport",$transport)}}"
              class="btn btn-outline-dark">بازگشت</a>
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
    </style>
@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });
        packing_form_data = {!! json_encode($packing_form_data) !!};
        sum_accepted = {{array_sum($packing_form_data)}};
        $("#form_api").submit(function () {
            add_packing();

            $("#packing_code").focus();
            return false;
        });
        var alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
        var beep = new Audio("{{asset("assets/voice/beep.mp3")}}");

        function add_packing() {
            var packing_code = $("#packing_code").val();
            $("#packing_code").val("");
            $("#error_message").html("")
            packing_code = parseInt(packing_code) - 1000;
            if (packing_code in packing_form_data) {


                if (parseInt(packing_form_data[packing_code]) == 0) {
                    beep.play();
                    sum_accepted++;
                } else {
                    $("#error_message").html("این کد بسته بندی قبلا خوانده شده است.")
                    alarm.play();
                }

                packing_form_data[packing_code] = 1;
                $("#sum_of_confirm_packing_form").html(sum_accepted);
                $("#last_packing_form").html("DCPK/" + (packing_code + 1000));
            } else {
                $("#error_message").html("کد بسته بندی نامعتبر است.")
                alarm.play();
            }
            $("#btn_submit").css("display", "none");
            if (sum_accepted == {{count($packing_form_data)}}) {
                send_to_server();
                setTimeout(function () {
                    $("#btn_submit").css("display", "")
                    $("#panel_packing").css("display", "none")
                }, 5000)

            }
            $("#packing_form_data_last").val(JSON.stringify(packing_form_data));
        }

        function send_to_server() {

            request = $.ajax({
                url: "{{url("api/utility/transport/loading/load_registration/control_packing_api")}}",
                type: "post",
                data: {
                    "packing_form_data": JSON.stringify(packing_form_data),
                    "transport_id": {{$transport->id}},
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                $("#btn_submit").css("display", "");
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });


        }


        @if(count($packing_form_data) != array_sum($packing_form_data))
        var run = setInterval("send_to_server()", 5000)
        @endif
    </script>
@endsection
