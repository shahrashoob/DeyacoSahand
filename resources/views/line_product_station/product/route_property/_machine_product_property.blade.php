<div class="table-responsive">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>خط</th>
            <th>ایستگاه</th>
            <th>گروه ماشین</th>
            <th>نوع عملیات</th>
            <th>نوع عملیات فرعی</th>
            <th>عنوان مشخصه            </th>
            <th>مقدار مشخصه</th>
        </tr>

        </thead>
        <tbody>
        @php $row=1; @endphp
        @foreach($route->line_product_station as $item)
            <tr>
                <td>{{$row++}}</td>
                <td>{{$item->line->caption??""}}</td>
                <td>{{$item->station->caption??""}}</td>
                <td>{{$item->machine_type->caption??""}}</td>
                <td>{{$item->station_operation->caption??""}}</td>
                <td>{{$item->station_sub_operation->caption??""}}</td>

                @foreach($item->station->machine_product_property as $property)
                    <td>{{$property->caption}}</td>
                    <td>

                        @switch($property->field_type_id)
                            @case(1)
                            <input type="number" name="data[{{$item->id}}][{{$item->station_sub_operation_id}}][{{$property->id}}]" value="{{$property->getValue($item->machine_type_id,$product->id,$item->station_sub_operation->id)}}" />
                            {{$property->special_unit->caption??""}}
                            @break
                            @case(2)
                            <input type="text" name="data[{{$item->id}}][{{$item->station_sub_operation_id}}][{{$property->id}}]" value="{{$property->getValue($item->machine_type_id,$product->id,$item->station_sub_operation->id)}}" />
                            {{$property->special_unit->caption??""}}
                            @break
                            @case(3)
                            @include("component.input._select_simple",["id"=>"data[".$item->id."][".$item->station_sub_operation_id."][".$property->id."]","label"=>"","option"=>$property_option[$property->id][$item->station_sub_operation_id]["items"],"class"=>""])
                            @break
                        @endswitch
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>

    </table>
</div>

