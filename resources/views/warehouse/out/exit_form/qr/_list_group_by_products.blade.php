@if(count($form->item)>0)
    @php
        $itemOrderByTransportCode=$form->itemOrderByTransportCode("group_by_product");
        $allow_show_sub_amount=$itemOrderByTransportCode[0]->packing_form_item->product->goods_kind->allow_show_sub_amount_in_exit_forms??1;

    @endphp
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>
                    آیتم های
                    {{isset($caption)?$caption:"برگ خروج"}}
                    به تفکیک کالا

                </h5>
            </div>

            <div class="card-block">


                <div class="row">

                    <div class="table-responsive ">
                        <table class="table table-styling center" style="width: 600px; margin: auto">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th> کد کالا</th>
                                <th> نام کالا</th>
                                <th>  {{$unit->caption}}</th>
                                @if($sub_unit && $allow_show_sub_amount)
                                    <th>
                                        {{$sub_unit->caption}}
                                    </th>
                                @endif
                                <th>درجه</th>
{{--                                <th> توضیحات</th>--}}
                            </tr>

                            </thead>
                            <tbody>
                            @php
                                $row=0;
                                $form=\App\Models\Form\Form::find($form->id);
                            @endphp
                            @foreach($itemOrderByTransportCode as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->product->code}}</td>
                                    <td>{{$item->product->caption}}</td>


                                    <td>{{$item->amount}}</td>
                                    @if($sub_unit && $allow_show_sub_amount)
                                        <td>{{$item->sub_amount}}</td>
                                    @endif
                                    <td>  {{$item->degree->caption??""}}</td>
{{--                                    <td>{!! $item["description"] !!}</td>--}}
                                </tr>

                            @endforeach

                            <tr style="font-weight: bold;font-size: 16px">
                                <td colspan="3">
                                    جمع کل
                                </td>
                                <td>
                                    {{$sum_amount}}
                                </td>
                                @if($sub_unit && $allow_show_sub_amount)
                                    <td>
                                        {{$sum_sub_amount}}
                                    </td>
                                @endif
                                <td colspan>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                </div>


            </div>


        </div>

    </div>
@endif
