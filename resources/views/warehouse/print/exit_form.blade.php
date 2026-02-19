<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">
    <div style="text-align: center;">
        <h2> برگ خروج از انبار - سامانه ERP </h2>
    </div>
    <div  class="border-none">
        <table >
            <tr>
                <td>شماره</td>
                <td>{{$form->code()}}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>تاریخ</td>
                <td>{{$form->get_create_date_and_time()}}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>سند</td>
                <td></td>
                <td>مرکز</td>
                <td>{{$order->customer->code??""}} - {{$order->customer->caption}}</td>
            </tr>
            <tr>
                <td>مبنا</td>
                <td></td>
                <td>انبار</td>
                <td></td>
            </tr>
            <tr>
                <td>آدرس</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td>شرح</td>
                <td colspan="3">{{$order->description_sheet->text??""}}</td>
            </tr>



    </table>

    <br/>
    <br/>
    <br/>
    </div>

    <div class="border-none">
    <table >
        <thead>
        <tr style="background: #9d9d9d">
            <th>#</th>
            <th>انبار</th>
            <th> کد کالا</th>
            <th> نام کالا</th>
            <th> کد میله ای کالا</th>
            <th> تعداد کارتن</th>
            <th>تعداد واحد فرعی<br/> در کارتن</th>
            <th>  واحد </th>

        </tr>

        </thead>
        <tbody>
        @php $row=1;$sum_carton=0;@endphp
        @foreach($form->item as $item)
            @php $sum_carton+=$item->amount @endphp
            <tr>
                <th>{{$row++}}</th>
                <th>{{$item->product->warehouse->code??""}} </th>
                <th>{{$item->product->code}}</th>
                <th>{{$item->product->caption}}</th>
                <th> </th>
                <th> {{$item->amount}}</th>
                <th> {{$item->product->number_in_carton}}</th>
                <th> {{$item->product->unit->bach_caption}}</th>

            </tr>
        @endforeach
        <tr style="background: #9d9d9d">
            <td colspan="6"></td>
            <td>جمع</td>
            <td>{{$sum_carton}}</td>
        </tr>
        </tbody>
    </table>
    </div>
    <br/>
    <br/>
    <div style="text-align: justify">
        کالای فوق صحیح و سالم و بدون هیچ نقصی توسط اینحانت ............................. به شماره موبایل .......................... شماره بارنامه ...........................
        خودرو ................. شماره پلاک ......................... کاملا شمارش و بارگیری و تحویل گردید. ضمنا چنانچه در طول مسیر از درب کارخانه تا مقصد بر اثر بی احتیاطی و بی توجهی اینجانب به کالاهای فوق الذکر خسارت وارد گردید و یا شمارش کالا در مقصد توسط صاحب کالا با تعداد ذکر شده در این فرم مقایرت داشته باشد متعقد می شوم که خسارت وارده در مقصد به صاحب کالا پرداخت و هیچ گونه اعتراضی نداشته باشم. این متن را به طور کامل مطالعه و قبول خواهم داشت.

    </div>
    <br/>

    <br/>
    <br/>
    <div class="border-none">
    <table >
        <tr>
            <td>تنظیم کننده:</td>
            <td>سرپرست انبار:</td>
            <td>تایید کننده:</td>
            <td>تحویل گیرنده:</td>
            <td>انتظامات:</td>
        </tr>
        <tr>
            <td>{{$form->worker->fullname()}}</td>
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
