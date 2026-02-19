<a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
   class="btn btn-outline-dark">بازگشت</a>

@if( $product_creation_process->has_physical_sample )
    <a class="btn btn-primary"
       href="{{route("line_product_station.product.product_creation.dashboard.show_print_form",$product_creation_process)}}"
    >
        <i class="fa fa-download"></i> مشاهده فرم
    </a>
@endif


@foreach($controller_info as $name=>$info)

    @if(
	in_array(\Illuminate\Support\Str::substr($product_creation_process->status_id,-3),$info["enable_status"])

	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))
	)
        {{--        Permission--}}
        @if( $post_user->checkButtonPermission($info["route"]."index"))

            @if(isset($info["message"]["confirm"]))
                <form id="form1" action="{{route($info["route"]."submit",$product_creation_process)}}"
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
                        @if(isset($button_list[$info["button_id"]]))
                            ({{$button_list[$info["button_id"]]}})
                        @endif
                    </button>
                </form>
            @else
                <a
                        href="{{route($info["route"]."index",$product_creation_process)}}"
                        class="btn {{$info["button"]["class"]}}"
                >
                    {{$info["button"]["caption"]}}
                </a>
            @endif
        @endif
    @endif

@endforeach


{{--بازگشت به مرحله قبل--}}
@if(count($before_status_list)>0)
    <div class="btn-group mb-2 mr-2 show">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">بازگشت به مراحل قبل</button>
        <div class="dropdown-menu " x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
          @foreach($before_status_list as $status_id =>$status_caption)
            <a class="dropdown-item" href="{{route($route_path."go_to_before_step",[$product_creation_process,$status_id])}}">{{$status_caption}}</a>
            @endforeach
        </div>
    </div>
@endif