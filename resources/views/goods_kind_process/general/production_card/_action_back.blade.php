@if(isset($back_url_type))
    @switch($back_url_type)
        @case("machine_index")
            <a href="{{route("production.machine.index")}}" class="btn btn-outline-dark">بازگشت</a>
            @break
        @default
            <a href="{{route("production.dashboard.list")}}" class="btn btn-outline-dark">بازگشت</a>
            @break
    @endswitch
@else
    <a href="{{route("production.dashboard.list")}}" class="btn btn-outline-dark">بازگشت</a>
@endif