<div class="row">
    <div class="col-md-12" style="overflow: auto">

        <h5>
            لیست ورژن های
            {{$product->fullCaption()}}
            <br/>
        </h5>
            @php $row=1;@endphp
            <table class="table table-styling center">
                <tr>
                    <th>کد ورژن</th>
                    <th>تاریخ</th>
                    <th>اقدام کننده</th>
                    <th colspan="5">دلیل ایجاد ورژن</th>



{{--                    <th>واحد اصلی</th>--}}
{{--                    <th>واحد فرعی</th>--}}
{{--                    <th>واحد فرعی2</th>--}}
{{--                    <th>نسبت واحد فرعی--}}
{{--                        <br/>--}}
{{--                        2به واحد اصلی--}}
{{--                    </th>--}}
{{--                    <th>--}}
{{--                        وزن--}}
{{--                        <br/>--}}
{{--                        (کیلوگرم)--}}

{{--                    </th>--}}


                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>تغییر در ستون های اصلی کالا </th>
                    <th>تغییر مشخصات کالا </th>
{{--                    <th>تغییر کالای مصرفی </th>--}}
{{--                    <th> BOM </th>--}}
                    <th> اضافه شدن SP
                    <br/>
                        کالای جایگزین تولید
                    </th>


                    <th colspan="7"></th>


                </tr>
                @foreach($product->product_versions as $item)
                    <tr>
                        <td>{{$item->version_code}}</td>
                        <td>{{$item->get_datetime()}}</td>
                        <td>{{$item->worker->fullName()}}</td>


                        <td>
                            <a href="{{route($route_path."details",[$product,"product_cols",$item??0,$product_creation_process])}}"> {!! $item->change_product_cols?"<span class='fa fa-check' ></span>":"" !!}</a>
                        </td>
                        <td>
                            <a href="{{route($route_path."details",[$product,"property",$item??0,$product_creation_process])}}"> {!! $item->change_property?"<span class='fa fa-check' ></span>":"" !!}</a>
                        </td>
{{--                        <td>--}}
{{--                            <a href="{{route($route_path"details",[$product,"consume",$item??0,$product_creation_process])}}"> {!! $item->change_consume?"<span class='fa fa-check' ></span>":"" !!}</a>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <a href="{{route($route_path"details",[$product,"bom",$item??0,$product_creation_process])}}"> {!! $item->change_bom?"<span class='fa fa-check' ></span>":"" !!}</a>--}}
{{--                        </td>--}}
                        <td>
                            <a href="{{route($route_path."details",[$product,"bom_permutation",$item??0,$product_creation_process])}}"> {!! $item->change_bom_permutation?"<span class='fa fa-check' ></span>":"" !!}</a>
                        </td>


{{--                        <td>--}}
{{--                            {{$item->unit->caption??""}}--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            {{$item->sub_unit->caption??""}}--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            {{$item->sub_unit2->caption??""}}--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            {{$item->weight??""}}--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            {{$item->frame_ratio_unit2??""}}--}}
{{--                        </td>--}}
                    </tr>
                @endforeach
            </table>

        @include($view_path."_btn_list".($custom_route??""))

    </div>
</div>



