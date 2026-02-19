@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        افزودن کالای جدید به بسته بندی
                        {{$packing_form->code}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" autocomplete="off"
                          action="{{route("contractor.panel.register_production.submit_add_new_product_to_packing",[$contractor_allocation,$packing_form])}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf



                        @include("component.input._lable",["id"=>"packing_type_id","value"=>$packing_form->packing_type->caption,"label"=>"نوع بسته بندی"])



                        @include("component.input._lable",["id"=>"packing_type_id","value"=>$contractor_allocation->product->fullCaption(),"label"=>"کالا"])


                        @if($packing_form->items()->count()>0)
                            @include("component.input._hidden",["id"=>"lot_number_code","value"=>$packing_form->items()->first()->lot_number->code??"","class_col"=>"col-md-4"])
                            @include("component.input._lable",["id"=>"lot_code","lable"=>"همبافت (لات)","value"=>$packing_form->items()->first()->lot_number->code??"","class_col"=>"col-md-4"])
                        @else
                            @include("component.input._text",["id"=>"lot_number_code","lable"=>"همبافت (لات)","value"=>$lot_number_code??"","class_col"=>"col-md-4"])
                        @endif

                        @if($packing_form->items()->count()>0 && $packing_form->packing_type->many_degrees_can_fit_into_one == 0)
                            @include("component.input._lable",["id"=>"","value"=>$packing_form->items()->first()->degree->caption,"label"=>"درجه کالا"])
                            @include("component.input._hidden",["id"=>"degree_id","value"=>$packing_form->items()->first()->degree->id??"","class_col"=>"col-md-4"])

                        @else

                            @include("component.input._aotocomplet2",[
                                "id"=>"degree_id",
                                "label"=>"درجه کالا ",
                                "option"=>$degree_option["items"],
                                "val"=>$degree_option["value"],
                                "text"=>$degree_option["text"],
                                "class_col"=>"col-md-4"
                                ])

                        @endif


                        @include("component.input._number",["id"=>"final_amount","lable"=>$contractor_allocation->product->unit->measurement,"value"=>$final_amount??"","class_col"=>"col-md-4"])
                        @include("component.input._number",["id"=>"sub_amount","lable"=>$contractor_allocation->product->sub_unit->measurement,"value"=>$sub_amount??"","class_col"=>"col-md-4"])

                        @if(isset($add_new_lot))
                            <div class="alert alert-warning">
                                همبافت (لات)
                                {{$lot_number_code}}
                                برای کالا در سیستم تعریف نشده است، در صورت اطمینان از تعریف همبافت جدید، بر روی دکمه
                                افزودن کلیک نمایید.
                            </div>
                            @include("component.input._hidden",["id"=>"add_new_lot","value"=>1])
                        @endif

                        <div class="col-md-12">
                            <a href="{{route("contractor.panel.register_production.view_packing",[$contractor_allocation,$packing_form])}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary"> افزودن</button>
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
@endsection
@section("scripts")
    <script>


        $('#form1').validate({
            rules: {
                "final_amount": "required",
                "sub_amount": "required",
            }
        });

    </script>
@endsection
