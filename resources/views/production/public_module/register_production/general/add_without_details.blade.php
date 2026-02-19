@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine_allocation->getTextOfThing("production_caption")}} {{$machine_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("production.public_module.register_production.submit_without_details",[$machine_allocation,$other_machine_allocation->id??0])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"machine_allocation_id",
                                    "label"=> $machine_allocation->getTextOfThing("production_caption"),
                                    "option"=>$machine_allocation_option["items"],
                                    "val"=>$machine_allocation_option["value"],
                                    "text"=>$machine_allocation_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>
                            @include("component.input._lable",["id"=>"product_id","value"=>$other_machine_allocation->product->fullCaption(),"label"=>"کالا"])
                            <div class="w-100"></div>
                            @include("component.input._text",["id"=>"amount","label"=>$other_machine_allocation->product->unit->measurement." کل","value"=>$amount??"","class_col"=>"col-md-4"])
                            <div class="w-100"></div>
                            @if($other_machine_allocation->product->sub_unit)
                                @include("component.input._text",["id"=>"sub_amount","label"=>$other_machine_allocation->product->sub_unit->measurement." کل","value"=>$sub_amount??"","class_col"=>"col-md-4"])
                            @endif
                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"degree_id",
                                    "label"=>"درجه ",
                                    "option"=>$degree_option["items"],
                                    "val"=>$degree_option["value"],
                                    "text"=>$degree_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <br/>
                            <div class="w-100"><br/></div>
                            @include("component.input._text",["id"=>"lot_number_code","label"=>" لات","value"=>$lot_number_code??"","class_col"=>"col-md-4"])

                            <div class="w-100"></div>


                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"packing_type_id",
                                    "label"=>"نوع بسته بندی ",
                                    "option"=>$packing_type_option["items"],
                                    "val"=>$packing_type_option["value"],
                                    "text"=>$packing_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"><br/></div>
                            @include("component.input._number",["id"=>"packing_form_number","label"=>" تعداد بسته بندی","value"=>$packing_form_number??"","class_col"=>"col-md-4"])
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"price","label"=>" مبلغ بدون ارزش افزوده (ریال)","value"=>$price??"","class_col"=>"col-md-4"])
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"tax_price","label"=>"مبلغ ارزش افزوده (ریال)","value"=>$tax_price??"","class_col"=>"col-md-4"])

                            @if(isset($lot_number_code))
                                <div class="col-md-12 alert alert-warning">
                                    شماره همبافت {{$lot_number_code}} در سیستم یافت نشد، آیا تمایل دارید شماره جدید
                                    تعریف
                                    کنید.
                                </div>
                                @include("component.input._hidden",["id"=>"create_new_lot_number","value"=>1,"class_col"=>"col-md-4"])

                            @endif
                            <div class="w-100"></div>
                            <div class="col-md-4">

                                <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                                   class="btn btn-outline-dark"> بازگشت</a>
                                <button type="submit" href="#" class="btn btn-primary"> ثبت</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .btn {
            width: 120px;
        }
    </style>
@endsection
@section("scripts")
    <script>
$("#machine_allocation_id").change(function (){
    window.location.replace("{{route("production.public_module.register_production.add_without_details",$machine_allocation->id)}}"+"/"+$(this).val());
});
        $('#form1').validate({
            rules: {
                "amount": {required: true, "min": 1},
                "sub_amount": {required: true, "min": 1},
                "degree_id": "required",
                "lot_number_code": "required",
                "packing_type_id": "required",
                "packing_form_number": {required: true, "min": 1},
                "price": {required: true, "min": 1},
                "tax_price": {required: true, "min": 0},
            }
        });
    </script>
@endsection
