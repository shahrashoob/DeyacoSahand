@if(count($form_general_item_list)>0)

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های ورود به انبار </h5>
            </div>
            <div class="card-block">

                <div class="row">
                    <div class="col-sm-12" style="overflow: auto">

                        <table class="table table-styling center">
                            <tr>
                                <th>درجه</th>
                                <th>لات</th>
                                <th>نوع بسته بندی</th>
                                <th>تعداد بسته بندی</th>
                                <th>{{$contractor_allocation->product->unit->measurement}} کل</th>
                                @if($contractor_allocation->product->sub_unit)
                                    <th>{{$contractor_allocation->product->sub_unit->measurement}} کل</th>
                                @endif
                                <th>مبلع</th>
                                <th>ارزش افزوده</th>
                                <th>مبلع کل</th>
                                <th>فرم انبار</th>
                                <th>وضعیت</th>
                            </tr>
                            <tr>
                                @foreach($form_general_item_list as $item)
                                    <td>{{$item->degree->caption??""}}</td>
                                    <td>{{$item->lot_number->code??""}}</td>
                                    <td>{{$item->packing_type->fullCaption()}}</td>
                                    <td>{{$item->packing_form_number??""}}</td>
                                    <td>{{$item->amount??""}}</td>
                                    @if($contractor_allocation->product->sub_unit)
                                        <td>{{$item->sub_amount??""}}</td>
                                    @endif
                                    <td>{{$item->price??""}}</td>
                                    <td>{{$item->tax_price??""}}</td>
                                    <td>{{$item->total_price_with_tax??""}}</td>
                                    <td>
                                        {{$item->form->code??""}}
                                    </td>
                                    <td>
                                        {{$item->form->status->caption??""}}
                                    </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>

                    <br/>
                </div>
            </div>
        </div>
    </div>
@endif
