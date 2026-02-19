<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5> مشخصات دستور پیمان</h5>
        </div>
        <div class="card-block">
            <div class="row">

                <div class="col-md-6">
                    @include("component.input._lable",["id"=>"","lable"=>"سریال دستور پیمان","value"=>$contractor_allocation->production->serial()])
                    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ تخصیص","value"=>$contractor_allocation->get_create_date()])

                    @include("component.input._lable",["id"=>"","lable"=>"   ساعت تخصیص ","value"=>$contractor_allocation->get_create_time()])


                    @include("component.input._lable_product",["id"=>"product1".$contractor_allocation->id,"lable"=>"   کالا ",
                        "product_property"=>$contractor_allocation->product,
                        "value"=>$contractor_allocation->product->fullCaption()])

                    @foreach ($bom->items as $item)
                        @if(isset($product_bom[$item->material_id]))
                            @include("component.input._label_bom",["id"=>"bom1","lable"=>" BOM","value"=>$item->material->caption,"bom"=> $product_bom[$item->material_id],"product"=>$item->material,"class_col"=>"col-md-12"])
                        @endif
                    @endforeach

                    @include("component.input._lable",["id"=>"","lable"=>"   مقدار تخصیص  ","value"=>$contractor_allocation->allocation_amount." ".$contractor_allocation->product->unit->caption])

                    @if($contractor_allocation->production->packing_type)
                        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>$contractor_allocation->production->packing_type->caption])
                    @endif

                    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$contractor_allocation->status->caption])
                </div>


            </div>
        </div>
    </div>
</div>
