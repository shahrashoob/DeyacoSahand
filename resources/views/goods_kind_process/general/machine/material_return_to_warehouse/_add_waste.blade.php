<div class="row" style="border: 2px solid #0f0240">
    @include("goods_kind_process.general.machine.material_return_to_warehouse._waste_list")

    <div class="col-md-12  alert alert-info">
        <div class="">لطفا مقدار مواد اولیه ضایعات شده را مشخص نمایید</div>
    </div>

    @if(isset($message) && $message!="")
        <div class="col-md-12  alert alert-danger">
            <div class="">{{$message}}</div>
        </div>
    @endif

    <div class="col-md-12  ">

    </div>

    <div class="col-md-12">
        <div class="row">

            @include("component.input._select",[
                    "id"=>"product_id_waste",
                     "label"=>"مواد اولیه",
                        "required"=>1,
                       "option"=>$product_option["items"],
                        "val"=>"",
                         "text"=>"",
                        "class_col"=>"col-md-3 col-sm-12"
                        ])


            @include("component.input._select",[
                    "id"=>"waste_id",
                     "label"=>"نوع ضایعات",
                        "required"=>1,
                       "option"=>[],
                        "val"=>"",
                         "text"=>"",
                        "class_col"=>"col-md-3 col-sm-12"
                        ])
            @include("component.input._select",[
                                            "id"=>"packing_type_id_waste",
                                             "label"=>"نوع بسته بندی",
                                                "required"=>1,
                                               "option"=>[],
                                                "val"=>"",

                                                 "text"=>"",
                                                "class_col"=>"col-md-2 col-sm-12"
                                                ])

            @include("component.input._number",["id"=>"gross_weight_waste", "label"=>"وزن ناخالص","required"=>1,"is_smart_object"=>$smart_object??null,"value"=>"","class_col"=>"col-md-2 col-sm-12 gross_weight_input"])


            @include("component.input._number",["id"=>"sub_packing_form_number_waste", "label"=>"تعداد بسته بندی فرعی","required"=>1,"value"=>"","class_col"=>"col-md-2 col-sm-12"])

            @include("component.input._select",[
                                   "id"=>"degree_id_waste",
                                    "label"=>"درجه",
                                       "required"=>1,
                                      "option"=>[],
                                       "val"=>"",

                                        "text"=>"",
                                       "class_col"=>"col-md-1 col-sm-12"
                                       ])


            @foreach($lot_number_option as $key=> $option)
                @if(count($option)!=1)
                    @include("component.input._select",[
                          "id"=>"lot_number_id_waste_".$key,
                          "label"=>"لات",
                          "required"=>1,
                          "option"=>$option,
                          "val"=>"",
                          "text"=>"",
                          "class_col"=>"col-md-1 col-sm-12 lot_numbers_waste lot_number_id_waste_".$key
                            ])
                @else
                    @include("component.input._hidden",[
	                         "id"=>"lot_number_id_waste_".$key,
	                           "label"=>"لات",
                          "required"=>1
                          ,"value"=>$option[0]["value"]])

                @endif
            @endforeach


            <div class="col-md-2 center">

                <br/>
                <button type="button" class="btn btn-success" id="btn_add_change_grade_waste">
                    افزودن
                    جدید
                </button>
            </div>

        </div>
    </div>


</div>
<br/>

@include("component.script_function.get_new_option")
<script>
    var goods_kind_ids =@php echo json_encode($goods_kind_ids); @endphp;
    var has_sub_packing_type =@php echo json_encode($has_sub_packing_type); @endphp;
    $(".lot_numbers_waste").css("display", "none");
    $("#degree_id_waste").parent().css("display", "none");
    $("#sub_packing_form_number_waste").parent().css("display", "none");
    $("#product_id_waste").change(function () {
        get_new_option(
            $("#waste_id").val(),
            $("#product_id_waste").val(),
            "نوع ضایعات",
            "waste_id",
            "product_consumed_waste"
        )
        $(".lot_numbers").css("display", "none");
        $(".lot_number_id_" + $("#product_id").val()).css("display", "");
    });
    $("#waste_id").change(function () {
        get_new_option(
            $("#packing_type_id_waste").val(),
            $("#waste_id").val(),
            "نوع بسته بندی",
            "packing_type_id_waste",
            "packing_type_product",
            []
        )
        $(".lot_numbers_waste").css("display", "none");
        $(".lot_number_id_waste_" + $("#product_id_waste").val()).css("display", "");
    });
    $("#degree_id_waste").change(function () {


    });

    $("#packing_type_id_waste").change(function () {

        get_new_option(
            $("#waste_id").val(),
            goods_kind_ids[$("#waste_id").val()],
            "درجه",
            "degree_id_waste",
            "degree",
            [],
            2
        )

        $value = has_sub_packing_type[$("#packing_type_id_waste").val()]
        $("#sub_packing_form_number_waste").parent().css("display", $value ? "" : "none");
    });

    $("#btn_add_change_grade_waste").click(function () {

        if ($("#product_id_waste").val() == "") {
            alert("لطفا یک ماده اولیه را انتخاب نمایید.");
            return false;
        }
        if ($("#waste_id").val() == "") {
            alert("لطفا یک ضایعات را انتخاب نمایید.");
            return false;
        }
        if ($("#gross_weight_waste").val() == "") {
            alert("لطفا وزن ناخالص را وارد نمایید.");
            return false;
        }
        if ($("#degree_id_waste").val() == "") {
            alert("لطفا درجه را انتخاب نمایید.");
            return false;
        }

        if ($("#packing_type_id_waste").val() == "") {
            alert("لطفا نوع بسته بندی وارد نمایید.");
            return false;
        }
        if (has_sub_packing_type[$("#packing_type_id_waste").val()] && $("#sub_packing_form_number").val() == "") {
            alert("لطفا تعداد بسته بندی فرعی وارد نمایید.");
            return false;
        }

        request = $.ajax({
            url: "{{url("api/goods_kind_process/general/machine/add_change_grade_api")}}",
            type: "post",
            data: {
                "machine_id": {{$machine->id}},
                "request_type": "waste_product",
                "machine_allocation_modification_id": {{$modification_temp->id}},
                "product_id": $("#product_id_waste").val(),
                "waste_id": $("#waste_id").val(),
                "degree_id": $("#degree_id_waste").val(),
                "packing_type_id": $("#packing_type_id_waste").val(),
                @foreach($lot_number_option as $key=> $optoin)
                "lot_number_id_{{$key}}": $("#lot_number_id_{{$key}}").val(),
                @endforeach
                "gross_weight": $("#gross_weight_waste").val(),
                "sub_packing_form_number": $("#sub_packing_form_number_waste").val()
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_waste").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    })

</script>

