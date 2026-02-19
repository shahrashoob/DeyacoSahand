<div class="row">

    @include("component.input._lable",["id"=>"","lable"=>" خط - ایستگاه","value"=>$machine->station->line->caption." - ".$machine->station->caption])
    @include("component.input._lable",["id"=>"","lable"=>"اپراتور مسئول","value"=>$machine->getOperator("fullname")])

    @if(!isset($show_allocation_id) || $show_allocation_id)
    @if(isset($allocation->items))
        @foreach($allocation->items as $item)

            @if(isset($item->production))
                @include("component.input._lable",["id"=>"","lable"=>"کارت تولید جاری باند ".$item->band_code,"value"=>$item->production->serial])

                @include("component.input._lable_product",["id"=>"product1".$item->id,"lable"=>"   کالای جاری باند ".$item->band_code,
            "product_property"=>$item->production->product,
            "value"=>$item->production->product->code."-".$item->production->product->caption])
            @endif


                @include("line_product_station.product.lot_number._lot_band",["productionFromItemLot"=>$productionFromItemLot,"band_code"=>$item->band_code])

        @endforeach

{{--            @include("component.input._lable",["id"=>"","lable"=>" تعداد کل داف ","value"=>$item->max_number_of_doffs??""])--}}
{{--            @include("component.input._lable",["id"=>"","lable"=>" متراژ هر داف ","value"=>$item->amount_of_each_doffs??""])--}}
{{--            @include("component.input._lable",["id"=>"","lable"=>" تعداد داف انجام شده ","value"=>$item->number_of_doffs_done??0])--}}

            @include("component.input._lable",["id"=>"","lable"=>"پیش بینی مدت زمان تولید (تئوری / ساعت) ","value"=>$item->allocation->theory_houre()])
            @include("component.input._lable",["id"=>"","lable"=>" پیش بینی مدت زمان تولید (عملی / ساعت) ","value"=>$item->allocation->partical_houre()])

            @include("component.input._lable",["id"=>"","lable"=>" تاریخ شروع تولید ","value"=>$item->start_time()])
            @include("component.input._lable",["id"=>"","lable"=>"پیش بینی تاریخ شروع تولید (تئوری / ساعت) ","value"=>$item->allocation->get_theory_datetime()])
            @include("component.input._lable",["id"=>"","lable"=>" پیش بینی  تاریخ شروع تولید (عملی / ساعت) ","value"=>$item->allocation->get_partical_datetime()])

    @endif
    @endif



    @include("component.input._lable",["id"=>"","lable"=>" شماره حامل","value"=>$production_form_carrier_code??""])

{{--    @if($production_form_reserve_carrier_code)--}}
{{--        @include("component.input._lable",["id"=>"","lable"=>" شماره غلطک پارچه خام رزرو","value"=>$production_form_reserve_carrier_code])--}}
{{--    @endif--}}

    @include("component.input._lable",["id"=>"","lable"=>" وضعیت فعال بودن ","value"=>$machine->active_status->caption??""])

    @if($machine->on_status_id!=53001)
        @include("component.input._lable",["id"=>"","lable"=>" وضعیت روشن بودن ","value"=>"خاموش به علت ".$machine->machine_off_reason->caption??""])
    @else
        @include("component.input._lable",["id"=>"","lable"=>"   وضعیت روشن بودن ","value"=>$machine->on_status->caption??""])
    @endif
    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت تولید ","value"=>$machine->production_status->caption??""])
    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت نگهداری و تعمیرات ","value"=>$machine->get_maintenance_status()])


</div>
