
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>#</th>
                <th>
                    <input type="checkbox" id="select_all_menu">
                    دسترسی به منو
                </th>
                <th>دسترسی به عملیات ها</th>
            </tr>

            </thead>
            <tbody>
            @php $row=0; $menu_count=0;@endphp
            @foreach($menu_list as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>

                        <input class="myCheckBox_menu" type="checkbox" id="switch-data[{{$item->id}}]" name="data[menu][{{$item->id}}]" {{$post->has_menu_permission($item->id)?"checked='checked'":""}}">
                        <b>{{$item->menu_type->caption??$item->menu_type_id}}</b> / {{$item->caption}}
                    </td>
                    <td>
                        @php $menu_count=0;@endphp
                        @foreach($item->button as $btn)
                           {!! ++$menu_count %3 ==0 ? "<br/><br/>":" " !!}
                            <input type="checkbox" id="switch-data[{{$btn->id}}]" name="data[button][{{$btn->id}}]" {{$post->has_button_permission($btn->id)?"checked='checked'":""}}">

                            {{$btn->caption}}
                            &nbsp;&nbsp;&nbsp;
                        @endforeach
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


