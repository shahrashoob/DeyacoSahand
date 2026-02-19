<div class="col-md-6">
    برای اضافه کاری
    باید
    <input name="for_work_overtime_a_few_top_levels_must_confirm" required="required"
           value="{{$post->for_work_overtime_a_few_top_levels_must_confirm}}" type="number" min=0 style="width: 50px"
    >
    سطح بالایی تایید کنند.

    <br/>
    <br/>


    برای اضافه کاری بیش از
    <input name="for_work_overtime_a_few_top_levels_must_confirm_time" required="required"
           value="{{$post->for_work_overtime_a_few_top_levels_must_confirm_time}}" type="number" min=0 style="width: 50px"
    >
    ساعت،
    باید
    <input name="for_work_overtime_a_few_top_levels_must_confirm_time_level" required="required"
           value="{{$post->for_work_overtime_a_few_top_levels_must_confirm_time_level}}" type="number" min=0 style="width: 50px"
    >

    سطح بالایی تایید کنند.

    <br/>
    <br/>
    تعداد مواردی که فرد می تواند پس از انجام اضافه کاری، اضافه کاری خود را در سامانه ثبت نماید،
    <input name="for_worker_overtime_number_of_register_after_tacking" required="required"
           value="{{$post->for_worker_overtime_number_of_register_after_tacking}}" type="number" min=0 style="width: 50px"
    >
    بار در سال (سال مالی) است.

    <br/>
    <br/>
    تعداد مجوز اضافه کاری خودکار
    <input name="for_work_overtime_automatic_number" required="required"
           value="{{$post->for_work_overtime_automatic_number}}" type="number" min=0 style="width: 50px"
    >
    بار در سال (سال مالی) می باشد.



</div>


<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



