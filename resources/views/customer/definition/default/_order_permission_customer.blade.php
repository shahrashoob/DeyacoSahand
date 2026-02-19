<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <th></th>
        <th> استفاده از تنظیمات پیش فرض</th>
        <th></th>

        <th>پست جهت اطلاع رسانی</th>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($orderPermissionType as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <input type="checkbox" class="order_permission" data-id="{{$item->id}}" id="order_permission_{{$item->id}}"
                           name="order_permission_enable_{{$item->id}}"
                            {{ isset($data_customer_default_setting["order_permission"][$item->id]["enable"]) && $data_customer_default_setting["order_permission"][$item->id]["enable"] ? 'checked' : '' }}>

                </td>
                <td>

                    <input type="checkbox" class="order_permission" data-id="{{$item->id}}" id="order_permission_{{$item->id}}"
                           name="order_permission_checked_{{$item->id}}"
                            {{ isset($data_customer_default_setting["order_permission"][$item->id]["checked"]) && $data_customer_default_setting["order_permission"][$item->id]["checked"] ? 'checked' : '' }}>

                    آیا نیاز به تایید
                    <b style="font-size: 18px" class="text-primary">{{$item->caption}}</b>

                    وجود دارد؟

                </td>
                <td>
                    <div class="col-md-12">
                        @include("component.input._select_simple",[
                            "id"=>"order_permission_".$item->id,
                            "option"=>$post_option_list[$item->id]["items"],
                            "val"=>$post_option_list[$item->id]["value"],
                            "text"=>$post_option_list[$item->id]["text"],
                            "class_col"=>""
                            ])
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>

</div>
