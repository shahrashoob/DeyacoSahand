<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th>کد انبار</th>
            <th>عنوان انبار</th>
            <th>نوع انبار</th>
            <th>گروه شیفت کاری انبار</th>
            <th></th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($warehouses as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    {{$item->code}}
                </td>
                <td>
                    {{$item->caption}}
                </td>
                <td>
                    {{$item->warehouse_type->caption}}
                </td>
                <td>
                    {{$item->shift->caption??""}}
                </td>
                <td>  <a  href="{{route("wh.warehouse.edit",$item)}}" target="_blank"><i class="fa fa-edit"></i> </a></td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div>
