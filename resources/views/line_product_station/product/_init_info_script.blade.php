
<script>
    function product_service_type() {
        if ($("#product_service_type_id").val() == 1) {
            $("#product_service_type_1").css("display", "")
            $("#product_service_type_2").css("display", "none")
        } else {
            $("#product_service_type_1").css("display", "none")
            $("#product_service_type_2").css("display", "")
        }
    }
    product_service_type()
    $("#product_service_type_id").change(function () {
        product_service_type()
    })



    function service_id_in_employer_system() {

        if ($("#supply_type_id1").val() == 3) {
            $("#service_id_in_employer_system1").parent().css("display", "")
        } else {
            $("#service_id_in_employer_system1").parent().css("display", "none")
        }
        if ($("#supply_type_id2").val() == 3) {
            $("#service_id_in_employer_system2").parent().css("display", "")
        } else {
            $("#service_id_in_employer_system2").parent().css("display", "none")
        }
    }
    service_id_in_employer_system();
    $("#supply_type_id1,#supply_type_id2").change(function (){
        service_id_in_employer_system();
    })

    // $("#goods_kind_id1").change(function () {
    //     get_new_option(
    //         $("#goods_type_id1").val(),
    //         $("#goods_kind_id1").val(),
    //         "نوع کالا",
    //         "goods_type_id1",
    //         "goods_type",
    //         [],
    //         0,
    //         "default_goods_type_ids"
    //     )
    // })
    //
    // $("#goods_type_id1").change(function () {
    //
    //     get_new_option(
    //         $("#unit_id1").val(),
    //         $("#goods_kind_id1").val(),
    //         "واحد اصلی",
    //         "unit_id1",
    //         "unit",
    //         [],
    //         0,
    //         "default_unit_ids"
    //     )
    // })
    //
    // $("#unit_id1").change(function () {
    //
    //     get_new_option(
    //         $("#sub_unit_id1").val(),
    //         $("#goods_kind_id1").val(),
    //         "واحد فرعی کالا",
    //         "sub_unit_id1",
    //         "unit",
    //         [],
    //         0,
    //         "default_sub_unit_ids"
    //     )
    // })
    //
    // $("#sub_unit_id1").change(function () {
    //
    //     get_new_option(
    //         $("#sub_unit2_id1").val(),
    //         $("#goods_kind_id1").val(),
    //         "واحد فرعی 2 کالا",
    //         "sub_unit2_id1",
    //         "unit",
    //         [],
    //         0,
    //         "default_sub_unit2_ids"
    //     )
    // })



</script>
