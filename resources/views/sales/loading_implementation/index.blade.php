@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  مدیریت تامین کنندگان  ")

@section('content')

    <form id="form1" autocomplete="off" action="{{route("sales.loading_implementation.submit")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> فرم ثبت بار ویژه دوره پیاده سازی </h5>
                    </div>
                    <div class="card-block" id="card-block">
                         @if (isset($customer))
                            <div class="w-100"></div> @include("component.input._text",["label"=>"مشتری","id"=>"","value"=>$customer->caption,"readonly"=>1 ,"class_col"=>"col-md-3"])
                             @include("component.input._hidden",["id"=>"customer_id","value"=>$customer->id ,"class_col"=>"col-md-4"])
   
                         @else
                            <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"customer_id",
                                "label"=>"مشتری",
                                "option"=>$customer_option["items"],
                                "val"=>$customer_option["value"],
                                "text"=>$customer_option["text"],
                                "class_col"=>"",
                                "my_function"=>"update_customer();"
                                ])
                            </div>
                             <div class="w-100"></div>
                         @endif
                            

                           <div class="col-md-3">
                                @include("component.input._select",[
                                "id"=>"product_id",
                                "label"=>"نام محصول",
                                "option"=>$product_option["items"],
                                "val"=>$product_option["value"],
                                "text"=>$product_option["text"],
                                "class_col"=>"", 
                                "my_function"=>"update_product();"
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
                            @include("component.input._hidden",["id"=>"add_new_row","value"=>0,"class_col"=>"col-md-4"])
                            <div class="w-100"></div>
                           
                            <div class="w-100"></div>
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
                    <button type="submit" class="btn btn-primary" id="submit"> ثبت تامین</button>
                    <button type="submit" class="btn btn-primary" id="submit_and_creat_new_row">  ثبت تامین و ایجاد ردیف جدید</button>
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

      $("#submit").click(function(){
           $("#add_new_row").val(0);
        })

        $("#submit_and_creat_new_row").click(function(){
           $("#add_new_row").val(1);
        })

        function update_product() {
            get_new_option(
                $("#customer_id").val(),
                $("#product_id").val(),
                "مشتری ",
                "customer_id",
                "customer_product",
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


                $("#customer_id").change(function () {

                    get_new_option(
            
                $("#product_id").val(),
                $("#customer_id").val(),
                "محصول",
                "product_id",
                "product_customer_line_product_station",
                []
            )

        })



        $("#product_id").change(function () {
                    get_new_option(
                $("#product_id").val(),
                $("#customer_id").val(),
                "محصول",
                "product_id",
                "product_customer_line_product_station",
                []
            )
            get_new_option(
                $("#warehouse_storage_type_id").val(),
                $("#product_id").val(),
                "نوع انبارش",
                "warehouse_storage_type_id",
                "warehouse_storage_type_option",
                [],
                0,
                "",
                 0
            )

            get_new_option(
                $("#packing_type_id").val(),
                $("#product_id").val(),
                "نوع بسته بندی",
                "packing_type_id",
                "packing_type_product",
                [],
                0,
                "",
                1
            )
            get_new_option(
                $("#degree_id").val(),
                goods_kind_product[$("#product_id").val()],
                "درجه کالا",
                "degree_id",
                "degree",
                [],
                0,
                "",
                1
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



        $('#form1').validate({
            rules: {
                "product_id_auto": "required",
                "packing_type_id": "required",
                "degree_id": "required",
                "lot_number_code": "required",
                "packing_form_number": "required",
                "customer_id": "required",
                "amount": "required",
                "warehouse_storage_type_id": "required",
            }
        });

        update_unit();
        update_warehouse_storage();
    </script>
@endsection
