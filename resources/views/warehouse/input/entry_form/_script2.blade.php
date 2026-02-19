<script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
@include("component.script_function.get_new_option")
<script>

    var goods_kind_product =@php echo json_encode($list_goods_kind_product); @endphp;
    var packing_form_rows =@php echo json_encode($packing_form_rows); @endphp;
    var first_packing_type_layers =@php echo json_encode($first_packing_type_layers); @endphp;

    function update_product() {
        get_new_option(
            $("#degree_id").val(),
            goods_kind_product[$("#product_id").val()],
            "درجه کالا",
            "degree_id",
            "degree",
            []
        )
        show_sub_packing_col($("#packing_type_id").val())
    }

    // $("#packing_type_id").change(function () {
    //
    //
    // })

    $("#degree_id").change(function () {

        get_new_option(
            $("#warehouse_id").val(),
            $("#degree_id").val(),
            "درجه کالا",
            "warehouse_id",
            "degree_warehouse",
            []
        )
    })


    function show_sub_packing_col(packing_type_id) {
        if (first_packing_type_layers[packing_type_id] ==null) {
            $(".class_sub_packing_number").css("display","none");
        }
        else{
            $(".class_sub_packing_number").css("display","");
        }
    }

    {{--function api_request() {--}}
    {{--    request = $.ajax({--}}
    {{--        url: "<?php echo e( url( "api/warehouse/input/entry_form/add_packing_api" ) ); ?>",--}}
    {{--        type: "post",--}}
    {{--        data: {--}}
    {{--            "product_id": $("#product_id").val(),--}}
    {{--            "packing_type_id": $("#packing_type_id").val(),--}}
    {{--            "degree_id": $("#degree_id").val(),--}}
    {{--            "lot_number_code": $("#lot_number_code").val(),--}}
    {{--            "warehouse_id": $("#warehouse_id").val(),--}}
    {{--            "trans_kind_id": $("#trans_kind_id").val(),--}}
    {{--            "opp_kind_id": $("#opp_kind_id").val(),--}}
    {{--            "cost_center_id": $("#cost_center_id").val(),--}}
    {{--            "description": $("#description").val(),--}}
    {{--            "packing_form_rows": packing_form_rows,--}}
    {{--            "weight": $("#weight").val(),--}}
    {{--            "gross_weight": $("#gross_weight").val(),--}}
    {{--            "unit_amount": $("#unit_amount").val(),--}}
    {{--            "sub_packing_form_number": $("#sub_packing_form_number").val(),--}}
    {{--            "enter_weight": $('#enter_weight:checked').val() == "on" ? 1 : 0,--}}
    {{--            "enter_gross_weight": $("#enter_gross_weight:checked").val() == "on" ? 1 : 0,--}}
    {{--            "enter_unit_amount": $("#enter_unit_amount:checked").val() == "on" ? 1 : 0,--}}
    {{--        }--}}
    {{--    });--}}
    {{--    request.done(function (response, textStatus, jqXHR) {--}}

    {{--        $("#card-block").html(response);--}}
    {{--    });--}}
    {{--    request.fail(function (jqXHR, textStatus, errorThrown) {--}}
    {{--        // Log the error to the console--}}
    {{--        console.error(--}}
    {{--            "The following error occurred: " +--}}
    {{--            textStatus, errorThrown--}}
    {{--        );--}}
    {{--    });--}}

    {{--    return false;--}}
    {{--}--}}

    {{--$("#enter_weight,#enter_gross_weight,#enter_unit_amount").change(function () {--}}

    {{--    api_request();--}}
    {{--})--}}
    // $("#add_packing_row").click(function () {
    //     amount = $('#enter_amount:checked').val() == "on" ? 1 : 0
    //     gross_amount = $('#enter_gross_amount:checked').val() == "on" ? 1 : 0
    //
    //     if (($("#amount").val() + 0 <= 0 && amount) || ($("#gross_amount").val() + 0 <= 0 && gross_amount) || $("#sub_packing_form_number").val() == "") {
    //         alert("لطفا  مقدار خالص/ مقدار ناخالص و تعداد بسته بندی را به صورت کامل تکمیل نمایید.");
    //         return false;
    //     }
    //     api_request();
    // });

</script>
@include("component._spinner",["id"=>".add_packing_row"])
