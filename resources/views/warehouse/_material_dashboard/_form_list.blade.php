<div class="table-responsive">

<table class="table table-styling"  style="font-size: 11px !important">
    <thead>
    <tr>
        <th>#</th>
        <th> شماره فرم </th>
        <th> تاریخ </th>
        <th> اقدام کننده </th>
        <th> وضعیت </th>

    </tr>

    </thead>
    <tbody>
    @php $row=0;@endphp
    @foreach($production->forms as $item)
        <tr>
            <td>{{++$row}}</td>
            <td>
                <a href="{{route("wh.material.show_exit_form",[$production,$item])}}">
                    فرم {{$item->code()}}
                </a>
            </td>
            <td>{{$item->get_create_date_and_time()}}</td>
            <td>{{$item->worker->fullname()}}</td>
            <td>{{$item->status->caption}}</td>

        </tr>
        @endforeach
    </tbody>
</table>

</div>
