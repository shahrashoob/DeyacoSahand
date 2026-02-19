<div class="col-md-12" style="overflow: auto">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>نام کالا</th>
            <th>مقدار بسته بندی</th>
            <th>مقدار جدید</th>
            <th>درصد جمع شدگی</th>
            <th>باند</th>
        </tr>

        </thead>
        <tbody>
        @php $row=1;@endphp
        @foreach($product_shrinkage_info as $item)
            <tr>
                <td >{{$row++}}</td>
                <td>{{$product_list[$item["product_id"]]->fullCaption()}}</td>
                <td>{{$item["final_amount"]}}</td>
                <td>{{$item["new_amount"]}}</td>
                <td>
                    {{round(($item["final_amount"]-$item["new_amount"])/$item["final_amount"]*100,2)}}
                </td>
                <td>{{$item["band"]}}</td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
