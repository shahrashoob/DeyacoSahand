<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"  نوع بسته بندی","value"=>
$packing_form->packing_type?$packing_form->packing_type->fullCaption()." -".$packing_form->reality_type->caption:"***"])

    @if($packing_form->carrier)
        @include("component.input._lable",["id"=>"","lable"=>"  شماره حامل","value"=>$packing_form->carrier?$packing_form->carrier->getCaption():"فاقد حامل"])
    @endif


    @include("component.input._lable",["id"=>"","lable"=>"مقدار اولیه","value"=>$packing_form->getAmount("amount", 4)])
    @php $init_sub_amount=$packing_form->getAmount("init_sub_amount", 4);@endphp
    @if($init_sub_amount > 0)
        @include("component.input._lable",["id"=>"","lable"=>"مقدار فرعی اولیه","value"=>$init_sub_amount])
    @endif
    {{--    @include("component.input._lable",["id"=>"","lable"=>"متراژ کنترل کیفیت","value"=>$packing_form->getAmountAfterControl()])--}}
    @include("component.input._lable",["id"=>"","lable"=>"مقدار نهایی","value"=>$packing_form->getFinalAmount(4)])



    @include("component.input._lable",["id"=>"","lable"=>"وزن خالص","value"=>$packing_form->weight])
    @include("component.input._lable",["id"=>"","lable"=>"وزن نا خالص","value"=>$packing_form->gross_weight])

    @if(isset($packing_form->packing_type->first_packing_type))
        @include("component.input._lable",["id"=>"","lable"=>"تعداد بسته بندی فرعی","value"=>$packing_form->sub_packing_form_number." عدد "])

    @endif


    @include("component.input._lable",["id"=>"","lable"=>"درصد جمع شدگی اولیه","value"=>$packing_form->initial_shrinkage_percent()." %"])
    @if(isset($final_shrinkage_percent))
        @include("component.input._lable",["id"=>"","lable"=>"درصد جمع شدگی نهایی","value"=>($final_shrinkage_percent)." %"])
    @endif

    @include("component.input._lable",["id"=>"","lable"=>"شماره فرم انبار","value"=>$packing_form->form->code??"---"])

    @include("component.input._lable",["id"=>"","lable"=>"وضعیت","value"=>$packing_form->getStatus()])
    @if($packing_form->warehouse_status_id==4203 || $packing_form->warehouse_status_id==4204)

        @include("component.input._lable",["id"=>"","lable"=>"وضعیت رزور","value"=>$packing_form->warehouse_status->caption,"message"=>$warehouse_status_reference_code??""])
    @endif

    @if($packing_form->warehouse_shelving)

        @include("component.input._lable",["id"=>"","lable"=>"شناسه قفسه انبار","value"=>$packing_form->warehouse_shelving->fullCode()])
    @endif

    @if(isset($pallet_item))

        @include("component.input._lable",["id"=>"","lable"=>"شماره پالت","value"=>$pallet_item->pallet->getCodeNumber()])
    @endif

</div>


