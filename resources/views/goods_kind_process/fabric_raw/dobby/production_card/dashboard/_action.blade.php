<a href="{{route("production.dashboard.list")}}" class="btn btn-outline-dark">بازگشت</a>

<a href="{{route("production.print_card",$production)}}" class="btn btn-info">پرینت کردن
    کارت</a>

@foreach($controller_info as $name=>$info)
    {{--     Enable Status--}}
    @if(in_array(\Illuminate\Support\Str::substr($production->waiting_status_id,-3),$info["enable_status"]))
        {{--       Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))
            <a href="{{route($info["route"]."index",$production)}}" class="btn {{$info["button"]["class"]}}">
                {{$info["button"]["caption"]}}
            </a>
        @endif
    @endif

@endforeach
