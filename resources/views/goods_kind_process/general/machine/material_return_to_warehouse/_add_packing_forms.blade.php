<div class="row">

    <div class="col-md-12  alert alert-info">
        <div class="">
            لطفا فقط بسته بندی های که بخشی از آنها مصرف شده یا مصرف نشده (کاملا سالم) است، را ثبت نمایید.
            <br/>
            تعداد کل بسته بندی ها:
            <a href="{{route($route_path."show_packing_form_by_product",[$modification_temp,$machine,$product->id,0])}}">

                {{count($master_packing_form_ids)}} عدد
            </a>
        </div>

    </div>

    @include("goods_kind_process.general.machine.material_return_to_warehouse._packing_form_list",["caption_list"=>"لیست بسته بندی های ثبت شده","allow_delete"=>true,"show_product"=>false])



    @if(isset($message) && $message!="")
        <div class="col-md-12  alert alert-danger">
            <div class="">{!! $message !!}</div>
        </div>
    @endif

    <div class="col-md-12  ">

    </div>

    <div class="col-md-12">
        <div class="row">


            @include("component.input._number",["id"=>"packing_form_code", "label"=>"کد بسته بندی","required"=>1,"value"=>"","class_col"=>"col-md-2 col-sm-12"])


            @include("component.input._select",["id"=>"consumed_status_id", "label"=>"وضعیت بسته بندی","required"=>1,"option"=>$modification_consumed_status_option["items"],"class_col"=>"col-md-2 col-sm-12 gross_weight_input"])
            <div class="w-100"><br/></div>
            @include("component.input._number",["id"=>"gross_weight", "label"=>"وزن ناخالص","required"=>1,"is_smart_object"=>$smart_object??null,"value"=>"","class_col"=>"col-md-2 col-sm-12 gross_weight_input"])


            @include("component.input._number",["id"=>"sub_packing_form_number", "label"=>"تعداد بسته بندی فرعی","required"=>1,"value"=>"","class_col"=>"col-md-2 col-sm-12"])


            <div class="w-100"></div>
            <div class="col-md-3 ">
                <button style="width: 100%" type="button" class="btn btn-primary" id="btn_add_packing_form">
                    ثبت بسته بندی
                </button>
            </div>
            <div class="col-md-3 ">
                <a style="width: 100%"
                   href="{{route($route_path."set_remainder_consumed",[$modification_temp,$machine,$product])}}"
                   class="btn btn-info">دیگر بسته بندی ها کاملا مصرف شده اند</a>
            </div>
            <div class="col-md-3 ">
                <a style="width: 100%" href="{{route($route_path."index",$machine)}}" class="btn btn-outline-dark">بارگشت</a>
            </div>

        </div>
    </div>


</div>
<br/>
<script>
    var master_packing_form_ids =@php echo json_encode($master_packing_form_ids); @endphp;
    $("#consumed_status_id").change(function () {

        if ($(this).val() == 6021101 || $(this).val() == 6021103) {
            $("#gross_weight,#sub_packing_form_number").parent().css("display", "none");
        } else {
            $("#gross_weight,#sub_packing_form_number").parent().css("display", "");
        }
    })


    $("#btn_add_packing_form").click(function () {
        packing_code = $("#packing_form_code").val();
        if (packing_code == "") {
            alert("کد بسته بندی نامعتبر است.");
            return false;
        }
        packing_code = parseInt(packing_code) - 1000;

        if (!(packing_code in master_packing_form_ids)) {
            alert("کد بسته بندی نامعتبر است.");
            return false;
        }
        if ($("#consumed_status_id").val() == "") {
            alert("لطفا نوع مصرف را انتخاب نمایید..");
            return false;
        }

        if ($("#consumed_status_id").val() == '6021102') {
            if ($("#gross_weight").val() == "") {
                alert("لطفا وزن ناخالص را وارد نمایید.");
                return false;
            }
            if ($("#sub_packing_form_number").val() == "") {
                alert("لطفا تعداد بسته بندی فرعی را انتخاب نمایید.");
                return false;
            }
        }


        request = $.ajax({
            url: "{{url("api/goods_kind_process/general/machine/add_change_grade_api")}}",
            type: "post",
            data: {
                "machine_id": {{$machine->id}},
                "request_type": "add_packing_form_product",
                "machine_allocation_modification_id": {{$modification_temp->id}},
                "product_id": {{$product->id}},
                "smart_object_id": {{$smart_object->id??0}},

                "consumed_status_id": $("#consumed_status_id").val(),
                "packing_form_code": $("#packing_form_code").val(),
                "gross_weight": $("#gross_weight").val(),
                "sub_packing_form_number": $("#sub_packing_form_number").val(),

                "route_path": '{{$route_path}}'
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_add_packing_form").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    })
    @if($smart_object)
    gross_weight_input_status(true)
    @endif
</script>

