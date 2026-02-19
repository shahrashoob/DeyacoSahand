<div class="content">

    <table style="width: 100%;">
        <tr>
            <td> {{$software_name}}</td>
        </tr>


        <tr>
            <td style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 3px;">
                            <div style="float: left;">
                                {{$qr}}
                            </div>
                        </td>
                        <td style="border: none; text-align: right; font-size: 12px;padding-right: 3px;">

                            کد و نام مخزن: {{$reservoir->id}}
                            -
                            {{$reservoir->caption}}
                            <br/>
                            کالای مجاز:
                            <br/>
                            <table style="border: none">
                    @foreach($reservoir->products as $item)

                        <tr>
                            <td style="border: none;font-size: 11px;text-align: right">
                                {{$item->product->code}}


                            </td>
                        </tr>
                                <tr>
                            <td style="border: none;font-size: 11px;text-align: right">
                                {{$item->product->caption}}


                            </td>
                        </tr>

                    @endforeach
                </table>
            </td>


        </tr>


    </table>
    </td>
    <td style="border: none"></td>
    </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
