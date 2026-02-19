<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> لیست فرم های بسته بندی </h5>
        </div>
        <div class="card-block" style="overflow: auto">

            <table class="table table-styling center">
                <tr>
                    <th></th>
                    <th>کد کالا</th>
                    <th>نام کالا</th>
                    <th>شماره حامل</th>
                    <th>شماره فرم انبار</th>
                    <th>کد بسته بندی</th>
                    <th>نوع بسته بندی</th>
                    <th>تعداد بسته بندی<br/> فرعی/اقلام</th>
                    <th>مقدار</th>
                    <th>مقدار<br/> فرعی</th>
                    <th>وضعیت</th>

                </tr>
                @if(count($contractor_packing_list)>0)

                    @php $row=1;@endphp
                    @foreach($contractor_packing_list as $item)
                        <tr>
                            <td>{{$row++}}</td>
                            <td>{{$contractor_allocation->product->code}}</td>
                            <td>{{$contractor_allocation->product->caption}}</td>
                            <td>{{$item->packing_form->carrier->code??""}}</td>
                            <td>{{$item->packing_form->form->code??"---"}}</td>
                            <td>
                                {{$item->packing_form->getCode()}}

                            </td>
                            <td>{{$item->packing_form->packing_type->caption??""}}</td>
                            <td>{{$item->packing_form->getItemCount()}}</td>
                            <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                            <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>
                            <td>{{$item->packing_form->status->caption}}</td>

                        </tr>
                    @endforeach
                @endif
            </table>

        </div>
    </div>
</div>

