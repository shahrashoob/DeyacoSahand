
<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th>دسترسی به وضعیت کارت های تولید </th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($production_status_list as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <input type="checkbox" id="switch-data[{{$item->id}}]" name="data[order_status][{{$item->id}}]" {{$post->has_order_status_permission($item->id)?"checked='checked'":""}}">
                    <b> {{$item->id." - ".$item->caption}}</b>
                </td>
                <td>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>

</div>

<a href="{{route("hr.post.index")}}" class="btn btn-outline-defualt">بازگشت</a>
<button type="submit" class="btn btn-success"
        onclick="return confirm('آیا از ثبت دسترسی ها اطمینان دارید')"> ثبت دسترسی
</button>

