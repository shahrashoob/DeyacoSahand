<div class="table-responsive">
    <table class="table table-styling">
        <thead>


        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($orderPermissionType as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <input type="checkbox"  name="data[order_permission][{{$item->id}}]" {{$customer->has_order_permission($item->id)?"checked='checked'":""}}">

                    آیا   نیاز به تایید
                    <b style="font-size: 18px" class="text-primary">{{$item->caption}}</b>

                    وجود دارد؟


                </td>
                <td>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>

</div>
