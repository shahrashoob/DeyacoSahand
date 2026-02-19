<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">
    <div style="text-align: center;">
        <h2> برگ دریافت از تولید محصول - سامانه ERP </h2>
    </div>
    <div class="border-none table_right">
        <table>
            <tr>
                <td style="width: 30px">شماره:</td>
                <td>{{$form->code()}}</td>
                <td style="width: 30px">مرکز:</td>
                <td>{{$warehouseProducts->first()->ic??""}}  </td>
                <td style="width: 30px">سند:</td>
                <td></td>
            </tr>
            <tr>
                <td>تاریخ:</td>
                <td>{{$form->get_create_date_and_time()}}</td>
                <td>انبار:</td>
                <td>{{$warehouseProducts->first()->warehouse->code??""}}  </td>
                <td>مبنا:</td>
                <td></td>
            </tr>

            <tr>
                <td>شرح:</td>
                <td colspan="5">{{$form->message->text??""}}</td>
            </tr>

        </table>


    </div>

    <div class="border-none">
        <table>
            <thead>
            <tr style="background: #9d9d9d">
                <th>#</th>
                <th>انبار</th>
                <th> کد کالا</th>
                <th> نام کالا</th>
                <th> واحد سنجش</th>
                <th> مقدار</th>

            </tr>

            </thead>
            <tbody>
            @php $row=1;$sum_carton=0;@endphp
            @foreach($warehouseProducts as $item)
                @php $sum_carton+=$item->input + $item->output @endphp
                <tr>
                    <th>{{$row++}}</th>
                    <th>{{$item->product->warehouse->code??""}} </th>
                    <th>{{$item->product->code}}</th>
                    <th>{{$item->product->caption}}</th>

                    <th> {{$item->product->unit->bach_caption}}</th>
                    <th> {{$item->input + $item->output}}</th>

                </tr>
            @endforeach
            <tr style="background: #9d9d9d">
                <td colspan="4"></td>
                <td>جمع</td>
                <td>{{$sum_carton}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="border-none">
        <table>
            <tr>
                <td>تنظیم کننده:</td>
                <td>بررسی کننده:</td>
                <td>تایید کننده:</td>
            </tr>
            <tr>
                <td>{{$form->worker->fullname()}}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
