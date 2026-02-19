@extends('layouts.admin._master')

@section('page_header_title',$product_creation_process?"داشبورد طراحی کالا":" کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن ردیف جدید برای {{$bom->caption}} - {{$bom->product_route->caption}}-
                        ({{$bom->product->fullCaption()}}) </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.product.bom_item.store",[$bom,$product_creation_process])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @switch($bom->product->supply_type_id)
                            @case(1)
                                <div class="row">


                                    <div class="w-100"></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"material_id",
                                            "label"=>" ماده اولیه   ",
                                            "option"=>$product_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"bill_of_material_dependency_type_id",
                                            "label"=>"نوع وابستگی ماده اولیه",
                                            "option"=>$bill_of_material_dependency_type_option["items"],
                                            "val"=>$bill_of_material_dependency_type_option["value"],
                                            "text"=>$bill_of_material_dependency_type_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_material_id",
                                            "label"=>"مقدار ماده اولیه به کدام کالا وابسته است",
                                            "option"=>$dependent_on_material_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>


                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_main_unit_type_id",
                                            "label"=>"واحد مرجع کالای غیر وابسته",
                                            "option"=>$unit_type_main_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_material_unit_type_id",
                                            "label"=>"واحد مرجع ماده اولیه",
                                            "option"=>$unit_type_material_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    @if(!$structureBOMItem)
{{--                                        تا زمانی که کالای ساختاری مشخص نشده است، از کاربر سوال می کند.--}}
                                        <div class="w-100"><br/></div>
                                        <div class="col-md-6">
                                            @include("component.input._select",[
                                                "id"=>"is_structure_product",
                                                "label"=>" آیا این ماده اولیه ساختاری است؟ ",
                                                "option"=>[["value"=>"","text"=>"لطفا یک مورد را انتخاب نمایید."], ["value"=>1,"text"=>"بله"],["value"=>"0","text"=>"خیر"]],
                                                "val"=>"",
                                                "text"=>"",
                                                "class_col"=>""
                                                ])
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-warning">
                                                در هر BOM باید فقط یک کالا به عنوان کالای ساختاری انتخاب شود.
                                                <br/>
                                                به عنوان مثال در پارچه تکمیل شده، پارچه خام کالای ساختاری می باشد.
                                            </div>
                                        </div>
                                    @endif
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"station_id",
                                            "label"=>" ایستگاه کاری ",
                                            "option"=>$station_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"station_operation_id",
                                            "label"=>" عملیات در ایستگاه کاری ",
                                            "option"=>$station_operation_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"station_sub_operation_id",
                                            "label"=>" عملیات فرعی  ",
                                            "option"=>$station_sub_operation_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"warehouse_id",
                                            "label"=>" انبار تحویل ماده اولیه   ",
                                            "option"=>$warehouse_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"productive_consume_warehouse_id",
                                            "label"=>" انبار مصرف کالای تولید   ",
                                            "option"=>$warehouse_consume_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"sampling_consume_warehouse_id",
                                            "label"=>" انبار مصرف کالای نمونه گیری   ",
                                            "option"=>$warehouse_consume_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>


                                    @include("component.input._text",["id"=>"amount",'label'=>"مقدار","value"=>""])


                                    @include("component.input._number",["id"=>"waste_prediction",'label'=>"درصد پیش بینی ضایعات","value"=>"0"])
                                    @include("component.input._number",["id"=>"consumption_correction_factor_prediction",'label'=>"پیش بینی ضریب اصلاح مصرف","value"=>"1"])
                                    @include("component.input._number",["id"=>"consumption_percent_of_production_channel",'label'=>"ضریب مصرف کانال تولید ","value"=>""])


                                    @include("component.input._number",["id"=>"input_line_code_from",'label'=>"  از خط ورودی","value"=>""])
                                    @include("component.input._number",["id"=>"input_line_code_to",'label'=>"تا خط ورودی","value"=>""])
                                    @include("component.input._number",["id"=>"number",'label'=>"تعداد ","value"=>"1"])
                                    @include("component.input._number",["id"=>"percent_of_use",'label'=>"درصد استفاده","value"=>"100"])

                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"bill_of_material_entering_type_id",
                                            "label"=>"روش تزریق ماده اولیه به ماشین",
                                            "option"=>$bill_of_material_entering_type_option["items"],
                                            "val"=>$bill_of_material_entering_type_option["value"],
                                            "text"=>$bill_of_material_entering_type_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>

                                </div>
                                @break
                            @case(3)

                                <div class="row">


                                    <div class="w-100"></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"material_id",
                                            "label"=>" ماده اولیه   ",
                                            "option"=>$product_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_material_id",
                                            "label"=>"مقدار ماده اولیه به کدام کالا وابسته است",
                                            "option"=>$dependent_on_material_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_main_unit_type_id",
                                            "label"=>"واحد مرجع کالای غیر وابسته",
                                            "option"=>$unit_type_main_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"dependent_on_material_unit_type_id",
                                            "label"=>"واحد مرجع ماده اولیه",
                                            "option"=>$unit_type_material_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"contractor_id",
                                            "label"=>" پیمانکار ",
                                            "option"=>$contractor_option["items"],
                                            "val"=>$contractor_option["value"],
                                            "text"=>$contractor_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>


                                    <div class="w-100"><br/></div>
                                    <div class="col-md-6">
                                        @include("component.input._select",[
                                            "id"=>"contractor_operation_id",
                                            "label"=>" عملیات پیمانکار ",
                                            "option"=>$contractor_operation_option["items"],
                                            "val"=>$contractor_operation_option["value"],
                                            "text"=>$contractor_operation_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"warehouse_id",
                                            "label"=>" انبار تحویل ماده اولیه   ",
                                            "option"=>$warehouse_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"productive_consume_warehouse_id",
                                            "label"=>" انبار مصرف کالای تولید   ",
                                            "option"=>$warehouse_consume_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100 warehouse_storage"><br/></div>
                                    <div class="col-md-6 warehouse_storage">
                                        @include("component.input._select",[
                                            "id"=>"sampling_consume_warehouse_id",
                                            "label"=>" انبار مصرف کالای نمونه گیری   ",
                                            "option"=>$warehouse_consume_option["items"],
                                            "val"=>"",
                                            "text"=>"",
                                            "class_col"=>""
                                            ])
                                    </div>


                                    @include("component.input._number",["id"=>"amount",'label'=>"مقدار","value"=>""])
                                    @include("component.input._number",["id"=>"number",'label'=>"تعداد ","value"=>"1"])
                                    @include("component.input._number",["id"=>"percent_of_use",'label'=>"درصد استفاده","value"=>"100"])

                                    @include("component.input._number",["id"=>"waste_prediction",'label'=>"درصد پیش بینی ضایعات","value"=>"0"])
                                    @include("component.input._number",["id"=>"consumption_correction_factor_prediction",'label'=>"پیش بینی ضریب اصلاح مصرف","value"=>"1"])

                                </div>
                                @break
                        @endswitch

                        @if($product_creation_process)
                            <a href="{{route("line_product_station.product.product_creation.bom.index",[$product_creation_process,$bom->product_route->code])}}"
                               class="btn btn-outline-dark">بازگشت</a>
                        @else
                            <a href="{{route("line_product_station.product.bom.index",[$bom->product,$bom->product_route->code])}}"
                               class="btn btn-outline-dark">بازگشت</a>
                        @endif


                        <button id="btn_submit" type="submit" class="btn btn-primary"> ذخیره BOM</button>


                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    @include("component.script_function.get_new_option")
    <script>
        var consume_goods_kind_ids = @php echo json_encode($consume_goods_kind_ids); @endphp;
        var material_warehouse_storage_type = @php echo json_encode($material_warehouse_storage_type); @endphp;
        var product_ids_no_warehouse = @php echo json_encode($product_ids_no_warehouse); @endphp;

        $("#material_id").change(function () {
            $("#station_sub_operation_id").val("");

            display = '';
            if (material_warehouse_storage_type[$(this).val()] == 1) {
                display = 'none';
            }
            $("#dependent_on_material_id").css("display", display);

            if(product_ids_no_warehouse.includes(parseInt($(this).val()))){

                $("#warehouse_id,#productive_consume_warehouse_id,#sampling_consume_warehouse_id").parent().css("display", "none");
            }
            else{
                $("#warehouse_id,#productive_consume_warehouse_id,#sampling_consume_warehouse_id").parent().css("display", "");
            }

        })
        $("#bill_of_material_dependency_type_id").change(function () {

            if ($(this).val() == 2) {
                display = 'none';
            }
            else{
                display="";
            }
            $("#dependent_on_material_id").parent().css("display", display);

        })
        $("#station_id").change(function () {
            get_new_option(
                $("#station_operation_id").val(),
                $("#station_id").val(),
                "عملیات در ایستگاه کاری",
                "station_operation_id",
                "station_operation_bom",
                    {{$bom->product_route->id}}
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
        $("#station_sub_operation_id,#contractor_operation_id").change(function () {
            if(!product_ids_no_warehouse.includes(parseInt($("#material_id").val()))) {
                get_new_option(
                    $("#warehouse_id").val(),
                    0,
                    "انبار تحویل کالا",
                    "warehouse_id",
                    "warehouse_delivery",
                    "[{{$bom->product->goods_kind_id}}," + consume_goods_kind_ids[$("#material_id").val()] + "]"
                )
            }
        })
        $("#warehouse_id").change(function () {
            get_new_option(
                $("#productive_consume_warehouse_id").val(),
                "default_productive_consume_warehouse_ids_",
                "انبار مصرف کالای تولیدی",
                "productive_consume_warehouse_id",
                "warehouse_consume",
                "[{{$bom->product->goods_kind_id}}," + consume_goods_kind_ids[$("#material_id").val()] + "]"
            )
        })
        $("#productive_consume_warehouse_id").change(function () {
            get_new_option(
                $("#sampling_consume_warehouse_id").val(),
                "default_sampling_consume_warehouse_ids_",
                "انبار مصرف کالای تولیدی",
                "sampling_consume_warehouse_id",
                "warehouse_consume",
                "[{{$bom->product->goods_kind_id}}," + consume_goods_kind_ids[$("#material_id").val()] + "]"
            )
        })

        $("#contractor_id").change(function () {
            get_new_option(
                $("#contractor_operation_id").val(),
                $("#contractor_id").val(),
                "عملیات پیمانکار",
                "contractor_operation_id",
                "contractor_operation",
            );


        })
        $("#btn_submit").click(function () {
            if ($("#input_line_code_from").val() > $("#input_line_code_to").val()) {
                alert("مقدار فیلد از خط  ورودی باید کوچکتر از فیلد تا خط ورودی باشد. ")
                return false;
            }

            if ($("#input_line_code_from").val() < $("#input_line_code_to").val()) {

                return confirm("با توجه به اطلاعات ثبت شده، تعداد " + ($("#input_line_code_to").val() - $("#input_line_code_from").val() + 1)
                    + " خط مشابه از شماره خط " + $("#input_line_code_from").val()
                    + " تا شماره خط " + $("#input_line_code_to").val()
                    + " برای کالا تعریف می شود، آیا از این کار اطمینان دارید؟");
            }

        })
        $('#form1').validate({
            rules: {
                "material_id": "required",
                "material_id_auto": "required",
                "amount": "required",
                "number": "required",
                "percent_of_use": "required",
                "production_status_id_auto": "required",
                "contractor_id": "required",
                "contractor_operation_id": "required",
                "warehouse_id": "required",
                "productive_consume_warehouse_id": "required",
                "sampling_consume_warehouse_id": "required",
                "station_id": "required",
                "station_operation_id": "required",
                "station_sub_operation_id": "required",
                "delivery_unit_id": "required",
                "input_line_code": {required: true, min: 1},
                "consumption_percent_of_production_channel": {required: true, min: 0},
                "dependent_on_main_unit_type_id": "required",
                "dependent_on_material_unit_type_id": "required",
                "dependent_on_material_id": "required",
                "bill_of_material_dependency_type_id": "required",
                "waste_prediction": "required",
                "consumption_correction_factor_prediction": "required",
                "is_structure_product": "required",
                "bill_of_material_entering_type_id": "required"
            }
        });
    </script>
@endsection
