@if(isset($back_url) && $back_url!="")
    <a class="btn btn-outline-dark" href="{{route($back_url)}}">بازگشت</a>
@endif
@foreach($controller_info as $name=>$info)
    {{--        ثبت دسترسی مربوط به اضافه کاری ، مرخصی و ... --}}
    @if(
	in_array(\Illuminate\Support\Str::substr($worker->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)

        @if( $post_user->checkButtonPermission($info["route"]."index",false,true))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$worker)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf

                    <button type="submit"
                            class="btn btn_action {{$info["button"]["class"]}}"
                            onclick="return confirm('{{$info["message"]["confirm"]}}')"
                    >
                        <i class="{{$info["button"]["icon"]}}"></i>
                        {{$info["button"]["caption"]}}
                    </button>
                </form>
            @else
                <a
                    href="{{route($info["route"]."index",$worker)}}"
                    class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif
    @endif

@endforeach

@php $list_show=[];@endphp

@foreach($post_user_list as $item)


    @if( $item->post->shift_delivery_module && $show_btn_delivery && !isset($list_show[$item->post->shift_delivery_module_id]["index"]))
        {{--        نمایش دکمه تحویل شیفت--}}
        <a
            href="{{route("hr.shift_delivery.module".$item->post->shift_delivery_module_id.".index",[$item->post->shift_delivery_module,$item->post])}}"
            class="btn btn-primary"
        >
            {{$item->post->shift_delivery_module->caption}}
        </a>
        @php $list_show[$item->post->shift_delivery_module_id]["index"]=1;@endphp
    @endif

    @if($item->post->shift_delivery_module &&  isset($show_btn_confirm_posts[$item->post->shift_delivery_module_id])  && !isset($list_show[$item->post->shift_delivery_module_id]["waiting_delivery"]))
        {{--                نمایش دکمه تایید تحویل شیفت--}}
        <a
            href="{{route("hr.shift_delivery.module".$item->post->shift_delivery_module_id.".waiting_delivery",[$item->post->shift_delivery_module_id,$item->post])}}"
            class="btn btn-primary"
        >
            تایید {{$item->post->shift_delivery_module->caption}}
        </a>
        @php $list_show[$item->post->shift_delivery_module_id]["waiting_delivery"]=1;@endphp
    @else

    @endif
@endforeach

@if(count($illegal_delivery_ids) > 0)
    <a
        href="{{route("hr.shift_delivery.illegal_delivery.index",$item->post)}}"
        class="btn btn-primary"
    >
      تحویل
        {{count($illegal_delivery_ids)}}
        ماشین که در شیف شما قرار ندارد
    </a>
@endif
{{--@if($item->post->shift_delivery_module )--}}

{{--    <a--}}
{{--        href="{{route("hr.shift_delivery.module".$item->post->shift_delivery_module_id.".show_efficiency",[$item->post->shift_delivery_module_id,$item->post])}}"--}}
{{--        class="btn btn-primary"--}}
{{--    >--}}
{{--       مشاهده راندمان--}}
{{--    </a>--}}

{{--@endif--}}

@if($worker_id_equal_auth_id)
    <a   class="btn btn-primary" href="{{route("utility.special_license.panel.new_special_license.index",[16,$worker->id])}}">

        <i class="fa fa-unlock-alt"></i>
        مجوز ثبت تردد
    </a>
@endif


