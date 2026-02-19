@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")

    <div class="row">

        @if(isset($error_message))
            <div class="col-md-12">
                <div class="alert alert-danger">
                    {!! $error_message !!}
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5> ( ویژه دوره پیاده سازی) برگ دستور تولید (پیمان)</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("utility.planing.production_order_demo_submit")}}"
                          method="post"
                          novalidate="novalidate" autocomplete="off" to>
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>" انتخاب کالا ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"priority_id",
                                    "label"=>" اولویت ",
                                    "option"=>$priority_option["items"],
                                    "val"=>$priority_option["value"],
                                    "text"=>$priority_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"production_type_id",
                                    "label"=>" نوع کارت ",
                                    "option"=>$production_type_option["items"],
                                    "val"=>$production_type_option["value"],
                                    "text"=>$production_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"carton","lable"=>" مقدار کارت (واحد اصلی) ","value"=>$carton??""])
                            @include("component.input._number",["id"=>"series","lable"=>"سری سفارش","value"=>$series??""])
                            @include("component.input._number",["id"=>"order_code","lable"=>"شماره سفارش","value"=>$order_code??""])

                            @include("component.input._number",["id"=>"parent_production_code","lable"=>"سریال کارت تولید سطح بالا","value"=>$parent_production_code??""])

                            @include("component.input.datepicker._datepicker",["id"=>"max_delivery_datetime1","lable"=>"حداکثر تاریخ تحویل  ","value"=>$max_delivery_datetime1??null])


                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">صدور کارت تولید</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("styles")
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        var required_parent_production =@php echo json_encode($list_required_parent); @endphp;
        var required_order =@php echo json_encode($list_required_order); @endphp;
        var allow_sample_production =@php echo json_encode($list_allow_production["sample"]); @endphp;
        var allow_production =@php echo json_encode($list_allow_production["production"]); @endphp;


        $("#product_id_auto, #priority_id").click(function () {


            updateInputs();

        })

        function updateInputs(){


            $("#production_type_id").val("")
            $("#production_type_id option[value=2]").css("display", "none")
            $("#production_type_id option[value=1]").css("display", "none")
            if (allow_sample_production[$("#product_id").val()]) {
                $("#production_type_id option[value=2]").css("display", "block")
            }

            if (allow_production[$("#product_id").val()]) {
                $("#production_type_id option[value=1]").css("display", "block")
            }
        }
        $("#production_type_id").change(function () {

            if (required_parent_production[$("#product_id").val()] &&  $("#production_type_id").val()==1 ) {
                $("#parent_production_code").parent().css("display", "block");
            } else {
                $("#parent_production_code").parent().css("display", "none");
            }

            if(required_order[$("#product_id").val()]  &&  $("#production_type_id").val()==1 ){
                $("#series").parent().css("display", "block");
                $("#order_code").parent().css("display", "block");
                $("#max_delivery_datetime1").parent().css("display", "block");
            }else{
                $("#series").parent().css("display", "none");
                $("#order_code").parent().css("display", "none");
                $("#max_delivery_datetime1").parent().css("display", "none");
            }
        })
        $('#form1').validate({
            rules: {
                "carton": {required:true, min:1},
                "series": {required:true,   minlength: 1, maxlength: 2},
                "order_code": "required",
                "priority_id": "required",
                "product_id_auto": "required",
                "parent_production_code": "required",
                "max_delivery_datetime1_value": "required",
                "production_type_id": "required",
            }
        });
    </script>
@endsection
