<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th>
                <input type="checkbox" id="select_all_goods_kind">
                رسته های کالا</th>
            <td>لیست ماژول ها</td>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($goods_kind_list as $goods_kind)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    <input class="myCheckBox_goods_kind" type="checkbox"
                           id="switch-data[{{$goods_kind->id}}]"
                           name="data[goods_kind_status][{{$goods_kind->id}}]" {{$post->has_goods_kind_permission($goods_kind->id)?"checked='checked'":""}}
                    >
                    <b> {{$goods_kind->id." - ".$goods_kind->caption}}</b>
                </td>
                <td>
                    @php $i=0;@endphp
                    @foreach($goods_kind->getModuleList() as $item)
                        <a href="{{route("hr.post.edit_module",[$post,$goods_kind,$item["status_type_id"]])}}">
                            {{$item["status_type_id"]}} - {{$item["caption"]}}
                        </a>
                        &nbsp;
                        &nbsp;
                        @if(++$i % 3 == 0 ) <br/> @endif
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

