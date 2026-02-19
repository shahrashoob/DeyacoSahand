
<div class="col-md-12">


    @include("component.input._checkbox",["id"=>"the_worker_has_permission_to_leave_after_entering","label"=>"افزاد شاغل در پست پس از ورود مجوز خروج دارند؟","checked"=>$post->the_worker_has_permission_to_leave_after_entering])
    @include("component.input._checkbox",["id"=>"should_complete_the_duration_of_operation","label"=>"آیا شاغلین مشغول در پست باید مدت زمان کارکرد روزانه را تکمیل نمایند","checked"=>$post->should_complete_the_duration_of_operation])

</div>
        <div class="col-md-12">
            <div class="row">
                @include("component.input._aotocomplet2",[
            "id"=>"traffic_notification_for_post_id",
            "label"=>"پست سازمانی جهت اطلاع رسانی تردد",
            "option"=>$traffic_notification_for_post_option["items"],
            "val"=>$traffic_notification_for_post_option["value"],
            "text"=>$traffic_notification_for_post_option["text"],
            "class_col"=>"col-md-6"
            ])
            </div>
        </div>

<div class="col-md-12">
    تعجیل مجاز برای ورود به سازمان
    <input name="allowed_earlier_time_for_entry" required="required"
           value="{{$post->allowed_earlier_time_for_entry}}" type="number" min=0 style="width: 50px"
    >

    دقیقه می باشد.

    <br/>
    <br/>
    تاخیر مجاز برای ورود به سازمان
    <input name="allowed_delay_time_for_entry" required="required"
           value="{{$post->allowed_delay_time_for_entry}}" type="number" min=0 style="width: 50px"
    >

    دقیقه می باشد.

    <br/>
    <br/>
    تعجیل مجاز برای خروج از سازمان
    <input name="allowed_earlier_time_for_exit" required="required"
           value="{{$post->allowed_earlier_time_for_exit}}" type="number" min=0 style="width: 50px"
    >

    دقیقه می باشد.

    <br/>
    <br/>
    تاخیر مجاز برای خروج از سازمان
    <input name="allowed_delay_time_for_exit" required="required"
           value="{{$post->allowed_delay_time_for_exit}}" type="number" min=0 style="width: 50px"
    >

    دقیقه می باشد.

    <br/>
    <br/>
</div>


<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>
