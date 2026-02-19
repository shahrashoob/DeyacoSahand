<div class="col-md-6">
    برای مرخصی
    باید
    <input name="for_leave_a_few_top_levels_must_confirm" required="required"
           value="{{$post->for_leave_a_few_top_levels_must_confirm}}" type="number" min=0 style="width: 50px"
    >
    سطح بالایی تایید کنند.

    <br/>
    <br/>


    برای مرخصی بیش از
    <input name="for_leave_a_few_top_levels_must_confirm_time" required="required"
           value="{{$post->for_leave_a_few_top_levels_must_confirm_time}}" type="number" min=0 style="width: 50px"
    >
    ساعت،
    باید
    <input name="for_leave_a_few_top_levels_must_confirm_time_level" required="required"
           value="{{$post->for_leave_a_few_top_levels_must_confirm_time_level}}" type="number" min=0 style="width: 50px"
    >

    سطح بالایی تایید کنند.

    <br/>
    <br/>

    تعداد مرخصی اضطراری در سال
    <input name="emergency_leave_number_in_year" required="required"
           value="{{$post->emergency_leave_number_in_year}}" type="number" min=0 style="width: 50px"
    >

    بار می باشد.

    <br/>
    <br/>
    مقدار مرخصی ازدواج در سال
    <input name="marriage_leave_time_in_year" required="required"
           value="{{$post->marriage_leave_time_in_year}}" type="number" min=0 style="width: 50px"
    >
    ساعت می باشد.

    <br/>
    <br/>
    مقدار مرخصی فوت اقوام در سال
    <input name="death_of_relatives_leave_time_in_year" required="required"
           value="{{$post->death_of_relatives_leave_time_in_year}}" type="number" min=0 style="width: 50px"
    >
    ساعت می باشد.
    <br/>
    <br/>
    تعداد مواردی که فرد می تواند پس از انجام مرخصی، مرخصی خود را در سامانه ثبت نماید،
    <input name="for_leave_number_of_register_after_tacking" required="required"
           value="{{$post->for_leave_number_of_register_after_tacking}}" type="number" min=0 style="width: 50px"
    >
    بار در سال (سال مالی) است.


</div>


<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



