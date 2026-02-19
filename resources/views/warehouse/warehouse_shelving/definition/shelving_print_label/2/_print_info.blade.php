<div class="content">

    <div style="text-align: center; width: 100%;font-size: 11px">
       {{$software_name}}
    </div>
    <table style="width: 100%;">
        <tr>

                <td style="border-bottom: none;direction: ltr !important;border-left: none">
                   <span style="font-size: 35px;">

                       {{ $cell_code}}
<span style="font-family: DejaVu Sans, sans-serif; font-weight: bold;  display: inline">
                             @if($updown==1)
                                 &uarr;
                             @else
                                 &darr;
                             @endif

                             </span>
                   </span>

                    <br/>


                </td>
            <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 3px;">
                <div style="float: left;">
                    {{$qr}}
                </div>
            </td>

        </tr>
    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
