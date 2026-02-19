<div class="table-responsive">
<table class="table table-styling" style="font-size: 11px !important">
    <thead>
    <tr>
        <th>#</th>
        <th>کد کالا </th>
        <th> عنوان کالا </th>
        <th> واحد سنجش </th>
        <th>  مقدار درخواست </th>
        <th>  مقدار تحویلی</th>
        <th>  مقدار تحویلی جدید</th>

    </tr>

    </thead>
    <tbody>
    @php $row=0;@endphp
    @foreach($production->RFWs as $item)
        <tr>
            <td>{{++$row}}</td>
                
            <td>{{$item->material->code}}</td>
            <td>{{$item->material->caption}}</td>
            <td>{{$item->material->unit->caption}}</td>
            <td>{{$item->amount}}</td>
            <td>{{$item->amount_sent}}</td>
            <td>
                @include("component.input._number_sample",["id"=>'data['.$item->id."]",
                    "max"=>$item->amount_remaining,
                    "value"=>(isset($delivery)?$item->amount_remaining:"")])
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>