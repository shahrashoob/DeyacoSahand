<div class="col-md-6">
    برای ماموریت
    باید
    <input name="for_mission_a_few_top_levels_must_confirm" required="required"
           value="{{$post->for_mission_a_few_top_levels_must_confirm}}" type="number" min=0 style="width: 50px"
    >
    سطح بالایی تایید کنند.

    <br/>
    <br/>


    برای ماموریت بیش از
    <input name="for_mission_a_few_top_levels_must_confirm_time" required="required"
           value="{{$post->for_mission_a_few_top_levels_must_confirm_time}}" type="number" min=0 style="width: 50px"
    >
    ساعت،
    باید
    <input name="for_mission_a_few_top_levels_must_confirm_time_level" required="required"
           value="{{$post->for_mission_a_few_top_levels_must_confirm_time_level}}" type="number" min=0 style="width: 50px"
    >

    سطح بالایی تایید کنند.

    <br/>
    <br/>
    تعداد مواردی که فرد می تواند پس از انجام ماموریت، ماموریت خود را در سامانه ثبت نماید،
    <input name="for_mission_number_of_register_after_tacking_to_posts" required="required"
           value="{{$post->for_mission_number_of_register_after_tacking_to_posts}}" type="number" min=0 style="width: 50px"
    >
    بار در سال (سال مالی) است.



</div>


<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



