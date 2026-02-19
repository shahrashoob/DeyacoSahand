<table class="table table-styling">
    <thead>
    <tr>
        <th>#</th>
        <th> شماره فرم</th>
        <th> تاریخ</th>
        <th> اقدام کننده</th>
        <th> وضعیت</th>

    </tr>

    </thead>
    <tbody>
    @php $row=0;@endphp
    @foreach($order->forms as $item)
        <tr>
            <td>{{++$row}}</td>

            <td>
                <a href="{{route("wh.product.show_exit_form",[$order,$item])}}">
                    فرم {{$item->code()}}
                </a>
            </td>
            <td>{{$item->get_create_date()}}</td>
            <td>{{$item->worker->fullname()}}</td>
            <td>{{$item->status->caption}}</td>

        </tr>
    @endforeach
    </tbody>
</table>
