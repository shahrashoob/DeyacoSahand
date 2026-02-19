<html>
<table>
    <thead>
    <tr>
        <th>product_code</th>
        <th>product_caption</th>
        <th>packing_type_code</th>
        <th>packing_type_caption</th>
        <th>price</th>
    </tr>
    <tr>
        <th>کد کالا</th>
        <th>نام کالا</th>
        <th>کد نوع بسته بندی</th>
        <th>نام نوع بسته بندی</th>
        <th>قیمت بروز (ریال)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($packing_type_product as $item)
        <tr>
            <td>{{ $item->product_code }}</td>
            <td>{{ $item->product_caption }}</td>
            <td>{{ $item->packing_type_code }}</td>

            <td>{{ $item->packing_type_caption }}</td>
            @php
                $key = $item->product_id . "_" . $item->packing_type_id;
            @endphp
            @if(isset($prices[$key]))
                <td>{{ $prices[$key] }}</td>
            @else
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
</html>
