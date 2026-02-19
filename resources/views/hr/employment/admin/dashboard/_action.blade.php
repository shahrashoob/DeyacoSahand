{{--@if(!in_array($employment->status_id,[4640100,4640109,]) )--}}
{{--    <a href="{{route('hr.employment.admin.dashboard.index')}}"--}}
{{--       class="btn btn btn-outline-dark ">بازگشت</a>--}}
{{--@endif--}}

@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($employment->status_id,-3),$info["enable_status"])

	        &&
	        (!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	        &&
	        !isset($info["hidden_button"])

	)
        {{--        Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$employment)}}"
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
                        href="{{route($info["route"]."index",$employment)}}"
                        class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif
    @endif

@endforeach
<div class="col-md-12">
    @if($employment->status_id== 4640123)
        <a class="btn btn-info" href="{{route("hr.employment.admin.customer.drafting_contract_final.download_contract",$employment)}}">
            دریافت  پیش نویس قرارداد مشتری
        </a>
    @endif
</div>

