<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>" نام کالا","value"=>$packing_form->product->code." - ".$packing_form->product->caption])
    @include("component.input._lable",["id"=>"","lable"=>" درجه کالا ","value"=>$packing_form->degree->caption??""])
    <div class="col-md-6 offset-md-6">
        <div class="form-group">
            <label>لات (همبافت):</label>
            @include("line_product_station.product.lot_number._label",["lot_number"=>$packing_form->lot_number,"id"=>$packing_form->id])
            @include("line_product_station.product.lot_number._collapse",["lot_number"=>$packing_form->lot_number,"id"=>$packing_form->id])
        </div>
    </div>
    @include("component.input._lable",["id"=>"","lable"=>"  بسته بندی","value"=>$packing_form->packing_type->fullCaption()])
    @include("component.input._lable",["id"=>"","lable"=>"  حامل","value"=>$packing_form->carrier->getCaption()])
{{--سیب--}}

    @include("component.input._lable",["id"=>"","lable"=>"متراژ سیستم","value"=>$packing_form->getAmount()])
    @include("component.input._lable",["id"=>"","lable"=>"متراژ کنترل کیفیت","value"=>$packing_form->getAmountAfterControl()])
    @include("component.input._lable",["id"=>"","lable"=>"متراژ نهایی","value"=>$packing_form->getFinalAmount()])

    @include("component.input._lable",["id"=>"","lable"=>"شماره فرم انبار","value"=>$packing_form->form->code??"---"])
    @include("component.input._lable",["id"=>"","lable"=>"وضعیت","value"=>$packing_form->status->caption])

</div>


