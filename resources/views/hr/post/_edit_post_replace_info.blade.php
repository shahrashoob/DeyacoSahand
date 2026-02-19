<div class="col-md-6">

    <input type="checkbox" id="for_leave_required_to_replace_person"
           name="for_leave_required_to_replace_person" {{$post->for_leave_required_to_replace_person?"checked":""}} >
    در زمان مرخصی/ماموریت/غیبت پست، آیا نیاز به انتخاب پست جانشین می باشد؟


</div>

<div class="col-md-12" id="post_replace_list"
     style="display:{{$post->for_leave_required_to_replace_person?"block":"none"}}">
    <h5>لیست پست های جانشین</h5>
    <div class="row">
        @foreach($replace_post as $item)
            <div class="col-md-2">
                <input type="checkbox"
                       name="data[post_replace][{{$item->id}}]" {{$post->has_post_replace($item->id)?"checked":""}}>
                {{$item->caption}}
            </div>
        @endforeach
    </div>
</div>
<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



