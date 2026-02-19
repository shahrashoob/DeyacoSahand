@php $machine=$reference;@endphp
@php $boject1=$special_license->getObject1();@endphp
@php $json_decode_list=$boject1["json_decode_list"];@endphp
@php $active_status=$boject1["active_status"];@endphp
@php $discharge_type=$boject1["discharge_type"];@endphp
<div class="col-md-12">

    باتوجه به اینکه بسته بندی با مشخصات زیر تعریف نشده است خواهشمند است جهت ثبت این بسته بندی در سامانه اقدام نمایید.

</div>
<br/>
<br/>
@include("component.input._lable", ["id"=>"caption", 'label'=>"عنوان", "value"=>$json_decode_list->caption,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"packing_layer", 'label'=>"لایه های بسته بندی",
 "value"=>$json_decode_list->count_packing_layer == 1?$json_decode_list->first_packing_type_id_auto??"":$json_decode_list->carrier_type_id_auto??"",
  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"packing_type_label_printing_type_id", 'label'=>"نوع قالب بسته بندی", "value"=>$json_decode_list->packing_type_label_printing_type_id_auto??"",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"printer_unit_display_type_id_auto", 'label'=>"نوع نمایش واحد کالا در قالب پرینتر", "value"=>$json_decode_list->printer_unit_display_type_id_auto??"",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"label_caption", 'label'=>"عنوان لیبل", "value"=>$json_decode_list->label_caption??"",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"length", 'label'=>"طول ", "value"=>$json_decode_list->length,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"width", 'label'=>"عرض", "value"=>$json_decode_list->width,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"height", 'label'=>"ارتفاع", "value"=>$json_decode_list->height,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"weight", 'label'=>"وزن بسته بندی بدون حامل و کالا (کیلوگرم)", "value"=>$json_decode_list->weight??"",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"discharge_type_id",
                                      'label'=>"نوع تخلیه",
                                      "value"=>$discharge_type->caption,
                                      "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"average_weight", 'label'=>"وضعیت", "value"=>$active_status->caption??"",  "class_col"=>"col-md-6",])


@include("component.input._lable", ["id"=>"many_degrees_can_fit_into_one", 'label'=>"آیا یک کالا با درجه های متفاوت میتوانند داخل بسته بندی قرار بگیرد؟",
                                       "value"=>isset($json_decode_list->many_degrees_can_fit_into_one)?"بله":"خیر",
                                        "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"it_is_possible_extract_production_form_separately", 'label'=>"آیا می توان آیتم های فرم تولید را به صورت مجزا با این بسته بندی استخراج نمود؟",
                                    "value"=>isset($json_decode_list->it_is_possible_extract_production_form_separately)?"بله":"خیر",
                                      "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"create_sub_packing_form_in_creation",
                                   'label'=>"آیا بسته بندی های فرعی در زمان ایجاد بسته بندی تعریف شوند؟",
                                    "value"=>isset($json_decode_list->create_sub_packing_form_in_creation)?"بله":"خیر",
                                     "class_col"=>"col-md-6",])



{{--<div class="col-md-12">--}}
{{--    <div class="table-responsive">--}}
{{--        <table class="table table-styling">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th> عنوان</th>--}}
{{--                <th> لایه های بسته بندی</th>--}}
{{--                <th>نوع قالب بسته بندی</th>--}}
{{--                <th>نوع نمایش واحد کالا در قالب پرینتر</th>--}}
{{--                <th>عنوان لیبل</th>--}}
{{--                <th>طول</th>--}}
{{--                <th>عرض</th>--}}
{{--                <th>ارتفاع</th>--}}
{{--                <th>وزن</th>--}}
{{--                <th>وضعیت</th>--}}

{{--            </tr>--}}

{{--            </thead>--}}
{{--            <tbody>--}}

{{--            <tr>--}}

{{--                <td>--}}
{{--                    {{$json_decode_list->caption??""}}--}}
{{--                </td>--}}
{{--                <td>--}}

{{--                    @if($json_decode_list->count_packing_layer == 1)--}}
{{--                        لایه یک:--}}
{{--                        {{$json_decode_list->first_packing_type_id_auto??""}},--}}
{{--                        لایه دو:--}}
{{--                        {{$json_decode_list->second_carrier_type_id_auto??""}}--}}
{{--                    @else--}}
{{--                        لایه یک:--}}
{{--                        {{$json_decode_list->carrier_type_id_auto??""}},--}}
{{--                    @endif--}}

{{--                </td>--}}

{{--                <td>{{$json_decode_list->packing_type_label_printing_type_id_auto??""}}</td>--}}
{{--                <td>{{$json_decode_list->printer_unit_display_type_id_auto??""}}</td>--}}
{{--                <td>{{$json_decode_list->label_caption??""}}</td>--}}
{{--                <td>{{$json_decode_list->length??""}}</td>--}}
{{--                <td>{{$json_decode_list->width??""}}</td>--}}
{{--                <td>{{$json_decode_list->height??""}}</td>--}}
{{--                <td>{{$json_decode_list->weight??""}}</td>--}}
{{--                <td>{{$active_status->caption??""}}</td>--}}

{{--            </tr>--}}
{{--            </tbody>--}}

{{--        </table>--}}
{{--    </div>--}}
{{--</div>--}}
<br/>
<br/>