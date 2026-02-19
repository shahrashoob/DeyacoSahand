
<script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
@include("component.script_function.get_new_option")
<script>
    var alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
    var beep = new Audio("{{asset("assets/voice/beep.mp3")}}");
    var packing_form_list_ids =@php echo json_encode($packing_form_list_ids); @endphp;

    $('#packing_form_code').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            $(".submit_packing_code").click()

        }
    });

    $(".submit_packing_code").click(function () {

        // if($("#amount").val() == "" || $("#gross_amount").val()=="" ||$("#sub_packing_form_number").val() ){
        //     alert("لطفا  مقدار خالص، مقدار ناخالص و تعداد بسته بندی را به صورت کامل تکمیل نمایید.");
        //     return false;
        // }
        request = $.ajax({
            url: "<?php echo e( url( "api/warehouse/output/exit_form_implementation/add_packing_api" ) ); ?>",
            type: "post",
            data: {
                "trans_kind_id": $("#trans_kind_id").val(),
                "opp_kind_id": $("#opp_kind_id").val(),
                "cost_center_id": $("#cost_center_id").val(),
                "description": $("#description").val(),
                "packing_form_list_ids": packing_form_list_ids,
                "packing_form_code": $("#packing_form_code").val(),
                "from_packing_form_code": $("#from_packing_form_code").val(),
                "to_packing_form_code": $("#to_packing_form_code").val(),
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#card-block").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });

        return false;
    });
</script>
@include("component._spinner",["id"=>".add_packing_row"])
