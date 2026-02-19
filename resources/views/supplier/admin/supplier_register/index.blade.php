@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  مدیریت تامین کنندگان  ")

@section('content')

    <form id="form1" autocomplete="off" action="{{route("supplier.admin.supplier_register.submit")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> فرم ثبت تامین ویژه دوره پیاده سازی </h5>
                    </div>
                    <div class="card-block" id="card-block">

                        <div class="row">

                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                "id"=>"product_id",
                                "label"=>"نام محصول",
                                "option"=>$product_option["items"],
                                "val"=>$product_option["value"],
                                "text"=>$product_option["text"],
                                "class_col"=>"",
                                "my_function"=>"update_product();"
                                ])
                            </div>
                            <div class="w-100"></div>

                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"supplier_id",
                                "label"=>"تامین کننده",
                                "option"=>$supplier_option["items"],
                                "val"=>$supplier_option["value"],
                                "text"=>$supplier_option["text"],
                                "class_col"=>"",
                                ])
                            </div>
                            <div class="w-100"><br/></div>

                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"debt_or_supply_id",
                                "label"=>"قرض / تامین ",
                                "option"=>$debt_or_supply_option["items"],
                                "val"=>$debt_or_supply_option["value"],
                                "text"=>$debt_or_supply_option["text"],
                                "class_col"=>"",
                                ])
                            </div>
                            <div class="w-100"><br/></div>


                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"warehouse_storage_type_id",
                                "label"=>"نوع انبارش",
                                "option"=>$warehouse_storage_type_option["items"],
                                "val"=>$warehouse_storage_type_option["value"],
                                "text"=>$warehouse_storage_type_option["text"],
                                "class_col"=>""
                                ])
                            </div>
                            <div class="w-100"><br/></div>


                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"packing_type_id",
                                "label"=>"نوع بسته بندی",
                                "option"=>$packing_type_option["items"],
                                "val"=>$packing_type_option["value"],
                                "text"=>$packing_type_option["text"],
                                "class_col"=>""
                                ])
                            </div>
                            <div class="w-100"><br/></div>


                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"degree_id",
                                "label"=>"درجه کالا",
                                "option"=>$degree_option["items"],
                                "val"=>$degree_option["value"],
                                "text"=>$degree_option["text"],
                                "class_col"=>""
                                ])
                            </div>
                            <div class="w-100"><br/></div>

                            @include("component.input._text",["label"=>"لات","id"=>"lot_number_code","value"=>$lot_number_code,"class_col"=>"col-md-3"])
                            <div class="w-100"></div>

                            @include("component.input._number",["label"=>"مقدار کل","id"=>"amount","value"=>$amount,"class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @include("component.input._number",["label"=>"مقدار فرعی","id"=>"sub_amount","value"=>$sub_amount,"class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @include("component.input._number",["label"=>"تعداد بسته بندی","id"=>"packing_form_number","value"=>$packing_form_number,"class_col"=>"col-md-3"])
                            <div class="w-100"><br/></div>
                            <div class="col-md-12">
                                <div class="w-100"></div>
                                <input type="checkbox" id="sale_info_complete_later" name="sale_info_complete_later"
                                       @if(isset($sale_info_complete_later) && $sale_info_complete_later) checked @endif
                                       style="display: inline !important;">
                                اطلاعات خرید را بعدا تکمیل می کنم.
                            </div>
                            <div  class="col-md-12">
                                <div style="border: 1px solid; padding: 5px">
                                    <div class="w-100"></div>
                                    @include("component.input._number",["label"=>"مبلغ کل بدون ارزش افزوده (ریال) ","id"=>"price","value"=>$price,"class_col"=>"col-md-3"])
                                    <div class="w-100"></div>
                                    @include("component.input._number",["label"=>"مبلغ ارزش افزوده (ریال)","id"=>"tax_price","value"=>$tax_price,"class_col"=>"col-md-3"])

                                </div>
                            </div>
                            @if(isset($create_new_lot_number))
                                <div class="col-md-12 alert alert-warning">
                                    کد همبافت {{$create_new_lot_number}} در سیستم یافت نشد، آیا تمایل دارید کد جدید
                                    تعریف
                                    کنید.
                                </div>
                                @include("component.input._hidden",["id"=>"create_new_lot_number","value"=>$create_new_lot_number,"class_col"=>"col-md-4"])

                            @endif
                        </div>


                    </div>
                </div>

                <div class="col-md-12 center">
                    <button type="submit" class="btn btn-primary"> ثبت تامین</button>

                </div>
            </div>


        </div>
    </form>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("component.script_function.get_new_option")
    <script>
        var goods_kind_product =@php echo json_encode($list_goods_kind_product); @endphp;
        var list_sub_unit_product =@php echo json_encode($list_sub_unit_product); @endphp;
        var list_unit_product =@php echo json_encode($list_unit_product); @endphp;

        

        function update_product() {
            get_new_option(
                $("#supplier_id").val(),
                $("#product_id").val(),
                "تامین کننده",
                "supplier_id",
                "supplier_product",
                []
            )


        }

        function update_unit() {
            if ($("#product_id").val() > 0) {
                $("#amount_label").text(" مقدار کل (" + list_unit_product[$("#product_id").val()] + ")")
                $("#sub_amount_label").text(" مقدار کل واحد فرعی (" + list_sub_unit_product[$("#product_id").val()] + ")")
            }
            if (list_sub_unit_product[$("#product_id").val()] == "") {
                $("#sub_amount").parent().css("display", "none")
            } else {
                $("#sub_amount").parent().css("display", "")
            }
        }
        function update_warehouse_storage(){
            if($("#warehouse_storage_type_id").val() == 2){ // انبارش با بسته بندی
                $("#packing_type_id").parent().css("display","")
                $("#packing_form_number").parent().css("display","")
            }
            else{
                $("#packing_type_id").parent().css("display","none")
                $("#packing_form_number").parent().css("display","none")
            }
        }

        function sale_info_complete_later(){
            if($("#sale_info_complete_later").is(":checked")){
                $("#price").prop("disabled",true);
                $("#tax_price").prop("disabled",true);
            }
            else{
                $("#price").prop("disabled",false);
                $("#tax_price").prop("disabled",false);
            }
        }

        $("#supplier_id").change(function () {
            get_new_option(
                $("#warehouse_storage_type_id").val(),
                $("#product_id").val(),
                "نوع انبارش",
                "warehouse_storage_type_id",
                "warehouse_storage_type_option",
                []
            )

            get_new_option(
                $("#packing_type_id").val(),
                $("#product_id").val(),
                "نوع بسته بندی",
                "packing_type_id",
                "packing_type_product",
                []
            )
            get_new_option(
                $("#degree_id").val(),
                goods_kind_product[$("#product_id").val()],
                "درجه کالا",
                "degree_id",
                "degree",
                []
            )
            update_unit();
            update_warehouse_storage();
        })

        $("#warehouse_storage_type_id").change(function () {

            update_warehouse_storage();


        })


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

        $("#sale_info_complete_later").change(sale_info_complete_later);

        $('#form1').validate({
            rules: {
                "product_id_auto": "required",
                "packing_type_id": "required",
                "degree_id": "required",
                "lot_number_code": "required",
                "packing_form_number": "required",
                "price": "required",
                "tax_price": "required",
                "supplier_id": "required",
                "amount": "required",
                "warehouse_storage_type_id": "required",
            }
        });

        update_unit();
        update_warehouse_storage();
        sale_info_complete_later();
    </script>
@endsection
