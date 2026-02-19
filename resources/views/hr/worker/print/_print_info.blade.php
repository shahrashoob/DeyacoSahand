<div class="content" style="border: 2px solid #504e4e ; border-radius: 10px; margin-right: 20px ; ">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%;border:none">
        <tr>
            <td colspan="3" style="border: none;padding-top: 3px">
              <div style="font-size: 14px">  {{$software_name}}</div>
                <br/>
                کارت پرسنلی
            </td>



        </tr>

        <tr>
            <td colspan="2" style="font-size: 12px;text-align: right; line-height: 2; padding-right: 5px;border: none">
                نام:
                {{$worker->firstname}}
                <br/>
                نام خانوادگی:
                {{$worker->lastname}}
                <br/>
                کد ملی:
                {{to_persian($worker->national_code)}}
                <br/>

                شماره پرسنلی:
                {{to_persian($worker->id)}}

{{--                <br/>--}}
{{--                پست سازمانی:--}}
{{--                @foreach($post_user as $item)--}}
{{--                    {{$item->post->caption}}--}}
{{--                    &nbsp;--}}
{{--                    &nbsp;--}}
{{--                @endforeach--}}


            </td>
            <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 5px;">
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
