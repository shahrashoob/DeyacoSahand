@if($machine_allocation->production->packing_types()->count() ==1)
    @include("component.input._lable",["id"=>"packing_type_id","value"=>$machine_allocation->production->packing_types()->first()->packing_type->fullCaption()??"","label"=>"نوع بسته بندی"])
    @include("component.input._hidden",["id"=>"packing_type_id","value"=>$machine_allocation->production->packing_types()->first()->packing_type->id??"",])
@endif

{{--@include("component.input._text",["id"=>"carrier_code","label"=>"شماره  حامل","value"=>$carrier_code??"","class_col"=>"col-md-4"])--}}

@include("component.input._hidden",["id"=>"packing_form_id","value"=>$packing_form->id??""])

@include("component.input._lable",["id"=>"product_id","value"=>$machine_allocation->product->fullCaption(),"label"=>"کالا"])

@include("component.input._hidden",["id"=>"action_type","value"=>"add_new_product_item"])
@if(isset($smart_object))
    @include("component.input._lable",["id"=>"smart_object_id","url"=>$url_scale??null,"value"=>$smart_object->caption,"label"=>"باسکول"])

@endif
@if($machine_allocation->product->unit->weight_conversion_rate ==0 ||
    ($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)
)

    <div class="col-md-6">

        <input type="checkbox" id="is_complete_information"
               @if(isset($is_complete_information) && $is_complete_information==1) checked="checked"@endif >
        <label for="is_complete_information"><b>ثبت وزن ناخالص در تکمیل اطلاعات</b></label>

    </div>
@else
    <input type="hidden" id="is_complete_information" value="-1"/>
@endif

{{--ذخیره مقدار اصلی--}}
<div class="col-md-6">
    <input type="checkbox" id="keep_unit_value"
           @if(isset($keep_unit_value) && $keep_unit_value) checked="checked"@endif >
    <label for="keep_unit_value"><b>مقدار اصلی را برای ثبت بسته بندی بعدی، ذخیره کن </b></label>

    <br/>

</div>


<div class="row" style="overflow: auto; min-height: 200px;">


    <table class="table table-styling center"
           style="width: 600px;margin: auto; border: 3px solid #efefef; margin-bottom: 20px">

        @if(isset($message_add_packing))
            <tr>

                <td colspan="6" class="alert alert-danger" style="width: 600px;margin: auto; ">
                    {!! $message_add_packing !!}
                </td>

            </tr>
        @endif

        @if(isset($success_message))
            <tr>

                <td colspan="6" class="alert alert-success" style="width: 600px;margin: auto; ">
                    {!! $success_message !!}
                </td>

            </tr>
        @endif
        @if(isset($pallet))
            <tr>
                <td colspan="6" class="alert-info text-info">
                         <span style="font-size: 20px;  font-weight: bold">
                             پالت شماره {{$pallet->id}}
                         </span>
                </td>
            </tr>
        @endif


        {{--        دانلود فایل بسته بندی--}}
        @if( isset($action_type) && ($action_type == "register_continue" || $action_type == "register_print_continue" ))
            <tr>
                <td colspan="6">

                            <span style="font-size: 20px; color: #0b2e13; font-weight: bold">
                            {{$machine_packing_count+1}}
                            بسته بندی در انتظار تایید
                        </span>

                    <br/>
                    یک بسته بندی جدید با شماره
                    <b style="font-size: 16px">{{$packing_form_new_added->getCode()}}</b>
                    ثبت گردید.
                    <br/>
                    <a href="{{route("fabric_raw.packing_form.print_qr.download",$packing_form_new_added)}}"><i
                                class="fa fa-download"></i> دانلود فرم {{$packing_form_new_added->getCode()}}</a>
                </td>
            </tr>
        @endif


        {{--    اگر کارت رزرو بعدی پشنهاد شد، جدول ثبت نباید نمایش داده شود.--}}

        <tr>
            <th style="">
                @if($machine_allocation->production->packing_types()->count() !=1)
                    بسته بندی
                @endif
            </th>
            <th style="width: 150px">لات</th>
            <th style="width: 150px">درجه</th>

            {{--                واحد اصلی--}}
            <th style="width: 150px" id="col_unit_caption">
                @if($machine_allocation->production->product->unit->weight_conversion_rate==0)
                    {{$machine_allocation->production->product->unit->measurement}}
                @else
                    وزن ناخالص
                    ( {{$machine_allocation->production->product->unit->caption}})
                @endif
            </th>

            {{--                واحد فرعی--}}
            @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)
                <th style="width: 150px">
                    {{$machine_allocation->production->product->sub_unit->measurement}}

                </th>
            @endif
            <th>

                <span id="number_of_sub_packing_caption"> تعداد بسته بندی فرعی</span>
            </th>
            <th>
                @if(isset($get_carrier_code) && $get_carrier_code)
                    شماره حامل
                @endif
            </th>
        </tr>
        @if(isset($packing_form))
            @foreach($packing_form->items as $item)
                <tr>
                    <td></td>
                    <td>{{$item->lot_number->code}}</td>
                    <td>{{$item->degree->code}}</td>
                    <td id="col_unit_amount">{{$item->final_amount}}</td>
                    @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)
                        <td>{{$item->sub_amount}}</td>
                    @endif
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        @endif
        <tr>
            <td>
                @if($machine_allocation->production->packing_types()->count() !=1)
                    <select id="packing_type_id" name="packing_type_id">
                        @foreach($packing_type_option["items"] as $item)
                            <option
                                    value="{{($item["value"]==0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td>
                @if($default_lot_number)
                    <input type="hidden" id="lot_number_code" name="lot_number_code" autofocus
                           value="{{$default_lot_number->code??""}}">
                    {{$default_lot_number->code??""}}
                @elseif(isset($packing_form) && $packing_form->items()->count()>0)
                    <input type="hidden" id="lot_number_code" name="lot_number_code" autofocus
                           value="{{$packing_form->items()->first()->lot_number->code??""}}">
                    {{$packing_form->items()->first()->lot_number->code??""}}
                @else
                    <input type="text" id="lot_number_code" name="lot_number_code" value="{{$lot_number_code??""}}">
                @endif

            </td>
            <td>

                @if(isset($packing_form) && $packing_form->items()->count()>0 && $packing_form->packing_type->many_degrees_can_fit_into_one == 0)
                    <input type="hidden" id="degree_id" name="lot_number_code"
                           value="{{$packing_form->items()->first()->degree->id}}">
                    {{$packing_form->items()->first()->degree->caption}}

                @else
                    <select id="degree_id" name="degree_id">
                        @foreach($degree_option["items"] as $item)
                            <option
                                    value="{{($item["value"]==0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td id="col_unit_input">


                {{--                    اگر واحد اصلی وزنی است، آیکن باسکون کنار آن اضافه می شود.--}}
                @if($machine_allocation->production->product->unit->weight_conversion_rate==0)
                    {{--                        واحد اصلی وزنی است--}}
                    <input type="number" id="amount" name="amount" autofocus
                           value="{{isset($add_new_lot)?($amount??""):($before_amount??"")}}">

                @else
                    {{--                        واحد اصلی وزنی نیست--}}
                    @if(isset($smart_object))
                        <input type="number" id="amount" name="amount" autofocus disabled
                               value="{{isset($add_new_lot)?($amount??""):($before_amount??"")}}">
                        <a href="#sdf" id="amount_smart_object" class="smart_object_icon "
                           onclick="set_id_for_smart_object('amount');">
                            <i class="fas  fa-weight"></i>
                        </a>
                    @else

                        <input type="number" id="amount" name="amount" autofocus
                               value="{{isset($add_new_lot)?($amount??""):($before_amount??"")}}">
                    @endif
                @endif


            </td>
            {{--                واحد فرعی--}}
            @if($machine_allocation->production->product->sub_unit && $machine_allocation->production->product->sub_unit->weight_conversion_rate==0)
                <td>
                    <input type="number" id="sub_amount" name="sub_amount" autofocus
                           value="{{isset($add_new_lot)?($sub_amount??""):""}}">
                </td>
            @endif
            <td>
                @if($machine_allocation->production->packing_types()->count() ==1)

                    @if($packing_type_layer_count[$machine_allocation->production->packing_types()->first()->packing_type->id] == 1)
                        <a href="#" id="add_new_product_item">
                            <i class="fa fa-plus"></i>
                            افزودن آیتم جدید
                        </a>
                    @else
                        <input type="number" id="number_of_sub_packing" name="number_of_sub_packing"
                               value="{{$number_of_sub_packing??""}}">
                    @endif

                @else
                    <a href="#" id="add_new_product_item" style="display: none">
                        <i class="fa fa-plus"></i>
                        افزودن آیتم جدید
                    </a>
                    <input type="number" id="number_of_sub_packing" name="number_of_sub_packing"
                           style="display: none"
                           value="{{$number_of_sub_packing??""}}">
                @endif

            </td>
            <td>
                @if(isset($get_carrier_code) && $get_carrier_code)

                    <input type="number" id="carrier_code" name="carrier_code" required

                           value="">
{{--                    <input type="number" id="carrier_code" name="carrier_code"--}}
{{--                           value=""/>--}}

                @endif

            </td>

        </tr>

        @php $allocation_item_query=$machine_allocation->allocation->items();
            $allocation_count=$allocation_item_query->count();
            $allocation_priority=$allocation_item_query->where("id","<",$machine_allocation->id)->count();
        @endphp
        @if($allocation_count > 1)
            {{--            پایان if کارت تولید بعدی--}}
            <tr>
                <td colspan="6" class="alert-warning ">
                    <b>
                        با توجه به اینکه تخصیص شامل
                        {{$allocation_count}}
                        کارت تولید می باشد، شما اکنون در حال تولید کارت با اولویت

                        <b>{{$allocation_priority+1 }}</b>
                        به سریال
                        {{$machine_allocation->production->serial}}
                        می باشید.
                    </b>

                </td>
            </tr>
        @endif

    </table>
</div>

<div class="col-md-12" style="text-align: center">
    <table style="margin: auto">


        @if($machine_allocation->product->unit->weight_conversion_rate ==0)
            <tr>
                <td style="padding:10px">
                    وزن ناخالص کل:
                </td>
                <td>
                    <input type="text" value="" id="gross_weight" value="{{$gross_weight??""}}"
                           @if(isset($smart_object)) disabled @endif
                    >
                    @if(isset($smart_object))
                        <a href="#sdf" id="gross_weight_smart_object" class="smart_object_icon "
                           onclick="set_id_for_smart_object('gross_weight');">
                            <i class="fas  fa-weight"></i>
                        </a>
                    @endif

                </td>

            </tr>

        @endif

        @if($barcode_algorithm_id !=0)
            <tr>
                <td style="padding:10px">بارکد(pin1):</td>
                <td>
                    <input type="text" value="" id="pin1"

                    >

                </td>
            </tr>

        @endif
    </table>
</div>
<div class="col-md-12" style="text-align: center;height: 350px">
    <br/>
    @include("production.public_module.register_production._btn_submit")


</div>
</div>
<div class="row" style="">

</div>

@include("component._spinner",["id"=>"#0"])

@include("production.public_module.register_production._add_packing_item_script")

