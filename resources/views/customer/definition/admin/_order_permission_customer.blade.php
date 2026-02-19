<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <th></th>
        <th></th>
        <th>پست جهت اطلاع رسانی</th>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($orderPermissionType as $item)

                <tr>
                    <td>{{++$row}}</td>
                    @if($mode=="create")
                    <td>

                        <input type="checkbox" class="order_permission" data-id="{{$item->id}}"
                               id="order_permission_{{$item->id}}"
                               name="data[order_permission][{{$item->id}}]"
                                {{ isset($data_customer_default_setting["order_permission"][$item->id]["checked"]) && $data_customer_default_setting["order_permission"][$item->id]["checked"] ? 'checked' : '' }}
                                {{ isset($data_customer_default_setting["order_permission"][$item->id]["enable"]) && $data_customer_default_setting["order_permission"][$item->id]["enable"] ? 'disabled' : '' }}
                        >

                        آیا نیاز به تایید
                        <b style="font-size: 18px" class="text-primary">{{$item->caption}}</b>

                        وجود دارد؟

                    </td>
                    @else

                        <td>

                            <input type="checkbox"
                                   name="data[order_permission][{{$item->id}}]" {{$customer->has_order_permission($item->id)?"checked='checked'":""}}
                            ">

                            آیا نیاز به تایید
                            <b style="font-size: 18px" class="text-primary">{{$item->caption}}</b>

                            وجود دارد؟

                        </td>
                    @endif
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
