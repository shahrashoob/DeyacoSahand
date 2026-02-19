<div>
    <table style="border: none">
        <tr>
            @for($k=1;$k<=2;$k++)
                <td style="border:none ;  ">
                    <table style="width: 100%;border: none; margin: 5px">


                        <tr>
                            <td style="border: 1px solid ; text-align: center; font-size: 16px;padding-right: 1px;">

                                {{$packing_form->items->first()->product->caption}}


                            </td>
                        </tr>

                        <tr>
                            <td style="padding-top: 1px; border: none;text-align: center; ">

                                <div style="font-size: 0.02px">
                                    {!! $barcode !!}
                                </div>

                            </td>

                        </tr>
                        <tr>
                            <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 3px;text-align: center; ">
                                <div style="float: left;">
                                    {{$qr}}
                                </div>
                            </td>


                        </tr>


                        <tr>
                            <td style="padding-top: 1px; border: none">

                                <div style="text-align: center; width: 100%;padding-top:6px;font-size: 0.02px">
                                    {!! $barcode_pin !!}
                                </div>
                            </td>

                        </tr>
                        <tr>
                            <td style="text-align: center; font-size: 7px">
                                <div >
                                    سازمان دیجیتال دیاکو
                                </div>
                            </td>
                        </tr>


                    </table>
                </td>
                @if($k==1)

                    <td style="width: 30px"></td>
                @endif
            @endfor
        </tr>
    </table>




</div>
