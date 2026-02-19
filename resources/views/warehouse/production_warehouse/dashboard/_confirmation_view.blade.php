
    <div class="row">
        <div
            class="w-100"></div>
        @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->getCreateFormUser()->fullname()])

        @foreach($product_request_form->forms as $product_request_form_form)
            @if(isset($form_info[$product_request_form_form->form_id]))
                <div class="col-md-12">
                    <label> برگ خروج {{$product_request_form_form->form->getCode()}}</label>

                </div>
                <br/>  <br/>
            @if($warehouse_entry_confirmation_in_altogether)
{{--                فقط یک بسته همراه با تعداد بسته بندی ها--}}
                    <div class="col-md-12 center" style="padding-top: 10px">
                        <table style="text-align: right; margin: auto">
                            <tr>
                                <td> تعداد بسته بندی ها:</td>
                                <td>
                                    <input class="input_packing_code"
                                           name="packing_form_number"
                                           type="number" style="width: 140px" required

                                           value="">
                                </td>
                            </tr>
                            <tr>
                                <td>  شماره یکی از بسته بندی ها:</td>
                                <td>
                                    <input class="input_packing_code"
                                           name="packing_code"
                                           type="number" style="width: 140px" required

                                           value="">
                                    /DCPK
                                </td>
                            </tr>
                        </table>



                        <br/>
                        <br/>






                    </div>
                    <br/>
                @else
{{--                بسته بسته وارد می کند--}}
                    @for($k=1; $k <= $form_info[$product_request_form_form->form_id];$k++)

                        <div class="col-md-12 center" style="padding-top: 10px">
                            <input class="input_packing_code"
                                   name="data[packing_code][{{$product_request_form_form->form_id}}][{{$k}}]"
                                   type="number" style="width: 140px" required
                                   {{isset($packing_codes_reading[$product_request_form_form->form_id][$k])?"":"autofocus"}}
                                   value="{{
	                                            isset($packing_codes_reading[$product_request_form_form->form_id][$k])?
	                                            $packing_codes_reading[$product_request_form_form->form_id][$k]:""
                                                    }}">
                            /DCPK
                        </div>
                        <br/>
                        {{--                                    @include("component.input._text",["id"=>, "label"=>"شماره بسته بندی  ".$k,"value"=>""])--}}


                    @endfor
                @endif

            @endif

        @endforeach
        <div class="col-md-12 center">
            @include("component.input._hidden",["id"=>"warehouse_entry_confirmation_in_altogether","value"=>$warehouse_entry_confirmation_in_altogether])
            <hr/>
            <a href="{{$dashboard_url}}"
               class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-success"
                    onclick="return confirm('آیا از تایید تحویل کالا  اطمینان دارید؟')">تایید تحویل
                کالا
            </button>
        </div>
    </div>



