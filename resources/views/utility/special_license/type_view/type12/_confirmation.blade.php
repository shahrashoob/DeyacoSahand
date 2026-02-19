@php $machine=$reference;@endphp
@php $boject1=$special_license->getObject1();@endphp
@php $json_decode_list=$boject1["json_decode_list"];@endphp
@php $unit=$boject1["unit"];@endphp
@php $carrier_group=$boject1["carrier_group"];@endphp

<div class="col-md-12">

    باتوجه به اینکه حامل با مشخصات زیر تعریف نشده است خواهشمند است جهت ثبت این حامل در سامانه اقدام نمایید.

</div>
<br/>
<br/>


@include("component.input._lable", ["id"=>"caption", 'label'=>"عنوان", "value"=>$json_decode_list->caption,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"caption", 'label'=>"گروه حامل", "value"=>$carrier_group->caption,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"unit", 'label'=>"واحد سنجش کالای حامل", "value"=>$unit->caption,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"min_band_number", 'label'=>"حداقل باند", "value"=>$json_decode_list->min_band_number,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"max_band_number", 'label'=>"حداکثر باند", "value"=>$json_decode_list->max_band_number,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"min_band_capacity", 'label'=>"حداقل ظرفیت هر باند", "value"=>$json_decode_list->min_band_capacity,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"max_band_capacity", 'label'=>"حداکثر ظرفیت هر باند", "value"=>$json_decode_list->max_band_capacity,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"length", 'label'=>"طول ", "value"=>$json_decode_list->length,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"width", 'label'=>"عرض", "value"=>$json_decode_list->width,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"height", 'label'=>"ارتفاع", "value"=>$json_decode_list->height,  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"product_code", 'label'=>"کد کالای حامل", "value"=>$json_decode_list->product_code??"",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"average_weight", 'label'=>"میانگین وزن حامل ها (کیلوگرم)", "value"=>$json_decode_list->average_weight??"",  "class_col"=>"col-md-6",])

@include("component.input._lable", ["id"=>"placed_in_warehouse", 'label'=>"آیا این نوع حامل می تواند در انبار قرار بگیرد؟", "value"=>isset($json_decode_list->placed_in_warehouse)?"بله":"خیر",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"has_number_ability", 'label'=>"آیا قابلیت شماره گذاری دارد؟", "value"=>isset($json_decode_list->has_number_ability)?"بله":"خیر",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"system_can_define_new_carrier", 'label'=>"آیا سیستم می تواند حامل جدید تعریف کند؟", "value"=>isset($json_decode_list->system_can_define_new_carrier)?"بله":"خیر",  "class_col"=>"col-md-6",])
@include("component.input._lable", ["id"=>"it_changes_volume_after_filling", 'label'=>"آیا این نوع حامل پس از تکمیل تغییر حجم دارد؟", "value"=>isset($json_decode_list->it_changes_volume_after_filling)?"بله":"خیر",  "class_col"=>"col-md-6",])

{{--<div class="col-md-12" >--}}
{{--<div class="table-responsive">--}}
{{--    <table class="table table-styling">--}}
{{--        <thead>--}}
{{--        <tr>--}}
{{--            <th> عنوان</th>--}}
{{--            <th>گروه حامل</th>--}}
{{--            <th>واحد سنجش <br/> کالای حامل</th>--}}
{{--            <th>حداقل باند</th>--}}
{{--            <th>حداکثر باند</th>--}}
{{--            <th>حداقل ظرفیت <br/>هر باند</th>--}}
{{--            <th>حداکثر ظرفیت <br/>هر باند</th>--}}
{{--            <th> در انبار <br/> قرار می گیرد؟</th>--}}
{{--            <th> قابلیت شماره <br/>گذاری دارد؟</th>--}}
{{--            <th>سیستم می تواند <br/>حامل جدید تعریف کند</th>--}}

{{--        </tr>--}}

{{--        </thead>--}}
{{--        <tbody>--}}

{{--            <tr>--}}

{{--                <td>--}}
{{--                    {{$json_decode_list->caption}}--}}
{{--                </td>--}}
{{--                <td>{{$carrier_group->caption}}</td>--}}
{{--                <td>{{ $unit->caption}}</td>--}}
{{--                <td>{{$json_decode_list->min_band_number}}</td>--}}
{{--                <td>{{$json_decode_list->max_band_number}}</td>--}}
{{--                <td>{{$json_decode_list->min_band_capacity}}</td>--}}
{{--                <td>{{$json_decode_list->max_band_capacity}}</td>--}}
{{--                <td>{!!  isset($json_decode_list->placed_in_warehouse)?"<i class='fa fa-check'></i>" :""!!}</td>--}}
{{--                <td>{!!isset($json_decode_list->has_number_ability)?"<i class='fa fa-check'></i>":"" !!}</td>--}}
{{--                <td>{!!isset($json_decode_list->system_can_define_new_carrier)?"<i class='fa fa-check'></i>":"" !!}</td>--}}


{{--            </tr>--}}
{{--        </tbody>--}}

{{--    </table>--}}
{{--</div>--}}
{{--</div>--}}
{{--<br/>--}}
{{--<br/>--}}