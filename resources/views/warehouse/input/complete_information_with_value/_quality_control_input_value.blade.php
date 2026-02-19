<div class="row" style="overflow: auto; min-height: 200px;">

    <div class="col-md-12" style="margin: auto">
        <table class="table table-styling center"
               style="width: 350px;margin: auto; border: 3px solid #efefef; margin-bottom: 20px">

            <tr>
                <td colspan="6">
                    <span style="font-size: 18px">{{$form_general_item->product->caption}}</span>
                    <br/>
                    <span>
                        کد کالا:
                        {{$form_general_item->product->code}}
                    &nbsp;
                    &nbsp;
                        درجه کالا:
                        {{$form_general_item->degree->caption}}
                        &nbsp;
                        &nbsp;
                        لات:
                        {{$form_general_item->lot_number->code}}
                        &nbsp;
                        &nbsp;
                        میانگین گرماژ:
                        {{$resul_check_quality["mean"]}}
                    </span>


                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if($product->unit->weight_conversion_rate==0)
                        {{$product->unit->measurement}}
                    @else
                        وزن ناخالص (کیلوگرم)
                    @endif
                </td>
                @if($product->sub_unit)
                    <td>
                        @if($product->sub_unit->weight_conversion_rate==0)
                            {{$product->sub_unit->measurement}}
                        @else
                            وزن ناخالص (کیلوگرم)
                        @endif

                    </td>
                @endif
                {{--                    <td>تعداد بسته بندی فرعی</td>--}}
                <td>

                </td>
                <td>
                    کد بسته بندی
                </td>
                <td>

                </td>
            </tr>
            @php $row=1; @endphp
            @foreach($form_general_item->form_general_item_packing_form as $form_general_item_packing_form_item)

                <tr

                >
                    <td>{{$row++}}

                    </td>
                    <td>{{$form_general_item_packing_form_item->packing_form->getFinalAmount()}}</td>
                    @if($product->sub_unit)
                        <td>{{$form_general_item_packing_form_item->packing_form->getSubAmount()}}</td>
                    @endif
                    {{--                    <td>{{$form_general_item_packing_form_item->packing_form->sub_packing_form_number}}</td>--}}
                    <td>

                        @switch($form_general_item_packing_form_item->check_quality_status_id)

                            @case(5003002)
                                <i class="fa  fa-check text-success"></i>
                                @break
                            @case(5003003)
                                <i class="fa  fa-times text-danger"></i>
                                @break
                        @endswitch

                    </td>
                    <td>{{$form_general_item_packing_form_item->packing_form->code}}</td>
                    <td>
                        <a onclick="confirm('آیا از حذف بسته بندی اطمینان دارید؟')"
                           href="{{route($route_path."delete_packing_form",[$form_general_item,$form_general_item_packing_form_item])}}">
                            <i class="text-danger fa fa-trash"></i>
                        </a>
                        <a href="{{route($route_path."print_packing_form",[$form_general_item_packing_form_item])}}">
                            <i class="fa fa-print"></i>
                        </a>
                    </td>
                </tr>

            @endforeach

            @if(!$resul_check_quality["quality_confirmed"])
            <tr>
                <td></td>
                <td id="col_unit_input">


                    {{--                    اگر واحد اصلی وزنی است، آیکن باسکون کنار آن اضافه می شود.--}}
                    @if($product->unit->weight_conversion_rate==0)
                        {{--                        واحد اصلی وزنی نیست--}}
                        <input type="number" id="amount" name="amount" autofocus
                               value="{{($before_amount??"")}}">

                    @else
                        {{--                        واحد اصلی وزنی است--}}
                        @if(isset($smart_object))
                            <input type="number" id="amount" name="amount" autofocus disabled
                                   value="{{($before_amount??"")}}">
                            <a href="#sdf" id="amount_smart_object" class="smart_object_icon "
                               onclick="set_id_for_smart_object('amount');">
                                <i class="fas  fa-weight"></i>
                            </a>
                        @else

                            <input type="number" id="amount" name="amount" autofocus
                                   value="{{($before_amount??"")}}">
                        @endif
                    @endif


                </td>
                {{--                واحد فرعی--}}
                @if($product->sub_unit && $product->sub_unit->weight_conversion_rate==0)
                    {{--                        واحد  فرعی وزنی نیست--}}
                    <td>
                        <input type="number" id="sub_amount" name="sub_amount"
                               value="">
                    </td>
                @else
                    {{--                        واحد فرعی وزنی است--}}
                    <td>
                        @if(isset($smart_object))
                            <input type="number" id="sub_amount" name="sub_amount" disabled
                                   value="{{($before_amount??"")}}">
                            <a href="#sdf" id="amount_smart_object" class="smart_object_icon "
                               onclick="set_id_for_smart_object('sub_amount');">
                                <i class="fas  fa-weight"></i>
                            </a>
                        @else

                            <input type="number" id="sub_amount" name="sub_amount" autofocus
                                   value="{{($before_amount??"")}}">
                        @endif
                    </td>
                @endif
                <td colspan="2"></td>
            </tr>
            @endif

            @if(isset($message))
                <tr>
                    <td colspan="6" class="alert alert-info">

                        {{$message}}

                    </td>
                </tr>
            @endif
            @if(isset($error_message))
                <tr>
                    <td colspan="6" class="alert alert-danger">

                        {{$error_message}}

                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="6">
                    @if($resul_check_quality["quality_confirmed"])
                        <a onclick="confirm('با تایید کنترل کیفی، تخلیه بار آغاز می شود، آیا از صحت اطلاعات اطمینان دارید؟')" href="{{route($route_path."quality_confirmed",$form_general_item)}}" class="btn btn-success">
                            تایید کنترل کیفی
                        </a>
                    @else
                        <button type="submit" class="btn btn-primary" onclick="add_new_product()">
                            ثبت و ادامه
                        </button>
                    @endif
                    <a href="{{route($route_path."index",$form_general_item->form)}}" class="btn btn-outline-dark">
                        بازگشت
                    </a>

                </td>
            </tr>

        </table>
    </div>

</div>

<script>

    function add_new_product(new_item_added) {


        var amount = $("#amount").val();
        var sub_amount = $("#sub_amount").val();
        var action_type = $("#action_type").val();
        var gross_weight = 0;
        var number_of_sub_packing = $("#number_of_sub_packing").val();


        // gross_weight = $("#gross_weight").val();

        if (isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) {
            alert("لطفا {{$product->unit->measurement}} را وارد نمایید.")
            return 0;
        }

        @if($product->sub_unit)

        if ((isNaN(parseFloat(sub_amount)) || parseFloat(sub_amount) <= 0)) {
            alert("{{$product->sub_unit->measurement}}  به درستی وارد نشده است.");
            return 0;
        }
        @endif

        // if (new_item_added !=-2 && isNaN(parseInt(packing_type_id))) {
        //     alert("لطفا نوع بسته بندی را انتخاب نمایید.")
        //     return 0;
        // }


        // if (packing_type_layer_count[$("#packing_type_id").val()] > 1 && (isNaN(parseInt(number_of_sub_packing)) || parseInt(number_of_sub_packing) <= 0)) {
        //     alert("لطفا تعداد بسته بندی فرعی را وارد کنید.")
        //     return 0;
        // }

        spinner_run_for()
        request = $.ajax({
            url: "{{url("api/other/complete_information_with_value/add_packing_form_api/".$form_general_item->id)}}",
            type: "post",
            data: {
                "user_id": {{$user_id}},
                "amount": amount,
                "sub_amount": sub_amount,
                "action_type": action_type,
                "gross_weight": gross_weight,
                "number_of_sub_packing": number_of_sub_packing,
                "smart_object_id": {{$smart_object->id??0}},
                // "keep_unit_value": $("#keep_unit_value").is(":checked") ? 1 : 0
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#complete_with_valueView").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    }
</script>

<script>

    // باسکول

    var id_gross_weight = "gross_weight";
    {{--                    اگر واحد اصلی وزنی است، آیکن باسکون کنار آن اضافه می شود.--}}
            @if($product->unit->weight_conversion_rate==0)
        id_gross_weight = "sub_amount";
    @else
        id_gross_weight = "amount";
    @endif

    var id_gross_weight_after_time_out = id_gross_weight;

    function set_id_for_smart_object(id) {
        id_gross_weight = id;
        $(".smart_object_icon ").removeClass("text-success");
        $("#" + id + "_smart_object").addClass("text-success");
    }

    function action_after_get_response(jsonObj) {
        if (typeof jsonObj["weight"] !== 'undefined') {
            $value = parseFloat(jsonObj["weight"]);
            if (id_gross_weight != "" && $value >= 0) {
                $("#" + id_gross_weight).val($value);
                set_id_for_smart_object("");


            }
            setTimeout(set_id_for_smart_object, 2000, id_gross_weight_after_time_out)
        } else {
            alert(jsonObj["error"])
        }
    }

</script>

@include("component.smart_object._get_value_from_smart_object")





