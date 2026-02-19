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
            url: "{{url( "api/warehouse/add_remove_packing_form_pallet_api" )}}",
            type: "post",
            data: {
                "packing_form_read_ids": packing_form_read_ids,
                "user_id": {{$user_id}},
                "pallet_id": {{$pallet->id}},
                "message_type_id": {{$message_type_id}}
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