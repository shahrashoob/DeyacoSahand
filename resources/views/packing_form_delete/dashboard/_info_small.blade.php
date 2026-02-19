<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>" درجه کالا ","value"=>$packing_form->degree->caption??""])

    @include("component.input._lable",["id"=>"","lable"=>"  بسته بندی","value"=>$packing_form->packing_type?$packing_form->packing_type->fullCaption():"***"])
    @include("component.input._lable",["id"=>"","lable"=>"  حامل","value"=>$packing_form->carrier?$packing_form->carrier->getCaption():"فاقد حامل"])
{{--سیب--}}

    @include("component.input._lable",["id"=>"","lable"=>"متراژ سیستم","value"=>$packing_form->getAmount()])
    @include("component.input._lable",["id"=>"","lable"=>"متراژ کنترل کیفیت","value"=>$packing_form->getAmountAfterControl()])
    @include("component.input._lable",["id"=>"","lable"=>"متراژ نهایی","value"=>$packing_form->getFinalAmount()])

    @include("component.input._lable",["id"=>"","lable"=>"وزن خالص","value"=>$packing_form->weight])
    @include("component.input._lable",["id"=>"","lable"=>"وزن نا خالص","value"=>$packing_form->gross_weight])

    @include("component.input._lable",["id"=>"","lable"=>"شماره فرم انبار","value"=>$packing_form->form->code??"---"])

    @include("component.input._lable",["id"=>"","lable"=>"وضعیت","value"=>$packing_form->getStatus()])

</div>


