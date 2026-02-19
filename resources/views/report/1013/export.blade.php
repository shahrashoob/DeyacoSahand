<html>

@if(count($order_factor) > 0)
<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره سفارش</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد مشتری</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام مشتری</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار</th>

    </tr>

    </thead>
    <tbody>
    @foreach($order_factor as $item)

        <tr>

            <td style="text-align:center; border:2px solid #000000;"> {{$item->order->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->order->customer->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->order->customer->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->product->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->product->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->value}}  </td>


        </tr>

    @endforeach
    </tbody>

</table>
@endif

@if(count($form_item_currency) > 0)
    <table style=" ">
        <thead>
        <tr>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره سفارش</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد مشتری</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام مشتری</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار</th>
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">برگ خروج از انبار</th>

        </tr>

        </thead>
        <tbody>
        @foreach($form_item_currency as $item)

            <tr>

                <td style="text-align:center; border:2px solid #000000;"> {{$item->order_code??""}}</td>
                <td style="text-align:center; border:2px solid #000000;"> {{$item->customer_code??""}}</td>
                <td style="text-align:center; border:2px solid #000000;"> {{$item->customer_caption??""}}</td>
                <td style="text-align:center; border:2px solid #000000;"> {{$item->product->code??""}}</td>
                <td style="text-align:center; border:2px solid #000000;"> {{$item->product->caption??""}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->value}}  </td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->form->code??""}}  </td>


            </tr>

        @endforeach
        </tbody>

    </table>
@endif


</html>
