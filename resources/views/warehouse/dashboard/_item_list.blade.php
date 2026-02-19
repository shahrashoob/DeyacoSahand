@if(count($form->item)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست آیتم های بسته بندی ها</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>{{$form->item->first()->product->unit->measurement}}</th>
                            <th>{{$form->item->first()->product->sub_unit->measurement??""}}</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;
  $form=\App\Models\Form\Form::find($form->id);
 @endphp
                        @foreach($form->item as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                @php $show_packing_code=isset($show_packing_code)?$show_packing_code:
                                    (isset($packing_form_id_where_put_in_warehouse[$item->packing_form_item->packing_form_id])?1:0);

                            @endphp
                                <td>{{$item->packing_form_item?$item->packing_form_item->getCode(false,false,$show_packing_code):""}} </td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>
                                <td>{{round($item->amount,2)}}</td>
                                <td>{{$item->packing_form_item?($item->packing_form_item->product->unit_id2 && $item->sub_amount > 0?round($item->sub_amount,4):""):""}}</td>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif
