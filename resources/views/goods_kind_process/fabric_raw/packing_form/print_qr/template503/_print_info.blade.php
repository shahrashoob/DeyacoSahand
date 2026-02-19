<div class="content">

    <table style="border: none">
        <tr>
            @for($k=1;$k<=2;$k++)
                <td style="border: none; ">
                    <table style="border: none;margin: 10px; padding-top: 15px">
                        <tr>
                            <td style=" border: none; font-size: 40px; padding-top: 10px">
                                {{$packing_form->items->first()->getCustomerCodeFromTariff()}}

                            </td>
                        </tr><tr>
                            <td style=" border: none; font-size: 35px; padding-bottom: 10px">
                                {{$packing_form->get_create_date_en(0,'F.Y')}}
                            </td>
                        </tr><tr>
                            <td style=" border: none">
                                {{$packing_form->getCode()}}
                                <div style="font-size: 0.02px">
                                    {!! $barcode_pin !!}
                                </div>


                            </td>
                        </tr>

                    </table>
                </td>
            @endfor
        </tr>
    </table>

</div>
