<html>
<head>
    @include("pdf._head")
</head>
<body>

<div class="content">

   @include("production.print._worker_list",["has_sub_index"=>1])

<br/><br/>


    <table style=" display:inline">
        <tr>
            <td>تاریخ</td>
            <td>{{$production->get_create_date_and_time()}}</td>

            <td rowspan="14" class="border-none" style="vertical-align: top">

                <table >
                    <tr >
                        <td ><b>تاریخ و ساعت</b></td>
                        <td ><b>عملیات</b></td>
                        <td ><b>اقدام کننده</b></td>
                    </tr>
                    @if($production->order)
                    <tr>
                        <td>
                                {{$production->order->order_datetime()}}
                        </td>
                        <td>سفارش گذاری</td>
                        <td></td>
                    </tr>
                    @endif
                    <tr>
                        <td>{{$production->get_create_date_and_time()}}</td>
                        <td>  صدور کارت</td>
                        <td>سامانه ERP</td>
                    </tr>
                    @foreach($production->get_log_with_status() as $item)

                        <tr>
                            <td>{{$item->get_datetime()}}</td>
                            <td>{{$item->getStatus()}}</td>
                            <td>{{$item->worker->fullname()}}</td>
                        </tr>
                    @endforeach
                </table>





            </td>
        </tr>
        <tr>
            <td>شماره سریال تولید</td>
            <td>{{$production->serial}}</td>


        </tr><tr>
            <td>زمان مجاز بیکاری (دقیقه)</td>

            <td>{{$production->unemployment_time}}</td>

        </tr><tr>
            <td>دون تایم خط (دقیقه)</td>
            <td>{{$production->down_time}}</td>


        </tr><tr>
            <td>زمان ست آپ (دقیقه)</td>
            <td>{{$production->set_up_time}}</td>
        </tr><tr>
            <td>سرپرست تولید</td>
            <td> {{isset($production->supervisor_worker)?$production->supervisor_worker->fullname():""}}</td>


        </tr><tr>
            <td>نام محصول</td>
            <td>{{$production->product->caption??""}}</td>

        </tr><tr>
            <td>کد محصول</td>
            <td>{{$production->product->code??""}}</td>


        </tr><tr>
            <td>نام خط</td>
            <td>{{$production->line->caption??""}}</td>

            </tr><tr>
            <td>کد خط</td>
            <td>{{$production->line->code??""}}</td>

        </tr>
        <tr>
            <td>تعداد  </td>
            <td>{{$production->number()}} </td>


        </tr>
        <tr>
            <td>تعداد تولید شده</td>
            <td>{{$production->number_product}}</td>


        </tr>
        <tr>
            <td>تعداد تکی</td>
            <td>{{$production->sub_number_product}}</td>

        </tr>

        <tr>
            <td> شاخص ارزیابی عملکرد</td>
            <td>{{$production->productivity_index}}</td>
        </tr>
        <tr>
            <td>وضعیت کارت تولید</td>
            <td>{{$production->getStatus()}}</td>
        </tr>
        <tr>
            <td>شرح درخواست</td>
            <td colspan="2">

                @if($production->order)
                    {{$production->order_list->description_request->text??""}}
                @endif
            </td>
        </tr>

    </table>

{{--
<table style=" display:inline">

        <tr>

        </tr>

        <tr>

        </tr>


        <tr>

        </tr>

        <tr>

        </tr>


        <tr>
            <td>
                    تاریخ و زمان تحویل به  واحد

            </td>
        </tr>


        <tr>
            <td>
                    تاریخ و زمان ثبت فرم

            </td>
        </tr>
   </table>




</div> --}}


</div>
</body>
</html>
