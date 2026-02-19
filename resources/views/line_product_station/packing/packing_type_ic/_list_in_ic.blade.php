<div class="row">
    <div class="col-md-12">
        @include("component.input._lable",["id"=>"caption",'label'=>"نام نوع بسته بندی","value"=>$result_packing_types["packing_type"]["caption"]??"","class_col"=>"" ])


        @include("component.input._lable",["id"=>"weight","label"=>"وزن بسته بندی بدون حامل و کالا(کیلوگرم)","value"=>$result_packing_types["packing_type"]["weight"]??"","class_col"=>""])

        @include("component.input._lable",["id"=>"weight_error_percentage","label"=>"درصد خطای وزن بسته بندی","value"=>$result_packing_types["packing_type"]["weight_error_percentage"]??"","class_col"=>""])


        @include("component.input._lable",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$result_packing_types["packing_type"]["length"]??"","class_col"=>""])
        @include("component.input._lable",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$result_packing_types["packing_type"]["width"]??"","class_col"=>""])
        @include("component.input._lable",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$result_packing_types["packing_type"]["height"]??"","class_col"=>""])
        @include("component.input._lable",["id"=>"discharge_type_id",'label'=>"نوع تخلیه","value"=>$discharge_type->caption ??"","class_col"=>""])

        @include("component.input._lable",["id"=>"many_degrees_can_fit_into_one",'label'=>"آیا یک کالا با درجه های متفاوت میتوانند داخل بسته بندی قرار بگیرد؟","value"=>$result_packing_types["packing_type"]["many_degrees_can_fit_into_one"]?'بله':'خیر',"class_col"=>""])

        @include("component.input._lable",["id"=>"create_sub_packing_form_in_creation",'label'=>"آیا بسته بندی های فرعی در زمان ایجاد بسته بندی تعریف شوند؟","value"=>$result_packing_types["packing_type"]["create_sub_packing_form_in_creation"]?'بله':'خیر',"class_col"=>""])

    </div>
</div>