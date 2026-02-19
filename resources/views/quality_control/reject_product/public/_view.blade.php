<div class="row">
    @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$reject_product_form->applicant->fullCaption()])
    @include("component.input._lable",["label"=>"وضعیت","value"=>$reject_product_form->status->caption??""])
    @include("component.input._lable",["label"=>"تاریخ و زمان ثبت","value"=>$reject_product_form->get_create_date_and_time()])
    @include("component.input._lable",["label"=>"علت مرجوعی","value"=>$reject_product_form->reject_product_reason_type->caption??""])

    <div class="table-responsive">
        <table class="table table-styling center" style="">
            <thead>
            <tr>
                <th>ردیف</th>
                <th>کد بسته بندی</th>
                <th> مقدار مرجوعی</th>
                <th> بسته سالم است</th>
            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($reject_product_form->items as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>{{$item->packing_form->getCode()}}</td>
                    <td>{{$item->packing_form->getFinalAmount()}}</td>
                    <td>{!! $item->packing_is_safe==1?"<i class='fa fa-check'/>":"" !!}

                    </td>


                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


</div>
