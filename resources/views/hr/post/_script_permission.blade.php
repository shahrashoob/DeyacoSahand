
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>#</th>
                <th> <input type="checkbox" id="select_all_script"></th>
                <th>

                    دسترسی به دستیارهای هوشمند</th>
                <th>
                    <input type="checkbox" id="select_all_script_allow_edit">
                    مجوز ویرایش
                </th>
                <th>
                    <input type="checkbox" id="select_all_script_allow_view">
                    مجوز مشاهده لاگ اجرا
                </th>
            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($script_list as $item)
                <tr>
                    <td>{{++$row}}</td>

                    <td>
                        <input class="myCheckBox_script"  type="checkbox" id="switch-data[{{$item->id}}]" name="data[script][{{$item->id}}]"
                        {{isset($post_script[$item->id])?"checked='checked'":""}}"
                        >

                    </td>
                    <td> {{$item->caption}}</td>
                    <td>
                        <input class="myCheckBox_script_allow_edit"    type="checkbox"  name="data[script_setting][edit][{{$item->id}}]"
                        {{isset($post_script[$item->id]) && $post_script[$item->id]->allow_edit?"checked='checked'":""}}"
                        >

                    </td>
                    <td>
                        <input class="myCheckBox_script_allow_view"    type="checkbox"  name="data[script_setting][view_log][{{$item->id}}]"
                        {{isset($post_script[$item->id]) && $post_script[$item->id]->allow_view_log?"checked='checked'":""}}"
                        >

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

