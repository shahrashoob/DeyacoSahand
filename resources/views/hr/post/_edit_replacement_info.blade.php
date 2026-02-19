<div class="col-md-6">
    برای جابجایی شیفت
    باید
    <input name="for_replacement_a_few_top_levels_must_confirm" required="required"
           value="{{$post->for_replacement_a_few_top_levels_must_confirm}}" type="number" min=0 style="width: 50px"
    >
    سطح بالایی تایید کنند.

    <br/>
    <br/>


    برای جابجایی شیفت بیش از
    <input name="for_replacement_a_few_top_levels_must_confirm_time" required="required"
           value="{{$post->for_replacement_a_few_top_levels_must_confirm_time}}" type="number" min=0 style="width: 50px"
    >
    ساعت،
    باید
    <input name="for_replacement_a_few_top_levels_must_confirm_time_level" required="required"
           value="{{$post->for_replacement_a_few_top_levels_must_confirm_time_level}}" type="number" min=0 style="width: 50px"
    >

    سطح بالایی تایید کنند.

    <br/>
    <br/>



</div>


<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



