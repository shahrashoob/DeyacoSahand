<script>
    function get_new_option(id, type, label, select_id, model, list = false, select_if_one_option = 0,default_value="",select_defult_if_count_is_one=0) {
// alert(
//             "type="+ type+" \n"+
//                 "id="+ id+" \n"+
//                 "label="+ label+" \n"+
//                 "select_id="+ select_id+" \n"+
//                 "model="+ model+" \n"
//         );

        if (select_if_one_option > 0 ) { // اگر تعداد گزینه ها برابر با این آیتم بود، گزینه را نمایش نمی دهد.
            $("#" + select_id).parent().css("display", "")
        }

        request = $.ajax({
            url: "{{url("api/option/get")}}",
            type: "post",
            data: {
                "type": type,
                "id": id,
                "label": label,
                "select_id": select_id,
                "model": model,
                "list": list,
                "default_value":default_value,
                "select_defult_if_count_is_one": select_defult_if_count_is_one
            }
        });
        request.done(function (response, textStatus, jqXHR) {


            $("#" + select_id).html(response);

            if (select_if_one_option > 0 && select_if_one_option == $("#" + select_id + " option").length) {
                $("#" + select_id).parent().css("display", "none")
                $("#" + select_id).val(  $("#" + select_id +' option:eq(1)').val())
            } else {
                $("#" + select_id).parent().css("display", "")
            }
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    }
</script>
