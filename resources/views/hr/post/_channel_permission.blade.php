
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>#</th>
                <th>
                    <input type="checkbox" id="select_all_chanel">
                    دسترسی به کانال ها
                </th>
            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($channel_list as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>

                        <input class="myCheckBox_chanel" type="checkbox" id="switch-data[{{$item->id}}]" name="data[channel_type][{{$item->id}}]" {{$post->has_channel_type_permission($item->id)?"checked='checked'":""}}">
                        <b> {{$item->caption}}</b>
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

