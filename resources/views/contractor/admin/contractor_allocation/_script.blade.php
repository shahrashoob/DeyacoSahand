<script>
    packing_list_json_data = {!! json_encode($packing_list_json_data) !!};
    selected_packing_form_ids = {!! json_encode($selected_packing_ids) !!};
    packing_list_transport_item = {!! json_encode($packing_list_transport_item) !!};
    var entry_with_pin = {{$allow_entry_with_pin?1:0}};
    var sum_accepted = {{$count_select}};
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

            // add_transport_item(packing_code);
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

                selected_packing_form_ids.unshift(packing_form_id)

            }
        } else {
            message = '{{$allow_entry_with_pin?"کد پین":"کد بسته بندی "}}' + packing_form_code_full + " جزء بسته بندی های مجاز نمی باشد."
        }

        if (message != "") {

            snd_alarm.play();
            $("#alert_danger_select").css("display", "")
            $("#alert_danger_select").text(message)
        } else {
            sum_accepted++;
            $("#sum_of_confirm_packing_form").html(sum_accepted);
            $("#last_packing_form").html("DCPK/" + (packing_form_id + 1000));
            snd_beep.play();
        }


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
        onchange_input_sub_number(packing_form_id, $(this).val())
    })

    function onchange_input_sub_number_event() {
        packing_form_id = $(this).attr('id').replace("packing_form_id_", "")
        onchange_input_sub_number(packing_form_id, $(this).val())
    }


    $(".count_select_packing_form").keyup(function () {
        $("#btn_return_list").css("display", "none");
    })


    function select_packing_form(packing_form_id) {
        $("#btn_return_list").css("display", "none");
        request = $.ajax({
            url: "{{url("api/contractor/admin/machine_allocation/select_packing_form_api")}}",
            type: "post",
            data: {
                "production_id": '{{$production->id??null}}',
                "production_channel_type_id": '{{$production_channel_type->id??null}}',
                "packing_form_id": packing_form_id,
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#btn_return_list").css("display", "");
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            $("#btn_return_list").css("display", "");
            sum_accepted-=1;
            alert("خطایی رخ داده است، لطفا دوباره تلاش کنید.");
            $("#sum_of_confirm_packing_form").html(sum_accepted);
            snd_beep.play();
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    }
</script>