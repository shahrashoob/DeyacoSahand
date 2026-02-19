@include("warehouse.input.entry_form._script")
<div class="card">
    <div class="card-header">
        <h5> فرم ورود به انبار </h5>
        <a href="{{route("wh.input.entry_form.index2")}}">ورود به انبار جهت کالاهای بدون بسته بندی و مخزن</a>
    </div>
    <div class="card-block" id="card-block">

        <h6>روش ورود اطلاعات</h6>
        <div class="row">
            <div class="col-md-3">
                @include("component.input._checkbox_simple",["id"=>"enter_weight","label"=>"ثبت وزن خالص","checked"=>$enter_weight])
            </div>
            <div class="col-md-3">
                @include("component.input._checkbox_simple",["id"=>"enter_gross_weight","label"=>"ثبت وزن ناخالص","checked"=>$enter_gross_weight])
            </div>
            <div class="col-md-3">
                @include("component.input._checkbox_simple",["id"=>"enter_unit_amount","label"=>"ثبت واحد اصلی","checked"=>$enter_unit_amount])
            </div>
        </div>
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
                "id"=>"packing_type_id",
                "label"=>"نوع بسته بندی",
                "option"=>$packing_type_option["items"],
                "val"=>$packing_type_option["value"],
                "text"=>$packing_type_option["text"],
                "class_col"=>""
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
            @include("component.input._textarea",["label"=>"شرح تراکنش","id"=>"description","value"=>$description,"width"=>"", "height"=>"50px"])
        </div>


        <h6>افزودن بسته بندی جدید</h6>
        <hr/>

        <div class="row">

            <div class="col-md-12">
                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 10px">ردیف</th>

                            @if($enter_gross_weight)
                                <th style="width: 250px"> وزن ناخالص</th>
                            @endif
                            @if($enter_weight)
                                <th  style="width: 250px">وزن خالص</th>
                            @endif
                            @if($enter_unit_amount)
                                <th  style="width: 250px">مقدار واحد اصلی</th>
                            @endif

                            <th class="class_sub_packing_number"  style="width: 250px">تعداد <br/>بسته بندی های فرعی</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @for($k=0;$k<30;$k++)
                            <tr>
                                <td>{{++$row}}</td>


                                @if($enter_gross_weight)
                                    <td>
                                    <input type="number" style="width: 60px" min="0" name="packing_form_rows[{{$k}}][gross_weight]" value="{{isset($packing_form_rows[0]["gross_weight"])?$packing_form_rows[$k]["gross_weight"]:""}}" >
                                    </td>
                                @endif
                                @if($enter_weight)
                                    <td>
                                        <input type="number" style="width: 60px" min="0" name="packing_form_rows[{{$k}}][weight]" value="{{isset($packing_form_rows[0]["weight"])?$packing_form_rows[$k]["weight"]:""}}" >
                                    </td>
                                @endif
                                @if($enter_unit_amount)
                                    <td>
                                        <input type="number" style="width: 60px" min="0" name="packing_form_rows[{{$k}}][unit_amount]" value="{{isset($packing_form_rows[0]["unit_amount"])?$packing_form_rows[$k]["unit_amount"]:""}}" >
                                    </td>
                                @endif
                                <td class="class_sub_packing_number">
                                    <input type="number" style="width: 60px" min="0" name="packing_form_rows[{{$k}}][sub_packing_form_number]" value="{{isset($packing_form_rows[0]["sub_packing_form_number"])?$packing_form_rows[$k]["sub_packing_form_number"]:""}}">
                                </td>
                                <td></td>
                            </tr>
                        @endfor

                        </tbody>
                    </table>
                </div>


            </div>


        </div>
    </div>
</div>


    <div class="col-md-12 center">
        <button type="submit" class="btn btn-primary"> ثبت فرم</button>

    </div>



