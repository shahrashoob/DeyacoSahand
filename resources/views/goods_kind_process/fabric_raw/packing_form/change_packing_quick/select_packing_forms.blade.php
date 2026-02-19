@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>تغییر بسته بندی سریع</h5>
                </div>

                @include("component.input._hidden",["id"=>"packing_form_read_ids","value"=>json_encode($packing_form_read_ids)])


                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12 center">
                            <form id="form_api">

                                <h5>تعداد بسته بندی انتخاب شده <span class="badge badge-secondary"
                                                                     id="sum_of_confirm_packing_form">{{count($packing_form_read_ids)}}</span>
                                </h5>

                                <h5>آخرین بسته بندی انتخاب شده <span class="badge badge-secondary"
                                                                     id="last_packing_form">---</span>
                                </h5>
                                <h5> <span class="badge text-danger"
                                           id="packing_not_register"></span>
                                </h5>
                                <div class="col-md-12 col-sm-12 " style="margin-top: 10px">
                                    {{$allow_entry_with_pin?"کد پین":"کد بسته بندی"}}
                                </div>
                                <h5 class="text-danger">
                                    <span id="alert_danger_select"></span>
                                </h5>

                                <input id="packing_code" autofocus type="text"
                                       style="width: 140px;height: 33px;margin-bottom: 15px"
                                       value="">
                                <br/>
                                <button type="submit" class="btn btn-primary btn-sm" id="submit_packing_code"
                                        style="width: 140px">بررسی
                                    و
                                    ثبت
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-md-12 center" id="btn_submit">
            <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
               class="btn btn-outline-dark">بازگشت</a>
            <a type="submit" class="btn btn-primary submit_form"
               href="{{route("fabric_raw.packing_form.change_packing_quick.show_list",$packing_form)}}"> تایید و
                ادامه</a>
        </div>

    </div>

@endsection
@section("styles")

@endsection


@section("scripts")
    <script>

        $('#form_apdfi').validate({
            rules: {
                "caption": "required",
            }
        });
        var packing_list_json_data = {!! json_encode($packing_list_json_data) !!}; // لیست همه بسته بندی های موجود در انبار
        var packing_form_read_ids = {!! json_encode($packing_form_read_ids) !!}; // بسته بندی های خوانده شده
        var entry_with_pin = {{$allow_entry_with_pin?1:0}};
        sum_accepted = {{count($packing_form_read_ids)}};
        var snd_alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
        var snd_beep = new Audio("{{asset("assets/voice/beep.mp3")}}");
        $("#packing_code").focus();

        $("#form_api").submit(function () {

            $("#alert_danger_select").css("display", "none")
            var packing_code = $("#packing_code").val();

            if ($("#packing_code").val() == "") {
                return false;
            }

            add_packing(packing_code);
            return false;
        });


        function add_packing(packing_code) {

            $("#btn_submit").css("display", "none");
            message = ""
            packing_form_code_full = (entry_with_pin ? "" : "DCPK/") + packing_code;


            if (packing_form_code_full in packing_list_json_data) {
                packing_form_id = packing_list_json_data[packing_form_code_full].id;

                if (packing_form_read_ids.includes(packing_form_id+'') || packing_form_read_ids.includes(packing_form_id)) {
                    message = " بسته بندی " + packing_form_code_full + " قبلا انتخاب شده است."
                } else {
                    sum_accepted++;
                    packing_form_read_ids.push(packing_form_id);
                    $("#sum_of_confirm_packing_form").html(sum_accepted);
                    $("#last_packing_form").html(packing_form_code_full);
                    send_to_server();
                }
            } else {
                message = '{{$allow_entry_with_pin?"  کد پین ":"کد بسته بندی "}}' + packing_form_code_full + " جزء بسته بندی های مجاز نمی باشد."
            }

            if (message != "") {

                snd_alarm.play();
                $("#alert_danger_select").css("display", "")
                $("#alert_danger_select").text(message)
            } else {

                snd_beep.play();
            }

            $("#packing_code").val("")


        }


        function send_to_server() {



            request = $.ajax({
                url: "{{url( "api/other/add_packing_form_to_change_packing_quick_list" )}}",
                type: "post",
                data: {
                    "packing_form_read_ids": packing_form_read_ids,
                    "user_id": {{$user_id}},
                    "packing_form_id": {{$packing_form->id}},
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


        var run = setInterval("send_to_server()", 3000)

    </script>
@endsection

