<a href="{{route("line_product_station.reservoir.definition.index")}}"
   class="btn btn-outline-dark">بازگشت</a>

<a href="{{route("line_product_station.reservoir.inject_to_reservoir.index",$reservoir)}}"
   class="btn btn-primary">تزریق کالا به مخزن</a>

<a href="{{route("line_product_station.reservoir.dashboard.log",$reservoir)}}"
   class="btn btn-info">سابقه عملیات</a>

{{--@if(isset($back))--}}
{{--    <a href="{{route($back)}}" class="btn btn-outline-dark">بازگشت</a>--}}
{{--@elseif(isset($back_url_route) && $back_url_route!="")--}}
{{--    <a href="{{route($back_url_route,[$back_url_route_id,$back_url_route_id2])}}" class="btn btn-outline-dark">بازگشت</a>--}}
{{--@elseif($packing_form->packing_form_master)--}}
{{--    <a href="{{route("fabric_raw.packing_form.view",$packing_form->packing_form_master)}}?page={{$page??1}}#dcpk{{$packing_form->id}}"--}}
{{--       class="btn btn-outline-dark">بازگشت</a>--}}

{{--@else--}}
{{--    <a href="{{route("fabric_raw.packing_form.index")}}?page={{$page??1}}#dcpk{{$packing_form->id}}"--}}
{{--       class="btn btn-outline-dark">بازگشت</a>--}}
{{--@endif--}}


{{--@foreach($controller_info as $name=>$info)--}}

{{--    @if(--}}
{{--	in_array(\Illuminate\Support\Str::substr($packing_form->status_id,-3),$info["enable_status"])--}}

{{--	        &&(!isset( $special_condition[$name]) || (isset( $special_condition[$name]) && $special_condition[$name]==true))--}}
{{--	)--}}
{{--        --}}{{--        Permission--}}
{{--        @if( $post_user->checkButtonPermission($info["route"]."index"))--}}

{{--            @if(isset($info["message"]["confirm"]))--}}
{{--                <form id="form1" action="{{route($info["route"]."submit",$packing_form)}}"--}}
{{--                      method="post"--}}
{{--                      autocomplete="off"--}}
{{--                      novalidate="novalidate"--}}
{{--                      style="display: inline"--}}
{{--                >--}}
{{--                    @csrf--}}

{{--                    <button type="submit"--}}
{{--                            class="btn_action btn {{$info["button"]["class"]}}"--}}
{{--                            onclick="return confirm('{{$info["message"]["confirm"]}}')"--}}
{{--                    >--}}
{{--                        {{$info["button"]["caption"]}}--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            @else--}}
{{--                <a--}}
{{--                        href="{{route($info["route"]."index",$packing_form)}}"--}}
{{--                        class="btn {{$info["button"]["class"]}}"--}}
{{--                >--}}
{{--                    {{$info["button"]["caption"]}}--}}
{{--                </a>--}}
{{--            @endif--}}

{{--        @endif--}}
{{--    @endif--}}

{{--@endforeach--}}

{{--@if(isset($button["warehouse_button"]) )--}}

{{--    <form id="form1" action="{{route("wh.dashboard.confirm_packing_list",[$packing_form->form,$packing_form])}}"--}}
{{--          method="post"--}}
{{--          autocomplete="off"--}}
{{--          novalidate="novalidate"--}}
{{--          style="display: inline"--}}
{{--    >--}}
{{--        @csrf--}}
{{--        @include("component.input._hidden",["id"=>"packing_form_code","value"=>$packing_form->code])--}}
{{--        <button type="submit" class="btn btn-primary"--}}
{{--                onclick='return confirm("آیا از تایید تحویل کالا  اطمینان دارید؟")'>تایید انبار--}}
{{--        </button>--}}
{{--    </form>--}}

{{--@endif--}}


