

@foreach($controller_info_bands as $name=>$info)

    @if(
	$production_form->status_id == 7002003 && // وضعیت فرم تولید در انتظار درجه بندی
	in_array($production_form_item->status_id,[0])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)

        @if(isset($info["message"]["confirm"]))
            <form id="form1" action="{{route($info["route"]."submit",$production_form_item)}}"
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
                href="{{route($info["route"]."index",$production_form_item)}}"
                class="btn {{$info["button"]["class"]}}"
            >
                {{$info["button"]["caption"]}}
            </a>
        @endif
    @endif

@endforeach
