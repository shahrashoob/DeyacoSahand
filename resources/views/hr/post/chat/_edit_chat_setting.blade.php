<div class="col-md-6">

    <input type="checkbox" id="can_chat_with_posts"
            {{$post->can_chat_with_posts?"checked":""}} >
    چت با همکار


</div>

<div class="col-md-12" id="post_chat_list"
     style="display:{{$post->can_chat_with_posts?"block":"none"}}; margin-right: 30px">
    <h5>لیست پست های سازمانی</h5>
    <div class="row">
        @foreach($post_list as $item)
            <div class="col-md-2">
                <input type="checkbox"
                       name="data[chat_with_posts][{{$item->id}}]" {{isset($chat_with_posts[$item->id])?"checked":""}}>
                {{$item->caption}}
            </div>
        @endforeach
    </div>
</div>
<div class="col-md-6">

    <input type="checkbox" id="chat_with_customers" name="chat_with_customers"
            {{$post->chat_with_customers?"checked":""}} >
    چت با مشتریان
</div>
{{--<div class="col-md-6">--}}

{{--    <input type="checkbox" id="chat_with_suppliers"  name="chat_with_suppliers"--}}
{{--            {{$post->chat_with_suppliers?"checked":""}} >--}}
{{--    چت با تامین کنندگان--}}
{{--</div><div class="col-md-6">--}}

{{--    <input type="checkbox" id="chat_with_contractors" name="chat_with_contractors"--}}
{{--            {{$post->chat_with_contractors?"checked":""}} >--}}
{{--    چت با پیمانکاران--}}
{{--</div>--}}

<br>
<br>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

<button type="submit" class="btn btn-primary"> ذخیره</button>



