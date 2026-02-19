<div class="table-responsive">

<table class="table table-styling" style="font-size: 11px !important">
    <thead>
    <tr>
        <th>#</th>
        <th>کد کالا </th>
        <th> عنوان کالا </th>
        <th> واحد سنجش </th>
        <th>  مقدار تحویلی جدید</th>

    </tr>

    </thead>
    <tbody>
    @php $row=0;@endphp
    @foreach($form->item as $item)
        <tr>
            <td>{{++$row}}</td>

            <td>{{$item->product->code}}</td>
            <td>{{$item->product->caption}}</td>
            <td>{{$item->product->unit->caption}}</td>
            <td style="font-size: 16px; font-weight:bold">
                {{$item->amount." ".$item->product->unit->caption}}

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
