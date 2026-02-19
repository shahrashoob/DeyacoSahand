<script>
    @if(isset($action_type) && ($action_type == "register_print_back" || $action_type == "register_back" ))

    console.log("{{$action_type}}")
    window.location.href = ("{{route("production.public_module.register_production.index",[$machine_allocation])}}");

    @endif

    var packing_type_layer_count =@php echo json_encode($packing_type_layer_count); @endphp;


    $("#amount").focus();
    // $("#carrier_code").parent().css("display", "none");

    $("#add_new_product_item").click(function () {

        add_new_product(-1);
    });

    $("#register_back").click(function () {
        $("#action_type").val("register_back");
        add_new_product(1);
    });
    $("#register_continue").click(function () {
        $("#action_type").val("register_continue");
        add_new_product(1);
    });
    $("#register_print_continue").click(function () {
        $("#action_type").val("register_print_continue");
        add_new_product(1);
    });
    $("#register_print_back").click(function () {
        $("#action_type").val("register_print_back");
        add_new_product(1);
    });

    $("#create_new_pallet").click(function () {
        $("#action_type").val("create_new_pallet");
        add_new_product(-2);
    })

    $("#create_new_pallet_and_print").click(function () {
        $("#action_type").val("create_new_pallet_and_print");
        add_new_product(-2);
    });


    $("#create_new_pallet_and_print_a4").click(function () {
        $("#action_type").val("create_new_pallet_and_print_a4");
        add_new_product(-2);
    });

    $("#end_of_pallet_a4").click(function () {
        $("#action_type").val("end_of_pallet_a4");
        add_new_product(-2);
    });

    $("#end_of_pallet").click(function () {
        $("#action_type").val("end_of_pallet");
        add_new_product(-2);
    });

    is_complete();
    $("#is_complete_information").change(function () {
        is_complete();
    })

    function is_complete() {
        if ($("#is_complete_information").is(":checked")) {
            $("#gross_weight").parent().css("display", "none")
            @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)

            $("#col_unit_input").css("display", "none")
            $("#col_unit_caption").css("display", "none")
            $("#col_unit_amount").css("display", "none")

            @endif

        } else {
            $("#gross_weight").parent().css("display", "")

            @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)

            $("#col_unit_input").css("display", "")
            $("#col_unit_caption").css("display", "")
            $("#col_unit_amount").css("display", "")

            @endif
        }
    }

    function add_new_product(new_item_added) {

        var carrier_code = $("#carrier_code").val();
        var lot_number_code = $("#lot_number_code").val();
        var degree_id = $("#degree_id").val();
        var amount = $("#amount").val();
        var sub_amount = $("#sub_amount").val();
        var packing_type_id = $("#packing_type_id").val();
        var action_type = $("#action_type").val();
        var gross_weight = 0;
        var number_of_sub_packing = $("#number_of_sub_packing").val();
        var is_complete_information = 0;


        @if($machine_allocation->product->unit->weight_conversion_rate ==0)

            is_complete_information = $("#is_complete_information").is(":checked");
        gross_weight = $("#gross_weight").val();

        if (new_item_added == 1 && isNaN(parseFloat(gross_weight)) && !is_complete_information) {
            alert("لطفا وزن کل را وارد نمایید.")
            return 0;
        }
        if (new_item_added == 1 && parseFloat(gross_weight) <= 0 && !is_complete_information) {
            alert("وزن کل به درستی وارد نشده است.")
            return 0;
        }


        @else
            is_complete_information = false;
        @endif

                @if(isset($get_carrier_code) && $get_carrier_code)
        if (isNaN(parseInt(carrier_code))) {
            alert("لطفا شماره حامل بسته بندی را وارد نمایید.")
            return 0;
        }
        @endif

        if (new_item_added != -2 && isNaN(parseInt(packing_type_id))) {
            alert("لطفا نوع بسته بندی را انتخاب نمایید.")
            return 0;
        }
        if (new_item_added != -2 && lot_number_code == "") {
            alert("لطفا لات را وارد کنید.")
            return 0;
        }
        if (new_item_added != -2 && isNaN(parseInt(degree_id))) {
            alert("لطفا درجه را انتخاب نمایید.")
            return 0;
        }


        @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)
            is_complete_information = $("#is_complete_information").is(":checked");
        if (new_item_added != -2 && isNaN(parseFloat(sub_amount)) || parseFloat(sub_amount) <= 0) {
            alert("مقدار  به درستی وارد نشده است.");
            return 0;
        }
        if (new_item_added != -2 && (isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) && is_complete_information == false) {
            alert("مقدار  به درستی وارد نشده است.");
            return 0;
        }
        @else
        if (new_item_added != -2 && isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) {
            alert("مقدار  به درستی وارد نشده است.");
            return 0;
        }

        @endif


        if (packing_type_layer_count[$("#packing_type_id").val()] > 1 && (isNaN(parseInt(number_of_sub_packing)) || parseInt(number_of_sub_packing) <= 0)) {
            alert("لطفا تعداد بسته بندی فرعی را وارد کنید.")
            return 0;
        }

        spinner_run_for()
        request = $.ajax({
            url: "{{url("api/production/public_module/add_new_packing/add_new_product_to_packing_api")}}",
            type: "post",
            data: {
                "machine_allocation_id": {{$machine_allocation->id}},
                "user_id": {{$user_id}},
                "packing_form_id": {{$packing_form->id??0}},
                "packing_type_id": packing_type_id,
                "lot_number_code": lot_number_code,
                "degree_id": degree_id,
                "amount": amount,
                "sub_amount": sub_amount,
                "carrier_code": carrier_code,
                "get_carrier_code": {{isset($get_carrier_code)? ($get_carrier_code?1:0):0}},
                "action_type": action_type,
                "gross_weight": gross_weight,
                "pin1": $("#pin1").val(),
                "new_item_added": new_item_added,
                "number_of_sub_packing": number_of_sub_packing,
                "is_complete_information": (is_complete_information ? 1 : -1),
                "smart_object_id": {{$smart_object->id??0}},
                "default_lot_number_id": {{$default_lot_number->id??0}},
                "source_production_form_item_id": {{$source_production_form_item_id??0}},
                "keep_unit_value": $("#keep_unit_value").is(":checked") ? 1 : 0
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

    function update_number_of_sub_packing() {
        if (packing_type_layer_count[$("#packing_type_id").val()] > 1) {
            $("#number_of_sub_packing").css("display", "block")
            $("#number_of_sub_packing_caption").css("display", "block")
            $("#add_new_product_item").css("display", "none")
        } else {
            $("#number_of_sub_packing").css("display", "none")
            $("#number_of_sub_packing_caption").css("display", "none")
            $("#add_new_product_item").css("display", "block")
        }
    }

    $("#packing_type_id").change(function () {

        update_number_of_sub_packing();
    });

    update_number_of_sub_packing();
</script>

<script>

    // باسکول
    var id_gross_weight = "gross_weight";

    {{--                    اگر واحد اصلی وزنی است، آیکن باسکون کنار آن اضافه می شود.--}}
            @if($machine_allocation->production->product->unit->weight_conversion_rate==0)

            @else
        id_gross_weight = "amount";
    @endif

    var id_gross_weight_after_time_out = id_gross_weight;

    function set_id_for_smart_object(id) {
        id_gross_weight = id;
        $(".smart_object_icon ").removeClass("text-success");
        $("#" + id + "_smart_object").addClass("text-success");
    }

    function action_after_get_response(jsonObj) {
        if (typeof jsonObj["weight"] !== 'undefined') {
            $value = parseFloat(jsonObj["weight"]);
            if (id_gross_weight != "" && $value >= 0) {
                $("#" + id_gross_weight).val($value);
                set_id_for_smart_object("");


            }
            setTimeout(set_id_for_smart_object, 2000, id_gross_weight_after_time_out)
        } else {
            alert(jsonObj["error"])
        }
    }

</script>

@include("component.smart_object._get_value_from_smart_object")