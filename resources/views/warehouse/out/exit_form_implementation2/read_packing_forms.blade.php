@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات انبار</h5>
                </div>
                <div class="card-block">


                    <div class="row">


                        @include("component.input._lable",[
                            "label"=>"نوع تراکنش",
                            "value"=>$trans_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"trans_kind_id",
                            "value"=>$trans_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"طرف حساب",
                            "value"=>$opp_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"opp_kind_id",
                            "value"=>$opp_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"مرکز هزینه",
                            "value"=>$cost_center->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"cost_center_id",
                            "value"=>$cost_center->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"انبار",
                            "value"=>$warehouse->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"warehouse_id",
                            "value"=>$warehouse->id??""
                            ])


                        @include("component.input._lable",[
                            "label"=>"شرح تراکنش انبار",
                            "value"=>$description
                            ])
                        @include("component.input._hidden",[
                            "id"=>"description",
                            "value"=>$description
                            ])


                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات بسته بندی ها</h5>
                </div>

                @include("component.input._hidden",["id"=>"packing_form_read_ids","value"=>json_encode($packing_form_read_ids)])


                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12 center">
                            <form id="form_api">

                                <h5>تعداد بسته بندی ثبت شده <span class="badge badge-secondary"
                                                                  id="sum_of_confirm_packing_form">{{count($packing_form_read_ids)}}</span>
                                </h5>
                                <h5>تعداد بسته بندی حمل و نقل ثبت شده <span class="badge badge-secondary"
                                                                            id="transport_count">{{$transport_count}}</span>
                                </h5>
                                <h5>آخرین بسته بندی ثبت شده <span class="badge badge-secondary"
                                                                  id="last_packing_form">---</span>
                                </h5>
                                <h5> <span class="badge text-danger"
                                           id="packing_not_register"></span>
                                </h5>
                                <div class="col-md-12 col-sm-12 " style="margin-top: 10px">
                                    <input type="radio" id="type_of_reading_1" name="type_of_reading"
                                           value="packing_form_code" checked><label for="type_of_reading_1"> بر اساس کد
                                        بسته بندی </label>
                                    <input type="radio" id="type_of_reading_2" name="type_of_reading"
                                           value="transport_code"> <label for="type_of_reading_2"> بر اساس کد بسته بندی
                                        حمل و نقل </label>
                                </div>
                                <h5 class="text-danger">
                                    <span id="error_message"></span>
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
            <a class="btn btn-outline-dark" href="{{route("wh.out.exit_form_implementation2.index")}}">بارگشت</a>
            <a type="submit" class="btn btn-primary submit_form"
               href="{{route("wh.out.exit_form_implementation2.show_list")}}"> ثبت و ادامه</a>
        </div>

    </div>

@endsection
@section("styles")

@endsection


@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });
        all_packing_form_data = {!! json_encode($all_packing_form_data) !!}; // لیست همه بسته بندی های موجود در انبار
        packing_form_read_ids = {!! json_encode($packing_form_read_ids) !!}; // بسته بندی های خوانده شده
        all_transport_packing_form_ids = {!! json_encode($all_transport_packing_form_ids) !!}; // همه بسته بندی های حمل و نقل موجود در انبار
        sum_accepted = {{count($packing_form_read_ids)}};
        sum_accepted_transport = {{$transport_count}}
        $("#form_api").submit(function () {

            var packing_code = $("#packing_code").val();
            $("#packing_code").val("");
            $("#error_message").html("")
            packing_code = parseInt(packing_code) - 1000;

            if ($("#type_of_reading_1").is(":checked") == 1) {
                add_packing(parseInt(packing_code));
            } else {
                add_transport_item(packing_code);
            }

            $("#packing_code").focus();
            return false;
        });
        var alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
        var beep = new Audio("{{asset("assets/voice/beep.mp3")}}");

        function add_packing(packing_form_id, allow_alarm = true) {
            packing_added = 0;
            if (packing_form_id in all_packing_form_data) {


                if (packing_form_read_ids.includes(packing_form_id)) {
                    $("#error_message").html(" کد بسته " + ("DCPK/" + (packing_form_id + 1000)) + " بندی قبلا خوانده شده است.")
                    alarm.play();
                } else {
                    if (allow_alarm) {
                        beep.play();
                    }
                    sum_accepted++;
                    packing_added = 1;
                    packing_form_read_ids[packing_form_read_ids['length']] = packing_form_id;
                }


                $("#sum_of_confirm_packing_form").html(sum_accepted);
                $("#last_packing_form").html("DCPK/" + (packing_form_id + 1000));

            } else {
                $("#error_message").html("وضعیت بسته بندی جهت خروج از انبار نامعتبر است.")
                alarm.play();
            }
            $("#btn_submit").css("display", "none");
            {{--if (sum_accepted == {{count($packing_form_data)}}) {--}}
            {{--    send_to_server();--}}
            {{--    setTimeout(function () {--}}
            {{--        $("#btn_submit").css("display", "")--}}
            {{--        $("#panel_packing").css("display", "none")--}}
            {{--    }, 5000)--}}

            {{--}--}}

            $("#packing_form_read_ids").val(JSON.stringify(packing_form_read_ids));
            return packing_added;
        }

        function add_transport_item(transport_item_id) {
            k = 0;
            // خواندن بسته بندی حمل و نقل
            $.each(all_transport_packing_form_ids, function (index, value) {
                if (transport_item_id == value) {

                    k += add_packing(parseInt(index), false);
                }

            });
            if (k > 0) {
                beep.play();
                sum_accepted_transport++;
                $("#transport_count").html(sum_accepted_transport);
                $("#last_packing_form").html(
                    $("#last_packing_form").html() + " (DCLP/" + (transport_item_id + 1000) + ")"
                );

            } else {
                $("#error_message").html("کد بسته بندی حمل و نقل نامعتبر است.")
                alarm.play();
            }

        }

        function send_to_server() {

            request = $.ajax({
                url: "{{url( "api/warehouse/output/exit_form_implementation2/add_packing_api" )}}",
                type: "post",
                data: {
                    "packing_form_read_ids": packing_form_read_ids,
                    "user_id": {{$user_id}},
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                $("#btn_submit").css("display", "");
                // packing_form_data_server = JSON.parse(response);
                // text_error="";
                // for (let index in packing_form_data){
                //     if(packing_form_data[index] ==1  && packing_form_data_server[index]+0 !=1){
                //         text_error+="DCPK/"+(parseInt(index)+1000)+",";
                //     }
                // }
                // if(text_error!=""){
                //     $("#btn_submit").css("display", "none");
                //     text_error="بسته بندی های ذیل به دلیل قطعی شبکه ثبت نشده است، لطفا یکبار دیگر آنها را بخوانید"+"<br/>"+text_error;
                // }
                // $("#error_message").html(text_error);

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


        var run = setInterval("send_to_server()", 5000)

    </script>
@endsection

