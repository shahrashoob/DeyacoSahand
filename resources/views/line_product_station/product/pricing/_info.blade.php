@if($add_pricing)
    <form id="form1" action="{{route($route_path."submit_add_pricing_to_tariff",[$product,$product_creation_process])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        @endif
        <div class="row" style="overflow: auto">
            <div class="col-md-12">

                <h5>
                    @if($add_tariff_rows)
                        <a href="{{route($route_path."create_product_tariff",[$product,$product_creation_process??null])}}"
                           class="btn btn-outline-success">افزودن ردیف جدید به تعرفه</a>
                    @endif
                </h5>
                <br/>


                @php $row=$product_tariffs->firstItem();@endphp
                <div style="overflow: auto">
                    <table class="table col-md-6 center">
                        <tr>
                            <th>ردیف</th>
                            <th>تعرفه</th>
                            <th>کد کالا</th>
                            <th> نام کالا</th>
                            <th>درجه</th>
                            <th>نوع بسته بندی</th>
                            <th>انبار</th>
                            <th>نوع فروش</th>
                            <th>نام و کد خدمت</th>
                            <th>حداقل خرید ({{$product->unit->caption}})</th>
                            <th>حداکثر خرید ({{$product->unit->caption}})</th>
                            <th>قیمت واحد (ریال)</th>
                            @if($receive_the_consumer_price_in_the_pricing)
                                <th>قیمت مصرف کننده</th>
                            @endif
                            <th>به قیمت واحد فاکتور با <br/>توجه به راس پرداخت،<br/> n درصد به ازای <br/>هر روز اضافه شود</th>

                            <th>درصد عوارض</th>
                            <th>درصد مالیات بر <br/>ارزش افزوده</th>
                            <th>نام و کد
                                <br/>
                                کالای مشتری
                            </th>


                        </tr>
                        @if(isset($is_pricing))
                            <tr>
                                <td colspan="10"></td>
                                <td>قیمت عمومی</td>
                                <td>
                                    <input id="public_fea" class="numeric" type="text"
                                           style="width: 100px"
                                           value="">
                                </td>
                                @if($receive_the_consumer_price_in_the_pricing)
                                    <td>
                                        <input id="public_consumer_price" class="numeric" type="text"
                                               style="width: 100px"
                                               value="">
                                    </td>
                                @endif
                                <td colspan="3"></td>
                            </tr>
                        @endif
                        @foreach($product_tariff_pricing as $item)
                            <tr class="alert-warning">
                                <td>{{++$row}}</td>
                                <td>
                                    {{$item->tariff->caption}}
                                </td>
                                <td>
                                    {{$item->product->code}}
                                </td>
                                <td>
                                    {{$item->product->caption}}
                                </td>
                                <td>
                                    {{$item->degree->caption}}
                                </td>
                                <td>
                                    {{$item->packing_type->caption}}
                                </td>
                                <td>
                                    {{$item->warehouse->caption}}
                                </td>
                                <td>
                                    {{$item->type_of_sale_of_product->caption}}
                                </td>
                                <td>
                                    {{$item->service_id?$item->service->fullCaption():""}}
                                </td>

                                <td>
                                    {{number_format($item->min_buy)}}
                                </td>
                                <td>
                                    {{number_format($item->max_buy)}}
                                </td>
                                @if($add_pricing)

                                    <td>
                                        <input class="numeric fea_input" type="text" name="fea_{{$item->id}}"
                                               style="width: 100px"
                                               value="">
                                    </td>
                                    @if($receive_the_consumer_price_in_the_pricing)
                                        <td>
                                            <input class="numeric consumer_price_input" type="text"
                                                   name="consumer_price_{{$item->id}}"
                                                   style="width: 100px"
                                                   value="">
                                        </td>
                                    @endif
                                @else
                                    <td></td>
                                    @if($receive_the_consumer_price_in_the_pricing)
                                        <td></td>
                                    @endif
                                @endif
                                <td>
                                    {{$item->increase_percentage_deadline_per_day}}
                                </td>
                                <td>
                                    {{$item->tax}}
                                </td>
                                <td>
                                    {{$item->fare}}
                                </td>
                                <td>
                                    @if($item->customer_product_code)
                                        {{$item->customer_product_code}} -
                                        {{$item->customer_product_caption}}
                                    @endif
                                </td>
                                <th>
                                    @if(isset($allow_delete_item) && $allow_delete_item)

                                        <a href="{{route($route_path."remove_product_tariff_pricing",[$product,$item,$product_creation_process??null])}}"
                                           class="text-danger"
                                           onclick="return confirm('آیا از حذف ردیف تعریف اطمینان دارید؟')"
                                        >
                                            <span class="fa fa-trash"></span>
                                        </a>
                                        <a href="{{route($route_path."add_tariff_together",[$product,$item,$product_creation_process??null])}}"
                                           class="text-primary"
                                        >
                                            <span class="fa fa-share-alt"></span>
                                        </a>
                                    @endif
                                </th>

                            </tr>
                        @endforeach
                        @foreach($product_tariffs as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    {{$item->tariff->caption}}
                                </td>
                                <td>
                                    {{$item->product->code}}
                                </td>
                                <td>
                                    {{$item->product->caption}}
                                </td>
                                <td>
                                    {{$item->degree->caption}}
                                </td>
                                <td>
                                    {{$item->packing_type->caption}}
                                </td>
                                <td>
                                    {{$item->warehouse->caption}}
                                </td>
                                <td>
                                    {{$item->type_of_sale_of_product->caption}}
                                </td>
                                <td>
                                    {{$item->service_id?$item->service->fullCaption():""}}
                                </td>
                                <td>
                                    {{number_format($item->min_buy)}}
                                </td>
                                <td>
                                    {{number_format($item->max_buy)}}
                                </td>

                                <td>
                                    {{number_format($item->fea)}}
                                </td>
                                @if($receive_the_consumer_price_in_the_pricing)
                                    <td>
                                        {{$item->consumer_price?number_format($item->consumer_price):""}}
                                    </td>
                                @endif
                                <td>
                                    {{$item->increase_percentage_deadline_per_day}}
                                </td>
                                <td>
                                    {{$item->tax}}
                                </td>
                                <td>
                                    {{$item->fare}}
                                </td>

                                <td>
                                    @if($item->customer_product_code)
                                        {{$item->customer_product_code}} -
                                        {{$item->customer_product_caption}}
                                    @endif
                                </td>
                                <th>
                                    @if(isset($allow_delete_item) && $allow_delete_item)
                                        <a href="{{route($route_path."edit_product_tariff_pricing",[$product,$item,$product_creation_process??null])}}"
                                           class="text-primary"

                                        >
                                            <span class="fa fa-pen"></span>
                                        </a>
                                        <a href="{{route($route_path."remove_product_tariff",[$product,$item,$product_creation_process??null])}}"
                                           class="text-danger"
                                           onclick="return confirm('آیا از حذف ردیف تعریف اطمینان دارید؟')"
                                        >
                                            <span class="fa fa-trash"></span>
                                        </a>

                                    @endif
                                </th>

                            </tr>
                        @endforeach

                    </table>
                </div>

                {{--                <div class="float-left">--}}
                {{--                    نمايش رکوردهای--}}
                {{--                    <b>{{$product_tariffs->firstItem()}}</b>--}}
                {{--                    تا--}}
                {{--                    <b>{{$product_tariffs->lastItem()}}</b>--}}
                {{--                    از--}}
                {{--                    <b>{{$product_tariffs->total()}}</b>--}}
                {{--                    رکورد موجود--}}


                {{--                </div>--}}
            </div>


        </div>
        <div class="text-center">
            {{$product_tariffs->links('pagination::bootstrap-4')}}
        </div>
        <div style="position: sticky; right: 0; z-index: 1000; width: 450px">
            @include($view_path."_btn_list")

            @if(count($product_tariff_pricing)>0 &&     $add_pricing)
                <button type="submit"
                        class="btn btn-primary">ثبت قیمت برای ردیف های تعرفه
                </button>
            @endif
        </div>

        @if($add_pricing)
    </form>
@endif
@include("component.input._seperated_number_3")
<script>

    $("#public_fea").change(function () {
        $(".fea_input").val($(this).val())
    })
    $("#public_consumer_price").change(function () {
        $(".consumer_price_input").val($(this).val())
    })
</script>