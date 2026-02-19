<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th>
                <input type="checkbox" id="select_all_goods_kind_property">
                دسترسی به مشخصات کالا
            </th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($goods_kind_list as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    <h5>
                        <input class="myCheckBox_goods_kind_property" type="checkbox" id="select_all_property_{{$item->id}}"
                               name="data[goods_kind_property][{{$item->id}}]"
                        >
                        رسته کالای
                        {{$item->caption}}</h5>
                    <div class="col-md-12">
                        @php $k=0;@endphp
                        <table style="border: none">
                            <tr>
                                @foreach($item->property()->where("status_id",1200)->get() as $property)
                                    <td>
                                        <input class="myCheckBox_property_{{$item->id}} myCheckBox_property_0" type="checkbox"
                                               name="data[property][{{$property->id}}]" {{$post->has_goods_kind_property_permission($property->id)?"checked='checked'":""}}
                                        >
                                        <input type="hidden"
                                               name="data[property_goods_kind][{{$property->id}}]"
                                               value="{{$item->id}}">
                                        <b>{{$property->caption}}</b>
                                    </td>
                            @if($k%6 == 5)
                                <tr></tr>
                                @endif
                                @php $k++;@endphp
                                @endforeach
                                </tr>
                        </table>
                    </div>
                </td>
                <td>
                    <script>
                        $("#select_all_property_{{$item->id}}").change(function () {

                            $(".myCheckBox_property_{{$item->id}}").prop('checked', $("#select_all_property_{{$item->id}}").is(':checked'));
                        })
                    </script>
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

