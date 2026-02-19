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
                            <th>
                                <input type="checkbox" id="reject_packing_check_all">
                            </th>
                            <th>ردیف</th>
                            <th>کد بسته بندی</th>
                            <th> کد کالا</th>
                            <th> نام کالا</th>
                            <th>حامل</th>
                            <th>  مقدار اصلی</th>
                            <th>
                                مقدار فرعی
                            </th>
                            <th>درجه</th>
                            <th>بسته بندی حمل و نقل</th>
                            <th>تعداد آیتم</th>
                            <th> بسته بندی سالم</th>
                            <th> مقدار مرجوعی

                            </th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;
                            $form=\App\Models\Form\Form::find($form->id);
                        @endphp
                        @foreach($form->itemOrderByTransportCode("group_by_packing_form") as $item)
                            @php
                                $packing_form_id=$item->packing_form_item->packing_form_id;
                                $allow_reject=$item->packing_form_item->packing_form->allowRejectProduct();
                            @endphp
                            <tr style="background: {{$allow_reject?"#fff":"#f6cfcf"}}">
                                <td>
                                    @if($allow_reject)
                                        <input id="reject_packing_{{$packing_form_id}}"
                                               name="data[reject_packing][{{$packing_form_id}}]" type="checkbox"
                                               data-id="{{$packing_form_id}}"
                                               class="reject_packing hidden">
                                    @endif
                                </td>
                                <td style="padding: 20px">{{++$row}}</td>
                                <td>  {{$item->packing_form_item->packing_form->code}}</td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>

                                <td>{{$item["carrier"]}}</td>

                                <td>{{$item->amount}}</td>
                                <td>{{$item->sub_amount}}</td>
                                <td>  {{$item->packing_form_item->degree->caption??""}}</td>
                                <td>{{$item->packing_form_item->packing_form->getTransportPackingForm("transport_item_code")}}</td>
                                <td>
                                    {{$item["item_count"]}}
                                </td>
                                <td>
                                    @if($allow_reject)
                                        <input id="packing_is_safe_{{$packing_form_id}}"
                                               name="data[packing_is_safe][{{$packing_form_id}}]" type="checkbox"
                                               data-id="{{$packing_form_id}}"
                                               class="packing_is_safe hidden" checked>
                                    @endif

                                </td>
                                <td>
                                    @if($allow_reject)
                                        <input id="amount_remaining_{{$packing_form_id}}"
                                               name="data[amount_remaining][{{$packing_form_id}}]"
                                               data-id="{{$packing_form_id}}" type="number" style="width: 60px" value=""
                                               max="{{$item->amount}}" min="{{$item->amount*  $percent_allow_to_reject_packing_form / 100}}" class="amount_remaining hidden "
                                               required>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                        <tr style="font-weight: bold;font-size: 16px">
                            <td colspan="6">
                                جمع کل
                            </td>
                            <td>
                                {{$sum_amount}}
                            </td>
                            <td>
                                {{$sum_sub_amount}}
                            </td>
                            <td colspan="6">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>


        </div>


    </div>

</div>
