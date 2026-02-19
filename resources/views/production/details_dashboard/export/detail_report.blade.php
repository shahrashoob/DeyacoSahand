<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">سریال کارت تولید</th>

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">اولین مشخصه مهم</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">دومین مشخصه مهم</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">سومین مشخصه مهم</th>

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار سفارش</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار کارت تولید</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار تخصیص داده شده</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار تولید شده</th>

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کارت تولید سطح بالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالای سطح بالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالای سطح بالا</th>

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">اولین مشخصه مهم</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">دومین مشخصه مهم</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">سومین مشخصه مهم</th>

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار کارت تولید سطح بالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار تخصیص سطح بالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار تولید شده سطح بالا</th>


        <th></th>


    </tr>

    </thead>
    <tbody>
    @foreach($list as $item)
        <tr>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production->serial}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->code}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->caption}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->product->property1_caption}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->property2_caption}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->property3_caption}}</td>


            <td style="text-align:center; border:2px solid #000000;">{{$item->order_amount}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->production_amount}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->allocation_amount}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_amount}}</td>


            @if($item->parent_product)

                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_production->serial}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_product->code}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_product->caption}}</td>

                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_product->property1_caption}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_product->property2_caption}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_product->property3_caption}}</td>


                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_production_amount}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_allocation_amount}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->parent_production_form_amount}}</td>
            @else
                <td style="text-align:center; border:2px solid #000000;"></td>
                <td style="text-align:center; border:2px solid #000000;"></td>
                <td style="text-align:center; border:2px solid #000000;"></td>
                <td style="text-align:center; border:2px solid #000000;"></td>
                <td style="text-align:center; border:2px solid #000000;"></td>

            @endif

        </tr>
    @endforeach
    </tbody>

</table>
</html>