<html>
<head>
    @include("pdf._head")
</head>
<body>

<div class="content">




    <table style=" display:inline">
        <tr>
            <td colspan="8" >
                    درخواست کالا از انبار
            </td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right">
                تاریخ درخواست:
                {{$production->get_create_date()}}
                <br/>
                شماره درخواست:
                {{$production->serial()}}

                <br/>
                عنوان مرکز هزینه با نام واحد:
                {{$production->product->ic}}
            </td>
        </tr>
        <tr>
            <td>ردیف</td>
            <td>کد کالا</td>
            <td>عنوان کالا</td>
            <td>واحد سنجش</td>
            <td>مقدار </td>
            <td>مقدار تحویلی</td>
            <td style="width: 200px">توضیحات</td>
        </tr>
        @php $i=1;@endphp
        @foreach ($production->RFWs as $item)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$item->material->code}}</td>
                <td>{{$item->material->caption}}</td>
                <td>{{$item->material->unit->caption}}</td>
                <td>{{$item->amount}}</td>
                <td>{{$item->amount_sent}}</td>
                <td></td>
            </tr>
        @endforeach
        <tr >
            <td colspan="2" style="height: 100px">
                درخواست دهنده
            </td>
            <td colspan="1">
                تایید کننده
            </td>
            <td colspan="1">
                تحویل دهنده
            </td>
            <td colspan="1">
                تحویل گیرنده
            </td>
        </tr>



    </table>



</div>
</body>
</html>
