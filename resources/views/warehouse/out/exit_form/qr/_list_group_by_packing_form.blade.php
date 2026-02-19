@php
    $itemOrderByTransportCode=$form->itemOrderByTransportCode("group_by_packing_form",true);;
    $allow_show_sub_amount=$itemOrderByTransportCode[0]->packing_form_item->product->goods_kind->allow_show_sub_amount_in_exit_forms??1;

@endphp
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5> آیتم های برگ خروج به تفکیک بسته بندی </h5>
        </div>

        <div class="card-block">


            <div class="row">

                <div class="table-responsive ">
                    <table class="table table-styling center" style="width: 600px; margin: auto">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>کد بسته بندی</th>
                            <th> کد کالا</th>
                            <th> نام کالا</th>
                            <th>حامل</th>
                            <th>  {{$unit->caption}}</th>
                            @if($sub_unit && $allow_show_sub_amount)
                                <th>
                                    {{$sub_unit->caption}}
                                </th>
                            @endif
                            <th>درجه</th>
                            <th>لات</th>
                            <th>بسته بندی حمل و نقل</th>
                            <th>تعداد آیتم</th>
                            <th> خط ورودی</th>
                            <th> توضیحات</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php
                            $form=\App\Models\Form\Form::find($form->id);
							$list=$itemOrderByTransportCode;
							$row=$list->firstItem();
                        @endphp
                        @foreach($list as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a  target="_blank"
                                       href="{{route("fabric_raw.packing_form.view",$item->packing_form_item->packing_form_id)}}">
                                        {{$item->packing_form_item->packing_form->code}}
                                    </a>



                                </td>
                                <td>{{$item->product->code}}

                                </td>
                                <td>{{$item->product->caption}}
                                    @if(isset($item->packing_form_item->version_code))
                                        (V{{$item->packing_form_item->version_code}})
                                    @endif
                                </td>

                                <td>{{$item["carrier"]}}</td>

                                <td>{{$item->amount}}</td>
                                @if($sub_unit && $allow_show_sub_amount)
                                    <td>{{$item->sub_amount}}</td>
                                @endif
                                <td>  {{$item->packing_form_item->degree->caption??""}}</td>
                                <td>  {{$item->packing_form_item->lot_number->code??""}}</td>
                                <td>{{$item->packing_form_item->packing_form->getTransportPackingForm("transport_item_code")}}</td>
                                <td>
                                    {{$item["item_count"]}}
                                </td>
                                <td> {{$item["line_input"]}}</td>
                                <td>{!! $item["description"] !!}</td>
                            </tr>

                        @endforeach

                        @foreach($form->getMasterPackingFrom() as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>  {{$item->code}}</td>
                                <td>{{$item->items()->first()->product->code}}</td>
                                <td>{{$item->items()->first()->product->caption}}</td>

                                <td></td>

                                <td>{{round($item->items()->first()->final_amount,2)}}</td>
                                @if($sub_unit && $allow_show_sub_amount)
                                    <td>{{round($item->items()->first()->sub_amount,2)}}</td>
                                @endif
                                <td>  {{$item->items()->first()->degree->caption??""}}</td>
                                <td></td>
                                <td>

                                </td>
                                <td></td>
                                <td>
                                    شامل
                                    {{$item->packing_form_contents()->count()}}
                                    بسته بندی فرعی
                                </td>
                            </tr>

                        @endforeach

{{--                        <tr style="font-weight: bold;font-size: 16px">--}}
{{--                            <td colspan="5">--}}
{{--                                جمع کل--}}
{{--                            </td>--}}
{{--                            <td>--}}
{{--                                {{$sum_amount}}--}}
{{--                            </td>--}}
{{--                            @if($sub_unit)--}}
{{--                                <td>--}}
{{--                                    {{$sum_sub_amount}}--}}
{{--                                </td>--}}
{{--                            @endif--}}
{{--                            <td colspan="5">--}}

{{--                            </td>--}}
{{--                        </tr>--}}
                        </tbody>
                    </table>
                </div>

                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$list->firstItem()}}</b>
                    تا
                    <b>{{$list->lastItem()}}</b>
                    از
                    <b>{{$list->total()}}</b>
                    رکورد موجود

                </div>

            </div>

            <div class="text-center">
                {{$list->links('pagination::bootstrap-4')}}
            </div>
        </div>


    </div>

</div>
