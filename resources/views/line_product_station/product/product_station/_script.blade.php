@include("component.script_function.get_new_option")
<script>
    var station_operation = [];
    @foreach($station_operation_list as $key=>$value )
        station_operation[{{$key}}] = {{$value}};
    @endforeach
    var include_questions_about_start_and_end_of_operation = @php echo json_encode($include_questions_about_start_and_end_of_operation); @endphp;

    function change_start_and_end_operation(machine_type_id) {

        if (include_questions_about_start_and_end_of_operation[machine_type_id] == 0 || machine_type_id == "") {

            $("#is_need_allocation_at_first_1").parent().css("display", "none");
            $("#is_need_start_setup_1").parent().css("display", "none");
            $("#is_need_start_of_operation_1").parent().css("display", "none");
            $("#is_need_end_of_operation_1").parent().css("display", "none");
            $("#is_need_final_setting_1").parent().css("display", "none");
            $("#is_need_for_quality_control_1").parent().css("display", "none");

        } else {

            $("#is_need_allocation_at_first_1").parent().css("display", "");
            $("#is_need_start_setup_1").parent().css("display", "");
            $("#is_need_start_of_operation_1").parent().css("display", "");
            $("#is_need_end_of_operation_1").parent().css("display", "");
            $("#is_need_final_setting_1").parent().css("display", "");
            $("#is_need_for_quality_control_1").parent().css("display", "");
        }

        if ($("#material_unit_type_id_dependent_to_batch").val() == 4) {
            $("#material_packing_type_id_dependent_to_batch").parent().css("display", "");
        } else {
            $("#material_packing_type_id_dependent_to_batch").parent().css("display", "none");
        }
    }

    $("#line_id").change(function () {
        get_new_option(
            $("#station_id").val(),
            $("#line_id").val(),
            "ایستگاه کاری",
            "station_id",
            "station",
        )
    })
    $("#station_id").change(function () {
        get_new_option(
            $("#station_operation_id").val(),
            $("#station_id").val(),
            "عملیات در ایستگاه کاری",
            "station_operation_id",
            "station_operation"
        )
        get_new_option(
            $("#machine_type_id").val(),
            $("#station_id").val(),
            "گروه ماشین",
            "machine_type_id",
            "machine_type"
        )
    })
    $("#station_operation_id").change(function () {

        get_new_option(
            $("#station_sub_operation_id").val(),
            $("#station_operation_id").val(),
            "عملیات فرعی",
            "station_sub_operation_id",
            "station_sub_operation_bom",
        )
    })
    $("#machine_type_id").change(function () {

        get_new_option(
            $("#production_channel_type_id").val(),
            $("#machine_type_id").val(),
            "نوع کانال تولید",
            "production_channel_type_id",
            "machine_type_production_channel_type",
        )

        change_start_and_end_operation($("#machine_type_id").val());

    })
    change_start_and_end_operation($("#machine_type_id").val())


    $("#contractor_id").change(function () {
        get_new_option(
            $("#contractor_operation_id").val(),
            $("#contractor_id").val(),
            "عملیات پیمانکار",
            "contractor_operation_id",
            "contractor_operation",
        )
    })

    $("#material_unit_type_id_dependent_to_batch").change(function () {



        if ($("#material_unit_type_id_dependent_to_batch").val() == 4) {
            get_new_option(
                $("#material_packing_type_id_dependent_to_batch").val(),
                $("#material_id_dependent_to_batch").val(),
                "بسته بندی مجاز مواد اولیه وابسته به بچ",
                "material_packing_type_id_dependent_to_batch",
                "packing_type_product",
            )
            // واحد مواد اولیه وابسته به بچ از نوع بسته بندی است
            // بنابراین باید نوع بسته بندی مجاز فرعی را محاسبه کنیم.
            $("#material_packing_type_id_dependent_to_batch").parent().css("display", "");
        } else {
            $("#material_packing_type_id_dependent_to_batch").parent().css("display", "none");
        }

    })
    $('#form1').validate({
        rules: {
            "status_id_auto": "required",
            "line_id": "required",
            "station_id": "required",
            "station_operation_id": "required",
            "station_sub_operation_id": "required",
            "machine_type_id": "required",
            "min_of_production": {required: true, min: 1, max: 1000000000},
            "max_of_production": {required: true, min: 1, max: 1000000000},
            "efficiency": "required",
            "setup_time": "required",
            "priority_number": {required: true, min: 1},
            "practical_capacity_of_production": "required",
            "batch": "required",
            "batch_error_percentage": "required",
            "material_id_dependent_to_batch": "required",
            "material_unit_type_id_dependent_to_batch": "required",
            "material_packing_type_id_dependent_to_batch": "required",
            "extra_production": "required",
            "percent_of_extra_production": "required",
            "production_channel_id_auto": "required",
            "contractor_id": "required",
            "supplier_id": "required",
            "contractor_operation_id": "required",
            "delivery_time": "required",
            "receiving_time": "required",
            "setup_time_for_co_channel": "required",
            "setup_time_for_non_co_channel": "required",
            "setup_time_for_sub_operation": "required",
            "setup_time_for_final_setting": "required",
            "purchasing_capacity": "required",
            "product_code_in_contractor_system": "required",
            "service_code_in_contractor_system": "required",
            "product_caption_in_supplier_system": "required",
            "production_channel_type_id": "required",
            "customer_id": "required",

        }
    });
</script>
<script>
    $("#station_sub_operation_id").change(function () {
        station_sub_operation();

    })


    function station_sub_operation() {
        $("#batch").parent().css("display", "none");
        $("#batch_error_percentage").parent().css("display", "none");
        $("#material_id_dependent_to_batch").parent().css("display", "none");
        $("#material_unit_type_id_dependent_to_batch").parent().css("display", "none");

        $("#setup_time_for_sub_operation").parent().css("display", "none");
        $("#practical_capacity_of_production").parent().css("display", "none");

        if (station_operation[$("#station_operation_id").val()] == 1) {

            $("#batch").parent().css("display", "");
            $("#batch_error_percentage").parent().css("display", "");
            $("#material_id_dependent_to_batch").parent().css("display", "");
            $("#material_unit_type_id_dependent_to_batch").parent().css("display", "");

            $("#setup_time_for_sub_operation").parent().css("display", "");
        }

        if (station_operation[$("#station_operation_id").val()] == 2) {
            $("#practical_capacity_of_production").parent().css("display", "");
        }

        // برای تامین کنندگان بچ دریافت شود.
        if ('{{$product->supply_type_id}}' == 2) {
            $("#batch").parent().css("display", "");
            $("#batch_error_percentage").parent().css("display", "");
            $("#material_id_dependent_to_batch").parent().css("display", "");
            $("#material_unit_type_id_dependent_to_batch").parent().css("display", "");
        }
    }

    station_sub_operation();

    // update is_need_for_quality_control
    $("#is_need_for_quality_control_0,#is_need_for_quality_control_1").change(function () {
        update_is_need_for_quality_control();
    })
    update_is_need_for_quality_control();

    function update_is_need_for_quality_control() {
        if ($("#is_need_for_quality_control_1").is(":checked")) {
            $("#is_ability_to_choose_next_station_1").parent().show();
        } else {
            $("#is_ability_to_choose_next_station_1").parent().hide();
        }
    }

    // update is_need_start_setup
    $("#is_need_start_setup_0,#is_need_start_setup_1").change(function () {
        update_is_need_start_setup();
    })
    update_is_need_start_setup();

    function update_is_need_start_setup() {
        if ($("#is_need_start_setup_1").is(":checked")) {
            $("#setup_time_for_co_channel").parent().show();
            $("#setup_time_for_non_co_channel").parent().show();
        } else {
            $("#setup_time_for_co_channel").parent().hide();
            $("#setup_time_for_non_co_channel").parent().hide();
        }
    }

    // update آیا در ابتدا نیاز به تخصیص دارد؟
    $("#is_need_final_setting_0,#is_need_final_setting_1").change(function () {
        update_is_need_final_setting();
    })
    update_is_need_final_setting();

    function update_is_need_final_setting() {
        if ($("#is_need_final_setting_1").is(":checked")) {
            $("#setup_time_for_final_setting").parent().show();
        } else {
            $("#setup_time_for_final_setting").parent().hide();
        }
    }


</script>
