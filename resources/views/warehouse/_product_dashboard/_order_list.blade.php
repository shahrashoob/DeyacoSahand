<div class="table-responsive">
    <table class="table table-styling" style="font-size: 11px !important">
        <thead>
        <tr>
            <th>#</th>
            <th>کد کالا</th>
            <th> عنوان کالا</th>
            <th>تعداد در کارتن</th>
            <th> مقدار درخواست</th>
            <th> مقدار تحویلی</th>
            @if($order->exit_status()==460000200)
                <th> مقدار تحویلی جدید</th>
            @endif
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($order->orderList as $item)
            <tr>
                <td>{{++$row}}</td>

                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}</td>
                <td>{{$item->product->number_in_carton}}</td>
                <td>{{$item->carton}} {{$item->product->unit->bach_caption}} </td>
                <td>{{$item->amount_sent}}</td>
                @if($order->exit_status()==460000200)
                    <td>
                        @include("component.input._number_sample",["id"=>'data['.$item->id."]",
                            "max"=>$item->amount_remaining,
                            "value"=>(isset($delivery)?$item->amount_remaining:"")])
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
