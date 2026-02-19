<div class="row">

    @include("component.input._lable",["id"=>"","lable"=>" خط - ایستگاه","value"=>$machine->station->line->caption." - ".$machine->station->caption])
    @include("component.input._lable",["id"=>"","lable"=>"اپراتور مسئول","value"=>$machine->getOperator("fullname")])
    @php $item=null;@endphp
    @if(isset($allocation->items))
        @if(count($allocation->items) == 1)
            @php $item=$allocation->items()->first();@endphp
            @if(isset($item->production))
                @include("component.input._lable",["id"=>"","lable"=>"کارت تولید جاری  ","value"=>$item->production->serial])

                @include("component.input._lable_product",["id"=>"product1".$item->id,"lable"=>"   کالای جاری  ","image"=>1,
            "product_property"=>$item->production->product,
            "value"=>$item->production->product->code."-".$item->production->product->caption])
            @endif


            @include("line_product_station.product.lot_number._lot_band",["productionFromItemLot"=>$productionFromItemLot,"band_code"=>$item->band_code,"no_band"=>1,"unit_caption"=>$item->production->product->unit->caption,"product"=>$item->product])

        @else
            @foreach($allocation->items as $item)

                @if(isset($item->production))
                    @include("component.input._lable",["id"=>"","lable"=>"کارت تولید جاری باند ".$item->band_code,"value"=>$item->production->serial])

                    @include("component.input._lable_product",["id"=>"product1".$item->id,"lable"=>"   کالای جاری باند ".$item->band_code,"image"=>1,
                "product_property"=>$item->production->product,
                "value"=>$item->production->product->code."-".$item->production->product->caption.(
                    $item->version_code?"(V".$item->version_code.")":"")])
                @endif


                @include("line_product_station.product.lot_number._lot_band",["productionFromItemLot"=>$productionFromItemLot,"band_code"=>$item->band_code,"unit_caption"=>$item->production->product->unit->caption,"product"=>$item->product])

            @endforeach
        @endif

        @if(  $allocation->allocation_doffs()->count()>0)
            @php $max_doff=""; $done_doff="";;$done_brand="";$packing_type_id_for_doff=0@endphp
            @foreach($allocation->allocation_doffs as $doff_item)
                @php

                    // اگر چند نوع بسته بندی باشد، باید با | جدا شود.
                    if(!isset($max_doff_packing_form[$doff_item->packing_type_id])){
                        $max_doff_packing_form[$doff_item->packing_type_id]=0;
                        $done_doff_packing_form[$doff_item->packing_type_id]=0;
                    }

                    $max_doff_packing_form[$doff_item->packing_type_id]+=$doff_item->max_number_of_doffs;
                    $done_doff_packing_form[$doff_item->packing_type_id]+=$doff_item->number_of_doffs_done;
                   // $amount_doff.=$doff_item->amount_of_each_doffs." ".($item->production->product->unit->caption??"")." | ";

                     $max_doff.=$doff_item->max_number_of_doffs.($doff_item->packing_type? " (".$doff_item->packing_type->caption.")":"")." | ";
                    $done_doff.=$doff_item->number_of_doffs_done." | ";
                   // $amount_doff.=$doff_item->amount_of_each_doffs." ".($item->production->product->unit->caption??"")." | ";
                    $done_brand.=($doff_item->max_number_of_doffs>$doff_item->number_of_doffs_done?$doff_item->number_of_brand_done:$doff_item->number_of_brand)." | ";
                    $packing_type_id_for_doff=$doff_item->packing_type_id;
                @endphp

            @endforeach

            @php

                if(count( $done_doff_packing_form)==1){
                    $max_doff=$max_doff_packing_form[$packing_type_id_for_doff];
                    $done_doff=$done_doff_packing_form[$packing_type_id_for_doff];
                }
            @endphp
            @include("component.input._lable",["id"=>"","lable"=>" تعداد کل داف ","value"=>$max_doff])


            {{--            @include("component.input._lable",["id"=>"","lable"=>" مقدار هر داف ","value"=>$amount_doff])--}}
            @if(count( $done_doff_packing_form)!=1)
                @include("line_product_station.product.unit_of_measure_type._allocation_doffs",["label"=>" مقدار هر داف ","production"=>$item->production,"allocation_doffs"=>$allocation->allocation_doffs ])
            @else
                @include("line_product_station.product.unit_of_measure_type._production",["label"=>" مقدار  داف ","production"=>$item->production,"units"=>["amount"=>($item->amount_of_each_doffs??"") ]])
            @endif


            @include("component.input._lable",["id"=>"","lable"=>" تعداد داف انجام شده ","value"=>$done_doff])

            @if($item->production->normal_amount)
                @include("component.input._lable",["id"=>"","lable"=>" تعداد لوگو انجام شد ","value"=>$done_brand])
            @endif

        @else
            @include("component.input._lable",["id"=>"","lable"=>" تعداد کل داف ","value"=>$item->max_number_of_doffs??""])

            {{--                @include("component.input._lable",["id"=>"","lable"=>" مقدار هر داف ","value"=>($item->amount_of_each_doffs??"").($item->production->product->unit->caption)])--}}
            @include("line_product_station.product.unit_of_measure_type._production",["label"=>" مقدار هر داف ","production"=>$item->production,"units"=>["amount"=>($item->amount_of_each_doffs??"") ]])

            @include("component.input._lable",["id"=>"","lable"=>" تعداد داف انجام شده ","value"=>$item->number_of_doffs_done??0])
        @endif
        @if($item)
            @include("component.input._lable",["id"=>"","lable"=>"پیش بینی مدت زمان تولید (تئوری / ساعت) ","value"=>$item->allocation->theory_houre()])
            @include("component.input._lable",["id"=>"","lable"=>" پیش بینی مدت زمان تولید (عملی / ساعت) ","value"=>$item->allocation->partical_houre()])


            @include("component.input._lable",["id"=>"","lable"=>" تاریخ شروع تولید ","value"=>$item->start_time()])
            @include("component.input._lable",["id"=>"","lable"=>"پیش بینی تاریخ شروع تولید (تئوری) ","value"=>$item->allocation->get_theory_datetime()])
            @include("component.input._lable",["id"=>"","lable"=>" پیش بینی  تاریخ شروع تولید (عملی) ","value"=>$item->allocation->get_partical_datetime()])
        @endif
    @endif




    @include("component.input._lable",["id"=>"","lable"=>" شماره حامل","value"=>$production_form_carrier_code??""])

    @if($production_form_reserve_carrier_code)
        @include("component.input._lable",["id"=>"","lable"=>" شماره حامل رزرو","value"=>$production_form_reserve_carrier_code])
    @endif

    @include("component.input._lable",["id"=>"","lable"=>" وضعیت فعال بودن ","value"=>$machine->active_status->caption??""])

    @if($machine->on_status_id!=53001)
        @include("component.input._lable",["id"=>"","lable"=>" وضعیت روشن بودن ","value"=>"خاموش به علت ".$machine->machine_off_reason->caption??""])
    @else
        @include("component.input._lable",["id"=>"","lable"=>"   وضعیت روشن بودن ","value"=>$machine->on_status->caption??""])
    @endif
    @include("component.input._lable",["id"=>"","lable"=>"وضعیت تولید","value"=>$machine->production_status->caption??""])
    @include("component.input._lable",["id"=>"","lable"=>"وضعیت نگهداری و تعمیرات","value"=>$machine->get_maintenance_status()])


    @php $machine_production_chanel=$machine->getCurrentProductionChannel();@endphp
    @include("component.input._lable",["id"=>"","lable"=>"کانال تولید جاری ماشین","value"=>
            $machine_production_chanel?
                ($machine_production_chanel->production_channel_type->caption??"---").
                (" (".$machine_production_chanel->getCode().")"):
                ""
                ])


</div>
