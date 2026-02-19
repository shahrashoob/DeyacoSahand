@if(isset($back))
    <a href="{{route($back)}}" class="btn btn-outline-dark">بازگشت</a>
@else
    <a href="{{route("packing_form.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
@endif


@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($packing_form->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
        {{--        Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$packing_form)}}"
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
                    href="{{route($info["route"]."index",$packing_form)}}"
                    class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif

        @endif
    @endif

@endforeach

@if(isset($button["warehouse_button"]) )

    <form id="form1" action="{{route("wh.dashboard.confirm_packing_list",[$packing_form->form,$packing_form])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate"
          style="display: inline"
    >
        @csrf
        @include("component.input._hidden",["id"=>"packing_form_code","value"=>$packing_form->code])
        <button type="submit" class="btn btn-primary"
                onclick='return confirm("آیا از تایید تحویل کالا  اطمینان دارید؟")'>تایید انبار
        </button>
    </form>

@endif


