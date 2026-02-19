<div class="content">


    <table style="width: 100%">
        <tr>
            <td>


                فرم درخواست طراحی کالا
                <br/>
                کد درخوست: {{$product_creation_process->getCode()}}
                <br/>
                نام پشنهادی کالا
                {{$product_creation_process->caption}}
            </td>

        </tr>
        <tr>
            <td>

                جناب آقای/سرکار خانم
                {{$product_creation_process->worker->fullName()}}
                لطفا پس از الصاق نمونه کالا به این فرم  آن را به آدرس گیرنده ارسال فرمایید.

                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <div style="  margin: auto;text-align: center">
                    محل الصاق نمونه
                </div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
            </td>


        </tr>

            <tr>
                <td>
                    فرستنده
                </td>
            </tr>
            <tr>
                <td>

                    آدرس:
                    {{$customer?($customer->getDefaultAddress()->province->caption??"")." - ":""}}
                    {{$customer?($customer->getDefaultAddress()->city_name??"")." - ":""}}
                    {{$customer?$customer->getDefaultAddress()->address:$product_creation_unit_address}}
                    <br/>
                    تلفن:
                    {{$customer?$customer->getDefaultAddress()->phone:$product_creation_unit_phone}}
                    <br/>
                    <br/>
                </td>
            </tr>

        <tr>
            <td>
                گیرنده
            </td>
        </tr>

        <tr>
            <td>
آدرس:
                {{$product_creation_unit_address}}
                <br/>
                تلفن:
                {{$product_creation_unit_phone}}
                <br/>
                <br/>
            </td>
        </tr>

    </table>
</div>
