@if(count($material_list) > 0 && count($master_packing_form_group_by_product) > 0)
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header"><h5>وضعیت بسته بندی ها به تفکیک هر کالا در {{$modification_temp->warehouse->caption }} </h5></div>
            <div class="card-block" style="overflow: auto">
                <div class="col-md-12 {{$alert_class??""}}" style="overflow: auto">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th colspan="4">وضعیت بسته بندی ها</th>

                            <th>

                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>

                            <th>تعداد کل</th>
                            <th> مصرف نشده</th>
                            <th> مصرف شده</th>
                            <th>کاملا مصرف شده</th>

                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($material_list as $material )
                            @if(isset($master_packing_form_group_by_product[$material->id]))
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$material->code}}</td>
                                    <td>
                                        <a href="{{route($route_path."add_packing_form",[$modification_temp,$machine,$material->id,"return_to_warehouse"])}}">
                                            {{$material->caption}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route($route_path."show_packing_form_by_product",[$modification_temp,$machine,$material->id,0])}}">

                                            {{$master_packing_form_group_by_product[$material->id]}} عدد
                                        </a>
                                    </td>
                                    <td>
                                        @if(isset( $modification_packing_form_group_by_product[6021101][$material->id]))
                                            <a href="{{route($route_path."show_packing_form_by_product",[$modification_temp,$machine,$material->id,6021101])}}">
                                                {{$modification_packing_form_group_by_product[6021101][$material->id]}}
                                                عدد</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset( $modification_packing_form_group_by_product[6021102][$material->id]))
                                            <a href="{{route($route_path."show_packing_form_by_product",[$modification_temp,$machine,$material->id,6021102])}}">

                                            {{$modification_packing_form_group_by_product[6021102][$material->id]}} عدد
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset( $modification_packing_form_group_by_product[6021103][$material->id]))
                                            <a href="{{route($route_path."show_packing_form_by_product",[$modification_temp,$machine,$material->id,6021103])}}">
                                            {{$modification_packing_form_group_by_product[6021103][$material->id]}} عدد
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!isset($allow_delete) || $allow_delete)
                                        <a href="{{route($route_path."remove_product_from_list",[$machine,$material->id])}}" class="text-danger"
                                           onclick="return confirm('با حذف این کالا از لیست برگشت مواد اولیه، تمامس بسته بندی های این کالا از لیست حذف می شوند، آیا از حذف اطمینان دارید؟')">
                                            <i class="fa fa-trash"></i>

                                        </a>
                                        @endif

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
