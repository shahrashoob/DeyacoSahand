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
            <td colspan="8" style="text-align: right">
                @if(count($rfw_production_list)>0)
                    کارت های درخواست شده:
                @endif
                @foreach($rfw_production_list as $item)
                    {{$item->production->serial()}},
                @endforeach
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
        @foreach ($rfw_list as $item)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$item->material->code}}</td>
                <td>{{$item->material->caption}}</td>
                <td>{{$item->material->unit->caption}}</td>
                <td>{{$amount[$item->material->id]}}</td>
                <td>{{$amount_sent[$item->material->id]}}</td>
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
