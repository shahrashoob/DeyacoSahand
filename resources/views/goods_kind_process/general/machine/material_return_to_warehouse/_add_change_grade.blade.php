<div class="row" style="border: 2px solid #0f0240">
    @include("goods_kind_process.general.machine.material_return_to_warehouse._change_grade_list")
    <div class="col-md-12  alert alert-info">
        <div class="">لطفا مقدار مواد اولیه تغییر درجه داده شده را مشخص نمایید</div>
    </div>
    @if(isset($message) && $message!="")
        <div class="col-md-12  alert alert-danger">
            <div class="">{{$message}}</div>
        </div>
    @endif

    <div class="col-md-12  ">

        {{--                                @foreach($material_list as $item)--}}
        {{--                                 --}}


        {{--                                @endforeach--}}
    </div>
    <div class="col-md-12">
        <div class="row">

            @include("component.input._select",[
                    "id"=>"product_id",
                     "label"=>"مواد اولیه",
                        "required"=>1,
                       "option"=>$product_option["items"],
                        "val"=>"",
                         "text"=>"",
                        "class_col"=>"col-md-3 col-sm-12"
                        ])


            @include("component.input._select",[
                                   "id"=>"packing_type_id",
                                    "label"=>"نوع بسته بندی",
                                       "required"=>1,
                                      "option"=>[],
                                       "val"=>"",

                                        "text"=>"",
                                       "class_col"=>"col-md-2 col-sm-12"
                                       ])


            @include("component.input._number",["id"=>"gross_weight", "label"=>"وزن ناخالص","required"=>1,"value"=>"","is_smart_object"=>$smart_object??null,"class_col"=>"col-md-2 col-sm-12 gross_weight_input"])




            @include("component.input._number",["id"=>"sub_packing_form_number", "label"=>"تعداد بسته بندی فرعی","required"=>1,"value"=>"","class_col"=>"col-md-2 col-sm-12"])


            @include("component.input._select",[
                                   "id"=>"degree_id",
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
                          "id"=>"lot_number_id_".$key,
                          "label"=>"لات",
                          "required"=>1,
                          "option"=>$option,
                          "val"=>"",
                          "text"=>"",
                          "class_col"=>"col-md-1 col-sm-12 lot_numbers_waste lot_number_id_waste_".$key
                            ])
                @else
                    @include("component.input._hidden",[
	                         "id"=>"lot_number_id_".$key,
	                           "label"=>"لات",
                          "required"=>1
                          ,"value"=>$option[0]["value"]])

                @endif

            @endforeach


            <div class="col-md-2 center">

                <br/>
                <button type="button" class="btn btn-success" id="btn_add_change_grade">
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
    $(".lot_numbers").css("display", "none");
    $("#degree_id").parent().css("display", "none");
    $("#sub_packing_form_number").parent().css("display", "none");
    $("#product_id").change(function () {
        get_new_option(
            $("#packing_type_id").val(),
            $("#product_id").val(),
            "نوع بسته بندی",
            "packing_type_id",
            "packing_type_product",
            []
        )
    });


    @if(isset($smart_object))
    gross_weight_input_status(true)
    @endif


    $("#packing_type_id").change(function () {

        get_new_option(
            $("#product_id").val(),
            goods_kind_ids[$("#product_id").val()],
            "درجه",
            "degree_id",
            "degree",
            [],
            2
        )
        $(".lot_numbers").css("display", "none");
        $(".lot_number_id_"+$("#product_id").val()).css("display", "");

        $value = has_sub_packing_type[$("#packing_type_id").val()];

        $("#sub_packing_form_number").parent().css("display", $value ? "" : "none");
    });

    $("#btn_add_change_grade").click(function () {

        if ($("#product_id").val() == "") {
            alert("لطفا یک ماده اولیه را انتخاب نمایید.");
            return false;
        }
        if ($("#gross_weight").val() == "") {
            alert("لطفا وزن ناخالص را وارد نمایید.");
            return false;
        }
        if ($("#degree_id").val() == "") {
            alert("لطفا درجه را انتخاب نمایید.");
            return false;
        }

        if ($("#packing_type_id").val() == "") {
            alert("لطفا نوع بسته بندی وارد نمایید.");
            return false;
        }
        if (has_sub_packing_type[$("#packing_type_id").val()]==1 &&  $("#sub_packing_form_number").val() == "") {
            alert("لطفا تعداد بسته بندی فرعی وارد نمایید.");
            return false;
        }

        request = $.ajax({
            url: "{{url("api/goods_kind_process/general/machine/add_change_grade_api")}}",
            type: "post",
            data: {
                "machine_id": {{$machine->id}},
                "request_type": "change_grade",
                "machine_allocation_modification_id": {{$modification_temp->id}},
                "product_id": $("#product_id").val(),
                "degree_id": $("#degree_id").val(),
                "packing_type_id": $("#packing_type_id").val(),
                @foreach($lot_number_option as $key=> $optoin)
                "lot_number_id_{{$key}}": $("#lot_number_id_{{$key}}").val(),
                @endforeach
                "gross_weight": $("#gross_weight").val(),
                "sub_packing_form_number": $("#sub_packing_form_number").val(),
                "smart_object_id": "{{$smart_object->id??null}}"
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_change_grade").html(response);
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

