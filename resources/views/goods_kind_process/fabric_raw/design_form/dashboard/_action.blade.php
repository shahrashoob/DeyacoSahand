<a href="{{route("fabric_raw.design_form.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>


@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($design_form->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
        {{--        Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$design_form)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf

                    <button type="submit"
                            class="btn {{$info["button"]["class"]}}"
                            onclick="return confirm('{{$info["message"]["confirm"]}}')"
                    >
                        {{$info["button"]["caption"]}}
                    </button>
                </form>
            @else
                <a
                    href="{{route($info["route"]."index",$design_form)}}"
                    class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif
    @endif

@endforeach
