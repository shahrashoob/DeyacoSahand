<div class="row">
    @include("component.input._text",["id"=>"code1",'label'=>"کد کالا","value"=>$product->code,"autofocus"=>1])
    @include("component.input._text",["id"=>"caption1",'label'=>"نام کالا ","value"=>$product->caption])


    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"goods_kind_id1",
            "label"=>"رسته کالا",
            "option"=>$goods_kind_option["items"],
            "val"=>$product->goods_kind->id??"",
            "text"=>$product->goods_kind->caption??"",
            "class_col"=>""
            ])
    </div>
    {{--    <div class="w-100"><br/></div>--}}
    {{--    <div class="col-md-6">--}}
    {{--        @include("component.input._select",[--}}
    {{--            "id"=>"goods_type_id1",--}}
    {{--            "label"=>" نوع کالا",--}}
    {{--            "option"=>$goods_type_option["items"],--}}
    {{--            "val"=>$product->goods_type->id??"",--}}
    {{--            "text"=>$product->goods_type->caption??"",--}}
    {{--            "class_col"=>""--}}
    {{--            ])--}}
    {{--    </div>--}}
    {{--    <div class="w-100"><br/></div>--}}
    {{--    <div class="col-md-6">--}}
    {{--        @include("component.input._select",[--}}
    {{--            "id"=>"unit_id1",--}}
    {{--            "label"=>" واحد کالا  ",--}}
    {{--            "option"=>$unit_option["items"],--}}
    {{--            "val"=>$product->unit->id??"",--}}
    {{--            "text"=>$product->unit->caption??"",--}}
    {{--            "class_col"=>""--}}
    {{--            ])--}}
    {{--    </div>--}}
    {{--    <div class="w-100"><br/></div>--}}
    {{--    <div class="col-md-6">--}}
    {{--        @include("component.input._select",[--}}
    {{--            "id"=>"sub_unit_id1",--}}
    {{--            "label"=>" واحد فرعی کالا  ",--}}
    {{--            "option"=>$sub_unit_option["items"],--}}
    {{--            "val"=>$product->sub_unit->id??"",--}}
    {{--            "text"=>$product->sub_unit->caption??"",--}}
    {{--            "class_col"=>""--}}
    {{--            ])--}}
    {{--    </div>--}}
    {{--    <div class="w-100"><br/></div>--}}
    {{--    <div class="col-md-6">--}}
    {{--        @include("component.input._select",[--}}
    {{--            "id"=>"sub_unit2_id1",--}}
    {{--            "label"=>" واحد فرعی 2 کالا  ",--}}
    {{--            "option"=>$sub_unit2_option["items"],--}}
    {{--            "val"=>$product->sub_unit2->id??"",--}}
    {{--            "text"=>$product->sub_unit2->caption??"",--}}
    {{--            "class_col"=>""--}}
    {{--            ])--}}
    {{--    </div>--}}


    <div class="w-100"><br/></div>

    @include("component.input._number",["id"=>"number_in_carton1",'label'=>"تعداد در واحد اصلی ( ویژه انتقال به نوسا)","value"=>$product->number_in_carton])
    @include("component.input._number",["id"=>"weight1",'label'=>" وزن کالا (کیلوگرم)","value"=>$product->weight])
    @if($product->predictive_weight)
        @include("component.input._lable",["id"=>"predictive_weight",'label'=>"وزن پیش بینی ","value"=>$product->predictive_weight." کیلوگرم"])
    @endif

    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"supply_type_id1",
            "label"=>" نوع تامین   ",
            "option"=>$supply_type_option["items"],
            "val"=>$product->supply_type->id??"",
            "text"=>$product->supply_type->caption??"",
            "class_col"=>""
            ])
    </div>
    <div class="w-100"><br/></div>

    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"service_id_in_employer_system1",
            "label"=>"  کد خدمت در سامانه کارفرما ",
            "option"=>$service_id_in_employer_system_option["items"],
            "val"=>$service_id_in_employer_system_option["value"],
            "text"=>$service_id_in_employer_system_option["text"],
            "class_col"=>""
            ])
    </div>

    <div class="w-100"><br/></div>
    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"active_status_id1",
            "label"=>" وضعیت   ",
            "option"=>$status_option["items"],
            "val"=>$product->active_status->id??"",
            "text"=>$product->active_status->caption??"",
            "class_col"=>""
            ])
    </div>
    <div class="w-100"><br/></div>
    <div class="col-md-6">
        <div class="row">
            @include("component.input._file_upload",["id"=>"image_file","label"=>"تصویر کالا ( 300*300 پیکسل)","value"=>""])

        </div>
    </div>

    <a href="{{asset("upload/product/".($product->image->filename??''))}}" class="view-image"
             data-title="{{$product->caption}}">
    <div class="col-md-6" style=" width: 300px;
  height: 300px;
  max-width: 300px;
  max-height: 300px;
  border: 2px solid;">

            <img style="width: 300px" src="{{asset("upload/product/".($product->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"
            />

    </div>
</a>

    <!-- استفاده از پلاگین -->


    <script>
        $(function () {
            $('a.view-image').simpleLightbox();
        });
    </script>


</div>

