<div class="content">

    <table style="width: 100%;">
        <tr>

            <td rowspan="3" style="border: none">
                <table style="border: none">
                    <tr>
                        <th style="border: none; font-size: 10px" text-rotate="90">
                            سازمان دیجیتال دیاکو
                        </th>

                    </tr>
                </table>

            </td>
            @if($packing_form->packing_type->label_caption!="")
                <td>
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            @else
                <td style="border-bottom: none"></td>
            @endif
            <td  style="border: none">
                

            </td>
        </tr>

        <tr>
            <td style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none;  font-size: 11px;padding-right: 3px;">

                          <table style="border: none">
                              <tr>
                                  <td style=" text-align: right; border: none">
                                      نام کالا:
                                      {{$packing_form->items->first()->product->caption}}
                                      <br/>
                                      کد کالا:
                                      {{$packing_form->items->first()->product->code}}


                                  </td>
                              </tr>
                              <tr>
                                  <td style="text-align: center; border: none">
                                      <div style="font-size: 0.02px; margin: auto; ">
                                          {!! $barcode !!}
                                      </div>
                                  </td>
                              </tr>
                          </table>





                        </td>
                        <td style="border: none; text-align: center;width: 80px;padding-left: 3px;">
                            <div style="float: left;font-size: .01px;">
                                {{$qr}}

                            </div>
                            <div style="font-size: 12px; padding-top: 10px">
                                {{$packing_form->getCode()}}
                            </div>
                        </td>


                    </tr>


                </table>
            </td>
            <td style="border: none"></td>
        </tr>
        <tr>
            <td style="padding-top: 5px">
                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>

            </td>
            <td style="border: none"></td>
        </tr>

    </table>
</div>
