<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table>
        <tr>
            <td colspan="3" >
                کارخانجات صنایع نساجی اردکان
                <br/>
                سازمان پزشکی خیریه حضرت سید الشهداء (ع) یزد
                <br/>
                فرم مشخصات پارچه خام
                <br/>
              <b>  کد و نام ماشین: {{$machine->fullCaption()}} - باند {{$band_code}}</b>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="center">مشخصات کالای در حال تولید</th>
        </tr>
        <tr>
            <td colspan="3">
                کد و نام کالا: {{isset($production)?$product->fullCaption():"---"}} <br/>
                شماره سریال کارت تولید: {{isset($production)?$production->serial():"---"}} <br/>
                تاریخ و ساعت تخصیص: {{$allocation->get_create_date_and_time()}}
            </td>
        </tr>


        @foreach($productionFromItemLot as $item_lot)
            @if(isset($item_lot->production_form_item->band_code) && $item_lot->production_form_item->band_code == $band_code)
                <tr>
                    <th colspan="3" class="center"> همبافت یا لات : {{$item_lot->lot_number->code??"---"}}</th>
                </tr>
                <tr>
                    <td colspan="3">

                        @php $lot_number=$item_lot->lot_number;@endphp

                        @if(isset($lot_number->lot_number_1))
                            لات  {{$lot_number->lot_number_1->product->caption??"***"}} :
                            <b> {{$lot_number->lot_number_1->code}}</b>
                        @endif

                        @if(isset($lot_number->lot_number_2))
                            <br/>
                            لات  {{$lot_number->lot_number_2->product->caption??"***"}} :
                            <b> {{$lot_number->lot_number_2->code}}</b>
                        @endif

                        @if(isset($lot_number->lot_number_3))
                            <br/>
                            لات {{$lot_number->lot_number_3->product->caption??"***"}} :
                            <b> {{$lot_number->lot_number_3->code}}</b>
                        @endif

                        @if(isset($lot_number->lot_number_4))
                            <br/>
                            لات  {{$lot_number->lot_number_4->product->caption??"***"}} :
                            <b> {{$lot_number->lot_number_4->code}}</b>
                        @endif
                    </td>
                </tr>
            @endif
            @break
        @endforeach


        <tr>
            <th colspan="3" class="center">مشخصات تار</th>
        </tr>
        <tr>
            <td>
                {{--            نمره تار 1 و 2--}}
                {!!$product->getPropertyValue(220235,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220236,"caption_value")!!}
            </td>
            <td>

                {{--            جنس تار 1 و 2--}}
                {!!$product->getPropertyValue(220239,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220240,"caption_value")!!}
            </td>
            <td>

                {{--            تراکم تار 1 و 2--}}
                {!!$product->getPropertyValue(220222,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220223,"caption_value")!!}
            </td>
        </tr>
        <tr>
            <td>
                {{--واکس--}}
                {!!$product->getPropertyValue(220294,"caption_value")!!}
            </td>
            <td>
                {{--سر نخ چله--}}
                {!!$product->getPropertyValue(220262,"caption_value")!!}
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                {{--زمینه--}}
                {!!$product->getPropertyValue(220330,"caption_value")!!}
            </td>
            <td>
                {{--حاشیه--}}
                {!!$product->getPropertyValue(220331,"caption_value")!!}
            </td>
            <td>
                {{--رزرو--}}
                {!!$product->getPropertyValue(220332,"caption_value")!!}
            </td>
        </tr>
        <tr>

            <td>
                {{--نمره شانه--}}
                {!!$product->getPropertyValue(220271,"caption_value")!!}
            </td>
            <td>
                {{--عرض شانه--}}
                {!!$product->getPropertyValue(220228,"caption_value")!!}
            </td>
            <td>
                {{--عرض چله--}}
                {!!$product->getPropertyValue(220298,"caption_value")!!}
            </td>
        </tr>

        <tr>
            <th colspan="3" class="center">مشخصات پود</th>
        </tr>
        <tr>
            <td>
                {{--نمره پود 1 و 2--}}
                {!!$product->getPropertyValue(220237,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220238,"caption_value")!!}
            </td>
            <td>
                {{--جنس پود 1 و 2--}}
                {!!$product->getPropertyValue(220241,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220242,"caption_value")!!}
            </td>
            <td>
                {{--تراکم پود 1 و 2--}}
                {!!$product->getPropertyValue(220224,"caption_value")!!}
                <br/>
                {!!$product->getPropertyValue(220225,"caption_value")!!}
            </td>
        </tr>
        <tr>
            <td>
                جدول دنده تراکم
            </td>
            <td colspan="2">

                {{-- A B C D--}}
                {!!$product->getPropertyValue(220326,"caption_value")!!}
                {!!$product->getPropertyValue(220327,"caption_value")!!}
                {!!$product->getPropertyValue(220328,"caption_value")!!}
                {!!$product->getPropertyValue(220329,"caption_value")!!}
            </td>
        </tr>
        <tr>
            <td>
                {{--نوع بافت زمینه--}}
                {!!$product->getPropertyValue(220221,"caption_value")!!}
            </td>
            <td>
                {{--نوع بافت کناره--}}
                {!!$product->getPropertyValue(220305,"caption_value")!!}
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                {{--تعداد ورد زمینه--}}
                {!!$product->getPropertyValue(220306,"caption_value")!!}
            </td>
            <td>
                {{--تعداد ورد کناره--}}
                {!!$product->getPropertyValue(220307,"caption_value")!!}
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                {{--نخ کشی از ورد زمینه--}}
                {!!$product->getPropertyValue(220308,"caption_value") !!}

            </td>
            <td>
                {{--ننخ کشی از ورد حاشیه--}}
                {!!$product->getPropertyValue(220309,"caption_value")!!}

            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                {{--تعداد نخ در دندانه شانه زمینه--}}
                {!!$product->getPropertyValue(220310,"caption_value")!!}
            </td>
            <td>
                {{--نتعداد نخ در دندانه شانه کناره--}}
                {!!$product->getPropertyValue(220311,"caption_value")!!}
            </td>
            <td></td>
        </tr>


    </table>

    سامانه جامع مدیریت کارخانجات نساجی اردکان

</div>
