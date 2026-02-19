@extends('layouts.admin._master',$permission_add_packing_form?["keypress_enable"=>1,"no_persian"=>1]:[])

@section("page_header_title","داشبورد انبار ")
@section("content")

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>دستور انبارگردانی شماره {{$warehouse_handling->id}}
                        - {{$warehouse_handling->warehouse->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="row">

                        @include("warehouse.warehouse_handling.dashboard._info")
                        <div class="w-100"><br></div>
                        <div class="w-25"></div>
                        <div class=" col-md-6  " id="table_status_list">
                            @include("warehouse.warehouse_handling.dashboard._table_status_list")
                        </div>

                        @if($permission_add_packing_form)
                            @include("warehouse.warehouse_handling.add_packing_form._add_packing_form")
                        @endif

                        <div class="col-md-12 " style="min-height: 80px;margin-top: 20px;">
                            <div class="{{$permission_add_packing_form?"hidden":""}} " id="btn_list">
                                @include("warehouse.warehouse_handling.dashboard._action")
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            @include("warehouse.warehouse_handling.dashboard._log")
        </div>


        @endsection
        @section("styles")

            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

            <style>
                .form-group {
                    margin: 0px !important;
                }

                .form-control {
                    width: 150px !important;
                    margin: auto;
                }

                .hidden {
                    display: none !important;
                }
            </style>

        @endsection
        @section("scripts")
            <script>
                var alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
                var beep = new Audio("{{asset("assets/voice/beep.mp3")}}");
                var packing_form_queue = [];
                var packing_form_reading_count = "{{$packing_form_count}}";
                var packing_form_data = {!! json_encode($packing_form_data) !!};
                var packing_form_reading = {!! json_encode($packing_form_reading_list) !!};
                var packing_form_gross_weight = [];
                var id_gross_weight = "gross_weight";
                var id_gross_weight_after_time_out = id_gross_weight;
                var entry_with_pin = "{{$entry_with_pin}}";

                function set_id_for_smart_object(id) {
                    id_gross_weight = id;
                    $(".smart_object_icon ").removeClass("text-success");
                    $("#" + id + "_smart_object").addClass("text-success");
                }

                $('#form1').validate({
                    rules: {
                        "caption": "required",
                    }
                });

                $("#form_api").submit(function () {

                    add_packing();

                    $("#packing_code").focus();
                    return false;
                });


                function add_packing() {
                    var packing_code = $("#packing_code").val();
                    var gross_weight = $("#gross_weight").val();

                    $("#packing_code").val("");
                    $("#error_message").html("")
                    $("#gross_weight").val("")
                    packing_form_code_full = (entry_with_pin==1 ? "" : "DCPK/") + packing_code;

                    if (packing_form_code_full in packing_form_data) {
                        packing_form_id = packing_form_data[packing_form_code_full].id;
                    }
                    else{
                        packing_form_id=0;
                    }

                    if (packing_form_id in packing_form_reading) {
                        alarm.play();
                        $("#error_message").text('{{$entry_with_pin?"کد پین":"کد بسته بندی "}} ' + packing_code + "  قبلا خوانده شده است.");

                        return false;
                    }


                    $("#btn_list").addClass("hidden");
                    packing_form_reading_count++;
                    // چک کردن اینکه بسته بندی در انبار است یا خیر
                    if (packing_form_id in packing_form_data) {
                        beep.play();
                        packing_form_queue.push(packing_form_id);
                        packing_form_reading[packing_form_id] = packing_form_id;
                        packing_form_gross_weight[packing_form_id] = gross_weight;


                    } else {

                        send_to_server(packing_form_code_full)
                    }


                    $("#sum_of_confirm_packing_form").text(packing_form_reading_count);
                    if(packing_form_id == 0 ){
                        $("#last_packing_form").text(packing_form_code_full);
                    }
                    else {
                        $("#last_packing_form").text(packing_form_data[packing_form_code_full].code);
                    }

                }


                @if($permission_add_packing_form)

                function send_to_server(packing_form_id_not_exists = 0) {
                    if (packing_form_id_not_exists == 0) {
                        packing_form_id_1 = packing_form_queue.shift();
                    } else {
                        packing_form_id_1 = packing_form_id_not_exists;
                    }

                    if (packing_form_id_1) {

                        request = $.ajax({
                            url: "{{url("api/warehouse/warehouse_handling/add_packing_form/add_packing_form_api")}}",
                            type: "post",
                            data: {
                                "packing_form_id_1": packing_form_id_1,
                                "warehouse_handling_id": {{$warehouse_handling->id}},
                                "gross_weight": packing_form_gross_weight[packing_form_id_1]
                            }
                        });
                        request.done(function (response, textStatus, jqXHR) {
                            response = jQuery.parseJSON(response);


                                if (response['result']) {
                                    if (packing_form_id in packing_form_reading) {
                                        // قبلا خوانده شده و نیاز به بوق تایید نمی باشد.
                                        //packing_form_reading_count--;
                                    }
                                    else{

                                        beep.play();

                                        packing_form_reading[packing_form_id_1] = packing_form_id_1;
                                    }
                                } else {
                                    alarm.play();
                                    // حذف از لیست خونده شده ها
                                   delete packing_form_reading[packing_form_id_1];
                                    packing_form_reading_count--;

                                    $("#sum_of_confirm_packing_form").text(packing_form_reading_count);
                                    $("#error_message").text(response['error']);
                                }



                            $("#table_status_list").html(response['html']);

                        });
                        request.fail(function (jqXHR, textStatus, errorThrown) {
                            // Log the error to the console
                            alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید."+"\n لطفا وضعیت اینترنت را چک کنید و در صورت عدم رفع مشکل با پشتیبانی تماس بگیرد.")
                            console.error(
                                "The following error occurred: " +
                                textStatus, errorThrown
                            );
                        });
                    } else {
                        if (packing_form_queue.length == 0) {
                            $("#btn_list").removeClass("hidden")
                        } else {
                            $("#btn_list").addClass("hidden")
                        }
                    }
                }

                setInterval(send_to_server, 3000);

                @endif

                function action_after_get_response(jsonObj) {
                    if (typeof jsonObj["weight"] !== 'undefined') {
                        $value = parseFloat(jsonObj["weight"]);
                        if (id_gross_weight != "" && $value >= 0) {
                            $("#" + id_gross_weight).val($value);
                            //set_id_for_smart_object("");


                        }
                        setTimeout(set_id_for_smart_object, 2000, id_gross_weight_after_time_out)
                    } else {
                        alert(jsonObj["error"])
                    }
                }

            </script>
    @include("component.smart_object._get_value_from_smart_object")
@endsection

