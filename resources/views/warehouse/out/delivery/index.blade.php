@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <a href="{{route("wh.out.delivery.packing_list_data_for_select",[$product_request_form,$product_id,$page,$dashboard_type??""])}}">
                            لیست بسته بندی های مجاز انتخاب برای
                            @if(isset($dashboard_type) && $dashboard_type=="customer")
                                {{$product_request_form->order->customer->caption}}
                            @else
                                درخواست
                                {{$product_request_form->getCode()}}
                            @endif
                        </a>
                    </h5>
                </div>


            </div>


        </div>
        <div class="col-md-12">
            @include("warehouse.out.delivery._select_packing_list")
        </div>
        <div class="col-md-12">
            @include("warehouse.out.dashboard._select_packing")
        </div>
        <div class="col-md-12 center" id="btn_return_list">

            @if(isset($dashboard_type) && $dashboard_type=="customer")
                <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
                   class="btn btn-primary">تایید و ادامه</a>

            @else
                <a href="{{route("wh.out.dashboard.view",[$product_request_form->id,$page])}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <a href="{{route("wh.out.dashboard.view",[$product_request_form->id,$page])}}"
                   class="btn btn-primary">تایید و ادامه</a>
            @endif


        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .count_select_packing_form,.amount_select_packing_form {
            width: 60px;
            text-align: center;
        }
    </style>
@endsection


@section("scripts")

    <script>
        var packing_list_json_data = {!! json_encode($packing_list_json_data) !!};
        var selected_packing_form_ids = {!! json_encode($selected_packing_ids,true) !!};
        var packing_list_transport_item = {!! json_encode($packing_list_transport_item) !!};
        var total_row = {{$selected_packing_list->total()}};
        var entry_with_pin = {{$allow_entry_with_pin?1:0}};
        var snd_alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
        var snd_beep = new Audio("{{asset("assets/voice/beep.mp3")}}");
        $("#packing_code").focus();

        $("#form_api").submit(function () {

            $("#alert_danger_select").css("display", "none")
            var packing_code = $("#packing_code").val();

            if ($("#packing_code").val() == "") {
                return false;
            }

            $("#packing_code").val("");

            if ($("#type_of_reading_1").is(":checked") == 1) {
                add_packing(packing_code);
            } else {

                add_transport_item(packing_code);
            }

            return false;
        });

        function add_packing(packing_code) {

            message = ""
            packing_form_code_full = (entry_with_pin ? "" : "DCPK/") + packing_code;

            if (packing_form_code_full in packing_list_json_data) {
                packing_form_id = packing_list_json_data[packing_form_code_full].id;

                if (selected_packing_form_ids.includes(packing_form_id)) {
                    message = " بسته بندی " + packing_form_code_full + " قبلا انتخاب شده است."
                } else {
                    select_packing_form(packing_form_id)


                    addRowToTable(packing_form_code_full, packing_form_id)
                    selected_packing_form_ids.unshift(packing_form_id)

                    // اضافه کردن دیگر بسته بندی های حمل نقل که در یک عدل هستند
                    if (packing_list_transport_item[packing_form_id]) {
                        for (const [p_id, t_code] of Object.entries(packing_list_transport_item)) {

                            if (t_code == packing_list_transport_item[packing_form_id]) {
                                packing_form_code_full_transport = getCodeOrPin1ById(parseInt(p_id));
                                selected_packing_form_ids.unshift(parseInt(p_id))
                                if (packing_form_code_full_transport != packing_form_code_full) {
                                    addRowToTable(packing_form_code_full_transport, p_id)
                                }
                            }
                        }
                    }
                }
            } else {
                message = '{{$allow_entry_with_pin?"کد پین":"کد بسته بندی "}}' + packing_form_code_full + " جزء بسته بندی های مجاز نمی باشد."
            }

            if (message != "") {

                snd_alarm.play();
                $("#alert_danger_select").css("display", "")
                $("#alert_danger_select").text(message)
            } else {

                snd_beep.play();
            }


        }

        function add_transport_item(transport_item_code) {

            message = ""
            transport_item_code_full = "DCLP/" + transport_item_code;

            count_of_packing_form = 0;
            for (const [p_id, t_code] of Object.entries(packing_list_transport_item)) {
                // alert(transport_item_code_full);
                if (t_code == transport_item_code_full) {
                    packing_form_code_full_transport = getCodeOrPin1ById(parseInt(p_id));

                    if (selected_packing_form_ids.includes(parseInt(p_id))) {
                        message = "کد بسته بندی حمل و نقل DCLP/" + transport_item_code + " قبلا انتخاب شده است.";
                    } else {
                        selected_packing_form_ids.unshift(parseInt(p_id))
                        addRowToTable(packing_form_code_full_transport, p_id)

                        count_of_packing_form++;
                    }

                    if (count_of_packing_form == 1) {
                        select_packing_form(p_id) // فقط یک بار درخواست به سرور ارسال می شود.

                    }

                }
            }

            if (count_of_packing_form == 0) {
                message = "کد بسته بندی حمل و نقل DCLP/" + transport_item_code + " جزء بسته بندی های  مجاز نمی باشد.";
            }

            if (message != "") {

                snd_alarm.play();
                $("#alert_danger_select").css("display", "")
                $("#alert_danger_select").text(message)
            } else {
                snd_beep.play();
            }
        }

        function addRowToTable(packing_code_full, packing_form_id) {
            var tbodyRef = document.getElementById('myTable').getElementsByTagName('tbody')[0];
            // Insert a row at the end of table
            var newRow = tbodyRef.insertRow();

            // Insert a row at the end of the row
            total_row++;
            var newCell = newRow.insertCell();
            var newText = document.createTextNode(total_row);
            newCell.appendChild(newText);
            // Insert a trash
            var a_href = document.createElement('a');
            var route = "{{route("wh.out.delivery.remove_packing_form_request",[$product_request_form])}}"
            a_href.href = route + "/" + packing_form_id;
            // Create the text node for anchor element.
            var i = document.createElement("i");
            i.className = "fa fa-trash";
            var span = document.createElement('span');
            span.className = "text-danger";
            span.appendChild(i);


            // Append the text node to anchor element.
            a_href.appendChild(span);

            newCell = newRow.insertCell();
            newCell.appendChild(a_href);
            // Insert a code


            if (entry_with_pin) {
                newCell = newRow.insertCell();
                newText = document.createTextNode("DCPK/" + (parseInt(packing_form_id) + 1000));
                newCell.appendChild(newText);
            }
            newCell = newRow.insertCell();
            newText = document.createTextNode(packing_code_full);
            newCell.appendChild(newText);
            // Insert amount
            if ({{$allow_select_partial_of_packing_in_output?1:0}} == 0) {
                newCell = newRow.insertCell();
                newText = document.createTextNode(packing_list_json_data[packing_code_full].final_amount);
                newCell.appendChild(newText);
            } else {
                var input = document.createElement('input');
                input.type = "number";
                input.id = "packing_form_id_amount_" + packing_list_json_data[packing_code_full].id;
                input.value = packing_list_json_data[packing_code_full].final_amount
                input.className = "amount_select_packing_form"
                input.onchange = onchange_input_amount_event;
                newCell = newRow.insertCell();
                newCell.appendChild(input);
            }

            // Insert a number of sub packing
            newCell = newRow.insertCell();
            newText = document.createTextNode(packing_list_json_data[packing_code_full].sub_packing_form_number);
            newCell.appendChild(newText);
            // Insert a input Count
            if (packing_list_json_data[packing_code_full].sub_packing_form_number + 0 <= 1) {
                newCell = newRow.insertCell();
                newText = document.createTextNode("کل بسته");
                newCell.appendChild(newText);
            } else {
                var input = document.createElement('input');
                input.type = "number";
                input.id = "packing_form_id_" + packing_list_json_data[packing_code_full].id;
                input.value = packing_list_json_data[packing_code_full].sub_packing_form_number
                input.className = "count_select_packing_form"
                input.onchange = onchange_input_sub_number_event;
                newCell = newRow.insertCell();
                newCell.appendChild(input);
            }

            // Insert transport
            newCell = newRow.insertCell();
            newText = document.createTextNode(packing_list_transport_item[packing_form_id] ? packing_list_transport_item[packing_form_id] : "");
            newCell.appendChild(newText);


        }

        // تابع برای پیدا کردن ,code,pin1 بر اساس id
        function getCodeOrPin1ById(targetId) {
            for (const key in packing_list_json_data) {
                if (packing_list_json_data[key].id === targetId) {
                    if (entry_with_pin) {
                        return packing_list_json_data[key].pin1 || null;
                    } else {
                        return packing_list_json_data[key].code || null;
                    }
                }
            }
            return '****'; // اگر پیدا نشد
        }

        $(".count_select_packing_form").change(function () {
            packing_form_id = $(this).attr('id').replace("packing_form_id_", "")
            onchange_input_sub_number(packing_form_id,)
        })
        $(".amount_select_packing_form").change(function () {
            packing_form_id = $(this).attr('id').replace("packing_form_id_amount_", "")

            onchange_input_sub_number(packing_form_id)
        })

        function onchange_input_amount_event() {
            packing_form_id = $(this).attr('id').replace("packing_form_id_", "")
            onchange_input_sub_number(packing_form_id)
        }

        function onchange_input_sub_number_event() {
            packing_form_id = $(this).attr('id').replace("packing_form_id_", "")
            onchange_input_sub_number(packing_form_id)
        }


        function onchange_input_amount_event() {
            packing_form_id = $(this).attr('id').replace("packing_form_id_amount_", "")
            onchange_input_sub_number(packing_form_id)
        }


        $(".count_select_packing_form, .amount_select_packing_form").keyup(function () {
            $("#btn_return_list").css("display", "none");
        })

        function onchange_input_sub_number(packing_form_id) {

            count_select_packing_form =
                count_select_packing_form = $("#packing_form_id_" + packing_form_id).val();


            amount_select_packing_form = $("#packing_form_id_amount_" + packing_form_id).val();

            request = $.ajax({
                url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                type: "post",
                data: {
                    "product_request_form_id": {{$product_request_form->id}},
                    "packing_form_id": packing_form_id,
                    "count_select_packing_form": count_select_packing_form,
                    "amount_select_packing_form": amount_select_packing_form,
                    "checked": 1,
                    "dashboard_type": '{{$dashboard_type}}',
                    "only_add": 1,
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#btn_return_list").css("display", "");
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                $("#btn_return_list").css("display", "");
                alert("خطایی رخ داده است، لطفا دوباره تلاش کنید.")
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }

        function select_packing_form(packing_form_id) {
            $("#btn_return_list").css("display", "none");
            request = $.ajax({
                url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                type: "post",
                data: {
                    "product_request_form_id": {{$product_request_form->id}},
                    "packing_form_id": packing_form_id,
                    "dashboard_type": '{{$dashboard_type}}',
                    "checked": 1,
                    "only_add": 1,
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#btn_return_list").css("display", "");
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                $("#btn_return_list").css("display", "");
                alert("خطایی رخ داده است، لطفا دوباره تلاش کنید.")
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }
    </script>

@endsection


