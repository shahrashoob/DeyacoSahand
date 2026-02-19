<a href="{{route("production.dashboard.list")}}" class="btn btn-outline-dark">بازگشت</a>

<a href="{{route("production.print_card",$production)}}" class="btn btn-info">پرینت کردن
    کارت</a>

<a href="{{route("production.dashboard.cancel",$production)}}"
   class="btn btn-danger">کنسل کردن </a>

@foreach($controller_info as $name=>$info)
    @if(in_array(\Illuminate\Support\Str::substr($production->waiting_status_id,-3),$info["enable_status"]))
        <a href="{{route($info["route"]."index",$production)}}" class="btn {{$info["button"]["class"]}}">
            {{$info["button"]["caption"]}}
        </a>
    @endif

@endforeach
