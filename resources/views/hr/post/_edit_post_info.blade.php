@include("component.input._text",["id"=>"caption","label"=>"عنوان پست","value"=>$post->caption])
{{--<div class="w-100"></div>--}}
{{--<div class="col-md-6">--}}
{{--    @include("component.input._aotocomplet2",[--}}
{{--        "id"=>"shift_id",--}}
{{--        "label"=>"شیفت  ",--}}
{{--        "option"=>$shift_option["items"],--}}
{{--        "val"=>$shift_option["value"],--}}
{{--        "text"=>$shift_option["text"],--}}
{{--        "class_col"=>""--}}
{{--        ])--}}
{{--</div>--}}

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"organization_category_id",
        "label"=>"رده سازمانی سازمانی ",
        "option"=>$organization_category_option["items"],
        "val"=>$organization_category_option["value"],
        "text"=>$organization_category_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"parent_id",
        "label"=>"پست مافوق  ",
        "option"=>$parent_option["items"],
        "val"=>$parent_option["value"],
        "text"=>$parent_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"active_status_id",
        "label"=>"وضعیت ",
        "option"=>$active_status_option["items"],
        "val"=>$active_status_option["value"],
        "text"=>$active_status_option["text"],
        "class_col"=>""
        ])
</div>

<div class="w-100"></div>
<div class="col-md-12">


    @include("component.input._checkbox",["id"=>"is_system_supervisor","label"=>" انتخاب به عنوان ناظر سیستم ","checked"=>$post->is_system_supervisor])
</div>
<div class="col-md-12">
    حداقل تعداد افراد در هر گروه شیفت پست
    <input name="min_person_number_in_shift_work" required="required"
           value="{{$post->min_person_number_in_shift_work}}" type="number" min=0 style="width: 50px"
    >
    نفر می باشد.
</div>
<br/>
<div class="col-md-12">
    حداکثر تعداد افراد در هر گروه شیفت پست
    <input name="max_person_number_in_shift_work" required="required"
           value="{{$post->max_person_number_in_shift_work}}" type="number" min=0 style="width: 50px"
    >
    نفر می باشد.
</div>



<br/>
<div class="col-md-12">
    <label class="form-label">نوع همکاری پست با سازمان </label>
    @foreach($cooperation_type_list as $item)

        <input class="myCheckBox_cooperation_type" type="checkbox" id="switch-data[{{$item->id}}]"
               name="data[cooperation_type][{{$item->id}}]" {{$post->has_cooperation_type($item->id)?"checked='checked'":""}}
        ">
        <b> {{$item->caption}}</b>

    @endforeach
    <br/>
</div>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>
@if($post_user->checkButtonPermission("hr.post.create"))
    <a href="{{route('hr.post.post_in_ic.index',[$post])}}" class="btn btn-outline-success">
        بروزرسانی از منظومه داده ای</a>
@endif


