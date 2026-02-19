<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">
    <div class="border-none">
        <div style="text-align: center;">
            <h2> برگ خروج از انبار - سامانه ERP </h2>
        </div>

        <table style=" display:inline">
            <tr>
                <td colspan="4" style="text-align: right">
                    شماره فرم:
                    {{$form->code()}}
                    <br/>
                    تاریخ فرم:
                    {{$form->get_create_date_and_time()}}
                    <br/>
                    سند
                    <br/>
                    مبنا

                </td>
                <td colspan="4" style="text-align: right">
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

        </table>
        <br/>
    </div>
    <div>
        <table>
            <thead>
            <tr style="background: #9d9d9d">
                <th>#</th>
                <th> کد کالا</th>
                <th> نام کالا</th>
                <th> کد میله ای کالا</th>
                <th>واحد سنجش</th>
                <th> مقدار درخواست</th>
                <th> مقدار تحویل</th>

            </tr>

            </thead>
            <tbody>
            @php $row=1;$sum_carton=0;@endphp
            @foreach($form->item as $item)
                @php $sum_carton+=$item->amount @endphp
                <tr>
                    <th>{{$row++}}</th>
                    <th>{{$item->product->code}}</th>
                    <th>{{$item->product->caption}}</th>
                    <th></th>
                    <th> {{$item->product->unit->caption}}</th>
                    <th> {{$material_amounts[$item->product->id]}}</th>
                    <th> {{$item->amount}}</th>

                </tr>
            @endforeach
            <tr style="background: #9d9d9d; font-weight: bold">
                <td colspan="4"></td>
                <td>جمع</td>
                <td></td>
                <td>{{$sum_carton}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <div class="border-none">
        <table>
            <tr>
                <td colspan="1">
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
            <tr>
                <td>
                    {{$form->worker->fullname()}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>


        </table>
    </div>


</div>

</body>
</html>
