<table style="border: none">
    <tr>
        <td style="border: none; text-align: center">
            <div style="font-size: .01px; position: fixed; left:0 ">
                @if(
                    (isset($product_request_form->order) && $product_request_form->order->selling_type_id>=1 )||
                    !isset($product_request_form->order)
                    )
                    {{$qr}}
                @else
                    <div style="min-width: 75px;min-height: 75px"></div>
                @endif
            </div>
        </td>
       <td style="border: none">
           <table style="border: none">
               <tr>
                   <td colspan="2" style="border: none; text-align: right;padding-right: 5px; font-size: 15px">
                       درخواست دهنده:
                       {{isset($product_request_form)?$product_request_form->applicant->fullCaption():""}}
                  <br/>
                   </td>
               </tr>
               <tr>
                   <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px">

                       برگ خروج از انبار: {{$form->code}}
                       @if(
                               (isset($product_request_form->order) && $product_request_form->order->selling_type_id==1 )||
                               !isset($product_request_form->order)
                               )
                           <br/>
                           فرم درخواست کالا: {{$form->getAllProductRequestFormCodes()}}
                           <br/>
                           شماره سفارش: {{$product_request_form&&$product_request_form->order?$product_request_form->order->code():""}}
                       @endif
                   </td>
                   <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">
                       تاریخ: {{$form->get_create_date()}}
                       <br/>
                       تعداد بسته بندی:  {{count($form->getPackingFormList())}} عدد
                       <br/>
                       تعداد بسته بندی ح و ن:
                       {{$form->getTransportCount()}}
                       عدد


                   </td>
               </tr>
           </table>
       </td>

    </tr>
</table>
