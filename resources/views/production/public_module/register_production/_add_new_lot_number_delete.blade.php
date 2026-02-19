<div class="alert alert-warning">

        لات
        {{$packing_form->items()->first()->lot_number->code??""}}
        برای کالا تعریف نشده است، در صورتی که از تعریف لات اطمینان دارید،
        فرم را تایید نمایید.

    <br/>

    <br/>
    <br/>
    <a href="#" id="add_new_lot_number_to_product" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-plus"></i>
            تعریف لات جدید
    </a>

</div>

<input type="hidden" id="add_new_lot" value="1">

<script>
    $("#add_new_lot_number_to_product").click(function () {
        add_new_lot_number();
    });

    function add_new_lot_number() {

        var carrier_code = $("#carrier_code").val();
        var lot_number_code = $("#lot_number_code").val();
        var degree_id = $("#degree_id").val();
        var final_amount = $("#final_amount").val();
        var packing_type_id = $("#packing_type_id").val();
        var action_type = $("#action_type").val();
        var sum_sub_amount = $("#sum_sub_amount").val();


        request = $.ajax({
            url: "{{url("api/fabric/special_production/add_new_packing/add_new_product_to_packing_api")}}",
            type: "post",
            data: {
                "machine_allocation_id": {{$machine_allocation->id}},
                "user_id": {{$user_id}},
                "packing_form_id": {{$packing_form->id??0}},
                "packing_type_id": packing_type_id,
                "lot_number_code": lot_number_code,
                "degree_id": degree_id,
                "final_amount": final_amount,
                "carrier_code": carrier_code,
                "action_type": action_type,
                "sum_sub_amount": sum_sub_amount,
                "add_new_lot":1
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_packing_item").html(response);
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
</script>
