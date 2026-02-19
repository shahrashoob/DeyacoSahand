<div >

    <table style="width: 100%;border: none; margin: 0px">


        <tr>
            <td style="border: none; text-align: center; font-size: 9px;padding-right: 1px;">

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


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 7px">
    سازمان دیجیتال دیاکو
</div>
