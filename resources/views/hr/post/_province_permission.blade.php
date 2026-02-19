
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>#</th>
                <th>
                    <input type="checkbox" id="select_all_province">
                    دسترسی به استان ها</th>
            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($province_list as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>

                        <input class="myCheckBox_province"  type="checkbox" id="switch-data[{{$item->id}}]" name="data[province][{{$item->id}}]" {{$post->has_province_permission($item->id)?"checked='checked'":""}}">
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

