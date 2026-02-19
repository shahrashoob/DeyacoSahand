<a href="{{route("wh.warehouse_handling.dashboard.index",)}}"    class="btn btn-outline-dark">بازگشت</a>


@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($warehouse_handling->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
{{--                Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$warehouse_handling)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf

                    <button type="submit"
                            class="btn_action btn {{$info["button"]["class"]}}"
                            onclick="return confirm('{{$info["message"]["confirm"]}}')"
                    >
                        {{$info["button"]["caption"]}}
                    </button>
                </form>
            @else
                <a
                    href="{{route($info["route"]."index",$warehouse_handling)}}"
                    class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif
    @endif

@endforeach

@foreach($controller_info_public as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($warehouse_handling->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
{{--                Permission--}}

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$warehouse_handling)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf

                    <button type="submit"
                            class="btn_action btn {{$info["button"]["class"]}}"
                            onclick="return confirm('{{$info["message"]["confirm"]}}')"
                    >
                        {{$info["button"]["caption"]}}
                    </button>
                </form>
            @else
                <a
                    href="{{route($info["route"]."index",$warehouse_handling)}}"
                    class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif

@endforeach
