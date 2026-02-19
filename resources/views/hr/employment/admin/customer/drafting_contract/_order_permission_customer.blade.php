<div class="table-responsive">
    <table class="table table-styling">
        <thead>
        <tr>

            <th></th>
            <th>تاییدیه ها</th>
            <th>پست جهت اطلاع رسانی</th>

        </tr>
        </thead>
        <tbody>
        @php $row=0; @endphp
        @foreach($orderPermissionType as $item)
            <tr>

                @if(isset($data_customer_default_setting["order_permission"][$item->id]["enable"]) && !$data_customer_default_setting["order_permission"][$item->id]["enable"])
                    <td>
                        مرحله
                        {{++$row}}
                    </td>
                    <td>

                        آیا نیاز به تایید
                        <b style="font-size: 18px" class="text-primary">{{$item->caption}}</b>
                        وجود دارد؟
                        <label>
                            <input type="radio"
                                   name="data[order_permission][{{$item->id}}]"
                                   value="1"
                                    {{ isset($data_customer_default_setting["order_permission"][$item->id]["checked"]) &&
                                        $data_customer_default_setting["order_permission"][$item->id]["checked"] ? 'checked' : '' }}>

                            بله
                        </label>
                        <label>
                            <input type="radio"
                                   name="data[order_permission][{{$item->id}}]"
                                   value="0"
                                    {{ isset($data_customer_default_setting["order_permission"][$item->id]["checked"]) &&
                                  !$data_customer_default_setting["order_permission"][$item->id]["checked"] ? 'checked' : '' }}>

                            خیر
                        </label>

                    </td>
                @endif
                @if(isset($data_customer_default_setting["order_permission"][$item->id]["enable"]) && !$data_customer_default_setting["order_permission"][$item->id]["enable"])
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
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
