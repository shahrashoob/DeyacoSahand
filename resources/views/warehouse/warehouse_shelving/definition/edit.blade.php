@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش    {{$warehouseShelving->fullCaption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.warehouse_shelving.definition.update",[$warehouse,$warehouseShelving])}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._radio_box01",["id"=>"warehouse_shelving_line_type_id","label0"=>"عدد","label1"=>"حروف","value0"=>1,"value1"=>2,'label'=>"نوع کاراکتر  ","value"=>$warehouseShelving->warehouse_shelving_line_type_id])
                            @include("component.input._radio_box01",["id"=>"updown_code","label0"=>"به سمت بالا","label1"=>"به سمت پایین","value0"=>1,"value1"=>2,'label'=>"جهت لیبل ","value"=>$warehouseShelving->updown_code])
                            @include("component.input._radio_box01",["id"=>"can_product_directly_in_location","label0"=>"خیر","label1"=>"بله","value0"=>0,"value1"=>1,'label'=>"آیا کالا به صورت مستقیم می تواند در این "."سایت"." قرار بگیرد؟","value"=>$warehouseShelving->can_product_directly_in_location  ])

                            @include("component.input._number",["id"=>"warehouse_shelving_line_part",'label'=>"تعداد بخش در نام گذاری","value"=>$warehouseShelving->warehouse_shelving_line_part,"max"=>5])


                            @include("component.input._select",[
                                       "id"=>"warehouse_shelving_line_status_id",
                                       "label"=>"وضعیت نمایش  کد سلول ",
                                       "option"=>$status_option["items"],
                                       "val"=>$status_option["value"],
                                       "text"=>$status_option["text"],
                                       "class_col"=>"col-md-6"
                                       ])

                            <div class="w-100"><br/></div>

                        </div>


                        @if($warehouseShelving->warehouse_shelving_type_id==1)
                            <a href="{{route("wh.warehouse_shelving.definition.index",$warehouse)}}" class="btn btn-outline-dark">بازگشت</a>
                        @else
                                <a href="{{route("wh.warehouse_shelving.definition.list",[$warehouse,$warehouseShelving->parent])}}" class="btn btn-outline-dark">بازگشت</a>
                        @endif

                        <button type="submit" class="btn btn-primary"> ویرایش </button>

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
        $('#form1').validate({
            rules: {
                "warehouse_shelving_line_type_id": "required",
                "warehouse_shelving_line_part": "required",
                "warehouse_shelving_line_status_id": "required",
            }
        });
    </script>
@endsection
