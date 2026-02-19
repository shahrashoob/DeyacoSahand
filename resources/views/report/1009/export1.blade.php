<table>
    <thead>

    <tr>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">ردیف</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">تاریخ ایجاد بسته بندی</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">شماره بسته بندی</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">شماره آیتم بسته بندی</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا </th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">درجه کالا</th>

        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار سیستم</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار کنترل کیفیت</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار نهایی</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">شماره آیتم فرم تولید</th>

        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">سریال کارت تولید/دستور پیمان</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">وضعیت کارت تولید/ دستور پیمان</th>

        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">سریال کارت تولید/دستور پیمان (بالا دستی)</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">وضعیت کارت تولید/ دستور پیمان (بالا دستی)</th>

        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">کد ماشین</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">نام ماشین ماشین</th>

        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">نام پیمانکار</th>

         <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf"> شماره سفارش</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">کد مشتری</th>



    </tr>

    </thead>
    <tbody>
    @php $i=1;@endphp

    @foreach($list as $item)
        <tr>
            <td style="text-align:center; border:2px solid #000000;">{{$i++}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form->get_create_date_and_time()}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form->code}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->code}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->degree->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->amount}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->amount_after_control}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->final_amount}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->code??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->serial??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->waiting_status->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->parent_production->serial??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->parent_production->waiting_status->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production_form->machine->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production_form->machine->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production_form->contractor->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->order->series??""}}/{{$item->production_form_item->production->order->code??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->production_form_item->production->order->customer->caption??""}}</td>

        </tr>
    @endforeach
    </tbody>

</table>
