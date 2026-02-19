@include("warehouse.input.entry_form._script")
<div class="card">
    <div class="card-header">
        <h5> فرم ورود به انبار </h5>
    </div>
    <div class="card-block" id="card-block">

        <h6>روش ورود اطلاعات</h6>
{{--        <div class="row">--}}
{{--            <div class="col-md-3">--}}
{{--                @include("component.input._checkbox_simple",["id"=>"enter_weight","label"=>"ثبت وزن خالص","checked"=>$enter_weight])--}}
{{--            </div>--}}
{{--            <div class="col-md-3">--}}
{{--                @include("component.input._checkbox_simple",["id"=>"enter_gross_weight","label"=>"ثبت وزن ناخالص","checked"=>$enter_gross_weight])--}}
{{--            </div>--}}
{{--            <div class="col-md-3">--}}
{{--                @include("component.input._checkbox_simple",["id"=>"enter_unit_amount","label"=>"ثبت واحد اصلی","checked"=>$enter_unit_amount])--}}
{{--            </div>--}}
{{--        </div>--}}
        <h6>مشخصات کالا</h6>
        <hr>
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
            @include("component.input._text",["label"=>"لات","id"=>"lot_number_code","value"=>$lot_number_code,"class_col"=>"col-md-3"])

        </div>
        <h6>مشخصات انبار</h6>
        <hr>
        <div class="row">
            <div class="col-md-3">
                @include("component.input._select",[
                "id"=>"warehouse_id",
                "label"=>" انتخاب انبار ",
                "option"=>$warehouse_option["items"],
                "val"=>$warehouse_option["value"],
                "text"=>$warehouse_option["text"],
                "class_col"=>""
                ])
            </div>
            <div class="col-md-3">
                @include("component.input._select",[
                "id"=>"trans_kind_id",
                "label"=>" نوع تراکنش  ",
                "option"=>$trans_kind_option["items"],
                "val"=>$trans_kind_option["value"],
                "text"=>$trans_kind_option["text"],
                "class_col"=>""
                ])
            </div>
            <div class="col-md-3">
                @include("component.input._aotocomplet2",[
                "id"=>"cost_center_id",
                "label"=>" مرکز هزینه ",
                "option"=>$cost_center_option["items"],
                "val"=>$cost_center_option["value"],
                "text"=>$cost_center_option["text"],
                "class_col"=>""
                ])
            </div>

            <div class="col-md-3">
                @include("component.input._aotocomplet2",[
                "id"=>"opp_kind_id",
                "label"=>" طرف حساب ",
                "option"=>$opp_kind_option["items"],
                "val"=>$opp_kind_option["value"],
                "text"=>$opp_kind_option["text"],
                "class_col"=>""
                ])
            </div>

            @include("component.input._number",["label"=>"مقدار کل","id"=>"amount","value"=>""])

            @include("component.input._textarea",["label"=>"شرح تراکنش","id"=>"description","value"=>$description,"width"=>"", "height"=>"50px"])



        </div>



    </div>
</div>


    <div class="col-md-12 center">
        <button type="submit" class="btn btn-primary"> ثبت فرم</button>

    </div>



