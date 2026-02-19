<a href="{{route("production.production_form.index")}}" class="btn btn-outline-dark">بازگشت</a>
<div class="btn-group mb-2 mr-2">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">دریافت فرم تولید
    </button>
    <div class="dropdown-menu" x-placement="bottom-start"
         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">

        <a class="dropdown-item"
           href="{{route("fabric.production_form.download_form",[$production_form])}}">دانلود</a>

        <a class="dropdown-item"
           href="{{route("fabric.production_form.direct_print",[$production_form])}}">پرینت</a>

    </div>
</div>


@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($production_form->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
        {{--        Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$production_form)}}"
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
                        href="{{route($info["route"]."index",$production_form)}}"
                        class=" btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif

        @endif
    @endif

@endforeach
