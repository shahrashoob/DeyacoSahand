<html>
<table>
    <thead>
    <tr>
        <td>{{$goods_kind->id}}</td>
        @foreach($goods_kind->property as $item)
            <td>{{$item->id}}</td>
        @endforeach
    </tr>

    <tr>
        <td>مشخصات {{$goods_kind->caption}} / کد کالا</td>
        @foreach($goods_kind->property as $item)
            <td>{{$item->caption}}</td>
        @endforeach
    </tr>

    <tr>
        <td>نوع فیلد</td>
        @foreach($goods_kind->property as $item)
            <td>{{$item->field_type->caption}}</td>
        @endforeach
    </tr>

    <tr>
        <td>واحد اندازه گیری</td>
        @foreach($goods_kind->property as $item)
            <td>{{$item->special_unit->caption??""}}</td>
        @endforeach
    </tr>

    <tr>
        <td>الزامی / اختیاری</td>
        @foreach($goods_kind->property as $item)
            <td {{$item->required?"style=\"background:#a80b00\"":""}}> {{$item->required?"*":""}}</td>
        @endforeach
    </tr>
    </thead>
</table>

</html>
