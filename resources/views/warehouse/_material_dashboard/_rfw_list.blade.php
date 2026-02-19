<div class="table-responsive">
    <table class="table table-styling" style="font-size: 11px !important">
        <thead>
        <tr>
            <th>#</th>
            <th>کد کالا</th>
            <th> عنوان کالا</th>
            <th> واحد سنجش</th>
            <th> مقدار درخواست</th>
            <th> مقدار تحویلی</th>
            @if($allow_register_new_form)
                <th> مقدار تحویلی جدید</th>
            @endif
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($rfw_list as $item)
            <tr>
                <td>{{++$row}}</td>

                <td>{{$item->material->code}}</td>
                <td>{{$item->material->caption}}</td>
                <td>{{$item->material->unit->caption}}</td>
                <td>{{$amount[$item->material->id]}}</td>
                <td>{{$amount_sent[$item->material->id]}}</td>
                @if($allow_register_new_form)
                    <td>
                        @include("component.input._number_sample",["id"=>'data['.$item->id."]",
                            "max"=>$amount_remaining[$item->material->id],
                            "value"=>(isset($delivery) && $delivery==1?$amount_remaining[$item->material->id]:"")])
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
