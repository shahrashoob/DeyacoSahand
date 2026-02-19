<tr style="background: #9d9d9d;">
    <td colspan="9">
        <div style="text-align: center;">
            <h3> مشخصات خریدار </h3>
        </div>
    </td>
</tr>

<tr>
    <td colspan="9">
        <div class="border-none table_right">
            <table class="border-none" style="border: none; ">
                <tr>
                    <td style="width: 25%;text-align: right" colspan="2">نام خریدار:
                        {{$company_name??""}}
                    </td>
                        <td style="width: 25%; text-align: right">شناسه ملی:
                            {{$national_code??""}}</td>
                        <td style="width: 25%; text-align: right">شماره اقتصادی:
                            {{$economic_number??""}}</td>

                </tr>
                <tr>
                    <td style="text-align: right">استان:
                        {{$company_province->caption??""}}
                     </td>
                    <td style="text-align: right">شهر / شهرستان:
                        {{$company_city_name??""}}</td>
                    <td style="text-align: right"> کد پستی:
                     {{$company_postal_code??""}}</td>
                    <td style="text-align: right">شماره تماس :
                      {{$company_phone_number??""}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right">نشانی:
                     {{$company_address??""}}
                    </td>
                </tr>

            </table>

        </div>
    </td>
</tr>
